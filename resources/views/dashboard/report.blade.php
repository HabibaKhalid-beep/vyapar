<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vyapar — Reports</title>

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <!-- Custom Styles -->
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

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
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');

    .reports-container {
      font-family: 'Inter', sans-serif;
      height: calc(100vh - 60px);
      /* Adjust based on topbar height to fit screen */
    }

    .reports-nav .nav-link {
      font-size: 14px;
      transition: all 0.2s ease-in-out;
      color: #495057;
    }

    .reports-nav .nav-link:hover {
      background-color: #f1f3f5;
      color: #212529;
    }

    .reports-nav .nav-link.active {
      background-color: #e2e8f0;
      font-weight: 500;
      color: #111827 !important;
      border-right: 4px solid #6366f1;
    }

    .cursor-pointer {
      cursor: pointer;
    }

    /* Minor overrides for tables and custom badges equivalent to Tailwind */
    .bg-success-subtle-custom {
      background-color: #d1fae5;
    }

    .text-success-custom {
      color: #047857;
    }

    .border-indigo-100 {
      border: 1px solid #e0e7ff;
    }

    .table-custom-header th {
      font-size: 12px;
      font-weight: 600;
      color: #6b7280;
      text-transform: uppercase;
      padding-top: 1rem;
      padding-bottom: 1rem;
      background-color: #f9fafb;
      border-bottom: 1px solid #e5e7eb;
    }

    .table-custom-body td {
      font-size: 14px;
      color: #374151;
      padding-top: 1rem;
      padding-bottom: 1rem;
      vertical-align: middle;
    }

    .reports-filter-box {
      background-color: white;
      border: 1px solid #e5e7eb;
      transition: background-color 0.2s;
    }

    .reports-filter-box:hover {
      background-color: #f9fafb;
    }

    /* PnL Custom Styles */
    .pnl-container::-webkit-scrollbar {
      width: 6px;
    }

    .pnl-container::-webkit-scrollbar-track {
      background: transparent;
    }

    .pnl-container::-webkit-scrollbar-thumb {
      background-color: #cbd5e1;
      border-radius: 10px;
    }

    .pnl-row-hover:hover {
      background-color: #f8fafc !important;
    }

    .pnl-chevron {
      width: 24px;
      text-align: center;
      cursor: pointer;
      color: #3b82f6;
      display: inline-block;
      transition: transform 0.2s;
    }

    .pnl-bullet {
      width: 24px;
      text-align: center;
      color: #9ca3af;
      display: inline-block;
      font-size: 20px;
      line-height: 14px;
    }

    /* Party Statement Styles */
    .ps-radio {
      appearance: none;
      width: 18px;
      height: 18px;
      border: 2px solid #ccc;
      border-radius: 50%;
      margin: 0;
      display: grid;
      place-content: center;
      cursor: pointer;
    }

    .ps-radio::before {
      content: "";
      width: 10px;
      height: 10px;
      border-radius: 50%;
      transform: scale(0);
      background-color: #4b5563;
      transition: transform 120ms ease-in-out;
    }

    .ps-radio:checked {
      border: 2px solid #4b5563;
    }

    .ps-radio:checked::before {
      transform: scale(1);
    }
  </style>
</head>

<body data-page="reports">

  <!-- Navbar & Sidebar injected by components.js -->

  <main class="main-content" id="mainContent" style="padding: 0; overflow: hidden;">

    <div class="d-flex w-100 reports-container" style="background-color: #f3f4f6;">
      <!-- Internal Reports Sidebar -->
      <aside class="reports-sidebar border-end flex-shrink-0"
        style="width: 200px; min-width: 200px; background-color: #f9fafb; overflow-y: auto;">
        <div class="pt-4">
          <h6 class="text-secondary text-uppercase fw-bold px-4 mb-3" style="font-size: 11px; letter-spacing: 0.5px;">
            Transaction report</h6>
          <ul class="nav flex-column mb-4 reports-nav">
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4 active" data-target="Sale"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i> Sale</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="Purchase"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i> Purchase</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="Daybook"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i> Day book</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="Alltransactions"><i
                  class="fa-regular fa-file-lines me-2 text-secondary "></i>All Transactions</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="ProfitAndLoss"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i> Profit and Loss</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="cashFlow"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Cash Flow</a></li>
            <!-- <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="Bill Wise Profit"><i
                  class="fa-solid fa-crown me-2 text-primary"></i> Bill Wise Profit</a></li> -->

          </ul>

          <h6 class="text-secondary text-uppercase fw-bold px-4 mb-3 mt-4"
            style="font-size: 11px; letter-spacing: 0.5px;">Party report</h6>
          <ul class="nav flex-column mb-4 reports-nav">

            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="Party Statement"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i> Party Statement</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="All Parties"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i> All Parties</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="Party Report by Items"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i> Party Report by Items</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="Partysalepurchase"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Sale Purchase by Party</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="Partysalepurchasegroup"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Sale Purchase by Party Group</a></li>
            <!-- <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="Trial Balance Report"><i
                  class="fa-solid fa-crown me-2 text-primary"></i> Trial Balance Report</a></li> -->
            <!-- <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="Balance Sheet"><i
                  class="fa-solid fa-crown me-2 text-primary"></i> Balance Sheet</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="Party wise Profit & Loss"><i
                  class="fa-solid fa-crown me-2 text-primary"></i> Party wise Profit & Loss</a></li> -->
          </ul>
          <h6 class="text-secondary text-uppercase fw-bold px-4 mb-3 mt-4"
            style="font-size: 11px; letter-spacing: 0.5px;">Item/Stock Report</h6>
          <ul class="nav flex-column mb-4 reports-nav">

            <li class="nav-item">
  <a href="#" class="nav-link py-2 px-4 report-nav-link" 
     data-tab="stock-summary"
     onclick="showTab('stock-summary'); return false;">
    <i class="fa-regular fa-file-lines me-2 text-secondary"></i>Stock Summary
  </a>
</li>
<li class="nav-item">
  <a href="#" class="nav-link py-2 px-4 report-nav-link" 
     data-tab="party-report-summary"
     onclick="showTab('party-report-summary'); return false;">
    <i class="fa-regular fa-file-lines me-2 text-secondary"></i>Party report by items
  </a>
</li>
<li class="nav-item">
  <a href="#" class="nav-link py-2 px-4 report-nav-link" 
     data-tab="item-wise-profit-and-loss"
     onclick="showTab('item-wise-profit-and-loss'); return false;">
    <i class="fa-regular fa-file-lines me-2 text-secondary"></i>Item wise profit and loss
  </a>
</li>
<li class="nav-item">
  <a href="#" class="nav-link py-2 px-4 report-nav-link" 
     data-tab="item-category-wise-profit-and-loss"
     onclick="showTab('item-category-wise-profit-and-loss'); return false;">
    <i class="fa-regular fa-file-lines me-2 text-secondary"></i>Item Category wise profit and loss
  </a>
</li>
<li class="nav-item">
  <a href="#" class="nav-link py-2 px-4 report-nav-link" 
     data-tab="low-stock-summary"
     onclick="showTab('low-stock-summary'); return false;">
    <i class="fa-regular fa-file-lines me-2 text-secondary"></i>Low stock summary
  </a>
</li>
<li class="nav-item">
  <a href="#" class="nav-link py-2 px-4 report-nav-link" 
     data-tab="stock-details"
     onclick="showTab('stock-details'); return false;">
    <i class="fa-regular fa-file-lines me-2 text-secondary"></i>Stock Details
  </a>
</li>
<li class="nav-item">
  <a href="#" class="nav-link py-2 px-4 report-nav-link" 
     data-tab="item-details"
     onclick="showTab('item-details'); return false;">
    <i class="fa-regular fa-file-lines me-2 text-secondary"></i>Item Details
  </a>
</li>
<li class="nav-item">
  <a href="#" class="nav-link py-2 px-4 report-nav-link" 
     data-tab="sale-purchase-report-by-item-category"
     onclick="showTab('sale-purchase-report-by-item-category'); return false;">
    <i class="fa-regular fa-file-lines me-2 text-secondary"></i>Sale/Purchase report by item category
  </a>
</li>
<li class="nav-item">
  <a href="#" class="nav-link py-2 px-4 report-nav-link" 
     data-tab="stock-summary-report-by-item-category"
     onclick="showTab('stock-summary-report-by-item-category'); return false;">
    <i class="fa-regular fa-file-lines me-2 text-secondary"></i>Stock Summary report by item category
  </a>
</li>
<li class="nav-item">
  <a href="#" class="nav-link py-2 px-4 report-nav-link" 
     data-tab="item-wise-discount"
     onclick="showTab('item-wise-discount'); return false;">
    <i class="fa-regular fa-file-lines me-2 text-secondary"></i>Item wise discount
  </a>
