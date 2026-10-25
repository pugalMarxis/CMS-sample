<?php

namespace App\Http\Controllers\ChildOfficer;

use App\Exports\ChildReportExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Child;
use App\Models\FollowUp;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ChildReportController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today();

        // // Child list with search and filters.
        // $childrenQuery = Child::query();

        // if ($request->filled('search')) {
        //     $search = trim($request->input('search'));

        //     $childrenQuery->where(function ($query) use ($search) {
        //         $query->where('child_code', 'like', "%{$search}%")
        //             ->orWhere('first_name', 'like', "%{$search}%")
        //             ->orWhere('middle_name', 'like', "%{$search}%")
        //             ->orWhere('last_name', 'like', "%{$search}%");
        //     });
        // }

        // if (in_array($request->input('status'), ['active', 'inactive'], true)) {
        //     $childrenQuery->where('status', $request->input('status'));
        // }

        // if ($request->filled('district')) {
        //     $childrenQuery->where('district', $request->input('district'));
        // }

        // Child list with search and filters.
        $childrenQuery = $this->filteredChildrenQuery($request);

        // Child summary statistics.
        $totalChildren = Child::count();
        $activeChildren = Child::where('status', 'active')->count();
        $inactiveChildren = Child::where('status', 'inactive')->count();

        $newThisMonth = Child::whereBetween('created_at', [
            Carbon::now()->startOfMonth(),
            Carbon::now()->endOfMonth(),
        ])->count();

        // Follow-up statistics.
        $pendingFollowUps = FollowUp::where('status', 'pending')->count();

        $overdueFollowUps = FollowUp::where('status', 'pending')
            ->whereNotNull('follow_up_date')
            ->whereDate('follow_up_date', '<', $today)
            ->count();

        // Attendance statistics.
        $attendanceTotal = Attendance::count();
        $presentAttendance = Attendance::where('status', 'present')->count();

        // Child totals grouped by district.
        $districtStats = Child::query()
            ->selectRaw("
                COALESCE(NULLIF(TRIM(district), ''), 'Not specified') AS district_name,
                COUNT(*) AS total
            ")
            ->groupBy('district_name')
            ->orderByDesc('total')
            ->get();

        // District filter options.
        $districts = Child::query()
            ->whereNotNull('district')
            ->where('district', '!=', '')
            ->distinct()
            ->orderBy('district')
            ->pluck('district');

        // Paginated child records.
        $children = $childrenQuery
            ->withCount(['followUps', 'attendances'])
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('child_officer.reports.index', compact(
            'totalChildren',
            'activeChildren',
            'inactiveChildren',
            'newThisMonth',
            'pendingFollowUps',
            'overdueFollowUps',
            'attendanceTotal',
            'presentAttendance',
            'districtStats',
            'districts',
            'children'
        ));
    }


private function filteredChildrenQuery(Request $request): Builder
{
    $query = Child::query();

    if ($request->filled('search')) {
        $search = trim($request->input('search'));

        $query->where(function ($query) use ($search) {
            $query->where('child_code', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('middle_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%");
        });
    }

    if (in_array($request->input('status'), ['active', 'inactive'], true)) {
        $query->where('status', $request->input('status'));
    }

    if ($request->filled('district')) {
        $query->where('district', $request->input('district'));
    }

    return $query;
}

public function exportExcel(Request $request)
{
    $children = $this->filteredChildrenQuery($request)
        ->orderBy('child_code')
        ->get();

    return Excel::download(
        new ChildReportExport($children),
        'child-reports.xlsx'
    );
}

public function exportPdf(Request $request)
{
    $children = $this->filteredChildrenQuery($request)
        ->orderBy('child_code')
        ->get();

    $pdf = Pdf::loadView(
        'child_officer.reports.export-pdf',
        compact('children')
    );

    $pdf->setPaper('a4', 'landscape');

    return $pdf->download('child-reports.pdf');
}



}
