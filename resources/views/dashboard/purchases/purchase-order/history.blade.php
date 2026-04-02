<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Order History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h3 class="mb-1">Purchase Order History</h3>
                        <div class="text-muted">Order No: {{ $purchase->bill_number ?? '-' }}</div>
                    </div>
                    <a href="{{ route('purchase-order') }}" class="btn btn-outline-secondary">Back</a>
                </div>

                <table class="table table-bordered bg-white">
                    <tbody>
                        <tr>
                            <th style="width: 220px;">Party</th>
                            <td>{{ $purchase->party?->name ?? $purchase->party_name ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <td>{{ optional($purchase->created_at)->format('d/m/Y h:i A') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated</th>
                            <td>{{ optional($purchase->updated_at)->format('d/m/Y h:i A') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Total Amount</th>
                            <td>Rs {{ number_format((float) $purchase->grand_total, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Balance</th>
                            <td>Rs {{ number_format((float) $purchase->balance, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>{{ (float) $purchase->balance <= 0 ? 'Order Completed' : 'Open' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
