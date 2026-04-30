<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vyapar — Sales Invoices</title>
  <meta name="description" content="Create professional estimates and quotations for your customers in Vyapar.">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <!-- Custom Styles -->
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <link href="{{ asset('css/sale.css') }}" rel="stylesheet">

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

  <style>
    .custom-table thead th {
  font-size: 13px;
  color: #6c757d;
  font-weight: 500;
  border-bottom: 1px solid #eee;
}

.custom-table tbody td {
  font-size: 14px;
  padding: 14px 10px;
  border-bottom: 1px solid #f1f1f1;
}

.custom-table tbody tr:hover {
  background-color: #fafafa;
}

.filter-icon {
  font-size: 11px;
  margin-left: 6px;
  color: #adb5bd;
  cursor: pointer;
}

.status-text {
  font-weight: 500;
}

.txn-table tbody tr.sale-cancelled {
  opacity: .72;
}

.txn-table tbody tr.sale-cancelled td {
  text-decoration: line-through;
}

.history-table td,
.history-table th {
  vertical-align: middle;
}

.text-success {
  color: #22c55e !important;
}

.text-warning {
  color: #f59e0b !important;
}

.text-danger {
  color: #ef4444 !important;
}

.action-icon {
  font-size: 14px;
  margin-right: 12px;
  cursor: pointer;
  color: #6c757d;
}

.action-icon:hover {
  color: #000;
}

.custom-table {
  border-collapse: collapse;
}

.custom-table th,
.custom-table td {
  border-right: 1px solid #e9ecef; /* vertical lines */
}

.custom-table th:last-child,
.custom-table td:last-child {
  border-right: none; /* last column pe line nahi */
}

.custom-table th,
.custom-table td {
  border-right: 1px solid #f1f1f1;
}

.custom-table thead th {
  background-color: #fafafa;
}

