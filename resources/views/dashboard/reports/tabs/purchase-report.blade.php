{{-- resources/views/dashboard/reports/tabs/purchase-report.blade.php --}}

<!-- ═══════════════════════════════════════════════
     PURCHASE BILLS TAB
═══════════════════════════════════════════════ -->
<div id="tab-Purchase" class="report-tab-content d-none">

  <!-- ── Header ── -->
  <div class="pur-report-header d-flex justify-content-between align-items-center mb-3 px-1">
    <h3 class="pur-report-title mb-0">Purchase Bills</h3>
    <div class="d-flex gap-2 align-items-center">
      <button class="pur-add-btn" id="purAddPurchaseBtn">
        <i class="fa-solid fa-plus me-1"></i> Add Purchase
      </button>
      <button class="pur-icon-btn" id="purSettingsBtn" title="Settings">
        <i class="fa-solid fa-gear"></i>
      </button>
    </div>
  </div>

  <!-- ── Filter Bar ── -->
  <div class="pur-filter-bar d-flex align-items-center gap-3 mb-3">
    <span class="pur-filter-label">Filter by :</span>

    <!-- Period Selector -->
    <div class="pur-period-pill d-flex align-items-center">
      <div class="pur-period-select-wrap">
        <select id="purPeriodSelect" class="pur-period-select">
          <option value="all" selected>All Purchase Bills</option>
          <option value="this_month">This Month</option>
          <option value="last_month">Last Month</option>
          <option value="this_quarter">This Quarter</option>
          <option value="this_year">This Year</option>
          <option value="custom">Custom</option>
        </select>
        <i class="fa-solid fa-chevron-down pur-period-arrow"></i>
      </div>
      <div class="pur-period-divider"></div>
      <!-- Date Range Display with calendar icon trigger -->
      <div class="pur-date-range-display" id="purDateRangeDisplay" style="cursor:pointer;" title="Click to set custom date range">
        <span class="pur-between-label">Between</span>
        <span id="purDateFrom">01/01/2000</span>
        <span class="mx-1" style="color:#6b7280;">To</span>
        <span id="purDateTo">31/12/2099</span>
        <i class="fa-regular fa-calendar-days ms-2" style="color:#2563eb;font-size:14px;" id="purCalendarIcon"></i>
      </div>
    </div>

    <!-- Hidden Calendar Date Picker Popup -->
    <div class="pur-calendar-popup d-none" id="purCalendarPopup">
      <div class="pur-calendar-popup-inner">
        <div class="d-flex align-items-center gap-2 mb-2">
          <label style="font-size:12px;color:#6b7280;white-space:nowrap;">From</label>
          <input type="date" id="purCalFrom" class="pur-date-input">
        </div>
        <div class="d-flex align-items-center gap-2 mb-3">
          <label style="font-size:12px;color:#6b7280;white-space:nowrap;">To &nbsp;&nbsp;&nbsp;</label>
          <input type="date" id="purCalTo" class="pur-date-input">
        </div>
        <div class="d-flex gap-2 justify-content-end">
          <button class="pur-col-clear-btn" id="purCalCancel">Cancel</button>
          <button class="pur-col-apply-btn" id="purCalApply">Apply</button>
        </div>
      </div>
    </div>

    <!-- Custom Date Inputs (legacy, hidden) -->
    <div class="pur-custom-dates d-none" id="purCustomDates">
      <input type="date" id="purFromDate" class="pur-date-input">
      <span class="mx-1 text-muted">To</span>
      <input type="date" id="purToDate" class="pur-date-input">
      <button class="pur-apply-dates-btn" id="purApplyDates">Apply</button>
    </div>

    <!-- Firms Filter -->
    <div class="pur-firm-select-wrap">
      <select id="purFirmSelect" class="pur-firm-select">
        <option value="all">All Firms</option>
      </select>
      <i class="fa-solid fa-chevron-down pur-period-arrow"></i>
    </div>

    <!-- Spacer -->
    <div class="ms-auto d-flex gap-2">
      <button class="pur-icon-btn" id="purExcelTopBtn" title="Export to Excel">
        <i class="fa-regular fa-file-excel text-success"></i>
      </button>
      <button class="pur-icon-btn" id="purPrintTopBtn" title="Print">
        <i class="fa-solid fa-print"></i>
      </button>
    </div>
  </div>

  <!-- ── Summary Cards ── -->
  <div class="pur-summary-row mb-4 d-flex align-items-center gap-3 flex-wrap">
    <div class="pur-stat-card pur-stat-paid">
      <div class="pur-stat-label">Paid</div>
      <div class="pur-stat-amount" id="purTotalPaid">Rs 0.00</div>
    </div>
    <div class="pur-stat-operator">+</div>
    <div class="pur-stat-card pur-stat-unpaid">
      <div class="pur-stat-label">Unpaid</div>
      <div class="pur-stat-amount" id="purTotalUnpaid">Rs 0.00</div>
    </div>
    <div class="pur-stat-operator">=</div>
    <div class="pur-stat-card pur-stat-total">
      <div class="pur-stat-label">Total</div>
      <div class="pur-stat-amount" id="purTotalAmount">Rs 0.00</div>
    </div>
  </div>

  <!-- ── Transactions Card ── -->
  <div class="pur-txn-card pur-txn-card-fullheight">

    <!-- Card Header -->
    <div class="pur-txn-card-header">
      <h5 class="pur-txn-title">Transactions</h5>
      <div class="pur-txn-actions">
        <button class="pur-txn-action-btn" id="purTxnSearchBtn" title="Search">
          <i class="fa-solid fa-magnifying-glass"></i>
        </button>
        <button class="pur-txn-action-btn" id="purTxnChartBtn" title="Chart">
          <i class="fa-solid fa-chart-simple"></i>
        </button>
        <button class="pur-txn-action-btn text-success" id="purTxnExcelBtn" title="Export to Excel">
          <i class="fa-regular fa-file-excel"></i>
        </button>
        <button class="pur-txn-action-btn" id="purTxnPrintBtn" title="Print">
          <i class="fa-solid fa-print"></i>
        </button>
      </div>
    </div>

    <!-- Search Bar -->
    <div class="pur-search-bar d-none" id="purTxnSearchBar">
      <div class="pur-search-inner">
        <i class="fa-solid fa-search" style="color:#9ca3af;"></i>
        <input type="text" id="purTxnSearchInput" class="pur-search-input" placeholder="Search transactions...">
        <button id="purTxnSearchClose" class="pur-search-close"><i class="fa-solid fa-xmark"></i></button>
      </div>
    </div>

    <!-- Chart Panel -->
    <div class="pur-chart-panel d-none" id="purTxnChartPanel">
      <div class="pur-chart-header">
        <div class="pur-chart-periods">
          <button class="pur-chart-period active" data-period="daily">Daily</button>
          <button class="pur-chart-period" data-period="weekly">Weekly</button>
          <button class="pur-chart-period" data-period="monthly">Monthly</button>
          <button class="pur-chart-period" data-period="yearly">Yearly</button>
        </div>
      </div>
      <div class="pur-chart-title">Purchase Graph</div>
      <div class="pur-chart-container">
        <canvas id="purChart"></canvas>
      </div>
    </div>

    <!-- Table -->
    <div class="table-responsive pur-table-scroll">
      <table class="pur-table" id="purTransactionsTable">
        <thead>
          <tr>
            <!-- Date -->
            <th>
              <div class="pur-th-inner">
                <span>Date</span>
                <button class="pur-th-sort" onclick="purSortBy('date', this)"><i class="fa-solid fa-sort"></i></button>
                <div class="pur-th-filter-wrap">
                  <button class="pur-th-filter-btn" onclick="purToggleColFilter(this)"><i class="fa-solid fa-filter"></i></button>
                  <div class="pur-col-filter-dropdown">
                    <div class="pur-col-filter-body">
                      <label class="pur-col-filter-label">Select Category:</label>
                      <select class="pur-col-filter-select" id="purFilterDateType">
                        <option value="equal">Equal to</option>
                        <option value="lt">Less than</option>
                        <option value="gt">Greater than</option>
                        <option value="range">Range</option>
                      </select>
                      <label class="pur-col-filter-label mt-2">Select Date:</label>
                      <input type="date" class="pur-col-filter-input" id="purFilterDateVal">
                      <div id="purFilterDateRange" class="d-none">
                        <input type="date" class="pur-col-filter-input mt-1" id="purFilterDateRangeFrom">
                        <input type="date" class="pur-col-filter-input mt-1" id="purFilterDateRangeTo">
                      </div>
                    </div>
                    <div class="pur-col-filter-actions">
                      <button class="pur-col-clear-btn" onclick="purClearColFilter('date', this)">Clear</button>
                      <button class="pur-col-apply-btn" onclick="purApplyColFilter('date', this)">Apply</button>
                    </div>
                  </div>
                </div>
              </div>
            </th>

            <!-- Invoice No -->
            <th>
              <div class="pur-th-inner">
                <span>Invoice No.</span>
                <button class="pur-th-sort" onclick="purSortBy('invoice_no', this)"><i class="fa-solid fa-sort"></i></button>
                <div class="pur-th-filter-wrap">
                  <button class="pur-th-filter-btn" onclick="purToggleColFilter(this)"><i class="fa-solid fa-filter"></i></button>
                  <div class="pur-col-filter-dropdown">
                    <div class="pur-col-filter-body">
                      <label class="pur-col-filter-label">Select Category:</label>
                      <select class="pur-col-filter-select" id="purFilterInvoiceType">
                        <option value="contains">Contains</option>
                        <option value="exact">Exact Match</option>
                      </select>
                      <label class="pur-col-filter-label mt-2">Invoice No.</label>
                      <input type="text" class="pur-col-filter-input" id="purFilterInvoiceVal" placeholder="e.g. PUR-001">
                    </div>
                    <div class="pur-col-filter-actions">
                      <button class="pur-col-clear-btn" onclick="purClearColFilter('invoice_no', this)">Clear</button>
                      <button class="pur-col-apply-btn" onclick="purApplyColFilter('invoice_no', this)">Apply</button>
                    </div>
                  </div>
                </div>
              </div>
            </th>

            <!-- Party Name -->
            <th>
              <div class="pur-th-inner">
                <span>Party Name</span>
                <button class="pur-th-sort" onclick="purSortBy('party_name', this)"><i class="fa-solid fa-sort"></i></button>
                <div class="pur-th-filter-wrap">
                  <button class="pur-th-filter-btn" onclick="purToggleColFilter(this)"><i class="fa-solid fa-filter"></i></button>
                  <div class="pur-col-filter-dropdown">
                    <div class="pur-col-filter-body">
                      <label class="pur-col-filter-label">Select Category:</label>
                      <select class="pur-col-filter-select" id="purFilterPartyType">
                        <option value="contains">Contains</option>
                        <option value="exact">Exact Match</option>
                      </select>
                      <label class="pur-col-filter-label mt-2">Party Name</label>
                      <input type="text" class="pur-col-filter-input" id="purFilterPartyVal" placeholder="Party name...">
                    </div>
                    <div class="pur-col-filter-actions">
                      <button class="pur-col-clear-btn" onclick="purClearColFilter('party_name', this)">Clear</button>
                      <button class="pur-col-apply-btn" onclick="purApplyColFilter('party_name', this)">Apply</button>
                    </div>
                  </div>
                </div>
              </div>
            </th>

            <!-- Transaction -->
            <th>
              <div class="pur-th-inner">
                <span>Transaction</span>
                <div class="pur-th-filter-wrap">
                  <button class="pur-th-filter-btn" onclick="purToggleColFilter(this)"><i class="fa-solid fa-filter"></i></button>
                  <div class="pur-col-filter-dropdown" style="min-width:200px;">
                    <div class="pur-col-filter-body">
                      <label class="pur-col-filter-checkbox"><input type="checkbox" value="Purchase" class="pur-txn-type-check"> Purchase</label>
                      <label class="pur-col-filter-checkbox"><input type="checkbox" value="Debit Note" class="pur-txn-type-check"> Debit Note</label>
                      <label class="pur-col-filter-checkbox"><input type="checkbox" value="Purchase (Invoice)" class="pur-txn-type-check"> Purchase (Invoice)</label>
                      <label class="pur-col-filter-checkbox"><input type="checkbox" value="Purchase [Cancelled]" class="pur-txn-type-check"> Purchase [Cancelled]</label>
                    </div>
                    <div class="pur-col-filter-actions">
                      <button class="pur-col-clear-btn" onclick="purClearColFilter('transaction', this)">Clear</button>
                      <button class="pur-col-apply-btn" onclick="purApplyColFilter('transaction', this)">Apply</button>
                    </div>
                  </div>
                </div>
              </div>
            </th>

            <!-- Payment Type -->
            <th>
              <div class="pur-th-inner">
                <span>Payment Type</span>
                <div class="pur-th-filter-wrap">
                  <button class="pur-th-filter-btn" onclick="purToggleColFilter(this)"><i class="fa-solid fa-filter"></i></button>
                  <div class="pur-col-filter-dropdown">
                    <div class="pur-col-filter-body">
                      <label class="pur-col-filter-checkbox"><input type="checkbox" value="Cash" class="pur-pay-type-check"> Cash</label>
                      <label class="pur-col-filter-checkbox"><input type="checkbox" value="Cheque" class="pur-pay-type-check"> Cheque</label>
                      <label class="pur-col-filter-checkbox"><input type="checkbox" value="Online" class="pur-pay-type-check"> Online</label>
                      <label class="pur-col-filter-checkbox"><input type="checkbox" value="Card" class="pur-pay-type-check"> Card</label>
                    </div>
                    <div class="pur-col-filter-actions">
                      <button class="pur-col-clear-btn" onclick="purClearColFilter('payment_type', this)">Clear</button>
                      <button class="pur-col-apply-btn" onclick="purApplyColFilter('payment_type', this)">Apply</button>
                    </div>
                  </div>
                </div>
              </div>
            </th>

            <!-- Amount -->
            <th>
              <div class="pur-th-inner">
                <span>Amount</span>
                <button class="pur-th-sort" onclick="purSortBy('amount', this)"><i class="fa-solid fa-sort"></i></button>
                <div class="pur-th-filter-wrap">
                  <button class="pur-th-filter-btn" onclick="purToggleColFilter(this)"><i class="fa-solid fa-filter"></i></button>
                  <div class="pur-col-filter-dropdown" style="min-width:220px;">
                    <div class="pur-col-filter-body">
                      <label class="pur-col-filter-label">Amount</label>
                      <div class="d-flex gap-2 mt-2">
                        <div class="flex-1">
                          <label class="pur-col-filter-label">Min</label>
                          <input type="number" class="pur-col-filter-input" id="purFilterAmountMin" placeholder="0">
                        </div>
                        <div class="flex-1">
                          <label class="pur-col-filter-label">Max</label>
                          <input type="number" class="pur-col-filter-input" id="purFilterAmountMax" placeholder="+500000">
                        </div>
                      </div>
                    </div>
                    <div class="pur-col-filter-actions">
                      <button class="pur-col-clear-btn" onclick="purClearColFilter('amount', this)">Clear</button>
                      <button class="pur-col-apply-btn" onclick="purApplyColFilter('amount', this)">Apply</button>
                    </div>
                  </div>
                </div>
              </div>
            </th>

            <!-- Balance Due -->
            <th>
              <div class="pur-th-inner">
                <span>Balance D...</span>
                <button class="pur-th-sort" onclick="purSortBy('balance', this)"><i class="fa-solid fa-sort"></i></button>
                <div class="pur-th-filter-wrap">
                  <button class="pur-th-filter-btn" onclick="purToggleColFilter(this)"><i class="fa-solid fa-filter"></i></button>
                  <div class="pur-col-filter-dropdown" style="min-width:220px;">
                    <div class="pur-col-filter-body">
                      <label class="pur-col-filter-label">Balance Range</label>
                      <div class="d-flex gap-2 mt-2">
                        <div class="flex-1">
                          <label class="pur-col-filter-label">Min</label>
                          <input type="number" class="pur-col-filter-input" id="purFilterBalanceMin" placeholder="0">
                        </div>
                        <div class="flex-1">
                          <label class="pur-col-filter-label">Max</label>
                          <input type="number" class="pur-col-filter-input" id="purFilterBalanceMax" placeholder="+500000">
                        </div>
                      </div>
                    </div>
                    <div class="pur-col-filter-actions">
                      <button class="pur-col-clear-btn" onclick="purClearColFilter('balance', this)">Clear</button>
                      <button class="pur-col-apply-btn" onclick="purApplyColFilter('balance', this)">Apply</button>
                    </div>
                  </div>
                </div>
              </div>
            </th>

            <!-- Status -->
            <th>
              <div class="pur-th-inner">
                <span>Status</span>
                <div class="pur-th-filter-wrap">
                  <button class="pur-th-filter-btn" onclick="purToggleColFilter(this)"><i class="fa-solid fa-filter"></i></button>
                  <div class="pur-col-filter-dropdown">
                    <div class="pur-col-filter-body">
                      <label class="pur-col-filter-checkbox"><input type="checkbox" value="Paid" class="pur-status-check"> Paid</label>
                      <label class="pur-col-filter-checkbox"><input type="checkbox" value="Unpaid" class="pur-status-check"> Unpaid</label>
                      <label class="pur-col-filter-checkbox"><input type="checkbox" value="Partial" class="pur-status-check"> Partial</label>
                    </div>
                    <div class="pur-col-filter-actions">
                      <button class="pur-col-clear-btn" onclick="purClearColFilter('status', this)">Clear</button>
                      <button class="pur-col-apply-btn" onclick="purApplyColFilter('status', this)">Apply</button>
                    </div>
                  </div>
                </div>
              </div>
            </th>

            <!-- Actions -->
            <th><div class="pur-th-inner"><span>Actions</span></div></th>
          </tr>
        </thead>
        <tbody id="purTxnTableBody">
          <tr id="purNoDataRow">
            <td colspan="9" class="pur-empty-state">
              <i class="fa-solid fa-spinner fa-spin" style="font-size:24px;color:#d1d5db;display:block;margin-bottom:6px;"></i>
              Loading transactions…
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="pur-pagination" id="purPagination" style="display:none;">
      <span class="pur-pagination-info" id="purPaginationInfo"></span>
      <div class="pur-pagination-btns">
        <button class="pur-page-btn" id="purPrevPage"><i class="fa-solid fa-chevron-left"></i></button>
        <span id="purPageNumbers"></span>
        <button class="pur-page-btn" id="purNextPage"><i class="fa-solid fa-chevron-right"></i></button>
      </div>
    </div>

  </div><!-- /pur-txn-card -->

