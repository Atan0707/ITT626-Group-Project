<!DOCTYPE html>
<html>
<head>
    <title>Parcels for {{ $formattedDate }}</title>
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }
            body {
                margin: 0;
                padding: 0;
                font-family: Arial, sans-serif;
            }
            .no-print {
                display: none;
            }
        }
        
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
            font-size: 14px;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .date {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 10px;
            text-align: left;
        }
        
        th {
            background-color: #f0f0f0;
        }
        
        .no-print {
            margin-bottom: 20px;
        }
        
        .daily-number {
            font-weight: bold;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()">Print</button>
        <a href="{{ route('admin.packages.index') }}"><button type="button">Back</button></a>
    </div>

    <div class="header">
        <div class="date">Parcels for {{ $formattedDate }}</div>
        <div>Total Parcels: {{ $packages->count() }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 80px">No #</th>
                <th>Name</th>
            </tr>
        </thead>
        <tbody>
            @forelse($packages as $package)
                <tr>
                    <td class="daily-number">#{{ $package->daily_number }}</td>
                    <td>{{ $package->name }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" style="text-align: center">No packages found for this date</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html> 