{{-- resources/views/dashboard/reports/tabs/party-reports.blade.php --}}
{{-- Include this file in your main reports.blade.php with: @include('dashboard.reports.tabs.party-reports') --}}

{{-- ===================== PARTY STATEMENT TAB ===================== --}}
<div id="tab-Party Statement" class="report-tab-content d-none d-flex flex-column h-100 bg-white">

    {{-- Filter Bar --}}
    <div class="d-flex align-items-center bg-light px-4 py-2 gap-3 flex-wrap">
        <select id="ps-period" class="bg-transparent border-0 fs-6 fw-bold" style="outline:none;">
            <option value="this_month" selected>This Month</option>
            <option value="last_month">Last Month</option>
            <option value="this_quarter">This Quarter</option>
            <option value="this_year">This Year</option>
            <option value="custom">Custom</option>
        </select>
        <div class="d-flex align-items-center" style="border:1px solid #AAAAAA; border-radius:5px; height:32px; padding:0 10px; font-size:13px;">
            <input type="date" id="ps-date-from" class="bg-transparent border-0" style="outline:none; font-size:13px;">
            <span class="mx-2 text-muted">To</span>
            <input type="date" id="ps-date-to" class="bg-transparent border-0" style="outline:none; font-size:13px;">
        </div>
        {{-- Party Selector --}}
        <select id="ps-party-select" class="form-select form-select-sm" style="width:200px;">
            <option value="">-- Select Party --</option>
            @foreach($parties as $party)
                <option value="{{ $party->id }}">{{ $party->name }}</option>
            @endforeach
        </select>
        <div class="ms-auto d-flex gap-3 text-secondary">
            <i class="fa-solid fa-file-excel fs-5 cursor-pointer" id="ps-excel-btn" title="Export Excel"></i>
            <i class="fa-solid fa-print fs-5 cursor-pointer" id="ps-print-btn" title="Print"></i>
        </div>
    </div>

    {{-- View Toggle --}}
    <div class="px-4 py-2 d-flex align-items-center gap-3 border-bottom">
        <span class="text-secondary fw-medium" style="font-size:14px;">View:</span>
        <label class="d-flex align-items-center gap-2 mb-0 cursor-pointer">
            <input type="radio" name="psView" value="vyapar" checked class="ps-radio"> <span style="font-size:14px;">Vyapar</span>
        </label>
        <label class="d-flex align-items-center gap-2 mb-0 cursor-pointer">
            <input type="radio" name="psView" value="accounting" class="ps-radio"> <span style="font-size:14px;">Accounting</span>
        </label>
    </div>

    {{-- Summary bar --}}
    <div id="ps-summary-bar" class="d-none px-4 py-2 bg-light border-bottom d-flex gap-4 flex-wrap" style="font-size:13px;">
        <span>Opening Balance: <strong id="ps-opening-bal">Rs 0.00</strong></span>
        <span>Closing Balance: <strong id="ps-closing-bal">Rs 0.00</strong></span>
        <span>Total Debit: <strong id="ps-total-debit" class="text-danger">Rs 0.00</strong></span>
        <span>Total Credit: <strong id="ps-total-credit" class="text-success">Rs 0.00</strong></span>
    </div>

    {{-- Table --}}
    <div class="flex-grow-1 overflow-auto">
        <table class="table table-hover mb-0 w-100" id="ps-table">
            <thead class="table-light" style="font-size:12px; text-transform:uppercase; position:sticky; top:0; z-index:1;">
                <tr>
                    <th>Date</th>
                    <th>TXN Type</th>
                    <th>Reference No.</th>
                    <th>Payment Type</th>
                    <th class="text-end">Debit</th>
                    <th class="text-end">Credit</th>
                    <th class="text-end">Running Balance</th>
                    <th>Print/Share</th>
                </tr>
            </thead>
            <tbody id="ps-tbody">
                <tr><td colspan="8" class="text-center text-muted py-5">Select a party and date range to view statement.</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Footer Summary --}}
    <div class="bg-white p-3 border-top d-flex flex-wrap justify-content-between align-items-center" style="font-size:13px; box-shadow:0 -4px 6px -1px rgba(0,0,0,0.05);">
        <div class="d-flex gap-4 flex-wrap">
            <span>Total Sale: <strong id="ps-foot-sale">Rs 0.00</strong></span>
            <span>Total Purchase: <strong id="ps-foot-purchase">Rs 0.00</strong></span>
            <span>Total Money-In: <strong id="ps-foot-moneyin">Rs 0.00</strong></span>
            <span>Total Money-Out: <strong id="ps-foot-moneyout">Rs 0.00</strong></span>
        </div>
        <div class="text-end">
            <span class="text-dark fw-bold">Total Receivable: </span>
            <span class="fw-bold" style="color:#00b894; font-size:18px;" id="ps-foot-receivable">Rs 0.00</span>
        </div>
    </div>
