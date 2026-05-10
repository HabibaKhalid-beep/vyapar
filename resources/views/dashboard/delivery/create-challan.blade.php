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
        /* ══════════════════════════════════════════════════
   GLOBAL FLOATING LABELS — ALL .party-meta-field
══════════════════════════════════════════════════ */
.party-meta-field {
    position: relative;
}
.party-meta-field label {
    position: absolute;
    top: 50%;
    left: 12px;
    transform: translateY(-50%);
    font-size: 12px;
    color: #94a3b8;
    font-weight: 400;
    text-transform: none;
    letter-spacing: 0;
    pointer-events: none;
    transition: top .15s, font-size .13s, color .15s, transform .15s, font-weight .15s;
    z-index: 2;
    background: #fff;
    padding: 0 3px;
    line-height: 1;
    white-space: nowrap;
}
.party-meta-field:has(textarea) label {
    top: 16px;
    transform: none;
}
.party-meta-field:focus-within label,
.party-meta-field.has-value label {
    top: -1px;
    transform: none;
    font-size: 9px;
    color: #2563eb;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.party-meta-field .meta-control:not(textarea),
.party-meta-field input.meta-control,
.party-meta-field select.meta-control {
    padding-top: 16px;
    padding-bottom: 4px;
}
.party-meta-field textarea.meta-control {
    padding-top: 20px;
    padding-bottom: 4px;
}
.party-meta-field .meta-control,
.party-meta-field input,
.party-meta-field select,
.party-meta-field textarea {
    border: 1.5px solid #374151 !important;
    border-radius: 8px;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.party-meta-field .meta-control:focus,
.party-meta-field input:focus,
.party-meta-field select:focus,
.party-meta-field textarea:focus {
    border-color: #2563eb !important;
    box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
}
.party-meta-field.has-value .meta-control,
.party-meta-field.has-value input,
.party-meta-field.has-value select,
.party-meta-field.has-value textarea {
    border-color: #2563eb !important;
}
.warehouse-compact-grid .party-meta-field label {
    font-size: 10px;
    left: 7px;
}
.warehouse-compact-grid .party-meta-field:focus-within label,
.warehouse-compact-grid .party-meta-field.has-value label {
    font-size: 8px;
    top: -1px;
}
.warehouse-compact-grid .party-meta-field .meta-control,
.warehouse-compact-grid .party-meta-field input,
.warehouse-compact-grid .party-meta-field select {
    padding-top: 14px;
    padding-bottom: 2px;
    height: 44px;
    min-height: 44px;
    font-size: 12px;
}
.warehouse-compact-grid .warehouse-field label {
    top: -1px;
    transform: none;
    font-size: 8px;
    color: #2563eb;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.warehouse-compact-grid .party-meta-field:has(select) label {
    top: -1px !important;
    transform: none !important;
    font-size: 8px !important;
    color: #2563eb !important;
    font-weight: 600 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.05em !important;
}
.warehouse-compact-grid .dropdown-toggle,
.warehouse-dropdown-wrapper .btn.dropdown-toggle {
    border: 1.5px solid #374151 !important;
    border-radius: 8px !important;
    height: 44px !important;
    min-height: 44px !important;
    font-size: 12px;
    padding: 14px 10px 2px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    background: #fff;
    color: #374151;
    width: 100%;
    transition: border-color .15s, box-shadow .15s;
    padding-bottom: 6px;
}
.warehouse-compact-grid .dropdown-toggle:focus,
.warehouse-dropdown-wrapper .btn.dropdown-toggle:focus {
    box-shadow: 0 0 0 3px rgba(37,99,235,0.12) !important;
}

        /* ── Scrollable Dropdowns ── */
        #partyDropdownMenu, #brokerDropdownMenu, #warehouseDropdownMenu, #topbarWarehouseDropdownMenu {
            min-width: 250px; max-width: 100%; max-height: 350px;
            overflow-y: auto; overflow-x: hidden;
        }
        #partyDropdownMenu::-webkit-scrollbar,
        #brokerDropdownMenu::-webkit-scrollbar,
        #warehouseDropdownMenu::-webkit-scrollbar,
        #topbarWarehouseDropdownMenu::-webkit-scrollbar { width: 8px; }
        #partyDropdownMenu::-webkit-scrollbar-track,
        #brokerDropdownMenu::-webkit-scrollbar-track,
        #warehouseDropdownMenu::-webkit-scrollbar-track,
        #topbarWarehouseDropdownMenu::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 4px; }
        #partyDropdownMenu::-webkit-scrollbar-thumb,
        #brokerDropdownMenu::-webkit-scrollbar-thumb,
        #warehouseDropdownMenu::-webkit-scrollbar-thumb,
        #topbarWarehouseDropdownMenu::-webkit-scrollbar-thumb { background: #888; border-radius: 4px; }
        #partyDropdownMenu::-webkit-scrollbar-thumb:hover,
        #brokerDropdownMenu::-webkit-scrollbar-thumb:hover,
        #warehouseDropdownMenu::-webkit-scrollbar-thumb:hover,
        #topbarWarehouseDropdownMenu::-webkit-scrollbar-thumb:hover { background: #555; }
        #partyDropdownMenu, #brokerDropdownMenu, #warehouseDropdownMenu, #topbarWarehouseDropdownMenu {
            scrollbar-width: thin; scrollbar-color: #888 #f1f1f1;
        }

        /* ── Dropdown Option Layout ── */
        .party-option span, .warehouse-option span { display: inline-block; width: 100%; }
        .party-option span:first-child, .warehouse-option span:first-child { width: 65%; }
        .party-option span:last-child, .warehouse-option span:last-child { width: 35%; text-align: right; }
        .dropdown-header {
            font-weight: 600; font-size: 0.9rem; background: #f8f9fa;
            border-bottom: 1px solid #ddd; position: sticky; top: 0; z-index: 1020;
        }
        .dropdown-item.party-option:hover,
        .dropdown-item.warehouse-option:hover,
        .dropdown-item.broker-option:hover { background-color: #e2f0ff; }
        .party-dropdown-wrapper, .broker-dropdown-wrapper, .warehouse-dropdown-wrapper { width: 100%; }

        /* ── Layout ── */
        .header-section { display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 28px; align-items: start; }
        .header-left { display: grid; grid-template-columns: minmax(220px, 1.3fr) repeat(4, minmax(120px, 1fr)); gap: 12px; min-width: 0; }
        .party-selector-group { margin-top: 0 !important; }
        .party-selector-panel { background: transparent; border: none; border-radius: 0; padding: 0; box-shadow: none; }
        .party-dropdown-wrapper .btn.dropdown-toggle,
        .broker-dropdown-wrapper .btn.dropdown-toggle {
            width: 100%; min-height: 48px; border-radius: 8px; border-color: #cbd5e1;
            display: flex; align-items: center; justify-content: space-between;
            font-weight: 500; background: #fff;
        }
        #partyBalanceDisplay { margin-top: 6px !important; font-size: 15px; }
        .party-meta-field { display: flex; flex-direction: column; gap: 6px; }
        .party-meta-field .meta-control {
            width: 100%; min-height: 48px; padding: 12px 14px;
            border: 1.5px solid #374151 !important; border-radius: 10px;
            background: #fbfdff; color: #111827; resize: none; font-size: 15px;
        }
        .party-meta-field textarea.meta-control { min-height: 82px; }
        .party-meta-grid { display: contents; }
        .party-meta-field.address-field { order: 3; }
        .header-right.w-25 {
            width: 420px !important; min-width: 420px; justify-content: flex-end;
            background: #ffffff; border: 1px solid #dbe4f0; border-radius: 16px;
            padding: 18px 20px; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04);
        }
        .broker-cell .meta-control {
            width: 100%; min-height: 40px; padding: 9px 11px;
            border: 1.5px solid #374151 !important; border-radius: 8px;
            background: #fff; color: #111827; font-size: 13px;
        }
        .broker-cell select.meta-control,
        .broker-cell select.broker-select {
            border: 1.5px solid #374151 !important;
            border-radius: 8px;
            height: 38px;
            padding: 0 10px;
        }

        /* ── Warehouse Compact Grid ── */
        .warehouse-compact-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 10px; margin-top: 8px;
        }

        /* ── Bottom Right — Enhanced ── */
        .bottom-right {
            background: #fff;
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 18px 20px;
            box-shadow: 0 4px 20px rgba(15,23,42,0.07);
            min-width: 280px;
        }
        .bottom-right .calc-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 7px 0;
            border-bottom: 1px solid #f1f5f9;
            gap: 12px;
            margin-bottom: 0;
        }
        .bottom-right .calc-row:last-of-type { border-bottom: none; }
        .bottom-right .calc-label {
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            min-width: 130px;
            white-space: nowrap;
        }
        .bottom-right .calc-inputs {
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .bottom-right .mini-input {
            border: 1.5px solid #e2e8f0 !important;
            border-radius: 6px;
            padding: 5px 10px;
            font-size: 13px;
            color: #111827;
            background: #f8fafc;
            transition: border-color .15s, box-shadow .15s;
            outline: none;
            min-width: 0;
        }
        .bottom-right .mini-input:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.10);
            background: #fff;
        }
        .bottom-right select.mini-input { cursor: pointer; }
        .bottom-right .final-total-group {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px solid #e2e8f0;
        }
        .bottom-right .total-input-large {
            width: 100% !important;
            font-size: 22px !important;
            font-weight: 700;
            color: #2563eb;
            border: 2px solid #2563eb !important;
            border-radius: 10px;
            padding: 10px 16px !important;
            background: #eff6ff;
            text-align: right;
            letter-spacing: 0.02em;
        }
        .bottom-right .tax-amount-display {
            font-size: 13px;
            color: #64748b;
            min-width: 40px;
            text-align: right;
        }

        /* ── Description — always visible, no button needed ── */
        .description-pane {
            position: relative;
            width: 100%;
        }
        .description-pane .form-label {
            position: absolute;
            top: -1px;
            left: 10px;
            font-size: 9px !important;
            font-weight: 600;
            color: #2563eb;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            background: #fff;
            padding: 0 3px;
            z-index: 2;
            line-height: 1;
            pointer-events: none;
            margin: 0;
        }
        .description-pane .description-input {
            border: 1.5px solid #374151 !important;
            border-radius: 8px;
            padding-top: 20px !important;
            padding-left: 12px;
            font-size: 13px;
            min-height: 70px;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            width: 100%;
            resize: vertical;
        }
        .description-pane .description-input:focus {
            border-color: #2563eb !important;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .description-pane .description-input::placeholder { color: #94a3b8; font-size: 13px; }

        /* ── ADD IMAGE / ADD DOCUMENT ── */
        .btn-action-light {
            display: flex !important;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 1.5px solid #374151 !important;
            border-radius: 8px !important;
            background: #fff !important;
            color: #374151 !important;
            font-size: 12px;
            font-weight: 500;
            padding: 10px 14px !important;
            cursor: pointer;
            transition: background .15s, border-color .15s, color .15s;
            min-height: 44px;
        }
        .btn-action-light:hover {
            background: #eff6ff !important;
            border-color: #2563eb !important;
            color: #2563eb !important;
        }
        .btn-action-light i { font-size: 13px; }

        /* ── Image Gallery ── */
        .image-gallery { display: grid; grid-template-columns: repeat(auto-fill, minmax(90px,1fr)); gap: 10px; margin-top: 10px; }
        .image-card { border: 1px solid #dbe4f0; border-radius: 10px; overflow: hidden; background: #fff; }
        .image-card img { width: 100%; height: 80px; object-fit: cover; }
        .image-card-body { padding: 8px; }
        .image-card-name { font-size: 11px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .compression-status { font-size: 12px; color: #64748b; }

        /* ══════════════════════════════════════════════════
           TOP BAR — Store + Warehouse Dropdown + Add Party
        ══════════════════════════════════════════════════ */
        .vyapar-topbar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px 6px;
            background: #fff;
            border-bottom: 1px solid #e8edf3;
        }

        /* Add Party stays left */
        .btn-add-party-topbar {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #fff;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 12px;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: border-color .15s, color .15s, background .15s;
            white-space: nowrap;
        }
        .btn-add-party-topbar:hover,
        .btn-add-party-topbar.active {
            border-color: #2563eb;
            color: #2563eb;
            background: #eff6ff;
        }
        .btn-add-party-topbar i { font-size: 12px; }

        .topbar-spacer { flex: 1; }

        /* Right-side group */
        .topbar-right-group {
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* ── Topbar Warehouse Dropdown ── */
        .topbar-warehouse-wrapper {
            position: relative;
        }
        .btn-topbar-warehouse {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 12px;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: border-color .15s, color .15s, background .15s;
            white-space: nowrap;
            min-width: 150px;
            justify-content: space-between;
        }
        .btn-topbar-warehouse:hover,
        .btn-topbar-warehouse.active {
            border-color: #2563eb;
            color: #2563eb;
            background: #eff6ff;
        }
        /* Prevent Bootstrap from adding its own caret — we supply our own icon */
        .btn-topbar-warehouse::after { display: none !important; }
        .btn-topbar-warehouse.selected {
            border-color: #2563eb;
            color: #2563eb;
            background: #eff6ff;
        }
        #topbarWarehouseDropdownMenu {
            min-width: 280px;
            border-radius: 8px;
            border: 1px solid #dbe4f0;
            box-shadow: 0 8px 24px rgba(15,23,42,0.12);
            padding: 6px 0;
        }
        #topbarWarehouseDropdownMenu .topbar-wh-option:hover { background: #eff6ff; }
        #topbarWarehouseDropdownMenu .topbar-wh-option.active {
            background: #dbeafe;
            color: #2563eb;
            font-weight: 600;
        }
        #topbarWarehouseDropdownMenu .topbar-wh-add {
            color: #2563eb;
            font-weight: 500;
        }
        #topbarWarehouseDropdownMenu .topbar-wh-add:hover { background: #eff6ff; }

        /* Main Store Button */
        .btn-store {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 5px 12px 5px 9px;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: border-color .15s, box-shadow .15s;
            white-space: nowrap;
        }
        .btn-store:hover { border-color: #2563eb; color: #2563eb; }
        .btn-store .store-icon {
            width: 22px; height: 22px;
            background: #2563eb;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 11px;
            flex-shrink: 0;
        }
        .btn-store .store-label { font-size: 13px; }
        .btn-store .store-chevron { font-size: 10px; color: #94a3b8; margin-left: 2px; }

        /* ══════════════════════════════════════════════════
           ADD PARTY POPUP — Blue outlined like image 3
        ══════════════════════════════════════════════════ */
        .party-quick-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1060;
            background: #fff;
            border: 2px solid #2563eb;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(37,99,235,0.18), 0 4px 20px rgba(0,0,0,0.10);
            width: 560px;
            max-width: 96vw;
            padding: 0;
            overflow: hidden;
        }
        .party-quick-popup.open { display: block; }

        .party-popup-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 1055;
            background: rgba(15,23,42,0.35);
        }
        .party-popup-backdrop.open { display: block; }

        .party-popup-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 20px 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        .party-popup-header h5 {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
            margin: 0;
        }
        .party-popup-header .popup-close-btn {
            background: none;
            border: none;
            font-size: 18px;
            color: #64748b;
            cursor: pointer;
            line-height: 1;
            padding: 2px 5px;
            border-radius: 4px;
            transition: background .12s, color .12s;
        }
        .party-popup-header .popup-close-btn:hover { background: #f1f5f9; color: #111827; }
        .party-popup-header .popup-settings-btn {
            background: none; border: none;
            font-size: 17px; color: #94a3b8; cursor: pointer;
            padding: 2px 5px; border-radius: 4px;
            transition: background .12s, color .12s;
        }
        .party-popup-header .popup-settings-btn:hover { background: #f1f5f9; color: #374151; }

        .party-popup-body { padding: 18px 20px; }

        .party-popup-top-row {
            display: grid;
            grid-template-columns: 1.4fr 1fr 1fr;
            gap: 12px;
            margin-bottom: 14px;
        }

        .party-popup-tabs {
            display: flex;
            gap: 0;
            border-bottom: 2px solid #e2e8f0;
            margin-bottom: 16px;
        }
        .party-popup-tab {
            padding: 7px 16px;
            font-size: 13px;
            font-weight: 500;
            color: #64748b;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
            transition: color .12s, border-color .12s;
            background: none;
            border-top: none;
            border-left: none;
            border-right: none;
        }
        .party-popup-tab:hover { color: #2563eb; }
        .party-popup-tab.active { color: #2563eb; border-bottom-color: #2563eb; }

        .party-popup-tab-pane { display: none; }
        .party-popup-tab-pane.active { display: block; }

        .credit-balance-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .pp-field {
            position: relative;
            margin-bottom: 4px;
        }
        .pp-field > label { display: none; }
        .pp-field input,
        .pp-field select,
        .pp-field textarea {
            border: 1.5px solid #374151;
            border-radius: 6px;
            padding: 18px 10px 6px 10px;
            font-size: 13px;
            color: #111827;
            background: #fff;
            outline: none;
            transition: border-color .15s, box-shadow .15s;
            width: 100%;
            min-height: 46px;
        }
        .pp-field select { padding-top: 18px; padding-bottom: 6px; appearance: auto; }
        .pp-field textarea { padding-top: 22px; padding-bottom: 6px; resize: vertical; min-height: 72px; }
        .pp-field input:focus,
        .pp-field select:focus,
        .pp-field textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
        }
        .pp-field::before {
            content: attr(data-label);
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 13px;
            color: #94a3b8;
            pointer-events: none;
            transition: top .15s, font-size .15s, color .15s, transform .15s;
            z-index: 2;
            background: transparent;
            line-height: 1;
        }
        .pp-field.pp-textarea::before { top: 14px; transform: none; }
        .pp-field.floated::before,
        .pp-field.pp-textarea.floated::before {
            top: 7px;
            transform: none;
            font-size: 10px;
            color: #2563eb;
            font-weight: 600;
            letter-spacing: 0.03em;
        }
        .pp-field.pp-textarea.floated::before { top: 5px; }
        .pp-field.pp-select::before {
            top: 7px;
            transform: none;
            font-size: 10px;
            color: #2563eb;
            font-weight: 600;
        }

        .opening-balance-row {
            display: flex;
            align-items: center;
            gap: 0;
            border: 1.5px solid #374151;
            border-radius: 6px;
            overflow: hidden;
        }
        .opening-balance-row input {
            border: none !important;
            border-radius: 0 !important;
            flex: 1;
            box-shadow: none !important;
        }
        .opening-balance-row input:focus { box-shadow: none !important; }
        .opening-balance-type-select {
            border: none !important;
            border-left: 1.5px solid #374151 !important;
            border-radius: 0 !important;
            width: 100px !important;
            background: #f8faff !important;
            box-shadow: none !important;
            font-size: 12px !important;
        }

        .credit-limit-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 12px;
        }
        .credit-limit-label { font-size: 13px; font-weight: 500; color: #374151; }
        .toggle-switch {
            position: relative;
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            gap: 6px;
            font-size: 12px;
            color: #64748b;
        }
        .toggle-switch input[type="checkbox"] { display: none; }
        .toggle-track {
            width: 36px; height: 20px;
            background: #2563eb;
            border-radius: 20px;
            position: relative;
            transition: background .15s;
        }
        .toggle-track::after {
            content: '';
            position: absolute;
            top: 3px; left: 3px;
            width: 14px; height: 14px;
            background: #fff;
            border-radius: 50%;
            transition: left .15s;
        }
        .toggle-switch input:checked + .toggle-track { background: #2563eb; }
        .toggle-switch input:checked + .toggle-track::after { left: 19px; }
        .toggle-switch input:not(:checked) + .toggle-track { background: #94a3b8; }

        .party-popup-footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 10px;
            padding: 14px 20px;
            border-top: 1px solid #e2e8f0;
            background: #f8fafc;
        }
        .btn-popup-secondary {
            padding: 8px 20px;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            background: #fff;
            font-size: 13px;
            font-weight: 500;
            color: #374151;
            cursor: pointer;
            transition: border-color .12s, background .12s;
        }
        .btn-popup-secondary:hover { border-color: #94a3b8; background: #f1f5f9; }
        .btn-popup-primary {
            padding: 8px 24px;
            border: none;
            border-radius: 6px;
            background: #2563eb;
            font-size: 13px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: background .12s, box-shadow .12s;
        }
        .btn-popup-primary:hover { background: #1d4ed8; box-shadow: 0 3px 12px rgba(37,99,235,0.25); }
        .btn-popup-primary:disabled { background: #93c5fd; cursor: not-allowed; }

        /* ── Add Warehouse Popup ── */
        .warehouse-quick-popup {
            display: none;
            position: fixed;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1060;
            background: #fff;
            border: 2px solid #2563eb;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(37,99,235,0.18), 0 4px 20px rgba(0,0,0,0.10);
            width: 540px;
            max-width: 96vw;
            padding: 0;
            overflow: hidden;
        }
        .warehouse-quick-popup.open { display: block; }

        .party-autocomplete-list {
            position: absolute;
            z-index: 9999;
            background: #fff;
            border: 1px solid #cbd5e1;
            border-radius: 6px;
            width: 100%;
            max-height: 260px;
            overflow-y: auto;
            box-shadow: 0 4px 16px rgba(0,0,0,0.10);
            list-style: none;
            padding: 4px 0;
            margin: 0;
            display: none;
            top: 100%;
            left: 0;
        }

        @media (max-width: 991px) {
            .header-section { grid-template-columns: 1fr; }
            .header-left { grid-template-columns: 1fr; }
            .header-right.w-25 { width: 100% !important; min-width: 0; }
            .warehouse-compact-grid { grid-template-columns: 1fr; }
            .party-quick-popup, .warehouse-quick-popup { width: 96vw; }
            .party-popup-top-row { grid-template-columns: 1fr; }
            .credit-balance-grid { grid-template-columns: 1fr; }
        }
    </style>
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
        {{-- FIX 1: Replaced "Add Warehouse" button with Warehouse dropdown --}}
        <div class="vyapar-topbar">
            {{-- Add Party Button (left side) --}}
            <button type="button" class="btn-add-party-topbar" id="topbarAddPartyBtn">
                <i class="fa-solid fa-user-plus"></i>
                Add Party
            </button>

            <div class="topbar-spacer"></div>

            {{-- Right group: Warehouse DROPDOWN + Main Store --}}
            <div class="topbar-right-group">

                {{-- ══ WAREHOUSE DROPDOWN (replaces Add Warehouse button) ══ --}}
                <div class="topbar-warehouse-wrapper dropdown">
                    <button class="btn-topbar-warehouse" type="button" id="topbarWarehouseBtn"
                            data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                        <span style="display:flex;align-items:center;gap:6px;">
                            <i class="fa-solid fa-warehouse"></i>
                            <span id="topbarWarehouseLabel">Select Warehouse</span>
                        </span>
                        <i class="fa-solid fa-chevron-down" style="font-size:10px;color:#94a3b8;"></i>
                    </button>
                    <ul class="dropdown-menu" id="topbarWarehouseDropdownMenu" aria-labelledby="topbarWarehouseBtn">
                        {{-- populated by JS --}}
                    </ul>
                </div>

                <button type="button" class="btn-store" id="mainStoreBtn">
                    <span class="store-icon"><i class="fa-solid fa-store" style="font-size:11px;"></i></span>
                    <span class="store-label">Main Store</span>
                    <i class="fa-solid fa-chevron-down store-chevron"></i>
                </button>
            </div>
        </div>

        <main id="content-area">
            <template id="form-template">
                <div class="invoice-container">
                    <div class="invoice-form invoice-card">
                        <div class="header-section">
                            <div class="header-left">
                                <div class="input-group party-selector-group">
                                    <div class="party-selector-panel">
                                        <div class="party-dropdown-wrapper" style="position: relative; display: inline-block;">
                                            <input type="text" class="form-control party-search-input w-100"
                                                placeholder="Search by Name/Phone..."
                                                id="partyDropdownBtn"
                                                style="font-size: 13px; border: 1px solid #cbd5e1; border-radius: 6px; padding: 6px 8px; min-height: 34px;">
                                            <div id="partyBalanceDisplay" style="color: #007bff; font-weight: 600; margin-top: 4px;">No party selected</div>
                                        </div>
                                        <input type="hidden" class="party-id" name="party_id">
                                    </div>
                                </div>

                                <div class="party-meta-grid">
                                    <div class="party-meta-field"><label>Phone No.</label><input type="text" class="meta-control phone-input"></div>
                                    <div class="party-meta-field address-field"><label>Billing Address</label><textarea class="meta-control billing-address" rows="2"></textarea></div>
                                    {{-- FIX 5: Remarks always visible as textarea, no button toggle needed --}}
                                    <div class="party-meta-field address-field"><label>Remarks</label><textarea class="meta-control description-input" rows="2" placeholder="Description"></textarea></div>
                                </div>
                            </div>

                            <div class="header-right w-25">
                                <div class="input-group"><span>Challan No.</span><input type="text" class="input-control underline-input bill-number" value="{{ $nextInvoiceNumber ?? 'Auto' }}" readonly></div>
                                <div class="input-group date-wrapper mt-2"><span>Invoice Date</span><input type="date" class="input-control underline-input invoice-date"></div>
                                <div class="input-group date-wrapper mt-2"><span>Due Date</span><input type="date" class="input-control underline-input due-date"></div>
                            </div>
                        </div>

                        <div class="alert alert-success d-none sale-success-msg"></div>

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
                                        <th style="min-width: 220px;">BROKER NAME</th>
                                        <th style="min-width: 180px;">BROKER PHONE</th>
                                        <th>AMOUNT</th>
                                        <th class="add-col" style="position: relative;">
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
                                            {{-- FIX 4: item-name select — JS (challanform_script.js) must populate this.
                                                 Added data-items attribute as fallback seed so items render on load --}}
                                            <select class="form-select item-name">
                                                <option value="" selected disabled>Select Item</option>
                                                {{-- Options populated by challanform_script.js via window.items --}}
                                            </select>
                                        </td>
                                        <td class="col-category d-none"><select class="item-category"><option value="">Select Category</option></select></td>
                                        <td class="col-item-code d-none"><input type="text" class="item-code" placeholder="Item Code" readonly></td>
                                        <td class="col-description d-none"><input type="text" class="item-desc" placeholder="Description" readonly></td>
                                        <td class="col-discount d-none">
                                            <div class="item-discount-fields">
                                                <input type="number" class="item-discount-pct" value="" min="0" step="0.01" placeholder="%">
                                                <input type="number" class="item-discount" value="0" min="0" step="0.01" placeholder="Amount">
                                            </div>
                                        </td>
                                        <td><input type="number" class="item-qty" value="1"></td>
                                        <td class="custom-size-td">
                                            <select class="item-unit">
                                                <option value="">Select Unit</option>
                                                <option value="PCS">PCS (Pieces)</option>
                                                <option value="BOX">BOX</option>
                                                <option value="PACK">PACK</option>
                                                <option value="SET">SET</option>
                                                <option value="KG">KG (Kilogram)</option>
                                                <option value="G">Gram</option>
                                                <option value="M">Meter</option>
                                                <option value="FT">Feet</option>
                                                <option value="L">Liter</option>
                                                <option value="ML">Milliliter</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="item-price" value="0"></td>
                                        <td class="broker-cell">
                                            <select class="meta-control broker-select">
                                                <option value="">Select Broker</option>
                                            </select>
                                            <input type="hidden" class="broker-id" name="broker_id">
                                            <input type="hidden" class="broker-name-input" value="">
                                        </td>
                                        <td class="broker-cell"><input type="text" class="meta-control broker-phone-input"></td>
                                        {{-- FIX 3: Removed `readonly` so user can manually type amount --}}
                                        <td class="col-amount"><input type="number" class="item-amount" value="0"></td>
                                        <td class="add-col"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="table-footer">
                                <button type="button" class="btn-add-row add-row-btn">ADD ROW</button>
                                <div class="footer-totals">
                                    <div><span class="total-label">TOTAL QTY</span><span class="total-qty">0</span></div>
                                    <div><span class="total-label">TOTAL AMOUNT</span><span class="total-base-amount">0</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="bottom-section">
                            <div class="bottom-left">

                                {{-- FIX 5: Removed "ADD DESCRIPTION" toggle button. Description pane is always visible. --}}
                                <div class="description-pane mb-2" style="width:50%;">
                                    <label class="form-label">Description / Remarks</label>
                                    <textarea class="form-control description-input" rows="3" placeholder="Enter a remark or description"></textarea>
                                </div>

                                <div style="display:flex;gap:8px;width:100%;flex-wrap:wrap;">
                                    <button type="button" class="btn-action-light w-50 add-image" style="flex:1;">
                                        <i class="fa-solid fa-camera"></i> ADD IMAGE
                                    </button>
                                    <button type="button" class="btn-action-light w-50 add-document" style="flex:1;">
                                        <i class="fa-solid fa-file-lines"></i> ADD DOCUMENT
                                    </button>
                                </div>

                                {{-- FIX 2: Warehouse compact grid — auto-filled by topbar warehouse dropdown selection --}}
                                <div class="warehouse-compact-grid">
                                    <div class="party-meta-field warehouse-field">
                                        <label>Warehouse Name</label>
                                        <div class="warehouse-dropdown-wrapper" style="position: relative; display: inline-block; width: 100%;">
                                            <button class="btn dropdown-toggle w-100 text-start" type="button" id="warehouseDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">Select Warehouse</button>
                                            <ul class="dropdown-menu w-100" aria-labelledby="warehouseDropdownBtn" id="warehouseDropdownMenu">
                                                <li class="dropdown-header d-flex justify-content-between px-3"><span>Warehouse</span><span>Phone</span></li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-primary add-warehouse-option" href="#">+ Add New Warehouse</a></li>
                                            </ul>
                                        </div>
                                        <input type="hidden" class="warehouse-id" name="warehouse_id">
                                    </div>
                                    <div class="party-meta-field"><label>Warehouse Phone</label><input type="text" class="meta-control warehouse-phone-input"></div>
                                    <div class="party-meta-field warehouse-handler-field"><label>Handler Name</label><input type="text" class="meta-control warehouse-handler-input"></div>
                                    <div class="party-meta-field warehouse-handler-phone-field"><label>Handler Phone</label><input type="text" class="meta-control warehouse-handler-phone-input"></div>
                                    <div class="party-meta-field vehicle-field"><label>Vehicle / Car</label><input type="text" class="meta-control vehicle-number-input"></div>
                                    <div class="party-meta-field destination-field"><label>Destination</label><input type="text" class="meta-control destination-input"></div>
                                    <div class="party-meta-field handler-user-field">
                                        <label>Related Handler User</label>
                                        <select class="meta-control responsible-user-select">
                                            <option value="">Select related handler user</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="image-gallery"></div>
                                <div class="compression-status mt-2"></div>
                                <input type="file" class="d-none image-input" accept="image/*" multiple>
                            </div>

                            <div class="bottom-right">
                                <div class="calc-row">
                                    <div class="calc-label"><span class="editable-heading" data-label-key="discount">Discount</span></div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input discount-pct" placeholder="%" style="width:60px;">
                                        <span style="color:#94a3b8;">-</span>
                                        <input type="number" class="mini-input discount-rs" placeholder="Rs" style="width:80px;">
                                    </div>
                                </div>
                                <div class="calc-row">
                                    <div class="calc-label">
                                        <input type="checkbox" class="custom-checkbox round-off-check" checked style="width:16px;height:16px;margin-right:4px;vertical-align:middle;">
                                        <label class="link-text" style="margin:0;font-size:12px;vertical-align:middle;">Round Off</label>
                                    </div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input round-off-val" value="0" readonly style="width:60px;text-align:right;">
                                    </div>
                                </div>
                                <div class="calc-row">
                                    <div class="calc-label"><span class="editable-heading" data-label-key="delivery_expense">Delivery Expense</span></div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input delivery-expense" placeholder="Rs" min="0" step="0.01" style="width:100px;">
                                    </div>
                                </div>
                                <div class="calc-row">
                                    <div class="calc-label"><span class="editable-heading" data-label-key="brokerage_type">Brokerage Type</span></div>
                                    <div class="calc-inputs">
                                        <select class="mini-input brokerage-type" style="width:140px;">
                                            <option value="">Select</option>
                                            <option value="full">Poori Brokerage</option>
                                            <option value="half">Aadhi Brokerage</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="calc-row">
                                    <div class="calc-label"><span class="editable-heading" data-label-key="brokerage_amount">Brokerage Amount</span></div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input brokerage-amount" placeholder="Rs" min="0" step="0.01" style="width:100px;">
                                    </div>
                                </div>
                                <div class="calc-row">
                                    <div class="calc-label"><span class="editable-heading" data-label-key="tax">Tax</span></div>
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
                                <input type="hidden" name="delivery_expense_label" class="heading-value" data-field="delivery_expense" value="Delivery Expense">
                                <input type="hidden" name="brokerage_type_label" class="heading-value" data-field="brokerage_type" value="Brokerage Type">
                                <input type="hidden" name="brokerage_amount_label" class="heading-value" data-field="brokerage_amount" value="Brokerage Amount">
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
                <button type="button" class="popup-settings-btn" title="Settings"><i class="fa-solid fa-gear"></i></button>
                <button type="button" class="popup-close-btn" id="partyPopupCloseBtn" aria-label="Close">&times;</button>
            </div>
        </div>
        <div class="party-popup-body">
            <div class="party-popup-top-row">
                <div class="pp-field" data-label="Party Name *">
                    <label>Party Name</label>
                    <input type="text" id="pp_name" autocomplete="off">
                </div>
                <div class="pp-field" data-label="Phone Number">
                    <label>Phone Number</label>
                    <input type="text" id="pp_phone" autocomplete="off">
                </div>
                <div class="pp-field pp-select" data-label="Party Group">
                    <label>Party Group</label>
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
                <div class="pp-field pp-textarea" data-label="Billing Address">
                    <label>Billing Address</label>
                    <textarea id="pp_billing_address" rows="3"></textarea>
                </div>
                <div class="pp-field pp-textarea mt-3" data-label="Shipping Address">
                    <label>Shipping Address</label>
                    <textarea id="pp_shipping_address" rows="2"></textarea>
                </div>
            </div>

            <div class="party-popup-tab-pane" data-pane="credit">
                <div class="credit-balance-grid">
                    <div class="pp-field floated" data-label="Opening Balance">
                        <label>Opening Balance</label>
                        <div class="opening-balance-row">
                            <input type="number" id="pp_opening_balance" min="0" step="0.01">
                            <select class="opening-balance-type-select" id="pp_transaction_type">
                                <option value="">Type</option>
                                <option value="receive">To Receive</option>
                                <option value="pay">To Pay</option>
                            </select>
                        </div>
                    </div>
                    <div class="pp-field floated" data-label="As Of Date">
                        <label>As Of Date</label>
                        <input type="date" id="pp_as_of_date">
                    </div>
                </div>
                <div class="credit-limit-row">
                    <span class="credit-limit-label">Credit Limit</span>
                    <label class="toggle-switch">
                        <input type="checkbox" id="pp_no_limit" checked>
                        <span class="toggle-track"></span>
                        <span>No Limit</span>
                    </label>
                    <span style="color:#94a3b8;font-size:12px;">Custom Limit</span>
                </div>
                <div class="pp-field mt-3 d-none" id="pp_custom_limit_field" data-label="Credit Limit Amount">
                    <label>Credit Limit Amount</label>
                    <input type="number" id="pp_credit_limit" min="0" step="0.01">
                </div>
            </div>

            <div class="party-popup-tab-pane" data-pane="additional">
                <div class="pp-field" data-label="GST / Tax Number">
                    <label>GST / Tax Number</label>
                    <input type="text" id="pp_gst">
                </div>
                <div class="pp-field mt-3" data-label="Email">
                    <label>Email</label>
                    <input type="email" id="pp_email">
                </div>
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
                <div class="col-md-6">
                    <div class="pp-field" data-label="Warehouse Name *"><label>Warehouse Name</label><input type="text" id="wh_name"></div>
                </div>
                <div class="col-md-6">
                    <div class="pp-field" data-label="Warehouse Phone"><label>Phone</label><input type="text" id="wh_phone"></div>
                </div>
                <div class="col-md-6">
                    <div class="pp-field" data-label="Handler Name"><label>Handler Name</label><input type="text" id="wh_handler_name"></div>
                </div>
                <div class="col-md-6">
                    <div class="pp-field" data-label="Handler Phone"><label>Handler Phone</label><input type="text" id="wh_handler_phone"></div>
                </div>
                <div class="col-md-6">
                    <div class="pp-field pp-select" data-label="Related User">
                        <label>Related User</label>
                        <select id="wh_user_id">
                            <option value=""></option>
                            @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}{{ $user->email ? ' (' . $user->email . ')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="pp-field pp-textarea" data-label="Address"><label>Address</label><textarea id="wh_address" rows="2"></textarea></div>
                </div>
            </div>
        </div>
        <div class="party-popup-footer">
            <button type="button" class="btn-popup-secondary" id="warehousePopupCancelBtn">Cancel</button>
            <button type="button" class="btn-popup-primary" id="warehousePopupSaveBtn">Save Warehouse</button>
        </div>
    </div>

    {{-- Bootstrap Warehouse Modal (kept for backward compat) --}}
    <div class="modal fade" id="warehouseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add New Warehouse</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <form id="warehouseForm">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Warehouse Name</label><input type="text" name="name" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Warehouse Phone</label><input type="text" name="phone" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Handler Name</label><input type="text" name="handler_name" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Handler Phone</label><input type="text" name="handler_phone" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Related User</label><select name="responsible_user_id" class="form-select"><option value="">Select related handler user</option>
@foreach($users as $user)
<option value="{{ $user->id }}">{{ $user->name }}</option>
@endforeach
</select></div>
                            <div class="col-md-6"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary save-warehouse-btn">Save Warehouse</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tabLimitModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content bg-dark text-dark border-secondary"><div class="modal-body text-center p-4"><i class="bi bi-exclamation-triangle text-warning display-4 mb-3"></i><h5>Maximum Limit Reached</h5><p>You can open a maximum of 10 transactions at a time.</p><button type="button" class="btn btn-primary px-4 mt-2" data-bs-dismiss="modal">OK</button></div></div></div></div>
    <div class="modal fade" id="closeConfirmModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content bg-dark text-dark border-secondary"><div class="modal-header border-secondary"><h5 class="modal-title">Close Tab?</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p>Are you sure you want to close this tab? Your delivery challan will not be saved.</p></div><div class="modal-footer border-secondary"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" id="confirm-close-btn" class="btn btn-danger">Close</button></div></div></div></div>

    <div class="modal fade" id="editHeadingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change field label</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="headingNameInput" class="form-label">Field name</label>
                        <input type="text" id="headingNameInput" class="form-control" placeholder="Enter new label">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary save-heading-btn">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @php
        $challanPayload = isset($challan) ? $challan->load(['items', 'party', 'broker', 'challanDetail'])->toArray() : null;
        $duplicatePayload = isset($duplicateChallan) ? array_merge($duplicateChallan->load(['items', 'party', 'broker', 'challanDetail'])->toArray(), ['bill_number' => $nextInvoiceNumber]) : null;
    @endphp

    <script>
        window.items          = @json($items ?? []);
        window.parties        = @json($parties ?? []);
        window.brokers        = @json($brokers ?? []);
        window.bankAccounts   = @json($bankAccounts ?? []);
        window.bankAccountRoutes  = { store: "{{ route('bank-accounts.store') }}" };
        window.transactionSettings = { countEnabled: @json(\App\Models\AppSetting::getValue('transaction_items_count_enabled', '0') === '1'), countLabel: 'Count' };
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
        window.saleStoreUrl  = "{{ isset($challan) ? route('delivery-challan.update', $challan->id) : route('delivery-challan.store') }}";
        window.saleHttpMethod = "{{ isset($challan) ? 'PUT' : 'POST' }}";
        window.challanId     = @json($challan->id ?? null);
        window.editSaleData  = @json($challanPayload ?? $duplicatePayload);
        window.docType       = 'delivery_challan';
    </script>

    {{-- ══ Heading Labels (editable) ══ --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modalEl      = document.getElementById('editHeadingModal');
            const headingInput = document.getElementById('headingNameInput');
            const saveButton   = modalEl?.querySelector('.save-heading-btn');
            const editModal    = modalEl ? new bootstrap.Modal(modalEl) : null;
            const headingStorageKey = 'deliveryChallanHeadingLabels';
            let currentHeadingKey = '';

            const defaultLabels = {
                discount: 'Discount',
                delivery_expense: 'Delivery Expense',
                brokerage_type: 'Brokerage Type',
                brokerage_amount: 'Brokerage Amount',
                tax: 'Tax',
                total: 'Total'
            };

            const templateEl = document.getElementById('form-template');

            const applyHeadingLabelsToRoot = (root) => {
                if (!root) return;
                Object.entries(getSavedHeadingLabels()).forEach(([key, label]) => {
                    root.querySelectorAll(`.editable-heading[data-label-key="${key}"]`).forEach(el => el.textContent = label);
                    root.querySelectorAll(`.heading-value[data-field="${key}"]`).forEach(el => el.value = label);
                });
            };

            const applyHeadingLabelsToTemplate = () => {
                if (!templateEl) return;
                applyHeadingLabelsToRoot(templateEl.content || templateEl);
            };

            const getSavedHeadingLabels = () => {
                let labels = { ...defaultLabels };
                try {
                    const stored = localStorage.getItem(headingStorageKey);
                    if (stored) {
                        const parsed = JSON.parse(stored);
                        if (parsed && typeof parsed === 'object') labels = { ...labels, ...parsed };
                    }
                } catch (e) { console.warn('Unable to load heading labels:', e); }
                return labels;
            };

            const loadHeadingLabels  = () => { applyHeadingLabelsToRoot(document); applyHeadingLabelsToTemplate(); };
            const saveHeadingLabels  = () => {
                const labels = {};
                document.querySelectorAll('.editable-heading').forEach(el => { if (el.dataset.labelKey) labels[el.dataset.labelKey] = el.textContent.trim(); });
                try { localStorage.setItem(headingStorageKey, JSON.stringify(labels)); } catch (e) {}
            };

            loadHeadingLabels();

            document.body.addEventListener('click', function (e) {
                const target = e.target.closest('.editable-heading');
                if (!target) return;
                e.preventDefault();
                currentHeadingKey = target.dataset.labelKey || '';
                if (!headingInput) return;
                headingInput.value = target.textContent.trim();
                editModal?.show();
            });

            saveButton?.addEventListener('click', function () {
                const newLabel = headingInput?.value.trim();
                if (!newLabel) return;
                document.querySelectorAll(`.editable-heading[data-label-key="${currentHeadingKey}"]`).forEach(el => el.textContent = newLabel);
                document.querySelectorAll(`.heading-value[data-field="${currentHeadingKey}"]`).forEach(el => el.value = newLabel);
                saveHeadingLabels();
                applyHeadingLabelsToTemplate();
                editModal?.hide();
            });

            headingInput?.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') { e.preventDefault(); saveButton?.click(); }
            });
        });
    </script>

    {{-- ══ FIX 4: Item select population — ensure window.items populates ALL item-name selects ══ --}}
    <script>
    (function() {
        function populateItemSelects(root) {
            root = root || document;
            var items = window.items || [];
            root.querySelectorAll('select.item-name').forEach(function(sel) {
                // Only repopulate if just has the placeholder or is empty
                var currentVal = sel.value;
                // Remove existing dynamic options (keep placeholder)
                while (sel.options.length > 1) sel.remove(1);
                items.forEach(function(item) {
                    var opt = document.createElement('option');
                    opt.value = item.id || '';
                    opt.textContent = item.name || item.item_name || '';
                    // Store price for auto-fill
                    opt.dataset.price   = item.sale_price || item.price || 0;
                    opt.dataset.unit    = item.unit || '';
                    opt.dataset.code    = item.item_code || item.code || '';
                    opt.dataset.desc    = item.description || '';
                    sel.appendChild(opt);
                });
                // Restore value if it was set
                if (currentVal) sel.value = currentVal;
            });
        }

        // Run immediately when items are available, and again on DOMContentLoaded
        document.addEventListener('DOMContentLoaded', function() {
            populateItemSelects();
            // Also rerun when forms/tabs are added
            document.addEventListener('challan:formAdded', function() { populateItemSelects(); });
            document.addEventListener('challan:rowAdded', function(e) {
                var root = e.detail && e.detail.row ? e.detail.row.closest('table') || document : document;
                populateItemSelects(root);
            });
            // MutationObserver for dynamically added selects
            var obs = new MutationObserver(function(mutations) {
                mutations.forEach(function(m) {
                    m.addedNodes.forEach(function(node) {
                        if (node.nodeType === 1) {
                            if (node.classList && node.classList.contains('item-name')) {
                                populateItemSelects(node.parentElement || document);
                            } else if (node.querySelectorAll) {
                                var sels = node.querySelectorAll('select.item-name');
                                if (sels.length) populateItemSelects(node);
                            }
                        }
                    });
                });
            });
            obs.observe(document.body, { childList: true, subtree: true });

            // Auto-fill price/unit when item is selected
            document.addEventListener('change', function(e) {
                if (!e.target.classList.contains('item-name')) return;
                var sel = e.target;
                var row = sel.closest('tr');
                if (!row) return;
                var opt = sel.options[sel.selectedIndex];
                if (!opt || !opt.value) return;
                // Fill price
                var priceInput = row.querySelector('.item-price');
                if (priceInput && opt.dataset.price) priceInput.value = parseFloat(opt.dataset.price) || 0;
                // Fill unit
                var unitSel = row.querySelector('.item-unit');
                if (unitSel && opt.dataset.unit) unitSel.value = opt.dataset.unit;
                // Fill item code
                var codeInput = row.querySelector('.item-code');
                if (codeInput) codeInput.value = opt.dataset.code || '';
                // Fill description
                var descInput = row.querySelector('.item-desc');
                if (descInput) descInput.value = opt.dataset.desc || '';
                // Trigger amount recalculation
                var qtyInput = row.querySelector('.item-qty');
                var qty = parseFloat(qtyInput ? qtyInput.value : 1) || 1;
                var price = parseFloat(priceInput ? priceInput.value : 0) || 0;
                var amountInput = row.querySelector('.item-amount');
                if (amountInput) amountInput.value = (qty * price).toFixed(2);
                // Fire input event so the main script recalculates totals
                if (priceInput) priceInput.dispatchEvent(new Event('input', { bubbles: true }));
            });
        });
    })();
    </script>

    {{-- ══ Broker dropdown population ══ --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {

        /* has-value tracking */
        function trackHasValue(root) {
            root = root || document;
            root.querySelectorAll('.party-meta-field input, .party-meta-field textarea').forEach(function(el) {
                function update() {
                    var field = el.closest('.party-meta-field');
                    if (!field) return;
                    field.classList.toggle('has-value', !!(el.value && el.value.trim() !== ''));
                }
                el.addEventListener('input', update);
                el.addEventListener('change', update);
                el.addEventListener('blur', update);
                update();
            });
            root.querySelectorAll('.party-meta-field select').forEach(function(el) {
                function update() {
                    var field = el.closest('.party-meta-field');
                    if (!field) return;
                    field.classList.toggle('has-value', !!(el.value && el.value !== ''));
                }
                el.addEventListener('change', update);
                el.addEventListener('blur', update);
                update();
            });
        }
        trackHasValue(document);
        document.addEventListener('challan:formAdded', function() { trackHasValue(document); });
        var hasValueObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(m) {
                m.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) trackHasValue(node);
                });
            });
        });
        hasValueObserver.observe(document.body, { childList: true, subtree: true });

        // Populate broker selects
        function populateBrokerSelects(root) {
            root = root || document;
            root.querySelectorAll('select.broker-select').forEach(function(sel) {
                while (sel.options.length > 1) sel.remove(1);
                (window.brokers || []).forEach(function(b) {
                    const opt = document.createElement('option');
                    opt.value = b.id || '';
                    opt.textContent = b.name || '';
                    opt.dataset.phone = b.phone || '';
                    sel.appendChild(opt);
                });
            });
        }
        populateBrokerSelects();
        document.addEventListener('challan:formAdded', function() { populateBrokerSelects(); });
        document.addEventListener('challan:rowAdded', function(e) {
            if (e.detail && e.detail.row) populateBrokerSelects(e.detail.row.closest('table') || document);
        });
        document.addEventListener('change', function(e) {
            if (!e.target.classList.contains('broker-select')) return;
            const sel = e.target;
            const row = sel.closest('tr');
            if (!row) return;
            const selectedOpt = sel.options[sel.selectedIndex];
            const phone = selectedOpt ? (selectedOpt.dataset.phone || '') : '';
            const phoneInput = row.querySelector('.broker-phone-input');
            if (phoneInput) phoneInput.value = phone;
            const brokerId = row.querySelector('.broker-id');
            if (brokerId) brokerId.value = sel.value;
            const brokerName = row.querySelector('.broker-name-input');
            if (brokerName) brokerName.value = selectedOpt ? (selectedOpt.textContent || '') : '';
        });
        const brokerObserver = new MutationObserver(function(mutations) {
            mutations.forEach(function(m) {
                m.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) {
                        const selects = node.querySelectorAll ? node.querySelectorAll('select.broker-select') : [];
                        if (node.classList && node.classList.contains('broker-select')) {
                            populateBrokerSelects(node.parentElement || document);
                        } else if (selects.length) {
                            populateBrokerSelects(node);
                        }
                    }
                });
            });
        });
        brokerObserver.observe(document.body, { childList: true, subtree: true });
    });
    </script>

    {{-- ══ Add Party Popup + Warehouse Logic ══ --}}
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

        function showToast(msg, isError = false) {
            const t = document.getElementById('sale-toast');
            if (!t) return alert(msg);
            t.querySelector('.toast-body').textContent = msg;
            t.classList.toggle('text-bg-success', !isError);
            t.classList.toggle('text-bg-danger', isError);
            new bootstrap.Toast(t, { delay: 4000 }).show();
        }

        /* ── Floating Labels ── */
        function initFloatingLabels(root) {
            root = root || document;
            root.querySelectorAll('.pp-field input, .pp-field textarea').forEach(function(el) {
                function update() {
                    const field = el.closest('.pp-field');
                    if (!field) return;
                    field.classList.toggle('floated', !!(el.value || document.activeElement === el));
                }
                el.addEventListener('focus', update);
                el.addEventListener('blur', update);
                el.addEventListener('input', update);
                update();
            });
            root.querySelectorAll('.pp-field.pp-select select').forEach(function(el) {
                const field = el.closest('.pp-field');
                if (field) field.classList.add('floated');
            });
            root.querySelectorAll('.pp-field input[type="date"]').forEach(function(el) {
                const field = el.closest('.pp-field');
                if (field) field.classList.add('floated');
            });
        }
        initFloatingLabels(document.getElementById('partyQuickPopup'));
        initFloatingLabels(document.getElementById('warehouseQuickPopup'));

        /* ══════════════════════════════════════════════════
           FIX 1 + 2: TOPBAR WAREHOUSE DROPDOWN
           Builds dropdown, handles selection, auto-fills form fields
        ══════════════════════════════════════════════════ */
        function buildTopbarWarehouseDropdown() {
            const menu = document.getElementById('topbarWarehouseDropdownMenu');
            if (!menu) return;
            menu.innerHTML = '';

            const whs = window.warehouses || [];
            if (whs.length === 0) {
                menu.innerHTML = '<li><span class="dropdown-item-text text-muted" style="font-size:12px;">No warehouses yet</span></li>';
            } else {
                whs.forEach(function(wh) {
                    const li = document.createElement('li');
                    const a = document.createElement('a');
                    a.className = 'dropdown-item topbar-wh-option d-flex justify-content-between align-items-center';
                    a.href = '#';
                    a.dataset.id          = wh.id || '';
                    a.dataset.name        = wh.name || '';
                    a.dataset.phone       = wh.phone || '';
                    a.dataset.handlerName = wh.handler_name || '';
                    a.dataset.handlerPhone= wh.handler_phone || '';
                    a.dataset.userId      = wh.responsible_user_id || '';
                    a.innerHTML = `<span style="font-weight:500;">${wh.name||''}</span><span style="font-size:11px;color:#94a3b8;">${wh.phone||''}</span>`;
                    li.appendChild(a);
                    menu.appendChild(li);
                });
                menu.appendChild(document.createElement('li')).outerHTML; // divider
                const divLi = document.createElement('li');
                divLi.innerHTML = '<hr class="dropdown-divider">';
                menu.appendChild(divLi);
            }

            // "Add New Warehouse" option
            const addLi = document.createElement('li');
            const addA = document.createElement('a');
            addA.className = 'dropdown-item topbar-wh-add';
            addA.href = '#';
            addA.innerHTML = '<i class="fa-solid fa-plus me-1"></i> Add New Warehouse';
            addLi.appendChild(addA);
            menu.appendChild(addLi);
        }

        // Single delegated click handler on the menu — no duplicates
        document.addEventListener('click', function(e) {
            // Warehouse option selected
            const opt = e.target.closest('#topbarWarehouseDropdownMenu .topbar-wh-option');
            if (opt) {
                e.preventDefault();
                const id          = opt.dataset.id;
                const name        = opt.dataset.name;
                const phone       = opt.dataset.phone;
                const handlerName = opt.dataset.handlerName;
                const handlerPhone= opt.dataset.handlerPhone;
                const userId      = opt.dataset.userId;
                // Update button label
                document.getElementById('topbarWarehouseLabel').textContent = name || 'Select Warehouse';
                document.getElementById('topbarWarehouseBtn').classList.add('selected');
                // Mark active
                document.querySelectorAll('#topbarWarehouseDropdownMenu .topbar-wh-option').forEach(o => o.classList.remove('active'));
                opt.classList.add('active');
                // Auto-fill form fields
                fillWarehouseFields(id, name, phone, handlerName, handlerPhone, userId);
                return;
            }
            // Add New Warehouse clicked
            const addLink = e.target.closest('#topbarWarehouseDropdownMenu .topbar-wh-add');
            if (addLink) {
                e.preventDefault();
                resetWarehouseForm();
                openWarehousePopup();
                return;
            }
        });

        function fillWarehouseFields(id, name, phone, handlerName, handlerPhone, userId) {
            // Fill the warehouse-id hidden input
            document.querySelectorAll('.warehouse-id').forEach(function(el) { el.value = id || ''; });
            // Fill warehouse phone
            document.querySelectorAll('.warehouse-phone-input').forEach(function(el) { el.value = phone || ''; triggerHasValue(el); });
            // Fill handler name
            document.querySelectorAll('.warehouse-handler-input').forEach(function(el) { el.value = handlerName || ''; triggerHasValue(el); });
            // Fill handler phone
            document.querySelectorAll('.warehouse-handler-phone-input').forEach(function(el) { el.value = handlerPhone || ''; triggerHasValue(el); });
            // Fill responsible user select
            document.querySelectorAll('.responsible-user-select').forEach(function(sel) {
                if (userId) sel.value = userId;
                triggerHasValue(sel);
            });
            // Update the in-form warehouse dropdown button text
            document.querySelectorAll('#warehouseDropdownBtn').forEach(function(btn) {
                btn.textContent = name || 'Select Warehouse';
            });
        }

        function triggerHasValue(el) {
            const field = el.closest('.party-meta-field');
            if (!field) return;
            const hasVal = el.tagName === 'SELECT'
                ? !!(el.value && el.value !== '')
                : !!(el.value && el.value.trim() !== '');
            field.classList.toggle('has-value', hasVal);
        }

        buildTopbarWarehouseDropdown();
        document.addEventListener('challan:formAdded', buildTopbarWarehouseDropdown);

        /* ── In-form warehouse dropdown (bottom-left grid) ── */
        function rebuildWarehouseDropdowns() {
            const whs = window.warehouses || [];
            document.querySelectorAll('#warehouseDropdownMenu').forEach(function(menu) {
                menu.querySelectorAll('li').forEach(function(li) {
                    if (li.querySelector('.warehouse-option')) li.remove();
                });
                const divider = menu.querySelector('.dropdown-divider');
                const dividerLi = divider ? divider.parentElement : null;
                whs.forEach(function(wh) {
                    const li = document.createElement('li');
                    li.innerHTML = `<a class="dropdown-item d-flex justify-content-between warehouse-option" href="#"
                        data-id="${wh.id||''}" data-name="${(wh.name||'').replace(/"/g,'&quot;')}"
                        data-phone="${wh.phone||''}" data-handler-name="${wh.handler_name||''}"
                        data-handler-phone="${wh.handler_phone||''}" data-user-id="${wh.responsible_user_id||''}">
                        <span>${wh.name||''}</span>
                        <span style="color:#64748b;font-size:12px;">${wh.phone||'-'}</span>
                    </a>`;
                    if (dividerLi) menu.insertBefore(li, dividerLi);
                    else menu.appendChild(li);
                });
            });
        }
        rebuildWarehouseDropdowns();
        document.addEventListener('challan:formAdded', rebuildWarehouseDropdowns);

        // In-form warehouse option click — also auto-fills
        document.addEventListener('click', function(e) {
            const opt = e.target.closest('.warehouse-option');
            if (!opt) return;
            e.preventDefault();
            fillWarehouseFields(
                opt.dataset.id, opt.dataset.name, opt.dataset.phone,
                opt.dataset.handlerName, opt.dataset.handlerPhone, opt.dataset.userId
            );
            // Update topbar label too
            document.getElementById('topbarWarehouseLabel').textContent = opt.dataset.name || 'Select Warehouse';
            document.getElementById('topbarWarehouseBtn').classList.add('selected');
        });

        // Fix select labels in warehouse grid
        function fixWarehouseSelectLabels() {
            document.querySelectorAll('.warehouse-compact-grid .party-meta-field:has(select)').forEach(function(field) {
                field.classList.add('has-value');
            });
        }
        fixWarehouseSelectLabels();
        document.addEventListener('challan:formAdded', fixWarehouseSelectLabels);
        new MutationObserver(fixWarehouseSelectLabels).observe(document.body, { childList: true, subtree: true });

        /* ══ ADD PARTY POPUP ══ */
        const partyPopup    = document.getElementById('partyQuickPopup');
        const partyBackdrop = document.getElementById('partyPopupBackdrop');
        const addPartyBtn   = document.getElementById('topbarAddPartyBtn');
        const closePartyBtn = document.getElementById('partyPopupCloseBtn');

        function openPartyPopup() {
            partyPopup.classList.add('open');
            partyBackdrop.classList.add('open');
            addPartyBtn.classList.add('active');
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('pp_as_of_date').value = today;
            initFloatingLabels(partyPopup);
            setTimeout(() => document.getElementById('pp_name').focus(), 80);
        }
        function closePartyPopup() {
            partyPopup.classList.remove('open');
            partyBackdrop.classList.remove('open');
            addPartyBtn.classList.remove('active');
        }
        function resetPartyForm() {
            ['pp_name','pp_phone','pp_billing_address','pp_shipping_address',
             'pp_opening_balance','pp_gst','pp_email','pp_credit_limit'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) { el.value = ''; el.closest('.pp-field')?.classList.remove('floated'); }
            });
            document.getElementById('pp_group').value = '';
            document.getElementById('pp_transaction_type').value = '';
            document.getElementById('pp_no_limit').checked = true;
            document.getElementById('pp_custom_limit_field').classList.add('d-none');
            document.querySelectorAll('.party-popup-tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.party-popup-tab-pane').forEach(p => p.classList.remove('active'));
            document.querySelector('.party-popup-tab[data-tab="address"]').classList.add('active');
            document.querySelector('.party-popup-tab-pane[data-pane="address"]').classList.add('active');
        }

        addPartyBtn?.addEventListener('click', function () { resetPartyForm(); openPartyPopup(); });
        closePartyBtn?.addEventListener('click', closePartyPopup);
        partyBackdrop?.addEventListener('click', function() { closePartyPopup(); closeWarehousePopup(); });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') { closePartyPopup(); closeWarehousePopup(); }
        });

        document.querySelectorAll('.party-popup-tab').forEach(function(tab) {
            tab.addEventListener('click', function () {
                document.querySelectorAll('.party-popup-tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.party-popup-tab-pane').forEach(p => p.classList.remove('active'));
                this.classList.add('active');
                document.querySelector(`.party-popup-tab-pane[data-pane="${this.dataset.tab}"]`).classList.add('active');
                initFloatingLabels(partyPopup);
            });
        });

        document.getElementById('pp_no_limit')?.addEventListener('change', function () {
            document.getElementById('pp_custom_limit_field').classList.toggle('d-none', this.checked);
        });

        document.addEventListener('click', function (e) {
            if (e.target && (e.target.id === 'addNewPartyBtn' || e.target.closest('#addNewPartyBtn'))) {
                e.preventDefault();
                resetPartyForm();
                openPartyPopup();
            }
        });

        async function saveParty(keepOpen) {
            const nameEl = document.getElementById('pp_name');
            const name = nameEl.value.trim();
            if (!name) { nameEl.style.borderColor = '#e53e3e'; nameEl.focus(); showToast('Party Name is required.', true); return; }
            nameEl.style.borderColor = '';
            const saveBtn    = document.getElementById('partyPopupSaveBtn');
            const saveNewBtn = document.getElementById('partyPopupSaveNewBtn');
            const activeBtn  = keepOpen ? saveNewBtn : saveBtn;
            const origText   = activeBtn.textContent;
            saveBtn.disabled = saveNewBtn.disabled = true;
            activeBtn.textContent = 'Saving...';
            try {
                const payload = new FormData();
                payload.append('name',             name);
                payload.append('phone',            document.getElementById('pp_phone').value.trim());
                payload.append('party_group_id',   document.getElementById('pp_group').value);
                payload.append('billing_address',  document.getElementById('pp_billing_address').value.trim());
                payload.append('shipping_address', document.getElementById('pp_shipping_address').value.trim());
                payload.append('opening_balance',  document.getElementById('pp_opening_balance').value || 0);
                payload.append('transaction_type', document.getElementById('pp_transaction_type').value);
                payload.append('as_of_date',       document.getElementById('pp_as_of_date').value);
                payload.append('gst_number',       document.getElementById('pp_gst').value.trim());
                payload.append('email',            document.getElementById('pp_email').value.trim());
                if (!document.getElementById('pp_no_limit').checked) {
                    payload.append('credit_limit', document.getElementById('pp_credit_limit').value || '');
                }
                const res = await fetch(window.partyStoreUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: payload
                });
                const contentType = res.headers.get('content-type') || '';
                if (!contentType.includes('application/json')) throw new Error('Server returned non-JSON response. Status: ' + res.status);
                const data = await res.json();
                if (!res.ok) {
                    if (data.errors) { const fe = Object.values(data.errors)[0]; throw new Error(Array.isArray(fe) ? fe[0] : fe); }
                    throw new Error(data.message || 'Failed to save party (status ' + res.status + ')');
                }
                const newParty = data.party || data.data || (data.id ? data : null);
                if (!newParty) throw new Error(data.message || 'Party saved but no data returned');
                window.parties = window.parties || [];
                window.parties.unshift(newParty);
                showToast('Party "' + (newParty.name || name) + '" added successfully.');
                if (keepOpen) {
                    resetPartyForm(); initFloatingLabels(partyPopup); document.getElementById('pp_name').focus();
                } else {
                    closePartyPopup();
                    const $ctx = $(document.querySelector('#content-area .invoice-container') || document);
                    if ($ctx.length && newParty.id) {
                        $ctx.find('.party-id').val(newParty.id);
                        $ctx.find('.party-search-input').val(newParty.name || '');
                        $ctx.find('.phone-input').val(newParty.phone || '');
                        $ctx.find('.billing-address').val(newParty.billing_address || '');
                        const amount = Number(newParty.opening_balance || 0).toFixed(2);
                        $ctx.find('#partyBalanceDisplay').html(`<span class="text-primary">Rs ${amount}</span>`);
                    }
                }
            } catch (err) {
                showToast(err.message || 'Error saving party. Please try again.', true);
                console.error('Party save error:', err);
            } finally {
                saveBtn.disabled = saveNewBtn.disabled = false;
                activeBtn.textContent = origText;
            }
        }

        document.getElementById('partyPopupSaveBtn')?.addEventListener('click', () => saveParty(false));
        document.getElementById('partyPopupSaveNewBtn')?.addEventListener('click', () => saveParty(true));

        /* ══ ADD WAREHOUSE POPUP ══ */
        const whPopup     = document.getElementById('warehouseQuickPopup');
        const closeWhBtn  = document.getElementById('warehousePopupCloseBtn');
        const cancelWhBtn = document.getElementById('warehousePopupCancelBtn');

        function openWarehousePopup() {
            whPopup.classList.add('open');
            partyBackdrop.classList.add('open');
            initFloatingLabels(whPopup);
            setTimeout(() => document.getElementById('wh_name').focus(), 80);
        }
        function closeWarehousePopup() {
            whPopup.classList.remove('open');
            partyBackdrop.classList.remove('open');
        }
        function resetWarehouseForm() {
            ['wh_name','wh_phone','wh_handler_name','wh_handler_phone','wh_address'].forEach(function(id) {
                const el = document.getElementById(id);
                if (el) { el.value = ''; el.closest('.pp-field')?.classList.remove('floated'); }
            });
            document.getElementById('wh_user_id').value = '';
        }

        closeWhBtn?.addEventListener('click', closeWarehousePopup);
        cancelWhBtn?.addEventListener('click', closeWarehousePopup);

        // In-form "Add New Warehouse" link — opens popup only (no Bootstrap modal)
        document.addEventListener('click', function (e) {
            const link = e.target.closest('.add-warehouse-option');
            if (!link) return;
            e.preventDefault();
            e.stopImmediatePropagation();
            const dropdownBtn = document.getElementById('warehouseDropdownBtn');
            if (dropdownBtn) { const bsD = bootstrap.Dropdown.getInstance(dropdownBtn); bsD && bsD.hide(); }
            resetWarehouseForm();
            openWarehousePopup();
        }, true);

        document.getElementById('warehousePopupSaveBtn')?.addEventListener('click', async function () {
            const nameEl = document.getElementById('wh_name');
            const name = nameEl.value.trim();
            if (!name) { nameEl.style.borderColor = '#e53e3e'; nameEl.focus(); showToast('Warehouse Name is required.', true); return; }
            nameEl.style.borderColor = '';
            const btn = this;
            btn.disabled = true; btn.textContent = 'Saving...';
            try {
                const payload = new FormData();
                payload.append('name',                name);
                payload.append('phone',               document.getElementById('wh_phone').value.trim());
                payload.append('handler_name',        document.getElementById('wh_handler_name').value.trim());
                payload.append('handler_phone',       document.getElementById('wh_handler_phone').value.trim());
                payload.append('responsible_user_id', document.getElementById('wh_user_id').value);
                payload.append('address',             document.getElementById('wh_address').value.trim());
                const res = await fetch(window.warehouseStoreUrl, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: payload
                });
                const data = await res.json();
                if (!res.ok) {
                    if (data.errors) { const fe = Object.values(data.errors)[0]; throw new Error(Array.isArray(fe) ? fe[0] : fe); }
                    throw new Error(data.message || 'Failed to save warehouse');
                }
                const wh = data.warehouse || data.data || (data.id ? data : null);
                if (!wh) throw new Error('Warehouse saved but no data returned');
                window.warehouses = window.warehouses || [];
                window.warehouses.push(wh);
                rebuildWarehouseDropdowns();
                buildTopbarWarehouseDropdown();

                // Auto-select the newly created warehouse
                fillWarehouseFields(wh.id, wh.name, wh.phone, wh.handler_name, wh.handler_phone, wh.responsible_user_id);
                document.getElementById('topbarWarehouseLabel').textContent = wh.name || 'Select Warehouse';
                document.getElementById('topbarWarehouseBtn').classList.add('selected');

                showToast('Warehouse "' + (wh.name || name) + '" added successfully.');
                closeWarehousePopup();
            } catch (err) {
                showToast(err.message || 'Error saving warehouse', true);
                console.error('Warehouse save error:', err);
            } finally {
                btn.disabled = false; btn.textContent = 'Save Warehouse';
            }
        });

        document.getElementById('mainStoreBtn')?.addEventListener('click', function () {
            showToast('Main Store selected.');
        });
    });
    </script>

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="sale-toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex"><div class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div>
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
    document.addEventListener('DOMContentLoaded', function () {
        const closeIcon = document.querySelector('.close-app-icon');
        if (!closeIcon) return;
        closeIcon.addEventListener('click', function () {
            if (window.history.length > 1) window.history.back();
            else window.location.href = '/dashboard/sales';
        });
    });
    </script>
</body>
</html>