<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Sales</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Form Styles -->
    <link rel="stylesheet" href="{{ asset('css/saleform_style.css') }}">

</head>
<style>
    /* Dropdown with two columns and scrollbar */
    #partyDropdownMenu {
    min-width: 280px;
    max-width: 100%;
    max-height: 400px;
    overflow-y: auto;
    overflow-x: hidden;
    padding: 0;
}

#partyDropdownMenu li.p-2 {
    padding: 12px;
    border-bottom: 1px solid #e0e0e0;
}

#partyDropdownMenu .form-control-sm {
    font-size: 13px;
}

/* Scrollbar styling for responsive dropdown */
#partyDropdownMenu::-webkit-scrollbar {
    width: 8px;
}

#partyDropdownMenu::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

#partyDropdownMenu::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

#partyDropdownMenu::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* Firefox scrollbar */
#partyDropdownMenu {
    scrollbar-width: thin;
    scrollbar-color: #888 #f1f1f1;
}

.party-option span {
    display: inline-block;
    width: 100%;
}
.party-option span:first-child {
    width: 60%; /* Party name */
}
.party-option span:last-child {
    width: 40%; /* Opening balance */
    text-align: right;
}

.item-picker {
    position: relative;
    min-width: 260px;
    flex: 1;
    overflow: visible;
}

.item-picker-input {
    width: 100%;
    border: 1px solid #cfd8e3;
    border-radius: 6px;
    padding: 10px 14px;
    font-size: 14px;
    background: #fff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.item-picker-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.item-picker-panel {
    position: fixed;
    top: calc(100% + 4px);
    left: 0;
    width: 100%;
    min-width: 320px;
    background: white;
    border: 1px solid #e1e8ed;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1055;
    display: none;
    overflow: hidden;
    max-width: none;
}

.item-picker-panel.open {
    display: block !important;
}

.item-picker-list {
    max-height: 320px;
    overflow-y: auto;
}

.item-picker-list::-webkit-scrollbar {
    width: 8px;
}

.item-picker-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.item-picker-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.item-picker-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.item-picker-add {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 18px;
    color: #2563eb;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.item-picker-add:hover {
    background: #f8fbff;
}

.item-picker-head,
.item-picker-row {
    display: grid;
    grid-template-columns: minmax(0, 2fr) 100px 110px 80px;
    gap: 12px;
    align-items: center;
}

@media (max-width: 768px) {
    .item-picker-head,
    .item-picker-row {
        grid-template-columns: minmax(0, 2fr) 80px 90px 70px;
        gap: 8px;
    }

    .item-picker-panel {
        max-width: 400px;
    }
}

@media (max-width: 576px) {
    .item-picker {
        min-width: 200px;
    }

    .item-picker-head,
    .item-picker-row {
        grid-template-columns: 1fr;
        gap: 4px;
    }

    .item-picker-head span:nth-child(2),
    .item-picker-head span:nth-child(3),
    .item-picker-head span:nth-child(4),
    .item-picker-row > div:nth-child(2),
    .item-picker-row > div:nth-child(3),
    .item-picker-row > div:nth-child(4) {
        display: none;
    }

    .item-picker-panel {
        max-width: 300px;
    }
}

.item-picker-head {
    padding: 10px 18px;
    font-size: 12px;
    font-weight: 700;
    color: #97a3b6;
    text-transform: uppercase;
}

.item-picker-list {
    max-height: 280px;
    overflow-y: auto;
}

.item-picker-row {
    padding: 12px 18px;
    cursor: pointer;
    border-top: 1px solid #f4f7fb;
}

.item-picker-row:hover {
    background: #f8fbff;
}

.item-picker-name small {
    color: #8a94a6;
    margin-left: 6px;
}

.item-picker-stock.neg {
    color: #dc3545;
}

.item-picker-empty {
    padding: 14px 18px;
    color: #8a94a6;
    font-size: 13px;
}

.table-container {
    position: relative;
    overflow-x: auto;
    overflow-y: visible;
    -webkit-overflow-scrolling: touch;
}

.item-table {
    width: max-content;
    min-width: 100%;
    table-layout: auto;
}

.item-table th,
.item-table td {
    white-space: nowrap;
}

.item-table td {
    overflow: visible;
}

.item-table td:first-child + td {
    min-width: 320px;
}

.item-table td input,
.item-table td select {
    min-width: 84px;
}

.item-picker-input {
    min-width: 240px;
}

.modal-stack-top {
    z-index: 1085;
}

.unit-menu-scroll {
    max-height: 260px;
    overflow-y: auto;
}

/* Header style */
.dropdown-header {
    font-weight: 600;
    font-size: 0.9rem;
    background: #f8f9fa;
    border-bottom: 1px solid #ddd;
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Hover effect */
.dropdown-item.party-option:hover {
    background-color: #e2f0ff;
}

.party-dropdown-wrapper,
.broker-dropdown-wrapper {
    width: 100%;
}

/* Hide element utility */
.is-hidden {
    display: none !important;
}

/* Party Group Dropdown Styles */
.party-group-dropdown {
    position: relative;
}

.party-group-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: white;
    border: 1px solid #ced4da;
    padding: 6px 12px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 14px;
}

.party-group-trigger:hover {
    border-color: #adb5bd;
    background: #f8f9fa;
}

.party-group-menu {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ced4da;
    border-radius: 6px;
    margin-top: 4px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    z-index: 100;
    display: none;
}

.party-group-menu.show {
    display: block;
}

.party-group-add-btn {
    width: 100%;
    padding: 10px 12px;
    background: white;
    border: none;
    border-bottom: 1px solid #ced4da;
    text-align: left;
    cursor: pointer;
    color: #007bff;
    font-weight: 500;
    font-size: 13px;
}

.party-group-add-btn:hover {
    background: #f8f9fa;
}

.party-group-options {
    max-height: 200px;
    overflow-y: auto;
}

.party-group-option {
    padding: 8px 12px;
    cursor: pointer;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    font-size: 13px;
    display: block;
}

.party-group-option:hover {
    background: #e2f0ff;
}

/* Modal for Party Group */
.txn-option-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 1050;
}

.txn-option-modal.show {
    display: flex;
}

.txn-option-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
}

.txn-option-dialog {
    position: relative;
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    margin: auto;
    padding: 24px;
    max-width: 400px;
    width: 90%;
    z-index: 1051;
}

.txn-option-title {
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 16px;
}

.txn-option-actions {
    display: flex;
    gap: 8px;
    margin-top: 20px;
    justify-content: flex-end;
}

.txn-option-btn {
    padding: 8px 16px;
    border: 1px solid #ced4da;
    background: white;
    border-radius: 4px;
    cursor: pointer;
    font-size: 14px;
    font-weight: 500;
}

.txn-option-btn.cancel {
    color: #6c757d;
}

.txn-option-btn.cancel:hover {
    background: #f8f9fa;
}

.txn-option-btn.ok {
    background: #007bff;
    color: white;
    border-color: #007bff;
}

.txn-option-btn.ok:hover {
    background: #0056b3;
}

.header-section {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 420px;
    gap: 18px;
    align-items: start;
}

.header-left {
    display: grid;
    grid-template-columns: minmax(220px, 1.2fr) repeat(3, minmax(120px, 1fr));
    gap: 8px;
    min-width: 0;
    align-items: start;
}

.party-selector-group {
    margin-top: 0 !important;
}

.party-selector-panel {
    background: transparent;
    border: none;
    border-radius: 0;
    padding: 0;
    box-shadow: none;
}

.party-dropdown-wrapper .btn.dropdown-toggle,
.broker-dropdown-wrapper .btn.dropdown-toggle {
    width: 100%;
    min-height: 34px;
    height: 34px;
    padding: 6px 8px;
    border-radius: 6px;
    border-color: #cbd5e1;
    display: flex;
    align-items: center;
    justify-content: space-between;
    font-weight: 500;
    background: #fff;
    font-size: 12px;
}

#partyBalanceDisplay {
    margin-top: 2px !important;
    font-size: 11px;
    line-height: 1.2;
}

