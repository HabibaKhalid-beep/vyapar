<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($challan) ? 'Edit' : 'Create' }} Delivery Challan | Vyapar</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/estimateform_style.css') }}">
    @include('dashboard.shared.party-item-create-styles')
    <style>
        /* ══ FLOATING LABELS ══ */
        .party-meta-field { position: relative; }
        .party-meta-field label {
            position: absolute; top: 50%; left: 12px; transform: translateY(-50%);
            font-size: 12px; color: #94a3b8; font-weight: 400; pointer-events: none;
            transition: top .15s, font-size .13s, color .15s, transform .15s;
            z-index: 2; background: #fff; padding: 0 3px; line-height: 1; white-space: nowrap;
        }
        .party-meta-field:has(textarea) label { top: 16px; transform: none; }
        .party-meta-field:focus-within label,
        .party-meta-field.has-value label {
            top: -1px; transform: none; font-size: 9px; color: #2563eb;
            font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em;
        }
        .party-meta-field .meta-control:not(textarea),
        .party-meta-field input.meta-control,
        .party-meta-field select.meta-control { padding-top: 16px; padding-bottom: 4px; }
        .party-meta-field textarea.meta-control { padding-top: 20px; padding-bottom: 4px; }
        .party-meta-field .meta-control,
        .party-meta-field input,
        .party-meta-field select,
        .party-meta-field textarea {
            border: 1.5px solid #374151 !important; border-radius: 8px;
            outline: none; transition: border-color .15s, box-shadow .15s;
        }
        .party-meta-field .meta-control:focus,
        .party-meta-field input:focus,
        .party-meta-field select:focus,
        .party-meta-field textarea:focus {
            border-color: #2563eb !important; box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .party-meta-field.has-value .meta-control,
        .party-meta-field.has-value input,
        .party-meta-field.has-value select,
        .party-meta-field.has-value textarea { border-color: #2563eb !important; }

        /* ══ PARTY ACCORDION ══ */
        .party-accordion {
    display: none;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-top: 10px;
    animation: accordionDown .18s ease;
}
.party-accordion.open { display: grid; }
        .party-accordion.open { display: grid; }
        @keyframes accordionDown {
            from { opacity: 0; transform: translateY(-6px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .accordion-field {
            position: relative;
        }
        .accordion-field label {
            position: absolute; top: -1px; left: 10px;
            font-size: 9px; font-weight: 600; color: #2563eb;
            text-transform: uppercase; letter-spacing: 0.05em;
            background: #fff; padding: 0 3px; z-index: 2;
            pointer-events: none; line-height: 1;
        }
        .accordion-field input {
            width: 100%; min-height: 48px;
            padding: 18px 12px 6px;
            border: 1.5px solid #2563eb !important;
            border-radius: 8px;
            background: #f8fbff; color: #111827;
            font-size: 13px; outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .accordion-field input:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
            background: #fff;
        }

        /* ══ SCROLLABLE DROPDOWNS ══ */
        #partyDropdownMenu, #topbarWarehouseDropdownMenu {
            min-width: 250px; max-width: 100%; max-height: 350px;
            overflow-y: auto; overflow-x: hidden;
            scrollbar-width: thin; scrollbar-color: #888 #f1f1f1;
        }

        /* ══ LAYOUT ══ */
        .header-section {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 420px;
            gap: 28px;
            align-items: start;
        }

        .party-selector-panel { background: transparent; border: none; border-radius: 0; padding: 0; box-shadow: none; }

        .party-meta-field .meta-control {
            width: 100%; min-height: 48px; padding: 12px 14px;
            border: 1.5px solid #374151 !important; border-radius: 10px;
            background: #fbfdff; color: #111827; resize: none; font-size: 15px;
        }
        .party-meta-field textarea.meta-control { min-height: 82px; }

        .header-right.w-25 {
            width: 420px !important; min-width: 420px;
            background: #ffffff; border: 1px solid #dbe4f0; border-radius: 16px;
            padding: 18px 20px; box-shadow: 0 10px 24px rgba(15,23,42,0.04);
        }

        /* ══ TOP BAR ══ */
        .vyapar-topbar {
            display: flex; align-items: center; gap: 8px;
            padding: 8px 16px 6px; background: #fff; border-bottom: 1px solid #e8edf3;
        }
        .btn-add-party-topbar {
            display: inline-flex; align-items: center; gap: 5px;
            background: #fff; border: 1.5px solid #cbd5e1; border-radius: 6px;
            padding: 5px 12px; font-size: 13px; font-weight: 500; color: #374151;
            cursor: pointer; transition: border-color .15s, color .15s, background .15s; white-space: nowrap;
        }
        .btn-add-party-topbar:hover, .btn-add-party-topbar.active {
            border-color: #2563eb; color: #2563eb; background: #eff6ff;
        }
        .topbar-spacer { flex: 1; }
        .topbar-right-group { display: flex; align-items: center; gap: 6px; }
        .btn-store {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fff; border: 1.5px solid #cbd5e1; border-radius: 6px;
            padding: 5px 12px 5px 9px; font-size: 13px; font-weight: 500; color: #374151;
            cursor: pointer; transition: border-color .15s, box-shadow .15s; white-space: nowrap;
        }
        .btn-store:hover { border-color: #2563eb; color: #2563eb; }
        .btn-store .store-icon {
            width: 22px; height: 22px; background: #2563eb; border-radius: 4px;
            display: inline-flex; align-items: center; justify-content: center;
            color: #fff; font-size: 11px; flex-shrink: 0;
        }
        .btn-topbar-warehouse {
            display: inline-flex; align-items: center; gap: 6px;
            background: #fff; border: 1.5px solid #cbd5e1; border-radius: 6px;
            padding: 5px 12px; font-size: 13px; font-weight: 500; color: #374151;
            cursor: pointer; transition: border-color .15s, color .15s, background .15s;
            white-space: nowrap; min-width: 150px; justify-content: space-between;
        }
        .btn-topbar-warehouse:hover, .btn-topbar-warehouse.selected {
            border-color: #2563eb; color: #2563eb; background: #eff6ff;
        }
        .btn-topbar-warehouse::after { display: none !important; }
        #topbarWarehouseDropdownMenu {
            min-width: 280px; border-radius: 8px; border: 1px solid #dbe4f0;
            box-shadow: 0 8px 24px rgba(15,23,42,0.12); padding: 6px 0;
        }

        /* ══ BOTTOM RIGHT — Discount/Total panel ══ */
        .bottom-right {
            background: #fff; border: 1px solid #e2e8f0; border-radius: 14px;
            padding: 18px 20px; box-shadow: 0 4px 20px rgba(15,23,42,0.07); min-width: 280px;
        }
        .bottom-right .calc-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 7px 0; border-bottom: 1px solid #f1f5f9; gap: 12px;
        }
        .bottom-right .calc-row:last-of-type { border-bottom: none; }
        .bottom-right .calc-label { font-size: 13px; font-weight: 500; color: #374151; min-width: 130px; white-space: nowrap; }
        .bottom-right .calc-inputs { display: flex; align-items: center; gap: 6px; }
        .bottom-right .mini-input {
            border: 1.5px solid #e2e8f0 !important; border-radius: 6px;
            padding: 5px 10px; font-size: 13px; color: #111827; background: #f8fafc;
            transition: border-color .15s, box-shadow .15s; outline: none; min-width: 0;
        }
        .bottom-right .mini-input:focus {
            border-color: #2563eb !important; box-shadow: 0 0 0 3px rgba(37,99,235,0.10); background: #fff;
        }
        .bottom-right .final-total-group { margin-top: 12px; padding-top: 12px; border-top: 2px solid #e2e8f0; }
        .bottom-right .total-input-large {
            width: 100% !important; font-size: 22px !important; font-weight: 700;
            color: #2563eb; border: 2px solid #2563eb !important; border-radius: 10px;
            padding: 10px 16px !important; background: #eff6ff; text-align: right; letter-spacing: 0.02em;
        }
        .bottom-right .tax-amount-display { font-size: 13px; color: #64748b; min-width: 40px; text-align: right; }

        /* ══ BROKER INFO FIELDS (bottom left) ══ */
        .broker-info-grid {
            display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 14px;
        }
        .broker-info-grid .broker-full-row { grid-column: 1 / -1; }
        .broker-input-field { position: relative; }
        .broker-input-field label {
            position: absolute; top: -1px; left: 10px;
            font-size: 9px; font-weight: 600; color: #2563eb;
            text-transform: uppercase; letter-spacing: 0.05em;
            background: #fff; padding: 0 3px; z-index: 2; pointer-events: none;
        }
        .broker-input-field input {
            border: 1.5px solid #374151; border-radius: 8px;
            padding: 14px 12px 6px; font-size: 13px; color: #111827;
            background: #fff; width: 100%; outline: none;
            transition: border-color .15s, box-shadow .15s;
        }
        .broker-input-field input:focus {
            border-color: #2563eb !important; box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }

        /* ══ ACTION BUTTONS ══ */
        .btn-action-light {
            display: flex !important; align-items: center; justify-content: center; gap: 8px;
            border: 1.5px solid #374151 !important; border-radius: 8px !important;
            background: #fff !important; color: #374151 !important;
            font-size: 12px; font-weight: 500; padding: 10px 14px !important;
            cursor: pointer; transition: background .15s, border-color .15s, color .15s; min-height: 44px;
        }
        .btn-action-light:hover { background: #eff6ff !important; border-color: #2563eb !important; color: #2563eb !important; }

        /* ══ INLINE DESCRIPTION INPUT IN TABLE ══ */
        .item-desc-inline {
            border: 1.5px solid #e2e8f0; border-radius: 6px;
            padding: 6px 10px; font-size: 12px; width: 100%; outline: none;
            transition: border-color .15s;
        }
        .item-desc-inline:focus { border-color: #2563eb !important; }

        /* ══ UNIT SELECT IN TABLE ══ */
        .item-unit-select {
            border: 1.5px solid #e2e8f0; border-radius: 6px;
            padding: 5px 4px; font-size: 12px; width: 100%; outline: none;
            background: #f8fafc; color: #374151; cursor: pointer;
            transition: border-color .15s;
        }
        .item-unit-select:focus { border-color: #2563eb !important; background: #fff; }

        /* ══ ADD PARTY POPUP ══ */
        .party-quick-popup {
            display: none; position: fixed; top: 50%; left: 50%;
            transform: translate(-50%, -50%); z-index: 1060;
            background: #fff; border: 2px solid #2563eb; border-radius: 12px;
            box-shadow: 0 20px 60px rgba(37,99,235,0.18), 0 4px 20px rgba(0,0,0,0.10);
            width: 560px; max-width: 96vw; padding: 0; overflow: hidden;
        }
        .party-quick-popup.open { display: block; }
        .party-popup-backdrop {
            display: none; position: fixed; inset: 0; z-index: 1055; background: rgba(15,23,42,0.35);
        }
        .party-popup-backdrop.open { display: block; }
        .party-popup-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 16px 20px 12px; border-bottom: 1px solid #e2e8f0;
        }
        .party-popup-header h5 { font-size: 15px; font-weight: 600; color: #111827; margin: 0; }
        .popup-close-btn {
            background: none; border: none; font-size: 18px; color: #64748b; cursor: pointer;
            line-height: 1; padding: 2px 5px; border-radius: 4px; transition: background .12s, color .12s;
        }
        .popup-close-btn:hover { background: #f1f5f9; color: #111827; }
        .party-popup-body { padding: 18px 20px; }
        .party-popup-top-row { display: grid; grid-template-columns: 1.4fr 1fr 1fr; gap: 12px; margin-bottom: 14px; }
        .party-popup-tabs { display: flex; border-bottom: 2px solid #e2e8f0; margin-bottom: 16px; }
        .party-popup-tab {
            padding: 7px 16px; font-size: 13px; font-weight: 500; color: #64748b; cursor: pointer;
            border-bottom: 2px solid transparent; margin-bottom: -2px;
            transition: color .12s, border-color .12s; background: none;
            border-top: none; border-left: none; border-right: none;
        }
        .party-popup-tab:hover { color: #2563eb; }
        .party-popup-tab.active { color: #2563eb; border-bottom-color: #2563eb; }
        .party-popup-tab-pane { display: none; }
        .party-popup-tab-pane.active { display: block; }
        .credit-balance-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .pp-field { position: relative; margin-bottom: 4px; }
        .pp-field > label { display: none; }
        .pp-field input, .pp-field select, .pp-field textarea {
            border: 1.5px solid #374151; border-radius: 6px; padding: 18px 10px 6px 10px;
            font-size: 13px; color: #111827; background: #fff; outline: none;
            transition: border-color .15s, box-shadow .15s; width: 100%; min-height: 46px;
        }
        .pp-field select { padding-top: 18px; padding-bottom: 6px; appearance: auto; }
        .pp-field textarea { padding-top: 22px; padding-bottom: 6px; resize: vertical; min-height: 72px; }
        .pp-field input:focus, .pp-field select:focus, .pp-field textarea:focus {
            border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .pp-field::before {
            content: attr(data-label); position: absolute; left: 10px; top: 50%;
            transform: translateY(-50%); font-size: 13px; color: #94a3b8; pointer-events: none;
            transition: top .15s, font-size .15s, color .15s, transform .15s; z-index: 2; background: transparent; line-height: 1;
        }
        .pp-field.pp-textarea::before { top: 14px; transform: none; }
        .pp-field.floated::before, .pp-field.pp-textarea.floated::before {
            top: 7px; transform: none; font-size: 10px; color: #2563eb; font-weight: 600; letter-spacing: 0.03em;
        }
        .pp-field.pp-textarea.floated::before { top: 5px; }
        .pp-field.pp-select::before { top: 7px; transform: none; font-size: 10px; color: #2563eb; font-weight: 600; }
        .opening-balance-row { display: flex; align-items: center; border: 1.5px solid #374151; border-radius: 6px; overflow: hidden; }
        .opening-balance-row input { border: none !important; border-radius: 0 !important; flex: 1; box-shadow: none !important; }
        .opening-balance-type-select {
            border: none !important; border-left: 1.5px solid #374151 !important;
            border-radius: 0 !important; width: 100px !important; background: #f8faff !important;
            box-shadow: none !important; font-size: 12px !important;
        }
        .credit-limit-row { display: flex; align-items: center; gap: 10px; margin-top: 12px; }
        .credit-limit-label { font-size: 13px; font-weight: 500; color: #374151; }
        .toggle-switch { position: relative; display: inline-flex; align-items: center; cursor: pointer; gap: 6px; font-size: 12px; color: #64748b; }
        .toggle-switch input[type="checkbox"] { display: none; }
        .toggle-track { width: 36px; height: 20px; background: #2563eb; border-radius: 20px; position: relative; transition: background .15s; }
        .toggle-track::after { content: ''; position: absolute; top: 3px; left: 3px; width: 14px; height: 14px; background: #fff; border-radius: 50%; transition: left .15s; }
        .toggle-switch input:checked + .toggle-track { background: #2563eb; }
        .toggle-switch input:checked + .toggle-track::after { left: 19px; }
        .toggle-switch input:not(:checked) + .toggle-track { background: #94a3b8; }
        .party-popup-footer {
            display: flex; align-items: center; justify-content: flex-end; gap: 10px;
            padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc;
        }
        .btn-popup-secondary {
            padding: 8px 20px; border: 1.5px solid #cbd5e1; border-radius: 6px;
            background: #fff; font-size: 13px; font-weight: 500; color: #374151; cursor: pointer;
        }
        .btn-popup-primary {
            padding: 8px 24px; border: none; border-radius: 6px; background: #2563eb;
            font-size: 13px; font-weight: 600; color: #fff; cursor: pointer;
        }
        .btn-popup-primary:hover { background: #1d4ed8; }
        .btn-popup-primary:disabled { background: #93c5fd; cursor: not-allowed; }

        /* ══ ADD WAREHOUSE POPUP ══ */
        .warehouse-quick-popup {
            display: none; position: fixed; top: 50%; left: 50%;
            transform: translate(-50%, -50%); z-index: 1060;
            background: #fff; border: 2px solid #2563eb; border-radius: 12px;
            box-shadow: 0 20px 60px rgba(37,99,235,0.18); width: 540px; max-width: 96vw; padding: 0; overflow: hidden;
        }
        .warehouse-quick-popup.open { display: block; }
        .image-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(90px,1fr)); gap: 10px; margin-top: 10px; }
        .image-card { border: 1px solid #dbe4f0; border-radius: 10px; overflow: hidden; background: #fff; }
        .image-card img { width: 100%; height: 80px; object-fit: cover; }
        .compression-status { font-size: 12px; color: #64748b; }

        @media (max-width: 991px) {
            .header-section { grid-template-columns: 1fr; }
            .header-right.w-25 { width: 100% !important; min-width: 0; }
            .broker-info-grid { grid-template-columns: 1fr; }
            .party-accordion { grid-template-columns: 1fr; }
        }
    </style>
    <style>.party-accordion{display:none!important}.party-accordion.open{display:grid!important}</style>
</head>
<body>
    

    <div class="container-fluid min-vh-100 d-flex flex-column p-0">
        <header class="tab-system-header">
            <div class="tab-strip-wrapper justify-content-between">
                <div class="d-flex align-items-end flex-grow-1 overflow-hidden">
                    <div id="tab-strip" class="tab-strip d-flex align-items-end"></div>
                    <button id="add-tab-btn" class="btn add-tab-btn" title="New Tab"><i class="bi bi-plus-lg"></i></button>
                </div>
                <div class="window-controls d-flex align-items-center px-2 gap-3">
                    <i id="calc-icon" class="fa-solid fa-calculator" title="Calculator"></i>
                    <a href="{{ route('settings.transactions') }}" class="text-reset" title="Settings">
                        <i class="fa-solid fa-gear"></i>
                    </a>
                    <i class="fa-solid fa-xmark close-app-icon" title="Close Window"></i>
                </div>
            </div>
            <div class="browser-toolbar d-flex align-items-center px-3">
                <p class="mt-3 ms-3 mb-0 me-3 mb-2">Delivery Challan</p>
            </div>
        </header>

        {{-- ══ TOP ACTION BAR ══ --}}
        <div class="vyapar-topbar">
            <button type="button" class="btn-add-party-topbar" id="topbarAddPartyBtn">
                <i class="fa-solid fa-user-plus"></i> Add Party
            </button>
            <div class="topbar-spacer"></div>
            <div class="topbar-right-group">
                <div class="dropdown">
                    <button class="btn-topbar-warehouse" type="button" id="topbarWarehouseBtn"
                            data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                        <span style="display:flex;align-items:center;gap:6px;">
                            <i class="fa-solid fa-warehouse"></i>
                            <span id="topbarWarehouseLabel">Select Warehouse</span>
                        </span>
                        <i class="fa-solid fa-chevron-down" style="font-size:10px;color:#94a3b8;"></i>
                    </button>
                    <ul class="dropdown-menu" id="topbarWarehouseDropdownMenu" aria-labelledby="topbarWarehouseBtn"></ul>
                </div>
                <button type="button" class="btn-store" id="mainStoreBtn">
                    <span class="store-icon"><i class="fa-solid fa-store" style="font-size:11px;"></i></span>
                    <span class="store-label">Main Store</span>
                    <i class="fa-solid fa-chevron-down" style="font-size:10px;color:#94a3b8;margin-left:2px;"></i>
                </button>
            </div>
        </div>

        <main id="content-area">
            <template id="form-template">
                <div class="invoice-container">
                    <div class="invoice-form invoice-card">

                        {{-- ══ HEADER SECTION ══ --}}
                        <div class="header-section">

                            {{-- LEFT: Party fields --}}
                            <div>
                                {{-- Row 1: Party | Billing Name (always visible) --}}
                                <div style="display:flex; gap:12px; align-items:start;">

                                    {{-- Party dropdown --}}
                                    <div class="party-selector-panel">
                                        <div class="party-dropdown-wrapper" style="position:relative;">
                                            <label style="position:absolute;top:-1px;left:10px;
                                                font-size:9px;font-weight:600;color:#2563eb;
                                                text-transform:uppercase;letter-spacing:0.05em;
                                                background:#fff;padding:0 3px;z-index:2;
                                                pointer-events:none;line-height:1;">Party <span style="color:#ef4444;">*</span></label>
                                            <input type="text"
                                                class="party-search-input"
                                                placeholder=""
                                                autocomplete="off"
                                                style="width:100%;min-height:48px;
                                                    padding:18px 34px 6px 10px;
                                                    border:1.5px solid #374151;border-radius:8px;
                                                    background:#fbfdff;color:#111827;
                                                    font-size:13px;font-weight:500;cursor:pointer;
                                                    outline:none;transition:border-color .15s,box-shadow .15s;">
                                            <i class="fa-solid fa-chevron-down" style="position:absolute;right:10px;top:50%;
                                                transform:translateY(-50%);font-size:10px;color:#94a3b8;pointer-events:none;"></i>
                                        </div>
                                        {{-- Hidden fields --}}
                                        <input type="hidden" class="party-id" name="party_id">
                                        <input type="hidden" class="party-shipping-address" name="shipping_address">
                                        <div class="party-bal-display"
                                            style="color:#2563eb;font-weight:600;margin-top:3px;font-size:12px;min-height:16px;">
                                        </div>
                                    </div>

                                    {{-- Billing Name (always visible) --}}
                                    <div class="party-meta-field" style="width:200px;flex-shrink:0;">
                                        <label>Billing Name(Optional)</label>
                                        <input type="text" class="meta-control billing-name-input" name="billing_name">
                                    </div>

                                </div>

                                {{-- ══ ACCORDION: expands when a party is selected ══ --}}
                                <div class="party-accordion">
                                    <div class="accordion-field">
                                        <label>Phone Number</label>
                                        <input type="text" class="phone-visible-input" name="phone" placeholder="">
                                    </div>
                                    <div class="accordion-field">
                                        <label>Billing Address</label>
                                        <input type="text" class="billing-address-visible" name="billing_address" placeholder="">
                                    </div>
                                </div>

                            </div>

                            {{-- RIGHT: Challan info panel --}}
                            <div class="header-right w-25">
                                <div class="input-group">
                                    <span>Challan No.</span>
                                    <input type="text" class="input-control underline-input bill-number" value="{{ $nextInvoiceNumber ?? 'Auto' }}" readonly>
                                </div>
                                <div class="input-group date-wrapper mt-2">
                                    <span>Invoice Date</span>
                                    <input type="date" class="input-control underline-input invoice-date">
                                </div>
                                <div class="input-group date-wrapper mt-2">
                                    <span>Due Date</span>
                                    <input type="date" class="input-control underline-input due-date">
                                </div>
                            </div>
                        </div>

                        <div class="alert alert-success d-none sale-success-msg"></div>

                        {{-- ══ ITEMS TABLE ══ --}}
                        <div class="table-container">
                            <table class="item-table">
                                <thead>
                                    <tr>
                                        <th class="row-num">#</th>
                                        <th style="width:28%;">ITEM</th>
                                        <th>GROSS WEIGHT</th>
                                        <th>NET WEIGHT</th>
                                        <th>DESCRIPTION</th>
                                        <th>UNIT</th>
                                        <th>AMOUNT</th>
                                        <th class="add-col" style="position:relative;">
                                            <button type="button" class="btn-add-circle table-settings-btn"
                                                data-bs-toggle="modal" data-bs-target="#itemColumnModal">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="item-rows">
                                    <tr class="item-row">
                                        <td class="row-num">
                                            <span class="row-index-text">1</span>
                                            <div class="delete-row-icon"><i class="fa-solid fa-trash-can"></i></div>
                                        </td>
                                        <td>
                                            <select class="form-select item-name">
                                                <option value="" selected disabled>Select Item</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="item-qty gross-weight-input" value="0" min="0" step="0.01"></td>
                                        <td><input type="number" class="item-price net-weight-input" value="0" min="0" step="0.01"></td>
                                        <td><input type="text" class="item-desc-inline" placeholder="Description"></td>
                                        <td>
                                            <select class="item-unit-select" name="unit[]">
                                                <option value="">NONE</option>
                                                <option value="KGS">KGS</option>
                                                <option value="PCS">PCS</option>
                                                <option value="LTR">LTR</option>
                                                <option value="MTR">MTR</option>
                                                <option value="BOX">BOX</option>
                                                <option value="CTN">CTN</option>
                                                <option value="BAG">BAG</option>
                                                <option value="TON">TON</option>
                                            </select>
                                        </td>
                                        <td class="col-amount"><input type="number" class="item-amount" value="0"></td>
                                        <td class="add-col"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="table-footer">
                                <button type="button" class="btn-add-row add-row-btn">ADD ROW</button>
                                <div class="footer-totals">
                                    <div><span class="total-label">TOTAL GROSS WT</span><span class="total-qty">0</span></div>
                                    <div><span class="total-label">TOTAL AMOUNT</span><span class="total-base-amount">0</span></div>
                                </div>
                            </div>

                            {{-- ══ TOTAL ROW BELOW TABLE ══ --}}
                            <div style="display:flex; justify-content:flex-end; padding:10px 12px 4px; border-top:1px solid #e2e8f0;">
                                <div style="display:flex; align-items:center; gap:12px;">
                                    <span style="font-size:13px; font-weight:600; color:#374151; text-transform:uppercase; letter-spacing:0.04em;">Total</span>
                                    <div style="position:relative;">
                                        <input type="text"
                                            class="table-grand-total"
                                            value="0.00"
                                            readonly
                                            style="width:160px; font-size:18px; font-weight:700; color:#2563eb;
                                                border:2px solid #2563eb; border-radius:8px;
                                                padding:8px 14px; background:#eff6ff;
                                                text-align:right; outline:none; cursor:default;">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- ══ BOTTOM SECTION ══ --}}
                        <div class="bottom-section">
                            <div class="bottom-left">

                                <div class="broker-info-grid">
                                    <div class="broker-input-field">
                                        <label>BROKAR</label>
                                        <input type="text" name="broker_name" class="broker-name-field" placeholder="">
                                    </div>
                                    <div class="broker-input-field">
                                        <label>CITY</label>
                                        <input type="text" name="broker_city" class="broker-city-field" placeholder="">
                                    </div>
                                    <div class="broker-input-field">
                                        <label>TRANSPORT NAME / GOODZ</label>
                                        <input type="text" name="transport_name" class="transport-name-field" placeholder="">
                                    </div>
                                    <div class="broker-input-field">
                                        <label>DETAIL</label>
                                        <input type="text" name="transport_detail" class="transport-detail-field" placeholder="">
                                    </div>
                                    <div class="broker-input-field broker-full-row">
                                        <label>BILTI NO / GARI NO</label>
                                        <input type="text" name="bilti_no" class="bilti-no-field" placeholder="">
                                    </div>
                                </div>

                                <div style="display:flex;gap:8px;width:100%;flex-wrap:wrap;margin-bottom:10px;">
                                    <button type="button" class="btn-action-light add-image" style="flex:1;">
                                        <i class="fa-solid fa-camera"></i> ADD IMAGE
                                    </button>
                                    <button type="button" class="btn-action-light add-document" style="flex:1;">
                                        <i class="fa-solid fa-file-lines"></i> ADD DOCUMENT
                                    </button>
                                </div>

                                <input type="hidden" class="warehouse-id" name="warehouse_id">

                                <div class="image-gallery"></div>
                                <div class="compression-status mt-2"></div>
                                <input type="file" class="d-none image-input" accept="image/*" multiple>
                            </div>

                            {{-- ══ RIGHT: Discount / Tax / Total calc panel ══ --}}
                            <div class="bottom-right">

                                <div class="calc-row">
                                    <div class="calc-label">
                                        <span class="editable-heading" data-label-key="discount">Discount</span>
                                    </div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input discount-pct" placeholder="%" style="width:55px;" min="0" step="0.01">
                                        <span style="color:#94a3b8;font-size:11px;">%</span>
                                        <span style="color:#94a3b8;">-</span>
                                        <input type="number" class="mini-input discount-rs" placeholder="Rs" style="width:72px;" min="0" step="0.01">
                                    </div>
                                </div>

                                <div class="calc-row">
                                    <div class="calc-label">
                                        <input type="checkbox" class="custom-checkbox round-off-check" checked style="width:16px;height:16px;margin-right:4px;vertical-align:middle;">
                                        <label style="margin:0;font-size:12px;vertical-align:middle;">Round Off</label>
                                    </div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input round-off-val" value="0" readonly style="width:60px;text-align:right;">
                                    </div>
                                </div>

                                <div class="calc-row">
                                    <div class="calc-label">
                                        <span class="editable-heading" data-label-key="delivery_expense">Karaya/extra kharc...</span>
                                    </div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input delivery-expense" placeholder="Rs" min="0" step="0.01" style="width:95px;">
                                        <span class="delivery-expense-display tax-amount-display">0</span>
                                    </div>
                                </div>

                                <div class="calc-row">
                                    <div class="calc-label">
                                        <span class="editable-heading" data-label-key="brokerage_amount">BROKERI</span>
                                    </div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input brokerage-amount" placeholder="Rs" min="0" step="0.01" style="width:95px;">
                                    </div>
                                </div>

                                <div class="calc-row">
                                    <div class="calc-label">
                                        <span class="editable-heading" data-label-key="commission">COMISSION</span>
                                    </div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input commission-amount" placeholder="Rs" min="0" step="0.01" style="width:95px;">
                                    </div>
                                </div>

                                <div class="calc-row">
                                    <div class="calc-label">
                                        <span class="editable-heading" data-label-key="tax">Tax</span>
                                    </div>
                                    <div class="calc-inputs">
                                        <select class="mini-input tax-select" style="width:110px;">
                                            <option value="0">NONE</option>
                                            <option value="5">GST@5%</option>
                                            <option value="12">GST@12%</option>
                                            <option value="18">GST@18%</option>
                                        </select>
                                        <span class="tax-amount-display">0</span>
                                    </div>
                                </div>

                                <div class="final-total-group">
                                    <div class="calc-row" style="margin-bottom:6px;border-bottom:none;">
                                        <div class="calc-label" style="font-weight:700;font-size:14px;color:#111827;">
                                            <span class="editable-heading" data-label-key="total">Total</span>
                                        </div>
                                    </div>
                                    <input type="text" class="total-input-large grand-total" value="0" readonly>
                                </div>

                                <input type="hidden" name="discount_label" class="heading-value" data-field="discount" value="Discount">
                                <input type="hidden" name="delivery_expense_label" class="heading-value" data-field="delivery_expense" value="Karaya/extra kharc...">
                                <input type="hidden" name="brokerage_amount_label" class="heading-value" data-field="brokerage_amount" value="BROKERI">
                                <input type="hidden" name="commission_label" class="heading-value" data-field="commission" value="COMISSION">
                                <input type="hidden" name="tax_label" class="heading-value" data-field="tax" value="Tax">
                                <input type="hidden" name="total_label" class="heading-value" data-field="total" value="Total">
                            </div>
                        </div>
                    </div>

                    <div class="sticky-actions">
                        <div class="btn-share">
                            <button class="btn-share-main" type="button">Share</button>
                            <button class="btn-share-arrow" type="button"><i class="fa-solid fa-chevron-down"></i></button>
                        </div>
                        <button class="btn-save" type="button">Save</button>
                    </div>
                </div>
            </template>
        </main>
    </div>

    {{-- ADD PARTY POPUP --}}
    <div class="party-popup-backdrop" id="partyPopupBackdrop"></div>
    <div class="party-quick-popup" id="partyQuickPopup" role="dialog" aria-modal="true" aria-labelledby="partyPopupTitle">
        <div class="party-popup-header">
            <h5 id="partyPopupTitle">Add Party</h5>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="popup-close-btn" id="partyPopupCloseBtn">&times;</button>
            </div>
        </div>
        <div class="party-popup-body">
            <div class="party-popup-top-row">
                <div class="pp-field" data-label="Party Name *"><label>Party Name</label><input type="text" id="pp_name" autocomplete="off"></div>
                <div class="pp-field" data-label="Phone Number"><label>Phone Number</label><input type="text" id="pp_phone" autocomplete="off"></div>
                <div class="pp-field pp-select" data-label="Party Group"><label>Party Group</label>
                    <select id="pp_group">
                        <option value=""></option>
                        @foreach($partyGroups ?? [] as $group)
                        <option value="{{ $group->id }}">{{ $group->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="party-popup-tabs">
                <button type="button" class="party-popup-tab active" data-tab="address">Address</button>
                <button type="button" class="party-popup-tab" data-tab="credit">Credit &amp; Balance</button>
                <button type="button" class="party-popup-tab" data-tab="additional">Additional Fields</button>
            </div>
            <div class="party-popup-tab-pane active" data-pane="address">
                <div class="pp-field pp-textarea" data-label="Billing Address"><label>Billing Address</label><textarea id="pp_billing_address" rows="3"></textarea></div>
                <div class="pp-field pp-textarea mt-3" data-label="Shipping Address"><label>Shipping Address</label><textarea id="pp_shipping_address" rows="2"></textarea></div>
            </div>
            <div class="party-popup-tab-pane" data-pane="credit">
                <div class="credit-balance-grid">
                    <div class="pp-field floated" data-label="Opening Balance"><label>Opening Balance</label>
                        <div class="opening-balance-row">
                            <input type="number" id="pp_opening_balance" min="0" step="0.01">
                            <select class="opening-balance-type-select" id="pp_transaction_type">
                                <option value="">Type</option>
                                <option value="receive">To Receive</option>
                                <option value="pay">To Pay</option>
                            </select>
                        </div>
                    </div>
                    <div class="pp-field floated" data-label="As Of Date"><label>As Of Date</label><input type="date" id="pp_as_of_date"></div>
                </div>
                <div class="credit-limit-row">
                    <span class="credit-limit-label">Credit Limit</span>
                    <label class="toggle-switch"><input type="checkbox" id="pp_no_limit" checked><span class="toggle-track"></span><span>No Limit</span></label>
                </div>
                <div class="pp-field mt-3 d-none" id="pp_custom_limit_field" data-label="Credit Limit Amount"><label>Credit Limit Amount</label><input type="number" id="pp_credit_limit" min="0" step="0.01"></div>
            </div>
            <div class="party-popup-tab-pane" data-pane="additional">
                <div class="pp-field" data-label="GST / Tax Number"><label>GST / Tax Number</label><input type="text" id="pp_gst"></div>
                <div class="pp-field mt-3" data-label="Email"><label>Email</label><input type="email" id="pp_email"></div>
            </div>
        </div>
        <div class="party-popup-footer">
            <button type="button" class="btn-popup-secondary" id="partyPopupSaveNewBtn">Save &amp; New</button>
            <button type="button" class="btn-popup-primary" id="partyPopupSaveBtn">Save</button>
        </div>
    </div>

    {{-- ADD WAREHOUSE POPUP --}}
    <div class="warehouse-quick-popup" id="warehouseQuickPopup" role="dialog" aria-modal="true">
        <div class="party-popup-header">
            <h5>Add New Warehouse</h5>
            <button type="button" class="popup-close-btn" id="warehousePopupCloseBtn">&times;</button>
        </div>
        <div class="party-popup-body">
            <div class="row g-3">
                <div class="col-md-6"><div class="pp-field" data-label="Warehouse Name *"><label>Warehouse Name</label><input type="text" id="wh_name"></div></div>
                <div class="col-md-6"><div class="pp-field" data-label="Warehouse Phone"><label>Phone</label><input type="text" id="wh_phone"></div></div>
                <div class="col-md-6"><div class="pp-field" data-label="Handler Name"><label>Handler Name</label><input type="text" id="wh_handler_name"></div></div>
                <div class="col-md-6"><div class="pp-field" data-label="Handler Phone"><label>Handler Phone</label><input type="text" id="wh_handler_phone"></div></div>
                <div class="col-md-6">
                    <div class="pp-field pp-select" data-label="Related User"><label>Related User</label>
                        <select id="wh_user_id">
                            <option value=""></option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}{{ $user->email ? ' (' . $user->email . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6"><div class="pp-field pp-textarea" data-label="Address"><label>Address</label><textarea id="wh_address" rows="2"></textarea></div></div>
            </div>
        </div>
        <div class="party-popup-footer">
            <button type="button" class="btn-popup-secondary" id="warehousePopupCancelBtn">Cancel</button>
            <button type="button" class="btn-popup-primary" id="warehousePopupSaveBtn">Save Warehouse</button>
        </div>
    </div>

    {{-- Modals --}}
    <div class="modal fade" id="tabLimitModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content bg-dark text-dark border-secondary"><div class="modal-body text-center p-4"><i class="bi bi-exclamation-triangle text-warning display-4 mb-3"></i><h5>Maximum Limit Reached</h5><p>You can open a maximum of 10 transactions at a time.</p><button type="button" class="btn btn-primary px-4 mt-2" data-bs-dismiss="modal">OK</button></div></div></div></div>
    <div class="modal fade" id="closeConfirmModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content bg-dark text-dark border-secondary"><div class="modal-header border-secondary"><h5 class="modal-title">Close Tab?</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><p>Are you sure you want to close this tab? Your delivery challan will not be saved.</p></div><div class="modal-footer border-secondary"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" id="confirm-close-btn" class="btn btn-danger">Close</button></div></div></div></div>
    <div class="modal fade" id="editHeadingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Change field label</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body"><div class="mb-3"><label for="headingNameInput" class="form-label">Field name</label><input type="text" id="headingNameInput" class="form-control" placeholder="Enter new label"></div></div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary save-heading-btn">Save</button></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @php
        $challanPayload = isset($challan) ? $challan->load(['items','party','broker','challanDetail'])->toArray() : null;
        $duplicatePayload = isset($duplicateChallan) ? array_merge($duplicateChallan->load(['items','party','broker','challanDetail'])->toArray(), ['bill_number' => $nextInvoiceNumber]) : null;
    @endphp

    <script>
        window.items              = @json($items ?? []);
        window.parties            = @json($parties ?? []);
        window.brokers            = @json($brokers ?? []);
        window.bankAccounts       = @json($bankAccounts ?? []);
        window.bankAccountRoutes  = { store: "{{ route('bank-accounts.store') }}" };
        window.transactionSettings = {
            countEnabled: @json(\App\Models\AppSetting::getValue('transaction_items_count_enabled','0') === '1'),
            countLabel: 'Count'
        };
        window.responsibleUsers   = @json($users ?? []);
        window.warehouses         = @json($warehouses ?? []);
        window.warehouseStoreUrl  = "{{ route('warehouses.store') }}";
        window.partyStoreUrl      = "{{ route('parties.store') }}";
        window.partyGroupStoreUrl = "{{ route('party-groups.store') }}";
        window.itemRoutes = {
            index:         "{{ url('dashboard/items') }}",
            store:         "{{ url('dashboard/items') }}",
            unitsIndex:    "{{ url('dashboard/items/units') }}",
            unitsStore:    "{{ url('dashboard/items/units') }}",
            categoryStore: "{{ url('dashboard/items/category') }}"
        };
        window.saleStoreUrl   = "{{ isset($challan) ? route('delivery-challan.update', $challan->id) : route('delivery-challan.store') }}";
        window.saleHttpMethod = "{{ isset($challan) ? 'PUT' : 'POST' }}";
        window.challanId      = @json($challan->id ?? null);
        window.editSaleData   = @json($challanPayload ?? $duplicatePayload);
        window.docType        = 'delivery_challan';
    </script>

    {{-- ══ Editable Heading Labels ══ --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        var modalEl      = document.getElementById('editHeadingModal');
        var headingInput = document.getElementById('headingNameInput');
        var saveButton   = modalEl ? modalEl.querySelector('.save-heading-btn') : null;
        var editModal    = modalEl ? new bootstrap.Modal(modalEl) : null;
        var storageKey   = 'deliveryChallanHeadingLabels';
        var currentKey   = '';
        var defaults     = { discount:'Discount', delivery_expense:'Karaya/extra kharc...', brokerage_amount:'BROKERI', commission:'COMISSION', tax:'Tax', total:'Total' };

        function getLabels() {
            var labels = Object.assign({}, defaults);
            try { var s = localStorage.getItem(storageKey); if (s) Object.assign(labels, JSON.parse(s)); } catch(e) {}
            return labels;
        }
        function applyLabels(root) {
            if (!root) return;
            Object.entries(getLabels()).forEach(function(e) {
                root.querySelectorAll('.editable-heading[data-label-key="'+e[0]+'"]').forEach(function(el){ el.textContent = e[1]; });
                root.querySelectorAll('.heading-value[data-field="'+e[0]+'"]').forEach(function(el){ el.value = e[1]; });
            });
        }
        applyLabels(document);
        var tpl = document.getElementById('form-template');
        if (tpl) applyLabels(tpl.content || tpl);

        document.body.addEventListener('click', function(e) {
            var t = e.target.closest('.editable-heading');
            if (!t) return;
            e.preventDefault();
            currentKey = t.dataset.labelKey || '';
            if (headingInput) headingInput.value = t.textContent.trim();
            if (editModal) editModal.show();
        });
        if (saveButton) saveButton.addEventListener('click', function() {
            var val = headingInput ? headingInput.value.trim() : '';
            if (!val) return;
            document.querySelectorAll('.editable-heading[data-label-key="'+currentKey+'"]').forEach(function(el){ el.textContent = val; });
            document.querySelectorAll('.heading-value[data-field="'+currentKey+'"]').forEach(function(el){ el.value = val; });
            try { var l={}; document.querySelectorAll('.editable-heading').forEach(function(el){ if(el.dataset.labelKey) l[el.dataset.labelKey]=el.textContent.trim(); }); localStorage.setItem(storageKey, JSON.stringify(l)); } catch(e){}
            if (editModal) editModal.hide();
        });
        if (headingInput) headingInput.addEventListener('keydown', function(e){ if(e.key==='Enter'){e.preventDefault(); if(saveButton) saveButton.click();} });
    });
    </script>

    {{-- ══ Item select population ══ --}}
    <script>
    (function(){
        function populateItemSelects(root) {
            root = root || document;
            var items = window.items || [];
            root.querySelectorAll('select.item-name').forEach(function(sel){
                var cur = sel.value;
                while(sel.options.length > 1) sel.remove(1);
                items.forEach(function(item){
                    var opt = document.createElement('option');
                    opt.value = item.id || '';
                    opt.textContent = item.name || item.item_name || '';
                    opt.dataset.price = item.sale_price || item.price || 0;
                    opt.dataset.desc  = item.description || '';
                    sel.appendChild(opt);
                });
                if(cur) sel.value = cur;
            });
        }
        document.addEventListener('DOMContentLoaded', function(){
            populateItemSelects();
            document.addEventListener('challan:formAdded', function(){ populateItemSelects(); });
            new MutationObserver(function(muts){
                muts.forEach(function(m){
                    m.addedNodes.forEach(function(n){
                        if(n.nodeType===1 && n.querySelectorAll && n.querySelectorAll('select.item-name').length) populateItemSelects(n);
                    });
                });
            }).observe(document.body, {childList:true, subtree:true});

            document.addEventListener('change', function(e){
                if(!e.target.classList.contains('item-name')) return;
                var sel = e.target, row = sel.closest('tr');
                if(!row) return;
                var opt = sel.options[sel.selectedIndex];
                if(!opt || !opt.value) return;
                var descInp = row.querySelector('.item-desc-inline');
                if(descInp && opt.dataset.desc) descInp.value = opt.dataset.desc;
                var grossInp = row.querySelector('.gross-weight-input');
                var netInp   = row.querySelector('.net-weight-input');
                var amtInp   = row.querySelector('.item-amount');
                if(grossInp && netInp && amtInp){
                    amtInp.value = ((parseFloat(grossInp.value)||0) * (parseFloat(netInp.value)||0)).toFixed(2);
                }
                var container = row.closest('.invoice-container') || document;
                recalcTotal(container);
            });
        });
    })();
    </script>

    {{-- ══ Total / Discount recalculation + Party Accordion ══ --}}
    <script>
    function recalcTotal(container) {
        container = container || document;
        var base = 0;
        container.querySelectorAll('.item-amount').forEach(function(inp){ base += parseFloat(inp.value)||0; });
        var totalGross = 0;
        container.querySelectorAll('.gross-weight-input').forEach(function(inp){ totalGross += parseFloat(inp.value)||0; });
        var tqEl = container.querySelector('.total-qty');
        if(tqEl) tqEl.textContent = totalGross.toFixed(2);
        var tbEl = container.querySelector('.total-base-amount');
        if(tbEl) tbEl.textContent = base.toFixed(2);

        var discPct = parseFloat(container.querySelector('.discount-pct')?.value)||0;
        var discRs  = parseFloat(container.querySelector('.discount-rs')?.value)||0;
        if(discPct > 0) discRs = base * discPct / 100;
        var afterDisc = base - discRs;

        var delivery   = parseFloat(container.querySelector('.delivery-expense')?.value)||0;
        var brokeri    = parseFloat(container.querySelector('.brokerage-amount')?.value)||0;
        var commission = parseFloat(container.querySelector('.commission-amount')?.value)||0;

        var taxPct = parseFloat(container.querySelector('.tax-select')?.value)||0;
        var taxAmt = afterDisc * taxPct / 100;
        var taxDisplayEls = container.querySelectorAll('.tax-amount-display');
        if(taxDisplayEls.length > 0) taxDisplayEls[taxDisplayEls.length-1].textContent = taxAmt.toFixed(2);

        var subtotal = afterDisc + delivery + brokeri + commission + taxAmt;

        var roCheck = container.querySelector('.round-off-check');
        var roundVal = 0;
        if(roCheck && roCheck.checked){
            var rounded = Math.round(subtotal);
            roundVal = rounded - subtotal;
            subtotal = rounded;
        }
        var roEl = container.querySelector('.round-off-val');
        if(roEl) roEl.value = roundVal.toFixed(2);

        var gtEl = container.querySelector('.grand-total');
        if(gtEl) gtEl.value = subtotal.toFixed(2);

        // Also sync the table-level total display
        var tgtEl = container.querySelector('.table-grand-total');
        if(tgtEl) tgtEl.value = subtotal.toFixed(2);
    }

    document.addEventListener('DOMContentLoaded', function(){

        /* ══ Floating label has-value tracking ══ */
        function trackHasValue(root){
            root = root||document;
            root.querySelectorAll('.party-meta-field input,.party-meta-field textarea').forEach(function(el){
                function upd(){ var f=el.closest('.party-meta-field'); if(f) f.classList.toggle('has-value',!!(el.value&&el.value.trim())); }
                el.addEventListener('input',upd); el.addEventListener('blur',upd); upd();
            });
            root.querySelectorAll('.party-meta-field select').forEach(function(el){
                function upd(){ var f=el.closest('.party-meta-field'); if(f) f.classList.toggle('has-value',!!(el.value&&el.value!=='')); }
                el.addEventListener('change',upd); el.addEventListener('blur',upd); upd();
            });
        }
        trackHasValue(document);
        document.addEventListener('challan:formAdded',function(){ trackHasValue(document); });
        new MutationObserver(function(m){ m.forEach(function(mu){ mu.addedNodes.forEach(function(n){ if(n.nodeType===1) trackHasValue(n); }); }); }).observe(document.body,{childList:true,subtree:true});

        /* ══ Input events → recalc ══ */
        document.addEventListener('input', function(e){
            if(!e.target.matches('.item-amount,.gross-weight-input,.net-weight-input,.discount-pct,.discount-rs,.delivery-expense,.brokerage-amount,.commission-amount')) return;
            var container = e.target.closest('.invoice-container')||document;
            if(e.target.matches('.gross-weight-input,.net-weight-input')){
                var row = e.target.closest('tr');
                if(row){
                    var gross = parseFloat(row.querySelector('.gross-weight-input')?.value)||0;
                    var net   = parseFloat(row.querySelector('.net-weight-input')?.value)||0;
                    var amtInp = row.querySelector('.item-amount');
                    if(amtInp) amtInp.value = (gross*net).toFixed(2);
                }
            }
            if(e.target.classList.contains('discount-pct')){
                var base=0; container.querySelectorAll('.item-amount').forEach(function(i){ base+=parseFloat(i.value)||0; });
                var rsInp = container.querySelector('.discount-rs');
                if(rsInp) rsInp.value = (base*(parseFloat(e.target.value)||0)/100).toFixed(2);
            }
            if(e.target.classList.contains('discount-rs')){
                var base2=0; container.querySelectorAll('.item-amount').forEach(function(i){ base2+=parseFloat(i.value)||0; });
                var pctInp = container.querySelector('.discount-pct');
                if(pctInp && base2>0) pctInp.value = ((parseFloat(e.target.value)||0)/base2*100).toFixed(2);
            }
            recalcTotal(container);
        });
        document.addEventListener('change', function(e){
            if(!e.target.matches('.tax-select,.round-off-check')) return;
            recalcTotal(e.target.closest('.invoice-container')||document);
        });

       

  

        /* ══ Topbar Warehouse Dropdown ══ */
        function buildTopbarWarehouseDropdown(){
            var menu = document.getElementById('topbarWarehouseDropdownMenu'); if(!menu) return;
            menu.innerHTML='';
            var whs = window.warehouses||[];
            if(!whs.length){ menu.innerHTML='<li><span class="dropdown-item-text text-muted" style="font-size:12px;">No warehouses yet</span></li>'; }
            else {
                whs.forEach(function(wh){
                    var li=document.createElement('li'), a=document.createElement('a');
                    a.className='dropdown-item topbar-wh-option d-flex justify-content-between align-items-center'; a.href='#';
                    a.dataset.id=wh.id||''; a.dataset.name=wh.name||''; a.dataset.phone=wh.phone||'';
                    a.innerHTML='<span style="font-weight:500;">'+(wh.name||'')+'</span><span style="font-size:11px;color:#94a3b8;">'+(wh.phone||'')+'</span>';
                    li.appendChild(a); menu.appendChild(li);
                });
                var d=document.createElement('li'); d.innerHTML='<hr class="dropdown-divider">'; menu.appendChild(d);
            }
            var addLi=document.createElement('li'), addA=document.createElement('a');
            addA.className='dropdown-item topbar-wh-add'; addA.href='#';
            addA.innerHTML='<i class="fa-solid fa-plus me-1"></i> Add New Warehouse';
            addLi.appendChild(addA); menu.appendChild(addLi);
        }
        buildTopbarWarehouseDropdown();
        document.addEventListener('challan:formAdded', buildTopbarWarehouseDropdown);

        document.addEventListener('click', function(e){
            var opt = e.target.closest('#topbarWarehouseDropdownMenu .topbar-wh-option');
            if(opt){ e.preventDefault();
                document.getElementById('topbarWarehouseLabel').textContent = opt.dataset.name||'Select Warehouse';
                document.getElementById('topbarWarehouseBtn').classList.add('selected');
                document.querySelectorAll('#topbarWarehouseDropdownMenu .topbar-wh-option').forEach(function(o){ o.classList.remove('active'); });
                opt.classList.add('active');
                document.querySelectorAll('.warehouse-id').forEach(function(el){ el.value=opt.dataset.id||''; });
                return;
            }
            var addLink = e.target.closest('#topbarWarehouseDropdownMenu .topbar-wh-add');
            if(addLink){ e.preventDefault(); resetWarehouseForm(); openWarehousePopup(); }
        });

        /* ══ ADD PARTY POPUP ══ */
        var partyPopup    = document.getElementById('partyQuickPopup');
        var partyBackdrop = document.getElementById('partyPopupBackdrop');
        var addPartyBtn   = document.getElementById('topbarAddPartyBtn');

        function initFloatingLabels(root){
            if(!root) return;
            root.querySelectorAll('.pp-field input,.pp-field textarea').forEach(function(el){
                function upd(){ var f=el.closest('.pp-field'); if(f) f.classList.toggle('floated',!!(el.value||document.activeElement===el)); }
                el.addEventListener('focus',upd); el.addEventListener('blur',upd); el.addEventListener('input',upd); upd();
            });
            root.querySelectorAll('.pp-field.pp-select select,.pp-field input[type="date"]').forEach(function(el){
                var f=el.closest('.pp-field'); if(f) f.classList.add('floated');
            });
        }
        initFloatingLabels(partyPopup);
        initFloatingLabels(document.getElementById('warehouseQuickPopup'));

        function openPartyPopup(){ partyPopup.classList.add('open'); partyBackdrop.classList.add('open'); if(addPartyBtn) addPartyBtn.classList.add('active'); var d=new Date().toISOString().split('T')[0]; var aod=document.getElementById('pp_as_of_date'); if(aod) aod.value=d; initFloatingLabels(partyPopup); setTimeout(function(){ var pn=document.getElementById('pp_name'); if(pn) pn.focus(); },80); }
        function closePartyPopup(){ partyPopup.classList.remove('open'); partyBackdrop.classList.remove('open'); if(addPartyBtn) addPartyBtn.classList.remove('active'); }
        function resetPartyForm(){
            ['pp_name','pp_phone','pp_billing_address','pp_shipping_address','pp_opening_balance','pp_gst','pp_email','pp_credit_limit'].forEach(function(id){ var el=document.getElementById(id); if(el){el.value=''; var f=el.closest('.pp-field'); if(f) f.classList.remove('floated');} });
            ['pp_group','pp_transaction_type'].forEach(function(id){ var el=document.getElementById(id); if(el) el.value=''; });
            var nolimit=document.getElementById('pp_no_limit'); if(nolimit) nolimit.checked=true;
            var clf=document.getElementById('pp_custom_limit_field'); if(clf) clf.classList.add('d-none');
            document.querySelectorAll('.party-popup-tab').forEach(function(t){t.classList.remove('active');});
            document.querySelectorAll('.party-popup-tab-pane').forEach(function(p){p.classList.remove('active');});
            var at=document.querySelector('.party-popup-tab[data-tab="address"]'); if(at) at.classList.add('active');
            var ap=document.querySelector('.party-popup-tab-pane[data-pane="address"]'); if(ap) ap.classList.add('active');
        }

        if(addPartyBtn) addPartyBtn.addEventListener('click', function(){ resetPartyForm(); openPartyPopup(); });
        var closePBtn = document.getElementById('partyPopupCloseBtn');
        if(closePBtn) closePBtn.addEventListener('click', closePartyPopup);
        if(partyBackdrop) partyBackdrop.addEventListener('click', function(){ closePartyPopup(); closeWarehousePopup(); });
        document.addEventListener('keydown', function(e){ if(e.key==='Escape'){ closePartyPopup(); closeWarehousePopup(); } });

        document.querySelectorAll('.party-popup-tab').forEach(function(tab){
            tab.addEventListener('click', function(){
                document.querySelectorAll('.party-popup-tab').forEach(function(t){t.classList.remove('active');});
                document.querySelectorAll('.party-popup-tab-pane').forEach(function(p){p.classList.remove('active');});
                this.classList.add('active');
                var pane=document.querySelector('.party-popup-tab-pane[data-pane="'+this.dataset.tab+'"]'); if(pane) pane.classList.add('active');
                initFloatingLabels(partyPopup);
            });
        });

        var ppNoLimit = document.getElementById('pp_no_limit');
        if(ppNoLimit) ppNoLimit.addEventListener('change', function(){ var clf=document.getElementById('pp_custom_limit_field'); if(clf) clf.classList.toggle('d-none',this.checked); });

        var csrfToken = document.querySelector('meta[name="csrf-token"]')?.content||'';

        function showToast(msg, isError){
            var t=document.getElementById('sale-toast'); if(!t) return alert(msg);
            t.querySelector('.toast-body').textContent=msg;
            t.classList.toggle('text-bg-success',!isError); t.classList.toggle('text-bg-danger',!!isError);
            new bootstrap.Toast(t,{delay:4000}).show();
        }

        async function saveParty(keepOpen){
            var nameEl=document.getElementById('pp_name'), name=(nameEl.value||'').trim();
            if(!name){ nameEl.style.borderColor='#e53e3e'; nameEl.focus(); showToast('Party Name is required.',true); return; }
            nameEl.style.borderColor='';
            var saveBtn=document.getElementById('partyPopupSaveBtn'), saveNewBtn=document.getElementById('partyPopupSaveNewBtn');
            var activeBtn=keepOpen?saveNewBtn:saveBtn, origText=activeBtn.textContent;
            saveBtn.disabled=saveNewBtn.disabled=true; activeBtn.textContent='Saving...';
            try{
                var fd=new FormData();
                fd.append('name',name); fd.append('phone',(document.getElementById('pp_phone').value||'').trim());
                fd.append('party_group_id',document.getElementById('pp_group').value||'');
                fd.append('billing_address',(document.getElementById('pp_billing_address').value||'').trim());
                fd.append('shipping_address',(document.getElementById('pp_shipping_address').value||'').trim());
                fd.append('opening_balance',document.getElementById('pp_opening_balance').value||0);
                fd.append('transaction_type',document.getElementById('pp_transaction_type').value||'');
                fd.append('as_of_date',document.getElementById('pp_as_of_date').value||'');
                fd.append('gst_number',(document.getElementById('pp_gst').value||'').trim());
                fd.append('email',(document.getElementById('pp_email').value||'').trim());
                if(!document.getElementById('pp_no_limit').checked) fd.append('credit_limit',document.getElementById('pp_credit_limit').value||'');
                var res=await fetch(window.partyStoreUrl,{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:fd});
                var data=await res.json();
                if(!res.ok){ var fe=data.errors?Object.values(data.errors)[0]:null; throw new Error(fe?(Array.isArray(fe)?fe[0]:fe):(data.message||'Failed')); }
                var newParty=data.party||data.data||(data.id?data:null);
                if(!newParty) throw new Error('Party saved but no data returned');
                window.parties=window.parties||[]; window.parties.unshift(newParty);
                showToast('Party "'+(newParty.name||name)+'" added successfully.');
                if(keepOpen){ resetPartyForm(); initFloatingLabels(partyPopup); document.getElementById('pp_name').focus(); }
                else{
                    closePartyPopup();
                    var ctx = document.querySelector('#content-area .invoice-container') || document;
                    if(ctx && newParty.id){
                        var pid = ctx.querySelector('.party-id'); if(pid) pid.value = newParty.id;
                        var psi = ctx.querySelector('.party-search-input');
                        if(psi){ psi.value = newParty.name||''; psi.style.borderColor='#2563eb'; psi.style.boxShadow='0 0 0 3px rgba(37,99,235,0.12)'; }
                        var balEl = ctx.querySelector('.party-bal-display');
                        if(balEl) balEl.textContent = 'BAL: '+parseFloat(newParty.opening_balance||0).toFixed(2);
                        // Populate accordion
                        var phoneInp = ctx.querySelector('.phone-visible-input');
                        if(phoneInp) phoneInp.value = newParty.phone||'';
                        var billAddrInp = ctx.querySelector('.billing-address-visible');
                        if(billAddrInp) billAddrInp.value = newParty.billing_address||'';
                        var staInp = ctx.querySelector('.party-shipping-address');
                        if(staInp) staInp.value = newParty.shipping_address||'';
                        // Open accordion
                        var acc = ctx.querySelector('.party-accordion');
                        if(acc) acc.classList.add('open');
                        // Billing name
                        var bni = ctx.querySelector('.billing-name-input');
                        if(bni && !bni.value.trim()){ bni.value = newParty.name||''; var nf=bni.closest('.party-meta-field'); if(nf) nf.classList.toggle('has-value',!!bni.value.trim()); }
                    }
                }
            }catch(err){ showToast(err.message||'Error saving party.',true); }
            finally{ saveBtn.disabled=saveNewBtn.disabled=false; activeBtn.textContent=origText; }
        }
        var spb=document.getElementById('partyPopupSaveBtn'); if(spb) spb.addEventListener('click',function(){ saveParty(false); });
        var spnb=document.getElementById('partyPopupSaveNewBtn'); if(spnb) spnb.addEventListener('click',function(){ saveParty(true); });

        /* ══ ADD WAREHOUSE POPUP ══ */
        var whPopup = document.getElementById('warehouseQuickPopup');
        function openWarehousePopup(){ whPopup.classList.add('open'); partyBackdrop.classList.add('open'); initFloatingLabels(whPopup); setTimeout(function(){ var wn=document.getElementById('wh_name'); if(wn) wn.focus(); },80); }
        function closeWarehousePopup(){ whPopup.classList.remove('open'); partyBackdrop.classList.remove('open'); }
        function resetWarehouseForm(){
            ['wh_name','wh_phone','wh_handler_name','wh_handler_phone','wh_address'].forEach(function(id){ var el=document.getElementById(id); if(el){el.value=''; var f=el.closest('.pp-field'); if(f) f.classList.remove('floated');} });
            var wu=document.getElementById('wh_user_id'); if(wu) wu.value='';
        }
        var whCloseBtn=document.getElementById('warehousePopupCloseBtn'); if(whCloseBtn) whCloseBtn.addEventListener('click',closeWarehousePopup);
        var whCancelBtn=document.getElementById('warehousePopupCancelBtn'); if(whCancelBtn) whCancelBtn.addEventListener('click',closeWarehousePopup);
        var whSaveBtn=document.getElementById('warehousePopupSaveBtn');
        if(whSaveBtn) whSaveBtn.addEventListener('click', async function(){
            var nameEl=document.getElementById('wh_name'), name=(nameEl.value||'').trim();
            if(!name){ nameEl.style.borderColor='#e53e3e'; nameEl.focus(); showToast('Warehouse Name is required.',true); return; }
            nameEl.style.borderColor='';
            var btn=this; btn.disabled=true; btn.textContent='Saving...';
            try{
                var fd=new FormData();
                fd.append('name',name); fd.append('phone',(document.getElementById('wh_phone').value||'').trim());
                fd.append('handler_name',(document.getElementById('wh_handler_name').value||'').trim());
                fd.append('handler_phone',(document.getElementById('wh_handler_phone').value||'').trim());
                fd.append('responsible_user_id',document.getElementById('wh_user_id').value||'');
                fd.append('address',(document.getElementById('wh_address').value||'').trim());
                var res=await fetch(window.warehouseStoreUrl,{method:'POST',headers:{'X-CSRF-TOKEN':csrfToken,'Accept':'application/json'},body:fd});
                var data=await res.json();
                if(!res.ok){ var fe=data.errors?Object.values(data.errors)[0]:null; throw new Error(fe?(Array.isArray(fe)?fe[0]:fe):(data.message||'Failed')); }
                var wh=data.warehouse||data.data||(data.id?data:null);
                if(!wh) throw new Error('Warehouse saved but no data returned');
                window.warehouses=window.warehouses||[]; window.warehouses.push(wh);
                buildTopbarWarehouseDropdown();
                document.getElementById('topbarWarehouseLabel').textContent=wh.name||'Select Warehouse';
                document.getElementById('topbarWarehouseBtn').classList.add('selected');
                document.querySelectorAll('.warehouse-id').forEach(function(el){ el.value=wh.id||''; });
                showToast('Warehouse "'+(wh.name||name)+'" added successfully.');
                closeWarehousePopup();
            }catch(err){ showToast(err.message||'Error saving warehouse',true); }
            finally{ btn.disabled=false; btn.textContent='Save Warehouse'; }
        });

        var mainStoreBtn=document.getElementById('mainStoreBtn');
        if(mainStoreBtn) mainStoreBtn.addEventListener('click',function(){ showToast('Main Store selected.'); });
    });
    </script>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index:1080;">
        <div id="sale-toast" class="toast align-items-center text-bg-success border-0" role="alert">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>
    </div>

    @include('components.modals.party-modal')
    @include('components.modals.item-modal')
    @include('dashboard.shared.item-column-modal')
    @include('components.bank-account-modal')

    <script src="{{ asset('js/challanform_script.js') }}"></script>
    <script src="{{ asset('js/challanscript.js') }}"></script>
    <script src="{{ asset('js/shared-party-item-create.js') }}"></script>
    <script src="{{ asset('js/bank-account-modal.js') }}"></script>
    <script src="{{ asset('js/transaction-count-column.js') }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
        var closeIcon = document.querySelector('.close-app-icon');
        if(!closeIcon) return;
        closeIcon.addEventListener('click', function(){
            if(window.history.length > 1) window.history.back();
            else window.location.href = '/dashboard/sales';
        });
    });
    </script>
</body>
</html>