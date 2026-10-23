<?php

namespace App\Http\Controllers\ChildOfficer;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Event;
use App\Models\EventFollowUpAction;
use App\Models\EventFollowUpUpdate;
use App\Models\EventFollowUp;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EventFollowUpController extends Controller
{
    public function index(Request $request): View
    {
        $this->authorizeChildOfficer($request);

        $query = EventFollowUp::with(['event', 'assignee']);

        if ($request->filled('search')) {
            $search = trim($request->string('search')->toString());
            $query->where(function ($followUps) use ($search) {
                $followUps->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%")
                    ->orWhereHas('event', function ($events) use ($search) {
                        $events->where('title', 'like', "%{$search}%")
                            ->orWhere('event_code', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->input('priority'));
        }

        if ($request->input('view') === 'upcoming') {
            $query->whereDate('due_date', '>=', today())
                ->whereNotIn('status', ['completed', 'cancelled']);
        } elseif ($request->input('view') === 'history') {
            $query->whereIn('status', ['completed', 'cancelled']);
        }

        $followUps = $query->latest()->paginate(10)->withQueryString();
        $openStatuses = ['open', 'pending', 'in_progress'];
        $stats = [
            'open' => EventFollowUp::whereIn('status', $openStatuses)->count(),
            'today' => EventFollowUp::whereIn('status', $openStatuses)
                ->whereDate('due_date', today())->count(),
            'overdue' => EventFollowUp::whereIn('status', $openStatuses)
                ->whereDate('due_date', '<', today())->count(),
            'completed' => EventFollowUp::where('status', 'completed')
                ->whereMonth('updated_at', now()->month)
                ->whereYear('updated_at', now()->year)->count(),
        ];
        $reportTotals = null;
        $workload = collect();

        if ($request->input('view') === 'reports') {
            $total = EventFollowUp::count();
            $completed = EventFollowUp::where('status', 'completed')->count();
            $reportTotals = [
                'completion_rate' => $total === 0 ? 0 : (int) round($completed / $total * 100),
                'completed' => $completed,
                'overdue' => $stats['overdue'],
                'open' => $stats['open'],
            ];
            $workload = User::query()
                ->whereHas('assignedEventFollowUps')
                ->withCount('assignedEventFollowUps')
                ->orderBy('name')
                ->get();
        }

        return view('child_officer.follow-ups.index', compact(
            'followUps', 'stats', 'reportTotals', 'workload'
        ));
    }

    public function create(Request $request): View
    {
        $this->authorizeChildOfficer($request);
        $events = Event::orderByDesc('event_date')->get();
        $staff = $this->authorizedStaff();

        return view('child_officer.follow-ups.create', compact('events', 'staff'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->authorizeChildOfficer($request);
        $validated = $request->validate([
            'event_id' => ['required', 'exists:events,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'reason' => ['required', 'string', 'max:2000'],
            'assigned_to' => ['required', Rule::in($this->authorizedStaff()->modelKeys())],
            'due_date' => ['required', 'date'],
            'reminder_at' => ['nullable', 'date'],
            'priority' => ['required', Rule::in(['low', 'normal', 'high', 'urgent'])],
            'status' => ['required', Rule::in(['open', 'in_progress', 'completed', 'cancelled'])],
            'outcome' => ['nullable', 'required_if:status,completed', 'string', 'max:5000'],
            'initial_notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $followUp = DB::transaction(function () use ($validated, $request) {
            $followUp = EventFollowUp::create([
                ...$validated,
                'created_by' => $request->user()->id,
            ]);

            if (!empty($validated['initial_notes'])) {
                $followUp->updates()->create([
                    'update_type' => 'progress',
                    'notes' => $validated['initial_notes'],
                    'author_id' => $request->user()->id,
                ]);
            }

            if (!empty($validated['outcome'])) {
                $followUp->updates()->create([
                    'update_type' => 'outcome',
                    'notes' => $validated['outcome'],
                    'author_id' => $request->user()->id,
                ]);
            }

            $this->audit($request, 'created', $followUp, 'Created event follow-up.');

            return $followUp;
        });

        return redirect()->route('child_officer.follow-ups.show', $followUp)
            ->with('success', 'Follow-up created successfully.');
    }

    public function show(Request $request, EventFollowUp $followUp): View
    {
        $this->authorizeChildOfficer($request);
        $followUp->load([
            'event', 'assignee', 'creator', 'updates.author', 'actions.assignee',
        ]);
        $staff = $this->authorizedStaff();

        return view('child_officer.follow-ups.show', compact('followUp', 'staff'));
    }

    public function storeUpdate(Request $request, EventFollowUp $followUp): RedirectResponse
    {
        $this->authorizeChildOfficer($request);
        $validated = $request->validate([
            'update_type' => ['required', Rule::in(['progress', 'outcome', 'issue', 'action_completed'])],
            'notes' => ['required', 'string', 'max:5000'],
            'next_action' => ['nullable', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $followUp, $validated) {
            $followUp->updates()->create([
                ...$validated,
                'author_id' => $request->user()->id,
            ]);

            if (in_array($validated['update_type'], ['progress', 'outcome', 'action_completed'], true)
                && in_array($followUp->status, ['open', 'pending'], true)) {
                $followUp->update(['status' => 'in_progress']);
            }

            $this->audit($request, 'updated', $followUp, 'Added a follow-up update.');
        });

        return back()->with('success', 'Update added to follow-up history.');
    }

    public function storeAction(Request $request, EventFollowUp $followUp): RedirectResponse
    {
        $this->authorizeChildOfficer($request);
        $validated = $request->validate([
            'description' => ['required', 'string', 'max:1000'],
            'assigned_to' => ['required', Rule::in($this->authorizedStaff()->modelKeys())],
            'due_date' => ['nullable', 'date'],
        ]);

        $followUp->actions()->create($validated + ['status' => 'pending']);
        $this->audit($request, 'updated', $followUp, 'Added a follow-up action.');

        return back()->with('success', 'Next action added.');
    }

    public function updateAction(Request $request, EventFollowUp $followUp, EventFollowUpAction $action): RedirectResponse
    {
        $this->authorizeChildOfficer($request);
        abort_unless($action->event_follow_up_id === $followUp->id, 404);

        $validated = $request->validate([
            'status' => ['required', Rule::in(['pending', 'completed'])],
            'completion_evidence' => ['nullable', 'string', 'max:2000'],
        ]);

        $action->update([
            ...$validated,
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);
        $this->audit($request, 'updated', $followUp, 'Updated a follow-up action.');

        return back()->with('success', 'Action updated.');
    }

    public function reschedule(Request $request, EventFollowUp $followUp): RedirectResponse
    {
        $this->authorizeChildOfficer($request);
        $validated = $request->validate([
            'due_date' => ['required', 'date', 'after_or_equal:today'],
            'reason' => ['required', 'string', 'max:2000'],
        ]);

        DB::transaction(function () use ($request, $followUp, $validated) {
            $followUp->update(['due_date' => $validated['due_date']]);
            $followUp->updates()->create([
                'update_type' => 'issue',
                'notes' => 'Rescheduled: ' . $validated['reason'],
                'author_id' => $request->user()->id,
            ]);
            $this->audit($request, 'updated', $followUp, 'Rescheduled follow-up.');
        });

        return back()->with('success', 'Follow-up rescheduled.');
    }

    public function complete(Request $request, EventFollowUp $followUp): RedirectResponse
    {
        $this->authorizeChildOfficer($request);
        $validated = $request->validate([
            'outcome' => ['required', 'string', 'max:5000'],
        ]);

        if ($followUp->actions()->where('status', '!=', 'completed')->exists()) {
            return back()->withErrors([
                'outcome' => 'Complete all next actions before closing this follow-up.',
            ]);
        }

        DB::transaction(function () use ($request, $followUp, $validated) {
            $followUp->update(['outcome' => $validated['outcome'], 'status' => 'completed']);
            $followUp->updates()->create([
                'update_type' => 'outcome',
                'notes' => $validated['outcome'],
                'author_id' => $request->user()->id,
            ]);
            $this->audit($request, 'completed', $followUp, 'Completed event follow-up.');
        });

        return redirect()->route('child_officer.follow-ups.show', $followUp)
            ->with('success', 'Follow-up completed and outcome recorded.');
    }

    private function authorizeChildOfficer(Request $request): void
    {
        abort_unless($request->user()?->role === 'child_officer', 403);
    }

    private function authorizedStaff()
    {
        return User::where('status', 'active')
            ->whereIn('role', ['child_officer', 'field_officer'])
            ->orderBy('name')
            ->get();
    }

    private function audit(Request $request, string $action, EventFollowUp $followUp, string $description): void
    {
        AuditLog::create([
            'user_id' => $request->user()->id,
            'action' => $action,
            'module' => 'event_follow_ups',
            'record_id' => $followUp->id,
            'description' => $description,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);
    }
}