.party-meta-field {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.party-meta-field label {
    color: #334155;
    font-size: 11px;
    font-weight: 600;
    line-height: 1;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.party-meta-field .meta-control {
    width: 100%;
    min-height: 34px;
    height: 34px;
    padding: 6px 8px;
    border: 1px solid #d7e0ea;
    border-radius: 6px;
    background: #fbfdff;
    color: #111827;
    resize: none;
    font-size: 12px;
}

.party-meta-field textarea.meta-control {
    min-height: 44px;
    height: 44px;
}

.party-meta-grid {
    display: contents;
}

.party-meta-field.address-field {
    order: 4;
}

.item-inline-input {
    width: 100%;
    min-width: 88px;
    height: 34px;
    padding: 6px 8px;
    border: 1px solid #d7e0ea;
    border-radius: 6px;
    background: #fff;
    font-size: 12px;
}

.bottom-right .broker-calc-row {
    align-items: flex-start;
}

.bottom-right .market-calc-row {
    align-items: flex-start;
}

.bottom-right .broker-calc-inputs {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
    justify-content: flex-end;
    width: 100%;
}

.bottom-right .market-calc-inputs {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 8px;
    width: 100%;
}

.bottom-right .market-calc-inputs.single-column {
    grid-template-columns: 1fr;
}

.bottom-right .broker-dropdown-wrapper {
    width: 100%;
    max-width: 180px;
}

.bottom-right .market-mini-input {
    min-height: 34px;
    height: 34px;
    padding: 6px 8px;
    border-radius: 6px;
    border: 1px solid #d7e0ea;
    background: #fff;
    font-size: 12px;
    width: 100%;
}

.bottom-right .broker-dropdown-wrapper .btn.dropdown-toggle,
.bottom-right .broker-phone-input,
.bottom-right .brokerage-type,
.bottom-right .brokerage-rate,
.bottom-right .brokerage-amount {
    min-height: 34px;
    height: 34px;
    padding: 6px 8px;
    border-radius: 6px;
    border: 1px solid #d7e0ea;
    background: #fff;
    font-size: 12px;
    width: 100%;
}

.bottom-right .broker-phone-input,
.bottom-right .brokerage-type,
.bottom-right .brokerage-rate,
.bottom-right .brokerage-amount {
    max-width: 98px;
}

.header-right.w-25 {
    width: 420px !important;
    min-width: 420px;
    justify-content: flex-end;
    background: #ffffff;
    border: 1px solid #dbe4f0;
    border-radius: 16px;
    padding: 18px 20px;
    box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 12px 16px;
    align-content: start;
}

.header-right.w-25 > .d-flex {
    display: none !important;
}

.header-right.w-25 .input-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 5px;
    margin: 0;
    min-width: 0;
}

.header-right.w-25 .input-group span {
    color: #1e293b;
    font-size: 11px;
    font-weight: 700;
    line-height: 1.2;
}

.header-right.w-25 .input-control {
    width: 100%;
    min-width: 0;
    height: 34px;
    padding: 6px 8px;
    border: 1px solid #d7e0ea;
    border-radius: 6px;
    background: #fbfdff;
    font-size: 12px;
}

.header-right.w-25 .invoice-number-group {
    grid-column: 1 / -1;
}

.header-right.w-25 .invoice-date-group {
    grid-column: 1;
}

.header-right.w-25 .order-date-group {
    grid-column: 2;
}

.header-right.w-25 .deal-days-group {
    grid-column: 1;
}

.header-right.w-25 .final-due-date-group {
    grid-column: 2;
}

.broker-option span:first-child {
    width: 70%;
}

.broker-option span:last-child {
    width: 30%;
    text-align: right;
    color: #64748b;
}

@media (max-width: 991px) {
    .header-section {
        grid-template-columns: 1fr;
    }

    .header-left {
        grid-template-columns: 1fr;
    }

    .header-right.w-25 {
        width: 100% !important;
        min-width: 0;
        grid-template-columns: 1fr;
    }

    .bottom-right .broker-dropdown-wrapper,
    .bottom-right .broker-phone-input,
    .bottom-right .brokerage-type,
    .bottom-right .brokerage-rate,
    .bottom-right .brokerage-amount {
        max-width: 100%;
    }

    .bottom-right .market-calc-inputs {
        grid-template-columns: 1fr;
    }

    .header-right.w-25 .invoice-date-group,
    .header-right.w-25 .order-date-group,
    .header-right.w-25 .deal-days-group,
    .header-right.w-25 .final-due-date-group {
        grid-column: auto;
    }
}
    </style>

@php
    $saleItemsSource = collect($items ?? []);

    if ($saleItemsSource->isEmpty()) {
        $saleItemsSource = \App\Models\Item::with('category')
            ->where(function ($query) {
                $query->where('type', 'product')
                    ->orWhereNull('type');
            })
            ->where(function ($query) {
                $query->where('is_active', true)
                    ->orWhereNull('is_active');
            })
            ->orderBy('name')
            ->get();
    }
@endphp

<body>

    <div class="container-fluid min-vh-100 d-flex flex-column p-0">
        <!-- Explorer / Tab Bar Area -->
        <header class="tab-system-header">
            <div class="tab-strip-wrapper justify-content-between">
                <div class="d-flex align-items-end flex-grow-1 overflow-hidden">
                    <div id="tab-strip" class="tab-strip d-flex align-items-end">
                        <!-- Tabs will be dynamically inserted here -->
                    </div>
                    <button id="add-tab-btn" class="btn add-tab-btn" title="New Tab">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>

                <div class="window-controls d-flex align-items-center px-2 gap-3">
                    <i id="calc-icon" class="fa-solid fa-calculator" title="Calculator"></i>
                    <i class="fa-solid fa-gear" title="Settings" data-bs-toggle="offcanvas" data-bs-target="#saleSettingsSidebar" aria-controls="saleSettingsSidebar"></i>
                    <i class="fa-solid fa-xmark close-app-icon" title="Close Window"></i>
                </div>
            </div>
            <!-- Browser Toolbar / Heading Area -->
            <div class="browser-toolbar d-flex align-items-center px-3">
                <p class="mt-3 ms-3 mb-0 me-3 mb-2">Sale | </p>
                <span class="h6 mt-3 me-2">Credit</span>
                <div class="form-check form-switch mt-4 mb-2">

                    <input class="form-check-input mb-2" type="checkbox" role="switch" id="saleToggleSwitch">
                </div>
                <span class="h6 mt-3 ms-2">Cash</span>
            </div>
        </header>

        <!-- Content Area -->
        <main id="content-area" class="">
            <!-- Tab contents will be dynamically inserted here
            <button id="global-save-btn" class="btn btn-primary position-absolute bottom-0 end-0 m-4 shadow-lg z-3">
                <i class="bi bi-save me-2"></i>Save
            </button> -->
            <!-- Form Template -->
            <template id="form-template">
                <div class="invoice-container">
                    <div class="invoice-form invoice-card">

                        <!-- Header Section -->
                        <div class="header-section">
                            <div class="header-left">
                                <div class="input-group party-selector-group">
                                <div class="party-selector-panel">
                                <!-- Party dropdown button -->
<div class="dropdown party-dropdown-wrapper" data-bs-auto-close="outside" style="position: relative; display: inline-block;">
    <button class="btn btn-outline-secondary dropdown-toggle w-200 text-start" type="button" id="partyDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
        Select Party
    </button>
    <!-- Balance display -->
    <div id="partyBalanceDisplay" style="color: #007bff; font-weight: 600; margin-top: 4px;">
        <!-- JS will populate balance here -->
    </div>

    <!-- Dropdown menu (existing) -->
    <ul class="dropdown-menu w-100" aria-labelledby="partyDropdownBtn" id="partyDropdownMenu">
        <li class="dropdown-header-search px-2 py-2">
            <input type="text" class="form-control form-control-sm party-search-input" placeholder="Search party..." style="font-size: 13px;">
        </li>
        <li class="dropdown-header d-flex justify-content-between px-3">
            <span>Party Name</span>
            <span>Opening Balance</span>
        </li>
          @foreach($parties as $party)
          <li>
            <a class="dropdown-item d-flex justify-content-between party-option" href="#"
               data-id="{{ $party->id }}"
               data-name="{{ $party->name }}"
               data-phone="{{ $party->phone }}"
               data-phone-number-2="{{ $party->phone_number_2 }}"
               data-city="{{ $party->city }}"
               data-ptcl="{{ $party->ptcl_number }}"
               data-email="{{ $party->email }}"
               data-address="{{ addslashes($party->address ?? '') }}"
               data-billing="{{ addslashes($party->billing_address ?? '') }}"
               data-shipping="{{ addslashes($party->shipping_address ?? '') }}"
               data-party-group="{{ $party->party_group }}"
               data-due-days="{{ $party->due_days ?? '' }}"
               data-opening="{{ $party->opening_balance ?? 0 }}"
               data-type="{{ $party->transaction_type }}"
               data-party-type="{{ is_array($party->party_type) ? implode(',', $party->party_type) : ($party->party_type ?? '') }}"
               data-credit-limit-enabled="{{ $party->credit_limit_enabled ?? 0 }}"
               data-credit-limit-amount="{{ $party->credit_limit_amount ?? '' }}"
               data-custom-fields="{{ e(json_encode($party->custom_fields ?? [])) }}">
                <span>{{ $party->name }}</span>
                <span
                  @if($party->transaction_type == 'pay')
                      class="text-danger"
                  @elseif($party->transaction_type == 'receive')
                      class="text-success"
                  @endif
                >
                  @if($party->transaction_type == 'pay')
                      <i class="fa-solid fa-arrow-up me-1"></i>
                  @elseif($party->transaction_type == 'receive')
                      <i class="fa-solid fa-arrow-down me-1"></i>
                  @endif
                  ₹{{ number_format($party->opening_balance ?? 0, 2) }}
                </span>
            </a>
          </li>
          @endforeach
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-primary" href="#" id="addNewPartyBtn">+ Add New Party</a></li>
    </ul>
</div>
<input type="hidden" class="party-id" name="party_id">
                                </div>
                                </div>
                                <div class="party-meta-grid">
                                    <div class="party-meta-field">
                                        <label>Phone No.</label>
                                        <input type="text" class="meta-control phone-input" readonly>
                                    </div>
                                    <div class="party-meta-field">
                                        <label>City</label>
                                        <input type="text" class="meta-control city-input" readonly>
                                    </div>
                                    <div class="party-meta-field">
                                        <label>PTCL No.</label>
                                        <input type="text" class="meta-control ptcl-input" readonly>
                                    </div>
                                    <div class="party-meta-field address-field">
                                        <label>Address</label>
                                        <textarea class="meta-control address-input" rows="2" readonly></textarea>
                                    </div>
                                    <div class="party-meta-field address-field">
                                        <label>Billing Address</label>
                                        <textarea class="meta-control billing-address" rows="2" readonly></textarea>
                                    </div>
                                    <div class="party-meta-field address-field">
                                        <label>Shipping Address</label>
                                        <textarea class="meta-control shipping-address" rows="2" readonly></textarea>
                                    </div>

                                </div>
                            </div>

                            <div class="header-right w-25">
                                <div class="d-flex justify-content-end mb-2">

                                </div>
                                <div class="input-group invoice-number-group">
                                    <span>Invoice No.</span>
                                    <input type="text" class="input-control underline-input bill-number" value="{{ $nextInvoiceNumber ?? 'Auto' }}" readonly>
                                </div>
                                <div class="input-group date-wrapper invoice-date-group">
                                    <span>Invoice Date</span>
                                    <input type="date" class="input-control underline-input invoice-date">
                                </div>

                                <div class="input-group date-wrapper deal-days-group">
                                    <span>Deal Days</span>
                                    <select class="input-control underline-input due-days-select">
                                        <option value="0">0 Days</option>
                                        <option value="5">5 Days</option>
                                        <option value="10">10 Days</option>
                                        <option value="15">15 Days</option>
                                        <option value="30">30 Days</option>
                                        <option value="45">45 Days</option>
                                        <option value="custom">Custom</option>
                                    </select>
                                    <input type="number" class="input-control underline-input due-days-custom d-none" placeholder="Custom deal days" min="0">
                                </div>
                                <div class="input-group date-wrapper final-due-date-group">
                                    <span>Due Date</span>
                                    <input type="date" class="input-control underline-input due-date" readonly>
                                </div>

                            </div>
                        </div>

                        <div class="alert alert-success d-none sale-success-msg"></div>

                        <!-- Table Section -->
                        <div class="table-container">
                            <table class="item-table">
                                <thead>
                                    <tr>
                                        <th class="row-num">#</th>
                                        <th style="width: 30%;">ITEM</th>
                                        <th class="col-category d-none">CATEGORY</th>
                                        <th class="col-item-code d-none">ITEM CODE</th>
                                        <th class="col-description d-none">DESCRIPTION</th>
                                        <th class="col-discount d-none">DISCOUNT</th>
                                        <th>QTY</th>
                                        <th class="custom-size-th">UNIT</th>
                                        <th>PRICE/UNIT</th>
                                        <th>AMOUNT</th>
                                        <th>PARCHI</th>
                                        <th>TOTAL WAZAN</th>
                                        <th>SAFI WAZAN</th>
                                        <th>RATE</th>
                                        <th>DEO</th>
                                        <th>BARDANA</th>
                                        <th>MAZDORI</th>
                                        <th>REHRA MAZDORI</th>
                                        <th>DAK KARAYA</th>
                                        <th>LOCAL</th>
                                        <th class="add-col" style="position: relative;">
                                            <button type="button" class="btn-add-circle table-settings-btn" data-bs-toggle="modal" data-bs-target="#itemColumnModal">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="item-rows">
                                    <!-- Row 1 -->
                                    <tr class="item-row">
                                        <td class="row-num">
                                            <span class="row-index-text">1</span>
                                            <div class="delete-row-icon"><i class="fa-solid fa-trash-can"></i></div>
                                        </td>
                                        <td>
                                            <div class="item-picker">
                                                <input type="text" class="item-picker-input" placeholder="Search Item" style="position: relative; z-index: 10;">
                                                <div class="item-picker-panel" style="position: absolute; top: calc(100% + 4px); left: 0; right: 0; width: 100%; min-width: 320px; background: white; border: 1px solid #e1e8ed; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1000; display: none; overflow: hidden;">
                                                    <div class="item-picker-add" style="display: flex; align-items: center; gap: 8px; padding: 12px 18px; color: #2563eb; font-weight: 600; cursor: pointer; border-bottom: 1px solid #e1e8ed;"><i class="fa-regular fa-square-plus"></i> Add Item</div>
                                                    <div class="item-picker-head" style="display: grid; grid-template-columns: minmax(0, 2fr) 100px 110px 80px; gap: 12px; padding: 10px 18px; font-size: 12px; font-weight: 700; color: #97a3b6; text-transform: uppercase; background: #f8fbff; border-bottom: 1px solid #e1e8ed;">
                                                        <span>Item</span>
                                                        <span>Sale Price</span>
                                                        <span>Purchase Price</span>
                                                        <span>Stock</span>
                                                    </div>
                                                    <div class="item-picker-list" style="max-height: 280px; overflow-y: auto;">
                                                        @forelse($saleItemsSource as $item)
                                                            <div class="item-picker-row item-picker-option" data-id="{{ $item->id }}">
                                                                <div class="item-picker-name">
                                                                    {{ $item->name }}
                                                                    @if(!empty($item->item_code))
                                                                        <small>({{ $item->item_code }})</small>
                                                                    @endif
                                                                </div>
                                                                <div>{{ number_format((float) ($item->sale_price ?? $item->price ?? 0), 2, '.', '') }}</div>
                                                                <div>{{ number_format((float) ($item->purchase_price ?? 0), 2, '.', '') }}</div>
                                                                <div class="item-picker-stock {{ (float) ($item->opening_qty ?? 0) < 0 ? 'neg' : '' }}">{{ (float) ($item->opening_qty ?? 0) }}</div>
                                                            </div>
                                                        @empty
                                                            <div class="item-picker-empty">No items found</div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                                <select class="form-select item-name d-none">
                                                    <option value="" selected disabled>Select Item</option>
                                                    @foreach($saleItemsSource as $item)
                                                        <option value="{{ $item->id }}"
                                                            data-price="{{ $item->price }}"
                                                            data-sale-price="{{ $item->sale_price }}"
                                                            data-purchase-price="{{ $item->purchase_price }}"
                                                            data-stock="{{ $item->opening_qty }}"
                                                            data-location="{{ $item->location }}"
                                                            data-label="{{ $item->name }}"
                                                            data-rich-label="{{ $item->name }} | Sale: {{ $item->sale_price ?? $item->price ?? 0 }} | Stock: {{ $item->opening_qty ?? 0 }} | Location: {{ $item->location ?? '' }}"
                                                            data-unit="{{ $item->unit }}"
                                                            data-category="{{ $item->category->name ?? $item->category_name ?? $item->category_id ?? '' }}"
                                                            data-item-code="{{ $item->item_code ?? '' }}"
                                                            data-description="{{ $item->description ?? $item->item_description ?? '' }}"
                                                            data-discount="{{ $item->discount ?? 0 }}"
                                                        >
                                                            {{ $item->name }} | Sale: {{ $item->sale_price ?? $item->price ?? 0 }} | Stock: {{ $item->opening_qty ?? 0 }} | Location: {{ $item->location ?? '' }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </td>
                                        <td class="col-category d-none"><input type="text" class="item-category"
                                                placeholder="Category"></td>
                                        <td class="col-item-code d-none"><input type="text" class="item-code"
                                                placeholder="Item Code"></td>
                                        <td class="col-description d-none"><input type="text" class="item-desc"
                                                placeholder="Description"></td>
                                        <td class="col-discount d-none"><input type="number" class="item-discount"
                                                value="0">
                                        </td>
                                        <td><input type="number" class="item-qty" value="1"></td>
                                      <td class="custom-size-td">
    <select class="item-unit">
        <option value="">Select Unit</option>

        <!-- Quantity -->
        <option value="PCS">PCS (Pieces)</option>
        <option value="BOX">BOX</option>
        <option value="PACK">PACK</option>
        <option value="SET">SET</option>

        <!-- Weight -->
        <option value="KG">KG (Kilogram)</option>
        <option value="G">Gram</option>

        <!-- Length -->
        <option value="M">Meter</option>
        <option value="FT">Feet</option>

        <!-- Volume -->
        <option value="L">Liter</option>
        <option value="ML">Milliliter</option>
    </select>
                                        </td>
                                        <td><input type="number" class="item-price" value="0"></td>
                                        <td class="col-amount"><input type="text" class="item-amount" value="0"
                                                readonly></td>
                                        <td><input type="number" class="item-inline-input tadad-input" value="0" min="0" step="1" placeholder="Tadad"></td>
                                        <td><input type="number" class="item-inline-input total-wazan-input" value="0" min="0" step="0.01" placeholder="Total Wazan"></td>
                                        <td><input type="number" class="item-inline-input safi-wazan-input" value="0" min="0" step="0.01" placeholder="Safi Wazan"></td>
                                        <td><input type="number" class="item-inline-input rate-input" value="0" min="0" step="0.01" placeholder="Rate"></td>
                                        <td><input type="number" class="item-inline-input deo-input" value="0" min="0" step="0.01" placeholder="Deo"></td>
                                        <td><input type="number" class="item-inline-input bardana-input" value="0" min="0" step="0.01" placeholder="Bardana"></td>
                                        <td><input type="number" class="item-inline-input labour-input" value="0" min="0" step="0.01" placeholder="Mazdori"></td>
                                        <td><input type="number" class="item-inline-input rehra-mazdori-input" value="0" min="0" step="0.01" placeholder="Rehra Mazdori"></td>
                                        <td><input type="number" class="item-inline-input post-expense-input" value="0" min="0" step="0.01" placeholder="Dak Karaya"></td>
                                        <td><input type="number" class="item-inline-input extra-expense-input" value="0" min="0" step="0.01" placeholder="Local"></td>
                                        <td class="add-col"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="table-footer">
                                <button type="button" class="btn-add-row add-row-btn">ADD ROW</button>
                                <div class="footer-totals">
                                    <div>
                                        <span class="total-label">TOTAL QTY</span>
                                        <span class="total-qty">0</span>
                                    </div>
                                    <div>
                                        <span class="total-label">TOTAL AMOUNT</span>
                                        <span class="total-base-amount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Split Section -->
                        <div class="bottom-section">
                            <!-- Left Column -->
                            <div class="bottom-left">
                                <div class="payment-section">
                                    <div class="payment-entry d-flex align-items-center gap-2 mb-2">
                                        <select class="input-control default-payment-direction d-none" style="max-width: 140px;">
                                            <option value="payment_in" selected>Payment In</option>
                                            <option value="payment_out">Payment Out</option>
                                        </select>
                                         <select class="input-control default-payment-type">
                                              <option value="" selected disabled>Select Payment Type</option>
                                              <option value="cash">Cash</option>
                                              @foreach($bankAccounts as $bank)
                                                  <option value="bank-{{ $bank->id }}">{{ $bank->display_with_account }}</option>
                                              @endforeach
                                          </select>
                                        <input type="number" class="input-control default-payment-amount d-none" placeholder="Amount" min="0" step="0.01">
                                        <input type="text" class="input-control default-payment-reference d-none" placeholder="Reference">
                                    </div>

                                    <div class="payment-entries">
                                        <!-- Payment rows will be added here when "Add Payment type" is clicked -->
                                    </div>

                                    <div class="payment-total d-flex justify-content-between align-items-center mt-2">
                                        <span class="text-muted">Total payment:</span>
                                        <span class="fw-bold payment-total-amount">0</span>
                                    </div>

                                    <a href="#" class="link-text add-payment-entry">+ Add Payment type</a>
                                </div>

                                <template id="payment-entry-template">
                                    <div class="payment-entry d-flex align-items-center gap-2 mb-2">
                                        <select class="input-control payment-direction-entry d-none" style="max-width: 140px;">
                                            <option value="payment_in" selected>Payment In</option>
                                            <option value="payment_out">Payment Out</option>
                                        </select>
                                         <select class="input-control payment-type-entry">
                                              <option value="" selected disabled>Select Bank Account</option>
                                              <option value="cash">Cash</option>
                                              @foreach($bankAccounts as $bank)
                                                  <option value="bank-{{ $bank->id }}">{{ $bank->display_with_account }}</option>
                                              @endforeach
                                          </select>
                                        <input type="number" class="input-control payment-amount" placeholder="Amount" min="0" step="0.01">
                                        <input type="text" class="input-control payment-reference" placeholder="Reference">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-payment-entry" title="Remove">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </template>

                                <div class="description-action-group mb-2">
                                    <button type="button" class="btn-action-light action-btn add-description">
                                        <i class="fa-solid fa-align-left"></i>
                                        ADD DESCRIPTION
                                    </button>
                                    <div class="description-pane d-none w-50" style="margin-top: -2px;">
                                        <textarea class="form-control description-input" rows="3" placeholder="Enter a remark or description"></textarea>
                                    </div>
                                </div>

                                <div class="action-buttons d-flex flex-wrap gap-2 mb-2">
                                    <button type="button" class="btn-action-light action-btn add-image">
                                        <i class="fa-solid fa-camera"></i>
                                        ADD IMAGE
                                    </button>

                                    <button type="button" class="btn-action-light action-btn add-document">
                                        <i class="fa-solid fa-align-left"></i>
                                        ADD DOCUMENT
                                    </button>

                                </div>

                                <div class="image-upload-section mt-2">
                                    <div class="image-placeholder text-center p-3 border border-dashed rounded" style="cursor:pointer;">
                                        <div class="text-muted">Click to select image(s)</div>
                                        <div class="small text-muted">(PNG/JPG, up to 5MB each)</div>
                                    </div>
                                    <div class="image-files-list d-flex flex-wrap gap-2 mt-2"></div>
                                    <div class="document-files-list list-group mt-2"></div>
                                </div>

                                <input type="file" class="d-none image-input" accept="image/*" multiple />
                                <input type="file" class="d-none document-input" accept=".pdf,.doc,.docx" multiple />
                            </div>

                            <!-- Right Column -->
                            <div class="bottom-right">
                                <div class="calc-row broker-calc-row">
                                    <div class="calc-label">Broker</div>
                                    <div class="calc-inputs broker-calc-inputs">
                                        <div class="broker-dropdown-wrapper" style="position: relative; display: inline-block;">
                                            <button class="btn btn-outline-secondary dropdown-toggle text-start" type="button" id="brokerDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                                                Select Broker
                                            </button>
                                            <ul class="dropdown-menu w-100" aria-labelledby="brokerDropdownBtn" id="brokerDropdownMenu">
                                                <li class="dropdown-header d-flex justify-content-between px-3">
                                                    <span>Broker Name</span>
                                                    <span>City</span>
                                                </li>
                                                @foreach($brokers as $broker)
                                                <li>
                                                    <a class="dropdown-item d-flex justify-content-between broker-option" href="#"
                                                       data-id="{{ $broker->id }}"
                                                       data-phone="{{ $broker->phone }}"
                                                       data-name="{{ $broker->name }}">
                                                        <span>{{ $broker->name }}</span>
                                                        <span>{{ $broker->city ?: '-' }}</span>
                                                    </a>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <input type="hidden" class="broker-id" name="broker_id">
                                        <input type="text" class="broker-phone-input" readonly placeholder="Phone">
                                    </div>
                                </div>

                                <div class="calc-row broker-calc-row">
                                    <div class="calc-label">Brokerage</div>
                                    <div class="calc-inputs broker-calc-inputs">
                                        <select class="brokerage-type">
                                            <option value="">Condition</option>
                                            <option value="full">Poori Brokerage</option>
                                            <option value="half">Aadhi Brokerage</option>
                                            <option value="per_kg">Per Kilo</option>
                                        </select>
                                        <input type="number" class="brokerage-rate" min="0" step="0.01" placeholder="Value">
                                        <input type="hidden" class="brokerage-base-amount" value="0">
                                        <input type="number" class="brokerage-amount" min="0" step="0.01" value="0" readonly>
                                    </div>
                                </div>

                                <!-- Discount -->
                                <div class="calc-row">
                                    <div class="calc-label">Discount</div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input discount-pct" placeholder="%">
                                        <span>-</span>
                                        <input type="number" class="mini-input discount-rs" placeholder="Rs">
                                    </div>
                                </div>

                                <!-- Tax -->
                                <div class="calc-row">
                                    <div class="calc-label">Tax</div>
                                    <div class="calc-inputs">
                                        <select class="mini-input tax-select" style="width: 100px;">
                                            <option value="0">NONE</option>
                                            <option value="5">GST@5%</option>
                                            <option value="12">GST@12%</option>
                                            <option value="18">GST@18%</option>
                                        </select>
                                        <span class="tax-amount-display">0</span>
                                    </div>
                                </div>

                                <!-- Round Off -->
                                <div class="calc-row">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="custom-checkbox round-off-check" checked>
                                        <label class="link-text">Round Off</label>
                                    </div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input round-off-val" value="0" readonly>
                                    </div>
                                </div>

                                <!-- Final Total -->
                                <div class="final-total-group">
                                    <div class="calc-row" style="margin-bottom: 5px;">
                                        <div class="calc-label" style="font-weight: 700;">Total</div>
                                    </div>
                                    <input type="text" class="total-input-large grand-total" value="0" readonly>
                                </div>

                                <div class="calc-row">
                                    <div class="calc-label">Paid Amount</div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input received-amount" value="0" readonly>
                                    </div>
                                </div>

                                <div class="calc-row">
                                    <div class="calc-label">Remaining Amount</div>
                                    <div class="calc-inputs">
                                        <span class="fw-bold balance-amount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Fixed Action Bar -->
                    <div class="sticky-actions">
                        <div class="btn-share">
                            <button class="btn-share-main" type="button">Save &amp; Share</button>
                            <button class="btn-share-arrow"><i class="fa-solid fa-chevron-down"></i></button>
                        </div>
                        <button class="btn-save" type="button">Save</button>
                    </div>
                </div>
            </template>
        </main>
    </div>

    <!-- Item Column Settings Modal -->
    <div class="modal fade" id="itemColumnModal" tabindex="-1" aria-labelledby="itemColumnModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="itemColumnModalLabel">Add fields to items</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-check mb-2">
                        <input class="form-check-input check-category" type="checkbox" id="colCategoryCheck">
                        <label class="form-check-label" for="colCategoryCheck">Item Category</label>
                    </div>
                    <div class="mb-3">
                        <select class="form-select form-select-sm item-filter-category" disabled>
                            <option value="">Select Category</option>
                        </select>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input check-item-code" type="checkbox" id="colItemCodeCheck">
                        <label class="form-check-label" for="colItemCodeCheck">Item Code</label>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control form-control-sm item-filter-code" placeholder="Filter by code" disabled>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input check-description" type="checkbox" id="colDescriptionCheck">
                        <label class="form-check-label" for="colDescriptionCheck">Description</label>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control form-control-sm item-filter-description" placeholder="Filter by description" disabled>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input check-discount" type="checkbox" id="colDiscountCheck">
                        <label class="form-check-label" for="colDiscountCheck">Discount</label>
                    </div>
                    <div class="mb-2">
                        <select class="form-select form-select-sm item-filter-discount" disabled>
                            <option value="">Any Discount</option>
                            <option value="has">Has Discount</option>
                            <option value="none">No Discount</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary w-100 item-filter-apply" data-bs-dismiss="modal">Apply</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Sale Settings Sidebar -->
    <div class="offcanvas offcanvas-end" tabindex="-1" id="saleSettingsSidebar" aria-labelledby="saleSettingsSidebarLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="saleSettingsSidebarLabel">Sale Settings</h5>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div>
                    <div class="fw-semibold">Sale Prefix</div>
                    <div class="text-muted small">INV- <a href="#" class="text-decoration-none">Edit</a></div>
                </div>
                <input class="form-check-input" type="checkbox" checked>
            </div>
            <div class="list-group mb-3">
                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-bs-toggle="modal" data-bs-target="#itemColumnModal">
                    <div>
                        <div class="fw-semibold">Add fields to invoice</div>
                        <div class="text-muted small">Select columns to show</div>
                    </div>
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
                <label class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">Quick Entry</div>
                        <div class="text-muted small">Speed up data entry</div>
                    </div>
                    <input class="form-check-input" type="checkbox">
                </label>
                <label class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">Link payment to invoices</div>
                        <div class="text-muted small">Keep payment history linked</div>
                    </div>
                    <input class="form-check-input" type="checkbox" checked>
                </label>
                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-semibold">Due dates &amp; payment terms</div>
                        <div class="text-muted small">Set payment terms</div>
                    </div>
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
                <button type="button" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center" data-bs-toggle="modal" data-bs-target="#additionalChargesModal">
                    <div>
                        <div class="fw-semibold">Additional charges</div>
                    </div>
                    <i class="fa-solid fa-chevron-right"></i>
                </button>
                <a href="{{ route('settings.print-layout') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                    <div class="fw-semibold">Print Settings</div>
                    <i class="fa-solid fa-chevron-right"></i>
                </a>
            </div>
            <div class="mb-3">
                <div class="fw-semibold mb-2">Billing Type</div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="billingType" id="billingLite" value="lite">
                    <label class="form-check-label" for="billingLite">Lite Sale</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="billingType" id="billingFull" value="full" checked>
                    <label class="form-check-label" for="billingFull">Full Sale</label>
                </div>
            </div>
            <button class="btn btn-link text-decoration-none p-0"><i class="fa-solid fa-gear me-1"></i> More Settings</button>
        </div>
    </div>

    <!-- Additional Charges Modal -->
    <div class="modal fade" id="additionalChargesModal" tabindex="-1" aria-labelledby="additionalChargesModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="additionalChargesModalLabel">Additional Charges</h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <span class="fw-semibold">Enable Additional Charges</span>
                        <div class="form-check form-switch m-0">
                            <input class="form-check-input" type="checkbox" id="additionalChargesToggle" checked>
                        </div>
                    </div>
                    <div class="additional-charge-block">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <input class="form-check-input additional-charge-check" type="checkbox" checked>
                            <input type="text" class="form-control form-control-sm additional-charge-input" value="Shipping">
                            <select class="form-select form-select-sm additional-charge-tax">
                                <option>NONE</option>
                                <option>GST 5%</option>
                                <option>GST 12%</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-3 ms-4">
                            <input class="form-check-input additional-charge-tax-check" type="checkbox">
                            <label class="form-check-label small">Enable tax for Shipping</label>
                        </div>
                    </div>
                    <div class="additional-charge-block">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <input class="form-check-input additional-charge-check" type="checkbox" checked>
                            <input type="text" class="form-control form-control-sm additional-charge-input" value="Packaging">
                            <select class="form-select form-select-sm additional-charge-tax">
                                <option>NONE</option>
                                <option>GST 5%</option>
                                <option>GST 12%</option>
                            </select>
                        </div>
                        <div class="form-check form-switch mb-3 ms-4">
                            <input class="form-check-input additional-charge-tax-check" type="checkbox">
                            <label class="form-check-label small">Enable tax for Packaging</label>
                        </div>
                    </div>
                    <div class="additional-charge-block">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <input class="form-check-input additional-charge-check" type="checkbox" checked>
                            <input type="text" class="form-control form-control-sm additional-charge-input" value="Adjustment">
                            <select class="form-select form-select-sm additional-charge-tax">
                                <option>NONE</option>
                                <option>GST 5%</option>
                                <option>GST 12%</option>
                            </select>
                        </div>
                        <div class="form-check form-switch ms-4">
                            <input class="form-check-input additional-charge-tax-check" type="checkbox">
                            <label class="form-check-label small">Enable tax for Adjustment</label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger w-100">Save Details</button>
                </div>
            </div>
        </div>
    </div>



    <!-- Tab Limit Modal -->
    <div class="modal fade" id="tabLimitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-dark border-secondary">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-exclamation-triangle text-warning display-4 mb-3"></i>
                    <h5>Maximum Limit Reached</h5>
                    <p>You can open a maximum of 10 transactions at a time.</p>
                    <button type="button" class="btn btn-primary px-4 mt-2" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Close Confirmation Modal -->
    <div class="modal fade" id="closeConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-dark border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Close Tab?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to close this tab? Your purchase will not be saved. Use the Save button on
                        the bottom right of the screen to save.</p>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm-close-btn" class="btn btn-danger">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>
    window.items = @json($saleItemsSource->values());
    window.parties = @json($parties ?? []);
    window.brokers = @json($brokers ?? []);
    window.bankAccounts = @json($bankAccounts ?? []);
    window.itemRoutes = {
        index: "{{ url('dashboard/items') }}",
        store: "{{ url('dashboard/items') }}",
        categoryStore: "{{ url('dashboard/items/category') }}",
        unitsIndex: "{{ url('dashboard/items/units') }}",
        unitsStore: "{{ url('dashboard/items/units') }}"
    };

    window.saleStoreUrl = "{{ route('sale.store') }}";
    window.saleMethod = 'POST';

    // Default values
    window.editSaleData = null;
    window.sourceEstimateId = null;
    window.sourceSaleOrderId = null;
    window.sourceChallanId = null;
    window.sourceProformaId = null;

    // Optional doc type (avoid JS error)
    window.docType = "{{ $docType ?? 'sale' }}";

    @if(isset($sale))
        // Edit mode
        window.saleStoreUrl = "{{ route('sale.update', $sale->id) }}";
        window.saleMethod = 'PUT';
        window.editSaleData = @json($sale->load(['items', 'payments']));

    @elseif(isset($convertedSaleData))
        // Convert from estimate / sale order / challan
        window.editSaleData = @json($convertedSaleData);
        window.sourceEstimateId = @json($convertedSaleData['source_estimate_id'] ?? null);
        window.sourceSaleOrderId = @json($convertedSaleData['source_sale_order_id'] ?? null);
        window.sourceChallanId = @json($convertedSaleData['source_challan_id'] ?? null);
        window.sourceProformaId = @json($convertedSaleData['source_proforma_id'] ?? null);
    @endif
</script>

    <!-- Toast container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="sale-toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Form Logic -->
    <script src="{{ asset('js/saleform_script.js') }}"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/script.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const unitButtons = document.querySelectorAll('.unit-option');
            const unitInput = document.getElementById('newItemUnit');
            const unitBtn = document.getElementById('newItemUnitBtn');
            const assignCodeBtn = document.getElementById('assignItemCodeBtn');
            const itemNameInput = document.getElementById('newItemName');
            const wholesaleToggle = document.getElementById('toggleWholesalePricing');
            const wholesaleSection = document.querySelector('.wholesale-pricing');
            const imagePickerCard = document.querySelector('.open-item-image-picker');
            let currentImageObjectUrl = null;

            if (unitButtons && unitInput && unitBtn) {
                unitButtons.forEach(btn => {
                    btn.addEventListener('click', function () {
                        const unit = this.dataset.unit || '';
                        unitInput.value = unit;
                        unitBtn.textContent = unit || 'Select Unit';
                    });
                });
            }

            if (assignCodeBtn && itemNameInput) {
                assignCodeBtn.addEventListener('click', function () {
                    const name = itemNameInput.value.trim();
                    const slug = name ? name.toUpperCase().replace(/[^A-Z0-9]+/g, '') : 'ITEM';
                    const suffix = Math.floor(Math.random() * 9000) + 1000;
                    document.getElementById('newItemCode').value = `${slug ? slug.substring(0, 6) : 'ITEM'}-${suffix}`;
                });
            }

            const itemTypeToggle = document.getElementById('newItemTypeToggle');
            const itemTypeHidden = document.getElementById('newItemType');
            const productLabel = document.getElementById('newItemProductLabel');
            const itemNameLabel = document.getElementById('newItemNameLabel');
            const stockTabButton = document.getElementById('stock-tab');
            const stockTabPane = document.getElementById('stock-tab-pane');
            const purchaseSection = document.getElementById('purchase-sec');

            if (itemTypeToggle && itemTypeHidden) {
                itemTypeToggle.addEventListener('change', function () {
                    const isService = this.checked;
                    itemTypeHidden.value = isService ? 'service' : 'product';
                    productLabel.textContent = isService ? 'Service' : 'Product';
                    itemNameLabel.textContent = isService ? 'Service Name *' : 'Item Name *';
                    if (stockTabButton && stockTabPane) {
                        stockTabButton.style.display = isService ? 'none' : '';
                        stockTabPane.style.display = isService ? 'none' : '';
                    }
                    if (purchaseSection) {
                        purchaseSection.style.display = isService ? 'none' : '';
                    }
                    const pricingTabEl = document.getElementById('pricing-tab');
                    if (pricingTabEl) {
                        const pricingTab = bootstrap.Tab.getOrCreateInstance(pricingTabEl);
                        pricingTab.show();
                    }
                });
            }

            const newItemImageInput = document.getElementById('newItemImage');
            const newItemImageThumb = document.getElementById('newItemImageThumb');
            const newItemImageLabel = document.getElementById('newItemImageLabel');

            if (imagePickerCard && newItemImageInput) {
                imagePickerCard.addEventListener('click', function (event) {
                    event.preventDefault();
                    event.stopPropagation();
                    newItemImageInput.click();
                });
            }

            if (newItemImageInput) {
                newItemImageInput.addEventListener('click', function (event) {
                    event.stopPropagation();
                });

                newItemImageInput.addEventListener('change', function (event) {
                    const file = event.target.files[0];
                    if (currentImageObjectUrl) {
                        URL.revokeObjectURL(currentImageObjectUrl);
                        currentImageObjectUrl = null;
                    }
                    if (!file) {
                        newItemImageThumb.innerHTML = '<i class="fa-regular fa-image fa-2x text-secondary"></i>';
                        newItemImageThumb.style.border = '1.5px solid #93c5fd';
                        newItemImageLabel.textContent = 'Click to choose image';
                        return;
                    }
                    currentImageObjectUrl = URL.createObjectURL(file);
                    newItemImageThumb.innerHTML = `<img src="${currentImageObjectUrl}" style="width:100%;height:100%;object-fit:cover;"/>`;
                    newItemImageThumb.style.border = '1.5px solid #2563eb';
                    newItemImageLabel.textContent = file.name;
                });
            }

            if (wholesaleToggle && wholesaleSection) {
                wholesaleToggle.addEventListener('click', function () {
                    wholesaleSection.classList.toggle('d-none');
                    this.textContent = wholesaleSection.classList.contains('d-none') ? '+ Add Wholesale Price' : '- Remove Wholesale Price';
                });
            }
        });
    </script>
     <div class="container">
        @yield('content')
    </div>

