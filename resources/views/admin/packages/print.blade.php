<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Package List - {{ $formattedDate }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        @media print {
            @page {
                size: A4;
                margin: 1cm;
            }
            body {
                font-size: 12pt;
            }
            .no-print {
                display: none;
            }
            .table td, .table th {
                padding: 0.5rem;
            }
        }
        .print-header {
            text-align: center;
            margin-bottom: 20px;
        }
        .shop-info {
            margin-bottom: 20px;
        }
        .date-info {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Print Header -->
        <div class="print-header">
            <h2>Package List</h2>
        </div>

        <!-- Shop Info -->
        <div class="shop-info">
            @if(Auth::guard('staff')->check())
                <h5>{{ Auth::guard('staff')->user()->shop->name }}</h5>
                <p>{{ Auth::guard('staff')->user()->shop->address }}</p>
            @else
                <h5>Admin View</h5>
                <p>All Shops</p>
            @endif
        </div>

        <!-- Date Info -->
        <div class="date-info">
            <strong>Date:</strong> {{ $formattedDate }}
            <br>
            <strong>Total Packages:</strong> {{ $packages->count() }}
        </div>

        <!-- Print Button (visible only on screen) -->
        <div class="no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print"></i> Print List
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        <!-- Packages Table -->
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="width: 80px">No #</th>
                        <th>Tracking Number</th>
                        <th>Name</th>
                        <th>Collection Signature</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($packages as $package)
                        <tr>
                            <td class="text-center">#{{ $package->daily_number }}</td>
                            <td>{{ $package->tracking_number }}</td>
                            <td>{{ $package->name }}</td>
                            <td style="height: 50px"></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No packages found for this date</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="mt-4">
            <p class="text-center">
                <small>Printed on {{ now()->format('d M Y h:i A') }}</small>
            </p>
        </div>
    </div>
</body>
</html> 