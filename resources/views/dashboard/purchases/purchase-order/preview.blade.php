<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $documentTitle ?? 'Purchase Preview' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
  .custom-table thead th {
    font-size: 13px; color: #6c757d; font-weight: 500;
    border-bottom: 1px solid #eee; position: sticky; top: 0; z-index: 5;
    background-color: #fafafa; white-space: nowrap; position: relative;
  }
  .custom-table tbody td {
    font-size: 14px; padding: 14px 10px;
    border-bottom: 1px solid #f1f1f1; white-space: nowrap;
  }
  .custom-table tbody tr:hover { background-color: #fafafa; }
  .custom-table th, .custom-table td { border-right: 1px solid #f1f1f1; }
  .custom-table th:last-child, .custom-table td:last-child { border-right: none; }
  .table-wrapper {
    overflow-x: auto; overflow-y: auto;
    max-height: 68vh; border: 1px solid #eef2f7; border-radius: 12px;
  }
  @media (max-width: 991px) {
    .table-wrapper { max-height: none; border-radius: 8px; }
    .custom-table thead th { font-size: 11px; padding: 8px 6px; }
    .custom-table tbody td { font-size: 12px; padding: 10px 6px; }
  }
  @media (max-width: 575px) {
    .custom-table thead th { font-size: 10px; padding: 6px 4px; }
    .custom-table tbody td { font-size: 11px; padding: 8px 4px; }
  }
</style>
</head>
<body class="bg-light">
<div class="container py-4">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-start mb-4">
                <div>
                    <h3 class="mb-1">{{ $documentTitle ?? 'Purchase Bill' }}</h3>
                    <div class="text-muted">Bill No: {{ $purchase->bill_number ?? '-' }}</div>
                </div>
                <div class="text-end">
                    <div><strong>Date:</strong> {{ optional($purchase->bill_date)->format('d/m/Y') ?? '-' }}</div>
                    <div><strong>Status:</strong> {{ (float) ($purchase->balance ?? 0) <= 0 ? 'Completed' : 'Open' }}</div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6 class="text-muted">Party</h6>
                    <div>{{ $purchase->party_name ?: ($purchase->party?->name ?? '-') }}</div>
                    <div>{{ $purchase->phone ?? '-' }}</div>
                    <div>{{ $purchase->billing_address ?? '-' }}</div>
                </div>
                <div class="col-md-6 text-md-end">
                    <h6 class="text-muted">Payment</h6>
                    @forelse($purchase->payments as $payment)
                        <div>{{ $payment->bankAccount?->display_with_account ?? $payment->payment_type }} - {{ number_format($payment->amount ?? 0, 2) }}</div>
                    @empty
                        <div>-</div>
                    @endforelse
                </div>
            </div>
<div class="table-wrapper">
  <table class="table align-middle custom-table mb-0">
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
                        @foreach($purchase->items as $index => $item)
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
                            <td class="text-end">{{ $purchase->total_qty ?? 0 }}</td>
                        </tr>
                        <tr>
                            <th>Total Amount</th>
                            <td class="text-end">{{ number_format($purchase->total_amount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Shipping</th>
                            <td class="text-end">{{ number_format($purchase->shipping_charge ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Paid</th>
                            <td class="text-end">{{ number_format($purchase->paid_amount ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Balance</th>
                            <td class="text-end">{{ number_format($purchase->balance ?? 0, 2) }}</td>
                        </tr>
                        <tr>
                            <th>Grand Total</th>
                            <td class="text-end fw-bold">{{ number_format($purchase->grand_total ?? 0, 2) }}</td>
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
<script>
  (function () {
    var isResizing = false, startX = 0, startW = 0, thEl = null;
    function init() {
      document.querySelectorAll('.custom-table thead th').forEach(function (th) {
        if (th.querySelector('.col-rh')) return;
        th.style.position = 'relative';
        var h = document.createElement('div');
        h.className = 'col-rh';
        h.style.cssText = 'position:absolute;right:0;top:0;bottom:0;width:5px;cursor:col-resize;z-index:10;';
        th.appendChild(h);
      });
    }
    document.addEventListener('mousedown', function (e) {
      if (!e.target.classList.contains('col-rh')) return;
      e.preventDefault();
      thEl = e.target.closest('th'); isResizing = true;
      startX = e.clientX; startW = thEl.getBoundingClientRect().width;
      document.body.style.cursor = 'col-resize';
      document.body.style.userSelect = 'none';
    });
    document.addEventListener('mousemove', function (e) {
      if (!isResizing || !thEl) return;
      var w = Math.max(60, startW + (e.clientX - startX));
      thEl.style.minWidth = w + 'px'; thEl.style.width = w + 'px';
    });
    document.addEventListener('mouseup', function () {
      if (!isResizing) return;
      isResizing = false; thEl = null;
      document.body.style.cursor = ''; document.body.style.userSelect = '';
    });
    document.addEventListener('DOMContentLoaded', init);
  })();
</script>
</body>
</html>