@section('modals')
<!-- MODAL: ADD PARTY -->
 <div class="modal fade" id="addPartyModal" tabindex="-1" aria-labelledby="addPartyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPartyModalLabel"><i class="fa-solid fa-user-plus me-2"></i>Add Party</h5>
        <div class="d-flex align-items-center gap-2" style="margin-left:79%;">
          <button class="btn btn-sm btn-outline-secondary" type="button" id="partyModalSettingsTrigger" title="Settings"><i class="fa-solid fa-gear"></i></button>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>

      <div class="modal-body">
        <form id="addPartyForm">
          @csrf
          <div class="row g-3 mb-4">
            <div class="col-md-4" data-party-setting="name">
              <label class="form-label fw-600">Party Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" placeholder="Enter party name" id="partyNameInput" required>
            </div>
            <div class="col-md-4" data-party-setting="phone">
              <label class="form-label fw-600">Phone Number</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                <input type="tel" name="phone" class="form-control" placeholder="Enter phone number" id="partyPhoneInput">
              </div>
            </div>
            <div class="col-md-4" data-party-setting="phone_2">
              <label class="form-label fw-600">Phone Number 2</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-phone-volume"></i></span>
                <input type="tel" name="phone_number_2" class="form-control" placeholder="Enter second phone number" id="partyPhone2Input">
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">PTCL Number</label>
              <input type="text" name="ptcl_number" class="form-control" placeholder="Enter PTCL number" id="partyPtclInput">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">City</label>
              <input type="text" name="city" class="form-control" placeholder="Enter city" id="partyCityInput">
            </div>


            <div class="col-md-4">
  <label class="form-label fw-600">Party Group</label>

  <div class="position-relative">
    <button type="button" class="form-control text-start" id="partyGroupTrigger">
      <span id="partyGroupText">Select group</span>
      <i class="fa fa-chevron-down float-end mt-1"></i>
    </button>

    <input type="hidden" name="party_group" id="partyGroupInput">

      <div id="partyGroupMenu" class="border bg-white position-absolute w-100 mt-1 d-none" style="z-index:999;">
      <button type="button" class="dropdown-item text-primary" id="addNewGroupBtn">+ New Group</button>
      <div id="partyGroupList">
        @foreach($partyGroups as $partyGroup)
          <button type="button" class="dropdown-item" data-group="{{ $partyGroup->name }}">{{ $partyGroup->name }}</button>
        @endforeach
      </div>
      </div>
    </div>
  </div>
          </div>

          <!-- Tabs -->
          <ul class="nav nav-tabs" id="partyModalTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="party-address-tab" data-bs-toggle="tab" data-bs-target="#partyAddressPane" type="button" role="tab" aria-controls="partyAddressPane" aria-selected="true">
                <i class="fa-solid fa-location-dot me-1"></i> Address
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="party-credit-tab" data-bs-toggle="tab" data-bs-target="#partyCreditPane" type="button" role="tab" aria-controls="partyCreditPane" aria-selected="false">
                <i class="fa-solid fa-credit-card me-1"></i> Credit & Balance
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="party-additional-tab" data-bs-toggle="tab" data-bs-target="#partyAdditionalPane" type="button" role="tab" aria-controls="partyAdditionalPane" aria-selected="false">
                <i class="fa-solid fa-sliders me-1"></i> Additional Fields
              </button>
            </li>
          </ul>

          <div class="tab-content pt-3" id="partyModalTabContent">
            <!-- Address Tab -->
            <div class="tab-pane fade show active" id="partyAddressPane" role="tabpanel" aria-labelledby="party-address-tab">
              <div class="row g-3">
                <div class="col-md-6" data-party-setting="email">
                  <label class="form-label">Email ID</label>
                  <input type="email" name="email" class="form-control" placeholder="example@email.com">
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-6">
                  <label class="form-label">Address</label>
                  <textarea id="partyAddressInput" class="form-control" name="address" rows="3" placeholder="Enter address"></textarea>
                </div>
                <div class="col-md-6" data-party-setting="billing_address">
                  <label class="form-label">Billing Address</label>
                  <textarea id="billingAddress" class="form-control" name="billing_address" rows="3" placeholder="Enter billing address"></textarea>
                </div>
                <div class="col-md-6" data-party-setting="shipping_address">
                  <label class="form-label">Shipping Address</label>
                  <textarea  id="shippingAddress" class="form-control" name="shipping_address" rows="3" placeholder="Enter shipping address"></textarea>
                </div>
              </div>
            </div>

            <!-- Credit & Balance Tab -->
          <div class="tab-pane fade" id="partyCreditPane" role="tabpanel" aria-labelledby="party-credit-tab">
            <div class="row g-3">
              <div class="col-md-4" data-party-setting="opening_balance">
                <label class="form-label">Opening Balance</label>
                <div class="input-group">
                  <span class="input-group-text">₹</span>
                  <input type="number" name="opening_balance" class="form-control" placeholder="0.00">
                </div>
              </div>
              <div class="col-md-4" data-party-setting="as_of_date">
                <label class="form-label">As Of Date</label>
                <input type="date" name="as_of_date" class="form-control" value="{{ date('Y-m-d') }}">
              </div>
              <div class="col-md-4" data-party-setting="credit_limit">
                <label class="form-label d-block">Credit Limit</label>
                <div class="form-check form-switch mt-2">
                  <input class="form-check-input" name="credit_limit_enabled" type="checkbox" id="creditLimitSwitch">
                  <label class="form-check-label" for="creditLimitSwitch">Enable</label>
                </div>
                <div class="input-group mt-2 is-hidden" id="creditLimitAmountWrap">
                  <span class="input-group-text">Rs</span>
                  <input type="number" name="credit_limit_amount" class="form-control" placeholder="Enter credit limit" id="creditLimitAmountInput" min="0" step="0.01">
                </div>
              </div>
              <div class="col-md-4" data-party-setting="due_days">
                <label class="form-label">Due Days</label>
                <input type="number" name="due_days" class="form-control" placeholder="e.g. 5, 10, 30" min="1" max="100" id="partyDueDaysInput">
              </div>
            </div>

            <!-- To Receive / To Pay Options at the bottom -->
            <div class="mt-4" data-party-setting="transaction_type">
              <label class="form-label d-block">Transaction Type</label>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="toReceive" value="receive">
                <label class="form-check-label" for="toReceive">To Receive</label>
              </div>
              <div class="form-check form-check-inline">
                <input class="form-check-input" type="checkbox" id="toPay" value="pay">
                <label class="form-check-label" for="toPay">To Pay</label>
              </div>
            </div>

            <div class="row g-3 mt-3" data-party-setting="party_type">
              <div class="col-md-6">
                <label class="form-label fw-600">Party Type</label>

                <div class="form-check">
                  <input class="form-check-input party-type-checkbox" type="checkbox" name="party_type[]" id="customerParty" value="customer">
                  <label class="form-check-label" for="customerParty">Customer</label>
                </div>

                <div class="form-check">
                  <input class="form-check-input party-type-checkbox" type="checkbox" name="party_type[]" id="supplierParty" value="supplier">
                  <label class="form-check-label" for="supplierParty">Supplier</label>
                </div>
              </div>
            </div>
          </div>

            <!-- Additional Fields Tab -->
            <div class="tab-pane fade" id="partyAdditionalPane" role="tabpanel" aria-labelledby="party-additional-tab" data-party-setting="additional_fields">
              <p class="text-muted mb-3" style="font-size:13px;">Add custom fields to track additional information.</p>
              <div class="row g-3">
                @for($i=1; $i<=4; $i++)
                <div class="col-md-6">
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="customField{{$i}}Check">
                    <label class="form-check-label" for="customField{{$i}}Check">Custom Field {{$i}}</label>
                  </div>
                  <input type="text" name="custom_fields[]" class="form-control form-control-sm" placeholder="Field name">
                </div>

                <input type="hidden" id="transactionTypeValue" name="transaction_type">
                @endfor

              </div>
            </div>
          </div>


          <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnSaveNewParty">
              <i class="fa-solid fa-plus me-1"></i> Save & New
            </button>
            <button type="button" class="btn btn-primary" id="btnSaveParty">
              <i class="fa-solid fa-check me-1"></i> Save
            </button>
 <button type="button" class="btn btn-primary" id="btnUpdateParty" style="display:none;">Update</button>
    <button type="button" class="btn btn-danger" id="btnDeleteParty" style="display:none;">Delete</button>
          </div>
        </form>

      </div>
    </div>
