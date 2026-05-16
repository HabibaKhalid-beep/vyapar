<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vyapar — Cash In Hand</title>
  <meta name="description" content="Cash in Hand transactions with full filter, sort, print and history support.">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('../css/styles.css') }}" rel="stylesheet">

  <script>
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
  </script>

  <style>
    /* ── Global ── */
    body { background-color: #ffffff; color: #000000; }
    .main-content { padding: 0px !important; }

    /* ── Balance Header ── */
    .balance-header {
      background-color: #fff !important;
      border-bottom: 1px solid #e2e8f0;
      padding: 1rem 1.5rem !important;
    }
    .balance-title { font-size: 1.25rem; font-weight: 700; color: #1e293b; }
    .balance-amount { font-size: 1.1rem; font-weight: 600; color: #10b981 !important; margin-left: 0.75rem; }
    .btn-adjust-cash {
      background-color: #e11d48 !important;
      color: #fff !important;
      font-weight: 500;
      font-size: 0.85rem;
      padding: 0.4rem 1rem;
      border: none;
      border-radius: 20px !important;
    }

    /* ── Transactions bar ── */
    .transactions-bar { padding: 0.75rem 1.5rem !important; background-color: #fff; border-bottom: 1px solid #f3f4f6; }
    .transactions-title { font-weight: 600; color: #475569; font-size: 0.95rem; margin-bottom: 0; }

    /* ── Table Wrapper ── */
    .cash-tbl-wrap { overflow-y: auto; overflow-x: auto; position: relative; border: 1px solid #ebebeb; }
    .cash-tbl-wrap::-webkit-scrollbar { width: 4px; height: 4px; }
    .cash-tbl-wrap::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

    .cash-tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }

    /* ── Header Cells with borders ── */
    .cash-tbl th {
      padding: 8px 12px; font-size: 12px; font-weight: 500;
      text-transform: uppercase; color: #7a828e;
      background: #f4f5f7; border-bottom: 1px solid #ebebeb;
      border-right: 1px solid #ebebeb; /* Proper grid vertical line */
      text-align: left; white-space: nowrap;
      position: relative; overflow: visible; user-select: none;
    }
    .cash-tbl th:last-child { border-right: none; }

    .cash-tbl th .th-inner {
      display: flex; align-items: center; justify-content: space-between; width: 100%; cursor: pointer;
    }
    .th-sort-arrow {
      display: inline-flex; align-items: center;
      color: #4a4a4a; flex-shrink: 0; font-size: 10px; font-style: normal;
      opacity: 0; transition: opacity .1s; line-height: 1; margin-left: auto; margin-right: 6px;
    }
    .cash-tbl th.sort-asc  .th-sort-arrow,
    .cash-tbl th.sort-desc .th-sort-arrow { opacity: 1; }
    .th-sort-arrow::after               { content: '↑'; }
    .cash-tbl th.sort-desc .th-sort-arrow::after { content: '↓'; }
    
    .cash-tbl th .th-filter-icon {
      color: #a0aec0; flex-shrink: 0; cursor: pointer; transition: color .15s;
      font-size: 11px; padding: 2px;
    }
    .cash-tbl th .th-filter-icon:hover  { color: #718096; }
    .cash-tbl th .th-filter-icon.active { color: #e53e3e !important; }

    /* ── COLUMN RESIZE HANDLE ── */
    .col-resize-handle {
      position: absolute; right: 0; top: 0; bottom: 0;
      width: 5px; cursor: col-resize; z-index: 10;
    }
    .col-resize-handle:hover,
    .col-resize-handle.resizing { background: #2563eb; opacity: .4; }

    /* ── Table Cells (Updated to Show Clean Pro Grid Lines) ── */
    .cash-tbl td {
      padding: 12px 10px; font-size: 13px; color: #000000; font-weight: 400;
      border-bottom: 1px solid #ebebeb; /* Explicit row dividing lines */
      border-right: 1px solid #ebebeb;  /* Explicit vertical dividing lines */
      vertical-align: middle;
      overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
    }
    .cash-tbl td:last-child { border-right: none; } /* Clear out outer border on last column */

    .cash-tbl td.td-price, .cash-tbl th.th-price-right { text-align: right; }
    .cash-tbl td.td-price { color: #000000; font-weight: 500; }
    .cash-tbl td.td-actions { padding: 2px 4px; width: 36px; text-align: center; }
    .cash-tbl tbody tr:hover td { background: #f0f9ff; }

    /* Pure Simple Text Type Tag Style */
    .type-label {
      font-size: 13px; font-weight: 400; color: #000000;
      text-transform: capitalize; display: inline-block;
    }

    /* Row action menu */
    .il-row-menu-wrap { position: relative; }
    .il-row-menu-btn {
      background: none; border: none; cursor: pointer; color: #9ca3af;
      padding: 4px 6px; border-radius: 4px; font-size: 18px; line-height: 1;
    }
    .il-row-menu-btn:hover { color: #374151; background: #f3f4f6; }
    .il-row-menu {
      position: fixed;
      background: #fff; border: 1px solid #e5e7eb;
      border-radius: 8px; box-shadow: 0 6px 24px rgba(0,0,0,.13);
      z-index: 9000; min-width: 160px; display: none;
      padding: 4px 0;
    }
    .il-row-menu.open { display: block; }
    .il-row-menu-item {
      padding: 10px 16px; cursor: pointer; font-size: 13px; color: #374151;
      display: flex; align-items: center; gap: 8px;
    }
    .il-row-menu-item:hover { background: #f9fafb; }
    .il-row-menu-item.danger { color: #ef4444; }
    .il-row-menu-item.danger:hover { background: #fef2f2; }
    .il-row-menu-item i { font-size: 13px; width: 16px; }

    /* Blue Excel icon button */
    .il-export-btn {
      background: none; border: none; cursor: pointer; padding: 2px;
      display: flex; align-items: center; justify-content: center;
    }
    .il-export-btn .excel-icon {
      width: 26px; height: 26px; background: #1d6fcc; border-radius: 4px;
      display: flex; align-items: center; justify-content: center;
      color: #fff; font-size: 13px; font-weight: 800; letter-spacing: -1px;
      font-family: Arial, sans-serif;
    }

    /* Search input */
    .txn-search-input {
      border: 1px solid #e5e7eb; border-radius: 6px;
      padding: 7px 10px 7px 34px; font-size: 13px; outline: none; width: 240px; color: #374151;
      background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' viewBox='0 0 24 24' stroke='%23b0b8c4' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath stroke-linecap='round' d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat 11px center;
    }
    .txn-search-input::placeholder { color: #b0b8c4; }
    .txn-search-input:focus { border-color: #2563eb; outline: none; }

    /* ══ COLUMN FILTER DROPDOWNS ══ */
    .col-filter-dd {
      display: none; position: absolute;
      background: #fff; border: 1px solid #cbd5e1;
      border-radius: 6px; box-shadow: 0 4px 12px rgba(0,0,0,.15);
      z-index: 9999; min-width: 240px; padding: 14px;
      text-transform: none; font-weight: normal;
    }
    .col-filter-dd.open { display: block; }
    .cfd-title { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 12px; }
    .cfd-cb-row {
      display: flex; align-items: center; gap: 10px;
      padding: 7px 2px; font-size: 13px; color: #374151; cursor: pointer;
    }
    .cfd-cb-row input[type=checkbox] { width: 15px; height: 15px; accent-color: #2563eb; flex-shrink: 0; }
    .cfd-select {
      width: 100%; border: 1.5px solid #e5e7eb; border-radius: 6px;
      padding: 9px 10px; font-size: 13px; color: #374151;
      background: #fff; outline: none; cursor: pointer; margin-bottom: 10px;
      appearance: none;
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
      background-repeat: no-repeat; background-position: right 10px center; padding-right: 28px;
    }
    .cfd-input {
      width: 100%; border: 1.5px solid #e5e7eb; border-radius: 6px;
      padding: 9px 10px; font-size: 13px; color: #374151;
      outline: none; box-sizing: border-box;
    }
    .cfd-input:focus { border-color: #2563eb; }
    .cfd-input::placeholder { color: #9ca3af; }
    .cfd-date-lbl { font-size: 11px; color: #9ca3af; margin-bottom: 6px; }
    .cfd-actions { display: flex; gap: 8px; margin-top: 14px; }
    .cfd-clear {
      flex: 1; border: 1.5px solid #e5e7eb; background: #fff;
      border-radius: 20px; padding: 8px 0; font-size: 12px;
      color: #6b7280; cursor: pointer; font-weight: 500;
    }
    .cfd-apply {
      flex: 1; border: none; background: #e53e3e;
      border-radius: 20px; padding: 8px 0; font-size: 12px;
      color: #fff; cursor: pointer; font-weight: 600;
    }
    .cfd-clear:hover { background: #f3f4f6; }
    .cfd-apply:hover { background: #c53030; }

    /* ══ VIEW HISTORY MODAL ══ */
    .history-modal-overlay {
      display: none; position: fixed; inset: 0;
      background: rgba(0,0,0,.45); z-index: 10000;
      align-items: center; justify-content: center;
    }
    .history-modal-overlay.open { display: flex; }
    .history-modal {
      background: #fff; border-radius: 12px;
      width: 90%; max-width: 560px; max-height: 80vh;
      display: flex; flex-direction: column;
      box-shadow: 0 20px 60px rgba(0,0,0,.2);
    }
    .history-modal-header {
      display: flex; align-items: center; justify-content: space-between;
      padding: 18px 20px; border-bottom: 1px solid #f3f4f6;
    }
    .history-modal-title { font-size: 16px; font-weight: 700; color: #1e293b; }
    .history-modal-close {
      background: none; border: none; cursor: pointer;
      font-size: 20px; color: #9ca3af; line-height: 1;
      padding: 0 4px;
    }
    .history-modal-close:hover { color: #374151; }
    .history-modal-body {
      padding: 20px; overflow-y: auto; flex: 1;
    }
    .history-empty {
      display: flex; flex-direction: column; align-items: center;
      justify-content: center; padding: 40px 20px; color: #9ca3af;
    }
    .history-empty svg { margin-bottom: 16px; opacity: .35; }
    .history-empty p { font-size: 14px; }
    .history-item {
      display: flex; gap: 12px; padding: 14px 0;
      border-bottom: 1px solid #f3f4f6;
    }
    .history-item:last-child { border-bottom: none; }
    .history-dot {
      width: 10px; height: 10px; border-radius: 50%;
      background: #2563eb; flex-shrink: 0; margin-top: 4px;
    }
    .history-action { font-size: 13px; font-weight: 600; color: #374151; }
    .history-meta { font-size: 12px; color: #9ca3af; margin-top: 2px; }
    .history-amount { font-size: 13px; font-weight: 700; color: #000000; }

    /* ══ PRINT STYLES ══ */
    @media print {
      body * { visibility: hidden; }
      #print-area, #print-area * { visibility: visible; }
      #print-area { position: fixed; left: 0; top: 0; width: 100%; }
    }

    /* Empty state */
    .empty-state {
      text-align: center; padding: 60px 20px; color: #9ca3af;
    }
    .empty-state i { font-size: 48px; margin-bottom: 12px; display: block; }
    .empty-state p { font-size: 14px; }
  </style>
</head>

<body data-page="cash-in-hand">

  <main class="main-content" id="mainContent">

    <div class="d-flex justify-content-between align-items-center balance-header">
      <div class="d-flex align-items-center">
        <span class="balance-title border-end border-2 pe-3">Cash In Hand</span>
        <span class="balance-amount">Rs {{ number_format($cashAccount->opening_balance ?? 500, 0) }}</span>
      </div>

      <button type="button" class="btn btn-adjust-cash" data-bs-toggle="modal" data-bs-target="#adjustCashModal">
        <i class="fas fa-sliders-h me-1"></i> Adjust Cash
      </button>
    </div>

    <div class="modal fade" id="adjustCashModal" tabindex="-1" aria-labelledby="adjustCashModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="adjustCashModalLabel">Adjust Cash</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="col-12 mb-3">
              <div class="d-flex gap-4">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="adjust" id="cash_add" value="add" checked onchange="previewAdjustedCash()">
                  <label class="form-check-label" for="cash_add">Add Cash</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="adjust" id="cash_reduce" value="reduce" onchange="previewAdjustedCash()">
                  <label class="form-check-label" for="cash_reduce">Reduce Cash</label>
                </div>
              </div>
            </div>
            <div class="mb-3">
              <label for="adj_amount" class="form-label">Enter Amount <span class="text-danger">*</span></label>
              <input type="number" class="form-control" id="adj_amount" min="0" step="1" oninput="previewAdjustedCash()" placeholder="0">
              <div class="mt-2 small text-secondary">
                Updated Cash: <strong id="adj_preview">Rs {{ number_format($cashAccount->opening_balance ?? 500, 0) }}</strong>
              </div>
            </div>
            <div class="mb-3">
              <label for="adj_date" class="form-label">Adjustment Date</label>
              <input type="date" class="form-control" id="adj_date" value="{{ date('Y-m-d') }}">
            </div>
            <div class="mb-3">
              <label for="adj_desc" class="form-label">Description</label>
              <input type="text" class="form-control" id="adj_desc" placeholder="Optional note">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger rounded-pill" onclick="saveAdjustCash()">Save</button>
          </div>
        </div>
      </div>
    </div>

    <div class="d-flex justify-content-between align-items-center transactions-bar">
      <p class="transactions-title">Transactions</p>
      <div class="d-flex align-items-center gap-2">
        <input
          type="text"
          class="txn-search-input"
          id="cashSearchInput"
          placeholder="Search transactions..."
          oninput="applyAllCashFilters()"
        />
        <button class="il-export-btn" title="Export to Excel" onclick="exportCashToExcel()">
          <div class="excel-icon">X</div>
        </button>
      </div>
    </div>

    <div class="cash-tbl-wrap">
      <table class="cash-tbl" id="cash-table">
        <colgroup>
          <col style="width: 140px;">
          <col style="width: 200px;">
          <col style="width: 130px;">
          <col style="width: 150px;">
          <col style="width: 36px;">
        </colgroup>
        <thead>
          <tr id="cash-thead-row">

            <th data-col="type">
              <span class="th-inner" onclick="sortCashCol('type')">
                TYPE <i class="th-sort-arrow"></i>
                <i class="fa-solid fa-filter th-filter-icon" id="fi-type" onclick="toggleCashColFilter(event,'ccf-type')"></i>
              </span>
              <div class="col-resize-handle"></div>
              
              <div class="col-filter-dd" id="ccf-type" onclick="event.stopPropagation()">
                <div class="cfd-title">Filter by Type</div>
                <label class="cfd-cb-row"><input type="checkbox" value="sale"     onchange="syncCashFilterIcons()"> Sale</label>
                <label class="cfd-cb-row"><input type="checkbox" value="purchase" onchange="syncCashFilterIcons()"> Purchase</label>
                <label class="cfd-cb-row"><input type="checkbox" value="payment"  onchange="syncCashFilterIcons()"> Payment</label>
                <label class="cfd-cb-row"><input type="checkbox" value="adjust"   onchange="syncCashFilterIcons()"> Adjust</label>
                <label class="cfd-cb-row"><input type="checkbox" value="expense"  onchange="syncCashFilterIcons()"> Expense</label>
                <div class="cfd-actions">
                  <button class="cfd-clear" onclick="clearCashColFilter('ccf-type')">Clear</button>
                  <button class="cfd-apply" onclick="applyAllCashFilters(); closeAllCashColFilters()">Apply</button>
                </div>
              </div>
            </th>

            <th data-col="name">
              <span class="th-inner" onclick="sortCashCol('name')">
                NAME <i class="th-sort-arrow"></i>
                <i class="fa-solid fa-filter th-filter-icon" id="fi-name" onclick="toggleCashColFilter(event,'ccf-name')"></i>
              </span>
              <div class="col-resize-handle"></div>

              <div class="col-filter-dd" id="ccf-name" onclick="event.stopPropagation()">
                <div class="cfd-title">Filter by Name</div>
                <select class="cfd-select" id="ccf-name-op">
                  <option value="contains">Contains</option>
                  <option value="exact">Exact match</option>
                  <option value="starts">Starts with</option>
                </select>
                <input type="text" class="cfd-input" id="ccf-name-val" placeholder="Name" oninput="syncCashFilterIcons()"/>
                <div class="cfd-actions">
                  <button class="cfd-clear" onclick="clearCashColFilter('ccf-name')">Clear</button>
                  <button class="cfd-apply" onclick="applyAllCashFilters(); closeAllCashColFilters()">Apply</button>
                </div>
              </div>
            </th>

            <th data-col="date">
              <span class="th-inner" onclick="sortCashCol('date')">
                DATE <i class="th-sort-arrow"></i>
                <i class="fa-solid fa-filter th-filter-icon" id="fi-date" onclick="toggleCashColFilter(event,'ccf-date')"></i>
              </span>
              <div class="col-resize-handle"></div>

              <div class="col-filter-dd" id="ccf-date" onclick="event.stopPropagation()">
                <div class="cfd-title">Filter by Date</div>
                <select class="cfd-select" id="ccf-date-op">
                  <option value="equal">Equal To</option>
                  <option value="before">Before</option>
                  <option value="after">After</option>
                  <option value="range">Date Range</option>
                </select>
                <div class="cfd-date-lbl">Select Date</div>
                <input type="date" class="cfd-input" id="ccf-date-val" oninput="syncCashFilterIcons(); toggleDateRange()"/>
                <div id="ccf-date-range-wrap" style="display:none; margin-top:8px;">
                  <div class="cfd-date-lbl">To Date</div>
                  <input type="date" class="cfd-input" id="ccf-date-val2" oninput="syncCashFilterIcons()"/>
                </div>
                <div class="cfd-actions">
                  <button class="cfd-clear" onclick="clearCashColFilter('ccf-date')">Clear</button>
                  <button class="cfd-apply" onclick="applyAllCashFilters(); closeAllCashColFilters()">Apply</button>
                </div>
              </div>
            </th>

            <th data-col="amount" class="th-price-right">
              <span class="th-inner" onclick="sortCashCol('amount')">
                AMOUNT <i class="th-sort-arrow"></i>
                <i class="fa-solid fa-filter th-filter-icon" id="fi-amount" onclick="toggleCashColFilter(event,'ccf-amount')"></i>
              </span>
              <div class="col-resize-handle"></div>

              <div class="col-filter-dd" id="ccf-amount" onclick="event.stopPropagation()">
                <div class="cfd-title">Filter by Amount</div>
                <select class="cfd-select" id="ccf-amount-op">
                  <option value="equal">Equal to</option>
                  <option value="lt">Less Than</option>
                  <option value="gt">Greater Than</option>
                  <option value="between">Between</option>
                </select>
                <input type="number" class="cfd-input" id="ccf-amount-val" placeholder="Amount" step="1" oninput="syncCashFilterIcons(); toggleAmountRange()"/>
                <div id="ccf-amount-range-wrap" style="display:none; margin-top:8px;">
                  <input type="number" class="cfd-input" id="ccf-amount-val2" placeholder="Max Amount" step="1" oninput="syncCashFilterIcons()"/>
                </div>
                <div class="cfd-actions">
                  <button class="cfd-clear" onclick="clearCashColFilter('ccf-amount')">Clear</button>
                  <button class="cfd-apply" onclick="applyAllCashFilters(); closeAllCashColFilters()">Apply</button>
                </div>
              </div>
            </th>

            <th data-col="actions"></th>

          </tr>
        </thead>
        <tbody id="cash-tbody">
          @php
            $allRows = collect();

            if (isset($cashAccount) && $cashAccount->id) {
                $bankTxns = \App\Models\BankTransaction::where('from_bank_account_id', $cashAccount->id)
                    ->orWhere('to_bank_account_id', $cashAccount->id)
                    ->orderByDesc('transaction_date')
                    ->orderByDesc('id')
                    ->get()
                    ->map(function ($bt) use ($cashAccount) {
                        $typeRaw = $bt->type ?? 'other';
                        $label   = match(true) {
                            str_contains($typeRaw, 'sale')        => 'Sale',
                            str_contains($typeRaw, 'purchase')    => 'Purchase',
                            str_contains($typeRaw, 'payment')     => 'Payment',
                            str_contains($typeRaw, 'expense')     => 'Expense',
                            str_contains($typeRaw, 'adjust')      => 'Adjust',
                            str_contains($typeRaw, 'cash_in')     => 'Cash In',
                            str_contains($typeRaw, 'cash_out')    => 'Cash Out',
                            default                               => ucwords(str_replace('_', ' ', $typeRaw)),
                        };
                        $isIn = $bt->to_bank_account_id === $cashAccount->id;
                        return [
                            'id'          => 'bt-' . $bt->id,
                            'ref_id'      => $bt->reference_id,
                            'ref_type'    => $bt->reference_type,
                            'type'        => $label,
                            'type_raw'    => $typeRaw,
                            'name'        => $bt->description ?? '—',
                            'date'        => $bt->transaction_date ? \Carbon\Carbon::parse($bt->transaction_date)->format('d/m/Y') : '—',
                            'date_sort'   => $bt->transaction_date ?? $bt->created_at,
                            'amount'      => $bt->amount ?? 0,
                            'direction'   => $isIn ? 'in' : 'out',
                        ];
                    });
                $allRows = $allRows->merge($bankTxns);
            }

            if (isset($cashTransactions) && $cashTransactions->isNotEmpty()) {
                $existing = $allRows->pluck('ref_id')->filter()->values();
                $extra = $cashTransactions
                    ->filter(fn($t) => !$existing->contains($t->id))
                    ->map(function ($t) {
                        $typeRaw = $t->type ?? 'other';
                        $label   = ucwords(str_replace('_', ' ', $typeRaw));
                        return [
                            'id'        => 'tx-' . $t->id,
                            'ref_id'    => $t->id,
                            'ref_type'  => $typeRaw,
                            'type'      => $label,
                            'type_raw'  => $typeRaw,
                            'name'      => $t->description ?? '—',
                            'date'      => \Carbon\Carbon::parse($t->transaction_date ?? $t->created_at)->format('d/m/Y'),
                            'date_sort' => $t->transaction_date ?? $t->created_at,
                            'amount'    => $t->amount ?? 0,
                            'direction' => 'in',
                        ];
                    });
                $allRows = $allRows->merge($extra);
            }

            $allRows = $allRows->sortByDesc('date_sort')->values();
          @endphp

          @forelse($allRows as $row)
            <tr
              data-type="{{ strtolower($row['type']) }}"
              data-name="{{ strtolower($row['name']) }}"
              data-date="{{ $row['date'] }}"
              data-amount="{{ $row['amount'] }}"
              data-id="{{ $row['id'] }}"
              data-ref-id="{{ $row['ref_id'] ?? '' }}"
              data-ref-type="{{ $row['ref_type'] ?? '' }}"
            >
              <td>
                <span class="type-label">
                  {{ $row['type'] }}
                </span>
              </td>
              <td title="{{ $row['name'] }}">{{ $row['name'] }}</td>
              <td>{{ $row['date'] }}</td>
              <td class="td-price">Rs {{ number_format($row['amount'], 0) }}</td>
              <td class="td-actions">
                <div class="il-row-menu-wrap">
                  <button
                    class="il-row-menu-btn"
                    onclick="toggleCashRowMenu(event,'cash-menu-{{ $loop->index }}')"
                    aria-label="Row actions"
                  >⋮</button>
                  <div class="il-row-menu" id="cash-menu-{{ $loop->index }}">
                    <div class="il-row-menu-item"
                         onclick="viewEditRow('{{ $row['ref_id'] ?? '' }}','{{ $row['ref_type'] ?? '' }}')">
                      <i class="fa-regular fa-pen-to-square"></i> View/Edit
                    </div>
                    <div class="il-row-menu-item danger"
                         onclick="deleteRow('{{ $row['ref_id'] ?? '' }}','{{ $row['ref_type'] ?? '' }}')">
                      <i class="fa-regular fa-trash-can"></i> Delete
                    </div>
                    <div class="il-row-menu-item"
                         onclick="printRow('{{ $row['ref_id'] ?? '' }}','{{ $row['ref_type'] ?? '' }}','{{ $row['type'] }}','{{ $row['name'] }}','{{ $row['date'] }}','{{ $row['amount'] }}')">
                      <i class="fa-solid fa-print"></i> Print
                    </div>
                    <div class="il-row-menu-item"
                         onclick="viewHistory('{{ $row['ref_id'] ?? '' }}','{{ $row['ref_type'] ?? '' }}','{{ $row['type'] }}')">
                      <i class="fa-solid fa-clock-rotate-left"></i> View History
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @empty
            <tr
              data-type="sale"
              data-name="ateeq"
              data-date="15/05/2026"
              data-amount="500"
              data-id="demo-1"
              data-ref-id="1"
              data-ref-type="sale"
            >
              <td><span class="type-label">Sale</span></td>
              <td>Ateeq</td>
              <td>15/05/2026</td>
              <td class="td-price">Rs 500</td>
              <td class="td-actions">
                <div class="il-row-menu-wrap">
                  <button class="il-row-menu-btn" onclick="toggleCashRowMenu(event,'cash-menu-0')">⋮</button>
                  <div class="il-row-menu" id="cash-menu-0">
                    <div class="il-row-menu-item" onclick="viewEditRow('1','sale')">
                      <i class="fa-regular fa-pen-to-square"></i> View/Edit
                    </div>
                    <div class="il-row-menu-item danger" onclick="deleteRow('1','sale')">
                      <i class="fa-regular fa-trash-can"></i> Delete
                    </div>
                    <div class="il-row-menu-item" onclick="printRow('1','sale','Sale','Ateeq','15/05/2026','500')">
                      <i class="fa-solid fa-print"></i> Print
                    </div>
                    <div class="il-row-menu-item" onclick="viewHistory('1','sale','Sale')">
                      <i class="fa-solid fa-clock-rotate-left"></i> View History
                    </div>
                  </div>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>

      <div id="cash-empty-state" class="empty-state" style="display:none;">
        <i class="fa-solid fa-inbox"></i>
        <p>No transactions match your filters.</p>
      </div>
    </div>

  </main>

  <div class="history-modal-overlay" id="historyModalOverlay" onclick="closeHistoryModal()">
    <div class="history-modal" onclick="event.stopPropagation()">
      <div class="history-modal-header">
        <span class="history-modal-title" id="historyModalTitle">Edit History</span>
        <button class="history-modal-close" onclick="closeHistoryModal()">×</button>
      </div>
      <div class="history-modal-body" id="historyModalBody"></div>
    </div>
  </div>

  <div id="print-area" style="display:none;"></div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
  <script src="{{ asset('../js/components.js') }}"></script>
  <script src="{{ asset('../js/common.js') }}"></script>

  <script>
    /* ════════════════════════════════════════
        COLUMN RESIZE ENGINE
       ════════════════════════════════════════ */
    (function () {
      let isResizing = false, startX = 0, startW = 0, th = null, handle = null;
      document.addEventListener('mousedown', function (e) {
        if (!e.target.classList.contains('col-resize-handle')) return;
        e.preventDefault();
        handle = e.target; th = handle.closest('th'); isResizing = true; startX = e.clientX;
        startW = th.getBoundingClientRect().width;
        var table = th.closest('table');
        var cols = table.querySelectorAll('colgroup col');
        var ths  = table.querySelectorAll('thead th');
        cols.forEach(function (col, i) {
          var w = ths[i] ? ths[i].getBoundingClientRect().width : 0;
          col.style.width = w + 'px';
        });
        table.style.tableLayout = 'fixed'; table.style.width = 'auto'; table.style.minWidth = '100%';
        handle.classList.add('resizing');
        document.body.style.cursor = 'col-resize'; document.body.style.userSelect = 'none';
      });
      document.addEventListener('mousemove', function (e) {
        if (!isResizing || !th) return;
        var newW = Math.max(60, startW + (e.clientX - startX));
        var table2 = th.closest('table');
        var ths2   = Array.from(table2.querySelectorAll('thead th'));
        var idx    = ths2.indexOf(th);
        var col    = table2.querySelectorAll('colgroup col')[idx];
        if (col) col.style.width = newW + 'px';
        th.style.width = newW + 'px'; th.style.minWidth = newW + 'px';
      });
      document.addEventListener('mouseup', function () {
        if (!isResizing) return; isResizing = false;
        if (handle) handle.classList.remove('resizing');
        document.body.style.cursor = ''; document.body.style.userSelect = '';
        handle = null; th = null;
      });
    })();

    /* ════════════════════════════════════════
        SORT
       ════════════════════════════════════════ */
    let cashSortCol = null, cashSortAsc = true;
    function sortCashCol(col) {
      if (cashSortCol === col) { cashSortAsc = !cashSortAsc; } else { cashSortCol = col; cashSortAsc = true; }
      document.querySelectorAll('#cash-thead-row th').forEach(function (th) { th.classList.remove('sort-asc', 'sort-desc'); });
      var th = document.querySelector('#cash-thead-row th[data-col="' + col + '"]');
      if (th) th.classList.add(cashSortAsc ? 'sort-asc' : 'sort-desc');

      var tbody = document.getElementById('cash-tbody');
      var rows = Array.from(tbody.querySelectorAll('tr'));
      rows.sort(function (a, b) {
        var av = a.dataset[col] || ''; var bv = b.dataset[col] || '';
        if (col === 'amount') return cashSortAsc ? parseFloat(av) - parseFloat(bv) : parseFloat(bv) - parseFloat(av);
        if (col === 'date') {
          var ap = av.split('/'); var bp = bv.split('/');
          var ad = ap.length === 3 ? new Date(ap[2], ap[1]-1, ap[0]) : new Date(av);
          var bd = bp.length === 3 ? new Date(bp[2], bp[1]-1, bp[0]) : new Date(bv);
          return cashSortAsc ? ad - bd : bd - ad;
        }
        return cashSortAsc ? av.localeCompare(bv) : bv.localeCompare(av);
      });
      rows.forEach(function (r) { tbody.appendChild(r); });
    }

    /* ════════════════════════════════════════
        ROW ACTION MENU
       ════════════════════════════════════════ */
    function toggleCashRowMenu(e, id) {
      e.stopPropagation();
      var btn = e.currentTarget; var rect = btn.getBoundingClientRect();
      document.querySelectorAll('.il-row-menu.open').forEach(function (m) { if (m.id !== id) m.classList.remove('open'); });
      var menu = document.getElementById(id);
      var isOpen = menu.classList.contains('open');
      menu.classList.remove('open');
      if (!isOpen) {
        menu.style.top  = (rect.bottom + window.scrollY + 2) + 'px';
        menu.style.left = rect.left + 'px';
        menu.classList.add('open');
        requestAnimationFrame(function () {
          var mRect = menu.getBoundingClientRect();
          menu.style.left = (rect.right - mRect.width) + 'px';
          if (parseFloat(menu.style.left) < 0) menu.style.left = '4px';
        });
      }
    }

    /* ════════════════════════════════════════
        COMBINED FILTER ENGINE
       ════════════════════════════════════════ */
    function parseDate(str) {
      var p = str.split('/');
      if (p.length === 3) return new Date(p[2], p[1]-1, p[0]);
      return new Date(str);
    }

    function applyAllCashFilters() {
      var q = (document.getElementById('cashSearchInput').value || '').toLowerCase().trim();
      var typeChecked = Array.from(document.querySelectorAll('#ccf-type input:checked')).map(function(c){ return c.value.toLowerCase().trim(); });
      var nameOp  = document.getElementById('ccf-name-op') ? document.getElementById('ccf-name-op').value : 'contains';
      var nameVal = document.getElementById('ccf-name-val') ? document.getElementById('ccf-name-val').value.toLowerCase().trim() : '';
      var dateOp   = document.getElementById('ccf-date-op') ? document.getElementById('ccf-date-op').value : 'equal';
      var dateVal  = document.getElementById('ccf-date-val') ? document.getElementById('ccf-date-val').value : '';
      var dateVal2 = document.getElementById('ccf-date-val2') ? document.getElementById('ccf-date-val2').value : '';
      var amtOp   = document.getElementById('ccf-amount-op') ? document.getElementById('ccf-amount-op').value : 'equal';
      var amtVal  = document.getElementById('ccf-amount-val') ? parseFloat(document.getElementById('ccf-amount-val').value) : NaN;
      var amtVal2 = document.getElementById('ccf-amount-val2') ? parseFloat(document.getElementById('ccf-amount-val2').value) : NaN;

      var rows = document.querySelectorAll('#cash-tbody tr');
      var visibleCount = 0;

      rows.forEach(function(row) {
        var rowType = (row.dataset.type || '').toLowerCase().trim();
        var rowName = (row.dataset.name || '').toLowerCase().trim();
        var rowDate = row.dataset.date || '';
        var rowAmount = parseFloat(row.dataset.amount || '0');
        var show = true;

        if (q && !row.textContent.toLowerCase().includes(q)) show = false;
        if (show && typeChecked.length > 0) {
          if (!typeChecked.some(function(tc){ return rowType.includes(tc); })) show = false;
        }
        if (show && nameVal) {
          if (nameOp === 'contains' && !rowName.includes(nameVal)) show = false;
          else if (nameOp === 'exact' && rowName !== nameVal) show = false;
          else if (nameOp === 'starts' && !rowName.startsWith(nameVal)) show = false;
        }
        if (show && dateVal) {
          var rd = parseDate(rowDate); var fd = new Date(dateVal);
          fd.setHours(0,0,0,0); rd.setHours(0,0,0,0);
          if (dateOp === 'equal' && rd.toDateString() !== fd.toDateString()) show = false;
          else if (dateOp === 'before' && rd >= fd) show = false;
          else if (dateOp === 'after' && rd <= fd) show = false;
          else if (dateOp === 'range' && dateVal2) {
            var fd2 = new Date(dateVal2); fd2.setHours(0,0,0,0);
            if (rd < fd || rd > fd2) show = false;
          }
        }
        if (show && !isNaN(amtVal)) {
          if (amtOp === 'equal' && rowAmount !== amtVal) show = false;
          else if (amtOp === 'lt' && rowAmount >= amtVal) show = false;
          else if (amtOp === 'gt' && rowAmount <= amtVal) show = false;
          else if (amtOp === 'between' && !isNaN(amtVal2)) {
            if (rowAmount < amtVal || rowAmount > amtVal2) show = false;
          }
        }
        row.style.display = show ? '' : 'none';
        if (show) visibleCount++;
      });
      document.getElementById('cash-empty-state').style.display = (visibleCount === 0 && rows.length > 0) ? 'block' : 'none';
      syncCashFilterIcons();
    }

    /* ════════════════════════════════════════
        DROPDOWN UI CONTROLS
       ════════════════════════════════════════ */
    function toggleCashColFilter(e, id) {
      e.stopPropagation();
      var icon = e.currentTarget; var th = icon.closest('th'); var dd = document.getElementById(id);
      var wasOpen = dd.classList.contains('open');
      closeAllCashColFilters();
      if (!wasOpen) {
        th.appendChild(dd); dd.style.top = th.offsetHeight + 'px'; dd.style.right = '0px'; dd.style.left = 'auto'; dd.classList.add('open');
        requestAnimationFrame(function () {
          var r = dd.getBoundingClientRect();
          if (r.left < 0) { dd.style.left = '0px'; dd.style.right = 'auto'; }
        });
      }
    }

    function closeAllCashColFilters() { document.querySelectorAll('.col-filter-dd.open').forEach(function (d) { d.classList.remove('open'); }); }
    function clearCashColFilter(id) {
      var dd = document.getElementById(id);
      dd.querySelectorAll('input[type=checkbox]').forEach(function (c) { c.checked = false; });
      dd.querySelectorAll('input[type=text], input[type=number], input[type=date]').forEach(function (i) { i.value = ''; });
      dd.querySelectorAll('select').forEach(function (s) { s.selectedIndex = 0; });
      if(document.getElementById('ccf-date-range-wrap')) document.getElementById('ccf-date-range-wrap').style.display = 'none';
      if(document.getElementById('ccf-amount-range-wrap')) document.getElementById('ccf-amount-range-wrap').style.display = 'none';
      applyAllCashFilters();
    }

    function syncCashFilterIcons() {
      var checks = {
        'type':   function () { return document.querySelectorAll('#ccf-type input:checked').length > 0; },
        'name':   function () { return !!(document.getElementById('ccf-name-val') && document.getElementById('ccf-name-val').value.trim() !== ''); },
        'date':   function () { return !!(document.getElementById('ccf-date-val') && document.getElementById('ccf-date-val').value !== ''); },
        'amount': function () { return !!(document.getElementById('ccf-amount-val') && document.getElementById('ccf-amount-val').value !== ''); },
      };
      Object.entries(checks).forEach(function (entry) {
        var icon = document.getElementById('fi-' + entry[0]);
        if (icon) icon.classList.toggle('active', entry[1]());
      });
    }

    function toggleDateRange() { var op = document.getElementById('ccf-date-op').value; document.getElementById('ccf-date-range-wrap').style.display = (op === 'range') ? 'block' : 'none'; }
    function toggleAmountRange() { var op = document.getElementById('ccf-amount-op').value; document.getElementById('ccf-amount-range-wrap').style.display = (op === 'between') ? 'block' : 'none'; }
    document.getElementById('ccf-date-op') && document.getElementById('ccf-date-op').addEventListener('change', toggleDateRange);
    document.getElementById('ccf-amount-op') && document.getElementById('ccf-amount-op').addEventListener('change', toggleAmountRange);

    /* ── Balance adjustment actions ── */
    var currentCash = {{ $cashAccount->opening_balance ?? 500 }};
    function previewAdjustedCash() {
      var amt = parseFloat(document.getElementById('adj_amount').value) || 0;
      var isAdd = document.getElementById('cash_add').checked;
      var updated = isAdd ? currentCash + amt : currentCash - amt;
      document.getElementById('adj_preview').textContent = 'Rs ' + updated.toLocaleString('en-PK', {maximumFractionDigits:0});
    }

    function saveAdjustCash() {
      var amt = parseFloat(document.getElementById('adj_amount').value) || 0;
      var isAdd = document.getElementById('cash_add').checked;
      var date = document.getElementById('adj_date').value;
      var desc = document.getElementById('adj_desc').value;
      if (!amt) { alert('Please enter an amount.'); return; }
      fetch('{{ route("cash-in-hand.adjust") }}', {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.App.csrfToken },
        body: JSON.stringify({ type: isAdd ? 'add' : 'reduce', amount: amt, date: date, description: desc })
      })
      .then(function(r){ return r.json(); }).then(function(d){ if (d.success) { window.location.reload(); } else { alert(d.message || 'Error saving adjustment.'); } })
      .catch(function(){ alert('Network error.'); });
    }

    function viewEditRow(refId, refType) {
      closeAllMenus(); if (!refId) return;
      var routeMap = { 'sale': '/sales/'+refId+'/edit', 'invoice': '/sales/'+refId+'/edit', 'purchase': '/purchases/'+refId+'/edit', 'payment_in': '/payment-in?edit_payment_in='+refId, 'payment_out':'/payment-out?edit_payment_out='+refId, 'expense': '/expenses/'+refId+'/edit' };
      if (routeMap[refType]) window.location.href = routeMap[refType]; else alert('Not available.');
    }

    function deleteRow(refId, refType) {
      closeAllMenus(); if (!refId || !confirm('Are you sure?')) return;
      var routeMap = { 'sale': '/sales/'+refId, 'invoice': '/sales/'+refId, 'purchase': '/purchases/'+refId, 'payment_in': '/payment-in/'+refId, 'payment_out':'/payment-out/'+refId, 'expense': '/expenses/'+refId };
      if (!routeMap[refType]) return;
      fetch(routeMap[refType], { method: 'DELETE', headers: { 'X-CSRF-TOKEN': window.App.csrfToken, 'Accept': 'application/json' } })
      .then(function(){ window.location.reload(); }).catch(function(){ window.location.reload(); });
    }

    function printRow(refId, refType, type, name, date, amount) {
      closeAllMenus(); var printArea = document.getElementById('print-area'); printArea.style.display = 'block';
      printArea.innerHTML = `<div style="font-family:Arial,sans-serif;padding:30px;max-width:400px;margin:auto;border:1px solid #e5e7eb;border-radius:8px;"><div style="text-align:center;margin-bottom:20px;"><h2 style="font-size:18px;font-weight:700;margin:0;">Cash Transaction Receipt</h2></div><table style="width:100%;font-size:13px;"><tr><td>Type</td><td><b>${type}</b></td></tr><tr><td>Name</td><td><b>${name}</b></td></tr><tr><td>Date</td><td><b>${date}</b></td></tr><tr><td>Amount</td><td><b>Rs ${parseFloat(amount).toLocaleString()}</b></td></tr></table></div>`;
      window.print(); setTimeout(function(){ printArea.style.display='none'; }, 1000);
    }

    function viewHistory(refId, refType, type) {
      closeAllMenus(); var overlay = document.getElementById('historyModalOverlay'); var body = document.getElementById('historyModalBody');
      body.innerHTML = '<div style="text-align:center;padding:30px;"><i class="fa-solid fa-spinner fa-spin"></i></div>'; overlay.classList.add('open');
      var endpoints = { 'payment_in': '/payment-in/'+refId+'/history', 'sale': '/sales/'+refId+'/history', 'invoice': '/sales/'+refId+'/history', 'purchase': '/purchases/'+refId+'/history', 'expense': '/expenses/'+refId+'/history' };
      if (!endpoints[refType]) { body.innerHTML = '<p class="text-center p-4">No history found.</p>'; return; }
      fetch(endpoints[refType], { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': window.App.csrfToken } })
      .then(function(r){ return r.json(); }).then(function(d){
        if (d.success && d.history && d.history.length > 0) {
          var html = ''; items.forEach(function(item) { html += `<div class="p-2 border-bottom"><b>${item.action}</b> - ${item.created_at}</div>`; }); body.innerHTML = html;
        } else { body.innerHTML = '<p class="text-center p-4">No history found.</p>'; }
      }).catch(function(){ body.innerHTML = '<p class="text-center p-4">No history found.</p>'; });
    }

    function closeHistoryModal() { document.getElementById('historyModalOverlay').classList.remove('open'); }
    function exportCashToExcel() {
      if (typeof XLSX === 'undefined') return;
      var data = Array.from(document.querySelectorAll('#cash-tbody tr')).filter(function(r){ return r.style.display !== 'none'; }).map(function(row) {
        var tds = row.querySelectorAll('td'); return [tds[0].textContent.trim(), tds[1].textContent.trim(), tds[2].textContent.trim(), tds[3].textContent.trim()];
      });
      var wb = XLSX.utils.book_new(); var ws = XLSX.utils.aoa_to_sheet([['Type', 'Name', 'Date', 'Amount']].concat(data));
      XLSX.utils.book_append_sheet(wb, ws, 'Cash Transactions'); XLSX.writeFile(wb, 'cash-in-hand.xlsx');
    }

    function closeAllMenus() { closeAllCashColFilters(); document.querySelectorAll('.il-row-menu.open').forEach(function (m) { m.classList.remove('open'); }); }
    document.addEventListener('click', closeAllMenus);
    document.addEventListener('keydown', function(e){ if (e.key === 'Escape') { closeAllMenus(); closeHistoryModal(); } });
  </script>
</body>
</html>