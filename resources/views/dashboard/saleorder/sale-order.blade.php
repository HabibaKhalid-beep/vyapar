<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vyapar — Sale Orders</title>
  <meta name="description" content="Record supplier purchase bills with live preview in Vyapar.">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <!-- Custom Styles -->
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <style>
    .sale-order-page {
      padding: 1.25rem;
    }

    .sale-order-card {
      border: 0;
      border-radius: 16px;
      box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    }

    .sale-order-toolbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 1rem;
      margin-bottom: 1.25rem;
      flex-wrap: wrap;
    }

    .sale-order-search {
      position: relative;
      min-width: 280px;
      max-width: 360px;
      width: 100%;
    }

    .sale-order-search i {
      position: absolute;
      left: 16px;
      top: 50%;
      transform: translateY(-50%);
      color: #64748b;
    }

    .sale-order-search input {
      border-radius: 999px;
      border: 1px solid #d7deea;
      padding: 0.85rem 1rem 0.85rem 2.75rem;
      width: 100%;
      background: #fff;
    }

    .sale-order-add-btn {
      border-radius: 999px;
      background: #1d8cf8;
      border: 0;
      color: #fff;
      padding: 0.8rem 1.35rem;
      font-weight: 600;
      box-shadow: 0 10px 20px rgba(29, 140, 248, 0.18);
    }

    .sale-order-table {
      min-width: 1180px;
    }

    .sale-order-table thead th {
      background: #f8fbff;
      color: #334155;
      font-size: 0.92rem;
      font-weight: 700;
      border-bottom: 1px solid #dbe4f0;
      padding: 1rem 0.85rem;
      vertical-align: middle;
      white-space: nowrap;
    }

    .sale-order-table tbody td {
      padding: 1rem 0.85rem;
      border-bottom: 1px solid #edf2f7;
      vertical-align: middle;
      color: #0f172a;
      white-space: nowrap;
    }

    .sale-order-table tbody tr:hover {
      background: #f8fbff;
    }

    .status-pill {
      display: inline-flex;
      align-items: center;
      border-radius: 999px;
      padding: 0.38rem 0.8rem;
      font-size: 0.83rem;
      font-weight: 600;
    }

    .status-pill.overdue {
      background: #fff4e8;
      color: #f97316;
    }

    .status-pill.completed {
      background: #e9f9ef;
      color: #16a34a;
    }

    .status-pill.pending {
      background: #eef4ff;
      color: #2563eb;
    }

    .convert-btn {
      border-radius: 8px;
      border: 1px solid #d8dee9;
      background: #fff;
      color: #6366f1;
      font-weight: 600;
      padding: 0.55rem 0.95rem;
      white-space: nowrap;
      box-shadow: 0 2px 6px rgba(15, 23, 42, 0.06);
    }

    .converted-link {
      color: #6366f1;
      font-weight: 500;
      text-decoration: underline;
      text-underline-offset: 2px;
    }

    .action-menu-btn {
      border: 0;
      background: transparent;
      color: #64748b;
      padding: 0.35rem 0.5rem;
    }

    .action-menu-btn::after {
      display: none;
    }
  </style>

   <script>
    // Ensure window.App is always initialized, even if Auth is null
    const authUser = @json(Auth::user());
    window.App = window.App || {
      isAuthenticated: @json(Auth::check()),
      user: authUser ? {
        id: authUser.id,
        name: authUser.name,
        roles: @json(Auth::user()?->roles()->pluck('name')->toArray() ?? []),
        permissions: @json(Auth::user()?->getAllPermissions() ?? []),
      } : { id: null, name: null, roles: [], permissions: [] },
      logoutUrl: "{{ route('logout') }}",
      csrfToken: "{{ csrf_token() }}",
    };
    console.log('App initialized:', window.App);
  </script>


</head>

