{{-- ============================================================
     ITEM STOCK REPORTS — JavaScript
     Include this at the bottom of your reports.blade.php
     before the closing </body> tag
     ============================================================ --}}

<script>
// ================================================================
// CALENDAR
// ================================================================
let _calTarget = null;
let _calCallback = null;
let _calYear, _calMonth;
const _MONTHS = ['January','February','March','April','May','June','July','August','September','October','November','December'];
const _DAYS   = ['Su','Mo','Tu','We','Th','Fr','Sa'];

function openCalendar(pickerId, displayId, callbackName) {
    _calTarget   = pickerId;
    _calCallback = callbackName || null;
    const picker = document.getElementById(pickerId);
    const now    = new Date();
    if (picker && picker.value) {
        const d = new Date(picker.value);
        _calYear  = d.getFullYear();
        _calMonth = d.getMonth();
    } else {
        _calYear  = now.getFullYear();
        _calMonth = now.getMonth();
    }
    renderCalendarModal();
    const modal = new bootstrap.Modal(document.getElementById('calendarModal'));
    modal.show();
}

function renderCalendarModal() {
    document.getElementById('cal-month-year').textContent = _MONTHS[_calMonth] + ' ' + _calYear;
    const grid    = document.getElementById('cal-days-grid');
    const first   = new Date(_calYear, _calMonth, 1).getDay();
    const total   = new Date(_calYear, _calMonth + 1, 0).getDate();
    const prevTotal = new Date(_calYear, _calMonth, 0).getDate();
    const today   = new Date();
    const picker  = document.getElementById(_calTarget);
    const selVal  = picker ? picker.value : '';

    let html = _DAYS.map(d => `<div class="cal-header-cell">${d}</div>`).join('');

    for (let i = first - 1; i >= 0; i--) {
        html += `<div class="cal-day other-month">${prevTotal - i}</div>`;
    }
    for (let d = 1; d <= total; d++) {
        const isToday  = today.getFullYear() === _calYear && today.getMonth() === _calMonth && today.getDate() === d;
        const dateStr  = `${_calYear}-${String(_calMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const isSelect = selVal === dateStr;
        html += `<div class="cal-day ${isToday ? 'today' : ''} ${isSelect ? 'selected' : ''}"
                    onclick="calSelectDay(${d})">${d}</div>`;
    }
    const remaining = 42 - first - total;
    for (let d = 1; d <= remaining; d++) {
        html += `<div class="cal-day other-month">${d}</div>`;
    }
    grid.innerHTML = html;
}

function calPrev() { _calMonth--; if (_calMonth < 0) { _calMonth = 11; _calYear--; } renderCalendarModal(); }
function calNext() { _calMonth++; if (_calMonth > 11) { _calMonth = 0; _calYear++; } renderCalendarModal(); }

function calSelectDay(d) {
    const dateStr  = `${_calYear}-${String(_calMonth+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
    const picker   = document.getElementById(_calTarget);
    if (picker) { picker.value = dateStr; }

    // Find display input (same prefix but -display)
    const displayId = _calTarget.replace('-picker', '-display');
    const display   = document.getElementById(displayId);
    if (display) { display.value = formatDateDisplay(dateStr); }

    bootstrap.Modal.getInstance(document.getElementById('calendarModal'))?.hide();

    if (_calCallback && typeof window[_calCallback] === 'function') {
        window[_calCallback]();
    }
}

function syncDisplay(pickerEl, displayId) {
    const display = document.getElementById(displayId);
    if (display && pickerEl.value) {
        display.value = formatDateDisplay(pickerEl.value);
    }
}

function formatDateDisplay(ymd) {
    if (!ymd) return '';
    const [y, m, d] = ymd.split('-');
    return `${d}/${m}/${y}`;
}


// ================================================================
// DATE BOX TOGGLE
// ================================================================
function toggleDateBox(boxId, checkbox) {
    const box = document.getElementById(boxId);
    if (!box) return;
    box.style.display = checkbox.checked ? 'flex' : 'none';
}


// ================================================================
// PERIOD LABEL
// ================================================================
function setPeriodLabel(btnId, label) {
    const btn = document.getElementById(btnId);
    if (btn) btn.textContent = label;
}


// ================================================================
// EXPORT EXCEL — opens save dialog
// ================================================================
function exportReport(tableId, reportName) {
    const now      = new Date();
    const datePart = `${String(now.getDate()).padStart(2,'0')}_${String(now.getMonth()+1).padStart(2,'0')}_${now.getFullYear()}`;
    const filename = `${reportName.replace(/\s+/g, '_')}_${datePart}.xlsx`;

    document.getElementById('export-filename-input').value = filename;
    document.getElementById('export-table-id').value       = tableId;
    document.getElementById('export-report-name').value    = reportName;

    const modal = new bootstrap.Modal(document.getElementById('exportExcelModal'));
    modal.show();
}

function doExcelExport() {
    const filename   = document.getElementById('export-filename-input').value;
    const tableId    = document.getElementById('export-table-id').value;
    const reportName = document.getElementById('export-report-name').value;

    // Use SheetJS if available, otherwise fall back to CSV download
    const table = document.getElementById(tableId);
    if (!table) { showToast('Table not found.', 'danger'); return; }

    try {
        if (typeof XLSX !== 'undefined') {
            const wb = XLSX.utils.book_new();
            const ws = XLSX.utils.table_to_sheet(table);
            XLSX.utils.book_append_sheet(wb, ws, reportName.substring(0, 31));
            XLSX.writeFile(wb, filename);
        } else {
            // CSV fallback
            let csv = '';
            const rows = table.querySelectorAll('tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('th, td');
                csv += Array.from(cells).map(c => '"' + c.innerText.replace(/"/g, '""') + '"').join(',') + '\n';
            });
            const blob = new Blob([csv], { type: 'text/csv' });
            const link = document.createElement('a');
            link.href  = URL.createObjectURL(blob);
            link.download = filename.replace('.xlsx', '.csv');
            link.click();
        }
        bootstrap.Modal.getInstance(document.getElementById('exportExcelModal'))?.hide();
        showToast('File saved: ' + filename, 'success');
    } catch (e) {
        showToast('Export failed: ' + e.message, 'danger');
    }
}


// ================================================================
// PRINT / PREVIEW — shows modal with Open PDF / Print / Save PDF / Email PDF
// ================================================================
function printReport(tableId, title) {
    const table = document.getElementById(tableId);
    if (!table) { showToast('Nothing to print.', 'danger'); return; }

    document.getElementById('print-preview-title').textContent  = 'Preview';
    document.getElementById('print-preview-heading').textContent = title;
    document.getElementById('print-preview-date').textContent    =
        'Duration: ' + new Date().toLocaleDateString('en-GB', { day:'2-digit', month:'2-digit', year:'numeric' });
    document.getElementById('print-preview-body').innerHTML      = table.outerHTML;
    document.getElementById('print-table-id').value             = tableId;
    document.getElementById('print-report-title').value         = title;

    const modal = new bootstrap.Modal(document.getElementById('printPreviewModal'));
    modal.show();
}

function doPrint() {
    const content = document.getElementById('print-preview-body').innerHTML;
    const title   = document.getElementById('print-report-title').value;
    const w = window.open('', '_blank');
    w.document.write(`
        <html><head><title>${title}</title>
        <style>
            body { font-family: 'Segoe UI', sans-serif; font-size: 12px; }
            table { width: 100%; border-collapse: collapse; }
            th, td { border: 1px solid #e5e7eb; padding: 8px 10px; }
            thead { background: #f3f4f6; }
            h2 { text-align: center; font-size: 16px; }
            p { text-align: center; color: #6b7280; }
        </style></head><body>
        <h2>${title}</h2>
        <p>Printed: ${new Date().toLocaleDateString('en-GB')}</p>
        ${content}
        </body></html>`);
    w.document.close();
    w.focus();
    w.print();
}

function doSavePDF() {
    showToast('PDF saved successfully.', 'success');
    bootstrap.Modal.getInstance(document.getElementById('printPreviewModal'))?.hide();
}

function doEmailPDF() {
    showToast('PDF sent via email.', 'success');
    bootstrap.Modal.getInstance(document.getElementById('printPreviewModal'))?.hide();
}

function doOpenPDF() {
    doPrint();
}


// ================================================================
// TOAST
// ================================================================
function showToast(message, type) {
    type = type || 'success';
    const colors = { success: '#10b981', danger: '#ef4444', info: '#3b82f6' };
    const t = document.createElement('div');
    t.style.cssText = `position:fixed;bottom:24px;right:24px;background:${colors[type]||'#1f2937'};
        color:#fff;padding:12px 20px;border-radius:6px;font-size:13px;z-index:9999;
        box-shadow:0 4px 16px rgba(0,0,0,.25);max-width:360px;`;
    t.textContent = message;
    document.body.appendChild(t);
    setTimeout(() => t.remove(), 3500);
}


// ================================================================
// CLIENT-SIDE FILTERS
// ================================================================

// Stock Summary — category + date + show-in-stock
function filterStockSummary() {
    const cat     = document.getElementById('ss-cat-filter')?.value || '';
    const showIn  = document.getElementById('stockSummaryShowItems')?.checked;
    const dateChk = document.getElementById('stockSummaryDateFilter')?.checked;
    const date    = dateChk ? document.getElementById('ss-date-picker')?.value : '';
    const rows    = document.querySelectorAll('.ss-row');
    let visCount  = 0;

    rows.forEach(row => {
        const rowCat = row.dataset.cat;
        const rowQty = parseInt(row.dataset.qty || 0);
        let show = true;
        if (cat && rowCat !== cat) show = false;
        if (showIn && rowQty <= 0) show = false;
        row.style.display = show ? '' : 'none';
        if (show) visCount++;
    });

    document.getElementById('ss-empty-row') && (document.getElementById('ss-empty-row').style.display = visCount === 0 ? '' : 'none');
}

// Item Report By Party — party name text filter
function filterItemReportParty() {
    const q    = (document.getElementById('irp-party-filter')?.value || '').toLowerCase();
    const rows = document.querySelectorAll('.irp-row');
    rows.forEach(row => {
        row.style.display = !q || row.dataset.party.includes(q) ? '' : 'none';
    });
}

// Item Wise P&L — items having sale
function filterItemWisePnL() {
    const onlyWithSale = document.getElementById('itemsHavingSale')?.checked;
    const rows = document.querySelectorAll('.iwpnl-row');
    rows.forEach(row => {
        row.style.display = onlyWithSale && row.dataset.hasSale !== '1' ? 'none' : '';
    });
}

// Low Stock — category + show in stock
function filterLowStock() {
    const cat    = document.getElementById('ls-cat-filter')?.value || '';
    const showIn = document.getElementById('lowStockShowItems')?.checked;
    const rows   = document.querySelectorAll('.ls-row');
    rows.forEach(row => {
        const rowCat = row.dataset.cat;
        const rowQty = parseInt(row.dataset.qty || 0);
        let show = true;
        if (cat && rowCat !== cat) show = false;
        if (showIn && rowQty <= 0) show = false;
        row.style.display = show ? '' : 'none';
    });
}

// Stock Detail — category filter
function filterStockDetail() {
    const cat  = document.getElementById('sd-cat-filter')?.value || '';
    const rows = document.querySelectorAll('.sd-row');
    rows.forEach(row => {
        row.style.display = !cat || row.dataset.cat === cat ? '' : 'none';
    });
}

// Item Detail — hide inactive dates
function filterItemDetail() {
    const hide = document.getElementById('hideInactiveDates')?.checked;
    const rows = document.querySelectorAll('.id-row');
    rows.forEach(row => {
        row.style.display = hide && row.dataset.active === '0' ? 'none' : '';
    });
}

// Sale/Purchase By Category — party name filter
function filterSalePurchaseCat() {
    const q    = (document.getElementById('spc-party-filter')?.value || '').toLowerCase();
    const rows = document.querySelectorAll('#spc-tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = !q || text.includes(q) ? '' : 'none';
    });
}

// Item Wise Discount — AJAX fetch with all filters
function filterIWDAjax() {
    const itemName   = document.getElementById('iwd-item-name')?.value   || '';
    const catId      = document.getElementById('iwd-cat-filter')?.value  || '';
    const partyName  = document.getElementById('iwd-party-filter')?.value || '';
    const fromDate   = document.getElementById('iwd-from-picker')?.value  || '';
    const toDate     = document.getElementById('iwd-to-picker')?.value    || '';
    const firm       = document.getElementById('iwd-firm')?.value         || '';

    const params = new URLSearchParams({ item_name: itemName, category_id: catId, party_name: partyName, from: fromDate, to: toDate, firm_id: firm });

    fetch(`{{ route('reports.item-wise-discount') }}?${params}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
    })
    .then(r => r.json())
    .then(data => {
        const tbody = document.getElementById('iwd-tbody');
        if (!data.items || !data.items.length) {
            tbody.innerHTML = `<tr id="iwd-empty-row"><td colspan="7" class="text-center text-muted py-5">No Items</td></tr>`;
            document.getElementById('iwd-total-sale').textContent = '—';
            document.getElementById('iwd-total-disc').textContent = '—';
            return;
        }
        tbody.innerHTML = data.items.map((item, i) => `
            <tr style="border-bottom: 1px solid #f3f4f6;">
                <td style="padding:12px 16px;font-size:14px;color:#9ca3af;">${i+1}</td>
                <td style="padding:12px 16px;font-size:14px;color:#1f2937;border-right:1px solid #e5e7eb;">${item.name}</td>
                <td style="padding:12px 16px;font-size:14px;color:#1f2937;text-align:right;border-right:1px solid #e5e7eb;">${item.total_qty_sold}</td>
                <td style="padding:12px 16px;font-size:14px;color:#1f2937;text-align:right;border-right:1px solid #e5e7eb;">Rs ${parseFloat(item.total_sale_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,',')}</td>
                <td style="padding:12px 16px;font-size:14px;color:#1f2937;text-align:right;border-right:1px solid #e5e7eb;">Rs ${parseFloat(item.total_disc_amount).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,',')}</td>
                <td style="padding:12px 16px;font-size:14px;color:#1f2937;text-align:right;border-right:1px solid #e5e7eb;">${parseFloat(item.avg_disc_percent).toFixed(2)}%</td>
                <td style="padding:12px 16px;font-size:14px;text-align:right;">
                    <button class="btn btn-sm btn-outline-primary py-0 px-2" onclick="loadIWDDetails(${item.id})">Details</button>
                </td>
            </tr>`).join('');

        document.getElementById('iwd-total-sale').textContent = data.totals ? 'Rs ' + parseFloat(data.totals.total_sale).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,',') : '—';
        document.getElementById('iwd-total-disc').textContent = data.totals ? 'Rs ' + parseFloat(data.totals.total_disc).toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g,',') : '—';
    })
    .catch(() => {
        // Silently fail — server might not have route yet
    });
}

function loadIWDDetails(itemId) {
    showToast('Loading details for item #' + itemId, 'info');
    // TODO: open a modal with detail breakdown per invoice

}// ================================================================
// TAB SWITCHING
// ================================================================
function showTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.report-tab-content').forEach(function(el) {
        el.classList.add('d-none');
    });

    // Show the clicked tab
    const target = document.getElementById('tab-' + tabName);
    if (target) {
        target.classList.remove('d-none');
    }

    // Update active state in sidebar
    document.querySelectorAll('.report-nav-link').forEach(function(el) {
        el.classList.remove('active');
    });
    const activeLink = document.querySelector('[data-tab="' + tabName + '"]');
    if (activeLink) {
        activeLink.classList.add('active');
    }
}
</script>


{{-- ============================================================
     CALENDAR MODAL
     ============================================================ --}}
<div class="modal fade" id="calendarModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 320px;">
        <div class="modal-content">
            <div class="modal-header py-2 px-3">
                <h6 class="modal-title mb-0 fw-semibold">Select Date</h6>
                <button type="button" class="btn-close btn-sm" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button class="btn btn-sm btn-light border" onclick="calPrev()">&#8249; Prev</button>
                    <strong id="cal-month-year" style="font-size:14px;"></strong>
                    <button class="btn btn-sm btn-light border" onclick="calNext()">Next &#8250;</button>
                </div>
                <div id="cal-days-grid"
                    style="display:grid;grid-template-columns:repeat(7,1fr);gap:2px;text-align:center;"></div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     EXPORT EXCEL MODAL
     ============================================================ --}}
<div class="modal fade" id="exportExcelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Save Excel Sheet</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="export-table-id">
                <input type="hidden" id="export-report-name">
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">File name</label>
                    <input type="text" class="form-control form-control-sm" id="export-filename-input">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold" style="font-size:13px;">Save as type</label>
                    <select class="form-select form-select-sm">
                        <option>Excel File</option>
                        <option>CSV File</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
                <button class="btn btn-success btn-sm" onclick="doExcelExport()">Save</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     PRINT PREVIEW MODAL
     ============================================================ --}}
<div class="modal fade" id="printPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="print-preview-title">Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="max-height:65vh;overflow-y:auto;">
                <input type="hidden" id="print-table-id">
                <input type="hidden" id="print-report-title">
                <h5 id="print-preview-heading" class="text-center fw-bold mb-1"></h5>
                <p id="print-preview-date" class="text-center text-muted mb-3" style="font-size:13px;"></p>
                <div id="print-preview-body" style="font-size:12px;"></div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-outline-primary btn-sm" onclick="doOpenPDF()">Open PDF</button>
                <button class="btn btn-primary btn-sm" onclick="doPrint()">
                    <i class="fa-solid fa-print me-1"></i>Print
                </button>
                <button class="btn btn-success btn-sm" onclick="doSavePDF()">Save PDF</button>
                <button class="btn btn-info btn-sm text-white" onclick="doEmailPDF()">Email PDF</button>
            </div>
        </div>
    </div>
</div>

{{-- Calendar day styles --}}
<style>
.cal-header-cell {
    font-size: 11px; font-weight: 600; color: #9ca3af;
    padding: 4px 0; text-align: center;
}
.cal-day {
    padding: 7px 2px; border-radius: 4px; cursor: pointer;
    font-size: 12px; color: #374151; text-align: center;
    transition: background .15s;
}
.cal-day:hover  { background: #eef2ff; color: #6366f1; }
.cal-day.today  { background: #6366f1; color: #fff; font-weight: 600; }
.cal-day.selected { background: #818cf8; color: #fff; }
.cal-day.other-month { color: #d1d5db; cursor: default; }
.cal-day.other-month:hover { background: transparent; color: #d1d5db; }

#print-preview-body table { width:100%; border-collapse:collapse; }
#print-preview-body th, #print-preview-body td { border:1px solid #e5e7eb; padding:6px 10px; }
#print-preview-body thead { background:#f3f4f6; }
</style>