</div>


{{-- ===================== ALL PARTIES TAB ===================== --}}
<div id="tab-All Parties" class="report-tab-content d-none d-flex flex-column h-100 bg-white">

    {{-- Filter Bar --}}
    <div class="d-flex align-items-center bg-light px-4 py-2 gap-3 flex-wrap">
        <div class="d-flex align-items-center gap-2">
            <input type="checkbox" id="ap-date-filter-check">
            <label for="ap-date-filter-check" class="mb-0" style="font-size:13px;">Date Filter</label>
            <input type="date" id="ap-date-input" class="form-control form-control-sm d-none" style="width:150px;">
        </div>
        <select id="ap-type-filter" class="form-select form-select-sm" style="width:140px;">
            <option value="">All Parties</option>
            <option value="receivable">Receivable</option>
            <option value="payable">Payable</option>
        </select>
        <div class="ms-2 position-relative" style="width:220px;">
            <i class="fa-solid fa-magnifying-glass position-absolute text-secondary" style="left:10px;top:50%;transform:translateY(-50%);"></i>
            <input type="text" id="ap-search" class="form-control form-control-sm" placeholder="Search party..." style="padding-left:32px;">
        </div>
        <div class="ms-auto d-flex gap-3 text-secondary">
            <i class="fa-solid fa-file-excel fs-5 cursor-pointer" id="ap-excel-btn" title="Export Excel"></i>
            <i class="fa-solid fa-print fs-5 cursor-pointer" id="ap-print-btn" title="Print"></i>
        </div>
    </div>

    {{-- Table --}}
    <div class="flex-grow-1 overflow-auto">
        <table class="table table-hover mb-0 w-100" id="ap-table">
            <thead class="table-light" style="font-size:12px; text-transform:uppercase; position:sticky; top:0; z-index:1;">
                <tr>
                    <th><input type="checkbox" id="ap-select-all"></th>
                    <th>Party Name</th>
                    <th>Email</th>
                    <th>Phone No.</th>
                    <th class="text-end">Receivable Balance</th>
                    <th class="text-end">Payable Balance</th>
                    <th class="text-end">Credit Limit</th>
                </tr>
            </thead>
            <tbody id="ap-tbody">
                <tr><td colspan="7" class="text-center text-muted py-5"><i class="fa fa-spinner fa-spin me-2"></i>Loading...</td></tr>
            </tbody>
        </table>
    </div>

    {{-- Footer --}}
    <div class="bg-white px-4 py-3 border-top d-flex justify-content-between align-items-center" style="font-size:13px; box-shadow:0 -4px 6px -1px rgba(0,0,0,0.02);">
        <div>
            <span class="text-dark fw-bold">Total Receivable: </span>
            <span class="fw-bold" style="color:#10b981;" id="ap-total-receivable">Rs 0.00</span>
        </div>
        <div>
            <span class="text-dark fw-bold">Total Payable: </span>
            <span class="fw-bold" style="color:#ef4444;" id="ap-total-payable">Rs 0.00</span>
        </div>
    </div>
</div>


