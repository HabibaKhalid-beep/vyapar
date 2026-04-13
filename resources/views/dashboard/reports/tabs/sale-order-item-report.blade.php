{{-- ============================================================
     FILE: resources/views/dashboard/reports/tabs/sale-order-item-report.blade.php
     
     HOW TO USE — in your main report.blade.php find this line:
     
         <div id="tab-sale order item" class="report-tab-content d-none">
         ... (the old static dummy div with hardcoded "abc","def" rows)
         </div>
     
     DELETE that entire old div and REPLACE with:
         @include('dashboard.reports.tabs.sale-order-item-report')
     ============================================================ --}}

<div id="tab-sale order item" class="report-tab-content d-none">
    <div class="d-flex flex-column"
        style="min-height:100vh; padding:24px; background:#ffffff; border:1px solid #e5e7eb;">

        {{-- ── Top: Date range + Export/Print ──────────────────── --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

            {{-- Left: date range --}}
            <div class="d-flex align-items-center flex-wrap gap-2">

                {{-- From --}}
                <div class="d-flex align-items-center px-2 py-1"
                    style="border:1px solid #d1d5db; border-radius:4px; background:#fff; cursor:pointer;"
                    onclick="document.getElementById('soi-from').showPicker()">
                    <span style="font-size:12px; color:#9ca3af; margin-right:6px;">From</span>
                    <span id="soi-from-display"
                        style="font-size:14px; color:#374151; font-weight:500; margin-right:6px;">
                        {{ now()->startOfMonth()->format('d/m/Y') }}
                    </span>
                    <i class="fa-regular fa-calendar" style="color:#9ca3af; font-size:13px;"></i>
                    <input type="date" id="soi-from"
                        value="{{ now()->startOfMonth()->format('Y-m-d') }}"
                        style="position:absolute; opacity:0; width:0; height:0; pointer-events:none;"
                        onchange="soiDateChanged('from', this.value)">
                </div>

                <span style="font-size:13px; color:#6b7280; font-weight:600;">To</span>

                {{-- To --}}
                <div class="d-flex align-items-center px-2 py-1"
                    style="border:1px solid #d1d5db; border-radius:4px; background:#fff; cursor:pointer;"
                    onclick="document.getElementById('soi-to').showPicker()">
                    <span style="font-size:12px; color:#9ca3af; margin-right:6px;">To</span>
                    <span id="soi-to-display"
                        style="font-size:14px; color:#374151; font-weight:500; margin-right:6px;">
                        {{ now()->format('d/m/Y') }}
                    </span>
                    <i class="fa-regular fa-calendar" style="color:#9ca3af; font-size:13px;"></i>
                    <input type="date" id="soi-to"
                        value="{{ now()->format('Y-m-d') }}"
                        style="position:absolute; opacity:0; width:0; height:0; pointer-events:none;"
                        onchange="soiDateChanged('to', this.value)">
                </div>

            </div>

            {{-- Right: Export + Print --}}
            <div class="d-flex gap-2">
                <button onclick="exportSaleOrderItemCSV()" title="Export Excel"
                    style="width:38px; height:38px; border-radius:50%; border:1px solid #e5e7eb;
                           background:#fff; cursor:pointer; display:flex; align-items:center;
                           justify-content:center;">
                    <i class="fa-solid fa-file-excel" style="color:#10b981; font-size:17px;"></i>
                </button>
                <button onclick="printSaleOrderItemReport()" title="Print"
                    style="width:38px; height:38px; border-radius:50%; border:1px solid #e5e7eb;
                           background:#fff; cursor:pointer; display:flex; align-items:center;
                           justify-content:center;">
                    <i class="fa-solid fa-print" style="color:#4b5563; font-size:17px;"></i>
                </button>
            </div>
        </div>

        {{-- ── FILTERS row (Party filter + SALE ORDER dropdown + All Status) ── --}}
        <div class="d-flex align-items-center gap-2 mb-4" style="font-size:13px; color:#6b7280; font-weight:500;">
            <span>FILTERS</span>

            {{-- Party filter --}}
            <input type="text" id="soi-party-filter" placeholder="Party filter"
                style="border:1px solid #d1d5db; border-radius:4px; padding:6px 12px;
                       font-size:13px; color:#374151; outline:none; width:160px;"
                oninput="renderSaleOrderItemTable()">

            {{-- Type dropdown --}}
            <select id="soi-type-filter"
                style="border:1px solid #d1d5db; border-radius:4px; padding:6px 28px 6px 10px;
                       font-size:13px; color:#374151; background:#fff; outline:none; cursor:pointer;"
                onchange="renderSaleOrderItemTable()">
                <option value="">SALE ORDER</option>
                <option value="sale order">Sale Order</option>
                <option value="purchase order">Purchase Order</option>
            </select>

            {{-- Status dropdown --}}
            <select id="soi-status-filter"
                style="border:1px solid #d1d5db; border-radius:4px; padding:6px 28px 6px 10px;
                       font-size:13px; color:#374151; background:#fff; outline:none; cursor:pointer;"
                onchange="renderSaleOrderItemTable()">
                <option value="">All Status</option>
                <option value="Open">Open</option>
                <option value="Closed">Closed</option>
                <option value="Cancelled">Cancelled</option>
            </select>

        </div>

        {{-- ── Data Table ── matching image exactly ──────────────── --}}
        <div class="table-responsive">
            <table class="w-100" id="soi-main-table" style="border-collapse:collapse;">
                <thead>
                    <tr style="border-bottom:1px solid #e5e7eb; background:#fff;">
                        <th style="padding:12px 16px; font-size:13px; font-weight:600;
                                   color:#374151; text-align:left;">
                            Item Name
                        </th>
                        <th style="padding:12px 16px; font-size:13px; font-weight:600;
                                   color:#374151; text-align:right; border-left:1px solid #e5e7eb;">
                            Quantity
                        </th>
                        <th style="padding:12px 16px; font-size:13px; font-weight:600;
                                   color:#374151; text-align:right; border-left:1px solid #e5e7eb;">
                            Amount
                        </th>
                    </tr>
                </thead>
                <tbody id="soi-table-body">
                    <tr>
                        <td colspan="3"
                            style="padding:60px; text-align:center; color:#9ca3af; font-size:13px;">
                            <i class="fa-solid fa-spinner fa-spin me-2"></i> Loading…
                        </td>
                    </tr>
                </tbody>
                <tfoot id="soi-table-foot">
                    <tr style="border-top:1px solid #e5e7eb; background:#fff;">
                        <td style="padding:12px 16px; font-size:14px; font-weight:700; color:#374151;">
                            Total
                        </td>
                        <td id="soi-total-qty"
                            style="padding:12px 16px; font-size:14px; font-weight:700;
                                   color:#374151; text-align:right; border-left:1px solid #e5e7eb;">
                            0
                        </td>
                        <td id="soi-total-amt"
                            style="padding:12px 16px; font-size:14px; font-weight:700;
                                   color:#374151; text-align:right; border-left:1px solid #e5e7eb;">
                            Rs 0.00
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

    </div>
</div>


{{-- ============================================================
     SCRIPTS for Sale Order Item tab
     ============================================================ --}}
<script>
/* ── cached data ──────────────────────────────────────────── */
let _soiRawData = [];

/* ── format date display dd/mm/yyyy ──────────────────────── */
function soiFormatDate(ymd) {
    if (!ymd) return '';
    const [y, m, d] = ymd.split('-');
    return `${d}/${m}/${y}`;
}

/* ── update display label when date picker changes ───────── */
function soiDateChanged(which, value) {
    if (which === 'from') {
        document.getElementById('soi-from-display').textContent = soiFormatDate(value);
    } else {
        document.getElementById('soi-to-display').textContent = soiFormatDate(value);
    }
    loadSaleOrderItemReport();
}

/* ── format Rs amount ─────────────────────────────────────── */
function soiFmt(val) {
    return 'Rs ' + parseFloat(val || 0).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
}

/* ── Load data from API ───────────────────────────────────── */
function loadSaleOrderItemReport() {
    const from  = document.getElementById('soi-from-date')?.value 
                  || document.getElementById('soi-from')?.value;
    const to    = document.getElementById('soi-to-date')?.value 
                  || document.getElementById('soi-to')?.value;
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
              <td colspan="4" style="padding:60px; text-align:center; color:#ef4444; font-size:13px;">
                Failed to load data. Please try again.
              </td>
            </tr>`;
    });
}

/* ── Render table (applies filters) ──────────────────────── */
function renderSaleOrderItemTable() {
    const partyF  = (document.getElementById('soi-party-filter')?.value || '').toLowerCase().trim();
    const statusF = document.getElementById('soi-status-filter')?.value || '';
    const tbody   = document.getElementById('soi-table-body');

    let rows = _soiRawData.filter(r => {
        if (partyF  && !(r.item_name || '').toLowerCase().includes(partyF)
                    && !(r.party_name|| '').toLowerCase().includes(partyF)) return false;
        if (statusF && r.status !== statusF) return false;
        return true;
    });

    if (!rows.length) {
        tbody.innerHTML = `
            <tr>
              <td colspan="3" style="padding:60px; text-align:center; color:#9ca3af; font-size:13px;">
                No sale order items found for the selected period.
              </td>
            </tr>`;
        document.getElementById('soi-total-qty').textContent = '0';
        document.getElementById('soi-total-amt').textContent = 'Rs 0.00';
        return;
    }

    let totalQty = 0, totalAmt = 0;

    tbody.innerHTML = rows.map(r => {
        const qty = parseFloat(r.quantity || 0);
        const amt = parseFloat(r.amount   || 0);
        totalQty += qty;
        totalAmt += amt;

        return `
        <tr style="border-bottom:1px solid #e5e7eb;"
            onmouseover="this.style.background='#f9fafb'"
            onmouseout="this.style.background=''">
            <td style="padding:14px 16px; font-size:14px; color:#374151;">
                ${r.item_name}
            </td>
            <td style="padding:14px 16px; font-size:14px; color:#374151;
                       text-align:right; border-left:1px solid #e5e7eb;">
                ${qty % 1 === 0 ? qty : qty.toFixed(2)}
            </td>
            <td style="padding:14px 16px; font-size:14px; color:#374151;
                       text-align:right; border-left:1px solid #e5e7eb;">
                ${soiFmt(amt)}
            </td>
        </tr>`;
    }).join('');

    document.getElementById('soi-total-qty').textContent = totalQty % 1 === 0 ? totalQty : totalQty.toFixed(2);
    document.getElementById('soi-total-amt').textContent = soiFmt(totalAmt);
}

/* ── Export CSV ───────────────────────────────────────────── */
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

/* ── Print ────────────────────────────────────────────────── */
function printSaleOrderItemReport() {
    const from = document.getElementById('soi-from-display').textContent;
    const to   = document.getElementById('soi-to-display').textContent;
    const tableHTML = document.getElementById('soi-main-table')?.outerHTML || '';

    const w = window.open('', '_blank');
    w.document.write(`
        <!DOCTYPE html><html>
        <head>
          <meta charset="UTF-8">
          <title>Sale Order Items Report</title>
          <style>
            body  { font-family:Arial,sans-serif; padding:32px; color:#1f2937; }
            h2    { font-size:18px; font-weight:700; margin-bottom:4px; }
            p     { font-size:12px; color:#6b7280; margin:0 0 20px; }
            table { width:100%; border-collapse:collapse; font-size:13px; }
            th    { padding:10px 14px; font-size:12px; font-weight:700; color:#374151;
                    border-bottom:1px solid #e5e7eb; text-align:left; }
            td    { padding:12px 14px; border-bottom:1px solid #e5e7eb; color:#374151; }
            tfoot td { font-weight:700; }
            @media print { button { display:none !important; } }
          </style>
        </head>
        <body>
          <h2>Sale Order Items Report</h2>
          <p>From ${from} To ${to}</p>
          ${tableHTML}
          <script>window.onload = function(){ window.print(); }<\/script>
        </body></html>`);
    w.document.close();
}

/* ── Auto-load on tab activation ─────────────────────────── */
document.addEventListener('DOMContentLoaded', function () {
    /* Load immediately so data is ready when user clicks the tab */
    loadSaleOrderItemReport();

    /* Also reload when this tab becomes active */
    document.querySelectorAll('[data-target="sale order item"], [data-tab="sale order item"]')
        .forEach(link => {
            link.addEventListener('click', function () {
                setTimeout(loadSaleOrderItemReport, 100);
            });
        });
});
</script>