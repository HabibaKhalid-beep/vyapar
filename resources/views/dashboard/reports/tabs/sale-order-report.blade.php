{{-- ============================================================
     Sale Order Report Tab
     resources/views/dashboard/reports/tabs/sale-order-report.blade.php
     ============================================================ --}}

<div id="tab-sale-order" class="report-tab-content d-none">
    <div class="d-flex flex-column" style="min-height:100vh; padding:24px; background:#fff; border:1px solid #e5e7eb;">

        {{-- ── Filters & Actions Row ──────────────────────────────── --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

            {{-- Left: Filters --}}
            <div class="d-flex align-items-center flex-wrap gap-2">

                {{-- Party filter --}}
                <input type="text" id="so-party-filter" placeholder="Party filter"
                    style="border:1px solid #d1d5db; border-radius:4px; padding:6px 10px;
                           font-size:13px; width:150px; outline:none; color:#374151;">

                {{-- Type dropdown --}}
                <select id="so-type-filter"
                    style="border:1px solid #d1d5db; border-radius:4px; padding:6px 10px;
                           font-size:13px; background:#fff; color:#374151; outline:none;">
                    <option value="">SALE ORDER</option>
                    <option value="sale order">Sale Order</option>
                </select>

                {{-- Status dropdown --}}
                <select id="so-status-filter"
                    style="border:1px solid #d1d5db; border-radius:4px; padding:6px 10px;
                           font-size:13px; background:#fff; color:#374151; outline:none;">
                    <option value="">All Status</option>
                    <option value="Open">Open</option>
                    <option value="Closed">Closed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>

                {{-- Date range --}}
                <div class="d-flex align-items-center gap-1">
                    <span style="font-size:12px; color:#9ca3af;">From</span>
                    <input type="date" id="so-from-date"
                        value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                        style="border:1px solid #d1d5db; border-radius:4px; padding:5px 8px;
                               font-size:13px; color:#374151; outline:none;">
                    <span style="font-size:12px; color:#9ca3af;">To</span>
                    <input type="date" id="so-to-date"
                        value="{{ now()->format('Y-m-d') }}"
                        style="border:1px solid #d1d5db; border-radius:4px; padding:5px 8px;
                               font-size:13px; color:#374151; outline:none;">
                </div>

                <button onclick="loadSaleOrderReport()"
                    style="background:#4f46e5; color:#fff; border:none; border-radius:4px;
                           padding:6px 14px; font-size:13px; cursor:pointer;">
                    Apply
                </button>
            </div>

            {{-- Right: Export & Print --}}
            <div class="d-flex gap-2">
                <button onclick="exportSaleOrderCSV()" title="Export to Excel"
                    style="width:38px; height:38px; border-radius:50%; border:1px solid #e5e7eb;
                           background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-file-excel" style="color:#10b981; font-size:17px;"></i>
                </button>
                <button onclick="printSaleOrderReport()" title="Print"
                    style="width:38px; height:38px; border-radius:50%; border:1px solid #e5e7eb;
                           background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-print" style="color:#4b5563; font-size:17px;"></i>
                </button>
            </div>
        </div>

        {{-- ── Summary Cards ─────────────────────────────────────── --}}
        <div class="d-flex gap-3 mb-4 flex-wrap">
            <div style="background:#eff6ff; border-radius:8px; padding:14px 20px; min-width:150px;">
                <div style="font-size:12px; color:#6b7280; margin-bottom:4px;">Total Orders</div>
                <div id="so-total-count" style="font-size:22px; font-weight:700; color:#2563eb;">0</div>
            </div>
            <div style="background:#f0fdf4; border-radius:8px; padding:14px 20px; min-width:150px;">
                <div style="font-size:12px; color:#6b7280; margin-bottom:4px;">Total Amount</div>
                <div id="so-total-amount" style="font-size:22px; font-weight:700; color:#16a34a;">Rs 0.00</div>
            </div>
            <div style="background:#fefce8; border-radius:8px; padding:14px 20px; min-width:150px;">
                <div style="font-size:12px; color:#6b7280; margin-bottom:4px;">Open Orders</div>
                <div id="so-open-count" style="font-size:22px; font-weight:700; color:#ca8a04;">0</div>
            </div>
        </div>

        {{-- ── Page Title ────────────────────────────────────────── --}}
        <h2 style="font-weight:700; color:#1f2937; margin:0 0 24px 0; font-size:22px;">
            Sale Orders
        </h2>

        {{-- ── Data Table ────────────────────────────────────────── --}}
        <div class="table-responsive">
            <table class="w-100" id="so-main-table" style="border-collapse:collapse;">
                <thead style="background:#f9fafb;">
                    <tr style="border-bottom:2px solid #e5e7eb;">
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:left;">#</th>
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:left;">Date</th>
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:left;">Order No.</th>
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:left;">Party Name</th>
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:left;">Status</th>
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody id="so-table-body">
                    <tr>
                        <td colspan="6" style="padding:48px; text-align:center; color:#9ca3af; font-size:13px;">
                            Select filters and click <strong>Apply</strong> to load data
                        </td>
                    </tr>
                </tbody>
                <tfoot id="so-table-foot" style="display:none;">
                    <tr style="background:#f9fafb; border-top:2px solid #e5e7eb;">
                        <td colspan="5" style="padding:11px 14px; font-size:14px; font-weight:700; color:#1f2937;">Total</td>
                        <td id="so-grand-total"
                            style="padding:11px 14px; font-size:14px; font-weight:700; color:#1f2937; text-align:right;">
                            Rs 0.00
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>

{{-- ============================================================
     Sale Order Item Report Tab
     ============================================================ --}}

<div id="tab-sale-order-item" class="report-tab-content d-none">
    <div class="d-flex flex-column" style="min-height:100vh; padding:24px; background:#fff; border:1px solid #e5e7eb;">

        {{-- Filters & Actions --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div class="d-flex align-items-center flex-wrap gap-2">

                <input type="text" id="soi-party-filter" placeholder="Party filter"
                    style="border:1px solid #d1d5db; border-radius:4px; padding:6px 10px;
                           font-size:13px; width:150px; outline:none; color:#374151;">

                <select id="soi-type-filter"
                    style="border:1px solid #d1d5db; border-radius:4px; padding:6px 10px;
                           font-size:13px; background:#fff; color:#374151; outline:none;">
                    <option value="">SALE ORDER</option>
                    <option value="sale order">Sale Order</option>
                </select>

                <select id="soi-status-filter"
                    style="border:1px solid #d1d5db; border-radius:4px; padding:6px 10px;
                           font-size:13px; background:#fff; color:#374151; outline:none;">
                    <option value="">All Status</option>
                    <option value="Open">Open</option>
                    <option value="Closed">Closed</option>
                    <option value="Cancelled">Cancelled</option>
                </select>

                <div class="d-flex align-items-center gap-1">
                    <span style="font-size:12px; color:#9ca3af;">From</span>
                    <input type="date" id="soi-from-date"
                        value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                        style="border:1px solid #d1d5db; border-radius:4px; padding:5px 8px;
                               font-size:13px; color:#374151; outline:none;">
                    <span style="font-size:12px; color:#9ca3af;">To</span>
                    <input type="date" id="soi-to-date"
                        value="{{ now()->format('Y-m-d') }}"
                        style="border:1px solid #d1d5db; border-radius:4px; padding:5px 8px;
                               font-size:13px; color:#374151; outline:none;">
                </div>

                <button onclick="loadSaleOrderItemReport()"
                    style="background:#4f46e5; color:#fff; border:none; border-radius:4px;
                           padding:6px 14px; font-size:13px; cursor:pointer;">
                    Apply
                </button>
            </div>

            <div class="d-flex gap-2">
                <button onclick="exportSaleOrderItemCSV()" title="Export to Excel"
                    style="width:38px; height:38px; border-radius:50%; border:1px solid #e5e7eb;
                           background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-file-excel" style="color:#10b981; font-size:17px;"></i>
                </button>
                <button onclick="printSaleOrderItemReport()" title="Print"
                    style="width:38px; height:38px; border-radius:50%; border:1px solid #e5e7eb;
                           background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-print" style="color:#4b5563; font-size:17px;"></i>
                </button>
            </div>
        </div>

        <h2 style="font-weight:700; color:#1f2937; margin:0 0 24px 0; font-size:22px;">
            Sale Order Items
        </h2>

        <div class="table-responsive">
            <table class="w-100" id="soi-main-table" style="border-collapse:collapse;">
                <thead style="background:#f9fafb;">
                    <tr style="border-bottom:2px solid #e5e7eb;">
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:left;">#</th>
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:left;">Item Name</th>
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:right;">Quantity</th>
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:right;">Amount</th>
                    </tr>
                </thead>
                <tbody id="soi-table-body">
                    <tr>
                        <td colspan="4" style="padding:48px; text-align:center; color:#9ca3af; font-size:13px;">
                            Select filters and click <strong>Apply</strong> to load data
                        </td>
                    </tr>
                </tbody>
                <tfoot id="soi-table-foot" style="display:none;">
                    <tr style="background:#f9fafb; border-top:2px solid #e5e7eb;">
                        <td style="padding:11px 14px; font-size:14px; font-weight:700; color:#1f2937;">Total</td>
                        <td></td>
                        <td id="soi-total-qty" style="padding:11px 14px; font-size:14px; font-weight:700; color:#1f2937; text-align:right;">0</td>
                        <td id="soi-total-amt" style="padding:11px 14px; font-size:14px; font-weight:700; color:#1f2937; text-align:right;">Rs 0.00</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

{{-- ============================================================
     Loan Statement Tab
     ============================================================ --}}

<div id="tab-loan-statement" class="report-tab-content d-none">    <div class="d-flex flex-column" style="min-height:100vh; padding:24px; background:#fff; border:1px solid #e5e7eb;">

        {{-- Filters & Actions --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
            <div class="d-flex align-items-center flex-wrap gap-2">

                {{-- Account selector --}}
                <div class="d-flex flex-column gap-1">
                    <label style="font-size:11px; font-weight:600; color:#6b7280; text-transform:uppercase; letter-spacing:.5px;">
                        Account
                    </label>
                    <select id="loan-account-select"
                        style="border:1px solid #d1d5db; border-radius:4px; padding:6px 12px;
                               font-size:13px; background:#fff; color:#374151; outline:none; min-width:200px;">
                        <option value="">— Select Account —</option>
                       @foreach(\App\Models\LoanAccount::orderBy('display_name')->get() as $la)
    <option value="{{ $la->id }}">{{ $la->display_name }}</option>
@endforeach
                    </select>
                </div>

                {{-- Date filter checkbox --}}
                <div class="d-flex align-items-center gap-2">
                    <input type="checkbox" id="loan-date-toggle" style="width:15px; height:15px; cursor:pointer;"
                        onchange="document.getElementById('loan-date-range').style.display = this.checked ? 'flex' : 'none'">
                    <label for="loan-date-toggle" style="font-size:13px; color:#6b7280; cursor:pointer; margin:0;">
                        Date filter
                    </label>
                </div>

                {{-- Date range (hidden by default) --}}
                <div id="loan-date-range" class="d-flex align-items-center gap-1" style="display:none!important;">
                    <span style="font-size:12px; color:#9ca3af;">From</span>
                    <input type="date" id="loan-from-date"
                        value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                        style="border:1px solid #d1d5db; border-radius:4px; padding:5px 8px;
                               font-size:13px; color:#374151; outline:none;">
                    <span style="font-size:12px; color:#9ca3af;">To</span>
                    <input type="date" id="loan-to-date"
                        value="{{ now()->format('Y-m-d') }}"
                        style="border:1px solid #d1d5db; border-radius:4px; padding:5px 8px;
                               font-size:13px; color:#374151; outline:none;">
                </div>

                <button onclick="loadLoanStatement()"
                    style="background:#4f46e5; color:#fff; border:none; border-radius:4px;
                           padding:6px 14px; font-size:13px; cursor:pointer;">
                    Apply
                </button>
            </div>

            <div class="d-flex gap-2 align-items-center">
                {{-- Add Loan A/C button --}}
                <button onclick="window.location.href='{{ route('loan-accounts') }}'"
                    style="background:#4f46e5; color:#fff; border:none; border-radius:4px;
                           padding:7px 14px; font-size:13px; cursor:pointer; white-space:nowrap;">
                    <i class="fa-solid fa-plus me-1"></i> Add Loan A/C
                </button>
                <button onclick="exportLoanStatementCSV()" title="Export to Excel"
                    style="width:38px; height:38px; border-radius:50%; border:1px solid #e5e7eb;
                           background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-file-excel" style="color:#10b981; font-size:17px;"></i>
                </button>
                <button onclick="printLoanStatement()" title="Print"
                    style="width:38px; height:38px; border-radius:50%; border:1px solid #e5e7eb;
                           background:#fff; cursor:pointer; display:flex; align-items:center; justify-content:center;">
                    <i class="fa-solid fa-print" style="color:#4b5563; font-size:17px;"></i>
                </button>
            </div>
        </div>

        <h2 style="font-weight:700; color:#1f2937; margin:0 0 24px 0; font-size:22px;">
            Loan Statement
        </h2>

        {{-- Summary cards --}}
        <div class="d-flex gap-3 mb-4 flex-wrap" id="loan-summary-row" style="display:none!important;">
            <div style="background:#eff6ff; border-radius:8px; padding:14px 20px; min-width:150px;">
                <div style="font-size:12px; color:#6b7280; margin-bottom:4px;">Opening Balance</div>
                <div id="loan-card-opening" style="font-size:18px; font-weight:700; color:#2563eb;">Rs 0.00</div>
            </div>
            <div style="background:#fef2f2; border-radius:8px; padding:14px 20px; min-width:150px;">
                <div style="font-size:12px; color:#6b7280; margin-bottom:4px;">Balance Due</div>
                <div id="loan-card-due" style="font-size:18px; font-weight:700; color:#dc2626;">Rs 0.00</div>
            </div>
            <div style="background:#f0fdf4; border-radius:8px; padding:14px 20px; min-width:150px;">
                <div style="font-size:12px; color:#6b7280; margin-bottom:4px;">Principal Paid</div>
                <div id="loan-card-paid" style="font-size:18px; font-weight:700; color:#16a34a;">Rs 0.00</div>
            </div>
            <div style="background:#fefce8; border-radius:8px; padding:14px 20px; min-width:150px;">
                <div style="font-size:12px; color:#6b7280; margin-bottom:4px;">Principal Due</div>
                <div id="loan-card-pdue" style="font-size:18px; font-weight:700; color:#ca8a04;">Rs 0.00</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="w-100" id="loan-main-table" style="border-collapse:collapse;">
                <thead style="background:#f9fafb;">
                    <tr style="border-bottom:2px solid #e5e7eb;">
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:left;">#</th>
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:left;">Date</th>
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:left;">Type</th>
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:right;">Amount</th>
                        <th style="padding:11px 14px; font-size:12px; font-weight:600; color:#6b7280; text-align:right;">Ending Balance</th>
                    </tr>
                </thead>
                <tbody id="loan-table-body">
                    <tr>
                        <td colspan="5" style="padding:48px; text-align:center; color:#9ca3af; font-size:13px;">
                            Select an account and click <strong>Apply</strong>
                        </td>
                    </tr>
                </tbody>
                <tfoot id="loan-table-foot" style="display:none;">
                    <tr style="border-top:1px solid #e5e7eb; background:#fafafa;">
                        <td colspan="3" style="padding:11px 14px; font-size:13px; font-weight:700; color:#374151;">
                            Opening Balance
                        </td>
                        <td colspan="2" id="loan-foot-opening"
                            style="padding:11px 14px; font-size:13px; font-weight:700; color:#374151; text-align:right;">
                            Rs 0.00
                        </td>
                    </tr>
                    <tr style="border-top:1px solid #e5e7eb; background:#fafafa;">
                        <td colspan="3" style="padding:11px 14px; font-size:13px; font-weight:700; color:#dc2626;">
                            Balance Due
                        </td>
                        <td colspan="2" id="loan-foot-due"
                            style="padding:11px 14px; font-size:13px; font-weight:700; color:#dc2626; text-align:right;">
                            Rs 0.00
                        </td>
                    </tr>
                    <tr style="border-top:1px solid #e5e7eb; background:#fafafa;">
                        <td colspan="3" style="padding:11px 14px; font-size:13px; font-weight:700; color:#16a34a;">
                            Total Principal Paid
                        </td>
                        <td colspan="2" id="loan-foot-paid"
                            style="padding:11px 14px; font-size:13px; font-weight:700; color:#16a34a; text-align:right;">
                            Rs 0.00
                        </td>
                    </tr>
                    <tr style="border-top:1px solid #e5e7eb; background:#fafafa;">
                        <td colspan="3" style="padding:11px 14px; font-size:13px; font-weight:700; color:#1f2937;">
                            Total Principal Due
                        </td>
                        <td colspan="2" id="loan-foot-pdue"
                            style="padding:11px 14px; font-size:13px; font-weight:700; color:#1f2937; text-align:right;">
                            Rs 0.00
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div><script>
/* ============================================================
   SALE ORDER REPORT
   ============================================================ */
let _soRawData = [];

function loadSaleOrderReport() {
    const from  = document.getElementById('so-from-date').value;
    const to    = document.getElementById('so-to-date').value;
    const tbody = document.getElementById('so-table-body');
    const tfoot = document.getElementById('so-table-foot');

    tbody.innerHTML = `
        <tr>
          <td colspan="6" style="padding:48px; text-align:center; color:#9ca3af; font-size:13px;">
            <i class="fa-solid fa-spinner fa-spin me-2"></i> Loading…
          </td>
        </tr>`;

    /* Use the dedicated sale-order endpoint in ReportController */
    fetch(`/dashboard/reports/sale-order-items?from=${from}&to=${to}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) throw new Error('API error');

        /* Group rows by bill_number to get one row per order */
        const orderMap = {};
        (data.rows || []).forEach(r => {
            const key = r.bill_number || r.date;
            if (!orderMap[key]) {
                orderMap[key] = {
                    bill_number  : r.bill_number,
                    invoice_date : r.date,
                    order_number : r.bill_number,
                    party_name   : r.party_name,
                    status       : r.status,
                    total_amount : 0,
                };
            }
            orderMap[key].total_amount += parseFloat(r.amount || 0);
        });

        _soRawData = Object.values(orderMap);

        /* Summary cards */
        const totalAmt = _soRawData.reduce((s, r) => s + r.total_amount, 0);
        document.getElementById('so-total-count').textContent  = _soRawData.length;
        document.getElementById('so-total-amount').textContent =
            'Rs ' + totalAmt.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        document.getElementById('so-open-count').textContent   =
            _soRawData.filter(r => (r.status || '').toLowerCase() === 'open').length;

        if (tfoot) tfoot.style.display = '';
        renderSaleOrderTable();
    })
    .catch(() => {
        tbody.innerHTML = `
            <tr>
              <td colspan="6" style="padding:48px; text-align:center;
                                     color:#ef4444; font-size:13px;">
                Failed to load data. Please try again.
              </td>
            </tr>`;
    });
}

function renderSaleOrderTable() {
    const partyF  = (document.getElementById('so-party-filter')?.value || '').toLowerCase();
    const statusF = document.getElementById('so-status-filter')?.value || '';
    const tbody   = document.getElementById('so-table-body');

    const rows = _soRawData.filter(r => {
        if (partyF  && !(r.party_name || '').toLowerCase().includes(partyF)) return false;
        if (statusF && (r.status || '') !== statusF) return false;
        return true;
    });

    if (!rows.length) {
        tbody.innerHTML = `
            <tr>
              <td colspan="6" style="padding:48px; text-align:center;
                                     color:#9ca3af; font-size:13px;">
                No sale orders found for the selected period.
              </td>
            </tr>`;
        document.getElementById('so-grand-total').textContent = 'Rs 0.00';
        return;
    }

    let grandTotal = 0;
    tbody.innerHTML = rows.map((r, idx) => {
        const amt = parseFloat(r.total_amount || 0);
        grandTotal += amt;
        const statusColor = r.status === 'Open'   ? '#ca8a04'
                          : r.status === 'Closed' ? '#16a34a' : '#6b7280';
        return `
        <tr style="border-bottom:1px solid #e5e7eb;"
            onmouseover="this.style.background='#f9fafb'"
            onmouseout="this.style.background=''">
            <td style="padding:11px 14px; font-size:13px; color:#6b7280;">${idx + 1}</td>
            <td style="padding:11px 14px; font-size:13px; color:#374151;">${r.invoice_date || ''}</td>
            <td style="padding:11px 14px; font-size:13px; color:#374151;">${r.order_number || '—'}</td>
            <td style="padding:11px 14px; font-size:13px; color:#374151;">${r.party_name || '—'}</td>
            <td style="padding:11px 14px; font-size:13px;">
                <span style="color:${statusColor}; font-weight:600;">${r.status || '—'}</span>
            </td>
            <td style="padding:11px 14px; font-size:13px; color:#374151; text-align:right;">
                Rs ${amt.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}
            </td>
        </tr>`;
    }).join('');

    document.getElementById('so-grand-total').textContent =
        'Rs ' + grandTotal.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function exportSaleOrderCSV() {
    const table = document.getElementById('so-main-table');
    if (!table) return;
    let csv = '';
    table.querySelectorAll('tr').forEach(tr => {
        const cells = [...tr.querySelectorAll('th,td')]
            .map(td => '"' + td.innerText.trim().replace(/"/g, '""') + '"');
        if (cells.length) csv += cells.join(',') + '\n';
    });
    const a = document.createElement('a');
    a.href     = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    a.download = 'sale_order_report.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function printSaleOrderReport() {
    const from = document.getElementById('so-from-date').value;
    const to   = document.getElementById('so-to-date').value;
    const w    = window.open('', '_blank');
    w.document.write(`
        <!DOCTYPE html><html><head><meta charset="UTF-8">
        <title>Sale Orders Report</title>
        <style>
          body{font-family:Arial,sans-serif;padding:32px;color:#1f2937}
          h2{font-size:18px;font-weight:700;margin-bottom:4px}
          p{font-size:12px;color:#6b7280;margin:0 0 20px}
          table{width:100%;border-collapse:collapse;font-size:13px}
          th{padding:10px 14px;font-weight:700;border-bottom:1px solid #e5e7eb;text-align:left}
          td{padding:10px 14px;border-bottom:1px solid #e5e7eb}
          tfoot td{font-weight:700}
        </style></head><body>
        <h2>Sale Orders Report</h2>
        <p>From ${from} To ${to}</p>
        ${document.getElementById('so-main-table').outerHTML}
        <script>window.onload=function(){window.print()}<\/script>
        </body></html>`);
    w.document.close();
}

/* ============================================================
   SALE ORDER ITEM REPORT
   ============================================================ */
let _soiRawData = [];

function loadSaleOrderItemReport() {
    const from  = document.getElementById('soi-from-date').value;
    const to    = document.getElementById('soi-to-date').value;
    const tbody = document.getElementById('soi-table-body');
    const tfoot = document.getElementById('soi-table-foot');

    tbody.innerHTML = `
        <tr>
          <td colspan="4" style="padding:60px; text-align:center; color:#9ca3af; font-size:13px;">
            <i class="fa-solid fa-spinner fa-spin me-2"></i> Loading…
          </td>
        </tr>`;

    fetch(`/dashboard/reports/sale-order-items?from=${from}&to=${to}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        if (!data.success) throw new Error('API error');

        _soiRawData = (data.rows || []).map(r => ({
            item_name  : r.item_name  || '—',
            quantity   : parseFloat(r.quantity  || 0),
            amount     : parseFloat(r.amount    || 0),
            status     : r.status     || '',
            party_name : r.party_name || '',
        }));

        if (tfoot) tfoot.style.display = '';
        renderSaleOrderItemTable();
    })
    .catch(() => {
        tbody.innerHTML = `
            <tr>
              <td colspan="4" style="padding:60px; text-align:center;
                                     color:#ef4444; font-size:13px;">
                Failed to load data. Please try again.
              </td>
            </tr>`;
    });
}

function renderSaleOrderItemTable() {
    const partyF  = (document.getElementById('soi-party-filter')?.value || '').toLowerCase();
    const statusF = document.getElementById('soi-status-filter')?.value || '';
    const tbody   = document.getElementById('soi-table-body');

    const rows = _soiRawData.filter(r => {
        if (partyF  && !(r.item_name   || '').toLowerCase().includes(partyF)
                    && !(r.party_name  || '').toLowerCase().includes(partyF)) return false;
        if (statusF && (r.status || '') !== statusF) return false;
        return true;
    });

    if (!rows.length) {
        tbody.innerHTML = `
            <tr>
              <td colspan="4" style="padding:60px; text-align:center;
                                     color:#9ca3af; font-size:13px;">
                No sale order items found for the selected period.
              </td>
            </tr>`;
        document.getElementById('soi-total-qty').textContent = '0';
        document.getElementById('soi-total-amt').textContent = 'Rs 0.00';
        return;
    }

    let totalQty = 0, totalAmt = 0;
    tbody.innerHTML = rows.map((r, idx) => {
        totalQty += r.quantity;
        totalAmt += r.amount;
        return `
        <tr style="border-bottom:1px solid #e5e7eb;"
            onmouseover="this.style.background='#f9fafb'"
            onmouseout="this.style.background=''">
            <td style="padding:11px 14px; font-size:13px; color:#6b7280;">${idx + 1}</td>
            <td style="padding:11px 14px; font-size:14px; color:#374151;">${r.item_name}</td>
            <td style="padding:11px 14px; font-size:14px; color:#374151; text-align:right;">
                ${r.quantity % 1 === 0 ? r.quantity : r.quantity.toFixed(2)}
            </td>
            <td style="padding:11px 14px; font-size:14px; color:#374151; text-align:right;">
                Rs ${r.amount.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',')}
            </td>
        </tr>`;
    }).join('');

    document.getElementById('soi-total-qty').textContent =
        totalQty % 1 === 0 ? totalQty : totalQty.toFixed(2);
    document.getElementById('soi-total-amt').textContent =
        'Rs ' + totalAmt.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

function exportSaleOrderItemCSV() {
    const table = document.getElementById('soi-main-table');
    if (!table) return;
    let csv = '';
    table.querySelectorAll('tr').forEach(tr => {
        const cells = [...tr.querySelectorAll('th,td')]
            .map(td => '"' + td.innerText.trim().replace(/"/g, '""') + '"');
        if (cells.length) csv += cells.join(',') + '\n';
    });
    const a = document.createElement('a');
    a.href     = 'data:text/csv;charset=utf-8,' + encodeURIComponent(csv);
    a.download = 'sale_order_item_report.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
}

function printSaleOrderItemReport() {
    const from = document.getElementById('soi-from-date').value;
    const to   = document.getElementById('soi-to-date').value;
    const w    = window.open('', '_blank');
    w.document.write(`
        <!DOCTYPE html><html><head><meta charset="UTF-8">
        <title>Sale Order Items Report</title>
        <style>
          body{font-family:Arial,sans-serif;padding:32px;color:#1f2937}
          h2{font-size:18px;font-weight:700;margin-bottom:4px}
          p{font-size:12px;color:#6b7280;margin:0 0 20px}
          table{width:100%;border-collapse:collapse;font-size:13px}
          th{padding:10px 14px;font-weight:700;border-bottom:1px solid #e5e7eb;text-align:left}
          td{padding:10px 14px;border-bottom:1px solid #e5e7eb}
          tfoot td{font-weight:700}
        </style></head><body>
        <h2>Sale Order Items Report</h2>
        <p>From ${from} To ${to}</p>
        ${document.getElementById('soi-main-table').outerHTML}
        <script>window.onload=function(){window.print()}<\/script>
        </body></html>`);
    w.document.close();
}/* ============================================================
   TAB AUTO-LOAD LISTENERS
   ============================================================ */
document.addEventListener('DOMContentLoaded', function () {

    /* Sale Order tab */
    document.querySelectorAll('[data-target="sale-order"], [data-tab="sale-order"]')
        .forEach(link => {
            link.addEventListener('click', function () {
                setTimeout(loadSaleOrderReport, 100);
            });
        });

    /* Sale Order Item tab */
    document.querySelectorAll('[data-target="sale-order-item"], [data-tab="sale-order-item"]')
        .forEach(link => {
            link.addEventListener('click', function () {
                setTimeout(loadSaleOrderItemReport, 100);
            });
        });

});
</script>  {{-- ← this is the EXISTING closing tag, don't add another --}}