{{-- ===================== PARTY REPORT BY ITEMS TAB ===================== --}}
<div id="tab-Party Report by Items" class="report-tab-content d-none d-flex flex-column h-100 bg-white">

    {{-- Filter Bar --}}
    <div class="d-flex align-items-center bg-light px-4 py-2 gap-3 flex-wrap">
        <select id="pri-period" class="bg-transparent border-0 fw-bold" style="outline:none;">
            <option value="this_month" selected>This Month</option>
            <option value="last_month">Last Month</option>
            <option value="this_quarter">This Quarter</option>
            <option value="this_year">This Year</option>
            <option value="custom">Custom</option>
        </select>
        <div class="d-flex align-items-center" style="border:1px solid #AAAAAA; border-radius:5px; height:32px; padding:0 10px; font-size:13px;">
            <input type="date" id="pri-date-from" class="bg-transparent border-0" style="outline:none; font-size:13px;">
            <span class="mx-2 text-muted">To</span>
            <input type="date" id="pri-date-to" class="bg-transparent border-0" style="outline:none; font-size:13px;">
        </div>
        <select id="pri-category" class="form-select form-select-sm" style="width:160px;">
            <option value="">All Categories</option>
        </select>
        <select id="pri-item" class="form-select form-select-sm" style="width:160px;">
            <option value="">All Items</option>
        </select>
        <div class="ms-2 position-relative" style="width:200px;">
            <i class="fa-solid fa-magnifying-glass position-absolute text-secondary" style="left:10px;top:50%;transform:translateY(-50%);"></i>
            <input type="text" id="pri-search" class="form-control form-control-sm" placeholder="Search..." style="padding-left:32px;">
        </div>
        <div class="ms-auto d-flex gap-3 text-secondary">
            <i class="fa-solid fa-file-excel fs-5 cursor-pointer" id="pri-excel-btn"></i>
            <i class="fa-solid fa-print fs-5 cursor-pointer" id="pri-print-btn"></i>
        </div>
    </div>

    {{-- Table --}}
    <div class="flex-grow-1 overflow-auto">
        <table class="table table-hover mb-0 w-100" id="pri-table">
            <thead class="table-light" style="font-size:12px; text-transform:uppercase; position:sticky; top:0; z-index:1;">
                <tr>
                    <th>Party Name</th>
                    <th class="text-end">Sale Qty</th>
                    <th class="text-end">Sale Amount</th>
                    <th class="text-end">Purchase Qty</th>
                    <th class="text-end">Purchase Amount</th>
                </tr>
            </thead>
            <tbody id="pri-tbody">
                <tr><td colspan="5" class="text-center text-muted py-5"><i class="fa fa-spinner fa-spin me-2"></i>Loading...</td></tr>
            </tbody>
            <tfoot class="table-light fw-bold" id="pri-tfoot" style="position:sticky; bottom:0;">
                <tr>
                    <td>Total:</td>
                    <td class="text-end" id="pri-total-sale-qty">0</td>
                    <td class="text-end" id="pri-total-sale-amt">Rs 0.00</td>
                    <td class="text-end" id="pri-total-pur-qty">0</td>
                    <td class="text-end" id="pri-total-pur-amt">Rs 0.00</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>