</div><!-- /tab-Purchase -->


<!-- ═══════════════════════════════════════════════
     EXCEL EXPORT MODAL
═══════════════════════════════════════════════ -->
<div class="pur-modal-overlay" id="purExcelModal" style="display:none;">
  <div class="pur-modal-box">
    <div class="pur-modal-header">
      <h5 class="pur-modal-title">Select Report Options</h5>
      <button class="pur-modal-close" id="purExcelModalClose"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="pur-modal-body">
      <div class="pur-modal-columns">
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-export-col" value="date" checked> Date</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-export-col" value="item_details"> Item Details</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-export-col" value="invoice_no" checked> Invoice No.</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-export-col" value="description"> Description</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-export-col" value="party_name" checked> Party Name</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-export-col" value="payment_status"> Payment Status</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-export-col" value="total" checked> Total</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-export-col" value="order_number"> Order Number</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-export-col" value="payment_type" checked> Payment Type</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-export-col" value="party_phone"> Party's Phone No.</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-export-col" value="received_paid" checked> Received/Paid</label>
        <label class="pur-modal-col-item"></label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-export-col" value="balance_due" checked> Balance Due</label>
      </div>
    </div>
    <div class="pur-modal-footer">
      <button class="pur-modal-generate-btn" id="purExcelGenerateBtn">Generate Report</button>
    </div>
  </div>
