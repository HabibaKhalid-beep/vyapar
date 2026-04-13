<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vyapar — Delivery Challan</title>
  <meta name="description" content="Record supplier purchase bills with live preview in Vyapar.">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <!-- Custom Styles -->
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
  <style>
    .search-container {
      position: relative;
      width: 50px;
      transition: all 0.3s ease;
    }

    .search-container.active {
      width: 250px;
    }

    .search-input {
      width: 100%;
      height: 40px;
      border: none;
      outline: none;
      padding: 0 40px 0 10px;
      border-radius: 20px;
      opacity: 0;
      transition: 0.3s;
    }

    .search-container.active .search-input {
      opacity: 1;
    }

    .search-btn {
      position: absolute;
      right: 5px;
      top: 5px;
      width: 30px;
      height: 30px;
      background: #6C757D;
      color: white;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
    }

    .filter-pill {
      background-color: #E4F2FF;
      border-radius: 999px;
      display: flex;
      align-items: center;
      height: 38px;
      padding: 0 8px;
    }

    .filter-left {
      border-right: 1px solid #ccc;
      padding: 0 10px;
    }

    .filter-right {
      padding: 0 10px;
      min-width: 210px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 13px;
      white-space: nowrap;
    }

    .filter-select {
      border: none;
      background: transparent;
      outline: none;
      font-size: 13px;
      padding: 0;
      margin: 0;
    }

    .small-pill {
      padding: 0 12px;
      min-width: 120px;
    }

    .date-input {
      border: none;
      background: transparent;
      font-size: 12px;
      width: 110px;
      outline: none;
    }

    .challan-header-actions {
      display: flex;
      align-items: center;
      gap: 12px;
      margin-left: auto;
      flex-shrink: 0;
    }

    .challan-action-btn {
      border: 0;
      background: transparent;
      padding: 0;
      line-height: 1;
      cursor: pointer;
    }

    .challan-action-btn.print-btn {
      color: #6c757d;
    }

    .challan-action-btn.excel-btn {
      color: #198754;
    }
  </style>
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


</head>

