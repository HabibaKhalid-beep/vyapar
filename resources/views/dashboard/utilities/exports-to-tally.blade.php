@extends('layouts.app')

@section('title', 'Utilities - Exports To Tally')
@section('description', 'Export transaction data to Tally format.')
@section('page', 'exports-to-tally')

@push('styles')
  <style>
    .tally-page {
      background: #f3f4f8;
      min-height: calc(100vh - 20px);
      padding: 22px;
    }

    .tally-card {
      border: 1px solid #e7e9f0;
      background: #fff;
      border-radius: 6px;
      overflow: hidden;
      box-shadow: 0 10px 30px rgba(31, 45, 93, 0.08);
    }

    .tally-topbar {
      border-bottom: 1px solid #edf0f5;
      padding: 24px 32px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .tally-period {
      display: flex;
      align-items: center;
      gap: 10px;
      flex-wrap: wrap;
    }

    .tally-period-select {
      border: 0;
      background: transparent;
      color: #1f3858;
      font-size: 2.2rem;
      font-weight: 700;
      padding: 0;
      outline: none;
      min-width: 230px;
    }

    .tally-date-range {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      border: 1px solid #dbe2ed;
      border-radius: 6px;
      overflow: hidden;
      background: #fff;
    }

    .tally-date-range span {
      background: #6e6f73;
      color: #fff;
      font-weight: 600;
      font-size: 1.4rem;
      padding: 9px 12px;
      line-height: 1;
    }

    .tally-date-range input {
      border: 0;
      padding: 10px 10px;
      font-size: 1rem;
      color: #2f3f58;
      width: 140px;
      outline: none;
      background: #fff;
    }

    .tally-export-btn {
      border: 0;
      border-radius: 999px;
      background: #ff1744;
      color: #fff;
      font-weight: 700;
      font-size: 1.45rem;
      padding: 14px 28px;
      display: inline-flex;
      align-items: center;
      gap: 10px;
      text-decoration: none;
      transition: 0.2s ease;
    }

    .tally-download-btn {
      border: 1px solid #d8dfeb;
      border-radius: 999px;
      background: #fff;
      color: #2b3f5d;
      font-weight: 700;
      font-size: 1rem;
      padding: 10px 18px;
      text-decoration: none;
      margin-left: 8px;
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .tally-export-btn:hover {
      background: #ec153f;
      color: #fff;
    }

    .tally-export-btn.is-loading {
      opacity: 0.75;
      pointer-events: none;
    }

    .tally-sync-status {
      margin: 12px 18px 0;
      border-radius: 8px;
      padding: 10px 12px;
      font-size: 0.98rem;
      display: none;
      border: 1px solid transparent;
    }

    .tally-sync-status.is-visible {
      display: block;
    }

    .tally-sync-status.is-success {
      background: #ebfff1;
      border-color: #b7ebc8;
      color: #1e7f44;
    }

    .tally-sync-status.is-error {
      background: #fff2f5;
      border-color: #ffc8d5;
      color: #bd3457;
    }

    .tally-sync-status.is-info {
      background: #edf5ff;
      border-color: #d0e4ff;
      color: #1d68bb;
    }

    .tally-table-wrap {
      padding: 14px 0 0;
    }

    .tally-filters {
      padding: 14px 16px 12px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .tally-types {
      display: flex;
      align-items: center;
      gap: 22px;
      flex-wrap: wrap;
    }

    .tally-types-label {
      font-weight: 700;
      font-size: 1.5rem;
      color: #36435a;
      margin-right: 8px;
    }

    .tally-check {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #384760;
      font-size: 1.2rem;
      font-weight: 500;
    }

    .tally-check input {
      width: 18px;
      height: 18px;
    }

    .tally-search {
      position: relative;
      width: 320px;
      max-width: 100%;
    }

    .tally-search i {
      position: absolute;
      left: 12px;
      top: 50%;
      transform: translateY(-50%);
      color: #8c96ab;
      font-size: 1.25rem;
    }

    .tally-search input {
      width: 100%;
      border: 1px solid #dce2ec;
      border-radius: 4px;
      height: 42px;
      padding: 8px 12px 8px 36px;
      color: #2d3f5b;
      outline: none;
    }

    .tally-grid {
      overflow: auto;
      max-height: calc(100vh - 320px);
      border-top: 1px solid #edf0f5;
    }

    .tally-table {
      width: 100%;
      min-width: 1100px;
      border-collapse: separate;
      border-spacing: 0;
    }

    .tally-table th,
    .tally-table td {
      border-right: 1px solid #e8edf4;
      border-bottom: 1px solid #e8edf4;
      padding: 13px 12px;
      white-space: nowrap;
      font-size: 1.18rem;
      color: #33435e;
    }

    .tally-table th {
      position: sticky;
      top: 0;
      background: #fff;
      z-index: 2;
      text-transform: uppercase;
      font-size: 1.12rem;
      color: #667287;
      font-weight: 700;
    }

    .tally-table tbody tr:nth-child(odd) td {
      background: #f5f8fc;
    }

    .tally-table tbody tr.is-highlight td {
      background: #cae9fb;
      font-weight: 700;
    }

    .th-content {
      display: inline-flex;
      align-items: center;
      gap: 8px;
    }

    .th-content i {
      font-size: 0.95rem;
      color: #8a95a9;
    }

    .tally-empty {
      text-align: center;
      color: #7f8aa0;
      font-size: 1.25rem;
      padding: 50px 14px;
    }

    .tally-footer-note {
      padding: 10px 18px 14px;
      display: flex;
      justify-content: flex-end;
    }

    .tally-learn {
      display: inline-flex;
      align-items: center;
      gap: 12px;
      padding: 8px 14px;
      border-radius: 8px;
      background: #e9f4ff;
      color: #2a82d8;
      text-decoration: none;
      font-weight: 600;
      font-size: 1rem;
    }

    @media (max-width: 991.98px) {
      .tally-page {
        padding: 14px;
      }

      .tally-topbar {
        padding: 16px;
      }

      .tally-period-select {
        font-size: 1.8rem;
      }

      .tally-export-btn {
        font-size: 1.1rem;
        width: 100%;
        justify-content: center;
      }

      .tally-date-range input {
        width: 118px;
      }

      .tally-types-label {
        width: 100%;
      }
    }
  </style>
@endpush

@section('content')
  <div class="tally-page">
    <div class="tally-card">
      <div class="tally-topbar">
        <div class="tally-period">
          <select id="periodSelect" class="tally-period-select">
            <option value="this_month">This Month</option>
            <option value="last_month">Last Month</option>
            <option value="this_year">This Year</option>
            <option value="custom">Custom</option>
          </select>
          <div class="tally-date-range">
            <span>Between</span>
            <input type="date" id="fromDate">
            <small>To</small>
            <input type="date" id="toDate">
          </div>
        </div>
        <div class="d-flex align-items-center flex-wrap gap-2">
          <a href="#" class="tally-export-btn" id="exportToTallyBtn">
            <i class="bi bi-file-earmark-spreadsheet-fill"></i>
            Export To Tally
          </a>
          <a href="#" class="tally-download-btn" id="downloadExcelBtn">
            <i class="bi bi-download"></i>
            Download Excel
          </a>
        </div>
      </div>
      <div class="tally-sync-status" id="tallySyncStatus"></div>

      <div class="tally-table-wrap">
        <div class="tally-filters">
          <div class="tally-types">
            <div class="tally-types-label">TRANSACTIONS</div>
            <label class="tally-check"><input type="checkbox" value="sale" checked> Sale</label>
            <label class="tally-check"><input type="checkbox" value="credit_note" checked> Credit Note</label>
            <label class="tally-check"><input type="checkbox" value="purchase" checked> Purchase</label>
            <label class="tally-check"><input type="checkbox" value="debit_note" checked> Debit Note</label>
            <label class="tally-check"><input type="checkbox" value="sale_cancelled" checked> Sale[Cancelled]</label>
          </div>

          <div class="tally-search">
            <i class="bi bi-search"></i>
            <input type="text" id="tallySearchInput" placeholder="Search">
          </div>
        </div>

        <div class="tally-grid">
          <table class="tally-table">
            <thead>
              <tr>
                <th><span class="th-content">DATE <i class="bi bi-funnel"></i></span></th>
                <th><span class="th-content">INVOICE NO. <i class="bi bi-funnel"></i></span></th>
                <th><span class="th-content">PARTY NAME <i class="bi bi-funnel"></i></span></th>
                <th><span class="th-content">TRANSACTION TYPE <i class="bi bi-funnel"></i></span></th>
                <th><span class="th-content">PAYMENT TYPE <i class="bi bi-funnel"></i></span></th>
                <th><span class="th-content">AMOUNT <i class="bi bi-funnel"></i></span></th>
                <th><span class="th-content">BALANCE <i class="bi bi-funnel"></i></span></th>
              </tr>
            </thead>
            <tbody id="tallyTableBody">
              <tr>
                <td colspan="7" class="tally-empty">Loading transactions...</td>
              </tr>
            </tbody>
          </table>
        </div>

        <div class="tally-footer-note">
          <a href="javascript:void(0)" class="tally-learn">
            Learn how to export Vyapar data to Tally.
            <span class="badge text-bg-primary rounded-pill">Watch Video</span>
          </a>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const periodSelect = document.getElementById('periodSelect');
      const fromDateInput = document.getElementById('fromDate');
      const toDateInput = document.getElementById('toDate');
      const searchInput = document.getElementById('tallySearchInput');
      const checkboxList = Array.from(document.querySelectorAll('.tally-check input'));
      const tableBody = document.getElementById('tallyTableBody');
      const exportBtn = document.getElementById('exportToTallyBtn');
      const downloadBtn = document.getElementById('downloadExcelBtn');
      const syncStatus = document.getElementById('tallySyncStatus');
      const dataUrl = "{{ route('utilities.exports-to-tally.data') }}";
      const downloadUrl = "{{ route('utilities.exports-to-tally.download') }}";
      const pushUrl = "{{ route('utilities.exports-to-tally.push') }}";
      const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

      function formatMoney(value) {
        const parsed = Number(value || 0);
        return Number.isNaN(parsed) ? '0.00' : parsed.toFixed(2);
      }

      function getToday() {
        const d = new Date();
        return d.toISOString().slice(0, 10);
      }

      function startOfMonth(date) {
        return new Date(date.getFullYear(), date.getMonth(), 1).toISOString().slice(0, 10);
      }

      function endOfMonth(date) {
        return new Date(date.getFullYear(), date.getMonth() + 1, 0).toISOString().slice(0, 10);
      }

      function selectedTypes() {
        return checkboxList.filter(function (cb) {
          return cb.checked;
        }).map(function (cb) {
          return cb.value;
        });
      }

      function buildParams() {
        const params = new URLSearchParams();
        const fromDate = fromDateInput.value;
        const toDate = toDateInput.value;
        const search = searchInput.value.trim();
        const types = selectedTypes();

        if (fromDate) {
          params.set('from', fromDate);
        }
        if (toDate) {
          params.set('to', toDate);
        }
        if (search) {
          params.set('search', search);
        }
        if (types.length) {
          params.set('types', types.join(','));
        }

        return params;
      }

      function showStatus(type, message) {
        if (!syncStatus) {
          return;
        }

        syncStatus.className = `tally-sync-status is-visible is-${type}`;
        syncStatus.textContent = message;
      }

      function renderRows(rows) {
        if (!rows.length) {
          tableBody.innerHTML = '<tr><td colspan="7" class="tally-empty">No transactions found for selected filters.</td></tr>';
          return;
        }

        tableBody.innerHTML = rows.map(function (row, index) {
          return `
            <tr class="${index === 0 ? 'is-highlight' : ''}">
              <td>${row.date || '-'}</td>
              <td>${row.invoice_no || '-'}</td>
              <td>${row.party_name || '-'}</td>
              <td>${row.transaction_type || '-'}</td>
              <td>${row.payment_type || '-'}</td>
              <td>${formatMoney(row.amount)}</td>
              <td>${formatMoney(row.balance)}</td>
            </tr>
          `;
        }).join('');
      }

      async function loadData() {
        tableBody.innerHTML = '<tr><td colspan="7" class="tally-empty">Loading transactions...</td></tr>';

        try {
          const params = buildParams();
          const response = await fetch(`${dataUrl}?${params.toString()}`, {
            credentials: 'same-origin',
            headers: {
              'Accept': 'application/json'
            }
          });

          if (!response.ok) {
            throw new Error('Server returned ' + response.status);
          }

          const json = await response.json();
          if (!json.success) {
            throw new Error(json.message || 'Unable to load data.');
          }

          renderRows(json.rows || []);
        } catch (error) {
          tableBody.innerHTML = `<tr><td colspan="7" class="tally-empty">Error: ${error.message}</td></tr>`;
        }
      }

      function applyPeriod() {
        const today = new Date();
        const period = periodSelect.value;

        if (period === 'this_month') {
          fromDateInput.value = startOfMonth(today);
          toDateInput.value = endOfMonth(today);
        } else if (period === 'last_month') {
          const lastMonth = new Date(today.getFullYear(), today.getMonth() - 1, 1);
          fromDateInput.value = startOfMonth(lastMonth);
          toDateInput.value = endOfMonth(lastMonth);
        } else if (period === 'this_year') {
          fromDateInput.value = `${today.getFullYear()}-01-01`;
          toDateInput.value = `${today.getFullYear()}-12-31`;
        } else if (!fromDateInput.value || !toDateInput.value) {
          fromDateInput.value = startOfMonth(today);
          toDateInput.value = getToday();
        }
      }

      let searchDebounce;
      searchInput.addEventListener('input', function () {
        clearTimeout(searchDebounce);
        searchDebounce = setTimeout(loadData, 350);
      });

      periodSelect.addEventListener('change', function () {
        applyPeriod();
        loadData();
      });

      fromDateInput.addEventListener('change', loadData);
      toDateInput.addEventListener('change', loadData);

      checkboxList.forEach(function (checkbox) {
        checkbox.addEventListener('change', loadData);
      });

      exportBtn.addEventListener('click', async function (event) {
        event.preventDefault();
        const types = selectedTypes();

        if (!types.length) {
          showStatus('error', 'Please select at least one transaction type before exporting.');
          return;
        }

        exportBtn.classList.add('is-loading');
        showStatus('info', 'Sending selected vouchers to Tally...');

        try {
          const response = await fetch(pushUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': csrfToken || ''
            },
            body: JSON.stringify({
              from: fromDateInput.value || null,
              to: toDateInput.value || null,
              search: searchInput.value.trim(),
              types: types
            })
          });

          const json = await response.json();
          if (!response.ok || !json.success) {
            throw new Error(json.message || 'Tally export failed.');
          }

          showStatus('success', `${json.message} Skipped: ${json.skipped_count || 0}.`);
        } catch (error) {
          showStatus('error', error.message);
        } finally {
          exportBtn.classList.remove('is-loading');
        }
      });

      downloadBtn.addEventListener('click', function (event) {
        event.preventDefault();
        const params = buildParams();
        window.location.href = `${downloadUrl}?${params.toString()}`;
      });

      applyPeriod();
      loadData();
    });
  </script>
@endpush