.add-sale-btn {
  background: linear-gradient(135deg, #ff4d4d, #ff4b4b);
  color: #fff;
  border: none;
  border-radius: 50px;
  padding: 10px 22px;
  font-size: 14px;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(255, 77, 77, 0.3);
  transition: all 0.25s ease;
  display: inline-flex;
  align-items: center;
}

.add-sale-btn i {
  font-size: 13px;
}

.add-sale-btn:hover {
  transform: translateY(-2px) scale(1.03);
  box-shadow: 0 6px 16px rgba(255, 77, 77, 0.45);
  background: linear-gradient(135deg, #ff3b3b, #ff3b3b);
}

.add-sale-btn:active {
  transform: scale(0.97);
  box-shadow: 0 3px 8px rgba(255, 77, 77, 0.3);
}

.agarri-btn {
  background: linear-gradient(135deg, #17365d, #295b93);
  color: #fff;
  border: none;
  border-radius: 50px;
  padding: 10px 20px;
  font-size: 14px;
  font-weight: 600;
  box-shadow: 0 4px 12px rgba(23, 54, 93, 0.28);
  transition: all 0.25s ease;
  display: inline-flex;
  align-items: center;
}

.agarri-btn:hover {
  transform: translateY(-2px) scale(1.02);
  color: #fff;
  box-shadow: 0 6px 16px rgba(23, 54, 93, 0.35);
}

/* common pill */
.filter-pill {
  background-color: #E4F2FF;
  border-radius: 999px;
  display: flex;
  align-items: center;
  height: 38px;
  padding: 0 8px;
}

/* left part */
.filter-left {
  border-right: 1px solid #ccc;
  padding: 0 10px;
}

/* right part */
.filter-right {
  padding: 0 10px;
}

/* select clean */
.filter-select {
  border: none;
  background: transparent;
  outline: none;
  font-size: 13px;
  padding: 0;
  margin: 0;
}

/* small pill (All Firms) */
.small-pill {
  padding: 0 12px;
  min-width: 120px;
}

/* date input */
.date-input {
  border: none;
  background: transparent;
  font-size: 12px;
  width: 110px;
  outline: none;
}

.table-wrapper {
  overflow-x: auto;
  overflow-y: auto;
  margin-bottom: 20px;
  max-height: 68vh;
  border: 1px solid #eef2f7;
  border-radius: 12px;
}

.card-body {
  overflow: hidden;
}

.pagination {
  margin: 0;
}

.d-flex.justify-content-between.align-items-center.mt-3 {
  padding-top: 10px;
  border-top: 1px solid #eee;
}

.table-responsive {
  overflow-x: auto;
  overflow-y: auto !important;
  max-height: 68vh;
}
.pagination-wrapper {
  display: flex;
  justify-content: flex-end;
}

.custom-table {
  min-width: 1500px;
}

.custom-table thead th {
  position: sticky;
  top: 0;
  z-index: 5;
  background-color: #fafafa;
  white-space: nowrap;
}

.custom-table tbody td {
  white-space: nowrap;
}

  </style>
</head>

<body data-page="sale">

  <!-- Navbar & Sidebar injected by components.js -->

  <!-- ═══════════════════════════════════════
     MAIN CONTENT — ESTIMATE / QUOTATION
     ═══════════════════════════════════════ -->
  <main id="mainContent" style="padding: 0px 0px; margin-left:17rem; margin-top: 3.6rem;">
    <div class="container-fluid col-12">
      <div class="d-flex justify-content-between align-items-center bg-white mb-2 p-4">
        <div>
         <div class="dropdown">
            <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
              <span class="h4"> Sales Invoice</span>
            </button>
            <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('sale.index') }}">Sale Invoice</a></li>
            <li><a class="dropdown-item" href="{{ route('sale.estimate') }}">Estimate / Quotation</a></li>
            <li><a class="dropdown-item" href="{{ route('sale-return') }}">Sale Return / Cr. Note</a></li>
            <li><a class="dropdown-item" href="{{ route('payment-in') }}">Payment In</a></li>
            <li><a class="dropdown-item" href="{{ route('payment-out') }}">Payment out</a></li>
            <li><a class="dropdown-item" href="{{ route('purchase-expenses') }}">Purchase Bill</a></li>
            <li><a class="dropdown-item" href="{{ route('purchase-return') }}">Purchase Return / Dr. Note</a></li>
            <li><a class="dropdown-item" href="{{ route('expense') }}">Expenses</a></li>

            </ul>
          </div>
        </div>
       <div class="d-flex align-items-center gap-2">
        <button id="agarriListBtn" class="btn agarri-btn" type="button">
          <i class="fa-solid fa-file-pdf me-2"></i> Agarri List
        </button>
        <button class="btn add-sale-btn" onclick="window.location='{{ route('sale.create') }}'">
          <i class="fa-solid fa-plus me-2"></i> Add Sale
        </button>
      </div>
      </div>
    <div class="d-flex justify-content-between align-items-center bg-white mb-2 px-3 py-2 rounded">

  <div class="d-flex align-items-center gap-2">

    <span class="small fw-semibold">Filter By:</span>

    <!-- Period Filter -->
    <div class="d-flex rounded-pill filter-pill">

      <div class="filter-left">
        <select id="salesPeriodSelect" class="filter-select">
          <option value="all">All Sales Invoices</option>
          <option value="this_month" {{ ($period ?? '') === 'this_month' ? 'selected' : '' }}>This Month</option>
          <option value="last_month" {{ ($period ?? '') === 'last_month' ? 'selected' : '' }}>Last Month</option>
          <option value="this_quarter" {{ ($period ?? '') === 'this_quarter' ? 'selected' : '' }}>This Quarter</option>
          <option value="this_year" {{ ($period ?? '') === 'this_year' ? 'selected' : '' }}>This Year</option>
          <option value="custom" {{ ($period ?? '') === 'custom' ? 'selected' : '' }}>Custom</option>
        </select>
      </div>

      <div class="filter-right">
        <div id="customDateRange" class="d-flex align-items-center gap-1" style="display:none;">
          <input id="salesCustomFrom" type="date" class="date-input" value="{{ $from ?? '' }}" />
          <span>to</span>
          <input id="salesCustomTo" type="date" class="date-input" value="{{ $to ?? '' }}" />
        </div>
      </div>

    </div>

    <!-- Firm Filter -->
    <div class="filter-pill small-pill">
      <select id="salesFirmSelect" class="filter-select text-center">
        <option value="">All Firms</option>
        @foreach(\App\Models\Party::orderBy('name')->get(['name']) as $party)
          @if(!empty($party->name))
            <option value="{{ $party->name }}" {{ ($firm ?? '') === $party->name ? 'selected' : '' }}>{{ $party->name }}</option>
          @endif
        @endforeach
      </select>
    </div>

    <a href="{{ route('sale.index', ['overdue' => $showOverdueOnly ? null : 1]) }}"
       class="btn btn-sm {{ $showOverdueOnly ? 'btn-danger' : 'btn-outline-danger' }}">
      {{ $showOverdueOnly ? 'Showing Overdue Only' : 'Overdue List' }}
    </a>

  </div>

</div>
      <div class="bg-white mb-2 px-4 py-3 rounded">
        @php
          $salesCollection = $sales->getCollection();
          $totalSalesAmount = $salesCollection->sum(fn($sale) => (float) ($sale->grand_total ?? $sale->total_amount ?? 0));
          $convertedAmount = $salesCollection->sum(fn($sale) => (float) ($sale->received_amount ?? 0));
          $openAmount = $salesCollection->sum(fn($sale) => (float) ($sale->balance ?? 0));
        @endphp
        <div class="border rounded p-1" style="width: 25rem; height: 8rem; background-color: #FCF8FF;">
          <div class="w-100 d-flex">
            <div class="w-50 mt-2">
              <p class="ps-3 text-secondary m-0">Total Sales Amount</p>
              <p class="ps-3 h4">Rs {{ number_format($totalSalesAmount, 2) }}</p>
            </div>
            <div class="w-50 mt-2 d-flex align-items-end justify-content-center flex-column">
              <div class="col-5 h-50 rounded-pill d-flex justify-content-center align-item-center me-4"
                style="background-color: #DEF7EE;">
                <p class="text-success pt-1">100% <i class="bi bi-arrow-up-right"></i></p>
              </div>
              <span class="me-4 pe-1 mt-1 text-secondary" style="font-size: 10px;">vs last month</span>
            </div>
          </div>
          <div class="w-100 d-flex mt-3">
            <p class="ps-3 pe-3 text-secondary" style="border-right:1px solid rgb(45, 44, 44);">Converted : <span
                class="fw-bold text-dark">Rs {{ number_format($convertedAmount, 2) }}</span></p>
            <p class="ps-3 text-secondary">Open : <span class="fw-bold text-dark">Rs {{ number_format($openAmount, 2) }}</span></p>

          </div>
        </div>
      </div>

 <div class="card border-0 shadow-sm">
  <div class="card-body p-3">

    <div class="d-flex justify-content-between align-items-center mb-3">
      <h6 class="fw-semibold mb-0">Transactions</h6>
      <div class="d-flex align-items-center gap-2">
        <div class="input-group input-group-sm">
          <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-search"></i></span>
          <input id="searchTransactionsInput" type="text" class="form-control form-control-sm border-start-0" placeholder="Search...">
        </div>
        <button id="exportExcel" class="btn btn-sm btn-outline-secondary" type="button" title="Export to Excel"><i class="fa-solid fa-file-excel"></i></button>
        <button id="printTable" class="btn btn-sm btn-outline-secondary" type="button" title="Print"><i class="fa-solid fa-print"></i></button>
        <button id="signalBtn" class="btn btn-sm btn-outline-secondary" type="button" title="Signal"><i class="fa-solid fa-signal"></i></button>
      </div>
    </div>

    <div class="table-responsive table-wrapper">
      <table class="table align-middle custom-table mb-0 txn-table">
        <thead>
          <tr class="{{ strtolower((string) ($sale->status ?? '')) === 'cancelled' ? 'sale-cancelled' : '' }}">
            <th>
              <div class="column-filter-header">
                <span>Date</span>
                <button class="filter-icon-btn" type="button"><i class="fa-solid fa-filter"></i></button>
              </div>
              <div class="column-filter-dropdown">
                <input type="text" class="form-control form-control-sm column-filter-input" placeholder="Filter Date">
                <div class="d-flex justify-content-end gap-2 mt-2">
                  <button class="btn btn-sm btn-outline-secondary column-filter-clear" data-column-index="0">Clear</button>
                  <button class="btn btn-sm btn-primary column-filter-apply" data-column-index="0">Apply</button>
                </div>
              </div>
            </th>
            <th>
              <div class="column-filter-header">
                <span>Invoice no</span>
                <button class="filter-icon-btn" type="button"><i class="fa-solid fa-filter"></i></button>
              </div>
              <div class="column-filter-dropdown">
                <input type="text" class="form-control form-control-sm column-filter-input" placeholder="Filter Invoice">
                <div class="d-flex justify-content-end gap-2 mt-2">
                  <button class="btn btn-sm btn-outline-secondary column-filter-clear" data-column-index="1">Clear</button>
                  <button class="btn btn-sm btn-primary column-filter-apply" data-column-index="1">Apply</button>
                </div>
              </div>
            </th>
            <th>
              <div class="column-filter-header">
                <span>Party Name</span>
                <button class="filter-icon-btn" type="button"><i class="fa-solid fa-filter"></i></button>
              </div>
              <div class="column-filter-dropdown">
                <input type="text" class="form-control form-control-sm column-filter-input" placeholder="Filter Party">
                <div class="d-flex justify-content-end gap-2 mt-2">
                  <button class="btn btn-sm btn-outline-secondary column-filter-clear" data-column-index="2">Clear</button>
                  <button class="btn btn-sm btn-primary column-filter-apply" data-column-index="2">Apply</button>
                </div>
              </div>
            </th>
            <th>
              <div class="column-filter-header">
                <span>Transaction</span>
                <button class="filter-icon-btn" type="button"><i class="fa-solid fa-filter"></i></button>
              </div>
              <div class="column-filter-dropdown">
                <input type="text" class="form-control form-control-sm column-filter-input" placeholder="Filter Transaction">
                <div class="d-flex justify-content-end gap-2 mt-2">
                  <button class="btn btn-sm btn-outline-secondary column-filter-clear" data-column-index="3">Clear</button>
                  <button class="btn btn-sm btn-primary column-filter-apply" data-column-index="3">Apply</button>
                </div>
              </div>
            </th>
            <th>
              <div class="column-filter-header">
                <span>Payment Type</span>
                <button class="filter-icon-btn" type="button"><i class="fa-solid fa-filter"></i></button>
              </div>
              <div class="column-filter-dropdown">
                <input type="text" class="form-control form-control-sm column-filter-input" placeholder="Filter Payment">
                <div class="d-flex justify-content-end gap-2 mt-2">
                  <button class="btn btn-sm btn-outline-secondary column-filter-clear" data-column-index="4">Clear</button>
                  <button class="btn btn-sm btn-primary column-filter-apply" data-column-index="4">Apply</button>
                </div>
              </div>
            </th>
            <th>
              <div class="column-filter-header">
                <span>Amount</span>
                <button class="filter-icon-btn" type="button"><i class="fa-solid fa-filter"></i></button>
              </div>
              <div class="column-filter-dropdown">
                <input type="text" class="form-control form-control-sm column-filter-input" placeholder="Filter Amount">
                <div class="d-flex justify-content-end gap-2 mt-2">
                  <button class="btn btn-sm btn-outline-secondary column-filter-clear" data-column-index="5">Clear</button>
                  <button class="btn btn-sm btn-primary column-filter-apply" data-column-index="5">Apply</button>
                </div>
              </div>
            </th>
            <th>
              <div class="column-filter-header">
                <span>Received Amount</span>
                <button class="filter-icon-btn" type="button"><i class="fa-solid fa-filter"></i></button>
              </div>
              <div class="column-filter-dropdown">
                <input type="text" class="form-control form-control-sm column-filter-input" placeholder="Filter Received">
                <div class="d-flex justify-content-end gap-2 mt-2">
                  <button class="btn btn-sm btn-outline-secondary column-filter-clear" data-column-index="6">Clear</button>
                  <button class="btn btn-sm btn-primary column-filter-apply" data-column-index="6">Apply</button>
                </div>
              </div>
            </th>
            <th>
              <div class="column-filter-header">
                <span>Balance</span>
                <button class="filter-icon-btn" type="button"><i class="fa-solid fa-filter"></i></button>
              </div>
              <div class="column-filter-dropdown">
                <input type="text" class="form-control form-control-sm column-filter-input" placeholder="Filter Balance">
                <div class="d-flex justify-content-end gap-2 mt-2">
                  <button class="btn btn-sm btn-outline-secondary column-filter-clear" data-column-index="7">Clear</button>
                  <button class="btn btn-sm btn-primary column-filter-apply" data-column-index="7">Apply</button>
                </div>
              </div>
            </th>
            <th>
              <div class="column-filter-header">
                <span>Due Date</span>
                <button class="filter-icon-btn" type="button"><i class="fa-solid fa-filter"></i></button>
              </div>
              <div class="column-filter-dropdown">
                <input type="text" class="form-control form-control-sm column-filter-input" placeholder="Filter Due Date">
                <div class="d-flex justify-content-end gap-2 mt-2">
                  <button class="btn btn-sm btn-outline-secondary column-filter-clear" data-column-index="8">Clear</button>
                  <button class="btn btn-sm btn-primary column-filter-apply" data-column-index="8">Apply</button>
                </div>
              </div>
            </th>
            <th>
              <div class="column-filter-header">
                <span>Status</span>
                <button class="filter-icon-btn" type="button"><i class="fa-solid fa-filter"></i></button>
              </div>
              <div class="column-filter-dropdown">
                <input type="text" class="form-control form-control-sm column-filter-input" placeholder="Filter Status">
                <div class="d-flex justify-content-end gap-2 mt-2">
                  <button class="btn btn-sm btn-outline-secondary column-filter-clear" data-column-index="9">Clear</button>
                  <button class="btn btn-sm btn-primary column-filter-apply" data-column-index="9">Apply</button>
                </div>
              </div>
            </th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          @forelse($sales as $sale)
          <tr>
            <td>{{ \Carbon\Carbon::parse($sale->invoice_date ?? $sale->created_at)->format('d/m/Y') }}</td>
            <td>{{ $sale->bill_number ?? $sale->id }}</td>
            <td>{{ $sale->party?->name ?? 'No Party Selected' }}</td>
            <td>Sale</td>

            <td>
              {{ $sale->payments->pluck('payment_type')->filter()->unique()->join(', ') ?: '-' }}
            </td>

            <td>Rs {{ number_format($sale->total_amount ?? 0) }}</td>
            <td>Rs {{ number_format($sale->received_amount ?? 0) }}</td>
            <td>Rs {{ number_format($sale->balance ?? 0) }}</td>
            <td>
              @if($sale->due_date)
                @php $isOverdue = ($sale->balance ?? 0) > 0 && \Carbon\Carbon::parse($sale->due_date)->isPast(); @endphp
                <span class="{{ $isOverdue ? 'text-danger fw-semibold' : 'text-muted' }}">
                  {{ \Carbon\Carbon::parse($sale->due_date)->format('d/m/Y') }}
                </span>
              @else
                -
              @endif
            </td>

            <td>
              @php
                $status = strtolower($sale->status ?? 'unpaid');
              @endphp

              <span class="status-text
                {{ $status == 'paid' ? 'text-success' : '' }}
                {{ $status == 'partial' ? 'text-warning' : '' }}
                {{ $status == 'unpaid' ? 'text-danger' : '' }}">

                {{ ucfirst($status) }}
              </span>
            </td>

            <td class="text-muted">
              <div class="d-flex align-items-center gap-2">
                <i class="fa-solid fa-print row-action-print" title="Print" style="cursor:pointer;"></i>
                <i class="fa-solid fa-share row-action-share" title="Share" style="cursor:pointer;"></i>
                <div class="dropdown sale-action-menu"
                     data-sale-id="{{ $sale->id }}"
                     data-party-name="{{ $sale->party?->name ?? 'No Party Selected' }}"
                     data-balance="{{ (float) ($sale->balance ?? 0) }}"
                     data-edit-url="{{ route('sale.edit', $sale) }}"
                     data-preview-url="{{ route('invoice', ['sale_id' => $sale->id]) }}"
                     data-pdf-url="{{ route('invoice', ['sale_id' => $sale->id]) }}"
                     data-print-url="{{ route('invoice', ['sale_id' => $sale->id]) }}"
                     data-delivery-preview-url="{{ route('invoice', ['sale_id' => $sale->id, 'doc' => 'delivery_challan']) }}"
                     data-payment-history-url="{{ route('sale.payment-history', $sale) }}"
                     data-bank-history-url="{{ route('sale.bank-history', $sale) }}"
                     data-convert-return-url="{{ route('sale-return.create', ['sale_id' => $sale->id]) }}"
                     data-cancel-url="{{ route('sale.cancel', $sale) }}"
                     data-is-cancelled="{{ strtolower((string) ($sale->status ?? '')) === 'cancelled' ? '1' : '0' }}"
                     data-sale-number="{{ $sale->bill_number ?? $sale->id }}">
                  <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="#" data-action="view">View / Edit</a></li>
                    <li><a class="dropdown-item" href="#" data-action="convert-return">Convert to Return</a></li>
                    <li><a class="dropdown-item" href="#" data-action="preview-delivery">Preview Delivery Challan</a></li>
                    <li><a class="dropdown-item" href="#" data-action="payment-history">Payment History</a></li>
                    <li><a class="dropdown-item" href="#" data-action="cancel">Cancel Invoice</a></li>
                    <li><a class="dropdown-item" href="#" data-action="delete">Delete</a></li>
                    <li><a class="dropdown-item" href="#" data-action="duplicate">Duplicate</a></li>
                    <li><a class="dropdown-item" href="#" data-action="pdf">Open PDF</a></li>
                    <li><a class="dropdown-item" href="#" data-action="print">Print</a></li>
                    <li><a class="dropdown-item" href="#" data-action="history">View History</a></li>
                  </ul>
                </div>
              </div>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="11" class="text-center text-muted py-4">
              No sales yet.
            </td>
          </tr>
          @endforelse
        </tbody>

      </table>
    </div>

    <div class="d-flex justify-content-between align-items-center mt-3">
      <div class="text-muted small">
        Showing {{ $sales->firstItem() ?: 0 }} to {{ $sales->lastItem() ?: 0 }} of {{ $sales->total() }} results
      </div>
      <div>
        {{ $sales->withQueryString()->links() }}
      </div>
    </div>
  </div>
</div>
    </div>
  </main>

  <!-- ═══════════════════════════════════════════
     SCRIPTS
     ═══════════════════════════════════════════ -->
  <div class="modal fade" id="salePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="salePreviewModalTitle">Preview</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0" style="min-height:70vh;">
          <iframe id="salePreviewFrame" title="Preview" style="width:100%; min-height:70vh; border:0;"></iframe>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="saleHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="saleHistoryModalTitle">History</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body" id="saleHistoryModalBody">
          <div class="text-muted">Loading...</div>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/components.js') }}"></script>
  <script src="{{ asset('js/common.js') }}"></script>
  <script src="{{ asset('js/sale.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const agarriButton = document.getElementById('agarriListBtn');
      const periodSelect = document.getElementById('salesPeriodSelect');
      const firmSelect = document.getElementById('salesFirmSelect');
      const customFrom = document.getElementById('salesCustomFrom');
      const customTo = document.getElementById('salesCustomTo');

      function buildDateRangeFromPeriod(period) {
        const now = new Date();
        let start = null;
        let end = null;

        if (period === 'this_month') {
          start = new Date(now.getFullYear(), now.getMonth(), 1);
          end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        } else if (period === 'last_month') {
          start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
          end = new Date(now.getFullYear(), now.getMonth(), 0);
        } else if (period === 'this_quarter') {
          const quarterStartMonth = Math.floor(now.getMonth() / 3) * 3;
          start = new Date(now.getFullYear(), quarterStartMonth, 1);
          end = new Date(now.getFullYear(), quarterStartMonth + 3, 0);
        } else if (period === 'this_year') {
          start = new Date(now.getFullYear(), 0, 1);
          end = new Date(now.getFullYear(), 11, 31);
        }

        return { start, end };
      }

      function toIsoDate(date) {
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
      }

      agarriButton?.addEventListener('click', function () {
        const params = new URLSearchParams();
        const selectedPeriod = periodSelect?.value || 'all';
        const selectedFirm = (firmSelect?.value || '').trim();

        if (selectedPeriod === 'custom') {
          if (customFrom?.value) params.set('from', customFrom.value);
          if (customTo?.value) params.set('to', customTo.value);
        } else {
          const range = buildDateRangeFromPeriod(selectedPeriod);
          if (range.start && range.end) {
            params.set('from', toIsoDate(range.start));
            params.set('to', toIsoDate(range.end));
          }
        }

        if (selectedFirm) {
          params.set('party_name', selectedFirm);
        }

        window.open(`{{ route('reports.unreceived-invoices.pdf') }}?${params.toString()}`, '_blank');
      });
    });
  </script>




</body>

</html>
