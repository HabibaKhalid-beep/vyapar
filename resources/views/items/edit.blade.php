@extends('layouts.app')

@section('title', 'Items')
@section('page', 'items')

@push('styles')
<style>
* { box-sizing: border-box; }

.vy-page {
    background: #fff;
    height: 100vh; max-height: 100vh;
    display: flex; flex-direction: column; overflow: hidden;
}

/* ── Header ── */
.vy-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 28px; border-bottom: 1px solid #e5e7eb;
    flex-shrink: 0; background: #fff;
}
.vy-header-left { display: flex; align-items: center; gap: 20px; }
.vy-title { font-size: 20px; font-weight: 700; color: #1a1a1a; }
.vy-toggle-wrap { display: flex; align-items: center; gap: 10px; }
.vy-toggle-lbl { font-size: 14px; font-weight: 500; }
.vy-header-right { display: flex; align-items: center; gap: 4px; }
.vy-icon-btn {
    background: none; border: none; padding: 8px; cursor: pointer;
    color: #9ca3af; border-radius: 4px; line-height: 1; transition: background .12s;
}
.vy-icon-btn:hover { background: #f3f4f6; }

/* Toggle switch — always blue */
.vy-toggle { position: relative; display: inline-block; width: 46px; height: 26px; }
.vy-toggle input { opacity: 0; width: 0; height: 0; }
.vy-slider {
    position: absolute; cursor: pointer; inset: 0;
    background: #2563eb; border-radius: 26px; transition: .2s;
}
.vy-slider:before {
    content: ""; position: absolute;
    width: 20px; height: 20px; left: 3px; bottom: 3px;
    background: white; border-radius: 50%; transition: .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.3);
}
input:checked + .vy-slider { background: #2563eb; }
input:checked + .vy-slider:before { transform: translateX(20px); }

/* ── Fields Row ── */
.vy-fields {
    display: flex; align-items: center; gap: 14px;
    padding: 20px 28px 0; flex-shrink: 0; flex-wrap: wrap;
}

/* Item Name — floating label */
.vy-name-wrap { position: relative; width: 230px; flex-shrink: 0; }
.vy-name-wrap .floating-label {
    position: absolute; top: -9px; left: 10px;
    background: #fff; padding: 0 4px;
    font-size: 11px; color: #2563eb; font-weight: 500;
    pointer-events: none; z-index: 1; white-space: nowrap;
}
.vy-name-input {
    width: 100%; border: 1.5px solid #2563eb; border-radius: 4px;
    padding: 13px 14px; font-size: 14px; color: #374151;
    background: #fff; outline: none; transition: border-color .15s, box-shadow .15s;
}
.vy-name-input:focus { box-shadow: 0 0 0 3px rgba(37,99,235,.10); }

/* Category */
.vy-cat-wrap { position: relative; width: 185px; flex-shrink: 0; }
.vy-cat-btn {
    display: flex; align-items: center; justify-content: space-between;
    border: 1.5px solid #d1d5db; border-radius: 4px;
    padding: 13px 12px; font-size: 14px; background: #fff;
    cursor: pointer; width: 100%; outline: none;
    transition: border-color .15s; color: #9ca3af;
}
.vy-cat-btn:hover { border-color: #93c5fd; }
.vy-cat-dd {
    position: absolute; top: calc(100% + 4px); left: 0;
    min-width: 190px; z-index: 9999;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 6px; box-shadow: 0 6px 20px rgba(0,0,0,.12);
    display: none;
}
.vy-cat-dd.open { display: block; }
.cat-add {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 14px; cursor: pointer;
    font-size: 13px; color: #2563eb; font-weight: 600;
    border-bottom: 1px solid #f3f4f6;
}
.cat-add:hover { background: #f9fafb; }
.cat-row {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 14px; cursor: pointer; font-size: 13px; color: #374151;
}
.cat-row:hover { background: #f9fafb; }
.cat-cb {
    width: 16px; height: 16px; flex-shrink: 0;
    border: 1.5px solid #d1d5db; border-radius: 3px;
    display: flex; align-items: center; justify-content: center;
}
.cat-cb.on { background: #2563eb; border-color: #2563eb; }

/* Unit button */
.vy-unit-btn {
    background: #dbeafe; color: #2563eb;
    border: 1.5px solid #93c5fd; border-radius: 4px;
    padding: 13px 22px; font-size: 14px; font-weight: 600;
    white-space: nowrap; cursor: pointer; flex-shrink: 0;
    transition: background .15s;
}
.vy-unit-btn:hover { background: #bfdbfe; }
.vy-unit-btn.chosen { background: #ede9fe; color: #6d28d9; border-color: #c4b5fd; }

/* Image area */
.vy-img-area {
    display: flex; align-items: center; gap: 10px;
    cursor: pointer; flex-shrink: 0;
    color: #6b7280; font-size: 14px; white-space: nowrap; margin-left: 18px;
}
.vy-img-icon {
    width: 36px; height: 36px; flex-shrink: 0;
    border: 1.5px solid #d1d5db; border-radius: 4px;
    background: #f9fafb; display: flex; align-items: center;
    justify-content: center; overflow: hidden;
}

/* Code row */
.vy-code-row { padding: 14px 28px 0; flex-shrink: 0; }
.vy-code-wrap {
    display: inline-flex; align-items: center;
    border: 1.5px solid #d1d5db; border-radius: 4px;
    overflow: hidden; width: 300px; background: #fff;
}
.vy-code-wrap input {
    flex: 1; border: none; outline: none;
    padding: 11px 14px; font-size: 14px; color: #374151;
    background: transparent; min-width: 0;
}
.vy-code-wrap input::placeholder { color: #9ca3af; }
.vy-assign-btn {
    flex-shrink: 0; border: none; background: #dbeafe;
    padding: 7px 14px; margin: 4px 5px 4px 0;
    font-size: 12px; font-weight: 600; color: #2563eb;
    cursor: pointer; white-space: nowrap; border-radius: 20px;
    transition: background .12s;
}
.vy-assign-btn:hover { background: #bfdbfe; }

/* Tabs */
.vy-tabs {
    display: flex; gap: 28px; padding: 0 28px; margin-top: 14px;
    border-bottom: 1px solid #e5e7eb; flex-shrink: 0;
}
.vy-tab {
    padding: 12px 0; font-size: 15px; font-weight: 500;
    cursor: pointer; border: none; border-bottom: 2px solid transparent;
    background: none; color: #9ca3af; margin-bottom: -1px; transition: all .15s;
}
.vy-tab.active { color: #e53e3e; border-bottom-color: #e53e3e; }
.vy-tab:hover:not(.active) { color: #4b5563; }

/* Body */
.vy-body { flex: 1; overflow-y: auto; min-height: 0; }
.vy-body::-webkit-scrollbar { width: 5px; }
.vy-body::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

/* Price sections */
.vy-price-sec {
    margin: 16px 28px; background: #f8f9fb; border-radius: 6px;
    padding: 18px 20px 20px; border: 1px solid #ebebeb;
}
.vy-price-title { font-size: 15px; font-weight: 600; color: #1f2937; margin-bottom: 14px; }
.vy-price-row { display: flex; align-items: flex-end; gap: 14px; flex-wrap: wrap; }
.vy-price-group { display: flex; flex-direction: column; gap: 4px; }
.vy-price-lbl { font-size: 12px; color: #6b7280; font-weight: 500; }
.vy-price-input-wrap {
    display: flex; align-items: center;
    border: 1.5px solid #d1d5db; border-radius: 4px;
    background: #fff; overflow: hidden; width: 220px;
    transition: border-color .15s;
}
.vy-price-input-wrap:focus-within { border-color: #2563eb; }
.vy-price-prefix {
    padding: 11px 10px 11px 14px; font-size: 14px; color: #374151; font-weight: 500;
    border-right: 1px solid #e5e7eb; background: #f9fafb; flex-shrink: 0;
}
.vy-price-input {
    border: none; outline: none; flex: 1;
    padding: 11px 14px; font-size: 14px; color: #374151; background: transparent;
}
.vy-price-input::placeholder { color: #9ca3af; }
.vy-ws-link {
    display: inline-flex; align-items: center; gap: 6px;
    color: #2563eb; font-size: 14px; font-weight: 500;
    cursor: pointer; background: none; border: none; padding: 0; margin-top: 12px;
}
.vy-ws-link:hover { color: #1d4ed8; }

/* Stock inputs */
.vy-stock-input {
    border: 1.5px solid #d1d5db; border-radius: 4px;
    padding: 11px 14px; font-size: 14px; color: #374151;
    background: #fff; outline: none; width: 220px; transition: border-color .15s;
}
.vy-stock-input:focus { border-color: #2563eb; }
.vy-stock-input::placeholder { color: #9ca3af; }

/* Footer */
.vy-footer {
    display: flex; align-items: center; justify-content: flex-end;
    gap: 12px; padding: 16px 28px;
    border-top: 1px solid #e5e7eb; background: #fff; flex-shrink: 0;
}
.vy-btn-delete {
    background: #fff; border: 1.5px solid #ef4444; border-radius: 4px;
    padding: 11px 24px; font-size: 14px; color: #ef4444; font-weight: 600;
    cursor: pointer; margin-right: auto; transition: background .12s;
}
.vy-btn-delete:hover { background: #fef2f2; }
.vy-btn-update {
    background: #2563eb; border: none; border-radius: 4px;
    padding: 11px 32px; font-size: 14px; font-weight: 700;
    color: #fff; cursor: pointer; transition: background .15s;
}
.vy-btn-update:hover { background: #1d4ed8; }

/* Discard dialog */
#discard-overlay {
    position: fixed; inset: 0; z-index: 3000;
    background: rgba(0,0,0,.35);
    display: none; align-items: center; justify-content: center;
}
#discard-overlay.open { display: flex; }
#discard-modal {
    background: #fff; border-radius: 6px;
    box-shadow: 0 8px 32px rgba(0,0,0,.2);
    width: 360px; max-width: 95vw;
    padding: 24px 28px 20px; animation: popIn .15s ease-out;
}
#discard-modal h4 { font-size: 14px; font-weight: 700; color: #1f2937; margin-bottom: 10px; }
#discard-modal p { font-size: 13px; color: #6b7280; margin-bottom: 22px; line-height: 1.5; }
.discard-footer { display: flex; justify-content: flex-end; gap: 10px; }
.discard-cancel {
    background: #fff; border: 1.5px solid #d1d5db; border-radius: 4px;
    padding: 9px 22px; font-size: 13px; color: #6b7280; cursor: pointer;
}
.discard-ok {
    background: #2563eb; border: none; border-radius: 4px;
    padding: 9px 22px; font-size: 13px; font-weight: 600; color: #fff; cursor: pointer;
}
.discard-ok:hover { background: #1d4ed8; }

/* Delete confirm modal */
#delete-overlay {
    position: fixed; inset: 0; z-index: 3000;
    background: rgba(0,0,0,.4);
    display: none; align-items: center; justify-content: center;
}
#delete-overlay.open { display: flex; }
#delete-modal {
    background: #fff; border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0,0,0,.25);
    width: 420px; max-width: 95vw; animation: popIn .15s ease-out;
}
@keyframes popIn {
    from { opacity:0; transform:scale(.96); }
    to   { opacity:1; transform:scale(1); }
}
.del-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 20px 14px; background: #e8f0fb; border-radius: 8px 8px 0 0;
}
.del-header-title { font-size: 15px; font-weight: 700; color: #1a2a4a; }
.del-header-close {
    background: none; border: none; cursor: pointer; font-size: 18px;
    color: #6b7280; width: 24px; height: 24px;
    display: flex; align-items: center; justify-content: center; border-radius: 4px;
}
.del-header-close:hover { background: #d1d5db; color: #111; }
.del-body { padding: 22px 24px 20px; }
.del-body p { font-size: 14px; font-weight: 600; color: #1a2a4a; }
.del-footer { display: flex; justify-content: flex-end; gap: 12px; padding: 14px 20px 18px; }
.del-btn {
    background: #5b9bd5; border: none; border-radius: 5px;
    padding: 9px 28px; font-size: 14px; font-weight: 600;
    color: #fff; cursor: pointer;
}
.del-btn:hover { background: #3a7bbf; }

/* Unit modal */
#unit-overlay {
    position: fixed; inset: 0; z-index: 2000;
    background: rgba(0,0,0,.45); display: none;
    align-items: center; justify-content: center;
}
#unit-overlay.open { display: flex; }
#unit-modal {
    background: #fff; border-radius: 6px;
    box-shadow: 0 10px 40px rgba(0,0,0,.22);
    width: 500px; max-width: 95vw; animation: popIn .15s ease-out;
}
.unit-hdr {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 26px; background: #e8f4fd;
    border-bottom: 1px solid #bfdbfe; border-radius: 6px 6px 0 0;
    font-size: 16px; font-weight: 600; color: #1e3a8a;
}
.unit-lbl {
    font-size: 11px; font-weight: 700; color: #2563eb;
    text-transform: uppercase; letter-spacing: .08em; margin-bottom: 8px;
}
.unit-sel {
    width: 100%; padding: 11px 30px 11px 14px;
    border: 1.5px solid #2563eb; border-radius: 4px;
    font-size: 14px; color: #374151; background: #fff;
    appearance: none; outline: none; cursor: pointer;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='11' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 12px center;
}
.unit-sel.sec { border-color: #d1d5db; }

/* Toast */
#toast {
    position: fixed; bottom: 28px; left: 50%;
    transform: translateX(-50%) translateY(20px);
    background: #111827; color: #fff; padding: 10px 22px;
    border-radius: 8px; font-size: 13px; font-weight: 500;
    opacity: 0; transition: all .25s; z-index: 9999; pointer-events: none;
}
#toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>
@endpush

@section('content')

<div class="vy-page">

    {{-- ── Header ── --}}
    <div class="vy-header">
        <div class="vy-header-left">
            <span class="vy-title">Edit Item</span>
            <div class="vy-toggle-wrap">
                <span id="lbl-product" class="vy-toggle-lbl" style="color:#2563eb;font-weight:600;">Product</span>
                <label class="vy-toggle" style="margin:0;">
                    <input type="checkbox" id="type-toggle" onchange="handleTypeToggle()">
                    <span class="vy-slider"></span>
                </label>
                <span id="lbl-service" class="vy-toggle-lbl" style="color:#9ca3af;">Service</span>
            </div>
        </div>
        <div class="vy-header-right">
            <button class="vy-icon-btn" title="Settings">
                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
            <button class="vy-icon-btn" title="Close" onclick="handleClose()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- ── Fields Row ── --}}
    <div class="vy-fields">

        <div class="vy-name-wrap">
            <span class="floating-label" id="name-floating-label">Item Name *</span>
            <input type="text" id="item-name" class="vy-name-input"
                   value="{{ $item->name ?? '' }}" oninput="markDirty()"/>
        </div>

        <div class="vy-cat-wrap" id="cat-wrapper">
            <button type="button" class="vy-cat-btn" onclick="toggleCatDD()">
              <span id="cat-label">{{ $item->category->name ?? 'Category' }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div id="cat-dropdown" class="vy-cat-dd">
                <div id="cat-add-btn" class="cat-add" onclick="event.stopPropagation();showCatInput()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/></svg>
                    Add New Category
                </div>
                <div id="cat-input-row" style="display:none;padding:10px 12px;border-bottom:1px solid #f3f4f6;">
                    <div style="display:flex;gap:6px;">
                        <input type="text" id="new-cat-text" placeholder="Category name"
                               style="flex:1;border:1px solid #d1d5db;border-radius:4px;padding:8px 10px;font-size:13px;outline:none;"/>
                        <button onclick="saveCat()" style="background:#2563eb;color:#fff;border:none;border-radius:4px;padding:8px 12px;font-size:12px;font-weight:600;cursor:pointer;">Add</button>
                    </div>
                </div>
                <div id="cat-list" style="max-height:180px;overflow-y:auto;"></div>
            </div>
        </div>

        <div style="position:relative;flex-shrink:0;">
            <button type="button" id="unit-trigger-btn" class="vy-unit-btn {{ $item->unit ? 'chosen' : '' }}" onclick="openUnitModal()">
                {{ $item->unit ? 'Edit Unit' : 'Select Unit' }}
            </button>
            <span id="unit-conv-label" style="font-size:12px;color:#6b7280;position:absolute;top:100%;margin-top:4px;left:50%;transform:translateX(-50%);white-space:nowrap;"></span>
        </div>

        <div class="vy-img-area" onclick="document.getElementById('img-file').click()">
            <div class="vy-img-icon" id="img-thumb">
                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                    <circle cx="12" cy="13" r="4"/>
                </svg>
            </div>
            <span>Add Item Image</span>
            <input type="file" id="img-file" accept="image/*" style="display:none;" onchange="previewImg(event);markDirty()"/>
        </div>
    </div>

    {{-- ── Item Code ── --}}
    <div class="vy-code-row">
        <div class="vy-code-wrap">
            <input type="text" id="item-code"
                   placeholder="Item Code"
                   value="{{ $item->item_code ?? '' }}"
                   oninput="markDirty()"/>
            <button type="button" class="vy-assign-btn" onclick="assignCode()">Assign Code</button>
        </div>
    </div>

    {{-- ── Tabs ── --}}
    <div class="vy-tabs">
        <button type="button" id="tab-pricing" class="vy-tab active" onclick="switchTab('pricing')">Pricing</button>
        <button type="button" id="tab-stock"   class="vy-tab"        onclick="switchTab('stock')">Stock</button>
    </div>

    {{-- ── Body ── --}}
    <div class="vy-body">

        {{-- PRICING --}}
        <div id="pane-pricing">

            <div class="vy-price-sec">
                <div class="vy-price-title">Sale Price</div>
                <div class="vy-price-row">
                    <div class="vy-price-group">
                        <span class="vy-price-lbl">Sale Price</span>
                        <div class="vy-price-input-wrap">
                            <span class="vy-price-prefix">Rs</span>
                            <input type="number" id="sale-price" class="vy-price-input"
                                   placeholder="0" min="0" step="0.01"
                                   value="{{ $item->sale_price ?? '' }}" oninput="markDirty()"/>
                        </div>
                    </div>
                </div>
                <button type="button" class="vy-ws-link" onclick="toggleWholesale()">
                    <span id="ws-icon" style="font-size:17px;line-height:1;font-weight:700;">+</span>
                    <span id="ws-label">Add Wholesale Price</span>
                </button>
                <div id="wholesale-row" style="display:none;margin-top:12px;">
                    <div class="vy-price-row">
                        <div class="vy-price-group">
                            <span class="vy-price-lbl">Wholesale Price</span>
                            <div class="vy-price-input-wrap">
                                <span class="vy-price-prefix">Rs</span>
                                <input type="number" id="wholesale-price" class="vy-price-input"
                                       placeholder="0" min="0" step="0.01"
                                       value="{{ $item->wholesale_price ?? '' }}" oninput="markDirty()"/>
                            </div>
                        </div>
                        <div class="vy-price-group">
                            <span class="vy-price-lbl">Minimum Wholesale Qty</span>
                            <div style="display:flex;align-items:center;gap:8px;">
                                <div class="vy-price-input-wrap" style="width:140px;">
                                    <input type="number" id="min-ws-qty" class="vy-price-input"
                                           placeholder="0" min="0"
                                           value="{{ $item->min_wholesale_qty ?? '' }}" oninput="markDirty()"/>
                                </div>
                                <span style="font-size:12px;color:#9ca3af;" id="ws-unit-label">{{ $item->unit ?? '' }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="vy-price-sec" id="purchase-sec">
                <div class="vy-price-title">Purchase Price</div>
                <div class="vy-price-row">
                    <div class="vy-price-group">
                        <span class="vy-price-lbl">Purchase Price</span>
                        <div class="vy-price-input-wrap">
                            <span class="vy-price-prefix">Rs</span>
                            <input type="number" id="purchase-price" class="vy-price-input"
                                   placeholder="0" min="0" step="0.01"
                                   value="{{ $item->purchase_price ?? '' }}" oninput="markDirty()"/>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- STOCK --}}
        <div id="pane-stock" style="display:none;padding:20px 28px;">
            <div style="display:flex;flex-wrap:wrap;gap:14px;">
                <div class="vy-price-group">
                    <span class="vy-price-lbl">Opening Quantity</span>
                    <input type="number" id="opening-qty" class="vy-stock-input"
                           placeholder="Opening Quantity" min="0"
                           value="{{ $item->opening_qty ?? '' }}" oninput="markDirty()"/>
                </div>
                <div class="vy-price-group">
                    <span class="vy-price-lbl">At Price</span>
                    <div class="vy-price-input-wrap">
                        <span class="vy-price-prefix">Rs</span>
                        <input type="number" id="at-price" class="vy-price-input"
                               placeholder="0" min="0" step="0.01"
                               value="{{ $item->at_price ?? '' }}" oninput="markDirty()"/>
                    </div>
                </div>
                <div class="vy-price-group">
                    <span class="vy-price-lbl">As Of Date</span>
                    <input type="date" id="as-of-date" class="vy-stock-input"
                           value="{{ $item->as_of_date ?? '' }}" oninput="markDirty()"/>
                </div>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:14px;margin-top:14px;">
                <div class="vy-price-group">
                    <span class="vy-price-lbl">Min Stock To Maintain</span>
                    <input type="text" id="min-stock" class="vy-stock-input"
                           placeholder="Min Stock To Maintain"
                           value="{{ $item->min_stock ?? '' }}" oninput="markDirty()"/>
                </div>
                <div class="vy-price-group">
                    <span class="vy-price-lbl">Location</span>
                    <input type="text" id="location" class="vy-stock-input"
                           placeholder="Location"
                           value="{{ $item->location ?? '' }}" oninput="markDirty()"/>
                </div>
            </div>
        </div>

    </div>

    {{-- ── Footer ── --}}
    <div class="vy-footer">
        <button type="button" class="vy-btn-delete" onclick="openDeleteModal()">Delete</button>
        <button type="button" class="vy-btn-update" onclick="updateItem()">Update</button>
    </div>

</div>

{{-- Toast --}}
<div id="toast"></div>

{{-- Discard dialog --}}
<div id="discard-overlay">
    <div id="discard-modal" onclick="event.stopPropagation()">
        <h4>Vyapar</h4>
        <p>Current changes will be discarded. Do you want to continue?</p>
        <div class="discard-footer">
            <button class="discard-cancel" onclick="closeDiscardModal()">Cancel</button>
            <button class="discard-ok" onclick="confirmDiscard()">OK</button>
        </div>
    </div>
</div>

{{-- Delete confirm --}}
<div id="delete-overlay">
    <div id="delete-modal" onclick="event.stopPropagation()">
        <div class="del-header">
            <span class="del-header-title">Are you sure you want to delete this Item?</span>
            <button class="del-header-close" onclick="closeDeleteModal()">✕</button>
        </div>
        <div class="del-body"><p>This Item will be permanently deleted.</p></div>
        <div class="del-footer">
            <button class="del-btn" onclick="confirmDelete()">YES</button>
            <button class="del-btn" onclick="closeDeleteModal()">NO</button>
        </div>
    </div>
</div>

{{-- Unit modal --}}
<div id="unit-overlay" onclick="if(event.target.id==='unit-overlay')closeUnitModal()">
    <div id="unit-modal" onclick="event.stopPropagation()">
        <div class="unit-hdr">
            <span>Select Unit</span>
            <button onclick="closeUnitModal()" style="background:none;border:none;cursor:pointer;color:#6b7280;padding:4px;line-height:1;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div style="padding:24px 28px;">
            <div style="display:flex;gap:22px;">
                <div style="flex:1;">
                    <div class="unit-lbl">Base Unit</div>
                    <select id="base-unit" class="unit-sel" onchange="onUnitChange()">
                        <option value="">None</option>
                        <option>BAGS (Bag)</option><option>BOTTLES (Btl)</option>
                        <option>BOX (Box)</option><option>BUNDLES (Bdl)</option>
                        <option>CANS (Can)</option><option>CARTONS (Ctn)</option>
                        <option>DOZENS (Dzn)</option><option>GRAMMES (Gm)</option>
                        <option>KILOGRAMS (Kg)</option><option>LITRE (Ltr)</option>
                        <option>METERS (Mtr)</option><option>MILILITRE (Ml)</option>
                        <option>NUMBERS (Nos)</option><option>PACKS (Pac)</option>
                    </select>
                </div>
                <div style="flex:1;">
                    <div class="unit-lbl">Secondary Unit</div>
                    <select id="secondary-unit" class="unit-sel sec" onchange="onUnitChange()">
                        <option value="">None</option>
                        <option>BAGS (Bag)</option><option>BOTTLES (Btl)</option>
                        <option>BOX (Box)</option><option>BUNDLES (Bdl)</option>
                        <option>CANS (Can)</option><option>CARTONS (Ctn)</option>
                        <option>DOZENS (Dzn)</option><option>GRAMMES (Gm)</option>
                        <option>KILOGRAMS (Kg)</option><option>LITRE (Ltr)</option>
                        <option>METERS (Mtr)</option><option>MILILITRE (Ml)</option>
                        <option>NUMBERS (Nos)</option><option>PACKS (Pac)</option>
                    </select>
                </div>
            </div>
        </div>
        <div id="conversion-row" style="display:none;padding:0 28px 16px;">
            <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:12px;">Conversion Rates</div>
            <div style="display:flex;align-items:center;gap:10px;">
                <span style="font-size:13px;color:#374151;">1</span>
                <span id="conv-base-lbl" style="font-size:13px;color:#374151;font-weight:500;"></span>
                <span style="font-size:13px;color:#374151;">=</span>
                <input type="number" id="conv-rate" value="1" min="0"
                       style="width:90px;border:1.5px solid #d1d5db;border-radius:4px;padding:8px 10px;font-size:14px;outline:none;text-align:center;"/>
                <span id="conv-sec-lbl" style="font-size:13px;color:#374151;font-weight:500;"></span>
            </div>
        </div>
        <div id="same-unit-error" style="display:none;margin:0 28px 12px;background:#e0f0ff;border:1px solid #93c5fd;border-radius:6px;padding:12px 16px;align-items:center;gap:10px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
            <span style="font-size:13px;color:#1e40af;">Base and secondary unit cannot be the same.</span>
        </div>
        <div style="display:flex;justify-content:flex-end;padding:14px 28px;border-top:1px solid #e5e7eb;">
            <button onclick="saveUnit()" style="background:#2563eb;color:#fff;border:none;border-radius:4px;padding:11px 32px;font-size:14px;font-weight:700;cursor:pointer;">SAVE</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const ITEM_ID   = {{ $item->id ?? 'null' }};
const ITEM_TYPE = '{{ $item->type ?? "product" }}';
const CSRF      = document.querySelector('meta[name="csrf-token"]')?.content;

let cats = [], selCats = [], baseUnit = '{{ $item->unit ?? "" }}', secUnit = '';
let wsOpen = false, isDirty = false, iType = ITEM_TYPE;
let discardTarget = null;

/* ── Init ── */
document.addEventListener('DOMContentLoaded', () => {
    loadCats();

    // Set type toggle
    if (iType === 'service') {
        document.getElementById('type-toggle').checked = true;
        applyTypeUI(true);
    }

    // Category label colour
    const catLbl = document.getElementById('cat-label');
    if (catLbl.textContent.trim() && catLbl.textContent.trim() !== 'Category') {
        catLbl.style.color = '#374151';
        selCats = [catLbl.textContent.trim()];
    } else { catLbl.style.color = '#9ca3af'; }

    // Wholesale pre-fill
    const wsVal = document.getElementById('wholesale-price').value;
    if (wsVal) {
        wsOpen = true;
        document.getElementById('wholesale-row').style.display = 'block';
        document.getElementById('ws-icon').textContent = '−';
        document.getElementById('ws-label').textContent = 'Remove Wholesale Price';
    }

    // Unit button label
    if (baseUnit) {
        const btn = document.getElementById('unit-trigger-btn');
        btn.textContent = 'Edit Unit';
        btn.classList.add('chosen');
    }
});

function markDirty() { isDirty = true; }

/* ── Toast ── */
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}

/* ── Categories ── */
function loadCats() {
    fetch('{{ route("items.category.list") }}', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json()).then(data => { cats = data; renderCats(); }).catch(() => {});
}
function toggleCatDD() { document.getElementById('cat-dropdown').classList.toggle('open'); renderCats(); }
function closeCatDD() {
    document.getElementById('cat-dropdown').classList.remove('open');
    document.getElementById('cat-input-row').style.display = 'none';
    document.getElementById('cat-add-btn').style.display = 'flex';
}
function renderCats() {
    const list = document.getElementById('cat-list');
    if (!cats.length) { list.innerHTML = '<p style="padding:12px 14px;font-size:13px;color:#9ca3af;margin:0;">No categories yet</p>'; return; }
    list.innerHTML = cats.map(c => `
        <div class="cat-row" onclick="toggleCat('${esc(c.name)}')">
            <div class="cat-cb ${selCats.includes(c.name)?'on':''}">
                ${selCats.includes(c.name)?'<svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>':''}
            </div>
            <span>${esc(c.name)}</span>
        </div>`).join('');
}
function toggleCat(cat) {
    selCats = selCats.includes(cat) ? [] : [cat];
    renderCats();
    const l = document.getElementById('cat-label');
    l.textContent = selCats[0] || 'Category';
    l.style.color = selCats[0] ? '#374151' : '#9ca3af';
    closeCatDD(); markDirty();
}
function showCatInput() {
    document.getElementById('cat-add-btn').style.display = 'none';
    document.getElementById('cat-input-row').style.display = 'block';
    setTimeout(() => document.getElementById('new-cat-text').focus(), 40);
}
function saveCat() {
    const v = document.getElementById('new-cat-text').value.trim(); if (!v) return;
    fetch('{{ route("items.category.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ name: v })
    }).then(r => r.json()).then(d => {
        const cat = d.category || d;
        if (cat?.id) { if (!cats.find(c => c.id == cat.id)) cats.push(cat); toggleCat(cat.name); }
        else showToast(d.message || 'Failed.');
    }).catch(() => showToast('Network error.'));
    document.getElementById('new-cat-text').value = '';
    document.getElementById('cat-input-row').style.display = 'none';
    document.getElementById('cat-add-btn').style.display = 'flex';
}
document.addEventListener('click', e => { const w = document.getElementById('cat-wrapper'); if (w && !w.contains(e.target)) closeCatDD(); });
document.addEventListener('keydown', e => { if (e.key==='Enter' && document.activeElement?.id==='new-cat-text') { e.preventDefault(); saveCat(); } });

/* ── Type toggle ── */
function handleTypeToggle() {
    if (isDirty) {
        // revert toggle until confirmed
        const tog = document.getElementById('type-toggle');
        tog.checked = !tog.checked;
        discardTarget = 'toggle';
        openDiscardModal(); return;
    }
    const s = document.getElementById('type-toggle').checked;
    iType = s ? 'service' : 'product';
    applyTypeUI(s);
}
function applyTypeUI(isService) {
    document.getElementById('lbl-product').style.color      = isService ? '#9ca3af' : '#2563eb';
    document.getElementById('lbl-product').style.fontWeight = isService ? '500' : '600';
    document.getElementById('lbl-service').style.color      = isService ? '#2563eb' : '#9ca3af';
    document.getElementById('lbl-service').style.fontWeight = isService ? '600' : '500';
    document.getElementById('name-floating-label').textContent = isService ? 'Service Name *' : 'Item Name *';
    document.getElementById('item-code').placeholder = isService ? 'Service Code' : 'Item Code';
    document.getElementById('tab-stock').style.display    = isService ? 'none' : '';
    document.getElementById('purchase-sec').style.display = isService ? 'none' : '';
    if (isService) switchTab('pricing');
}

/* ── Tabs ── */
function switchTab(t) {
    ['pricing','stock'].forEach(x => {
        document.getElementById('tab-'+x).classList.toggle('active', x===t);
        document.getElementById('pane-'+x).style.display = x===t ? 'block' : 'none';
    });
}

/* ── Code ── */
function assignCode() { document.getElementById('item-code').value = 'ITM-'+Math.floor(Math.random()*90000+10000); markDirty(); }

/* ── Wholesale ── */
function toggleWholesale() {
    wsOpen = !wsOpen;
    document.getElementById('wholesale-row').style.display = wsOpen ? 'block' : 'none';
    document.getElementById('ws-icon').textContent = wsOpen ? '−' : '+';
    document.getElementById('ws-label').textContent = wsOpen ? 'Remove Wholesale Price' : 'Add Wholesale Price';
}

/* ── Unit modal ── */
function onUnitChange() {
    const b = document.getElementById('base-unit').value;
    const s = document.getElementById('secondary-unit').value;
    document.getElementById('conversion-row').style.display = 'none';
    document.getElementById('same-unit-error').style.display = 'none';
    if (!b || !s) return;
    if (b === s) { document.getElementById('same-unit-error').style.display = 'flex'; return; }
    document.getElementById('conv-base-lbl').textContent = b.split('(')[0].trim().toUpperCase();
    document.getElementById('conv-sec-lbl').textContent  = s.split('(')[0].trim().toUpperCase();
    document.getElementById('conversion-row').style.display = 'block';
}
function openUnitModal()  { document.getElementById('unit-overlay').classList.add('open'); onUnitChange(); }
function closeUnitModal() { document.getElementById('unit-overlay').classList.remove('open'); }
function saveUnit() {
    const b = document.getElementById('base-unit').value;
    const s = document.getElementById('secondary-unit').value;
    if (b && s && b === s) { document.getElementById('same-unit-error').style.display='flex'; return; }
    baseUnit = b; secUnit = s;
    const btn = document.getElementById('unit-trigger-btn');
    if (baseUnit) {
        const bName = baseUnit.match(/\(([^)]+)\)/)?.[1] || baseUnit;
        const sName = secUnit ? secUnit.match(/\(([^)]+)\)/)?.[1] || secUnit : '';
        const rate  = document.getElementById('conv-rate').value;
        btn.textContent = 'Edit Unit'; btn.classList.add('chosen');
        document.getElementById('unit-conv-label').textContent = (secUnit && secUnit!==baseUnit) ? `1 ${bName} = ${rate} ${sName}` : '';
        document.getElementById('ws-unit-label').textContent = bName;
    } else {
        btn.textContent = 'Select Unit'; btn.classList.remove('chosen');
        document.getElementById('unit-conv-label').textContent = '';
    }
    closeUnitModal(); markDirty();
}

/* ── Image ── */
function previewImg(e) {
    const f = e.target.files[0]; if (!f) return;
    const r = new FileReader();
    r.onload = ev => { document.getElementById('img-thumb').innerHTML = `<img src="${ev.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:3px;"/>`; };
    r.readAsDataURL(f);
}

/* ── Discard dialog ── */
function handleClose() {
    discardTarget = 'close';
    openDiscardModal();
}
function openDiscardModal()  { document.getElementById('discard-overlay').classList.add('open'); }
function closeDiscardModal() { document.getElementById('discard-overlay').classList.remove('open'); discardTarget=null; }
function confirmDiscard() {
    const target = discardTarget;
    closeDiscardModal();
    if (target === 'close') { navigateAway(); }
    else if (target === 'toggle') {
        isDirty = false;
        const tog = document.getElementById('type-toggle');
        tog.checked = !tog.checked;
        iType = tog.checked ? 'service' : 'product';
        applyTypeUI(tog.checked);
    }
}

function navigateAway() { window.location.href = iType==='service' ? '{{ route("items.services") }}' : '{{ route("items") }}'; }

/* ── Delete ── */
function openDeleteModal()  { document.getElementById('delete-overlay').classList.add('open'); }
function closeDeleteModal() { document.getElementById('delete-overlay').classList.remove('open'); }
function confirmDelete() {
    closeDeleteModal();
    const fd = new FormData(); fd.append('_method','DELETE'); fd.append('_token',CSRF);
    fetch(`{{ url("dashboard/items") }}/${ITEM_ID}`, { method:'POST', headers:{'Accept':'application/json'}, body:fd })
    .then(async r => {
        if (r.ok) { window.location.href = iType==='service' ? '{{ route("items.services") }}' : '{{ route("items") }}'; }
        else { let msg='Failed'; try{ const d=await r.json(); if(d.message) msg=d.message; }catch(e){} showToast(msg); }
    }).catch(() => showToast('Network error.'));
}

/* ── Update ── */
function updateItem() {
    const name = document.getElementById('item-name').value.trim();
    if (!name) { document.getElementById('item-name').focus(); showToast('Item name is required.'); return; }
    const payload = {
        _method: 'PUT', type: iType, name,
        category:        selCats[0]||'',
        unit:            baseUnit,
        item_code:       document.getElementById('item-code').value,
        sale_price:      document.getElementById('sale-price').value,
        purchase_price:  document.getElementById('purchase-price').value,
        wholesale_price: document.getElementById('wholesale-price').value,
        min_wholesale_qty: document.getElementById('min-ws-qty').value,
        opening_qty:     document.getElementById('opening-qty').value,
        at_price:        document.getElementById('at-price').value,
        as_of_date:      document.getElementById('as-of-date').value,
        min_stock:       document.getElementById('min-stock').value,
        location:        document.getElementById('location').value,
    };
    fetch(`{{ url("dashboard/items") }}/${ITEM_ID}`, {
        method: 'POST',
        headers: { 'Content-Type':'application/json', 'Accept':'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(payload)
    })
    .then(async r => {
        if (r.ok) { isDirty=false; showToast('Item updated!'); setTimeout(()=>navigateAway(), 1000); }
        else {
            let msg='Failed to update';
            try { const d=await r.json(); if(d.message) msg=d.message; if(d.errors) msg=Object.values(d.errors).flat().join(', '); } catch(e) {}
            showToast(msg);
        }
    }).catch(() => showToast('Network error.'));
}

function esc(s){ return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
@endpush