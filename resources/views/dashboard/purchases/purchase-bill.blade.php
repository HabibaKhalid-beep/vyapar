<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vyapar - Purchase Bills</title>
    <meta name="description" content="Manage purchase bills in Vyapar.">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
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
        .purchase-page-card {
            border: 1px solid #d9e3ef;
            border-radius: 14px;
            background: #fff;
        }
        .purchase-summary-card {
            min-width: 180px;
            border-radius: 14px;
            padding: 16px 18px;
        }
        .purchase-summary-card h6 {
            font-size: 14px;
            margin-bottom: 8px;
            color: #556277;
        }
        .purchase-summary-card strong {
            font-size: 30px;
            line-height: 1;
        }
        .purchase-table th {
            font-size: 13px;
            color: #556277;
            font-weight: 700;
            background: #f8fbff;
            border-bottom: 1px solid #dbe5f0;
            white-space: nowrap;
        }
        .purchase-table td {
            vertical-align: middle;
            border-bottom: 1px solid #ecf1f6;
        }
        .purchase-table tbody tr:hover {
            background: #f8fbff;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }
        .status-paid {
            background: #e9fbf1;
            color: #0a9b63;
        }
        .status-unpaid {
            background: #eef4ff;
            color: #2563eb;
        }
        .action-icon-btn {
            width: 34px;
            height: 34px;
            border: 1px solid #d9e3ef;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #6b7280;
            background: #fff;
            text-decoration: none;
        }
        .action-icon-btn:hover {
            color: #111827;
            background: #f8fbff;
        }
        .purchase-search {
            max-width: 280px;
        }
        .purchase-search .form-control {
            border-radius: 12px;
            padding-left: 38px;
        }
        .purchase-search .fa-magnifying-glass {
            position: absolute;
            top: 12px;
            left: 14px;
            color: #9aa4b2;
        }
        .purchase-empty {
            padding: 48px 16px;
            color: #7b8794;
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
    </style>
</head>
<body data-page="purchase-bill">
<main class="main-content" id="mainContent">
    <div class="d-flex justify-content-between align-items-center bg-light mb-3 p-4 rounded">
        <div>
            <h4 class="mb-0">Purchase Bills</h4>
        </div>
        <button class="btn rounded-pill px-4" style="background-color:#D4112E;" onclick="window.location.href='{{ route('purchase-bill.create') }}'">
            <span class="text-light">+ Add Purchase</span>
        </button>
    </div>

    <div class="d-flex flex-wrap gap-3 mb-3">
        <div class="purchase-summary-card" style="background:#dff8ee;">
            <h6>Paid</h6>
            <strong>Rs {{ number_format($paidTotal ?? 0, 2) }}</strong>
        </div>
        <div class="purchase-summary-card" style="background:#e7f1ff;">
            <h6>Unpaid</h6>
            <strong>Rs {{ number_format($unpaidTotal ?? 0, 2) }}</strong>
        </div>
        <div class="purchase-summary-card" style="background:#ffe7c7;">
            <h6>Total</h6>
            <strong>Rs {{ number_format($grandTotal ?? 0, 2) }}</strong>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center bg-light mb-3 px-3 py-2 rounded">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="small fw-semibold">Filter By:</span>

            <div class="d-flex rounded-pill filter-pill">
                <div class="filter-left">
                    <select id="purchaseBillPeriodSelect" class="filter-select">
                        <option value="all" selected>All Purchase Invoices</option>
                        <option value="this_month">This Month</option>
                        <option value="last_month">Last Month</option>
                        <option value="this_quarter">This Quarter</option>
                        <option value="this_year">This Year</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                <div class="filter-right">
                    <span id="purchaseBillDateRangeDisplay"></span>
                    <div id="purchaseBillCustomDateRange" class="d-none align-items-center gap-1">
                        <input id="purchaseBillCustomFrom" type="date" class="date-input" />
                        <span>to</span>
                        <input id="purchaseBillCustomTo" type="date" class="date-input" />
                    </div>
                </div>
            </div>

            <div class="filter-pill small-pill">
                <select id="purchaseBillFirmSelect" class="filter-select text-center">
                    <option value="">All Firms</option>
                    @foreach($purchases->map(fn($purchase) => $purchase->party_name ?: ($purchase->party?->name))->filter()->unique()->values() as $firm)
                        <option value="{{ $firm }}">{{ $firm }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="purchase-page-card p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-3">
            <h5 class="mb-0">Transactions</h5>
            <div class="d-flex align-items-center gap-2">
                <div class="position-relative purchase-search">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="purchaseBillSearch" value="{{ $search ?? '' }}" class="form-control" placeholder="Search by invoice or party">
                </div>
                <button type="button" class="action-icon-btn" onclick="window.print()" title="Print list">
                    <i class="fa-solid fa-print"></i>
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table purchase-table align-middle mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice No.</th>
                        <th>Party Name</th>
                        <th>Payment Type</th>
                        <th class="text-end">Amount</th>
                        <th class="text-end">Balance Due</th>
                        <th>Status</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($purchases as $purchase)
                        @php
                            $primaryPayment = $purchase->payments->first();
                            $paymentLabel = $primaryPayment?->bankAccount?->display_with_account
                                ?? $primaryPayment?->payment_type
                                ?? '-';
                            $status = (float) ($purchase->balance ?? 0) <= 0 ? 'Paid' : 'Unpaid';
                        @endphp
                        <tr class="purchase-bill-row">
                            <td>{{ optional($purchase->bill_date)->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $purchase->bill_number ?? '-' }}</td>
                            <td>{{ $purchase->party_name ?: ($purchase->party?->name ?? '-') }}</td>
                            <td>{{ $paymentLabel }}</td>
                            <td class="text-end">{{ number_format($purchase->grand_total ?? 0, 2) }}</td>
                            <td class="text-end">{{ number_format($purchase->balance ?? 0, 2) }}</td>
                            <td>
                                <span class="status-badge {{ $status === 'Paid' ? 'status-paid' : 'status-unpaid' }}">
                                    {{ $status }}
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center gap-2">
                                    <a href="{{ route('purchase-bills.print', $purchase) }}" target="_blank" class="action-icon-btn" title="Print">
                                        <i class="fa-solid fa-print"></i>
                                    </a>
                                    <a href="{{ route('purchase-bills.preview', $purchase) }}" target="_blank" class="action-icon-btn" title="Preview">
                                        <i class="fa-regular fa-eye"></i>
                                    </a>
                                    <div class="dropdown">
                                        <button class="action-icon-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="{{ route('purchase-bills.preview', $purchase) }}" target="_blank">View</a></li>
                                            <li><a class="dropdown-item" href="{{ route('purchase-bills.edit', $purchase) }}">Edit</a></li>
                                            <li><a class="dropdown-item" href="{{ route('purchase-bills.preview', $purchase) }}" target="_blank">Preview</a></li>
                                            <li><a class="dropdown-item" href="{{ route('purchase-bills.pdf', $purchase) }}" target="_blank">Open PDF</a></li>
                                            <li><a class="dropdown-item" href="{{ route('purchase-bills.print', $purchase) }}" target="_blank">Print</a></li>
                                            <li>
                                                <button
                                                    type="button"
                                                    class="dropdown-item"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#purchaseHistoryModal"
                                                    data-bill="{{ $purchase->bill_number ?? '-' }}"
                                                    data-created="{{ optional($purchase->created_at)->format('d/m/Y h:i A') ?? '-' }}"
                                                    data-updated="{{ optional($purchase->updated_at)->format('d/m/Y h:i A') ?? '-' }}"
                                                    data-items="{{ $purchase->items->count() }}"
                                                    data-payments="{{ $purchase->payments->count() }}"
                                                >
                                                    History
                                                </button>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger js-delete-purchase" data-id="{{ $purchase->id }}">
                                                    Delete
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="purchase-empty text-center">
                                No purchase bills found yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</main>

<div class="modal fade" id="purchaseHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Purchase History</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-2"><strong>Bill No:</strong> <span id="historyBillNo">-</span></div>
                <div class="mb-2"><strong>Created At:</strong> <span id="historyCreatedAt">-</span></div>
                <div class="mb-2"><strong>Updated At:</strong> <span id="historyUpdatedAt">-</span></div>
                <div class="mb-2"><strong>Total Items:</strong> <span id="historyItemsCount">0</span></div>
                <div><strong>Total Payments:</strong> <span id="historyPaymentsCount">0</span></div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/components.js') }}"></script>
<script src="{{ asset('js/common.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
        const searchInput = document.getElementById('purchaseBillSearch');
        const periodSelect = document.getElementById('purchaseBillPeriodSelect');
        const firmSelect = document.getElementById('purchaseBillFirmSelect');
        const dateRangeDisplay = document.getElementById('purchaseBillDateRangeDisplay');
        const customDateRange = document.getElementById('purchaseBillCustomDateRange');
        const customFromInput = document.getElementById('purchaseBillCustomFrom');
        const customToInput = document.getElementById('purchaseBillCustomTo');

        let globalSearch = (searchInput?.value || '').toLowerCase().trim();
        let periodFilter = periodSelect?.value || 'all';
        let firmFilter = firmSelect?.value || '';
        let customFrom = '';
        let customTo = '';

        function formatDisplayDate(date) {
            const dd = String(date.getDate()).padStart(2, '0');
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const yyyy = date.getFullYear();
            return `${dd}/${mm}/${yyyy}`;
        }

        function formatIsoDate(date) {
            const dd = String(date.getDate()).padStart(2, '0');
            const mm = String(date.getMonth() + 1).padStart(2, '0');
            const yyyy = date.getFullYear();
            return `${yyyy}-${mm}-${dd}`;
        }

        function parseRowDate(value) {
            const parts = (value || '').trim().split(/[\/-]/);
            if (parts.length !== 3) return null;

            const day = parseInt(parts[0], 10);
            const month = parseInt(parts[1], 10) - 1;
            const year = parseInt(parts[2], 10);

            if ([day, month, year].some(Number.isNaN)) return null;
            return new Date(year, month, day);
        }

        function updateRangeDisplay(from, to) {
            if (!dateRangeDisplay) return;
            dateRangeDisplay.textContent = from && to ? `${formatDisplayDate(from)} To ${formatDisplayDate(to)}` : '';
        }

        function getPeriodRange(period) {
            const now = new Date();
            let start = null;
            let end = null;

            if (period === 'this_month') {
                start = new Date(now.getFullYear(), now.getMonth(), 1);
                end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
            } else if (period === 'last_month') {
                start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
                end = new Date(now.getFullYear(), now.getMonth(), 0);
            } else if (period === 'this_quarter') {
                const quarterStartMonth = Math.floor(now.getMonth() / 3) * 3;
                start = new Date(now.getFullYear(), quarterStartMonth, 1);
                end = new Date(now.getFullYear(), quarterStartMonth + 3, 0);
            } else if (period === 'this_year') {
                start = new Date(now.getFullYear(), 0, 1);
                end = new Date(now.getFullYear(), 11, 31);
            }

            return { start, end };
        }

        function setCustomMode(isCustom) {
            if (!dateRangeDisplay || !customDateRange) return;
            dateRangeDisplay.classList.toggle('d-none', isCustom);
            customDateRange.classList.toggle('d-none', !isCustom);
            customDateRange.classList.toggle('d-flex', isCustom);
        }

        function applyPurchaseBillFilters() {
            document.querySelectorAll('.purchase-bill-row').forEach((row) => {
                const cells = row.querySelectorAll('td');
                const rowText = (row.textContent || '').toLowerCase().replace(/\s+/g, ' ').trim();
                const rowDate = parseRowDate(cells[0]?.textContent || '');
                const rowFirm = (cells[2]?.textContent || '').trim().toLowerCase();
                let visible = true;

                if (globalSearch && !rowText.includes(globalSearch)) {
                    visible = false;
                }

                if (visible && firmFilter && rowFirm !== firmFilter.toLowerCase()) {
                    visible = false;
                }

                if (visible && periodFilter !== 'all') {
                    let rangeStart = null;
                    let rangeEnd = null;

                    if (periodFilter === 'custom') {
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

                row.style.display = visible ? '' : 'none';
            });
        }

        function initializePeriodFilter() {
            if (periodFilter === 'custom') {
                const today = new Date();
                const todayIso = formatIsoDate(today);
                customFromInput.value = todayIso;
                customToInput.value = todayIso;
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
        applyPurchaseBillFilters();

        searchInput?.addEventListener('input', function () {
            globalSearch = this.value.toLowerCase().trim();
            applyPurchaseBillFilters();
        });

        periodSelect?.addEventListener('change', function () {
            periodFilter = this.value || 'all';

            if (periodFilter === 'custom') {
                const today = new Date();
                const todayIso = formatIsoDate(today);
                customFromInput.value = todayIso;
                customToInput.value = todayIso;
                customFrom = todayIso;
                customTo = todayIso;
                setCustomMode(true);
            } else {
                const range = getPeriodRange(periodFilter);
                setCustomMode(false);
                updateRangeDisplay(range.start, range.end);
            }

            applyPurchaseBillFilters();
        });

        firmSelect?.addEventListener('change', function () {
            firmFilter = this.value || '';
            applyPurchaseBillFilters();
        });

        customFromInput?.addEventListener('change', function () {
            customFrom = this.value || '';
            applyPurchaseBillFilters();
        });

        customToInput?.addEventListener('change', function () {
            customTo = this.value || '';
            applyPurchaseBillFilters();
        });

        document.querySelectorAll('.js-delete-purchase').forEach((button) => {
            button.addEventListener('click', async function () {
                const purchaseId = this.dataset.id;
                if (!purchaseId || !confirm('Delete this purchase bill?')) {
                    return;
                }

                const response = await fetch(`/dashboard/purchase-bills/${purchaseId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                });

                const data = await response.json().catch(() => ({}));
                if (!response.ok || !data.success) {
                    alert(data.message || 'Unable to delete purchase bill.');
                    return;
                }

                window.location.reload();
            });
        });

        const historyModal = document.getElementById('purchaseHistoryModal');
        if (historyModal) {
            historyModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                if (!button) return;

                document.getElementById('historyBillNo').textContent = button.getAttribute('data-bill') || '-';
                document.getElementById('historyCreatedAt').textContent = button.getAttribute('data-created') || '-';
                document.getElementById('historyUpdatedAt').textContent = button.getAttribute('data-updated') || '-';
                document.getElementById('historyItemsCount').textContent = button.getAttribute('data-items') || '0';
                document.getElementById('historyPaymentsCount').textContent = button.getAttribute('data-payments') || '0';
            });
        }
    });
</script>
</body>
</html>