<body data-page="delivery-challan">

  <!-- Navbar & Sidebar injected by components.js -->

  <!-- ═══════════════════════════════════════
     MAIN CONTENT — PURCHASE BILL
     ═══════════════════════════════════════ -->
  <main class="main-content" id="mainContent">


    <div class="d-flex justify-content-between align-items-center bg-light p-4 border-bottom mb-2">
      <div class="col-12 text-center">
        <h4 class="mb-0 text-secondary">Delivery Challan</h4>
      </div>

    </div>
    <div class="d-flex justify-content-between align-items-center bg-light mb-2 px-3 py-2 rounded gap-3 flex-wrap">
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="small fw-semibold">Filter By:</span>

        <div class="d-flex rounded-pill filter-pill">
          <div class="filter-left">
            <select id="challanPeriodSelect" class="filter-select">
              <option value="all" selected>All Delivery Challans</option>
              <option value="this_month">This Month</option>
              <option value="last_month">Last Month</option>
              <option value="this_quarter">This Quarter</option>
              <option value="this_year">This Year</option>
              <option value="custom">Custom</option>
            </select>
          </div>

          <div class="filter-right">
            <span id="challanDateRangeDisplay"></span>
            <div id="challanCustomDateRange" class="d-none align-items-center gap-1">
              <input id="challanCustomFrom" type="date" class="date-input" />
              <span>to</span>
              <input id="challanCustomTo" type="date" class="date-input" />
            </div>
          </div>
        </div>

        <div class="filter-pill small-pill">
          <select id="challanFirmSelect" class="filter-select text-center">
            <option value="" selected>All Firms</option>
            @foreach($challans->map(fn($challan) => $challan->display_party_name)->filter()->unique()->values() as $firm)
              <option value="{{ $firm }}">{{ $firm }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="challan-header-actions">
        <button type="button" class="challan-action-btn print-btn" id="challanPrintBtn" title="Print">
          <i class="fas fa-print fs-5"></i>
        </button>
        <button type="button" class="challan-action-btn excel-btn" id="challanExcelBtn" title="Export Excel">
          <i class="fas fa-file-excel fs-5"></i>
        </button>
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

          <button onclick="window.location.href='{{ route('create-challan') }}'"
        class="btn btn-primary rounded">
    <span class="text-primary bg-light rounded-circle" style="padding: 0px 4px;">+</span>
    Add Delivery Challan
</button>
        </div>

       <div class="table-responsive">
          <table class="table align-middle mb-0" id="challanTable">
            <thead>
              <tr class="text-uppercase small text-secondary">
                <th class="py-3">Date</th>
                <th class="py-3">Party</th>
                <th class="py-3">Challan No.</th>
                <th class="py-3">Due Date</th>
                <th class="py-3 text-end">Total Amount</th>
                <th class="py-3">Status</th>
                <th class="py-3">Action</th>
                <th class="py-3 text-center" style="width:56px;"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($challans as $challan)
                @php
                  $isClosed = $challan->status === 'closed';
                  $isOverdue = !$isClosed && $challan->due_date && $challan->due_date->isPast();
                  $convertedInvoice = $convertedInvoices[$challan->id] ?? null;
                  $convertedInvoiceNumber = $convertedInvoice->bill_number ?? null;
                  $overdueDays = $isOverdue ? max(1, $challan->due_date->copy()->startOfDay()->diffInDays(now()->copy()->startOfDay())) : 0;
                @endphp
                <tr class="challan-row">
                  <td>{{ optional($challan->invoice_date)->format('d/m/Y') ?? '-' }}</td>
                  <td>{{ $challan->display_party_name }}</td>
                  <td>{{ $challan->bill_number ?? '-' }}</td>
                  <td>
                    <div>{{ optional($challan->due_date)->format('d/m/Y') ?? '-' }}</div>
                    @if($isOverdue)
                      <span class="badge text-bg-light text-secondary mt-1">Overdue: {{ $overdueDays }} {{ $overdueDays === 1 ? 'day' : 'days' }}</span>
                    @endif
                  </td>
                  <td class="text-end fw-semibold">Rs {{ number_format($challan->grand_total ?? 0, 2) }}</td>
                  <td>
                    @if($isClosed)
                      <span class="text-primary fw-semibold">Closed</span>
                      <span class="badge text-bg-light text-secondary">{{ optional($challan->updated_at)->format('d/m/Y') }}</span>
                    @else
                      <span class="text-primary fw-semibold">Open</span>
                    @endif
                  </td>
                  <td>
                    @if($isClosed)
                      <a href="{{ $convertedInvoice ? route('sale.edit', $convertedInvoice->id) : '#' }}" class="text-decoration-underline text-primary">
                        Converted To Invoice No.{{ $convertedInvoiceNumber ?? '-' }}
                      </a>
                    @else
                      <a href="{{ route('delivery-challans.convert-to-sale', $challan->id) }}" class="btn btn-sm btn-light border text-uppercase text-primary px-3">
                        Convert To Sale
                      </a>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="dropdown">
                      <button class="btn btn-sm border-0 text-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-vertical"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="{{ route('delivery-challan.edit', $challan->id) }}">View/Edit</a></li>
                        <li><a class="dropdown-item" href="#" onclick="deleteChallan('{{ route('delivery-challan.destroy', $challan->id) }}'); return false;">Delete</a></li>
                        <li><a class="dropdown-item" href="#" onclick="openDeliveryChallanPdf('{{ route('invoice', ['sale_id' => $challan->id]) }}'); return false;">Open PDF</a></li>
                        <li><a class="dropdown-item" href="{{ route('delivery-challan.preview', $challan->id) }}" target="_blank">Preview</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printDeliveryChallan('{{ route('invoice', ['sale_id' => $challan->id, 'print' => 1]) }}'); return false;">Print</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center text-muted py-5">No delivery challans found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </main>

  <!-- ═══════════════════════════════════════════
     SCRIPTS
     ═══════════════════════════════════════════ -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/components.js') }}"></script>
  <script src="{{ asset('js/common.js') }}"></script>
  <script src="{{ asset('js/delivery_challan.js') }}"></script>
  <script>
    function openDeliveryChallanPdf(url) {
      window.open(url, '_blank');
    }

    function printDeliveryChallan(url) {
      window.open(url, '_blank');
    }

    function getVisibleChallanRows() {
      return Array.from(document.querySelectorAll('#challanTable tbody tr.challan-row'))
        .filter((row) => row.style.display !== 'none');
    }

    function exportVisibleChallansToExcel() {
      const table = document.getElementById('challanTable');
      const rows = getVisibleChallanRows();

      if (!table || !rows.length) {
        alert('Export ke liye koi delivery challan available nahi hai.');
        return;
      }

      const headerCells = Array.from(table.querySelectorAll('thead th'))
        .slice(0, 7)
        .map((cell) => `"${cell.innerText.trim().replace(/"/g, '""')}"`);

      const csvLines = [headerCells.join(',')];

      rows.forEach((row) => {
        const cells = Array.from(row.querySelectorAll('td')).slice(0, 7);
        const values = cells.map((cell) => {
          const text = cell.innerText.replace(/\s+/g, ' ').trim();
          return `"${text.replace(/"/g, '""')}"`;
        });

        csvLines.push(values.join(','));
      });

      const now = new Date();
      const filename = `delivery-challans-${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}.csv`;
      const blob = new Blob(["\uFEFF" + csvLines.join('\n')], { type: 'text/csv;charset=utf-8;' });
      const link = document.createElement('a');

      link.href = URL.createObjectURL(blob);
      link.download = filename;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      URL.revokeObjectURL(link.href);
    }

    function printVisibleChallans() {
      const table = document.getElementById('challanTable');
      const rows = getVisibleChallanRows();

      if (!table || !rows.length) {
        alert('Print ke liye koi delivery challan available nahi hai.');
        return;
      }

      const headerHtml = Array.from(table.querySelectorAll('thead th'))
        .slice(0, 7)
        .map((cell) => `<th>${cell.innerText.trim()}</th>`)
        .join('');

      const bodyHtml = rows.map((row) => {
        const cols = Array.from(row.querySelectorAll('td'))
          .slice(0, 7)
          .map((cell) => `<td>${cell.innerText.replace(/\n+/g, '<br>')}</td>`)
          .join('');

        return `<tr>${cols}</tr>`;
      }).join('');

      const printWindow = window.open('', '_blank', 'width=1000,height=700');

      if (!printWindow) {
        alert('Print window open nahi ho saki. Browser popup allow karein.');
        return;
      }

      printWindow.document.write(`
        <html>
          <head>
            <title>Delivery Challans Print</title>
            <style>
              body { font-family: Arial, sans-serif; padding: 24px; color: #111827; }
              h2 { margin-bottom: 16px; }
              table { width: 100%; border-collapse: collapse; }
              th, td { border: 1px solid #d1d5db; padding: 10px; text-align: left; font-size: 13px; vertical-align: top; }
              th { background: #f3f4f6; }
            </style>
          </head>
          <body>
            <h2>Delivery Challans</h2>
            <table>
              <thead><tr>${headerHtml}</tr></thead>
              <tbody>${bodyHtml}</tbody>
            </table>
          </body>
        </html>
      `);
      printWindow.document.close();
      printWindow.focus();
      printWindow.print();
    }

    function deleteChallan(url) {
      if (!confirm('Are you sure you want to delete this delivery challan?')) {
        return;
      }

      const csrfToken =
        document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
        || window.App?.csrfToken
        || '';

      fetch(url, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
      })
        .then(async (response) => {
          const data = await response.json();

          if (!response.ok) {
            throw new Error(data?.message || 'Delete failed');
          }

          window.location.reload();
        })
        .catch((error) => {
          alert(error.message || 'Unable to delete delivery challan.');
        });
    }

    // Filter functionality
    $(document).ready(function () {
      const $periodSelect = $("#challanPeriodSelect");
      const $firmSelect = $("#challanFirmSelect");
      const $dateRangeDisplay = $("#challanDateRangeDisplay");
      const $customDateRange = $("#challanCustomDateRange");
      const $customFrom = $("#challanCustomFrom");
      const $customTo = $("#challanCustomTo");
      const $printBtn = $("#challanPrintBtn");
      const $excelBtn = $("#challanExcelBtn");

      let periodFilter = $periodSelect.val() || "all";
      let firmFilter = $firmSelect.val() || "";
      let customFrom = "";
      let customTo = "";

      function formatDisplayDate(date) {
        const dd = String(date.getDate()).padStart(2, "0");
        const mm = String(date.getMonth() + 1).padStart(2, "0");
        const yyyy = date.getFullYear();
        return `${dd}/${mm}/${yyyy}`;
      }

      function formatIsoDate(date) {
        const dd = String(date.getDate()).padStart(2, "0");
        const mm = String(date.getMonth() + 1).padStart(2, "0");
        const yyyy = date.getFullYear();
        return `${yyyy}-${mm}-${dd}`;
      }

      function parseRowDate(value) {
        const parts = (value || "").trim().split(/[-\/]/);
        if (parts.length !== 3) return null;

        const day = parseInt(parts[0], 10);
        const month = parseInt(parts[1], 10) - 1;
        const year = parseInt(parts[2], 10);

        if ([day, month, year].some(Number.isNaN)) return null;
        return new Date(year, month, day);
      }

      function updateRangeDisplay(from, to) {
        if (!from || !to) {
          $dateRangeDisplay.text("");
          return;
        }

        $dateRangeDisplay.text(`${formatDisplayDate(from)} To ${formatDisplayDate(to)}`);
      }

      function getPeriodRange(period) {
        const now = new Date();
        let start = null;
        let end = null;

        if (period === "this_month") {
          start = new Date(now.getFullYear(), now.getMonth(), 1);
          end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
        } else if (period === "last_month") {
          start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
          end = new Date(now.getFullYear(), now.getMonth(), 0);
        } else if (period === "this_quarter") {
          const quarterStartMonth = Math.floor(now.getMonth() / 3) * 3;
          start = new Date(now.getFullYear(), quarterStartMonth, 1);
          end = new Date(now.getFullYear(), quarterStartMonth + 3, 0);
        } else if (period === "this_year") {
          start = new Date(now.getFullYear(), 0, 1);
          end = new Date(now.getFullYear(), 11, 31);
        }

        return { start, end };
      }

      function setCustomMode(isCustom) {
        $dateRangeDisplay.toggleClass("d-none", isCustom);
        $customDateRange.toggleClass("d-none", !isCustom).toggleClass("d-flex", isCustom);
      }

      function applyChallanFilters() {
        $("#challanTable tbody tr.challan-row").each(function () {
          const $row = $(this);
          const rowText = $row.text().toLowerCase().replace(/\s+/g, " ").trim();
          const partyName = $row.find("td").eq(1).text().trim().toLowerCase();
          const rowDateText = $row.find("td").eq(0).text().trim();
          const rowDate = parseRowDate(rowDateText);

          let visible = true;

          if (visible && firmFilter && partyName !== firmFilter.toLowerCase()) {
            visible = false;
          }

          if (visible && periodFilter !== "all") {
            let rangeStart = null;
            let rangeEnd = null;

            if (periodFilter === "custom") {
              rangeStart = customFrom ? new Date(customFrom) : null;
              rangeEnd = customTo ? new Date(customTo) : null;
            } else {
              const range = getPeriodRange(periodFilter);
              rangeStart = range.start;
              rangeEnd = range.end;
            }

            if (!rowDate || !rangeStart || !rangeEnd) {
              visible = false;
            } else {
              rangeStart.setHours(0, 0, 0, 0);
              rangeEnd.setHours(23, 59, 59, 999);
              rowDate.setHours(12, 0, 0, 0);

              if (rowDate < rangeStart || rowDate > rangeEnd) {
                visible = false;
              }
            }
          }

          $row.toggle(visible);
        });
      }

      function initializePeriodFilter() {
        if (periodFilter === "custom") {
          const today = new Date();
          const todayIso = formatIsoDate(today);
          $customFrom.val(todayIso);
          $customTo.val(todayIso);
          customFrom = todayIso;
          customTo = todayIso;
          setCustomMode(true);
          return;
        }

        const range = getPeriodRange(periodFilter);
        setCustomMode(false);
        updateRangeDisplay(range.start, range.end);
      }

      initializePeriodFilter();
      applyChallanFilters();

      $periodSelect.on("change", function () {
        periodFilter = $(this).val() || "all";
        initializePeriodFilter();
        applyChallanFilters();
      });

      $firmSelect.on("change", function () {
        firmFilter = $(this).val() || "";
        applyChallanFilters();
      });

      $customFrom.on("change", function () {
        customFrom = $(this).val() || "";
        applyChallanFilters();
      });

      $customTo.on("change", function () {
        customTo = $(this).val() || "";
        applyChallanFilters();
      });

      $printBtn.on("click", function () {
        printVisibleChallans();
      });

      $excelBtn.on("click", function () {
        exportVisibleChallansToExcel();
      });
    });
  </script>

</body>

</html>
