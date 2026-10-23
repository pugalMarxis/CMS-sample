<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $followUp->title }} | KFHI-CMS</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f4f8f5; color: #1e2922; font-family: Arial, sans-serif; }
        .page-shell { display: flex; min-height: 100vh; }
        main { width: min(1050px, 100%); margin: 0 auto; padding: 32px clamp(18px, 4vw, 54px); }
        .breadcrumb { margin-bottom: 18px; color: #68766d; font-size: 12px; } .breadcrumb a { color: #2e7d32; text-decoration: none; }
        .heading, .heading-actions, .info-grid, .action-head { display: flex; justify-content: space-between; align-items: flex-start; gap: 12px; }
        h1 { margin: 0 0 5px; font-size: 24px; } h2 { margin: 0 0 14px; font-size: 16px; } h3 { margin: 0 0 5px; font-size: 14px; }
        p, .muted { margin: 0; color: #68766d; font-size: 12px; line-height: 1.55; }
        .heading { margin-bottom: 20px; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 36px; padding: 0 13px; border: 1px solid #2e7d32; border-radius: 7px; background: #2e7d32; color: #fff; font: inherit; font-size: 12px; font-weight: 700; text-decoration: none; cursor: pointer; }
        .button.secondary { background: #fff; color: #2e7d32; }
        .badge { display: inline-block; padding: 5px 9px; border-radius: 99px; background: #edf3ee; color: #526258; font-size: 10px; font-weight: 700; white-space: nowrap; }
        .badge.high, .badge.urgent { background: #fff0e8; color: #ad4c13; } .badge.done { background: #e6f4e8; color: #26733a; }
        .section { margin: 12px 0; padding: 18px; border: 1px solid #dce5de; border-radius: 8px; background: #fff; }
        .section-letter { margin-bottom: 8px; color: #2e7d32; font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .info-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .info { padding: 11px 0; border-bottom: 1px solid #edf1ee; } .info:last-child, .info:nth-last-child(2) { border-bottom: 0; }
        .label { margin-bottom: 5px; color: #68766d; font-size: 11px; } .value { font-size: 13px; line-height: 1.5; white-space: pre-wrap; }
        .wide { grid-column: 1 / -1; }
        .history { display: grid; gap: 10px; }
        .history-item { padding: 12px 13px; border-left: 2px solid #8aba90; background: #f7faf7; }
        .history-top { display: flex; justify-content: space-between; gap: 12px; margin-bottom: 6px; font-size: 12px; font-weight: 700; }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        label { display: block; margin-bottom: 6px; color: #35443a; font-size: 11px; font-weight: 700; }
        input, select, textarea { width: 100%; min-height: 38px; border: 1px solid #d5dfd8; border-radius: 6px; padding: 9px 10px; background: #fff; color: #1e2922; font: inherit; font-size: 12px; }
        textarea { min-height: 78px; resize: vertical; } .field.wide { grid-column: 1 / -1; }
        .form-actions { display: flex; justify-content: flex-end; margin-top: 11px; }
        .action-row { display: grid; grid-template-columns: 1fr auto; gap: 10px; padding: 12px 0; border-bottom: 1px solid #edf1ee; }
        .action-row:last-of-type { border-bottom: 0; }
        .action-form { display: flex; align-items: center; gap: 6px; }
        .action-form input { width: 150px; min-height: 32px; padding: 6px 8px; }
        .action-form button { min-height: 32px; padding: 0 9px; font-size: 11px; }
        details { margin-top: 14px; } summary { color: #2e7d32; font-size: 12px; font-weight: 700; cursor: pointer; }
        .details-form { margin-top: 12px; padding-top: 12px; border-top: 1px solid #edf1ee; }
        .notice { margin: 0 0 13px; padding: 10px 12px; border-radius: 6px; background: #e6f4e8; color: #26733a; font-size: 12px; }
        .error { margin-top: 5px; color: #b44949; font-size: 11px; }
        .empty { padding: 12px 0; }
        .complete-button { width: 100%; min-height: 42px; }
        @media (max-width: 700px) { .heading { display: block; } .heading-actions { justify-content: flex-start; margin-top: 12px; flex-wrap: wrap; } }
        @media (max-width: 560px) { .info-grid, .form-grid { grid-template-columns: 1fr; } .wide, .field.wide { grid-column: auto; } .info:nth-last-child(2) { border-bottom: 1px solid #edf1ee; } .action-row { grid-template-columns: 1fr; } .action-form { flex-wrap: wrap; } .action-form input { width: 100%; } }
    </style>
</head>
<body>
<div class="page-shell">
    @include('components.child-officer-sidebar')
    <main>
        <div class="breadcrumb"><a href="{{ route('child_officer.follow-ups.index') }}">Follow-ups</a> / FU-{{ str_pad($followUp->id, 3, '0', STR_PAD_LEFT) }}</div>
        @if (session('success')) <div class="notice">{{ session('success') }}</div> @endif
        <header class="heading">
            <div><h1>{{ $followUp->title }}</h1><p>{{ $followUp->event?->title ?? 'Event unavailable' }}</p></div>
            <div class="heading-actions">
                @if ($followUp->status === 'completed') <span class="badge done">Completed</span>
                @elseif ($followUp->due_date && $followUp->due_date->isPast() && !$followUp->due_date->isToday()) <span class="badge high">Overdue</span>
                @else <span class="badge {{ in_array($followUp->priority, ['high', 'urgent']) ? $followUp->priority : '' }}">{{ $followUp->due_date?->isToday() ? 'Due Today' : ucfirst(str_replace('_', ' ', $followUp->status)) }}</span> @endif
                @if ($followUp->status !== 'completed')
                    <details><summary class="button secondary">Reschedule</summary><form class="details-form" method="POST" action="{{ route('child_officer.follow-ups.reschedule', $followUp) }}">@csrf @method('PATCH')<label for="reschedule-date">New due date</label><input id="reschedule-date" type="date" name="due_date" min="{{ today()->toDateString() }}" required><label for="reschedule-reason" style="margin-top:8px">Reason</label><textarea id="reschedule-reason" name="reason" required></textarea><div class="form-actions"><button class="button" type="submit">Save new date</button></div></form></details>
                @endif
            </div>
        </header>
        @if ($errors->any()) <div class="notice" style="background:#fff0e8;color:#ad4c13">{{ $errors->first() }}</div> @endif

        <section class="section">
            <div class="section-letter">A. Event information</div>
            <div class="info-grid">
                <div class="info"><div class="label">Event name</div><div class="value">{{ $followUp->event?->title ?? 'Event unavailable' }}</div></div>
                <div class="info"><div class="label">Event date</div><div class="value">{{ $followUp->event?->event_date?->format('d M Y') ?? 'Not set' }}</div></div>
                <div class="info"><div class="label">Event location</div><div class="value">{{ $followUp->event?->location ?: 'Not recorded' }}</div></div>
                <div class="info"><div class="label">Event reference</div><div class="value">{{ $followUp->event?->event_code ?? 'Not recorded' }}</div></div>
                <div class="info wide"><div class="label">Event organizer</div><div class="value">{{ $followUp->event?->creator?->name ?? 'Not recorded' }}</div></div>
            </div>
        </section>

        <section class="section">
            <div class="section-letter">B. Follow-up task details</div>
            <div class="info-grid">
                <div class="info"><div class="label">Follow-up ID</div><div class="value">FU-{{ str_pad($followUp->id, 3, '0', STR_PAD_LEFT) }}</div></div>
                <div class="info"><div class="label">Assigned officer</div><div class="value">{{ $followUp->assignee?->name ?? 'Unassigned' }}</div></div>
                <div class="info"><div class="label">Priority</div><div class="value">{{ ucfirst($followUp->priority) }}</div></div>
                <div class="info"><div class="label">Due date</div><div class="value">{{ $followUp->due_date?->format('d M Y') ?? 'Not set' }}</div></div>
                <div class="info wide"><div class="label">Task description</div><div class="value">{{ $followUp->description ?: 'No task description provided.' }}</div></div>
                <div class="info wide"><div class="label">Reason for follow-up</div><div class="value">{{ $followUp->reason ?: 'Not recorded' }}</div></div>
                @if ($followUp->initial_notes)<div class="info wide"><div class="label">Initial notes</div><div class="value">{{ $followUp->initial_notes }}</div></div>@endif
            </div>
        </section>

        <section class="section">
            <div class="section-letter">C. Follow-up history</div>
            <div class="history">
                <article class="history-item"><div class="history-top"><span>Task created and assigned</span><span class="muted">{{ $followUp->created_at?->format('d M Y') }} · {{ $followUp->creator?->name ?? 'Staff' }}</span></div><p>{{ $followUp->reason ?: 'Follow-up task created for this event.' }}</p></article>
                @forelse ($followUp->updates as $update)
                    <article class="history-item"><div class="history-top"><span>{{ ucfirst(str_replace('_', ' ', $update->update_type)) }}</span><span class="muted">{{ $update->created_at->format('d M Y') }} · {{ $update->author?->name ?? 'Staff' }}</span></div><p>{{ $update->notes }}</p>@if ($update->next_action)<p style="margin-top:6px"><strong>Next action:</strong> {{ $update->next_action }}</p>@endif</article>
                @empty
                    <p class="empty">No progress updates have been recorded yet.</p>
                @endforelse
            </div>
        </section>

        @if ($followUp->status !== 'completed')
            <section class="section">
                <div class="section-letter">D. Add an update</div>
                <form method="POST" action="{{ route('child_officer.follow-ups.updates.store', $followUp) }}">
                    @csrf
                    <div class="form-grid">
                        <div><label for="update_type">Update type</label><select id="update_type" name="update_type" required><option value="progress">Progress update</option><option value="outcome">Outcome recorded</option><option value="issue">Issue or delay</option><option value="action_completed">Action completed</option></select></div>
                        <div class="field wide"><label for="notes">Notes</label><textarea id="notes" name="notes" placeholder="What has happened? What progress was made? What remains unresolved?" required>{{ old('notes') }}</textarea></div>
                        <div class="field wide"><label for="next_action">Next action (if needed)</label><input id="next_action" name="next_action" placeholder="Describe the next required step..." value="{{ old('next_action') }}"></div>
                    </div>
                    <div class="form-actions"><button class="button" type="submit">Save update</button></div>
                </form>
            </section>
        @endif

        <section class="section">
            <div class="section-letter">E. Next actions</div>
            @forelse ($followUp->actions as $action)
                <div class="action-row">
                    <div><h3>{{ $action->description }}</h3><p>{{ $action->assignee?->name ?? 'Unassigned' }} · {{ $action->status === 'completed' ? 'Completed' : 'Due ' . ($action->due_date?->format('d M Y') ?? 'date not set') }}</p>@if ($action->completion_evidence)<p>{{ $action->completion_evidence }}</p>@endif</div>
                    @if ($action->status !== 'completed' && $followUp->status !== 'completed')
                        <form class="action-form" method="POST" action="{{ route('child_officer.follow-ups.actions.update', [$followUp, $action]) }}">@csrf @method('PATCH')<input type="hidden" name="status" value="completed"><input name="completion_evidence" placeholder="Completion evidence"><button class="button" type="submit">Mark complete</button></form>
                    @else <span class="badge done">{{ ucfirst($action->status) }}</span> @endif
                </div>
            @empty
                <p class="empty">No next actions have been added.</p>
            @endforelse
            @if ($followUp->status !== 'completed')
                <details><summary>＋ Add Next Action</summary><form class="details-form" method="POST" action="{{ route('child_officer.follow-ups.actions.store', $followUp) }}">@csrf<div class="form-grid"><div class="field wide"><label for="action-description">Action description</label><input id="action-description" name="description" required></div><div><label for="action-assignee">Responsible staff member</label><select id="action-assignee" name="assigned_to" required>@foreach ($staff as $member)<option value="{{ $member->id }}" @selected($member->id === auth()->id())>{{ $member->name }}{{ $member->id === auth()->id() ? ' (me)' : '' }}</option>@endforeach</select></div><div><label for="action-due-date">Deadline</label><input id="action-due-date" type="date" name="due_date"></div></div><div class="form-actions"><button class="button" type="submit">Add action</button></div></form></details>
            @endif
        </section>

        <section class="section">
            <div class="section-letter">F. Completion and closure</div>
            @if ($followUp->status === 'completed')
                <p><strong>Outcome recorded</strong></p><p style="margin:8px 0 12px">{{ $followUp->outcome }}</p><span class="badge done">Follow-up completed</span>
            @else
                <p>Record the outcome after all required actions are finished. If more work remains, keep the follow-up open or reschedule it.</p>
                <form method="POST" action="{{ route('child_officer.follow-ups.complete', $followUp) }}" style="margin-top:12px">@csrf @method('PATCH')<label for="outcome">Final outcome</label><textarea id="outcome" name="outcome" required placeholder="Record the event outcome, completed work and any unresolved matters.">{{ old('outcome') }}</textarea><div class="form-actions"><button class="button complete-button" type="submit">Complete Follow-up</button></div></form>
            @endif
        </section>
    </main>
</div>
</body>
</html>