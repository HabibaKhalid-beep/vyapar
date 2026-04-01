<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estimate Preview</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <div>
                        <h3 class="mb-1">Estimate / Quotation</h3>
                        <p class="text-muted mb-0">Reference No: {{ $sale->bill_number ?? '-' }}</p>
                    </div>
                    <div class="text-end">
                        <div><strong>Date:</strong> {{ optional($sale->invoice_date)->format('d/m/Y') ?? '-' }}</div>
                        <div><strong>Status:</strong> {{ ucfirst($sale->status ?? 'open') }}</div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6 class="text-muted">Party</h6>
                        <div>{{ $sale->display_party_name }}</div>
                        <div>{{ $sale->phone ?? '-' }}</div>
                        <div>{{ $sale->billing_address ?? '-' }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Rate</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($sale->items as $index => $item)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $item->item_name ?? '-' }}</td>
                                    <td>{{ $item->quantity ?? 0 }}</td>
                                    <td>{{ $item->unit ?? '-' }}</td>
                                    <td>{{ number_format($item->unit_price ?? 0, 2) }}</td>
                                    <td>{{ number_format($item->amount ?? 0, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="row justify-content-end mt-4">
                    <div class="col-md-4">
                        <table class="table table-sm">
                            <tr>
                                <th>Total Qty</th>
                                <td class="text-end">{{ $sale->total_qty ?? 0 }}</td>
                            </tr>
                            <tr>
                                <th>Total Amount</th>
                                <td class="text-end">{{ number_format($sale->total_amount ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <th>Grand Total</th>
                                <td class="text-end fw-bold">{{ number_format($sale->grand_total ?? 0, 2) }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(!empty($autoPrint))
        <script>
            window.addEventListener('load', function () {
                window.print();
            });
        </script>
    @endif
</body>
</html>
