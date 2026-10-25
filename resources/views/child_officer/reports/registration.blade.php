
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Child Registration Report | KFHI-CMS</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f7f5;
            color: #26352b;
        }
        

main {
    flex: 1;
    min-width: 0;
    margin: 0;
    padding: 30px;
    box-sizing: border-box;
}

h1 {
    color: #246b45;
}
        .stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 16px;
            margin: 24px 0;
        }
        .card, .panel {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 8px #0000000b;
        }
        .number {
            display: block;
            margin-top: 10px;
            font-size: 28px;
            font-weight: bold;
            color: #246b45;
        }
        form {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            align-items: end;
        }
        label {
            display: block;
            margin-bottom: 6px;
            font-size: 13px;
        }
        input, select, button, .btn {
            padding: 10px;
            border: 1px solid #ccd6cf;
            border-radius: 6px;
            font-size: 14px;
        }
        button, .btn {
            background: #246b45;
            color: white;
            text-decoration: none;
            border: 0;
            cursor: pointer;
            display: inline-block;
        }
        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin: 20px 0;
        }
        .table-wrap { overflow-x: auto; }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            background: white;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e4eae5;
            white-space: nowrap;
        }
        th { background: #e8f3eb; }
        .muted { color: #68766d; }
        .pagination { margin-top: 20px; }

        @media (max-width: 700px) {
    body {
        flex-direction: column;
    }

    .stats {
        grid-template-columns: 1fr;
    }

    main {
        padding: 12px;
    }
}
    </style>
</head>
<body style="display: flex; min-height: 100vh; margin: 0;">
    @include('components.child-officer-sidebar')

    <main>
        <h1>Child Registration Report</h1>
        <p class="muted">
            Review registered children and export records using your selected filters.
        </p>

        <section class="stats">
            <div class="card">
                Total matching registrations
                <span class="number">{{ $totalRegistered }}</span>
            </div>
            <div class="card">
                Registered today
                <span class="number">{{ $registeredToday }}</span>
            </div>
            <div class="card">
                Registered this month
                <span class="number">{{ $registeredThisMonth }}</span>
            </div>
        </section>

        <section class="panel">
            <h2>Filter registrations</h2>

            <form method="GET"
                  action="{{ route('child_officer.registration_reports.index') }}">
                <div>
                    <label for="start_date">From date</label>
                    <input type="date" id="start_date" name="start_date"
                           value="{{ request('start_date') }}">
                </div>

                <div>
                    <label for="end_date">To date</label>
                    <input type="date" id="end_date" name="end_date"
                           value="{{ request('end_date') }}">
                </div>

                <div>
                    <label for="district">District</label>
                    <select id="district" name="district">
                        <option value="">All districts</option>
                        @foreach($districts as $district)
                            <option value="{{ $district }}"
                                @selected(request('district') === $district)>
                                {{ $district }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="search">Child name or code</label>
                    <input type="text" id="search" name="search"
                           placeholder="Search..." value="{{ request('search') }}">
                </div>

                <button type="submit">Apply filters</button>
                <a class="btn"
                   href="{{ route('child_officer.registration_reports.index') }}">
                    Clear
                </a>
            </form>

            <div class="actions">
                <a class="btn"
                   href="{{ route('child_officer.registration_reports.export.excel', request()->query()) }}">
                    Export Excel
                </a>
                <a class="btn"
                   href="{{ route('child_officer.registration_reports.export.pdf', request()->query()) }}">
                    Export PDF
                </a>
            </div>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Child Code</th>
                            <th>Child Name</th>
                            <th>Gender</th>
                            <th>District</th>
                            <th>School</th>
                            <th>Grade</th>
                            <th>Registration Date</th>
                            <th>Registered By</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($children as $child)
                            <tr>
                                <td>{{ $child->child_code }}</td>
                                <td>{{ trim($child->first_name.' '.($child->middle_name ?? '').' '.($child->last_name ?? '')) }}</td>
                                <td>{{ $child->gender ?: '—' }}</td>
                                <td>{{ $child->district ?: '—' }}</td>
                                <td>{{ $child->school_name ?: '—' }}</td>
                                <td>{{ $child->grade ?: '—' }}</td>
                                <td>{{ $child->created_at?->format('Y-m-d') ?? '—' }}</td>
                                <td>{{ $child->creator?->name ?? 'Unknown' }}</td>
                                <td>{{ $child->status ?? 'Unknown' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9">No registration records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="pagination">
    {{ $children->links('pagination::simple-tailwind') }}
</div>
        </section>
    </main>
</body>
</html>
