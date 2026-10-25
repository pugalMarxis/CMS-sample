
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Reports | KFHI-CMS</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f4f8f5;
            color: #26382c;
        }
        .co-layout { display: flex; min-height: 100vh; }
        .co-main { flex: 1; min-width: 0; padding: 32px; }
        .page-heading {
            display: flex; justify-content: space-between;
            align-items: center; gap: 16px; flex-wrap: wrap;
            margin-bottom: 26px;
        }
        h1 { margin: 0 0 8px; font-size: 28px; }
        .muted { color: #728075; font-size: 13px; }
        .eyebrow {
            color: #2e7d32; font-size: 11px;
            font-weight: bold; letter-spacing: 1.4px;
            text-transform: uppercase; margin-bottom: 9px;
        }
        .date-label {
            background: white; border: 1px solid #dfe9e2;
            padding: 12px 15px; border-radius: 10px;
            font-size: 13px;
        }
        .stats {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 16px; margin-bottom: 22px;
        }
        .stat, .panel {
            background: white; border: 1px solid #e1eae3;
            border-radius: 13px; padding: 21px;
            box-shadow: 0 3px 12px rgba(33, 70, 42, .035);
        }
        .stat-top {
            display: flex; align-items: center;
            justify-content: space-between; gap: 8px;
            color: #647469; font-size: 12px;
        }
        .stat-icon {
            display: grid; place-items: center;
            width: 36px; height: 36px; border-radius: 10px;
            background: #e8f5e9; color: #2e7d32;
            font-size: 17px;
        }
        .stat-number { margin: 17px 0 8px; font-size: 29px; font-weight: 700; }
        .stat-note { color: #849087; font-size: 11px; }
        .middle-grid {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 18px; margin-bottom: 22px;
        }
        .panel { min-width: 0; margin-bottom: 0; }
        .panel-heading {
            display: flex; justify-content: space-between;
            align-items: center; gap: 12px; margin-bottom: 18px;
        }
        h2 { font-size: 16px; margin: 0 0 6px; }
        .panel-subtitle { color: #849087; font-size: 12px; }
        .pill {
            display: inline-block; padding: 6px 9px;
            border-radius: 20px; font-size: 11px;
            font-weight: 700; background: #e8f5e9; color: #2e7d32;
        }
        .pill.warning { background: #fff0df; color: #a45c08; }
        .pill.danger { background: #fdeaea; color: #b33e3e; }
        .metric-row { margin: 20px 0; }
        .metric-label {
            display: flex; justify-content: space-between;
            gap: 10px; font-size: 12px; margin-bottom: 9px;
        }
        .track { height: 8px; background: #edf2ee; border-radius: 20px; overflow: hidden; }
        .bar { height: 100%; background: #43a047; border-radius: 20px; }
        .bar.orange { background: #e6a23c; }
        .bar.red { background: #d95d5d; }
        .district-row {
            display: flex; justify-content: space-between;
            align-items: center; gap: 12px;
            padding: 12px 0; border-bottom: 1px solid #eef2ee;
            font-size: 12px;
        }
        .district-row:last-child { border-bottom: 0; }
        .district-name { overflow-wrap: anywhere; }
        .district-count {
            font-weight: 700; color: #2e7d32;
            background: #e8f5e9; border-radius: 8px;
            padding: 5px 9px; white-space: nowrap;
        }
        .table-panel { padding: 22px; }
        .filters {
            display: grid; grid-template-columns: 2fr 1fr 1fr auto;
            gap: 10px; margin: 20px 0;
        }
        input, select {
            width: 100%; min-width: 0; padding: 11px 12px;
            border: 1px solid #dce6de; border-radius: 8px;
            background: white; color: #35473a; font-size: 12px;
        }
        .btn {
            display: inline-flex; justify-content: center;
            align-items: center; padding: 11px 15px;
            border: 0; border-radius: 8px;
            background: #2e7d32; color: white;
            font-size: 12px; font-weight: 700;
            text-decoration: none; cursor: pointer;
        }
        .btn-light { background: #edf4ee; color: #35633a; }
        .table-wrap { width: 100%; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th {
            background: #f6f9f6; color: #718075;
            font-size: 10px; text-transform: uppercase;
            letter-spacing: .5px; white-space: nowrap;
        }
        th, td { padding: 14px 12px; border-bottom: 1px solid #edf1ed; }
        td { font-size: 12px; }
        .child-name { font-weight: 700; margin-bottom: 5px; }
        .child-code { color: #849087; font-size: 11px; }
        .status {
            display: inline-block; border-radius: 20px;
            padding: 6px 9px; font-size: 10px;
            font-weight: 700; text-transform: capitalize;
        }
        .status.active { background: #e4f5e7; color: #247438; }
        .status.inactive { background: #f0eeee; color: #686868; }
        .empty { text-align: center; padding: 35px 10px; color: #849087; }
        .pagination {
            display: flex; justify-content: space-between;
            align-items: center; gap: 12px; flex-wrap: wrap;
            margin-top: 18px; font-size: 12px; color: #718075;
        }
        .pagination nav p { margin: 0; }
        .pagination nav a, .pagination nav span {
            display: inline-block; padding: 7px 10px;
            margin: 2px; border: 1px solid #dfe9e2;
            border-radius: 6px; text-decoration: none; color: #35633a;
            font-size: 12px;
        }
        .pagination nav span[aria-current="page"] span {
            background: #2e7d32; color: white;
        }
        @media (max-width: 1100px) {
            .stats { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .filters { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 750px) {
            .co-layout { flex-direction: column; }
            .co-main { padding: 20px 14px; }
            .middle-grid { grid-template-columns: 1fr; }
            .page-heading { align-items: flex-start; }
        }
        @media (max-width: 430px) {
            .stats { grid-template-columns: 1fr 1fr; gap: 9px; }
            .stat { padding: 14px; }
            .stat-number { font-size: 23px; }
            .filters { grid-template-columns: 1fr; }
        }
        @media print {
            .child-officer-sidebar, .child-officer-sidebar-toggle,
            .filters, .pagination, .date-label { display: none !important; }
            body { background: white; }
            .co-main { padding: 0; }
            .stat, .panel { box-shadow: none; break-inside: avoid; }
        }
    </style>
</head>
<body>
<div class="co-layout">
    @include('components.child-officer-sidebar')

    <main class="co-main">
        <header class="page-heading">
            <div>
                <div class="eyebrow">Child Officer / Reports</div>
                <h1>Child Reports</h1>
                <div class="muted">Monitor child registrations, follow-ups and attendance.</div>
            </div>
            <div class="date-label">📅 {{ now()->format('d M Y') }}</div>
        </header>

        <section class="stats">
            <article class="stat">
                <div class="stat-top"><span>Total Children</span><span class="stat-icon">♧</span></div>
                <div class="stat-number">{{ number_format($totalChildren) }}</div>
                <div class="stat-note">All non-deleted child records</div>
            </article>
            <article class="stat">
                <div class="stat-top"><span>Active Children</span><span class="stat-icon">✓</span></div>
                <div class="stat-number">{{ number_format($activeChildren) }}</div>
                <div class="stat-note">Currently marked active</div>
            </article>
            <article class="stat">
                <div class="stat-top"><span>Inactive Children</span><span class="stat-icon">Ⅱ</span></div>
                <div class="stat-number">{{ number_format($inactiveChildren) }}</div>
                <div class="stat-note">Currently marked inactive</div>
            </article>
            <article class="stat">
                <div class="stat-top"><span>New This Month</span><span class="stat-icon">＋</span></div>
                <div class="stat-number">{{ number_format($newThisMonth) }}</div>
                <div class="stat-note">Registered during this month</div>
            </article>
        </section>

        <section class="middle-grid">
            <article class="panel">
                <div class="panel-heading">
                    <div><h2>Follow-up Overview</h2><div class="panel-subtitle">Tasks that need monitoring</div></div>
                    <span class="pill warning">Follow-ups</span>
                </div>
                <div class="metric-row">
                    <div class="metric-label"><span>Pending follow-ups</span><strong>{{ number_format($pendingFollowUps) }}</strong></div>
                    <div class="track"><div class="bar orange" style="width: {{ $pendingFollowUps > 0 ? min(100, ($pendingFollowUps / max(1, $pendingFollowUps + $overdueFollowUps)) * 100) : 0 }}%"></div></div>
                </div>
                <div class="metric-row">
                    <div class="metric-label"><span>Overdue follow-ups</span><strong>{{ number_format($overdueFollowUps) }}</strong></div>
                    <div class="track"><div class="bar red" style="width: {{ $overdueFollowUps > 0 ? min(100, ($overdueFollowUps / max(1, $pendingFollowUps + $overdueFollowUps)) * 100) : 0 }}%"></div></div>
                </div>
                <div class="muted">Overdue means a pending follow-up with a follow-up date before today.</div>
            </article>

            <article class="panel">
                <div class="panel-heading">
                    <div><h2>Attendance Overview</h2><div class="panel-subtitle">Recorded attendance entries</div></div>
                    <span class="pill">Attendance</span>
                </div>
                <div class="metric-row">
                    <div class="metric-label"><span>Total attendance records</span><strong>{{ number_format($attendanceTotal) }}</strong></div>
                    <div class="track"><div class="bar" style="width: {{ $attendanceTotal > 0 ? 100 : 0 }}%"></div></div>
                </div>
                <div class="metric-row">
                    <div class="metric-label"><span>Marked present</span><strong>{{ number_format($presentAttendance) }}</strong></div>
                    <div class="track"><div class="bar" style="width: {{ $attendanceTotal > 0 ? min(100, ($presentAttendance / $attendanceTotal) * 100) : 0 }}%"></div></div>
                </div>
                <div class="muted">Present percentage: {{ $attendanceTotal > 0 ? number_format(($presentAttendance / $attendanceTotal) * 100, 1) : '0.0' }}%</div>
            </article>
        </section>

        <section class="panel" style="margin-bottom:22px">
            <div class="panel-heading">
                <div><h2>Children by District</h2><div class="panel-subtitle">Distribution of registered children by district</div></div>
                <span class="pill">{{ $districtStats->count() }} districts/groups</span>
            </div>
            @if($districtStats->isEmpty())
                <div class="empty">No district information is available yet.</div>
            @else
                @php $maxDistrictCount = max(1, (int) $districtStats->max('total')); @endphp
                @foreach($districtStats as $district)
                    <div class="district-row">
                        <span class="district-name">{{ $district->district_name }}</span>
                        <span style="display:flex;align-items:center;gap:12px;min-width:45%;justify-content:flex-end">
                            <span class="track" style="width:100%;max-width:260px"><span class="bar" style="display:block;width:{{ min(100, ($district->total / $maxDistrictCount) * 100) }}%"></span></span>
                            <span class="district-count">{{ number_format($district->total) }}</span>
                        </span>
                    </div>
                @endforeach
            @endif
        </section>

        <section class="panel table-panel">
            <div class="panel-heading">
                <div><h2>Child Records</h2><div class="panel-subtitle">Search and filter records for reporting</div></div>
                
                    <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                        <a class="btn" href="{{ route('child_officer.reports.export.excel', request()->query()) }}">
                            Export Excel
                        </a>

                        <a class="btn" style="background: #b33e3e;"
                        href="{{ route('child_officer.reports.export.pdf', request()->query()) }}">
                            Export PDF
                        </a>

                        <button class="btn btn-light" type="button" onclick="window.print()">
                            Print Report
                        </button>
                    </div>

            </div>

            <form class="filters" method="GET" action="{{ route('child_officer.reports.index') }}">
                <input type="search" name="search" value="{{ request('search') }}" placeholder="Search name or child code...">
                <select name="status">
                    <option value="">All statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                <select name="district">
                    <option value="">All districts</option>
                    @foreach($districts as $district)
                        <option value="{{ $district }}" {{ request('district') === $district ? 'selected' : '' }}>{{ $district }}</option>
                    @endforeach
                </select>
                <button class="btn" type="submit">Apply Filters</button>
            </form>

            <div class="table-wrap">
                <table>
                    <thead>
                    <tr>
                        <th>Child</th>
                        <th>Gender</th>
                        <th>District</th>
                        <th>School / Grade</th>
                        <th>Follow-ups</th>
                        <th>Attendance</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($children as $child)
                        <tr>
                            <td>
                                <div class="child-name">{{ trim($child->first_name.' '.($child->middle_name ?? '').' '.($child->last_name ?? '')) }}</div>
                                <div class="child-code">{{ $child->child_code }}</div>
                            </td>
                            <td>{{ $child->gender ?: '—' }}</td>
                            <td>{{ $child->district ?: 'Not specified' }}</td>
                            <td>{{ $child->school_name ?: '—' }}<br><span class="muted">Grade: {{ $child->grade ?: '—' }}</span></td>
                            <td>{{ $child->follow_ups_count }}</td>
                            <td>{{ $child->attendances_count }}</td>
                            <td><span class="status {{ strtolower($child->status) === 'active' ? 'active' : 'inactive' }}">{{ $child->status ?: 'Unknown' }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="empty">No child records match your filters.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">
                <div>
                    Showing {{ $children->firstItem() ?? 0 }}–{{ $children->lastItem() ?? 0 }}
                    of {{ number_format($children->total()) }} children
                </div>
                <div>{{ $children->links() }}</div>
            </div>
        </section>
    </main>
</div>
</body>
</html>
