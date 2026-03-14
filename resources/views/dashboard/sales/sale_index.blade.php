@extends('layouts.app')

@section('title', 'Vyapar — Sales')
@section('description', 'Manage and view sale invoices with filters, totals and transaction history.')
@section('page', 'sales')

@section('content')

  @push('styles')
    <link href="{{ asset('css/sale.css') }}" rel="stylesheet">
  @endpush



  <div class="card vyapar-card sale-top-card sale-card">
    <div class="card-body p-3">
      <div class="sale-topbar">
        <div class="sale-search">
          <span class="sale-search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
          <input id="searchTransactionsInput" type="text" class="form-control form-control-sm" placeholder="Search Transactions" />
        </div>

        <div class="sale-actions">
         <button class="btn btn-outline-danger btn-sm" onclick="window.location='{{ route('sale.create') }}'">
    <i class="fa-solid fa-plus me-1"></i> Add Sale
</button>
          <button class="btn btn-outline-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Add Purchase
          </button>
          <button class="btn btn-outline-primary btn-sm" title="Add more">
            <i class="fa-solid fa-plus"></i>
          </button>

          <div class="sale-dropdown">
            <button class="btn btn-sm sale-dropdown-toggle" type="button" title="More options">
              <i class="fa-solid fa-ellipsis-vertical"></i>
            </button>
            <div class="sale-dropdown-menu" style="margin-right:20px;">
              <button type="button" data-action="notifications">
                <i class="fa-solid fa-bell me-2"></i> Notifications
              </button>
              <button type="button" data-action="settings">
                <i class="fa-solid fa-gear me-2"></i> Settings
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
<div class="card vyapar-card sale-card sale-invoices-card">
    <div class="card-body d-flex justify-content-between align-items-center p-3">
      <div class="d-flex align-items-center gap-2">
        <h4 class="mb-0 sale-invoices-title">Sale Invoices</h4>
        <div class="sale-dropdown">
          <button class="sale-dropdown-toggle" type="button" title="View options"><i class="fa-solid fa-chevron-down"></i></button>
          <div class="sale-dropdown-menu">
            <div class="sale-dropdown-header">Invoices (12)</div>
            <button type="button" class="sale-dropdown-item" data-action="all">All invoices</button>
            <button type="button" class="sale-dropdown-item" data-action="paid">Paid invoices</button>
            <button type="button" class="sale-dropdown-item" data-action="unpaid">Unpaid invoices</button>
          </div>
        </div>
      </div>

      <div class="d-flex align-items-center gap-2">

         <div class="sale-actions">
         <button class="btn btn-danger btn-sm" onclick="window.location='{{ route('sale.create') }}'">
    <i class="fa-solid fa-plus me-1"></i> Add Sale
</button>
        </div>
        <button class="btn btn btn-sm"><i class="fa-solid fa-gear"></i></button>
      </div>
    </div>
  </div>
{{-- FILTER CARD --}}
<div class="card vyapar-card sale-card">
<div class="card-body p-3">

<div class="sale-filter-row">
<div class="sale-filter-left">

<span class="sale-filter-label">Filter by :</span>

<div class="sale-pill">
Custom <i class="fa-solid fa-chevron-down"></i>
</div>

<div class="sale-pill">
<i class="fa-regular fa-calendar"></i>
01/03/2026 To 31/03/2026
</div>

<div class="sale-pill">
All Firms <i class="fa-solid fa-chevron-down"></i>
</div>

</div>
</div>

</div>
</div>


{{-- SUMMARY CARD --}}
<div class="card vyapar-card sale-card">
<div class="card-body p-3">

<div class="sale-mini-card">

<div class="sale-summary-label">
Total Sales Amount
</div>

<div class="sale-summary-value">
Rs 2,500
</div>

<div class="sale-summary-sub">
Received: <strong>Rs 490</strong> |
Balance: <strong>Rs 2,010</strong>
</div>

</div>

</div>
</div>


{{-- TRANSACTION CARD --}}
<div class="card vyapar-card sale-card">
<div class="card-body p-0">

<div class="sale-table-header d-flex justify-content-between align-items-center p-3 border-bottom">

<h6 class="mb-0">Transactions</h6>

<div class="d-flex gap-3">
<i class="fa-solid fa-magnifying-glass"></i>
<i class="fa-solid fa-chart-simple"></i>
<i class="fa-solid fa-file-excel"></i>
<i class="fa-solid fa-print"></i>
</div>

</div>

<div class="table-responsive">

<table class="txn-table w-100">

<thead>
<tr>
<th>
  <div class="column-filter-header">
    <span>Date</span>
    <button class="filter-icon-btn" data-column="date"><i class="fa-solid fa-filter"></i></button>
    <div class="column-filter-dropdown" data-column="date">
      <div class="filter-dropdown-title">Filter Date</div>
      <div class="filter-option"><input type="date" class="form-control form-control-sm" /></div>
      <div class="filter-option"><input type="date" class="form-control form-control-sm" /></div>
      <button class="btn btn-sm btn-primary mt-2">Apply</button>
    </div>
  </div>
</th>
<th>
  <div class="column-filter-header">
    <span>Invoice no</span>
    <button class="filter-icon-btn" data-column="invoice"><i class="fa-solid fa-filter"></i></button>
    <div class="column-filter-dropdown" data-column="invoice">
      <div class="filter-dropdown-title">Filter Invoice</div>
      <div class="filter-option"><input type="number" class="form-control form-control-sm" placeholder="From" /></div>
      <div class="filter-option"><input type="number" class="form-control form-control-sm" placeholder="To" /></div>
      <button class="btn btn-sm btn-primary mt-2">Apply</button>
    </div>
  </div>
