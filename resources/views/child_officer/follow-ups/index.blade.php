<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Follow-ups | KFHI-CMS</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; background: #f4f8f5; color: #1e2922; font-family: Arial, sans-serif; }
        .page-shell { display: flex; min-height: 100vh; }
        main { width: min(1440px, 100%); margin: 0 auto; padding: 34px clamp(18px, 4vw, 54px); }
        .heading, .row, .record-top, .record-bottom { display: flex; align-items: center; justify-content: space-between; gap: 16px; }
        .heading { margin-bottom: 22px; }
        h1 { margin: 0 0 5px; font-size: 26px; }
        h2 { margin: 0 0 4px; font-size: 17px; }
        p { margin: 0; color: #68766d; font-size: 13px; }
        .button { display: inline-flex; align-items: center; justify-content: center; min-height: 38px; padding: 0 15px; border: 1px solid #2e7d32; border-radius: 7px; background: #2e7d32; color: #fff; font-weight: 700; text-decoration: none; font-size: 13px; }
        .button.secondary { color: #2e7d32; background: #fff; }
        .stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 12px; margin-bottom: 26px; }
        .stat, .record, .filter-panel { border: 1px solid #dce5de; border-radius: 8px; background: #fff; }
        .stat { padding: 15px 17px; }
        .stat-label { color: #68766d; font-size: 12px; }
        .stat-value { margin: 8px 0 4px; font-size: 27px; font-weight: 700; line-height: 1; }
        .stat-note { color: #68766d; font-size: 11px; }
        .section-head { margin: 0 0 12px; }
        .tabs { display: flex; gap: 18px; overflow-x: auto; border-bottom: 1px solid #dce5de; margin-bottom: 16px; }
        .tab { flex: 0 0 auto; padding: 0 2px 11px; color: #68766d; font-size: 13px; text-decoration: none; }
        .tab.active { border-bottom: 2px solid #2e7d32; color: #2e7d32; font-weight: 700; }
        .filter-panel { padding: 14px; margin-bottom: 16px; }
        .filters { display: grid; grid-template-columns: minmax(200px, 1fr) repeat(2, minmax(140px, 190px)) auto; gap: 10px; }
        input, select, textarea { width: 100%; min-height: 39px; border: 1px solid #d5dfd8; border-radius: 6px; padding: 9px 11px; background: #fff; color: #1e2922; font: inherit; font-size: 13px; }
        .list-heading { margin: 16px 0 10px; color: #68766d; font-size: 12px; font-weight: 700; }
        .records { display: grid; gap: 10px; }
        .record { padding: 16px; }
        .record-top { align-items: flex-start; }
        .event-name { color: #68766d; font-size: 11px; }
        .record h3 { margin: 5px 0 8px; font-size: 15px; }
        .record-meta { display: flex; flex-wrap: wrap; gap: 7px 18px; color: #5d6b62; font-size: 12px; }
        .due { color: #53645a; font-size: 12px; white-space: nowrap; }
        .badge { display: inline-block; padding: 4px 8px; border-radius: 99px; background: #edf3ee; color: #526258; font-size: 10px; font-weight: 700; white-space: nowrap; }
        .badge.high, .badge.urgent, .badge.overdue { background: #fff0e8; color: #ad4c13; }
        .badge.completed { background: #e6f4e8; color: #26733a; }
        .badge.progress { background: #eaf1ff; color: #315a9a; }
        .record-bottom { margin-top: 13px; }
        .record-bottom a { color: #2e7d32; font-size: 12px; font-weight: 700; text-decoration: none; }
        .empty { padding: 30px 16px; text-align: center; color: #68766d; }
        .pagination { margin-top: 16px; }
        .alert { padding: 11px 13px; margin: 0 0 14px; border-radius: 6px; background: #e6f4e8; color: #26733a; font-size: 13px; }
        @media (max-width: 760px) { .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); } .filters { grid-template-columns: 1fr 1fr; } .filters input { grid-column: 1 / -1; } .heading { align-items: flex-start; } }
        @media (max-width: 450px) { .record-top { display: block; } .due { display: inline-block; margin-top: 9px; } }
    </style>
</head>
<body>
<div class="page-shell">
    @include('components.child-officer-sidebar')
    <main>
        @if (session('success')) <div class="alert">{{ session('success') }}</div> @endif
        <header class="heading">
            <div><h1>Follow-ups</h1><p>Manage event outcomes, pending actions and progress.</p></div>
            <a class="button" href="{{ route('child_officer.follow-ups.create') }}">＋ New Follow-up</a>
        </header>

        <section class="stats" aria-label="Follow-up summary">
            <article class="stat"><div class="stat-label">Total Open</div><div class="stat-value">{{ $stats['open'] }}</div><div class="stat-note">Unresolved tasks</div></article>
            <article class="stat"><div class="stat-label">Due Today</div><div class="stat-value">{{ str_pad($stats['today'], 2, '0', STR_PAD_LEFT) }}</div><div class="stat-note">Need attention</div></article>
            <article class="stat"><div class="stat-label">Overdue</div><div class="stat-value">{{ str_pad($stats['overdue'], 2, '0', STR_PAD_LEFT) }}</div><div class="stat-note">Past due date</div></article>
            <article class="stat"><div class="stat-label">Completed</div><div class="stat-value">{{ $stats['completed'] }}</div><div class="stat-note">This month</div></article>
        </section>

        <div class="section-head"><h2>All follow-up tasks</h2></div>
        <nav class="tabs" aria-label="Follow-up views">
            <a class="tab {{ !request('view') ? 'active' : '' }}" href="{{ route('child_officer.follow-ups.index') }}">Overview</a>
            <a class="tab {{ request('view') === 'upcoming' ? 'active' : '' }}" href="{{ route('child_officer.follow-ups.index', ['view' => 'upcoming']) }}">Upcoming Follow-ups</a>
            <a class="tab {{ request('view') === 'history' ? 'active' : '' }}" href="{{ route('child_officer.follow-ups.index', ['view' => 'history']) }}">Follow-up History</a>
            <a class="tab {{ request('view') === 'reports' ? 'active' : '' }}" href="{{ route('child_officer.follow-ups.index', ['view' => 'reports']) }}">Reports</a>
        </nav>

        @if (request('view') === 'reports')
            <section class="stats" aria-label="Follow-up reports">
                <article class="stat"><div class="stat-label">Completion rate</div><div class="stat-value">{{ $reportTotals['completion_rate'] }}%</div><div class="stat-note">All recorded follow-ups</div></article>
                <article class="stat"><div class="stat-label">Completed</div><div class="stat-value">{{ $reportTotals['completed'] }}</div><div class="stat-note">All time</div></article>
                <article class="stat"><div class="stat-label">Overdue</div><div class="stat-value">{{ $reportTotals['overdue'] }}</div><div class="stat-note">Currently past due</div></article>
                <article class="stat"><div class="stat-label">Open workload</div><div class="stat-value">{{ $reportTotals['open'] }}</div><div class="stat-note">Tasks not yet closed</div></article>
            </section>
            <h2 class="section-head">Assigned workload</h2>
            <section class="records" style="margin-bottom:18px">
                @forelse ($workload as $member)
                    <article class="record"><div class="record-top"><strong>{{ $member->name }}</strong><span class="badge">{{ $member->assigned_event_follow_ups_count }} assigned</span></div></article>
                @empty
                    <article class="record empty">No assigned follow-ups to report.</article>
                @endforelse
            </section>
        @endif

        <form class="filter-panel" method="GET" action="{{ route('child_officer.follow-ups.index') }}">
            @if (request('view')) <input type="hidden" name="view" value="{{ request('view') }}"> @endif
            <div class="filters">
                <input name="search" value="{{ request('search') }}" placeholder="Search event, task, or follow-up ID..." aria-label="Search follow-ups">
                <select name="status" aria-label="Filter by status">
                    <option value="">All statuses</option>
                    @foreach (['open' => 'Open', 'pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <select name="priority" aria-label="Filter by priority">
                    <option value="">All priorities</option>
                    @foreach (['low', 'normal', 'high', 'urgent'] as $priority)
                        <option value="{{ $priority }}" @selected(request('priority') === $priority)>{{ ucfirst($priority) }}</option>
                    @endforeach
                </select>
                <button class="button secondary" type="submit">Apply filters</button>
            </div>
        </form>

        <div class="row list-heading"><span>Follow-up records</span><span>{{ $followUps->total() }} records</span></div>
        <section class="records">
            @forelse ($followUps as $followUp)
                @php
                    $isOpen = in_array($followUp->status, ['open', 'pending', 'in_progress']);
                    $isOverdue = $isOpen && $followUp->due_date && $followUp->due_date->isPast() && !$followUp->due_date->isToday();
                    $statusLabel = $isOverdue ? 'Overdue' : match ($followUp->status) {
                        'pending', 'open' => 'Open', 'in_progress' => 'In Progress', 'completed' => 'Completed', 'cancelled' => 'Cancelled', default => ucfirst($followUp->status),
                    };
                @endphp
                <article class="record">
                    <div class="record-top">
                        <div>
                            <div class="event-name">FU-{{ str_pad($followUp->id, 3, '0', STR_PAD_LEFT) }} · {{ $followUp->event?->title ?? 'Event unavailable' }}</div>
                            <h3>{{ $followUp->title }}</h3>
                            <div class="record-meta"><span>Assigned to {{ $followUp->assignee?->name ?? 'Unassigned' }}</span><span>Due date · {{ $followUp->due_date?->format('d M Y') ?? 'Not set' }}</span></div>
                        </div>
                        <div class="due"><span class="badge {{ $isOverdue ? 'overdue' : ($followUp->status === 'completed' ? 'completed' : ($followUp->status === 'in_progress' ? 'progress' : '')) }}">{{ $statusLabel }}</span><br><span class="badge {{ in_array($followUp->priority, ['high', 'urgent']) ? $followUp->priority : '' }}" style="margin-top:6px">{{ ucfirst($followUp->priority) }}</span></div>
                    </div>
                    <div class="record-bottom"><span class="event-name">{{ $followUp->event?->event_code }}</span><a href="{{ route('child_officer.follow-ups.show', $followUp) }}">View details&nbsp; ›</a></div>
                </article>
            @empty
                <div class="record empty">No follow-up records match these filters. Create a follow-up from an existing event to get started.</div>
            @endforelse
        </section>
        <div class="pagination">{{ $followUps->links() }}</div>
    </main>
</div>
</body>
</html>