<body data-page="sale-orders">

  <!-- Navbar & Sidebar injected by components.js -->

  <!-- ═══════════════════════════════════════
     MAIN CONTENT — PURCHASE BILL
     ═══════════════════════════════════════ -->
  <main class="main-content sale-order-page" id="mainContent">


    <div class="d-flex justify-content-between align-items-center bg-light p-3 border-bottom mb-2">
      <div class="text-center col-12">
        <h4 class="text-secondary">Sales Orders</h4>

      </div>

    </div>

    <div class="card sale-order-card">
      <div class="card-body">
        <div class="row g-2 mb-1">
          <p class="fw-bold">Transactions</p>
        </div>
        <div class="sale-order-toolbar">
          <form method="GET" action="{{ route('sale-order') }}" class="sale-order-search">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Search..." name="search" value="{{ $search ?? '' }}">
          </form>
          <div>
            <button class="btn sale-order-add-btn" onclick="window.location='{{ route('sale-order.create') }}'">
              <i class="fa-solid fa-plus me-2"></i>Add Sale Order
            </button>
          </div>
        </div>

        <div class="table-responsive small-table">
          <table class="table sale-order-table align-middle mb-0">
            <thead>
              <tr>
                <th>Party</th>
                <th>No.</th>
                <th>Date</th>
                <th>Due Date</th>
                <th class="text-end">Total Amount</th>
                <th class="text-end">Balance</th>
                <th>Type</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody>
              @forelse(($saleOrders ?? collect()) as $saleOrder)
                @php
                  $isCompleted = $saleOrder->status === 'completed';
                  $isOverdue = !$isCompleted && $saleOrder->due_date && $saleOrder->due_date->isPast();
                  $statusLabel = $isCompleted ? 'Order Completed' : ($isOverdue ? 'Order Overdue' : ucfirst($saleOrder->status ?? 'pending'));
                  $convertedInvoiceNumber = $convertedInvoices[$saleOrder->id] ?? $saleOrder->reference_id ?? null;
                @endphp
                <tr>
                  <td>{{ $saleOrder->display_party_name }}</td>
                  <td>{{ $saleOrder->bill_number ?? '-' }}</td>
                  <td>{{ optional($saleOrder->order_date)->format('d/m/Y') ?? '-' }}</td>
                  <td>{{ optional($saleOrder->due_date)->format('d/m/Y') ?? '-' }}</td>
                  <td class="text-end">Rs {{ number_format($saleOrder->grand_total ?? 0, 2) }}</td>
                  <td class="text-end">Rs {{ number_format($saleOrder->balance ?? 0, 2) }}</td>
                  <td>Sale Order</td>
                  <td>
                    <span class="status-pill {{ $isCompleted ? 'completed' : ($isOverdue ? 'overdue' : 'pending') }}">
                      {{ $statusLabel }}
                    </span>
                  </td>
                  <td>
                    @if($isCompleted)
                      <a href="{{ route('sale.edit', $saleOrder->id) }}" class="converted-link">
                        Converted To Invoice No.{{ $convertedInvoiceNumber }}
                      </a>
                    @else
                      <a href="{{ route('sale-orders.convert-to-sale', $saleOrder->id) }}" class="btn convert-btn btn-sm">
                        CONVERT TO SALE
                      </a>
                    @endif
                    <div class="dropdown d-inline ms-2">
                      <button class="btn btn-sm action-menu-btn dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('sale.edit', $saleOrder->id) }}"><i class="fas fa-edit me-2"></i>View/Edit</a></li>
                        <li><a class="dropdown-item" href="#" onclick="previewSaleOrder('{{ route('sale-orders.preview', $saleOrder->id) }}'); return false;"><i class="fas fa-file-alt me-2"></i>Preview</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printSaleOrder('{{ route('sale-orders.print', $saleOrder->id) }}'); return false;"><i class="fas fa-print me-2"></i>Print</a></li>
                        <li><a class="dropdown-item" href="#" onclick="openSaleOrderPdf('{{ route('sale-orders.pdf', $saleOrder->id) }}'); return false;"><i class="fas fa-file-pdf me-2"></i>Open PDF</a></li>
                        <li><a class="dropdown-item" href="#" onclick="duplicateSaleOrder('{{ route('sale.edit', $saleOrder->id) }}'); return false;"><i class="fas fa-copy me-2"></i>Duplicate</a></li>
                        <li><a class="dropdown-item" href="#" onclick="viewSaleOrderHistory({{ $saleOrder->id }}); return false;"><i class="fas fa-clock-rotate-left me-2"></i>View History</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteSaleOrder('{{ route('sale.destroy', $saleOrder->id) }}'); return false;"><i class="fas fa-trash me-2"></i>Delete</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="9" class="text-center text-muted py-4">
                    No sale orders found.
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </main>

  <!-- ═══════════════════════════════════════════
     SCRIPTS
     ═══════════════════════════════════════════ -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/components.js') }}"></script>
  <script src="{{ asset('js/common.js') }}"></script>
  <script>
    function previewSaleOrder(url) {
      window.open(url, '_blank');
    }

    function printSaleOrder(url) {
      window.open(url, '_blank');
    }

    function openSaleOrderPdf(url) {
      window.open(url, '_blank');
    }

    function duplicateSaleOrder(url) {
      window.open(url, '_blank');
    }

    function viewSaleOrderHistory(id) {
      alert('History for sale order #' + id + ' will be added next.');
    }

    function deleteSaleOrder(url) {
      if (!confirm('Are you sure you want to delete this sale order?')) {
        return;
      }

      fetch(url, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
          'Accept': 'application/json',
        },
      })
        .then(async (response) => {
          const data = await response.json();

          if (!response.ok) {
            throw new Error(data.message || 'Delete failed');
          }

          window.location.reload();
        })
        .catch((error) => {
          alert(error.message || 'Unable to delete sale order.');
        });
    }
  </script>
  <script src="{{ asset('js/sale-orders.js') }}"></script>

</body>

</html>