</div>

<!-- ═══════════════════════════════════════════════
     PRINT OPTIONS MODAL
═══════════════════════════════════════════════ -->
<div class="pur-modal-overlay" id="purPrintModal" style="display:none;">
  <div class="pur-modal-box">
    <div class="pur-modal-header">
      <h5 class="pur-modal-title">Select Print Options</h5>
      <button class="pur-modal-close" id="purPrintModalClose"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="pur-modal-body">
      <div class="pur-modal-columns">
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-print-col" value="date" checked> Date</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-print-col" value="invoice_no" checked> Invoice No.</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-print-col" value="party_name" checked> Party Name</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-print-col" value="total" checked> Total</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-print-col" value="payment_type" checked> Payment Type</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-print-col" value="received_paid" checked> Received/Paid</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-print-col" value="balance_due" checked> Balance Due</label>
        <label class="pur-modal-col-item"><input type="checkbox" class="pur-print-col" value="status" checked> Status</label>
      </div>
    </div>
    <div class="pur-modal-footer">
      <button class="pur-modal-generate-btn" id="purPrintGenerateBtn">Get Print</button>
    </div>
  </div>
</div>


{{-- ══════════════════════════════════════════════════
     STYLES
══════════════════════════════════════════════════ --}}
<style>
/* ── Base ── */
.pur-report-title {
  font-size: 22px; font-weight: 700; color: #111827;
}

