<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Child Registration Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 10px; }
        h1 { color: #2e7d32; }
        p { color: #555; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 6px; text-align: left; }
        th { background: #e8f5e9; }
    </style>
</head>
<body>
    <h1>KFHI-CMS Child Registration Report</h1>
    <p>Generated: {{ now()->format('d M Y, h:i A') }}</p>
    <p>Total records in this export: {{ $children->count() }}</p>

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
                    <td>{{ $child->district ?: 'Not specified' }}</td>
                    <td>{{ $child->school_name ?: '—' }}</td>
                    <td>{{ $child->grade ?: '—' }}</td>
                    <td>{{ $child->created_at?->format('Y-m-d H:i') ?? '—' }}</td>
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
</body>
</html>