</li>

          </ul>
          <h6 class="text-secondary text-uppercase fw-bold px-4 mb-3 mt-4"
            style="font-size: 11px; letter-spacing: 0.5px;">Business Status</h6>
          <ul class="nav flex-column mb-4 reports-nav">

            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="bank statement"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Bank Statement</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="discount report"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Discount report</a></li>

          </ul>

          <h6 class="text-secondary text-uppercase fw-bold px-4 mb-3 mt-4"
            style="font-size: 11px; letter-spacing: 0.5px;">Taxes</h6>
          <ul class="nav flex-column mb-4 reports-nav">

            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="tax report"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Tax Report</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="tax rate report"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Tax Rate Report</a></li>

          </ul>
          <h6 class="text-secondary text-uppercase fw-bold px-4 mb-3 mt-4"
            style="font-size: 11px; letter-spacing: 0.5px;">Expense Report</h6>
          <ul class="nav flex-column mb-4 reports-nav">

            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="expense"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Expense</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="expense category report"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Expense Category Report</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="expense item report"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Expense item Report</a></li>

          </ul>

          <h6 class="text-secondary text-uppercase fw-bold px-4 mb-3 mt-4"
            style="font-size: 11px; letter-spacing: 0.5px;">Sale Order Report</h6>
          <ul class="nav flex-column mb-4 reports-nav">

            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="sale order"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Sale Order</a></li>
            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="sale order item"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Sale Order item</a></li>


          </ul>
          <h6 class="text-secondary text-uppercase fw-bold px-4 mb-3 mt-4"
            style="font-size: 11px; letter-spacing: 0.5px;">Loan Accounts</h6>
          <ul class="nav flex-column mb-4 reports-nav">

            <li class="nav-item"><a href="#" class="nav-link py-2 px-4" data-target="loan statement"><i
                  class="fa-regular fa-file-lines me-2 text-secondary"></i>Loan Statement</a></li>



          </ul>
        </div>
      </aside>

      <!-- Main Reports Content Area -->
      <div class="flex-grow-1 overflow-auto p-4" id="reportsContentArea">

        <!-- Tab Content: Sale -->
       @include('dashboard.reports.tabs.sale-report')
        <!-- purchase Bills  -->
        <div id="tab-Purchase" class="report-tab-content d-none">

          <!-- Header -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0">Purchase Bills</h3>
            <div class="d-flex gap-2">
              <button class="btn btn-danger rounded-pill px-4 fw-medium text-white"
                style="background-color: #ef4444; border: none;">
                <span class="fs-5 lh-1 me-1">+</span> Add Purchase
              </button>
              <button class="btn bg-white border border-secondary-subtle text-secondary px-3 py-1 shadow-sm">
                <i class="fa-solid fa-gear"></i>
              </button>
            </div>
          </div>

          <div class="d-flex align-items-center bg-light px-4 py-2 rounded">
            <div class="d-flex">
              <!-- <div class="d-flex justify-content-center align-items-center me-2">Filter By: </div> -->
              <select name="" id="" class="bg-transparent border-0 fs-5 fw-bold" style="outline:none;">
                <option value="">All Purchase Invoices</option>
                <option value="" selected>This Month</option>
                <option value="">Last Month</option>
                <option value="">This Quarter</option>
                <option value="">This Year</option>
                <option value="">Custom</option>
              </select>
            </div>
            <div class="d-flex pt-3 ps-2">
              <p class="text-center pt-2 text-white fw-bold"
                style="width: 6rem; height: 40px; background-color: #AAAAAA; border-radius: 5px 0px 0px 5px">Between</p>
              <div class="d-flex justify-content-center pt-2 gap-3"
                style="width: 20rem; height: 40px; border-radius: 0px 5px 5px 0px; border: 1px solid #AAAAAA">
                <p>01/03/2026</p>
                <p>To</p>
                <p>31/03/2026</p>
              </div>

            </div>
            <div class="d-flex justify-content-center align-items-center ms-4"
              style="border: 1px solid #AAAAAA;border-radius: 5px; width: 8rem; height: 40px;"><select name="" id=""
                class="bg-transparent border-0" style="outline:none;">
                <option value="" selected>All Firms</option>
                <option value=""><a href="">Firm 1</a></option>
                <option value=""><a href="">Firm 2</a></option>
                <option value=""><a href="">Firm 3</a></option>

              </select></div>
            <div class="px-5 mx-5"></div>
            <div class="d-flex gap-5 text-secondary">
              <i class="fa-solid fa-file-excel fs-5"></i>
              <i class="fa-solid fa-print fs-5"></i>
            </div>

          </div>

          <div class="bg-light mb-2 px-4 py-3 rounded d-flex">
            <div class="rounded d-flex flex-column ps-3 pt-2"
              style="width: 11rem; height:5rem; background-color: #B9F3E7;">
              <p class="mb-1 h5">Paid</p>
              <p class="fs-4 fw-bold">Rs 0.00</p>
            </div>
            <div class="d-flex align-items-center mx-3"><span class="h2">+</span></div>
            <div class="rounded" style="width: 11rem; height:5rem; background-color: #B9F3E7;">
              <div class="rounded d-flex flex-column ps-3 pt-2"
                style="width: 11rem; height:5rem; background-color: #CFE6FE;">
                <p class="mb-1 h5">Unpaid</p>
                <p class="fs-4 fw-bold">Rs 0.00</p>
              </div>
            </div>
            <div class="d-flex align-items-center mx-3"><span class="h2">=</span></div>
            <div class="rounded" style="width: 11rem; height:5rem; background-color: #B9F3E7;">
              <div class="rounded d-flex flex-column ps-3 pt-2"
                style="width: 11rem; height:5rem; background-color: #F8C889;">
                <p class="mb-1 h5">Total</p>
                <p class="fs-4 fw-bold">Rs 0.00</p>
              </div>
            </div>
          </div>

          <div class="card shadow-sm border-0">
            <div class="card-body">
              <div class="row g-2 mb-3">
                <p class="fw-bold">Transactions</p>
              </div>

              <div class="table-responsive small-table">
                <table class="table table-hover mb-0 align-middle table-clean">
                  <thead>
                    <tr class="d-flex gap-3">
                      <th class="d-flex">
                        <p class="pt-1">Date</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Date:</p>
                              <input type="date" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                            </li>
                            <div class="mt-2 ms-4">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Invoice No.</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Party Name</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Party Name</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Party Name</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Price Range</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Price Range</p>
                              <input type="range" min="" max="" value="100">
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Min</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;"
                                placeholder="0">
                              <p class="mb-0 mt-1" style="font-size: 11px;">Max</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;"
                                placeholder="+500000">

                            </li>
                            <div class="mt-2 ms-4">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Balance</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Price Range</p>
                              <input type="range" min="" max="" value="100">
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Min</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;"
                                placeholder="0">
                              <p class="mb-0 mt-1" style="font-size: 11px;">Max</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;"
                                placeholder="+500000">

                            </li>
                            <div class="mt-2 ms-4">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Status</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Open</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Completed</span>
                            </li>
                            <div class="mt-2 ms-4">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Actions</p>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="7" class="text-center text-muted py-4">
                        No estimates yet. Click "New Estimate" to create one.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
        <!-- Day book  -->
        <div id="tab-Daybook" class="report-tab-content d-none">


          <div class="d-flex align-items-center bg-light px-4 py-2 rounded mb-2">

            <div class="d-flex pt-3 ps-2">
              <p class="text-center pt-2 text-white fw-bold"
                style="width: 6rem; height: 40px; background-color: #AAAAAA; border-radius: 5px 0px 0px 5px">Date</p>
              <div class="d-flex justify-content-center pt-2 gap-3"
                style="width: 10rem; height: 40px; border-radius: 0px 5px 5px 0px; border: 1px solid #AAAAAA">
                <p>01/03/2026</p>
              </div>

            </div>
            <div class="d-flex justify-content-center align-items-center ms-4"
              style="border: 1px solid #AAAAAA;border-radius: 5px; width: 8rem; height: 40px;"><select name="" id=""
                class="bg-transparent border-0" style="outline:none;">
                <option value="" selected>All Firms</option>
                <option value=""><a href="">Firm 1</a></option>
                <option value=""><a href="">Firm 2</a></option>
                <option value=""><a href="">Firm 3</a></option>

              </select></div>
            <div class="px-3 col-3"></div>
            <div class="px-5 mx-5"></div>
            <div class="d-flex gap-5 text-secondary">
              <i class="fa-solid fa-file-excel fs-5"></i>
              <i class="fa-solid fa-print fs-5"></i>
            </div>

          </div>



          <div class="card shadow-sm border-0">
            <div class="card-body">
              <div class="row g-2 mb-3">
                <p class="fw-bold">Transactions</p>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <div class="topbar-search ms-3">
                  <span class="search-icon"><i class="bi bi-search"></i></span>
                  <input type="text" placeholder="Search...">
                </div>

              </div>

              <div class="table-responsive small-table">
                <table class="table table-hover mb-0 align-middle table-clean">
                  <thead>
                    <tr class="d-flex gap-3">

                      <th class="d-flex">
                        <p class="pt-1">Name</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Contains</option>
                                <option value=""><a href="">Exact match</a></option>

                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Name</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Reference No.</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Contains</option>
                                <option value=""><a href="">Exact match</a></option>

                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Reference No.</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Type</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Sale</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Purchase</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Payment In</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Payment Out</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Credit Note</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Debit Note</span>
                            </li>
                            <div class="mt-2 ms-2">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Payment Type</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Cash</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Cheque</span>
                            </li>

                            <div class="mt-2 ms-2">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>


                      <th class="d-flex">
                        <p class="pt-1">Total</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Total Amount</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Money In</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Money In</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Money Out</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Money Out</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>

                      <th class="d-flex">
                        <p class="pt-1">Print/Share</p>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="7" class="text-center text-muted py-4">
                        No estimates yet. Click "New Estimate" to create one.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div>
          </div>
        </div>
        <!-- All Transactions  -->
        <div id="tab-Alltransactions" class="report-tab-content d-none">



          <div class="d-flex align-items-center bg-light px-4 py-2">
            <div class="d-flex">
              <!-- <div class="d-flex justify-content-center align-items-center me-2">Filter By: </div> -->
              <select name="" id="" class="bg-transparent border-0 fs-5 fw-bold" style="outline:none;">
                <option value="">All Purchase Invoices</option>
                <option value="" selected>This Month</option>
                <option value="">Last Month</option>
                <option value="">This Quarter</option>
                <option value="">This Year</option>
                <option value="">Custom</option>
              </select>
            </div>
            <div class="d-flex pt-3 ps-2">
              <p class="text-center pt-2 text-white fw-bold"
                style="width: 6rem; height: 40px; background-color: #AAAAAA; border-radius: 5px 0px 0px 5px">Between</p>
              <div class="d-flex justify-content-center pt-2 gap-3"
                style="width: 15rem; height: 40px; border-radius: 0px 5px 5px 0px; border: 1px solid #AAAAAA">
                <p>01/03/2026</p>
                <p>To</p>
                <p>31/03/2026</p>
              </div>

            </div>
            <div class="d-flex justify-content-center align-items-center ms-4"
              style="border: 1px solid #AAAAAA;border-radius: 5px; width: 8rem; height: 40px;"><select name="" id=""
                class="bg-transparent border-0" style="outline:none;">
                <option value="" selected>All Firms</option>
                <option value=""><a href="">Firm 1</a></option>
                <option value=""><a href="">Firm 2</a></option>
                <option value=""><a href="">Firm 3</a></option>

              </select></div>
            <div class="px-1 mx-2"></div>
            <div class="d-flex gap-5 text-secondary">
              <i class="fa-solid fa-file-excel fs-5"></i>
              <i class="fa-solid fa-print fs-5"></i>
            </div>

          </div>
          <div class="bg-light px-4 mb-3 pb-3">
            <div class="d-flex justify-content-center align-items-center"
              style="border: 1px solid #AAAAAA;border-radius: 5px; width: 13rem; height: 40px;"><select name="" id=""
                class="bg-transparent border-0" style="outline:none;">
                <option value="" selected>All Transactions</option>
                <option value=""><a href="">Sale</a></option>
                <option value=""><a href="">Purchase</a></option>
                <option value=""><a href="">Payment In</a></option>
                <option value=""><a href="">Payment Out</a></option>
                <option value=""><a href="">Credit Note</a></option>
                <option value=""><a href="">Debit Note</a></option>
                <option value=""><a href="">Sale Order</a></option>
                <option value=""><a href="">Purchase Order</a></option>
                <option value=""><a href="">Estimate</a></option>
                <option value=""><a href="">Proforma Invoice</a></option>
                <option value=""><a href="">Delivery Challan</a></option>
                <option value=""><a href="">Expense</a></option>
                <option value=""><a href="">Party to Party [Received]</a></option>
                <option value=""><a href="">Party to Party [Paid]</a></option>
                <option value=""><a href="">Manufacture</a></option>
                <option value=""><a href="">Sale FA</a></option>
                <option value=""><a href="">Purchase FA</a></option>
                <option value=""><a href="">Sale [Canceled]</a></option>
                <option value=""><a href="">Journel Entry</a></option>
                <option value=""><a href="">Purchase (Job Work)</a></option>

              </select></div>
          </div>



          <div class="card shadow-sm border-0">
            <div class="card-body">
              <div class="row g-2 mb-3">
                <p class="fw-bold">Transactions</p>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <div class="topbar-search ms-3">
                  <span class="search-icon"><i class="bi bi-search"></i></span>
                  <input type="text" placeholder="Search...">
                </div>

              </div>

              <div class="table-responsive small-table">
                <table class="table table-hover mb-0 align-middle table-clean">
                  <thead>
                    <tr class="d-flex gap-3">
                      <th class="d-flex">
                        <p class="pt-1">Date</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Date:</p>
                              <input type="date" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                            </li>
                            <div class="mt-2 ms-4">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Refernece No</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Reference No.</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Party Name</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Contains</option>
                                <option value=""><a href="">Exact Match</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Party Name</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Category Name</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Contains</option>
                                <option value=""><a href="">Exact Match</a></option>

                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Category Name</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-4">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Type</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Sale</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Purchase</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Payment In</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Payment Out</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Credit Note</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Debit Note</span>
                            </li>
                            <div class="mt-2 ms-4">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Total</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Total</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Received</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Received/Paid</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Balance</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Balance</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>


                      <th class="d-flex col-1">
                        <p class="pt-1">Print / Share</p>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="7" class="text-center text-muted py-4">
                        No estimates yet. Click "New Estimate" to create one.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div>
          </div>

        </div>
        <!-- Cash Flow -->
        <div id="tab-cashFlow" class="report-tab-content d-none">



          <div class="d-flex align-items-center bg-light px-4 py-2">
            <div class="d-flex">
              <!-- <div class="d-flex justify-content-center align-items-center me-2">Filter By: </div> -->
              <select name="" id="" class="bg-transparent border-0 fs-5 fw-bold" style="outline:none;">
                <option value="">All Purchase Invoices</option>
                <option value="" selected>This Month</option>
                <option value="">Last Month</option>
                <option value="">This Quarter</option>
                <option value="">This Year</option>
                <option value="">Custom</option>
              </select>
            </div>
            <div class="d-flex pt-3 ps-2">
              <p class="text-center pt-2 text-white fw-bold"
                style="width: 6rem; height: 40px; background-color: #AAAAAA; border-radius: 5px 0px 0px 5px">Between</p>
              <div class="d-flex justify-content-center pt-2 gap-3"
                style="width: 15rem; height: 40px; border-radius: 0px 5px 5px 0px; border: 1px solid #AAAAAA">
                <p>01/03/2026</p>
                <p>To</p>
                <p>31/03/2026</p>
              </div>

            </div>
            <div class="d-flex justify-content-center align-items-center ms-4"
              style="border: 1px solid #AAAAAA;border-radius: 5px; width: 8rem; height: 40px;"><select name="" id=""
                class="bg-transparent border-0" style="outline:none;">
                <option value="" selected>All Firms</option>
                <option value=""><a href="">Firm 1</a></option>
                <option value=""><a href="">Firm 2</a></option>
                <option value=""><a href="">Firm 3</a></option>

              </select></div>
            <div class="px-1 mx-2"></div>
            <div class="d-flex gap-5 text-secondary">
              <i class="fa-solid fa-file-excel fs-5"></i>
              <i class="fa-solid fa-print fs-5"></i>
            </div>

          </div>
          <div class="col-12 bg-light d-flex">
            <p class="text-success ps-4">Opening cash in hand: Rs 0.00</p>
            <div class="ms-4">
              <input type="checkbox" class="me-2 mt-1"><span>Show zero amount transactions</span>
            </div>
          </div>

          <div class="card shadow-sm border-0">
            <div class="card-body">
              <div class="row g-2 mb-3">
                <p class="fw-bold">Transactions</p>
              </div>
              <div class="col-12 d-flex justify-content-between">
                <div class="topbar-search ms-3">
                  <span class="search-icon"><i class="bi bi-search"></i></span>
                  <input type="text" placeholder="Search...">
                </div>

              </div>

              <div class="table-responsive small-table">
                <table class="table table-hover mb-0 align-middle table-clean">
                  <thead>
                    <tr class="d-flex gap-3">

                      <th class="d-flex">
                        <p class="pt-1">Name</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Contains</option>
                                <option value=""><a href="">Exact match</a></option>

                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Name</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Reference No.</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Contains</option>
                                <option value=""><a href="">Exact match</a></option>

                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Reference No.</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Type</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Sale</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Purchase</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Payment In</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Payment Out</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Credit Note</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Debit Note</span>
                            </li>
                            <div class="mt-2 ms-2">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Payment Type</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Cash</span>
                            </li>
                            <li class="dropdown-item">
                              <input type="checkbox"><span class="ms-1">Cheque</span>
                            </li>

                            <div class="mt-2 ms-2">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>


                      <th class="d-flex">
                        <p class="pt-1">Total</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Total Amount</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Cash In</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Cash In</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Cash Out</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Cash Out</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>
                      <th class="d-flex">
                        <p class="pt-1">Running Cash</p>
                        <div class="dropdown ms-3">
                          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                            aria-expanded="false">
                            <i class="fa-solid fa-filter"></i>
                          </button>
                          <ul class="dropdown-menu">
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                              <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                                style="outline:none;">
                                <option value="" selected>Equal to</option>
                                <option value=""><a href="">Less than</a></option>
                                <option value=""><a href="">Greater than</a></option>
                                <option value=""><a href="">Range</a></option>
                              </select>
                            </li>
                            <li class="dropdown-item">
                              <p class="mb-0" style="font-size: 11px;">Running Cash in hand</p>
                              <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                            </li>
                            <div class="mt-2 ms-3">
                              <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                  style="color: #71748E;">Clear</span></button>
                              <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                  class="text-light">Apply</span></button>
                            </div>

                          </ul>
                        </div>
                      </th>

                      <th class="d-flex">
                        <p class="pt-1">Print/Share</p>
                      </th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td colspan="7" class="text-center text-muted py-4">
                        No estimates yet. Click "New Estimate" to create one.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

            </div>
          </div>


        </div>



        <!-- Profit and Loss Tab -->
        <div id="tab-ProfitAndLoss" class="report-tab-content d-none p-4 w-100 bg-white pnl-container"
          style="height: 100%; overflow-y: auto; overflow-x: hidden;">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3 border rounded px-3 py-2 bg-white"
              style="border-color: #e5e7eb;">
              <span class="text-secondary fw-medium" style="font-size: 14px;">From</span>
              <div class="d-flex align-items-center gap-2 cursor-pointer">
                <input type="date" value="2026-03-01" class="fw-medium text-dark bg-transparent border-0"
                  style="font-size: 14px; outline: none; box-shadow: none;">
              </div>
              <span class="text-secondary fw-medium ms-2" style="font-size: 14px;">To</span>
              <div class="d-flex align-items-center gap-2 cursor-pointer">
                <input type="date" value="2026-03-27" class="fw-medium text-dark bg-transparent border-0"
                  style="font-size: 14px; outline: none; box-shadow: none;">
              </div>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-white border rounded-circle d-flex align-items-center justify-content-center"
                style="width: 40px; height: 40px; border-color: #e5e7eb;">
                <i class="fa-regular fa-file-excel text-success fs-5"></i>
              </button>
              <button class="btn btn-white border rounded-circle d-flex align-items-center justify-content-center"
                style="width: 40px; height: 40px; border-color: #e5e7eb;">
                <i class="fa-solid fa-print text-secondary fs-5"></i>
              </button>
            </div>
          </div>

          <h4 class="fw-bold text-dark mb-4 text-uppercase" style="letter-spacing: 0.5px;">Profit and Loss Report</h4>

          <div class="d-flex justify-content-between align-items-end mb-3">
            <div class="d-flex align-items-center gap-3">
              <span class="text-secondary fw-medium" style="font-size: 14px;">View :</span>
              <div class="form-check mb-0">
                <input class="form-check-input" type="radio" name="pnlViewType" id="viewVyapar" value="vyapar" checked>
                <label class="form-check-label fw-medium text-dark" for="viewVyapar" style="font-size: 14px;">
                  Vyapar
                </label>
              </div>
              <div class="form-check mb-0">
                <input class="form-check-input" type="radio" name="pnlViewType" id="viewAccounting" value="accounting">
                <label class="form-check-label fw-medium text-dark" for="viewAccounting" style="font-size: 14px;">
                  Accounting
                </label>
              </div>
            </div>

            <button id="pnlExpandAllBtn" class="btn btn-link text-primary text-decoration-none p-0 fw-medium d-none"
              style="font-size: 14px;">
              <i class="fa-solid fa-chevron-down me-1"></i> Expand all accounts
            </button>
          </div>

          <!-- Table Header -->
          <div class="d-flex justify-content-between align-items-center px-4 py-2 mb-2 rounded"
            style="background-color: #f3f4f6;">
            <span class="fw-bold text-dark" style="font-size: 14px;">Particulars</span>
            <span class="fw-bold text-dark" style="font-size: 14px;">Amount</span>
          </div>

          <!-- Vyapar View Data -->
          <div id="pnlVyaparView" class="w-100">
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom"
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Sale (+)</span>
              <span class="text-success fw-medium" style="font-size: 14px;">Rs 1,211.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom"
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Credit Note (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom"
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Sale FA (+)</span>
              <span class="text-success fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom"
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Purchase (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom"
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Debit Note (+)</span>
              <span class="text-success fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom"
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Purchase FA (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>

            <div class="d-flex justify-content-between align-items-center px-4 py-3 "
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Direct Expenses(-)</span>
              <span></span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-white"
              style="padding-left: 2.5rem !important;">
              <span class="text-dark" style="font-size: 14px;">Other Direct Expenses (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-white"
              style="padding-left: 2.5rem !important;">
              <span class="text-dark" style="font-size: 14px;">Payment-in Discount (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>

            <div class="d-flex justify-content-between align-items-center px-4 py-3 "
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Tax Payable (-)</span>
              <span></span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-white"
              style="padding-left: 2.5rem !important;">
              <span class="text-dark" style="font-size: 14px;">Tax Payable (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3  bg-white"
              style="padding-left: 2.5rem !important;">
              <span class="text-dark" style="font-size: 14px;">TCS Payable (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-white"
              style="padding-left: 2.5rem !important;">
              <span class="text-dark" style="font-size: 14px;">TDS Payable (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 "
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Tax Receivable (-)</span>
              <span></span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3  bg-white"
              style="padding-left: 2.5rem !important;">
              <span class="text-dark" style="font-size: 14px;">Tax Receivable (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-white"
              style="padding-left: 2.5rem !important;">
              <span class="text-dark" style="font-size: 14px;">TCS Receivable (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-white"
              style="padding-left: 2.5rem !important;">
              <span class="text-dark" style="font-size: 14px;">TDS Receivable (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 "
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Opening Socket (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 1,211.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 "
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Closing Socket (+)</span>
              <span class="text-success fw-medium" style="font-size: 14px;">Rs 1,211.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3" style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Opening Socket FA (-)</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 1,211.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom"
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Closing Socket FA (+)</span>
              <span class="text-success fw-medium" style="font-size: 14px;">Rs 1,211.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom"
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Gross Profit</span>
              <span class="text-success fw-medium" style="font-size: 14px;">Rs 1,211.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3" style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Other Income</span>
              <span class="text-success fw-medium" style="font-size: 14px;">Rs 1,211.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 "
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Indirect Expenses (-)</span>
              <span></span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-white"
              style="padding-left: 2.5rem !important;">
              <span class="text-dark" style="font-size: 14px;">Other Expenses</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-white"
              style="padding-left: 2.5rem !important;">
              <span class="text-dark" style="font-size: 14px;">Loan Interest Expenses</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 bg-white"
              style="padding-left: 2.5rem !important;">
              <span class="text-dark" style="font-size: 14px;">Loan Processing Fee Expense</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-white"
              style="padding-left: 2.5rem !important;">
              <span class="text-dark" style="font-size: 14px;">Loan Charges Expense</span>
              <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
            </div>
            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom"
              style="background-color: #fafafa;">
              <span class="text-dark fw-medium" style="font-size: 14px;">Profit</span>
              <span class="text-success fw-medium" style="font-size: 14px;">Rs 1,211.00</span>
            </div>
          </div>

          <!-- Accounting View Data -->
          <div id="pnlAccountingView" class="w-100 d-none">
            <div
              class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom bg-white pnl-row-hover">
              <span class="text-dark fw-bold" style="font-size: 14px;"><span class="pnl-chevron"><i
                    class="fa-solid fa-chevron-down"></i></span> Income</span>
              <span></span>
            </div>
            <!-- L2 Children -->
            <div class="pnl-child-group">
              <div class="d-flex justify-content-between align-items-center py-3 border-bottom bg-white pnl-row-hover"
                style="padding-left: 3.5rem; padding-right: 1.5rem;">
                <span class="text-dark fw-medium" style="font-size: 14px;"><span class="pnl-chevron"><i
                      class="fa-solid fa-chevron-down"></i></span> Sale Accounts</span>
                <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
              </div>
              <div class="pnl-child-group">

                <div class="pnl-child-group">
                  <div
                    class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                    style="padding-left: 6.5rem; padding-right: 1.5rem;">
                    <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span> Sale
                      Revenue Account</span>
                    <span class="text-Success fw-medium" style="font-size: 14px;">Rs 0.00</span>
                  </div>
                  <div
                    class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                    style="padding-left: 6.5rem; padding-right: 1.5rem;">
                    <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                      Additional Charges on Sale</span>
                    <span class="text-success fw-medium" style="font-size: 14px;">Rs 0.00</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="pnl-child-group">
              <div class="d-flex justify-content-between align-items-center py-3 border-bottom bg-white pnl-row-hover"
                style="padding-left: 3.5rem; padding-right: 1.5rem;">
                <span class="text-dark fw-medium" style="font-size: 14px;"><span class="pnl-chevron"><i
                      class="fa-solid fa-chevron-down"></i></span> Other Incomes (Direct)</span>
                <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
              </div>
              <div class="pnl-child-group">

                <div class="pnl-child-group">
                  <div
                    class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                    style="padding-left: 6.5rem; padding-right: 1.5rem;">
                    <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                      Payment-Out Discount</span>
                    <span class="text-Success fw-medium" style="font-size: 14px;">Rs 0.00</span>
                  </div>
                  <div
                    class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                    style="padding-left: 6.5rem; padding-right: 1.5rem;">
                    <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span> Other
                      Direct Incomes</span>
                    <span class="text-success fw-medium" style="font-size: 14px;">Rs 0.00</span>
                  </div>
                </div>
              </div>


            </div>
            <div class="pnl-child-group">
              <div class="d-flex justify-content-between align-items-center py-3 border-bottom bg-white pnl-row-hover"
                style="padding-left: 3.5rem; padding-right: 1.5rem;">
                <span class="text-dark fw-medium" style="font-size: 14px;"><span class="pnl-chevron"><i
                      class="fa-solid fa-chevron-down"></i></span> Other Incomes (Indirect)</span>
                <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
              </div>
              <div class="pnl-child-group">

                <div class="pnl-child-group">
                  <div
                    class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                    style="padding-left: 6.5rem; padding-right: 1.5rem;">
                    <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span> Profit
                      on Sale of Assets</span>
                    <span class="text-Success fw-medium" style="font-size: 14px;">Rs 0.00</span>
                  </div>
                  <div
                    class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                    style="padding-left: 6.5rem; padding-right: 1.5rem;">
                    <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                      Appreciation on Assets</span>
                    <span class="text-success fw-medium" style="font-size: 14px;">Rs 0.00</span>
                  </div>
                </div>
                <div class="pnl-child-group">
                  <div
                    class="d-flex justify-content-between align-items-center py-3 border-bottom bg-white pnl-row-hover"
                    style="padding-left: 3.5rem; padding-right: 1.5rem;">
                    <span class="text-dark fw-medium" style="font-size: 14px;"><span class="pnl-chevron"><i
                          class="fa-solid fa-chevron-down"></i></span> Expenses</span>
                    <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
                  </div>
                  <div class="pnl-child-group">
                    <div
                      class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                      style="padding-left: 5rem; padding-right: 1.5rem;">
                      <span class="text-dark" style="font-size: 14px;"><span class="pnl-chevron"><i
                            class="fa-solid fa-chevron-right"></i></span> Purchase Accounts</span>
                      <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
                    </div>
                    <div class="pnl-child-group">
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Opening Socket</span>
                        <span class="text-Success fw-medium" style="font-size: 14px;">Rs 0.00</span>
                      </div>
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Closing Socket</span>
                        <span class="text-success fw-medium" style="font-size: 14px;">Rs 0.00</span>
                      </div>
                    </div>
                  </div>

                  <div class="pnl-child-group">
                    <div
                      class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                      style="padding-left: 5rem; padding-right: 1.5rem;">
                      <span class="text-dark" style="font-size: 14px;"><span class="pnl-chevron"><i
                            class="fa-solid fa-chevron-right"></i></span> Direct Expenses</span>
                      <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
                    </div>
                    <div class="pnl-child-group">
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Payment-In Discount</span>
                        <span class="text-Success fw-medium" style="font-size: 14px;">Rs 0.00</span>
                      </div>
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Manufacturing Expense</span>
                        <span class="text-success fw-medium" style="font-size: 14px;">Rs 0.00</span>
                      </div>
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Petrol</span>
                        <span class="text-success fw-medium" style="font-size: 14px;">Rs 0.00</span>
                      </div>
                    </div>
                  </div>

                  <div class="pnl-child-group" style="display: none;"></div>

                  <div
                    class="d-flex justify-content-between align-items-center py-3 border-bottom bg-white pnl-row-hover"
                    style="padding-left: 3.5rem; padding-right: 1.5rem;">
                    <span class="text-dark fw-medium" style="font-size: 14px;"><span class="pnl-chevron"><i
                          class="fa-solid fa-chevron-down"></i></span> Indirect Expenses</span>
                    <span class="text-danger fw-medium" style="font-size: 14px;">Rs 100.00</span>
                  </div>
                  <div class="pnl-child-group">
                    <div
                      class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                      style="padding-left: 5rem; padding-right: 1.5rem;">
                      <span class="text-dark" style="font-size: 14px;"><span class="pnl-chevron"><i
                            class="fa-solid fa-chevron-right"></i></span> Cost of Financing</span>
                      <span class="text-danger fw-medium" style="font-size: 14px;">Rs 100.00</span>
                    </div>
                    <div class="pnl-child-group" style="display: none;">
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Charges On Loan</span>
                        <span class="text-danger fw-medium" style="font-size: 14px;">Rs 100.00</span>
                      </div>
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Processing Fee for Loans</span>
                        <span class="text-danger fw-medium" style="font-size: 14px;">Rs 100.00</span>
                      </div>
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Interest Payment for ABC</span>
                        <span class="text-danger fw-medium" style="font-size: 14px;">Rs 100.00</span>
                      </div>
                    </div>
                    <div
                      class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                      style="padding-left: 5rem; padding-right: 1.5rem;">
                      <span class="text-dark" style="font-size: 14px;"><span class="pnl-chevron"><i
                            class="fa-solid fa-chevron-right"></i></span> Other Expenses</span>
                      <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
                    </div>
                    <div class="pnl-child-group" style="display: none;">
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Expenses on Purchase of assets</span>
                        <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
                      </div>
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Depreciation of assets</span>
                        <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
                      </div>
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Loyalty redeemed</span>
                        <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
                      </div>
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Rent</span>
                        <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
                      </div>
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Salary</span>
                        <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
                      </div>
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Tea</span>
                        <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
                      </div>
                      <div
                        class="d-flex justify-content-between align-items-center py-2 border-bottom bg-white pnl-row-hover"
                        style="padding-left: 6.5rem; padding-right: 1.5rem;">
                        <span class="text-secondary" style="font-size: 14px;"><span class="pnl-bullet">&bull;</span>
                          Transport</span>
                        <span class="text-danger fw-medium" style="font-size: 14px;">Rs 0.00</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>


            </div>

            <!-- Footer Row -->
            <div class="d-flex justify-content-between align-items-center px-4 py-4 mt-2">
              <span class="text-dark fw-bold" style="font-size: 14px;">Net Profit (Incomes - Expenses)</span>
              <span class="text-success fw-bold" style="font-size: 14px;">Rs 1,111.00</span>
            </div>

          </div>
        </div>

        <!-- Party Statement Tab -->
        


        
       
 @include('dashboard.reports.tabs.party-reports')


        <!-- Stock Summary Tab -->
      <!-- Stock Summary Tab -->
        @include('dashboard.reports.tabs.item-stock-reports')


       

        <!-- bank statement -->
        <div id="tab-bank statement" class="report-tab-content d-none">
          <div class="d-flex flex-column"
            style="min-height: 100vh; padding: 24px; background-color: #ffffff; border: 1px solid #e5e7eb;">

            <!-- Filters & Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <!-- Left Side -->
              <div class="d-flex align-items-center" style="gap: 16px;">
                <span style="font-size: 12px; font-weight: 700; color: #6b7280; text-transform: uppercase;">Bank
                  Name</span>

                <select class="form-select form-select-sm"
                  style="width: 130px; border: 1px solid #e5e7eb; border-radius: 4px; color: #374151; box-shadow: none; outline: none;">
                  <option selected>ABC</option>
                </select>

                <div class="form-check mb-0 d-flex align-items-center" style="gap: 8px;">
                  <input class="form-check-input mt-0" type="checkbox" id="stockDateFilter"
                    style="border-color: #d1d5db; box-shadow: none;">
                  <label class="form-check-label mb-0" for="stockDateFilter"
                    style="color: #6b7280; font-size: 14px;">Date filter</label>
                </div>

                <div id="stockDateInput" class="d-flex align-items-center px-2 py-1 d-none"
                  style="border: 1px solid #d1d5db; border-radius: 4px; background-color: #ffffff;">
                  <span style="font-size: 12px; color: #9ca3af; margin-right: 8px;">Date</span>
                  <span style="font-size: 14px; color: #374151; margin-right: 8px; font-weight: 500;">28/03/2026</span>
                  <i class="fa-regular fa-calendar" style="color: #9ca3af; font-size: 14px;"></i>
                </div>


              </div>

              <!-- Right Side -->
              <div class="d-flex" style="gap: 8px;">
                <button id="stockExcelBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-file-excel" style="color: #10b981; font-size: 18px;"></i>
                </button>
                <button id="stockPrintBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-print" style="color: #4b5563; font-size: 18px;"></i>
                </button>
              </div>
            </div>

            <!-- Page Title -->
            <h2 style="font-weight: 700; color: #1f2937; margin: 24px 0 32px 0; font-size: 24px;">Bank Statement</h2>

            <!-- Data Table Architecture -->
            <div class="table-responsive">
              <table class="w-100" style="border-collapse: collapse;">
                <thead style="background-color: #f3f4f6;">
                  <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px 16px; width: 40px;"></th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: left; border-right: 1px solid #e5e7eb;">
                      Date</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Description</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Withdrawal Amount</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Deposit Amount</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right;">
                      Balance Amount</th>

                  </tr>

                  </tr>
                </thead>
                <tbody style="background-color: #ffffff;">
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">1</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      abc</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td class="stock-qty-cell"
                      style="padding: 16px; font-size: 14px; color: #ef4444; text-align: right; border-right: 1px solid #e5e7eb;">
                      -1</td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right;">Rs 0.00</td>

                  </tr>
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">2</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      def</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 200.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>
                    <td class="stock-qty-cell"
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      5</td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right;">Rs 1,000.00</td>

                  </tr>
                </tbody>
                <tfoot style="background-color: #ffffff;">
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Balance</td>
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px;"></td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #ef4444; font-weight: 700; text-align: right; border-right: 1px solid #e5e7eb;">
                      -1</td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>

                </tfoot>
              </table>
            </div>

          </div>
        </div>

        <!-- discount report -->
        <div id="tab-discount report" class="report-tab-content d-none">
          <div class="d-flex flex-column"
            style="min-height: 100vh; padding: 24px; background-color: #ffffff; border: 1px solid #e5e7eb;">

            <!-- Filters & Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <!-- Left Side -->
              <div class="d-flex align-items-center" style="gap: 16px;">


                <div class="form-check mb-0 d-flex align-items-center" style="gap: 8px;">
                  <input class="form-check-input mt-0" type="checkbox" id="stockDateFilter"
                    style="border-color: #d1d5db; box-shadow: none;">
                  <label class="form-check-label mb-0" for="stockDateFilter"
                    style="color: #6b7280; font-size: 14px;">Date filter</label>
                </div>

                <div id="stockDateInput" class="d-flex align-items-center px-2 py-1 d-none"
                  style="border: 1px solid #d1d5db; border-radius: 4px; background-color: #ffffff;">
                  <span style="font-size: 12px; color: #9ca3af; margin-right: 8px;">Date</span>
                  <span style="font-size: 14px; color: #374151; margin-right: 8px; font-weight: 500;">28/03/2026</span>
                  <i class="fa-regular fa-calendar" style="color: #9ca3af; font-size: 14px;"></i>
                </div>


              </div>

              <!-- Right Side -->
              <div class="d-flex" style="gap: 8px;">
                <button id="stockExcelBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-file-excel" style="color: #10b981; font-size: 18px;"></i>
                </button>
                <button id="stockPrintBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-print" style="color: #4b5563; font-size: 18px;"></i>
                </button>
              </div>
            </div>

            <!-- Page Title -->
            <h2 style="font-weight: 700; color: #1f2937; margin: 24px 0 32px 0; font-size: 24px;">Discount Report</h2>

            <!-- Data Table Architecture -->
            <div class="table-responsive">
              <table class="w-100" style="border-collapse: collapse;">
                <thead style="background-color: #f3f4f6;">
                  <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px 16px; width: 40px;"></th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: left; border-right: 1px solid #e5e7eb;">
                      Party Name</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Sale Discount</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Purchase / Expense Discount</th>


                  </tr>

                  </tr>
                </thead>
                <tbody style="background-color: #ffffff;">
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">1</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      abc</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>


                  </tr>
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">2</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      def</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 200.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>


                  </tr>
                </tbody>
                <tfoot style="background-color: #ffffff;">
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Total Sale Discount</td>
                    <td style="padding: 16px;"></td>


                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Total Purchase Discount</td>
                    <td style="padding: 16px;"></td>


                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>

                </tfoot>
              </table>
            </div>

          </div>
        </div>

        <!-- tax report -->
        <div id="tab-tax report" class="report-tab-content d-none">
          <div class="d-flex flex-column"
            style="min-height: 100vh; padding: 24px; background-color: #ffffff; border: 1px solid #e5e7eb;">

            <!-- Filters & Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <!-- Left Side -->
              <div class="d-flex align-items-center" style="gap: 16px;">


                <div class="form-check mb-0 d-flex align-items-center" style="gap: 8px;">
                  <input class="form-check-input mt-0" type="checkbox" id="stockDateFilter"
                    style="border-color: #d1d5db; box-shadow: none;">
                  <label class="form-check-label mb-0" for="stockDateFilter"
                    style="color: #6b7280; font-size: 14px;">Date filter</label>
                </div>

                <div id="stockDateInput" class="d-flex align-items-center px-2 py-1 d-none"
                  style="border: 1px solid #d1d5db; border-radius: 4px; background-color: #ffffff;">
                  <span style="font-size: 12px; color: #9ca3af; margin-right: 8px;">Date</span>
                  <span style="font-size: 14px; color: #374151; margin-right: 8px; font-weight: 500;">28/03/2026</span>
                  <i class="fa-regular fa-calendar" style="color: #9ca3af; font-size: 14px;"></i>
                </div>


              </div>

              <!-- Right Side -->
              <div class="d-flex" style="gap: 8px;">
                <button id="stockExcelBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-file-excel" style="color: #10b981; font-size: 18px;"></i>
                </button>
                <button id="stockPrintBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-print" style="color: #4b5563; font-size: 18px;"></i>
                </button>
              </div>
            </div>

            <!-- Page Title -->
            <h2 style="font-weight: 700; color: #1f2937; margin: 24px 0 32px 0; font-size: 24px;">Tax Report</h2>

            <!-- Data Table Architecture -->
            <div class="table-responsive">
              <table class="w-100" style="border-collapse: collapse;">
                <thead style="background-color: #f3f4f6;">
                  <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px 16px; width: 40px;"></th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: left; border-right: 1px solid #e5e7eb;">
                      Party Name</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Sale Tax</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Purchase / Expense Tax</th>


                  </tr>

                  </tr>
                </thead>
                <tbody style="background-color: #ffffff;">
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">1</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      abc</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>


                  </tr>
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">2</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      def</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 200.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>


                  </tr>
                </tbody>
                <tfoot style="background-color: #ffffff;">
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Total TAx In</td>
                    <td style="padding: 16px;"></td>


                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Total Tax Out</td>
                    <td style="padding: 16px;"></td>


                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>

                </tfoot>
              </table>
            </div>

          </div>
        </div>

        <!-- tax rate report -->
        <div id="tab-tax rate report" class="report-tab-content d-none">
          <div class="d-flex flex-column"
            style="min-height: 100vh; padding: 24px; background-color: #ffffff; border: 1px solid #e5e7eb;">

            <!-- Filters & Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <!-- Left Side -->
              <div class="d-flex align-items-center" style="gap: 16px;">


                <div class="form-check mb-0 d-flex align-items-center" style="gap: 8px;">
                  <input class="form-check-input mt-0" type="checkbox" id="stockDateFilter"
                    style="border-color: #d1d5db; box-shadow: none;">
                  <label class="form-check-label mb-0" for="stockDateFilter"
                    style="color: #6b7280; font-size: 14px;">Date filter</label>
                </div>

                <div id="stockDateInput" class="d-flex align-items-center px-2 py-1 d-none"
                  style="border: 1px solid #d1d5db; border-radius: 4px; background-color: #ffffff;">
                  <span style="font-size: 12px; color: #9ca3af; margin-right: 8px;">Date</span>
                  <span style="font-size: 14px; color: #374151; margin-right: 8px; font-weight: 500;">28/03/2026</span>
                  <i class="fa-regular fa-calendar" style="color: #9ca3af; font-size: 14px;"></i>
                </div>


              </div>

              <!-- Right Side -->
              <div class="d-flex" style="gap: 8px;">
                <button id="stockExcelBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-file-excel" style="color: #10b981; font-size: 18px;"></i>
                </button>
                <button id="stockPrintBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-print" style="color: #4b5563; font-size: 18px;"></i>
                </button>
              </div>
            </div>

            <!-- Page Title -->
            <h2 style="font-weight: 700; color: #1f2937; margin: 24px 0 32px 0; font-size: 24px;">Tax Rate Report</h2>

            <!-- Data Table Architecture -->
            <div class="table-responsive">
              <table class="w-100" style="border-collapse: collapse;">
                <thead style="background-color: #f3f4f6;">
                  <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px 16px; width: 40px;"></th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: left; border-right: 1px solid #e5e7eb;">
                      Tax Name</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Tax Percent</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Taxable Sale Amount</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Tax In</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Taxable Purchase/ Expense Amount</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Tax Out</th>


                  </tr>

                  </tr>
                </thead>
                <tbody style="background-color: #ffffff;">
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">1</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      abc</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>


                  </tr>
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">2</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      def</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 200.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>


                  </tr>
                </tbody>
                <tfoot style="background-color: #ffffff;">
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Total Tax In</td>
                    <td style="padding: 16px;"></td>


                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Total Tax Out</td>
                    <td style="padding: 16px;"></td>


                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>

                </tfoot>
              </table>
            </div>

          </div>
        </div>

        <!-- expense report  -->
        <div id="tab-expense" class="report-tab-content">

          <!-- Header -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0">Expenses</h3>
            <div class="d-flex gap-2">
              <button class="btn btn-danger rounded-pill px-4 fw-medium text-white"
                style="background-color: #ef4444; border: none;">
                <span class="fs-5 lh-1 me-1">+</span> Add Expense
              </button>
              <button class="btn bg-white border border-secondary-subtle text-secondary px-3 py-1 shadow-sm">
                <i class="fa-solid fa-gear"></i>
              </button>
            </div>
          </div>

          <!-- Filter Bar -->
          <div class="d-flex justify-content-between align-items-center bg-light mb-2 px-4 py-2 rounded">
            <div class="d-flex">
              <div class="d-flex justify-content-center align-items-center me-2">Filter By: </div>
              <div class="d-flex rounded-pill" style="background-color:#E4F2FF;">
                <div class="d-flex justify-content-center align-items-center text-center"
                  style="width: 9rem; height:40px; border-right: 1px solid rgb(45, 44, 44); font-size:12px;"><select
                    name="" id="" class="bg-transparent border-0" style="outline:none;">
                    <option value="">All Estimates</option>
                    <option value="" selected>This Month</option>
                    <option value="">Last Month</option>
                    <option value="">This Quarter</option>
                    <option value="">This Year</option>
                    <option value="">Custom</option>
                  </select></div>
                <div class="d-flex justify-content-center align-items-center" style="width: 16rem; height: 40px;">
                  01/03/2026
                  To 31/03/2026</div>

              </div>
              <div class="d-flex justify-content-center align-items-center rounded-pill ms-4"
                style="background-color:#E4F2FF; width: 8rem; height: 40px;"><select name="" id=""
                  class="bg-transparent border-0" style="outline:none;">
                  <option value="" selected>All Firms</option>
                  <option value=""><a href="">Firm 1</a></option>
                  <option value=""><a href="">Firm 2</a></option>
                  <option value=""><a href="">Firm 3</a></option>

                </select></div>

            </div>
          </div>



          <!-- Data Table -->
          <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
              <h5 class="fw-bold mb-0 text-dark fs-5">Transactions</h5>
              <div class="text-secondary d-flex gap-2">
                <button class="btn btn-link text-secondary p-1 text-decoration-none" title="Search"><i
                    class="fa-solid fa-magnifying-glass"></i></button>
                <button class="btn btn-link text-secondary p-1 text-decoration-none" title="Chart"><i
                    class="fa-solid fa-chart-simple"></i></button>
                <button class="btn btn-link text-success p-1 text-decoration-none" title="Excel"><i
                    class="fa-regular fa-file-excel"></i></button>
                <button class="btn btn-link text-secondary p-1 text-decoration-none" title="Print"><i
                    class="fa-solid fa-print"></i></button>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-hover mb-0 align-middle table-clean">
                <thead>
                  <tr class="d-flex gap-3">
                    <th class="d-flex">
                      <p class="pt-1">Date</p>
                      <div class="dropdown ms-3">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa-solid fa-filter"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                            <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                              style="outline:none;">
                              <option value="" selected>Equal to</option>
                              <option value=""><a href="">Less than</a></option>
                              <option value=""><a href="">Greater than</a></option>
                              <option value=""><a href="">Range</a></option>
                            </select>
                          </li>
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Select Date:</p>
                            <input type="date" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                          </li>
                          <div class="mt-2 ms-4">
                            <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                style="color: #71748E;">Clear</span></button>
                            <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                class="text-light">Apply</span></button>
                          </div>

                        </ul>
                      </div>
                    </th>
                    <th class="d-flex">
                      <p class="pt-1">Exp No.</p>
                      <div class="dropdown ms-3">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa-solid fa-filter"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                            <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                              style="outline:none;">
                              <option value="" selected>Contanis</option>
                              <option value="">Exact Match</option>

                            </select>
                          </li>
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Exp No.</p>
                            <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                          </li>
                          <div class="mt-2 ms-3">
                            <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                style="color: #71748E;">Clear</span></button>
                            <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                class="text-light">Apply</span></button>
                          </div>

                        </ul>
                      </div>
                    </th>
                    <th class="d-flex">
                      <p class="pt-1">Party</p>
                      <div class="dropdown ms-3">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa-solid fa-filter"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                            <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                              style="outline:none;">
                              <option value="" selected>Contanis</option>
                              <option value="">Exact Match</option>

                            </select>
                          </li>
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Party</p>
                            <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                          </li>
                          <div class="mt-2 ms-3">
                            <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                style="color: #71748E;">Clear</span></button>
                            <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                class="text-light">Apply</span></button>
                          </div>

                        </ul>
                      </div>
                    </th>
                    <th class="d-flex">
                      <p class="pt-1">Category Name</p>
                      <div class="dropdown ms-3">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa-solid fa-filter"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                            <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                              style="outline:none;">
                              <option value="" selected>Contanis</option>
                              <option value="">Exact Match</option>

                            </select>
                          </li>
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Category Name</p>
                            <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                          </li>
                          <div class="mt-2 ms-3">
                            <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                style="color: #71748E;">Clear</span></button>
                            <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                class="text-light">Apply</span></button>
                          </div>

                        </ul>
                      </div>
                    </th>

                    <th class="d-flex">
                      <p class="pt-1">Payment Type</p>
                      <div class="dropdown ms-3">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa-solid fa-filter"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li class="dropdown-item">
                            <input type="checkbox"><span class="ms-1">Cash</span>
                          </li>
                          <li class="dropdown-item">
                            <input type="checkbox"><span class="ms-1">Cheque</span>
                          </li>

                          <div class="mt-2 ms-4">
                            <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                style="color: #71748E;">Clear</span></button>
                            <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                class="text-light">Apply</span></button>
                          </div>

                        </ul>
                      </div>
                    </th>

                    <th class="d-flex">
                      <p class="pt-1">Amount</p>
                      <div class="dropdown ms-3">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa-solid fa-filter"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                            <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                              style="outline:none;">
                              <option value="" selected>Equal to</option>
                              <option value="">Less Than</option>
                              <option value="">Greater Than</option>

                            </select>
                          </li>
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Amount</p>
                            <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                          </li>
                          <div class="mt-2 ms-3">
                            <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                style="color: #71748E;">Clear</span></button>
                            <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                class="text-light">Apply</span></button>
                          </div>

                        </ul>
                      </div>
                    </th>

                    <th class="d-flex">
                      <p class="pt-1">Balance Due</p>
                      <div class="dropdown ms-3">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa-solid fa-filter"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                            <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                              style="outline:none;">
                              <option value="" selected>Equal to</option>
                              <option value="">Less Than</option>
                              <option value="">Greater Than</option>

                            </select>
                          </li>
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Balance Due</p>
                            <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                          </li>
                          <div class="mt-2 ms-3">
                            <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                style="color: #71748E;">Clear</span></button>
                            <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                class="text-light">Apply</span></button>
                          </div>

                        </ul>
                      </div>
                    </th>



                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                      No estimates yet. Click "New Estimate" to create one.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>


        <!-- expense category report -->
        <div id="tab-expense category report" class="report-tab-content d-none">
          <div class="d-flex flex-column"
            style="min-height: 100vh; padding: 24px; background-color: #ffffff; border: 1px solid #e5e7eb;">

            <!-- Filters & Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <!-- Left Side -->
              <div class="d-flex align-items-center" style="gap: 16px;">


                <div class="form-check mb-0 d-flex align-items-center" style="gap: 8px;">
                  <input class="form-check-input mt-0" type="checkbox" id="stockDateFilter"
                    style="border-color: #d1d5db; box-shadow: none;">
                  <label class="form-check-label mb-0" for="stockDateFilter"
                    style="color: #6b7280; font-size: 14px;">Date filter</label>
                </div>

                <div id="stockDateInput" class="d-flex align-items-center px-2 py-1"
                  style="border: 1px solid #d1d5db; border-radius: 4px; background-color: #ffffff;">
                  <span style="font-size: 12px; color: #9ca3af; margin-right: 8px;">Date</span>
                  <span style="font-size: 14px; color: #374151; margin-right: 8px; font-weight: 500;">28/03/2026</span>
                  <i class="fa-regular fa-calendar" style="color: #9ca3af; font-size: 14px;"></i>
                </div>


              </div>

              <!-- Right Side -->
              <div class="d-flex" style="gap: 8px;">
                <button id="stockExcelBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-file-excel" style="color: #10b981; font-size: 18px;"></i>
                </button>
                <button id="stockPrintBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-print" style="color: #4b5563; font-size: 18px;"></i>
                </button>
              </div>
            </div>

            <!-- Page Title -->
            <h2 style="font-weight: 700; color: #1f2937; margin: 24px 0 32px 0; font-size: 24px;">Expense category
              Report</h2>

            <!-- Data Table Architecture -->
            <div class="table-responsive">
              <table class="w-100" style="border-collapse: collapse;">
                <thead style="background-color: #f3f4f6;">
                  <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px 16px; width: 40px;"></th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: left; border-right: 1px solid #e5e7eb;">
                      Expense Category</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Category Type</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Amount</th>



                  </tr>

                  </tr>
                </thead>
                <tbody style="background-color: #ffffff;">
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">1</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      abc</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>

                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>


                  </tr>
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">2</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      def</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 200.00</td>

                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>


                  </tr>
                </tbody>
                <tfoot style="background-color: #ffffff;">
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Total Expense</td>
                    <td style="padding: 16px;"></td>


                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>


                </tfoot>
              </table>
            </div>

          </div>
        </div>


        <!-- expense item report  -->
        <div id="tab-expense item report" class="report-tab-content">

          <!-- Header -->
          <div class="d-flex justify-content-between align-items-center mb-4">
            <h3 class="fw-bold text-dark mb-0">Expenses item report</h3>
            <div class="d-flex gap-2">
              <button class="btn btn-danger rounded-pill px-4 fw-medium text-white"
                style="background-color: #ef4444; border: none;">
                <span class="fs-5 lh-1 me-1">+</span> Add Expense
              </button>
              <button class="btn bg-white border border-secondary-subtle text-secondary px-3 py-1 shadow-sm">
                <i class="fa-solid fa-gear"></i>
              </button>
            </div>
          </div>

          <!-- Filter Bar -->
          <div class="d-flex justify-content-between align-items-center bg-light mb-2 px-4 py-2 rounded">
            <div class="d-flex">
              <div class="d-flex justify-content-center align-items-center me-2">Filter By: </div>
              <div class="d-flex rounded-pill" style="background-color:#E4F2FF;">
                <div class="d-flex justify-content-center align-items-center text-center"
                  style="width: 9rem; height:40px; border-right: 1px solid rgb(45, 44, 44); font-size:12px;"><select
                    name="" id="" class="bg-transparent border-0" style="outline:none;">
                    <option value="">All Estimates</option>
                    <option value="" selected>This Month</option>
                    <option value="">Last Month</option>
                    <option value="">This Quarter</option>
                    <option value="">This Year</option>
                    <option value="">Custom</option>
                  </select></div>
                <div class="d-flex justify-content-center align-items-center" style="width: 16rem; height: 40px;">
                  01/03/2026
                  To 31/03/2026</div>

              </div>
              <div class="d-flex justify-content-center align-items-center rounded-pill ms-4"
                style="background-color:#E4F2FF; width: 8rem; height: 40px;"><select name="" id=""
                  class="bg-transparent border-0" style="outline:none;">
                  <option value="" selected>All Firms</option>
                  <option value=""><a href="">Firm 1</a></option>
                  <option value=""><a href="">Firm 2</a></option>
                  <option value=""><a href="">Firm 3</a></option>

                </select></div>

            </div>
          </div>



          <!-- Data Table -->
          <div class="card border-0 shadow-sm rounded-3 overflow-hidden bg-white">
            <div class="card-header bg-white border-bottom p-3 d-flex justify-content-between align-items-center">
              <h5 class="fw-bold mb-0 text-dark fs-5">Transactions</h5>
              <div class="text-secondary d-flex gap-2">
                <button class="btn btn-link text-secondary p-1 text-decoration-none" title="Search"><i
                    class="fa-solid fa-magnifying-glass"></i></button>
                <button class="btn btn-link text-secondary p-1 text-decoration-none" title="Chart"><i
                    class="fa-solid fa-chart-simple"></i></button>
                <button class="btn btn-link text-success p-1 text-decoration-none" title="Excel"><i
                    class="fa-regular fa-file-excel"></i></button>
                <button class="btn btn-link text-secondary p-1 text-decoration-none" title="Print"><i
                    class="fa-solid fa-print"></i></button>
              </div>
            </div>
            <div class="table-responsive">
              <table class="table table-hover mb-0 align-middle table-clean">
                <thead>
                  <tr class="d-flex gap-3">

                    <th class="d-flex">
                      <p class="pt-1">Expense item</p>
                      <div class="dropdown ms-3">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa-solid fa-filter"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                            <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                              style="outline:none;">
                              <option value="" selected>Contanis</option>
                              <option value="">Exact Match</option>

                            </select>
                          </li>
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Expense Item</p>
                            <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                          </li>
                          <div class="mt-2 ms-3">
                            <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                style="color: #71748E;">Clear</span></button>
                            <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                class="text-light">Apply</span></button>
                          </div>

                        </ul>
                      </div>
                    </th>
                    <th class="d-flex">
                      <p class="pt-1">Party</p>
                      <div class="dropdown ms-3">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa-solid fa-filter"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                            <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                              style="outline:none;">
                              <option value="" selected>Contanis</option>
                              <option value="">Exact Match</option>

                            </select>
                          </li>
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Party</p>
                            <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                          </li>
                          <div class="mt-2 ms-3">
                            <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                style="color: #71748E;">Clear</span></button>
                            <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                class="text-light">Apply</span></button>
                          </div>

                        </ul>
                      </div>
                    </th>
                    <th class="d-flex">
                      <p class="pt-1">Unit Price</p>
                      <div class="dropdown ms-3">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa-solid fa-filter"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                            <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                              style="outline:none;">
                              <option value="" selected>Equal to</option>
                              <option value="">Less Than</option>
                              <option value="">Greater Than</option>

                            </select>
                          </li>
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Category Name</p>
                            <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                          </li>
                          <div class="mt-2 ms-3">
                            <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                style="color: #71748E;">Clear</span></button>
                            <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                class="text-light">Apply</span></button>
                          </div>

                        </ul>
                      </div>
                    </th>



                    <th class="d-flex">
                      <p class="pt-1">Quantity</p>
                      <div class="dropdown ms-3">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa-solid fa-filter"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                            <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                              style="outline:none;">
                              <option value="" selected>Equal to</option>
                              <option value="">Less Than</option>
                              <option value="">Greater Than</option>

                            </select>
                          </li>
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Quantity</p>
                            <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                          </li>
                          <div class="mt-2 ms-3">
                            <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                style="color: #71748E;">Clear</span></button>
                            <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                class="text-light">Apply</span></button>
                          </div>

                        </ul>
                      </div>
                    </th>

                    <th class="d-flex">
                      <p class="pt-1">Amount</p>
                      <div class="dropdown ms-3">
                        <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown"
                          aria-expanded="false">
                          <i class="fa-solid fa-filter"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                            <select name="" id="" class="bg-transparent border py-2 rounded w-100"
                              style="outline:none;">
                              <option value="" selected>Equal to</option>
                              <option value="">Less Than</option>
                              <option value="">Greater Than</option>

                            </select>
                          </li>
                          <li class="dropdown-item">
                            <p class="mb-0" style="font-size: 11px;">Amount</p>
                            <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                          </li>
                          <div class="mt-2 ms-3">
                            <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                                style="color: #71748E;">Clear</span></button>
                            <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                                class="text-light">Apply</span></button>
                          </div>

                        </ul>
                      </div>
                    </th>



                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">
                      No estimates yet. Click "New Estimate" to create one.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- sale order-->
        <div id="tab-sale order" class="report-tab-content d-none">
          <div class="d-flex flex-column"
            style="min-height: 100vh; padding: 24px; background-color: #ffffff; border: 1px solid #e5e7eb;">

            <!-- Filters & Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <!-- Left Side -->
              <div class="d-flex align-items-center" style="gap: 16px;">


                <div class="form-check mb-0 d-flex align-items-center" style="gap: 8px;">
                  <input class="form-check-input mt-0" type="checkbox" id="stockDateFilter"
                    style="border-color: #d1d5db; box-shadow: none;">
                  <label class="form-check-label mb-0" for="stockDateFilter"
                    style="color: #6b7280; font-size: 14px;">Date filter</label>
                </div>

                <div id="stockDateInput" class="d-flex align-items-center px-2 py-1"
                  style="border: 1px solid #d1d5db; border-radius: 4px; background-color: #ffffff;">
                  <span style="font-size: 12px; color: #9ca3af; margin-right: 8px;">Date</span>
                  <span style="font-size: 14px; color: #374151; margin-right: 8px; font-weight: 500;">28/03/2026</span>
                  <i class="fa-regular fa-calendar" style="color: #9ca3af; font-size: 14px;"></i>
                </div>


              </div>

              <!-- Right Side -->
              <div class="d-flex" style="gap: 8px;">
                <button id="stockExcelBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-file-excel" style="color: #10b981; font-size: 18px;"></i>
                </button>
                <button id="stockPrintBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-print" style="color: #4b5563; font-size: 18px;"></i>
                </button>
              </div>
            </div>

            <!-- Page Title -->
            <h2 style="font-weight: 700; color: #1f2937; margin: 24px 0 32px 0; font-size: 24px;">Sale Order</h2>

            <!-- Data Table Architecture -->
            <div class="table-responsive">
              <table class="w-100" style="border-collapse: collapse;">
                <thead style="background-color: #f3f4f6;">
                  <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px 16px; width: 40px;"></th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: left; border-right: 1px solid #e5e7eb;">
                      Date</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Order No</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Name</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Due Date</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Status</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Type</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Total</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Advance</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Balance</th>



                  </tr>

                  </tr>
                </thead>
                <tbody style="background-color: #ffffff;">
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">1</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      abc</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>

                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>


                  </tr>
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">2</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      def</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 200.00</td>

                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>


                  </tr>
                </tbody>
                <tfoot style="background-color: #ffffff;">
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Total Amount</td>
                    <td style="padding: 16px;"></td>


                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>


                </tfoot>
              </table>
            </div>

          </div>
        </div>


        <!-- sale order item -->
        <div id="tab-sale order item" class="report-tab-content d-none">
          <div class="d-flex flex-column"
            style="min-height: 100vh; padding: 24px; background-color: #ffffff; border: 1px solid #e5e7eb;">

            <!-- Filters & Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <!-- Left Side -->
              <div class="d-flex align-items-center" style="gap: 16px;">


                <div class="form-check mb-0 d-flex align-items-center" style="gap: 8px;">
                  <input class="form-check-input mt-0" type="checkbox" id="stockDateFilter"
                    style="border-color: #d1d5db; box-shadow: none;">
                  <label class="form-check-label mb-0" for="stockDateFilter"
                    style="color: #6b7280; font-size: 14px;">Date filter</label>
                </div>

                <div id="stockDateInput" class="d-flex align-items-center px-2 py-1"
                  style="border: 1px solid #d1d5db; border-radius: 4px; background-color: #ffffff;">
                  <span style="font-size: 12px; color: #9ca3af; margin-right: 8px;">Date</span>
                  <span style="font-size: 14px; color: #374151; margin-right: 8px; font-weight: 500;">28/03/2026</span>
                  <i class="fa-regular fa-calendar" style="color: #9ca3af; font-size: 14px;"></i>
                </div>


              </div>

              <!-- Right Side -->
              <div class="d-flex" style="gap: 8px;">
                <button id="stockExcelBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-file-excel" style="color: #10b981; font-size: 18px;"></i>
                </button>
                <button id="stockPrintBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-print" style="color: #4b5563; font-size: 18px;"></i>
                </button>
              </div>
            </div>

            <!-- Page Title -->
            <h2 style="font-weight: 700; color: #1f2937; margin: 24px 0 32px 0; font-size: 24px;">Sale Order items</h2>

            <!-- Data Table Architecture -->
            <div class="table-responsive">
              <table class="w-100" style="border-collapse: collapse;">
                <thead style="background-color: #f3f4f6;">
                  <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px 16px; width: 40px;"></th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: left; border-right: 1px solid #e5e7eb;">
                      Item Name</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Quantity</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Amount</th>



                  </tr>

                  </tr>
                </thead>
                <tbody style="background-color: #ffffff;">
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">1</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      abc</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>

                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>


                  </tr>
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">2</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      def</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 200.00</td>

                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>


                  </tr>
                </tbody>

              </table>
            </div>

          </div>
        </div>


        <!-- Loan statement -->
        <div id="tab-loan statement" class="report-tab-content d-none">
          <div class="d-flex flex-column"
            style="min-height: 100vh; padding: 24px; background-color: #ffffff; border: 1px solid #e5e7eb;">

            <!-- Filters & Actions -->
            <div class="d-flex justify-content-between align-items-center mb-4">
              <!-- Left Side -->
              <div class="d-flex align-items-center" style="gap: 16px;">

                <div class="form-check mb-0 d-flex align-items-center" style="gap: 8px;">
                   <label class="form-check-label mb-0" for="stockDateFilter"
                    style="color: #6b7280; font-size: 14px;">Account</label>
                  <input class="mt-0" type="text"
                    style="border-color: #d1d5db; box-shadow: none;">

                </div>


                <div class="form-check mb-0 d-flex align-items-center" style="gap: 8px;">
                  <input class="form-check-input mt-0" type="checkbox" id="stockDateFilter"
                    style="border-color: #d1d5db; box-shadow: none;">
                  <label class="form-check-label mb-0" for="stockDateFilter"
                    style="color: #6b7280; font-size: 14px;">Date filter</label>
                </div>

                <div id="stockDateInput" class="d-flex align-items-center px-2 py-1"
                  style="border: 1px solid #d1d5db; border-radius: 4px; background-color: #ffffff;">
                  <span style="font-size: 12px; color: #9ca3af; margin-right: 8px;">Date</span>
                  <span style="font-size: 14px; color: #374151; margin-right: 8px; font-weight: 500;">28/03/2026</span>
                  <i class="fa-regular fa-calendar" style="color: #9ca3af; font-size: 14px;"></i>
                </div>


              </div>

              <!-- Right Side -->
              <div class="d-flex" style="gap: 8px;">
                <button id="stockExcelBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-file-excel" style="color: #10b981; font-size: 18px;"></i>
                </button>
                <button id="stockPrintBtn" class="btn d-flex align-items-center justify-content-center p-0"
                  style="width: 40px; height: 40px; border-radius: 50%; border: 1px solid #e5e7eb; background-color: #ffffff;">
                  <i class="fa-solid fa-print" style="color: #4b5563; font-size: 18px;"></i>
                </button>
              </div>
            </div>

            <!-- Page Title -->
            <h2 style="font-weight: 700; color: #1f2937; margin: 24px 0 32px 0; font-size: 24px;">Loan Statement</h2>

            <!-- Data Table Architecture -->
            <div class="table-responsive">
              <table class="w-100" style="border-collapse: collapse;">
                <thead style="background-color: #f3f4f6;">
                  <tr style="border-bottom: 2px solid #e5e7eb;">
                    <th style="padding: 12px 16px; width: 40px;"></th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: left; border-right: 1px solid #e5e7eb;">
                      Date</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Type</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Amount</th>
                    <th
                      style="padding: 12px 16px; font-size: 13px; font-weight: 600; color: #6b7280; text-align: right; border-right: 1px solid #e5e7eb;">
                      Ending Balance</th>



                  </tr>

                  </tr>
                </thead>
                <tbody style="background-color: #ffffff;">
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">1</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      abc</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>

                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 1,111.00</td>


                  </tr>
                  <tr class="stock-row" style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px; font-size: 14px; color: #9ca3af; text-align: left;">2</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: left; border-right: 1px solid #e5e7eb;">
                      def</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 200.00</td>

                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>
                    <td
                      style="padding: 16px; font-size: 14px; color: #1f2937; text-align: right; border-right: 1px solid #e5e7eb;">
                      Rs 180.00</td>


                  </tr>
                </tbody>
                <tfoot style="background-color: #ffffff;">
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Opening Balance</td>
                    <td style="padding: 16px;"></td>


                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Balance Due</td>
                    <td style="padding: 16px;"></td>


                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Total Principal Paid</td>
                    <td style="padding: 16px;"></td>


                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>
                  <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 16px;"></td>
                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: left;">
                      Total Principal due</td>
                    <td style="padding: 16px;"></td>


                    <td style="padding: 16px; font-size: 14px; color: #1f2937; font-weight: 700; text-align: right;">Rs
                      0.00</td>
                  </tr>


                </tfoot>
              </table>
            </div>

          </div>
        </div>

        <!-- Empty State Tab Content Template (Hidden by default) -->
        <div id="tab-Empty" class="report-tab-content d-none d-flex align-items-center justify-content-center"
          style="height: 60vh;">
          <div class="text-center bg-white p-5 rounded-3 border shadow-sm">
            <h4 class="text-secondary fw-normal empty-title">No view configured</h4>
            <p class="text-muted mt-2">Switch back to "Sale" to view the functional UI.</p>
          </div>
        </div>

      </div>
    </div>

  </main>

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/components.js') }}"></script>
  <script src="{{ asset('js/common.js') }}"></script>

  @include('dashboard.reports.partials._party-report-scripts')
   @include('dashboard.reports.partials._reports-scripts')
 <script>
document.addEventListener('DOMContentLoaded', function () {

    // Make item/stock tab links work independently
    document.querySelectorAll('[data-tab]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopImmediatePropagation(); // stops components.js from intercepting
            window.showTab(this.getAttribute('data-tab'));
        });
    });

});
</script>
</body>


</html>