{{-- ===================== SALE PURCHASE BY PARTY TAB ===================== --}}
<div id="tab-Partysalepurchase" class="report-tab-content d-none d-flex flex-column h-100 bg-white">

    {{-- Filter Bar --}}
    <div class="d-flex align-items-center bg-light px-4 py-2 gap-3 flex-wrap">
        <select id="spp-period" class="bg-transparent border-0 fw-bold" style="outline:none;">
            <option value="this_month" selected>This Month</option>
            <option value="last_month">Last Month</option>
            <option value="this_quarter">This Quarter</option>
            <option value="this_year">This Year</option>
            <option value="custom">Custom</option>
        </select>
        <div class="d-flex align-items-center" style="border:1px solid #AAAAAA; border-radius:5px; height:32px; padding:0 10px; font-size:13px;">
            <input type="date" id="spp-date-from" class="bg-transparent border-0" style="outline:none; font-size:13px;">
            <span class="mx-2 text-muted">To</span>
            <input type="date" id="spp-date-to" class="bg-transparent border-0" style="outline:none; font-size:13px;">
        </div>
        <div class="ms-2 position-relative" style="width:200px;">
            <i class="fa-solid fa-magnifying-glass position-absolute text-secondary" style="left:10px;top:50%;transform:translateY(-50%);"></i>
            <input type="text" id="spp-search" class="form-control form-control-sm" placeholder="Search party..." style="padding-left:32px;">
        </div>
        <div class="ms-auto d-flex gap-3 text-secondary">
            <i class="fa-solid fa-file-excel fs-5 cursor-pointer" id="spp-excel-btn"></i>
            <i class="fa-solid fa-print fs-5 cursor-pointer" id="spp-print-btn"></i>
        </div>
    </div>

    {{-- Table --}}
    <div class="flex-grow-1 overflow-auto">
        <table class="table table-hover mb-0 w-100" id="spp-table">
            <thead class="table-light" style="font-size:12px; text-transform:uppercase; position:sticky; top:0; z-index:1;">
                <tr>
                    <th>Party Name</th>
                    <th class="text-end">Sale Amount</th>
                    <th class="text-end">Purchase Amount</th>
                </tr>
            </thead>
            <tbody id="spp-tbody">
                <tr><td colspan="3" class="text-center text-muted py-5"><i class="fa fa-spinner fa-spin me-2"></i>Loading...</td></tr>
            </tbody>
            <tfoot class="table-light fw-bold" style="position:sticky; bottom:0;">
                <tr>
                    <td>Total:</td>
                    <td class="text-end" id="spp-total-sale">Rs 0.00</td>
                    <td class="text-end" id="spp-total-purchase">Rs 0.00</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>