</div>

</div>

<div class="modal fade" id="partyGroupModal" tabindex="-1">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">New Party Group</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="newGroupName" class="form-control" placeholder="Enter group name">
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
        <button class="btn btn-primary btn-sm" id="saveGroupBtn">Save</button>
      </div>
    </div>
  </div>
</div>

@php
    $salesModalUnits = [];

    if (\Illuminate\Support\Facades\Schema::hasTable('item_units')) {
        $salesModalUnits = \App\Models\ItemUnit::query()
            ->where('is_active', true)
            ->orderBy('short_name')
            ->get(['name', 'short_name']);
    }

@endphp
<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header align-items-start justify-content-between">
        <div>
          <h5 class="modal-title">Add Item</h5>
          <p class="text-muted small mb-0">Create item details, pricing, stock and description.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span id="newItemProductLabel" class="text-primary fw-semibold">Product</span>
          <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="newItemTypeToggle">
            <label class="form-check-label" for="newItemTypeToggle">Service</label>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addItemForm">
          <input type="hidden" id="newItemType" name="item_type" value="product">
          <div class="row g-3">
            <div class="col-md-5">
              <label for="newItemName" class="form-label" id="newItemNameLabel">Item Name *</label>
              <input type="text" class="form-control" id="newItemName" required>
            </div>
            <div class="col-md-4">
              <label for="newItemCategory" class="form-label">Category</label>
              <select class="form-select" id="newItemCategory">
                <option value="">Select Category</option>
                @foreach($categories ?? [] as $category)
                  <option value="{{ $category->id ?? '' }}">{{ $category->name ?? '' }}</option>
                @endforeach
                <option value="__add_new__">+ Add Category</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Unit</label>
              <div class="w-100">
                <button class="btn btn-outline-primary w-100 text-start" type="button" id="newItemUnitBtn">
                  Select Unit
                </button>
                <input type="hidden" id="newItemUnit" name="unit">
                <input type="hidden" id="newItemSecondaryUnit" name="secondary_unit">
                <input type="hidden" id="newItemUnitConversionRate" name="unit_conversion_rate">
              </div>
            </div>
            <div class="col-md-6">
              <label for="newItemCode" class="form-label">Item Code</label>
              <div class="input-group">
                <input type="text" class="form-control" id="newItemCode" placeholder="Enter item code">
                <button type="button" class="btn btn-outline-secondary" id="assignItemCodeBtn">Assign</button>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Item Image</label>
              <div class="border rounded-3 p-3 text-center h-100 d-flex flex-column justify-content-center align-items-center open-item-image-picker" style="cursor:pointer;">
                <div id="newItemImageThumb" style="width:68px; height:68px; border:1.5px solid #93c5fd; border-radius:12px; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%); overflow:hidden;">
                  <i class="fa-regular fa-image fa-2x text-secondary"></i>
                </div>
                <div class="text-secondary mt-2" id="newItemImageLabel">Click to choose image</div>
                <input type="file" class="form-control d-none" id="newItemImage" accept="image/*">
              </div>
            </div>
          </div>

          <ul class="nav nav-tabs mt-4" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing-tab-pane" type="button" role="tab" aria-controls="pricing-tab-pane" aria-selected="true">Pricing</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock-tab-pane" type="button" role="tab" aria-controls="stock-tab-pane" aria-selected="false">Stock</button>
            </li>
          </ul>

          <div class="tab-content pt-3">
            <div class="tab-pane fade show active" id="pricing-tab-pane" role="tabpanel" aria-labelledby="pricing-tab">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="newItemSalePrice" class="form-label">Sale Price</label>
                  <input type="number" class="form-control" id="newItemSalePrice" min="0" step="0.01" placeholder="Sale Price">
                </div>
                <div class="col-md-6">
                  <label for="newItemPurchasePrice" class="form-label">Purchase Price</label>
                  <input type="number" class="form-control" id="newItemPurchasePrice" min="0" step="0.01" placeholder="Purchase Price">
                </div>
                <div class="col-12">
                  <div class="border rounded-3 p-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <div class="fw-semibold">Wholesale Pricing</div>
                      <button type="button" class="btn btn-link btn-sm p-0" id="toggleWholesalePricing">+ Add Wholesale Price</button>
                    </div>
                    <div class="row g-2 wholesale-pricing d-none">
                      <div class="col-md-6">
                        <label for="newItemWholesalePrice" class="form-label">Wholesale Price</label>
                        <input type="number" class="form-control" id="newItemWholesalePrice" min="0" step="0.01" placeholder="Wholesale Price">
                      </div>
                      <div class="col-md-6">
                        <label for="newItemWholesaleMinQty" class="form-label">Minimum Wholesale Qty</label>
                        <input type="number" class="form-control" id="newItemWholesaleMinQty" min="0" step="1" placeholder="Minimum Qty">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="stock-tab-pane" role="tabpanel" aria-labelledby="stock-tab">
              <div class="row g-3">
                <div class="col-md-4">
                  <label for="newItemStock" class="form-label">Opening Quantity</label>
                  <input type="number" class="form-control" id="newItemStock" min="0" step="1" placeholder="Opening Qty">
                </div>
                <div class="col-md-4">
                  <label for="newItemAtPrice" class="form-label">At Price</label>
                  <input type="number" class="form-control" id="newItemAtPrice" min="0" step="0.01" placeholder="At Price">
                </div>
                <div class="col-md-4">
                  <label for="newItemAsOfDate" class="form-label">As Of Date</label>
                  <input type="date" class="form-control" id="newItemAsOfDate" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-6">
                  <label for="newItemMinStock" class="form-label">Min Stock To Maintain</label>
                  <input type="number" class="form-control" id="newItemMinStock" min="0" step="1" placeholder="Min Stock">
                </div>
                <div class="col-md-6">
                  <label for="newItemLocation" class="form-label">Location</label>
                  <input type="text" class="form-control" id="newItemLocation" placeholder="Location">
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold">Item Images</label>
                  <div class="item-stock-images-trigger open-item-stock-images-picker">
                    <span><i class="fa-regular fa-camera me-2"></i>Add Item Images</span>
                  </div>
                  <input type="file" class="d-none" id="newItemStockImages" accept="image/*" multiple>
                  <div id="newItemStockImagesList" class="item-stock-images-list"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row g-3 mt-4">
            <div class="col-12">
              <label for="newItemDescription" class="form-label">Description</label>
              <textarea class="form-control" id="newItemDescription" rows="4" placeholder="Item description"></textarea>
            </div>
          </div>

          <input type="hidden" name="_token" value="{{ csrf_token() }}">
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveNewItemBtn">Save Item</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-stack-top" id="selectItemUnitModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Unit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="newItemBaseUnitSelect" class="form-label text-uppercase small fw-bold">Base Unit</label>
            <select class="form-select" id="newItemBaseUnitSelect">
              <option value="">Select Base Unit</option>
              @foreach($salesModalUnits as $unit)
                @php
                  $unitShortName = strtoupper($unit['short_name'] ?? $unit->short_name ?? '');
                  $unitName = strtoupper($unit['name'] ?? $unit->name ?? '');
                  $unitLabel = $unitName && $unitName !== $unitShortName ? $unitName . ' (' . $unitShortName . ')' : $unitShortName;
                @endphp
                <option value="{{ $unitShortName }}">{{ $unitLabel }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label for="newItemSecondaryUnitSelect" class="form-label text-uppercase small fw-bold">Secondary Unit</label>
            <select class="form-select" id="newItemSecondaryUnitSelect">
              <option value="">Select Secondary Unit</option>
              @foreach($salesModalUnits as $unit)
                @php
                  $unitShortName = strtoupper($unit['short_name'] ?? $unit->short_name ?? '');
                  $unitName = strtoupper($unit['name'] ?? $unit->name ?? '');
                  $unitLabel = $unitName && $unitName !== $unitShortName ? $unitName . ' (' . $unitShortName . ')' : $unitShortName;
                @endphp
                <option value="{{ $unitShortName }}">{{ $unitLabel }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-link text-primary p-0 open-add-unit-from-selector">+ Add Unit</button>
          </div>
          <div class="col-12">
            <label for="newItemUnitConversionInput" class="form-label fw-semibold">Conversion Rate</label>
            <div class="item-unit-conversion-row">
              <span class="base-unit-preview">1 Base Unit</span>
              <span>=</span>
              <input type="number" class="form-control" id="newItemUnitConversionInput" min="0" step="0.0001" value="0">
              <span class="secondary-unit-preview">Secondary Unit</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="saveSelectedUnitsBtn">Save</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-stack-top" id="addCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="quickCategoryName" class="form-label">Category Name</label>
          <input type="text" class="form-control" id="quickCategoryName" placeholder="Enter category name">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveQuickCategoryBtn">Save Category</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-stack-top" id="addUnitModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Unit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-8">
            <label for="quickUnitName" class="form-label">Unit Name</label>
            <input type="text" class="form-control" id="quickUnitName" placeholder="e.g. KILOGRAMS">
          </div>
          <div class="col-md-4">
            <label for="quickUnitShortName" class="form-label">Short Name</label>
            <input type="text" class="form-control" id="quickUnitShortName" placeholder="e.g. KG">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveQuickUnitBtn">Save Unit</button>
      </div>
    </div>
  </div>
</div>
@endsection
    @yield('modals')

    <script>
document.addEventListener("DOMContentLoaded", function () {
    const partySelect = document.querySelector(".party-select");
    const addModalEl = document.getElementById('addPartyModal');
    const addModal = new bootstrap.Modal(addModalEl);

    if (partySelect) {
        partySelect.addEventListener("change", function () {
            if (this.value === "__new") {
                addModal.show();

                // Optional: Reset modal har bar open hone pe
                document.getElementById("addPartyForm").reset();
            }
        });
    }

    if (addModalEl) {
        addModalEl.addEventListener('shown.bs.modal', function () {
            const addressTabEl = document.getElementById('party-address-tab');
            const addressPaneEl = document.getElementById('partyAddressPane');
            const creditTabEl = document.getElementById('party-credit-tab');
            const creditPaneEl = document.getElementById('partyCreditPane');
            const additionalTabEl = document.getElementById('party-additional-tab');
            const additionalPaneEl = document.getElementById('partyAdditionalPane');

            [addressTabEl, creditTabEl, additionalTabEl].forEach(tab => {
                if (tab) {
                    tab.classList.remove('active');
                    tab.setAttribute('aria-selected', 'false');
                }
            });

            [addressPaneEl, creditPaneEl, additionalPaneEl].forEach(pane => {
                if (pane) {
                    pane.classList.remove('show', 'active');
                }
            });

            if (addressTabEl) {
                addressTabEl.classList.add('active');
                addressTabEl.setAttribute('aria-selected', 'true');
            }
            if (addressPaneEl) {
                addressPaneEl.classList.add('show', 'active');
                addressPaneEl.style.display = '';
            }

            if (addressTabEl && bootstrap.Tab) {
                const addressTab = bootstrap.Tab.getOrCreateInstance(addressTabEl);
                addressTab.show();
            }
        });
    }

    // Party search functionality handled by saleform_script.js
});

document.addEventListener("DOMContentLoaded", function () {

    const trigger = document.getElementById("partyGroupTrigger");
    const menu = document.getElementById("partyGroupMenu");
    const list = document.getElementById("partyGroupList");
    const input = document.getElementById("partyGroupInput");
    const text = document.getElementById("partyGroupText");

    const groupModal = new bootstrap.Modal(document.getElementById('partyGroupModal'));
    window.salePartyGroups = window.salePartyGroups || Array.from((list?.querySelectorAll('.dropdown-item') || []))
        .filter((btn) => btn.id !== 'addNewGroupBtn')
        .map((btn) => btn.textContent.trim())
        .filter(Boolean);

    if (!trigger || !menu || !list || !input || !text) {
        return;
    }

    // Toggle dropdown
    trigger.addEventListener("click", () => {
        menu.classList.toggle("d-none");
    });

    // Close outside
    document.addEventListener("click", (e) => {
        if (!trigger.contains(e.target) && !menu.contains(e.target)) {
            menu.classList.add("d-none");
        }
    });

    // Render groups
    function renderGroups() {
        list.innerHTML = "";
        window.salePartyGroups.forEach(g => {
            const btn = document.createElement("button");
            btn.type = "button";
            btn.className = "dropdown-item";
            btn.dataset.group = g;
            btn.textContent = g;

            btn.onclick = () => {
                input.value = g;
                text.textContent = g;
                menu.classList.add("d-none");
            };

            list.appendChild(btn);
        });
    }

    renderGroups();

    list.addEventListener("click", function (event) {
        const btn = event.target.closest("button.dropdown-item");
        if (!btn || btn.id === "addNewGroupBtn") return;

        input.value = btn.dataset.group || btn.textContent.trim();
        text.textContent = btn.dataset.group || btn.textContent.trim();
        menu.classList.add("d-none");
    });

    // Open modal
    const addNewGroupBtn = document.getElementById("addNewGroupBtn");
    if (addNewGroupBtn) {
        addNewGroupBtn.onclick = () => {
            groupModal.show();
        };
    }

    const partyGroupsStoreUrl = '{{ route("party-groups.store") }}';

    // Save group
    const saveGroupBtn = document.getElementById("saveGroupBtn");
    if (saveGroupBtn) {
      saveGroupBtn.onclick = async () => {
        const nameEl = document.getElementById("newGroupName");
        const name = nameEl.value.trim();

        if (!name) return alert("Enter group name");

        try {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            const response = await fetch(partyGroupsStoreUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token || '',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ name }),
            });

            const result = await response.json();
            if (!response.ok || !result.success) {
                throw new Error(result.message || 'Unable to save party group');
            }

            const groupName = result.partyGroup?.name || name;
            if (!window.salePartyGroups.includes(groupName)) {
                window.salePartyGroups.push(groupName);
            }
            renderGroups();

            input.value = groupName;
            text.textContent = groupName;

            nameEl.value = "";
            groupModal.hide();
        } catch (error) {
            console.error(error);
            alert('Could not save party group. Please try again.');
        }
      };
    }

});

