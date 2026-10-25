
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Child Management Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 10px;
            color: #222;
        }

        h1 {
            text-align: center;
            color: #2e7d32;
        }

        p {
            text-align: center;
            color: #666;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #e8f5e9;
        }
    </style>
</head>
<body>
    <h1>Child Management Report</h1>
    <p>Generated: {{ now()->format('Y-m-d H:i') }}</p>

    <table>
        <thead>
            <tr>
                <th>Child Code</th>
                <th>First Name</th>
                <th>Middle Name</th>
                <th>Last Name</th>
                <th>District</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($children as $child)
                <tr>
                    <td>{{ $child->child_code }}</td>
                    <td>{{ $child->first_name }}</td>
                    <td>{{ $child->middle_name }}</td>
                    <td>{{ $child->last_name }}</td>
                    <td>{{ $child->district ?: 'Not specified' }}</td>
                    <td>{{ $child->status }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">No child records found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