{{-- ===================== SALE PURCHASE BY PARTY GROUP TAB ===================== --}}
<div id="tab-Partysalepurchasegroup" class="report-tab-content d-none d-flex flex-column h-100 bg-white">

    {{-- Filter Bar --}}
    <div class="d-flex align-items-center bg-light px-4 py-2 gap-3 flex-wrap">
        <select id="spg-period" class="bg-transparent border-0 fw-bold" style="outline:none;">
            <option value="this_month" selected>This Month</option>
            <option value="last_month">Last Month</option>
            <option value="this_quarter">This Quarter</option>
            <option value="this_year">This Year</option>
            <option value="custom">Custom</option>
        </select>
        <div class="d-flex align-items-center" style="border:1px solid #AAAAAA; border-radius:5px; height:32px; padding:0 10px; font-size:13px;">
            <input type="date" id="spg-date-from" class="bg-transparent border-0" style="outline:none; font-size:13px;">
            <span class="mx-2 text-muted">To</span>
            <input type="date" id="spg-date-to" class="bg-transparent border-0" style="outline:none; font-size:13px;">
        </div>
        <div class="ms-2 position-relative" style="width:200px;">
            <i class="fa-solid fa-magnifying-glass position-absolute text-secondary" style="left:10px;top:50%;transform:translateY(-50%);"></i>
            <input type="text" id="spg-search" class="form-control form-control-sm" placeholder="Search group..." style="padding-left:32px;">
        </div>
        <div class="ms-auto d-flex gap-3 text-secondary">
            <i class="fa-solid fa-file-excel fs-5 cursor-pointer" id="spg-excel-btn"></i>
            <i class="fa-solid fa-print fs-5 cursor-pointer" id="spg-print-btn"></i>
        </div>
    </div>

    {{-- Table --}}
    <div class="flex-grow-1 overflow-auto">
        <table class="table table-hover mb-0 w-100" id="spg-table">
            <thead class="table-light" style="font-size:12px; text-transform:uppercase; position:sticky; top:0; z-index:1;">
                <tr>
                    <th>Party Group</th>
                    <th class="text-end">Sale Amount</th>
                    <th class="text-end">Purchase Amount</th>
                </tr>
            </thead>
            <tbody id="spg-tbody">
                <tr><td colspan="3" class="text-center text-muted py-5"><i class="fa fa-spinner fa-spin me-2"></i>Loading...</td></tr>
            </tbody>
            <tfoot class="table-light fw-bold" style="position:sticky; bottom:0;">
                <tr>
                    <td>Total:</td>
                    <td class="text-end" id="spg-total-sale">Rs 0.00</td>
                    <td class="text-end" id="spg-total-purchase">Rs 0.00</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<div id="tab-unreceived-invoices" class="report-tab-content d-none d-flex flex-column h-100 bg-white">
    <div class="d-flex align-items-center bg-light px-4 py-3 gap-3 flex-wrap">
        <div class="fw-bold text-dark">Agarri List / Unreceived Invoice PDF</div>
        <input type="date" id="ui-date-from" class="form-control form-control-sm" style="width:160px;">
        <input type="date" id="ui-date-to" class="form-control form-control-sm" style="width:160px;">
        <select id="ui-party" class="form-select form-select-sm" style="width:200px;">
            <option value="">All Parties</option>
            @foreach($parties as $party)
                <option value="{{ $party->id }}">{{ $party->name }}</option>
            @endforeach
        </select>
        <select id="ui-broker" class="form-select form-select-sm" style="width:200px;">
            <option value="">All Brokers</option>
            @foreach(($brokers ?? collect()) as $broker)
                <option value="{{ $broker->id }}">{{ $broker->name }}</option>
            @endforeach
        </select>
        <input type="text" id="ui-city" class="form-control form-control-sm" style="width:180px;" placeholder="City">
        <div class="ms-auto d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" id="ui-reset-btn">Reset</button>
            <button class="btn btn-danger btn-sm" id="ui-generate-pdf-btn">
                <i class="fa-solid fa-file-pdf me-1"></i>Generate PDF
            </button>
        </div>
    </div>

    <div class="p-4 flex-grow-1 overflow-auto">
        <div class="border rounded-4 p-4 bg-white shadow-sm" style="max-width: 980px;">
            <h4 class="fw-bold mb-3">Urdu Khata Format PDF</h4>
            <p class="text-muted mb-3">
                Ye report sirf un sales ko include karegi jahan <strong>balance &gt; 0</strong> ho.
                PDF me party, broker, mobile numbers, WhatsApp, PTCL, item names, soda date, due date,
                deal days aur late days sab show honge.
            </p>
            <ul class="mb-0 text-secondary" style="line-height: 1.9;">
                <li>Sort by due date ascending</li>
                <li>Group by city and party</li>
                <li>Urdu style RTL PDF layout</li>
                <li>Direct PDF download</li>
            </ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const fromInput = document.getElementById('ui-date-from');
    const toInput = document.getElementById('ui-date-to');
    const partyInput = document.getElementById('ui-party');
    const brokerInput = document.getElementById('ui-broker');
    const cityInput = document.getElementById('ui-city');
    const generateButton = document.getElementById('ui-generate-pdf-btn');
    const resetButton = document.getElementById('ui-reset-btn');

    const today = new Date();
    const firstDay = new Date(today.getFullYear(), today.getMonth(), 1);
    const formatIso = (date) => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;

    if (fromInput && !fromInput.value) fromInput.value = formatIso(firstDay);
    if (toInput && !toInput.value) toInput.value = formatIso(today);

    generateButton?.addEventListener('click', function () {
        const params = new URLSearchParams();
        if (fromInput?.value) params.set('from', fromInput.value);
        if (toInput?.value) params.set('to', toInput.value);
        if (partyInput?.value) params.set('party_id', partyInput.value);
        if (brokerInput?.value) params.set('broker_id', brokerInput.value);
        if (cityInput?.value.trim()) params.set('city', cityInput.value.trim());

        window.open(`{{ route('reports.unreceived-invoices.pdf') }}?${params.toString()}`, '_blank');
    });

    resetButton?.addEventListener('click', function () {
        if (fromInput) fromInput.value = formatIso(firstDay);
        if (toInput) toInput.value = formatIso(today);
        if (partyInput) partyInput.value = '';
        if (brokerInput) brokerInput.value = '';
        if (cityInput) cityInput.value = '';
    });
});
</script>