</th>
<th>
  <div class="column-filter-header">
    <span>Party Name</span>
    <button class="filter-icon-btn" data-column="party"><i class="fa-solid fa-filter"></i></button>
    <div class="column-filter-dropdown" data-column="party">
      <div class="filter-dropdown-title">Filter Party</div>
      <div class="filter-option"><input type="text" class="form-control form-control-sm" placeholder="Party" /></div>
      <button class="btn btn-sm btn-primary mt-2">Apply</button>
    </div>
  </div>
</th>
<th>
  <div class="column-filter-header">
    <span>Transaction</span>
    <button class="filter-icon-btn" data-column="transaction"><i class="fa-solid fa-filter"></i></button>
    <div class="column-filter-dropdown" data-column="transaction">
      <div class="filter-dropdown-title">Filter Transaction</div>
      <div class="filter-option"><select class="form-select form-select-sm"><option>All</option><option>Sale</option><option>Purchase</option></select></div>
      <button class="btn btn-sm btn-primary mt-2">Apply</button>
    </div>
  </div>
</th>
<th>
  <div class="column-filter-header">
    <span>Payment Type</span>
    <button class="filter-icon-btn" data-column="payment"><i class="fa-solid fa-filter"></i></button>
    <div class="column-filter-dropdown" data-column="payment">
      <div class="filter-dropdown-title">Filter Payment</div>
      <div class="filter-option"><select class="form-select form-select-sm"><option>All</option><option>Cash</option><option>Credit</option></select></div>
      <button class="btn btn-sm btn-primary mt-2">Apply</button>
    </div>
  </div>
</th>
<th>
  <div class="column-filter-header">
    <span>Amount</span>
    <button class="filter-icon-btn" data-column="amount"><i class="fa-solid fa-filter"></i></button>
    <div class="column-filter-dropdown" data-column="amount">
      <div class="filter-dropdown-title">Filter Amount</div>
      <div class="filter-option"><input type="number" class="form-control form-control-sm" placeholder="Min" /></div>
      <div class="filter-option"><input type="number" class="form-control form-control-sm" placeholder="Max" /></div>
      <button class="btn btn-sm btn-primary mt-2">Apply</button>
    </div>
  </div>
</th>
<th>
  <div class="column-filter-header">
    <span>Balance</span>
    <button class="filter-icon-btn" data-column="balance"><i class="fa-solid fa-filter"></i></button>
    <div class="column-filter-dropdown" data-column="balance">
      <div class="filter-dropdown-title">Filter Balance</div>
      <div class="filter-option"><input type="number" class="form-control form-control-sm" placeholder="Min" /></div>
      <div class="filter-option"><input type="number" class="form-control form-control-sm" placeholder="Max" /></div>
      <button class="btn btn-sm btn-primary mt-2">Apply</button>
    </div>
  </div>
</th>
<th>
  <div class="column-filter-header">
    <span>Status</span>
    <button class="filter-icon-btn" data-column="status"><i class="fa-solid fa-filter"></i></button>
    <div class="column-filter-dropdown" data-column="status">
      <div class="filter-dropdown-title">Filter Status</div>
      <div class="filter-option"><select class="form-select form-select-sm"><option>All</option><option>Unpaid</option><option>Partial</option><option>Paid</option></select></div>
      <button class="btn btn-sm btn-primary mt-2">Apply</button>
    </div>
  </div>
</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

<tr>
<td>01/03/2026</td>
<td>#INV-1001</td>
<td>Ali Traders</td>
<td><span class="badge bg-success">Sale</span></td>
<td>Cash</td>
<td>Rs 1,200</td>
<td>Rs 0</td>
<td><span class="badge bg-success">Paid</span></td>
<td>
<button class="btn btn-sm btn-light"><i class="fa-solid fa-eye"></i></button>
<button class="btn btn-sm btn-light"><i class="fa-solid fa-pen"></i></button>
</td>
</tr>

<tr>
<td>03/03/2026</td>
<td>#INV-1002</td>
<td>Hassan Store</td>
<td><span class="badge bg-success">Sale</span></td>
<td>Credit</td>
<td>Rs 800</td>
<td>Rs 800</td>
<td><span class="badge bg-danger">Unpaid</span></td>
<td>
<button class="btn btn-sm btn-light"><i class="fa-solid fa-eye"></i></button>
<button class="btn btn-sm btn-light"><i class="fa-solid fa-pen"></i></button>
</td>
</tr>

<tr>
<td>05/03/2026</td>
<td>#INV-1003</td>
<td>Usman Mart</td>
<td><span class="badge bg-success">Sale</span></td>
<td>Cash</td>
<td>Rs 500</td>
<td>Rs 100</td>
<td><span class="badge bg-warning text-dark">Partial</span></td>
<td>
<button class="btn btn-sm btn-light"><i class="fa-solid fa-eye"></i></button>
<button class="btn btn-sm btn-light"><i class="fa-solid fa-pen"></i></button>
</td>
</tr>

<tr>
<td>07/03/2026</td>
<td>#INV-1004</td>
<td>Bilal Electronics</td>
<td><span class="badge bg-success">Sale</span></td>
<td>Cash</td>
<td>Rs 2,000</td>
<td>Rs 0</td>
<td><span class="badge bg-success">Paid</span></td>
<td>
<button class="btn btn-sm btn-light"><i class="fa-solid fa-eye"></i></button>
<button class="btn btn-sm btn-light"><i class="fa-solid fa-pen"></i></button>
</td>
</tr>

</tbody>

</table>

</div>

</div>
</div>


@endsection

@push('scripts')
  <script src="{{ asset('js/dashboard.js') }}"></script>
  <script src="{{ asset('js/sale.js') }}"></script>
@endpush