/* ── Buttons ── */
.pur-add-btn {
  background: #ef4444; color: #fff; border: none;
  border-radius: 999px; padding: 8px 20px;
  font-size: 14px; font-weight: 600; cursor: pointer;
  transition: background 0.15s;
}
.pur-add-btn:hover { background: #dc2626; }

.pur-icon-btn {
  width: 36px; height: 36px; border-radius: 8px;
  border: 1px solid #e5e7eb; background: #fff;
  color: #6b7280; font-size: 15px; cursor: pointer;
  display: flex; align-items: center; justify-content: center;
  transition: background 0.15s;
}
.pur-icon-btn:hover { background: #f3f4f6; }

/* ── Filter Bar ── */
.pur-filter-bar { flex-wrap: wrap; }
.pur-filter-label { font-size: 14px; color: #6b7280; white-space: nowrap; }

.pur-period-pill {
  background: #E4F2FF; border-radius: 999px;
  overflow: visible; height: 40px; position: relative;
}
.pur-period-select-wrap {
  display: flex; align-items: center; padding: 0 14px;
  border-right: 1px solid rgba(0,0,0,0.15); position: relative;
}
.pur-period-select {
  background: transparent; border: none; outline: none;
  font-size: 13px; font-weight: 500; color: #374151;
  cursor: pointer; padding-right: 18px; appearance: none;
}
.pur-period-arrow {
  position: absolute; right: 14px;
  font-size: 10px; color: #6b7280; pointer-events: none;
}
.pur-date-range-display {
  padding: 0 16px; font-size: 13px; color: #374151;
  white-space: nowrap; display: flex; align-items: center; gap: 4px;
  border-radius: 0 999px 999px 0;
  transition: background 0.15s;
}
.pur-date-range-display:hover { background: rgba(37,99,235,0.07); }
.pur-between-label {
  background: #aaaaaa; color: #fff; font-weight: 600;
  padding: 4px 10px; border-radius: 4px; font-size: 12px;
}

/* ── Calendar Popup ── */
.pur-calendar-popup {
  position: absolute;
  top: calc(100% + 8px);
  left: 0;
  z-index: 500;
  background: #fff;
  border: 1px solid #e5e7eb;
  border-radius: 14px;
  box-shadow: 0 16px 40px rgba(0,0,0,0.14);
  min-width: 260px;
}
.pur-calendar-popup-inner { padding: 16px 18px; }

.pur-firm-select-wrap {
  position: relative; display: flex; align-items: center;
  background: #E4F2FF; border-radius: 999px;
  padding: 0 14px; height: 40px;
}
.pur-firm-select {
  background: transparent; border: none; outline: none;
  font-size: 13px; color: #374151; cursor: pointer;
  appearance: none; padding-right: 18px;
}
.pur-custom-dates { display: flex; align-items: center; gap: 8px; }
.pur-date-input {
  border: 1px solid #e5e7eb; border-radius: 8px;
  padding: 6px 10px; font-size: 13px; outline: none; color: #374151;
}
.pur-date-input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); }
.pur-apply-dates-btn {
  background: #ef4444; color: #fff; border: none;
  border-radius: 8px; padding: 6px 14px; font-size: 13px; cursor: pointer;
}

/* ── Summary Cards ── */
.pur-stat-card {
  border-radius: 12px; padding: 16px 24px; min-width: 160px;
}
.pur-stat-paid   { background: #B9F3E7; }
.pur-stat-unpaid { background: #CFE6FE; }
.pur-stat-total  { background: #F8C889; }
.pur-stat-label  { font-size: 14px; font-weight: 600; color: #374151; margin-bottom: 4px; }
.pur-stat-amount { font-size: 22px; font-weight: 700; color: #111827; }
.pur-stat-operator {
  font-size: 28px; font-weight: 300; color: #9ca3af; line-height: 1;
}

/* ── Transactions Card — full height ── */
.pur-txn-card {
  background: #fff; border: 1px solid #e5e7eb;
  border-radius: 16px; overflow: hidden;
  box-shadow: 0 2px 10px rgba(0,0,0,0.05);
}
/* Make the card stretch to fill remaining viewport height */
.pur-txn-card-fullheight {
  display: flex;
  flex-direction: column;
  min-height: calc(100vh - 340px); /* adjust offset to match your header/summary height */
}
/* Make the table scroll area grow to fill the card */
.pur-table-scroll {
  flex: 1;
  overflow-y: auto;
}

.pur-txn-card-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 16px 20px; border-bottom: 1px solid #f3f4f6;
  flex-shrink: 0;
}
.pur-txn-title { font-size: 16px; font-weight: 700; color: #111827; margin: 0; }
.pur-txn-actions { display: flex; gap: 4px; }
.pur-txn-action-btn {
  width: 34px; height: 34px; border: none; background: transparent;
  color: #6b7280; font-size: 15px; cursor: pointer; border-radius: 8px;
  display: flex; align-items: center; justify-content: center;
  transition: background 0.15s;
}
.pur-txn-action-btn:hover { background: #f3f4f6; color: #374151; }
.pur-txn-action-btn.text-success { color: #10b981 !important; }

/* ── Search Bar ── */
.pur-search-bar { padding: 12px 20px; border-bottom: 1px solid #f3f4f6; flex-shrink: 0; }
.pur-search-inner {
  display: flex; align-items: center; gap: 8px;
  border: 1px solid #e5e7eb; border-radius: 10px; padding: 8px 12px;
  max-width: 360px;
}
.pur-search-input { flex:1; border:none; outline:none; font-size:14px; color:#374151; }
.pur-search-close { border:none; background:transparent; color:#9ca3af; cursor:pointer; }

/* ── Chart Panel ── */
.pur-chart-panel { padding: 16px 20px; border-bottom: 1px solid #f3f4f6; flex-shrink: 0; }
.pur-chart-header { display: flex; justify-content: flex-end; margin-bottom: 12px; }
.pur-chart-periods { display: flex; gap: 4px; }
.pur-chart-period {
  border: none; background: transparent; font-size: 13px; font-weight: 600;
  color: #6b7280; padding: 6px 12px; cursor: pointer;
  border-bottom: 2px solid transparent; transition: color 0.15s, border-color 0.15s;
}
.pur-chart-period.active { color: #2563eb; border-color: #2563eb; }
.pur-chart-title { font-size: 15px; font-weight: 700; color: #111827; margin-bottom: 8px; }
.pur-chart-container { height: 220px; }

/* ── Table ── */
.pur-table { width: 100%; border-collapse: collapse; font-size: 13px; }
.pur-table thead tr { background: #fff; }
.pur-table th { padding: 0; border-bottom: 1px solid #f3f4f6; white-space: nowrap; position: sticky; top: 0; z-index: 10; background: #fff; }
.pur-th-inner {
  display: flex; align-items: center; gap: 4px;
  padding: 12px 14px; color: #6b7280; font-size: 12px;
  font-weight: 700; text-transform: uppercase; letter-spacing: 0.03em;
}
.pur-th-sort {
  border: none; background: transparent; color: #c4c9d4;
  font-size: 11px; cursor: pointer; padding: 0; transition: color 0.15s;
}
.pur-th-sort:hover, .pur-th-sort.active { color: #2563eb; }

/* Column filter */
.pur-th-filter-wrap { position: relative; display: inline-flex; }
.pur-th-filter-btn {
  border: none; background: transparent; color: #c4c9d4;
  font-size: 11px; cursor: pointer; padding: 2px 4px; border-radius: 4px;
  transition: color 0.15s, background 0.15s;
}
.pur-th-filter-btn:hover, .pur-th-filter-btn.active { color: #2563eb; background: #eff6ff; }
.pur-col-filter-dropdown {
  display: none; position: absolute; top: calc(100% + 6px); left: 0;
  background: #fff; border: 1px solid #e5e7eb; border-radius: 12px;
  box-shadow: 0 12px 30px rgba(0,0,0,0.12); z-index: 200;
  min-width: 190px; overflow: hidden;
}
.pur-col-filter-dropdown.open { display: block; }
.pur-col-filter-body { padding: 12px 14px; }
.pur-col-filter-label { display: block; font-size: 11px; color: #9ca3af; margin-bottom: 4px; }
.pur-col-filter-select, .pur-col-filter-input {
  width: 100%; border: 1px solid #e5e7eb; border-radius: 8px;
  padding: 6px 10px; font-size: 13px; outline: none; color: #374151; background: #fff;
}
.pur-col-filter-checkbox {
  display: flex; align-items: center; gap: 8px;
  font-size: 13px; color: #374151; padding: 5px 0; cursor: pointer;
}
.pur-col-filter-actions {
  display: flex; justify-content: flex-end; gap: 8px;
  padding: 10px 14px; border-top: 1px solid #f3f4f6; background: #fafafa;
}
.pur-col-clear-btn {
  background: #EBEAEA; color: #71748E; border: none;
  border-radius: 999px; padding: 6px 14px; font-size: 12px; font-weight: 600; cursor: pointer;
}
.pur-col-apply-btn {
  background: #ef4444; color: #fff; border: none;
  border-radius: 999px; padding: 6px 14px; font-size: 12px; font-weight: 600; cursor: pointer;
}

/* Table body */
.pur-table tbody tr { border-bottom: 1px solid #f9fafb; }
.pur-table tbody tr:hover { background: #fafafa; }
.pur-table td { padding: 13px 14px; color: #374151; font-size: 13px; vertical-align: middle; }
.pur-empty-state { text-align: center; padding: 48px; color: #9ca3af; font-size: 14px; }

/* Row action buttons */
.pur-row-print-btn, .pur-row-share-btn {
  border: none; background: transparent; color: #9ca3af;
  font-size: 14px; cursor: pointer; padding: 4px 6px; border-radius: 6px;
  transition: color 0.15s, background 0.15s;
}
.pur-row-print-btn:hover { color: #374151; background: #f3f4f6; }
.pur-row-share-btn:hover { color: #2563eb; background: #eff6ff; }
.pur-row-more-btn {
  border: none; background: transparent; color: #9ca3af;
  font-size: 14px; cursor: pointer; padding: 4px 6px; border-radius: 6px;
}
.pur-row-more-btn:hover { color: #374151; background: #f3f4f6; }

/* Status badges */
.pur-badge-paid    { background:#d1fae5; color:#047857; border-radius:999px; padding:4px 10px; font-size:11px; font-weight:700; }
.pur-badge-unpaid  { background:#fef3c7; color:#d97706; border-radius:999px; padding:4px 10px; font-size:11px; font-weight:700; }
.pur-badge-partial { background:#dbeafe; color:#2563eb; border-radius:999px; padding:4px 10px; font-size:11px; font-weight:700; }

/* Pagination */
.pur-pagination {
  display: flex; align-items: center; justify-content: space-between;
  padding: 12px 20px; border-top: 1px solid #f3f4f6; font-size: 13px; color: #6b7280;
  flex-shrink: 0;
}
.pur-pagination-btns { display: flex; align-items: center; gap: 4px; }
.pur-page-btn {
  width: 30px; height: 30px; border-radius: 8px; border: 1px solid #e5e7eb;
  background: #fff; color: #6b7280; cursor: pointer; font-size: 11px;
  display: flex; align-items: center; justify-content: center;
}
.pur-page-btn:hover { background: #f3f4f6; }

/* ── Modals ── */
.pur-modal-overlay {
  position: fixed; inset: 0; background: rgba(0,0,0,0.4);
  z-index: 1100; display: flex; align-items: center; justify-content: center;
}
.pur-modal-box {
  background: #fff; border-radius: 16px;
  width: min(520px, calc(100vw - 32px));
  box-shadow: 0 20px 60px rgba(0,0,0,0.2); overflow: hidden;
}
.pur-modal-header {
  display: flex; align-items: center; justify-content: space-between;
  padding: 20px 24px 16px;
}
.pur-modal-title { font-size: 18px; font-weight: 700; color: #111827; margin: 0; }
.pur-modal-close { border: none; background: transparent; color: #9ca3af; font-size: 18px; cursor: pointer; }
.pur-modal-body { padding: 0 24px 16px; }
.pur-modal-columns { display: grid; grid-template-columns: 1fr 1fr; gap: 6px 24px; }
.pur-modal-col-item {
  display: flex; align-items: center; gap: 10px;
  font-size: 14px; color: #374151; padding: 6px 0; cursor: pointer;
}
.pur-modal-col-item input[type="checkbox"] { width: 18px; height: 18px; accent-color: #2563eb; cursor: pointer; }
.pur-modal-footer { padding: 16px 24px 20px; text-align: center; }
.pur-modal-generate-btn {
  background: #ef4444; color: #fff; border: none; border-radius: 999px;
  padding: 12px 36px; font-size: 15px; font-weight: 600;
  cursor: pointer; transition: background 0.15s;
}
.pur-modal-generate-btn:hover { background: #dc2626; }
</style>


{{-- ══════════════════════════════════════════════════
     SCRIPTS
══════════════════════════════════════════════════ --}}
<script>
(function () {
  'use strict';

  /* ── State ── */
  let purAllRows    = [];
  let purFiltered   = [];
  let purSortState  = { key: null, dir: 1 };
  let purColFilters = {};
  let purCurrentPage = 1;
  const PUR_PAGE_SIZE = 25;

  /* ── Helpers ── */
  function fmt(n) { return 'Rs ' + parseFloat(n || 0).toFixed(2); }
  function fmtDate(d) {
    if (!d) return '-';
    return new Date(d).toLocaleDateString('en-GB');
  }

  /* ── Date Range Logic ── */
  function getDateRange(period) {
    const now = new Date();
    const y = now.getFullYear(), m = now.getMonth();
    switch (period) {
      case 'this_month':   return [new Date(y, m, 1), new Date(y, m + 1, 0)];
      case 'last_month':   return [new Date(y, m - 1, 1), new Date(y, m, 0)];
      case 'this_quarter': {
        const q = Math.floor(m / 3);
        return [new Date(y, q * 3, 1), new Date(y, q * 3 + 3, 0)];
      }
      case 'this_year':    return [new Date(y, 0, 1), new Date(y, 11, 31)];
      case 'all':
      default:             return [new Date(2000, 0, 1), new Date(2099, 11, 31)];
    }
  }
  function toISO(d)     { return d.toISOString().split('T')[0]; }
  function toDisplay(d) { return d.toLocaleDateString('en-GB'); }

  function updateDateDisplay(from, to) {
    document.getElementById('purDateFrom').textContent = typeof from === 'string'
      ? new Date(from).toLocaleDateString('en-GB') : toDisplay(from);
    document.getElementById('purDateTo').textContent = typeof to === 'string'
      ? new Date(to).toLocaleDateString('en-GB') : toDisplay(to);
  }

  /* ── Fetch Data ── */
  function purLoadData(fromDate, toDate) {
    const tbody = document.getElementById('purTxnTableBody');
    tbody.innerHTML = `<tr><td colspan="9" class="pur-empty-state">
      <i class="fa-solid fa-spinner fa-spin" style="font-size:24px;color:#d1d5db;display:block;margin-bottom:6px;"></i>
      Loading transactions…
    </td></tr>`;

    fetch(`{{ route('reports.purchase') }}?from=${fromDate}&to=${toDate}`, {
      headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
    })
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        purAllRows  = data.transactions || [];
        purFiltered = [...purAllRows];
        updatePurSummary(data);
        purRenderTable();
      } else {
        tbody.innerHTML = `<tr><td colspan="9" class="pur-empty-state">No data found.</td></tr>`;
      }
    })
    .catch(() => {
      tbody.innerHTML = `<tr><td colspan="9" class="pur-empty-state text-danger">Error loading data.</td></tr>`;
    });
  }

  function updatePurSummary(data) {
    document.getElementById('purTotalPaid').textContent   = fmt(data.total_paid    || 0);
    document.getElementById('purTotalUnpaid').textContent = fmt(data.total_unpaid  || 0);
    document.getElementById('purTotalAmount').textContent = fmt(data.total_amount  || 0);
  }

  /* ── Render Table ── */
  function purRenderTable() {
    const tbody = document.getElementById('purTxnTableBody');
    const total = purFiltered.length;
    const start = (purCurrentPage - 1) * PUR_PAGE_SIZE;
    const pageRows = purFiltered.slice(start, start + PUR_PAGE_SIZE);

    if (!total) {
      tbody.innerHTML = `<tr><td colspan="9" class="pur-empty-state">
        <i class="fa-solid fa-receipt" style="font-size:36px;color:#d1d5db;display:block;margin-bottom:8px;"></i>
        No purchase bills found for this period.
      </td></tr>`;
      document.getElementById('purPagination').style.display = 'none';
      return;
    }

    tbody.innerHTML = '';
    pageRows.forEach(row => {
      const tr = document.createElement('tr');
      const balDue = parseFloat(row.balance_due || row.balance || 0);
      let badge = '';
      if (balDue <= 0) {
        badge = `<span class="pur-badge-paid">Paid</span>`;
      } else if ((row.received_paid || row.received || 0) > 0) {
        badge = `<span class="pur-badge-partial">Partial</span>`;
      } else {
        badge = `<span class="pur-badge-unpaid">Unpaid</span>`;
      }

      tr.innerHTML = `
        <td>${fmtDate(row.bill_date || row.invoice_date || row.date)}</td>
        <td>${row.bill_number || row.invoice_no || '-'}</td>
        <td>${row.party_name || '-'}</td>
        <td>Purchase</td>
        <td>${row.payment_type || 'Cash'}</td>
        <td>${fmt(row.total_amount || row.amount || 0)}</td>
        <td>${fmt(balDue)}</td>
        <td>${badge}</td>
        <td>
          <button class="pur-row-print-btn" title="Print">
            <i class="fa-solid fa-print"></i>
          </button>
          <button class="pur-row-share-btn" title="Share">
            <i class="fa-solid fa-arrow-up-from-bracket"></i>
          </button>
          <button class="pur-row-more-btn" title="More">
            <i class="fa-solid fa-ellipsis-vertical"></i>
          </button>
        </td>
      `;
      tbody.appendChild(tr);
    });

    /* Pagination */
    const totalPages = Math.ceil(total / PUR_PAGE_SIZE);
    const pag = document.getElementById('purPagination');
    if (totalPages > 1) {
      pag.style.display = 'flex';
      document.getElementById('purPaginationInfo').textContent =
        `Showing ${start + 1}–${Math.min(start + PUR_PAGE_SIZE, total)} of ${total}`;
      let html = '';
      for (let i = 1; i <= totalPages; i++) {
        html += `<button class="pur-page-btn${i === purCurrentPage ? ' active' : ''}"
          style="${i === purCurrentPage ? 'background:#eff6ff;color:#2563eb;border-color:#bfdbfe;font-weight:700;' : ''}"
          onclick="purGoToPage(${i})">${i}</button>`;
      }
      document.getElementById('purPageNumbers').innerHTML = html;
    } else {
      pag.style.display = 'none';
    }
  }

  window.purGoToPage = function(page) {
    purCurrentPage = page;
    purRenderTable();
  };

  /* ── Apply All Filters ── */
  function purApplyAllFilters() {
    const keyword = (document.getElementById('purTxnSearchInput')?.value || '').toLowerCase();
    purFiltered = purAllRows.filter(row => {
      if (keyword) {
        const hay = [row.bill_date, row.bill_number, row.party_name, row.payment_type, row.total_amount].join(' ').toLowerCase();
        if (!hay.includes(keyword)) return false;
      }
      if (purColFilters.party_name) {
        const name = (row.party_name || '').toLowerCase();
        const { type, val } = purColFilters.party_name;
        if (type === 'exact'    && name !== val.toLowerCase()) return false;
        if (type === 'contains' && !name.includes(val.toLowerCase())) return false;
      }
      if (purColFilters.amount) {
        const amt = parseFloat(row.total_amount || 0);
        const { min, max } = purColFilters.amount;
        if (min != null && amt < min) return false;
        if (max != null && amt > max) return false;
      }
      return true;
    });

    if (purSortState.key) {
      purFiltered.sort((a, b) => {
        let av = a[purSortState.key] || 0;
        let bv = b[purSortState.key] || 0;
        if (typeof av === 'string') av = av.toLowerCase();
        if (typeof bv === 'string') bv = bv.toLowerCase();
        return av < bv ? -purSortState.dir : av > bv ? purSortState.dir : 0;
      });
    }

    purCurrentPage = 1;
    purRenderTable();
  }

  /* ── Sort ── */
  window.purSortBy = function(key, btn) {
    purSortState.dir = purSortState.key === key ? purSortState.dir * -1 : 1;
    purSortState.key = key;
    document.querySelectorAll('.pur-th-sort').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    purApplyAllFilters();
  };

  /* ── Column Filter ── */
  window.purToggleColFilter = function(btn) {
    const dropdown = btn.nextElementSibling;
    const isOpen = dropdown.classList.contains('open');
    document.querySelectorAll('.pur-col-filter-dropdown.open').forEach(d => {
      d.classList.remove('open');
      d.previousElementSibling?.classList.remove('active');
    });
    if (!isOpen) { dropdown.classList.add('open'); btn.classList.add('active'); }
  };

  window.purClearColFilter = function(key, btn) {
    delete purColFilters[key];
    const dd = btn.closest('.pur-col-filter-dropdown');
    dd.querySelectorAll('input').forEach(i => { i.value = ''; i.checked = false; });
    purApplyAllFilters();
    dd.classList.remove('open');
    dd.previousElementSibling?.classList.remove('active');
  };

  window.purApplyColFilter = function(key, btn) {
    const dd = btn.closest('.pur-col-filter-dropdown');
    if (key === 'party_name') {
      const type = document.getElementById('purFilterPartyType')?.value;
      const val  = document.getElementById('purFilterPartyVal')?.value.trim();
      if (val) purColFilters[key] = { type, val };
    } else if (key === 'amount') {
      const min = parseFloat(document.getElementById('purFilterAmountMin')?.value);
      const max = parseFloat(document.getElementById('purFilterAmountMax')?.value);
      if (!isNaN(min) || !isNaN(max)) {
        purColFilters[key] = { min: isNaN(min) ? null : min, max: isNaN(max) ? null : max };
      }
    } else if (key === 'balance') {
      const min = parseFloat(document.getElementById('purFilterBalanceMin')?.value);
      const max = parseFloat(document.getElementById('purFilterBalanceMax')?.value);
      if (!isNaN(min) || !isNaN(max)) {
        purColFilters[key] = { min: isNaN(min) ? null : min, max: isNaN(max) ? null : max };
      }
    }
    purApplyAllFilters();
    dd.classList.remove('open');
    dd.previousElementSibling?.classList.remove('active');
  };

  /* ── Period Selector ── */
  document.getElementById('purPeriodSelect')?.addEventListener('change', function() {
    if (this.value === 'custom') {
      // Open calendar popup instead
      document.getElementById('purCalendarPopup').classList.remove('d-none');
      return;
    }
    document.getElementById('purCalendarPopup').classList.add('d-none');
    const [from, to] = getDateRange(this.value);
    updateDateDisplay(from, to);
    purLoadData(toISO(from), toISO(to));
  });

  /* ── Calendar Icon / Date Range Display Click ── */
  document.getElementById('purDateRangeDisplay')?.addEventListener('click', function(e) {
    const popup = document.getElementById('purCalendarPopup');
    popup.classList.toggle('d-none');
    if (!popup.classList.contains('d-none')) {
      // Pre-fill inputs with current displayed dates
      const fromText = document.getElementById('purDateFrom').textContent;
      const toText   = document.getElementById('purDateTo').textContent;
      // Convert dd/mm/yyyy to yyyy-mm-dd for input
      function ddmmToISO(str) {
        const parts = str.split('/');
        if (parts.length === 3) return `${parts[2]}-${parts[1]}-${parts[0]}`;
        return '';
      }
      document.getElementById('purCalFrom').value = ddmmToISO(fromText);
      document.getElementById('purCalTo').value   = ddmmToISO(toText);
    }
  });

  document.getElementById('purCalApply')?.addEventListener('click', function() {
    const from = document.getElementById('purCalFrom').value;
    const to   = document.getElementById('purCalTo').value;
    if (!from || !to) return;
    updateDateDisplay(from, to);
    document.getElementById('purCalendarPopup').classList.add('d-none');
    // Switch period selector to custom
    document.getElementById('purPeriodSelect').value = 'custom';
    purLoadData(from, to);
  });

  document.getElementById('purCalCancel')?.addEventListener('click', function() {
    document.getElementById('purCalendarPopup').classList.add('d-none');
  });

  /* legacy apply dates button */
  document.getElementById('purApplyDates')?.addEventListener('click', function() {
    const from = document.getElementById('purFromDate')?.value;
    const to   = document.getElementById('purToDate')?.value;
    if (!from || !to) return;
    updateDateDisplay(from, to);
    purLoadData(from, to);
  });

  /* ── Search ── */
  document.getElementById('purTxnSearchBtn')?.addEventListener('click', function() {
    const bar = document.getElementById('purTxnSearchBar');
    const hidden = bar.classList.contains('d-none');
    bar.classList.toggle('d-none', !hidden);
    if (hidden) document.getElementById('purTxnSearchInput')?.focus();
  });
  document.getElementById('purTxnSearchClose')?.addEventListener('click', function() {
    document.getElementById('purTxnSearchBar').classList.add('d-none');
    document.getElementById('purTxnSearchInput').value = '';
    purApplyAllFilters();
  });
  document.getElementById('purTxnSearchInput')?.addEventListener('input', purApplyAllFilters);

  /* ── Chart Toggle ── */
  let purChart = null;
  document.getElementById('purTxnChartBtn')?.addEventListener('click', function() {
    const panel = document.getElementById('purTxnChartPanel');
    panel.classList.toggle('d-none');
    if (!panel.classList.contains('d-none')) purInitChart('daily');
  });

  document.querySelectorAll('.pur-chart-period').forEach(btn => {
    btn.addEventListener('click', function() {
      document.querySelectorAll('.pur-chart-period').forEach(b => b.classList.remove('active'));
      this.classList.add('active');
      purInitChart(this.dataset.period);
    });
  });

  function purInitChart(period) {
    const canvas = document.getElementById('purChart');
    if (!canvas) return;
    const grouped = {};
    purFiltered.forEach(row => {
      const d = new Date(row.bill_date || row.invoice_date || row.date);
      let key = period === 'daily' ? d.toISOString().split('T')[0]
              : period === 'monthly' ? d.toLocaleString('default', { month: 'short', year: '2-digit' })
              : period === 'yearly' ? d.getFullYear().toString()
              : `W${Math.ceil(d.getDate()/7)} ${d.toLocaleString('default',{month:'short'})}`;
      grouped[key] = (grouped[key] || 0) + parseFloat(row.total_amount || 0);
    });
    if (purChart) purChart.destroy();
    const drawFn = () => {
      purChart = new Chart(canvas, {
        type: 'bar',
        data: {
          labels: Object.keys(grouped),
          datasets: [{ label: 'Purchases', data: Object.values(grouped),
            backgroundColor: 'rgba(239,68,68,0.7)', borderColor: '#ef4444', borderWidth: 1 }]
        },
        options: {
          responsive: true, maintainAspectRatio: false,
          plugins: { legend: { display: false } },
          scales: {
            x: { grid: { color: '#f3f4f6' } },
            y: { grid: { color: '#f3f4f6' } }
          }
        }
      });
    };
    if (typeof Chart === 'undefined') {
      const s = document.createElement('script');
      s.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js';
      s.onload = drawFn;
      document.head.appendChild(s);
    } else { drawFn(); }
  }

  /* ── Excel Export ── */
  document.getElementById('purTxnExcelBtn')?.addEventListener('click', () => {
    document.getElementById('purExcelModal').style.display = 'flex';
  });
  document.getElementById('purExcelTopBtn')?.addEventListener('click', () => {
    document.getElementById('purExcelModal').style.display = 'flex';
  });
  document.getElementById('purExcelModalClose')?.addEventListener('click', () => {
    document.getElementById('purExcelModal').style.display = 'none';
  });
  document.getElementById('purExcelModal')?.addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
  });
  document.getElementById('purExcelGenerateBtn')?.addEventListener('click', function() {
    const cols = Array.from(document.querySelectorAll('.pur-export-col:checked')).map(c => c.value);
    purExportCSV(cols);
    document.getElementById('purExcelModal').style.display = 'none';
  });

  function purExportCSV(cols) {
    const colMap = {
      date: 'Date', invoice_no: 'Invoice No', party_name: 'Party Name',
      total: 'Total', payment_type: 'Payment Type', received_paid: 'Received/Paid',
      balance_due: 'Balance Due', status: 'Status'
    };
    const headers = cols.map(c => colMap[c] || c);
    const lines = [headers.join(',')];
    purFiltered.forEach(row => {
      const vals = cols.map(c => {
        if (c === 'date')         return fmtDate(row.bill_date || row.invoice_date);
        if (c === 'invoice_no')   return row.bill_number || '-';
        if (c === 'party_name')   return row.party_name || '-';
        if (c === 'total')        return parseFloat(row.total_amount || 0).toFixed(2);
        if (c === 'payment_type') return row.payment_type || 'Cash';
        if (c === 'received_paid')return parseFloat(row.received_paid || 0).toFixed(2);
        if (c === 'balance_due')  return parseFloat(row.balance_due || 0).toFixed(2);
        return '-';
      });
      lines.push(vals.map(v => `"${String(v).replace(/"/g,'""')}"`).join(','));
    });
    const blob = new Blob(['\uFEFF' + lines.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = `PurchaseReport_${new Date().toISOString().split('T')[0]}.csv`;
    document.body.appendChild(a); a.click(); a.remove();
    URL.revokeObjectURL(a.href);
  }

  /* ── Print ── */
  document.getElementById('purTxnPrintBtn')?.addEventListener('click', () => {
    document.getElementById('purPrintModal').style.display = 'flex';
  });
  document.getElementById('purPrintTopBtn')?.addEventListener('click', () => {
    document.getElementById('purPrintModal').style.display = 'flex';
  });
  document.getElementById('purPrintModalClose')?.addEventListener('click', () => {
    document.getElementById('purPrintModal').style.display = 'none';
  });
  document.getElementById('purPrintModal')?.addEventListener('click', function(e) {
    if (e.target === this) this.style.display = 'none';
  });
  document.getElementById('purPrintGenerateBtn')?.addEventListener('click', function() {
    const cols = Array.from(document.querySelectorAll('.pur-print-col:checked')).map(c => c.value);
    document.getElementById('purPrintModal').style.display = 'none';
    purDoPrint(cols);
  });

  function purDoPrint(cols) {
    const colMap = {
      date: { label: 'DATE', fn: r => fmtDate(r.bill_date || r.invoice_date) },
      invoice_no: { label: 'INVOICE NO.', fn: r => r.bill_number || '-' },
      party_name: { label: 'PARTY NAME', fn: r => r.party_name || '-' },
      total: { label: 'TOTAL', fn: r => fmt(r.total_amount || 0) },
      payment_type: { label: 'PAYMENT TYPE', fn: r => r.payment_type || 'Cash' },
      received_paid: { label: 'RECEIVED/PAID', fn: r => fmt(r.received_paid || 0) },
      balance_due: { label: 'BALANCE DUE', fn: r => fmt(r.balance_due || 0) },
      status: { label: 'STATUS', fn: r => (parseFloat(r.balance_due||0) <= 0 ? 'Paid' : 'Unpaid') },
    };
    const active = cols.filter(c => colMap[c]);
    const ths = active.map(c => `<th style="background:#f3f4f6;padding:8px 10px;border:1px solid #e5e7eb;">${colMap[c].label}</th>`).join('');
    const trs = purFiltered.map(row =>
      `<tr>${active.map(c => `<td style="padding:7px 10px;border:1px solid #e5e7eb;">${colMap[c].fn(row)}</td>`).join('')}</tr>`
    ).join('');
    const w = window.open('', '_blank', 'width=900,height=700');
    if (!w) return;
    w.document.write(`<html><head><title>Purchase Report</title><style>
      body{font-family:Arial,sans-serif;padding:20px;color:#1f2937;}
      h2{text-align:center;} table{width:100%;border-collapse:collapse;font-size:12px;}
    </style></head><body>
      <h2>Purchase Report</h2>
      <p><strong>${document.getElementById('purDateFrom').textContent} to ${document.getElementById('purDateTo').textContent}</strong></p>
      <table><thead><tr>${ths}</tr></thead><tbody>${trs}</tbody></table>
    </body></html>`);
    w.document.close();
    w.focus();
    w.print();
  }

  /* ── Add Purchase → redirect to purchase create page ── */
  document.getElementById('purAddPurchaseBtn')?.addEventListener('click', function() {
    window.location.href = '{{ route("purchase-bill.create") }}';
  });

  /* ── Close dropdowns and calendar popup on outside click ── */
  document.addEventListener('click', function(e) {
    // Close column filter dropdowns
    if (!e.target.closest('.pur-th-filter-wrap')) {
      document.querySelectorAll('.pur-col-filter-dropdown.open').forEach(d => {
        d.classList.remove('open');
        d.previousElementSibling?.classList.remove('active');
      });
    }
    // Close calendar popup when clicking outside the period pill area
    if (!e.target.closest('.pur-period-pill') && !e.target.closest('#purCalendarPopup')) {
      document.getElementById('purCalendarPopup')?.classList.add('d-none');
    }
  });

  /* ── INIT: fetch ALL purchase records on load ── */
  (function init() {
    // Default to "all" — fetch all records from 2000 to 2099
    const allFrom = '2000-01-01';
    const allTo   = '2099-12-31';
    updateDateDisplay(allFrom, allTo);
    purLoadData(allFrom, allTo);
  })();

})();
</script>