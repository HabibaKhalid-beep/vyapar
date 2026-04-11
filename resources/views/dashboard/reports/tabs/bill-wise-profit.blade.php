{{-- ============================================================
     Tab: Bill Wise Profit (Profit on Sale Invoices)
     ============================================================ --}}

<div id="tab-BillWiseProfit" class="report-tab-content d-none"
     style="padding: 24px; background: #fff; min-height: 100vh; overflow-y: auto;">

    {{-- ── Top Bar ── --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">

        {{-- Date Range --}}
        <div class="d-flex align-items-center border rounded px-3 py-2 bg-white"
             style="gap:12px; border-color:#e5e7eb;">
            <span class="text-secondary fw-medium" style="font-size:14px;">From</span>
            <input type="date" id="bwpFrom"
                   class="fw-medium text-dark bg-transparent border-0"
                   style="font-size:14px; outline:none;">
            <span class="text-secondary fw-medium" style="font-size:14px;">To</span>
            <input type="date" id="bwpTo"
                   class="fw-medium text-dark bg-transparent border-0"
                   style="font-size:14px; outline:none;">
            <button class="btn btn-sm btn-primary ms-2" id="bwpApplyBtn"
                    style="font-size:13px;">Apply</button>
        </div>

        {{-- Export --}}
        <div class="d-flex gap-2">
            <button class="btn d-flex align-items-center justify-content-center p-0"
                    id="bwpExcelBtn"
                    style="width:40px;height:40px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;"
                    title="Export Excel">
                <i class="fa-regular fa-file-excel text-success" style="font-size:18px;"></i>
            </button>
            <button class="btn d-flex align-items-center justify-content-center p-0"
                    onclick="window.print()"
                    style="width:40px;height:40px;border-radius:50%;border:1px solid #e5e7eb;background:#fff;"
                    title="Print">
                <i class="fa-solid fa-print text-secondary" style="font-size:18px;"></i>
            </button>
        </div>
    </div>

    {{-- ── Title ── --}}
    <h4 class="fw-bold text-dark mb-4 text-uppercase" style="letter-spacing:.5px;">
        Profit on Sale Invoices
    </h4>

    {{-- ── Filters Row ── --}}
    <div class="d-flex align-items-center gap-3 mb-3 flex-wrap">
        <div class="d-flex align-items-center gap-2">
            <label class="text-secondary fw-medium mb-0" style="font-size:13px;">FILTERS</label>
            <input type="text" id="bwpPartyFilter"
                   class="form-control form-control-sm"
                   placeholder="Party filter"
                   style="width:180px; border-color:#e5e7eb;">
        </div>
    </div>

    {{-- ── Loading ── --}}
    <div id="bwpLoading" class="text-center py-5 d-none">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="text-muted mt-2">Loading...</p>
    </div>

    {{-- ── Empty State ── --}}
    <div id="bwpEmpty" class="text-center py-5 d-none">
        <i class="fa-regular fa-folder-open text-secondary" style="font-size:48px;"></i>
        <p class="text-muted mt-3">No sale invoices found for this period.</p>
    </div>

    {{-- ── Data Table ── --}}
    <div class="table-responsive" id="bwpTableWrap">
        <table class="w-100" style="border-collapse:collapse;" id="bwpTable">
            <thead style="background:#f3f4f6;">
                <tr style="border-bottom:2px solid #e5e7eb;">
                    <th class="bwp-th" style="width:40px; padding:12px 16px;"></th>
                    <th class="bwp-th bwp-sortable" data-col="invoice_date"
                        style="padding:12px 16px; font-size:13px; font-weight:600; color:#6b7280;
                               text-align:left; border-right:1px solid #e5e7eb; cursor:pointer; white-space:nowrap;">
                        DATE
                        <span class="bwp-sort-icon ms-1"><i class="fa-solid fa-sort" style="font-size:10px;color:#9ca3af;"></i></span>
                    </th>
                    <th class="bwp-th bwp-sortable" data-col="bill_number"
                        style="padding:12px 16px; font-size:13px; font-weight:600; color:#6b7280;
                               text-align:left; border-right:1px solid #e5e7eb; cursor:pointer; white-space:nowrap;">
                        INVOICE NO
                        <span class="bwp-sort-icon ms-1"><i class="fa-solid fa-sort" style="font-size:10px;color:#9ca3af;"></i></span>
                    </th>
                    <th class="bwp-th bwp-sortable" data-col="party_name"
                        style="padding:12px 16px; font-size:13px; font-weight:600; color:#6b7280;
                               text-align:left; border-right:1px solid #e5e7eb; cursor:pointer; white-space:nowrap;">
                        PARTY
                        <span class="bwp-sort-icon ms-1"><i class="fa-solid fa-sort" style="font-size:10px;color:#9ca3af;"></i></span>
                    </th>
                    <th class="bwp-th bwp-sortable" data-col="total_amount"
                        style="padding:12px 16px; font-size:13px; font-weight:600; color:#6b7280;
                               text-align:right; border-right:1px solid #e5e7eb; cursor:pointer; white-space:nowrap;">
                        TOTAL SALE AMOUNT
                        <span class="bwp-sort-icon ms-1"><i class="fa-solid fa-sort" style="font-size:10px;color:#9ca3af;"></i></span>
                    </th>
                    <th class="bwp-th bwp-sortable" data-col="profit"
                        style="padding:12px 16px; font-size:13px; font-weight:600; color:#6b7280;
                               text-align:right; border-right:1px solid #e5e7eb; cursor:pointer; white-space:nowrap;">
                        PROFIT (+) / LOSS (-)
                        <span class="bwp-sort-icon ms-1"><i class="fa-solid fa-sort" style="font-size:10px;color:#9ca3af;"></i></span>
                    </th>
                    <th class="bwp-th"
                        style="padding:12px 16px; font-size:13px; font-weight:600; color:#6b7280;
                               text-align:left; white-space:nowrap;">
                        DETAILS
                    </th>
                </tr>
            </thead>
            <tbody id="bwpTbody" style="background:#fff;"></tbody>
            <tfoot id="bwpTfoot" style="background:#f9fafb; display:none;">
                <tr style="border-top:2px solid #e5e7eb;">
                    <td colspan="3"
                        style="padding:16px; font-size:14px; font-weight:700; color:#1f2937; text-align:left;">
                        Summary
                    </td>
                    <td style="padding:16px;"></td>
                    <td style="padding:16px; font-size:14px; font-weight:700; color:#1f2937; text-align:right;"
                        id="bwpTotalSale">Rs 0.00</td>
                    <td style="padding:16px; font-size:14px; font-weight:700; text-align:right;"
                        id="bwpTotalProfit">Rs 0.00</td>
                    <td style="padding:16px;"></td>
                </tr>
                <tr>
                    <td colspan="4"
                        style="padding:4px 16px 12px; font-size:13px; color:#6b7280;">
                        Total Sale Amount: <span class="fw-medium text-dark" id="bwpSummaryTotalSale">Rs 0.00</span>
                        &nbsp;&nbsp;&nbsp;
                        Total Profit(+)/Loss(-): <span class="fw-medium" id="bwpSummaryTotalProfit">Rs 0.00</span>
                    </td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>

</div>{{-- /tab-BillWiseProfit --}}


{{-- ════════════════════════════════════════════════════════
     MODAL — Bill Detail
════════════════════════════════════════════════════════ --}}
<div id="bwpModal" style="
    display:none;
    position:fixed; inset:0; z-index:9999;
    background:rgba(0,0,0,0.45);
    align-items:center; justify-content:center;">

    <div style="
        background:#fff;
        border-radius:10px;
        width:660px; max-width:95vw;
        max-height:90vh;
        overflow-y:auto;
        box-shadow:0 8px 40px rgba(0,0,0,0.18);
        padding:0;
        position:relative;">

        <div style="padding:20px 24px 16px; border-bottom:1px solid #e5e7eb;
                    display:flex; align-items:center; justify-content:space-between;">
            <h5 class="mb-0 fw-bold text-dark" id="bwpModalTitle" style="font-size:16px;">Invoice</h5>
            <button id="bwpModalClose"
                    style="background:none;border:none;cursor:pointer;padding:4px;
                           width:32px;height:32px;border-radius:50%;
                           display:flex;align-items:center;justify-content:center;
                           border:2px solid #e5e7eb; color:#6b7280; font-size:18px; line-height:1;">
                &times;
            </button>
        </div>

        <div style="padding:20px 24px;">
            <p class="fw-bold text-dark mb-3" style="font-size:15px;">Cost Calculation</p>

            <div style="border:1px solid #e5e7eb; border-radius:6px; overflow:hidden; margin-bottom:20px;">
                <table class="w-100" style="border-collapse:collapse;">
                    <thead style="background:#f3f4f6;">
                        <tr>
                            <th style="padding:10px 14px;font-size:12px;font-weight:600;color:#6b7280;text-align:left;border-right:1px solid #e5e7eb;">ITEM NAME</th>
                            <th style="padding:10px 14px;font-size:12px;font-weight:600;color:#6b7280;text-align:right;border-right:1px solid #e5e7eb;">QUANTITY</th>
                            <th style="padding:10px 14px;font-size:12px;font-weight:600;color:#6b7280;text-align:right;border-right:1px solid #e5e7eb;">PURCHASE PRICE</th>
                            <th style="padding:10px 14px;font-size:12px;font-weight:600;color:#6b7280;text-align:right;">TOTAL COST</th>
                        </tr>
                    </thead>
                    <tbody id="bwpModalItemsTbody">
                        <tr><td colspan="4" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>
                    </tbody>
                </table>
            </div>

            <div style="border:1px solid #e5e7eb; border-radius:6px; overflow:hidden;">
                <div style="display:flex;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:14px;color:#374151;">Sale Amount</span>
                    <span style="font-size:14px;font-weight:500;color:#1f2937;" id="bwpSumSaleAmt">Rs 0.00</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:14px;color:#374151;">Total Cost</span>
                    <span style="font-size:14px;font-weight:500;color:#1f2937;" id="bwpSumTotalCost">Rs 0.00</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:14px;color:#374151;">Tax Payable</span>
                    <span style="font-size:14px;font-weight:500;color:#1f2937;" id="bwpSumTaxPayable">Rs 0.00</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:12px 16px;border-bottom:1px solid #f3f4f6;">
                    <span style="font-size:14px;color:#374151;">TDS Receivable</span>
                    <span style="font-size:14px;font-weight:500;color:#1f2937;" id="bwpSumTdsReceivable">Rs 0.00</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:14px 16px;border-bottom:1px solid #f3f4f6;background:#f9fafb;">
                    <span style="font-size:13px;color:#6b7280;">Profit (Sale Amount - Total Cost - Tax Payable + TDS Receivable)</span>
                    <span style="font-size:14px;font-weight:700;min-width:100px;text-align:right;" id="bwpSumProfit">Rs 0.00</span>
                </div>
                <div style="display:flex;justify-content:space-between;padding:14px 16px;background:#f9fafb;">
                    <span style="font-size:13px;color:#6b7280;">Profit (Excluding Additional Charges)</span>
                    <span style="font-size:14px;font-weight:700;min-width:100px;text-align:right;" id="bwpSumProfitExcl">Rs 0.00</span>
                </div>
            </div>
        </div>
    </div>
</div>


{{-- ════════════════════════════════════════════════════════
     JAVASCRIPT — Bill Wise Profit
════════════════════════════════════════════════════════ --}}
<script>
(function () {
    const fmtNum = v => parseFloat(v || 0).toLocaleString('en-IN', {minimumFractionDigits:2, maximumFractionDigits:2});
    const fmt    = v => 'Rs ' + fmtNum(v);
    const fmtSigned = v => {
        const n = parseFloat(v || 0);
        return n < 0 ? '- Rs ' + Math.abs(n).toLocaleString('en-IN',{minimumFractionDigits:2}) : fmt(n);
    };
    const $id = id => document.getElementById(id);

    const today    = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const toYMD    = d => d.toISOString().split('T')[0];

    let allRows = [];
    let sortCol = 'invoice_date';
    let sortDir = 'asc';

    $id('bwpFrom').value = toYMD(firstDay);
    $id('bwpTo').value   = toYMD(today);

    $id('bwpApplyBtn').addEventListener('click', loadBWP);

    $id('bwpPartyFilter').addEventListener('input', function() {
        renderTable(allRows);
    });

    document.querySelectorAll('.bwp-sortable').forEach(function(th) {
        th.addEventListener('click', function() {
            const col = this.dataset.col;
            if (sortCol === col) { sortDir = sortDir === 'asc' ? 'desc' : 'asc'; }
            else { sortCol = col; sortDir = 'asc'; }
            updateSortIcons();
            renderTable(allRows);
        });
    });

    $id('bwpModalClose').addEventListener('click', closeModal);
    $id('bwpModal').addEventListener('click', function(e) { if (e.target === this) closeModal(); });

    function closeModal() { $id('bwpModal').style.display = 'none'; }

    /* ── Excel export ── */
    $id('bwpExcelBtn').addEventListener('click', function() {
        const from = $id('bwpFrom').value;
        const to   = $id('bwpTo').value;
        const a = document.createElement('a');
        a.href = '/dashboard/reports/bill-wise-profit/export?from=' + from + '&to=' + to;
        a.download = 'bill_wise_profit.csv';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    });

    /* ── Load main table ── */
    function loadBWP() {
        const from = $id('bwpFrom').value;
        const to   = $id('bwpTo').value;

        $id('bwpLoading').classList.remove('d-none');
        $id('bwpEmpty').classList.add('d-none');
        closeModal();

        fetch('/dashboard/reports/bill-wise-profit?from=' + from + '&to=' + to, {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            $id('bwpLoading').classList.add('d-none');
            if (data.success) {
                allRows = data.rows || [];
                renderTable(allRows);
            }
        })
        .catch(err => {
            $id('bwpLoading').classList.add('d-none');
            console.error('BWP fetch error:', err);
        });
    }

    /* ── Render table ── */
    function renderTable(rows) {
        const partyQ = ($id('bwpPartyFilter').value || '').toLowerCase().trim();
        let filtered = partyQ ? rows.filter(r => (r.party_name || '').toLowerCase().includes(partyQ)) : rows;

        filtered = [...filtered].sort(function(a, b) {
            let va = a[sortCol], vb = b[sortCol];
            if (sortCol === 'invoice_date') { va = new Date(va); vb = new Date(vb); }
            else if (['total_amount','profit'].includes(sortCol)) { va = parseFloat(va||0); vb = parseFloat(vb||0); }
            else { va = String(va||'').toLowerCase(); vb = String(vb||'').toLowerCase(); }
            return sortDir === 'asc' ? (va > vb ? 1 : va < vb ? -1 : 0) : (va < vb ? 1 : va > vb ? -1 : 0);
        });

        const tbody = $id('bwpTbody');
        tbody.innerHTML = '';

        if (!filtered.length) {
            $id('bwpEmpty').classList.remove('d-none');
            $id('bwpTfoot').style.display = 'none';
            return;
        }

        $id('bwpEmpty').classList.add('d-none');
        $id('bwpTfoot').style.display = '';

        let totalSale = 0, totalProfit = 0;

        filtered.forEach(function(row, idx) {
            const profit  = parseFloat(row.profit || 0);
            const saleAmt = parseFloat(row.total_amount || 0);
            totalSale   += saleAmt;
            totalProfit += profit;

            const tr = document.createElement('tr');
            tr.style.borderBottom = '1px solid #e5e7eb';
            tr.innerHTML = `
                <td style="padding:14px 16px;font-size:14px;color:#9ca3af;">${idx + 1}</td>
                <td style="padding:14px 16px;font-size:14px;color:#1f2937;border-right:1px solid #e5e7eb;">${row.invoice_date || '-'}</td>
                <td style="padding:14px 16px;font-size:14px;color:#1f2937;border-right:1px solid #e5e7eb;">${row.bill_number || '-'}</td>
                <td style="padding:14px 16px;font-size:14px;color:#1f2937;border-right:1px solid #e5e7eb;">${row.party_name || '-'}</td>
                <td style="padding:14px 16px;font-size:14px;color:#1f2937;text-align:right;border-right:1px solid #e5e7eb;">${fmt(saleAmt)}</td>
                <td style="padding:14px 16px;font-size:14px;text-align:right;border-right:1px solid #e5e7eb;" class="${profit >= 0 ? 'text-success' : 'text-danger'} fw-medium">${fmtSigned(profit)}</td>
                <td style="padding:14px 16px;font-size:14px;text-align:left;">
                    <a href="#" class="text-primary text-decoration-none bwp-show-detail fw-medium"
                       data-sale-id="${row.id}" data-bill="${row.bill_number || ''}"
                       data-party="${row.party_name || ''}" data-sale-amt="${saleAmt}"
                       style="font-size:13px;">Show &gt;</a>
                </td>
            `;
            tbody.appendChild(tr);
        });

        const profitClass = totalProfit >= 0 ? 'text-success' : 'text-danger';
        $id('bwpTotalSale').textContent            = fmt(totalSale);
        $id('bwpTotalProfit').textContent          = fmtSigned(totalProfit);
        $id('bwpTotalProfit').className            = profitClass + ' fw-bold';
        $id('bwpSummaryTotalSale').textContent     = fmt(totalSale);
        $id('bwpSummaryTotalProfit').textContent   = fmtSigned(totalProfit);
        $id('bwpSummaryTotalProfit').className     = profitClass + ' fw-medium';

        tbody.querySelectorAll('.bwp-show-detail').forEach(function(a) {
            a.addEventListener('click', function(e) {
                e.preventDefault();
                showModal(this.dataset.saleId, this.dataset.bill, this.dataset.party, parseFloat(this.dataset.saleAmt || 0));
            });
        });
    }

    /* ── Show modal ── */
    function showModal(saleId, billNumber, partyName, saleAmount) {
        $id('bwpModalTitle').textContent = 'Invoice #' + (billNumber || saleId) + (partyName ? ' - ' + partyName : '');
        $id('bwpModalItemsTbody').innerHTML = '<tr><td colspan="4" class="text-center py-3"><div class="spinner-border spinner-border-sm text-primary"></div></td></tr>';
        $id('bwpSumSaleAmt').textContent       = fmt(saleAmount);
        $id('bwpSumTotalCost').textContent     = '...';
        $id('bwpSumTaxPayable').textContent    = 'Rs 0.00';
        $id('bwpSumTdsReceivable').textContent = 'Rs 0.00';
        $id('bwpSumProfit').textContent        = '...';
        $id('bwpSumProfitExcl').textContent    = '...';
        $id('bwpModal').style.display = 'flex';

        fetch('/dashboard/reports/bill-wise-profit/' + saleId + '/items', {
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (!data.success) {
                $id('bwpModalItemsTbody').innerHTML = '<tr><td colspan="4" class="text-center text-muted py-3">No details found.</td></tr>';
                return;
            }

            $id('bwpModalTitle').textContent = 'Invoice #' + (data.bill_number || saleId) + (data.party_name ? ' - ' + data.party_name : '');

            const items = data.items || [];
            const tbody = $id('bwpModalItemsTbody');
            tbody.innerHTML = '';
            let totalCost = 0;

            items.forEach(function(item) {
                const costAmt = parseFloat(item.cost_amount || 0);
                totalCost += costAmt;
                const tr = document.createElement('tr');
                tr.style.borderBottom = '1px solid #f3f4f6';
                tr.innerHTML = `
                    <td style="padding:10px 14px;font-size:14px;color:#1f2937;border-right:1px solid #e5e7eb;">${item.item_name || '-'}</td>
                    <td style="padding:10px 14px;font-size:14px;color:#1f2937;text-align:right;border-right:1px solid #e5e7eb;">${item.quantity || 0}</td>
                    <td style="padding:10px 14px;font-size:14px;color:#1f2937;text-align:right;border-right:1px solid #e5e7eb;">${fmt(item.purchase_price)}</td>
                    <td style="padding:10px 14px;font-size:14px;color:#1f2937;text-align:right;">${fmt(costAmt)}</td>
                `;
                tbody.appendChild(tr);
            });

            const taxPayable    = parseFloat(data.tax_payable    || 0);
            const tdsReceivable = parseFloat(data.tds_receivable || 0);
            const profit        = saleAmount - totalCost - taxPayable + tdsReceivable;
            const profitColor   = profit >= 0 ? '#16a34a' : '#dc2626';

            $id('bwpSumTotalCost').textContent     = fmt(totalCost);
            $id('bwpSumTaxPayable').textContent    = fmt(taxPayable);
            $id('bwpSumTdsReceivable').textContent = fmt(tdsReceivable);
            $id('bwpSumProfit').textContent        = (profit < 0 ? '- ' : '') + fmt(Math.abs(profit));
            $id('bwpSumProfit').style.color        = profitColor;
            $id('bwpSumProfitExcl').textContent    = (profit < 0 ? '- ' : '') + fmt(Math.abs(profit));
            $id('bwpSumProfitExcl').style.color    = profitColor;
        })
        .catch(err => {
            $id('bwpModalItemsTbody').innerHTML = '<tr><td colspan="4" class="text-center text-danger py-3">Error loading details.</td></tr>';
            console.error(err);
        });
    }

    /* ── Sort icons ── */
    function updateSortIcons() {
        document.querySelectorAll('.bwp-sortable').forEach(function(th) {
            const icon = th.querySelector('.bwp-sort-icon i');
            if (!icon) return;
            if (th.dataset.col === sortCol) {
                icon.className = sortDir === 'asc' ? 'fa-solid fa-sort-up text-primary' : 'fa-solid fa-sort-down text-primary';
            } else {
                icon.className = 'fa-solid fa-sort';
                icon.style.color = '#9ca3af';
            }
        });
    }

    /* ── Auto-load on tab show ── */
    document.addEventListener('DOMContentLoaded', function() {
        const tabEl = $id('tab-BillWiseProfit');
        if (tabEl && !tabEl.classList.contains('d-none')) { loadBWP(); }

        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(m) {
                if (m.target.id === 'tab-BillWiseProfit' &&
                    !m.target.classList.contains('d-none') &&
                    !m.target.dataset.loaded) {
                    m.target.dataset.loaded = '1';
                    loadBWP();
                }
            });
        });
        if (tabEl) observer.observe(tabEl, { attributes: true, attributeFilter: ['class'] });
    });

})();
</script>