document.addEventListener("DOMContentLoaded", function () {
    const addModalEl = document.getElementById('addPartyModal');
    const addModal = new bootstrap.Modal(addModalEl);
    const saveBtn = document.getElementById("btnSaveParty");
    const saveNewBtn = document.getElementById("btnSaveNewParty");

    // Handle modal close - clean up backdrops to prevent black screen
    addModalEl.addEventListener('hidden.bs.modal', function () {
        // Remove any remaining backdrops
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
        // Remove modal-open class from body
        document.body.classList.remove('modal-open');
        // Reset overflow if it was set
        document.body.style.overflow = '';
        document.body.style.paddingRight = '';
    });

    // Handle modal show - ensure clean state
    addModalEl.addEventListener('show.bs.modal', function () {
        // Remove any orphaned backdrops before opening
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
    });




    function getPartyData() {
        const form = document.getElementById("addPartyForm");
        return new FormData(form);
    }

   function applyPartyDueDays(partyRecord = {}) {
    const dealDaysSelect = document.querySelector(".due-days-select");
    const dealDaysCustomInput = document.querySelector(".due-days-custom");
    const dueDateInput = document.querySelector(".due-date");
    const orderDateInput = document.querySelector(".order-date");
    if (!dealDaysSelect || !dueDateInput || !orderDateInput) {
        return;
    }

    const dueDays = Number(partyRecord.due_days || 0);
    const orderDateValue = orderDateInput.value;
    const allowedDays = ['0', '5', '10', '15', '30', '45'];

    if (dueDays > 0) {
        if (allowedDays.includes(String(dueDays))) {
            dealDaysSelect.value = String(dueDays);
            dealDaysCustomInput?.classList.add('d-none');
            if (dealDaysCustomInput) dealDaysCustomInput.value = '';
        } else {
            dealDaysSelect.value = 'custom';
            dealDaysCustomInput?.classList.remove('d-none');
            if (dealDaysCustomInput) dealDaysCustomInput.value = dueDays;
        }
    } else {
        dealDaysSelect.value = '0';
        dealDaysCustomInput?.classList.add('d-none');
        if (dealDaysCustomInput) dealDaysCustomInput.value = '';
    }

    if (!orderDateValue || dueDays <= 0) {
        return;
    }

    const dueDate = new Date(orderDateValue);
    if (Number.isNaN(dueDate.getTime())) {
        return;
    }

    dueDate.setDate(dueDate.getDate() + dueDays);
    const yyyy = dueDate.getFullYear();
    const mm = String(dueDate.getMonth() + 1).padStart(2, '0');
    const dd = String(dueDate.getDate()).padStart(2, '0');
    dueDateInput.value = `${yyyy}-${mm}-${dd}`;
   }

   function saveParty(closeAfterSave = true) {
    const form = document.getElementById("addPartyForm");
    const data = new FormData(form);

    // Transaction type fix
    const toReceive = document.getElementById("toReceive").checked;
    const toPay = document.getElementById("toPay").checked;
    if(toReceive) data.set("transaction_type", "receive");
    else if(toPay) data.set("transaction_type", "pay");

    // Credit limit fix
    const creditSwitch = document.getElementById("creditLimitSwitch");
    data.set("credit_limit_enabled", creditSwitch.checked ? 1 : 0);

    fetch("{{ route('parties.store') }}", {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json"   // important!
        },
        body: data
    })
    .then(res => res.json())
    .then(res => {
        if(res.success) {
            const party = res.party || {};
            const partyRecord = {
                id: party.id || '',
                name: party.name || data.get('name') || '',
                phone: party.phone || data.get('phone') || '',
                phone_number_2: party.phone_number_2 || data.get('phone_number_2') || '',
                ptcl_number: party.ptcl_number || data.get('ptcl_number') || '',
                email: party.email || data.get('email') || '',
                city: party.city || data.get('city') || '',
                address: party.address || data.get('address') || '',
                billing_address: party.billing_address || data.get('billing_address') || '',
                shipping_address: party.shipping_address || data.get('shipping_address') || '',
                party_group: party.party_group || data.get('party_group') || '',
                due_days: party.due_days || data.get('due_days') || '',
                opening_balance: party.opening_balance || data.get('opening_balance') || 0,
                credit_limit_enabled: party.credit_limit_enabled || data.get('credit_limit_enabled') || 0,
                credit_limit_amount: party.credit_limit_amount || data.get('credit_limit_amount') || '',
                custom_fields: party.custom_fields || data.getAll('custom_fields[]') || [],
                party_type: party.party_type || data.getAll('party_type[]') || [],
                transaction_type: party.transaction_type || data.get('transaction_type') || '',
            };

            if (partyRecord.party_group) {
                window.salePartyGroups = window.salePartyGroups || [];
                if (!window.salePartyGroups.includes(partyRecord.party_group)) {
                    window.salePartyGroups.push(partyRecord.party_group);
                }

                const partyGroupList = document.getElementById('partyGroupList');
                if (partyGroupList && !partyGroupList.querySelector(`[data-group="${partyRecord.party_group}"]`)) {
                    const groupBtn = document.createElement('button');
                    groupBtn.type = 'button';
                    groupBtn.className = 'dropdown-item';
                    groupBtn.dataset.group = partyRecord.party_group;
                    groupBtn.textContent = partyRecord.party_group;
                    groupBtn.onclick = () => {
                        const groupInput = document.getElementById('partyGroupInput');
                        const groupText = document.getElementById('partyGroupText');
                        if (groupInput) groupInput.value = partyRecord.party_group;
                        if (groupText) groupText.textContent = partyRecord.party_group;
                    };
                    partyGroupList.appendChild(groupBtn);
                }
            }

            if (partyRecord.id) {
                window.parties = Array.isArray(window.parties) ? window.parties.filter(p => String(p.id) !== String(partyRecord.id)) : [];
                window.parties.push(partyRecord);

                const dropdownMenu = document.getElementById("partyDropdownMenu");
                const partyIdInput = document.querySelector(".party-id");
                const dropdownBtn = document.getElementById("partyDropdownBtn");
                const balanceDisplay = document.getElementById("partyBalanceDisplay");

                if (dropdownMenu) {
                    const existing = dropdownMenu.querySelector(`.party-option[data-id="${partyRecord.id}"]`);
                    if (existing) {
                        existing.closest('li')?.remove();
                    }

                    const optionHtml = `
                      <li>
                        <a class="dropdown-item d-flex justify-content-between party-option"
                           href="#"
                           data-id="${partyRecord.id}"
                           data-name="${partyRecord.name}"
                           data-phone="${partyRecord.phone}"
                           data-phone-number-2="${partyRecord.phone_number_2 || ''}"
                           data-city="${partyRecord.city}"
                           data-ptcl="${partyRecord.ptcl_number}"
                           data-email="${partyRecord.email || ''}"
                           data-address="${partyRecord.address.replace(/"/g, '&quot;')}"
                           data-billing="${partyRecord.billing_address.replace(/"/g, '&quot;')}"
                           data-shipping="${partyRecord.shipping_address.replace(/"/g, '&quot;') }"
                           data-party-group="${partyRecord.party_group || ''}"
                           data-due-days="${partyRecord.due_days}"
                           data-opening="${partyRecord.opening_balance}"
                           data-type="${partyRecord.transaction_type}"
                           data-party-type="${Array.isArray(partyRecord.party_type) ? partyRecord.party_type.join(',') : partyRecord.party_type || ''}"
                           data-credit-limit-enabled="${partyRecord.credit_limit_enabled || 0}"
                           data-credit-limit-amount="${partyRecord.credit_limit_amount || ''}"
                           data-custom-fields="${String(JSON.stringify(partyRecord.custom_fields || [])).replace(/"/g, '&quot;')}">
                            <span>${partyRecord.name}</span>
                            <span class="text-success">0</span>
                        </a>
                      </li>
                    `;

                    const divider = dropdownMenu.querySelector('li > hr.dropdown-divider');
                    if (divider) {
                        divider.closest('li')?.insertAdjacentHTML('beforebegin', optionHtml);
                    } else {
                        dropdownMenu.insertAdjacentHTML('beforeend', optionHtml);
                    }
                }

                if (partyIdInput) partyIdInput.value = partyRecord.id;
                if (dropdownBtn) dropdownBtn.textContent = partyRecord.name || 'Select Party';
                if (balanceDisplay) {
                    balanceDisplay.textContent = partyRecord.transaction_type === 'pay'
                        ? `To Pay Rs ${partyRecord.opening_balance || 0}`
                        : `To Receive Rs ${partyRecord.opening_balance || 0}`;
                    balanceDisplay.className = partyRecord.transaction_type === 'pay' ? 'text-danger small' : 'text-success small';
                }

                document.querySelector(".phone-input").value = partyRecord.phone || "";
                document.querySelector(".city-input").value = partyRecord.city || "";
                document.querySelector(".ptcl-input").value = partyRecord.ptcl_number || "";
                document.querySelector(".address-input").value = partyRecord.address || "";
                document.querySelector(".billing-address").value = partyRecord.billing_address || "";
                document.querySelector(".shipping-address").value = partyRecord.shipping_address || "";
                applyPartyDueDays(partyRecord);
            }

            // Close modal first, then show success message
            if(closeAfterSave) {
                bootstrap.Modal.getOrCreateInstance(addModalEl).hide();
                // Wait for modal to close, then reset
                setTimeout(() => {
                    form.reset();
                    // Clean up any leftover backdrops
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                    document.body.classList.remove('modal-open');
                }, 300);
            } else {
                form.reset();
            }

            // Show success message without blocking UI
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = `
                <div style="background: #28a745; color: white; padding: 12px 20px; border-radius: 4px; margin: 10px; position: fixed; top: 20px; right: 20px; z-index: 9999; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                    <i class="fa-solid fa-check me-2"></i> Party saved successfully!
                </div>
            `;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        } else {
            const errorToast = document.createElement('div');
            errorToast.innerHTML = `
                <div style="background: #dc3545; color: white; padding: 12px 20px; border-radius: 4px; margin: 10px; position: fixed; top: 20px; right: 20px; z-index: 9999; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                    <i class="fa-solid fa-exclamation me-2"></i> Error saving party
                </div>
            `;
            document.body.appendChild(errorToast);
            setTimeout(() => errorToast.remove(), 3000);
        }
    })
    .catch(err => {
        console.error(err);
        const errorToast = document.createElement('div');
        errorToast.innerHTML = `
            <div style="background: #dc3545; color: white; padding: 12px 20px; border-radius: 4px; margin: 10px; position: fixed; top: 20px; right: 20px; z-index: 9999; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                    <i class="fa-solid fa-exclamation me-2"></i> Something went wrong! Check console.
                </div>
            `;
        document.body.appendChild(errorToast);
        setTimeout(() => errorToast.remove(), 3000);
    });
}
    saveBtn.addEventListener('click', function () {
        saveParty(true); // close modal after save
    });

    saveNewBtn.addEventListener('click', function () {
        saveParty(false); // reset modal for new entry
    });
});

