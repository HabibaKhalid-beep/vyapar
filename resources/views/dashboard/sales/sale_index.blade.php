@extends('layouts.app')

@section('title', 'Vyapar — Sales')
@section('description', 'Manage and view sale invoices with filters, totals and transaction history.')
@section('page', 'sales')

@section('content')

  @push('styles')
    <link href="{{ asset('css/sale.css') }}" rel="stylesheet">
  @endpush

  <div class="card vyapar-card mb-3">
    <div class="card-body p-3">
      <div class="sale-header">
        <div id="businessNameBadge" class="business-badge" title="Click to set business name">
          <span class="badge-dot"></span>
          <span id="businessNameText" class="badge-text">Enter Business Name</span>
        </div>

        <div class="business-actions">
          <button class="btn btn-danger btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Add Sale
          </button>
          <button class="btn btn-primary btn-sm">
            <i class="fa-solid fa-plus me-1"></i> Add Purchase
          </button>
          <button class="btn btn-outline-secondary btn-sm" title="More actions">
            <i class="fa-solid fa-ellipsis"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="card vyapar-card mb-3">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
          <strong class="me-2">Sale Invoices</strong>
          <i class="fa-solid fa-chevron-down"></i>
        </div>

        <div class="d-flex flex-wrap gap-2 align-items-center">
          <div class="d-flex align-items-center gap-2">
            <span class="text-muted">Filter by:</span>
            <select class="form-select form-select-sm" style="width: auto;">
              <option>Custom</option>
              <option>Today</option>
              <option>This Week</option>
            </select>
          </div>

          <div class="d-flex align-items-center gap-2">
            <input type="date" class="form-control form-control-sm" style="width: 170px;" value="2026-03-01">
            <span class="text-muted">to</span>
            <input type="date" class="form-control form-control-sm" style="width: 170px;" value="2026-03-31">
          </div>

          <div class="d-flex align-items-center gap-2">
            <select class="form-select form-select-sm" style="width: auto;">
              <option>All Firms</option>
              <option>Firm A</option>
              <option>Firm B</option>
            </select>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="card vyapar-card mb-3">
    <div class="card-body">
      <div class="d-flex flex-wrap align-items-center justify-content-between">
        <div>
          <div id="saleBusinessMeta" class="sale-card-meta"></div>
          <div class="text-muted" style="font-size:12px;">Total Sales Amount</div>
          <div class="fw-bold" style="font-size:20px;">Rs 2,500</div>
          <div class="text-muted" style="font-size:12px;">
            Received: <span class="text-dark">Rs 490</span> &nbsp;|&nbsp;
            Balance: <span class="text-dark">Rs 2,010</span>
          </div>
        </div>

        <div class="d-flex gap-2">
          <button class="btn btn-sm btn-outline-secondary" title="Export">
            <i class="fa-solid fa-file-excel"></i>
          </button>
          <button class="btn btn-sm btn-outline-secondary" title="Print">
            <i class="fa-solid fa-print"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  <div class="card vyapar-card">
    <div class="card-body p-0">
      <table class="txn-table w-100 mb-0">
        <thead>
          <tr>
            <th>Date</th>
            <th>Invoice no</th>
            <th>Party Name</th>
            <th>Transaction</th>
            <th>Payment Type</th>
            <th>Amount</th>
            <th>Balance</th>
            <th>Actions</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>11/03/2026</td>
            <td>3</td>
            <td>dodh patya</td>
            <td>Sale</td>
            <td>Cash</td>
            <td>Rs 1,000</td>
            <td>Rs 1,000</td>
            <td>
              <button class="btn btn-link btn-sm p-0" title="Print"><i class="fa-solid fa-print"></i></button>
              <button class="btn btn-link btn-sm p-0" title="Share"><i class="fa-solid fa-share-nodes"></i></button>
            </td>
            <td><span class="text-danger">Unpaid</span></td>
          </tr>
          <tr>
            <td>10/03/2026</td>
            <td>2</td>
            <td>dodh patya</td>
            <td>Sale</td>
            <td>Cash</td>
            <td>Rs 1,000</td>
            <td>Rs 1,000</td>
            <td>
              <button class="btn btn-link btn-sm p-0" title="Print"><i class="fa-solid fa-print"></i></button>
              <button class="btn btn-link btn-sm p-0" title="Share"><i class="fa-solid fa-share-nodes"></i></button>
            </td>
            <td><span class="text-danger">Unpaid</span></td>
          </tr>
          <tr>
            <td>10/03/2026</td>
            <td>1</td>
            <td>dodh patya</td>
            <td>Sale</td>
            <td>Cash</td>
            <td>Rs 500</td>
            <td>Rs 10</td>
            <td>
              <button class="btn btn-link btn-sm p-0" title="Print"><i class="fa-solid fa-print"></i></button>
              <button class="btn btn-link btn-sm p-0" title="Share"><i class="fa-solid fa-share-nodes"></i></button>
            </td>
            <td><span class="text-warning">Partial</span></td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

@endsection

@push('scripts')
  <script src="{{ asset('js/dashboard.js') }}"></script>
  <script src="{{ asset('js/sale.js') }}"></script>
@endpush