</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    const dropdownBtn = document.getElementById("partyDropdownBtn");
    const dropdownMenu = document.getElementById("partyDropdownMenu");
    const partyIdInput = document.querySelector(".party-id");
    const balanceDisplay = document.getElementById("partyBalanceDisplay");
    const brokerDropdownBtn = document.getElementById("brokerDropdownBtn");
    const brokerDropdownMenu = document.getElementById("brokerDropdownMenu");
    const brokerIdInput = document.querySelector(".broker-id");
    const addModalEl = document.getElementById('addPartyModal');

    const addModal = new bootstrap.Modal(addModalEl);

    const setPartyFieldValues = (partyRecord = {}) => {
        document.querySelector(".phone-input").value = partyRecord.phone || "";
        document.querySelector(".city-input").value = partyRecord.city || "";
        document.querySelector(".ptcl-input").value = partyRecord.ptcl_number || partyRecord.ptcl || "";
        document.querySelector(".address-input").value = partyRecord.address || "";
        document.querySelector(".billing-address").value = partyRecord.billing_address || partyRecord.billing || "";
        document.querySelector(".shipping-address").value = partyRecord.shipping_address || partyRecord.shipping || "";
    };

    const setDueDateFromParty = (partyRecord = {}) => {
        const dealDaysSelect = document.querySelector(".due-days-select");
        const dealDaysCustomInput = document.querySelector(".due-days-custom");
        const dueDateInput = document.querySelector(".due-date");
        const orderDateInput = document.querySelector(".order-date");
        if (!dueDateInput || !orderDateInput || !dealDaysSelect) return;

        const dueDays = Number(partyRecord.due_days || partyRecord.dueDays || 0);
        const orderDateValue = orderDateInput.value;

        if (dueDays > 0) {
            const allowedDays = ['0', '5', '10', '15', '30', '45'];
            if (allowedDays.includes(String(dueDays))) {
                dealDaysSelect.value = String(dueDays);
                dealDaysCustomInput?.classList.add('d-none');
                if (dealDaysCustomInput) dealDaysCustomInput.value = '';
            } else {
                dealDaysSelect.value = 'custom';
                dealDaysCustomInput?.classList.remove('d-none');
                if (dealDaysCustomInput) dealDaysCustomInput.value = dueDays;
            }
        } else {
            dealDaysSelect.value = '0';
            dealDaysCustomInput?.classList.add('d-none');
            if (dealDaysCustomInput) dealDaysCustomInput.value = '';
        }

        if (!orderDateValue) {
            return;
        }

        const dueDate = new Date(orderDateValue);
        if (Number.isNaN(dueDate.getTime())) return;

        dueDate.setDate(dueDate.getDate() + dueDays);
        const yyyy = dueDate.getFullYear();
        const mm = String(dueDate.getMonth() + 1).padStart(2, '0');
        const dd = String(dueDate.getDate()).padStart(2, '0');
        dueDateInput.value = `${yyyy}-${mm}-${dd}`;
    };


    dropdownMenu.addEventListener("click", function(e) {
        if(e.target.closest(".party-option")) {
            e.preventDefault();
            const option = e.target.closest(".party-option");
            const name = option.querySelector("span:first-child").textContent;
            let opening = parseFloat(option.dataset.opening) || 0;
            const type = option.dataset.type;
            const id = option.dataset.id;
            const selectedParty = (window.parties || []).find((party) => String(party.id) === String(id)) || {};
            const partyRecord = {
                phone: selectedParty.phone ?? option.dataset.phone ?? "",
                city: selectedParty.city ?? option.dataset.city ?? "",
                ptcl_number: selectedParty.ptcl_number ?? option.dataset.ptcl ?? "",
                address: selectedParty.address ?? option.dataset.address ?? "",
                billing_address: selectedParty.billing_address ?? option.dataset.billing ?? "",
                shipping_address: selectedParty.shipping_address ?? option.dataset.shipping ?? "",
                due_days: selectedParty.due_days ?? option.dataset.dueDays ?? "",
            };

            // Button pe sirf party name
            dropdownBtn.textContent = name;

            // Show balance below button with color
          if(type === "pay"){
    balanceDisplay.innerHTML = `
        <i class="fa-solid fa-arrow-up text-danger me-1"></i>
        ₹${opening.toFixed(2)}
    `;
}
else if(type === "receive"){
    balanceDisplay.innerHTML = `
        <i class="fa-solid fa-arrow-down text-success me-1"></i>
        ₹${opening.toFixed(2)}
    `;
}
else {
    balanceDisplay.innerHTML = `₹${opening.toFixed(2)}`;
}

            // Save selected party id
            partyIdInput.value = id;

            // Populate detail fields
            setPartyFieldValues(partyRecord);
            setDueDateFromParty(partyRecord);
        }
        else if(e.target.id === "addNewPartyBtn") {
            addModal.show();
            document.getElementById("addPartyForm").reset();
            balanceDisplay.textContent = "";
            setPartyFieldValues({});
        }
    });

    brokerDropdownMenu?.addEventListener("click", function(e) {
        if (!e.target.closest(".broker-option")) return;

        e.preventDefault();
        const option = e.target.closest(".broker-option");
        const name = option.dataset.name || option.querySelector("span:first-child").textContent;
        const phone = option.dataset.phone || "";
        const id = option.dataset.id || "";

        brokerDropdownBtn.textContent = name;
        brokerIdInput.value = id;
        document.querySelector(".broker-phone-input").value = phone;
    });

});

// Credit Limit Toggle
document.addEventListener("DOMContentLoaded", function() {
    const creditLimitSwitch = document.getElementById("creditLimitSwitch");
    const creditLimitAmountWrap = document.getElementById("creditLimitAmountWrap");

    if (creditLimitSwitch) {
        creditLimitSwitch.addEventListener("change", function() {
            if (this.checked) {
                creditLimitAmountWrap.classList.remove("is-hidden");
            } else {
                creditLimitAmountWrap.classList.add("is-hidden");
            }
        });
    }
});

// Add New Party button in dropdown
document.addEventListener("DOMContentLoaded", function() {
    const addNewPartyBtn = document.getElementById("addNewPartyBtn");
    const addPartyModal = document.getElementById("addPartyModal");

    if (addNewPartyBtn && addPartyModal) {
        addNewPartyBtn.addEventListener("click", function(e) {
            e.preventDefault();
            const modal = new bootstrap.Modal(addPartyModal);
            modal.show();
        });
    }
});
</script>
</body>

</html>
