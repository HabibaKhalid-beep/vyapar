@extends('layouts.app')

@section('title', 'POS')
@section('page', 'sale')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
    font-family: 'Inter', sans-serif;
    font-size: 13px;
    background: #f0f2f5;
    color: #222;
    height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ── TOP MENU BAR ── */
.app-menu-bar {
    background: #fff;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    align-items: center;
    justify-content: space-between;
    height: 32px;
    padding: 0 10px;
    flex-shrink: 0;
    user-select: none;
}
.app-menu-bar .brand {
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 700;
    font-size: 15px;
    color: #e53935;
}
.app-menu-bar .brand svg { width: 22px; height: 22px; }
.menu-items { display: flex; gap: 2px; }
.menu-items button {
    background: none; border: none; cursor: pointer;
    font-size: 12px; color: #444; padding: 4px 10px;
    border-radius: 3px;
}
.menu-items button:hover { background: #f0f2f5; }
.support-bar {
    display: flex; align-items: center; gap: 8px; font-size: 12px; color: #555;
}
.support-bar a { color: #1976d2; text-decoration: none; font-size: 12px; }
.win-controls { display: flex; gap: 0; }
.win-controls button {
    background: none; border: none; cursor: pointer;
    width: 40px; height: 32px; font-size: 14px; color: #555;
}
.win-controls button:hover { background: #e0e0e0; }
.win-controls .close-btn:hover { background: #e53935; color: #fff; }

/* ── TAB BAR ── */
.tab-bar {
    background: #f5f5f5;
    border-bottom: 1px solid #ddd;
    display: flex;
    align-items: flex-end;
    height: 36px;
    padding: 0 4px 0 0;
    flex-shrink: 0;
}
.tab {
    display: flex; align-items: center; gap: 6px;
    background: #fff;
    border: 1px solid #ddd;
    border-bottom: none;
    border-radius: 6px 6px 0 0;
    padding: 6px 12px;
    font-size: 12px;
    color: #333;
    cursor: pointer;
    min-width: 90px;
    height: 32px;
}
.tab.active { background: #fff; border-bottom: 2px solid #fff; z-index: 1; }
.tab .close-tab { color: #999; font-size: 11px; margin-left: 4px; }
.tab .close-tab:hover { color: #e53935; }
.new-tab-btn {
    background: none; border: none; cursor: pointer;
    font-size: 16px; color: #555; padding: 4px 10px;
    height: 32px; display: flex; align-items: center;
}
.new-tab-btn:hover { color: #1976d2; }

/* ── MAIN LAYOUT ── */
.pos-main {
    flex: 1; display: flex; flex-direction: column; min-height: 0; overflow: hidden;
}

/* Search Bar */
.pos-search-bar {
    background: #fff;
    border-bottom: 1px solid #e0e0e0;
    padding: 8px 12px;
    display: flex;
    align-items: center;
    flex-shrink: 0;
}
.pos-search-bar .search-wrap {
    flex: 1;
    position: relative;
}
.pos-search-bar input {
    width: 100%;
    height: 36px;
    border: 1.5px solid #1976d2;
    border-radius: 4px;
    padding: 0 36px 0 12px;
    font-size: 13px;
    outline: none;
}
.pos-search-bar input:focus { border-color: #1565c0; }
.pos-search-bar .search-icon {
    position: absolute; right: 10px; top: 50%; transform: translateY(-50%);
    color: #888; font-size: 15px;
}

/* Search Dropdown */
.search-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 2px);
    left: 0; right: 0;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 4px 16px rgba(0,0,0,0.10);
    z-index: 1000;
}
.search-dropdown.show { display: block; }
.search-dropdown table { width: 100%; border-collapse: collapse; }
.search-dropdown thead tr { background: #f5f5f5; }
.search-dropdown th, .search-dropdown td {
    padding: 7px 12px; font-size: 12px; text-align: left;
    border-bottom: 1px solid #f0f0f0;
}
.search-dropdown tbody tr { cursor: pointer; }
.search-dropdown tbody tr:hover { background: #e3f2fd; }
.search-dropdown .item-bold { font-weight: 600; }

/* ── CONTENT SPLIT ── */
.pos-content {
    flex: 1; display: flex; min-height: 0; overflow: hidden;
}

/* LEFT PANEL */
.pos-left {
    flex: 1; display: flex; flex-direction: column; min-height: 0; overflow: hidden;
    border-right: 1px solid #e0e0e0;
    background: #fff;
}

/* Item Table */
.pos-table-wrap {
    flex: 1; overflow-y: auto; min-height: 0;
}
.pos-table {
    width: 100%; border-collapse: collapse;
    font-size: 13px;
}
.pos-table thead th {
    background: #f5f5f5;
    border-bottom: 1px solid #e0e0e0;
    padding: 8px 12px;
    font-weight: 600;
    font-size: 12px;
    color: #555;
    position: sticky; top: 0; z-index: 1;
}
.pos-table tbody td {
    padding: 8px 12px;
    border-bottom: 1px solid #f0f0f0;
    color: #222;
}
.pos-table tbody tr:hover { background: #f7fbff; }
.pos-table tbody tr.selected-row { background: #e3f2fd; }
.pos-table .empty-row td {
    text-align: center; color: #aaa; padding: 30px;
}

/* Shortcut Buttons */
.pos-shortcuts {
    flex-shrink: 0;
    background: #fff;
    border-top: 1px solid #e0e0e0;
    padding: 6px 8px;
}
.shortcut-row { display: flex; gap: 6px; margin-bottom: 6px; }
.shortcut-row:last-child { margin-bottom: 0; }
.btn-sc {
    flex: 1;
    background: #f5f5f5;
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 6px 4px;
    font-size: 12px;
    text-align: center;
    cursor: pointer;
    color: #333;
    line-height: 1.3;
}
.btn-sc:hover { background: #e3f2fd; border-color: #90caf9; }
.btn-sc span { display: block; font-size: 11px; color: #888; margin-top: 1px; }

/* RIGHT PANEL */
.pos-right {
    width: 340px;
    flex-shrink: 0;
    background: #fff;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
    padding: 12px;
    gap: 10px;
}
.pos-right label {
    font-size: 12px; color: #555; display: block; margin-bottom: 3px;
}
.pos-right input[type="text"],
.pos-right input[type="date"],
.pos-right input[type="number"],
.pos-right select {
    width: 100%; height: 34px;
    border: 1px solid #ccc; border-radius: 4px;
    padding: 0 8px; font-size: 13px;
    outline: none;
}
.pos-right input:focus, .pos-right select:focus { border-color: #1976d2; }

/* Summary Box */
.summary-box {
    background: #f7fbff;
    border: 1px solid #dce8f5;
    border-radius: 6px;
    padding: 10px 12px;
}
.summary-total-row {
    display: flex; align-items: center; gap: 8px; margin-bottom: 4px;
}
.summary-icon {
    background: #e3f2fd; border-radius: 50%; width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center;
    color: #1976d2; font-size: 14px; flex-shrink: 0;
}
.summary-total { font-weight: 700; font-size: 15px; color: #111; flex: 1; }
.summary-breakup { color: #1976d2; font-size: 12px; cursor: pointer; text-decoration: none; }
.summary-meta { font-size: 12px; color: #777; }

.payment-row { display: flex; gap: 8px; }
.payment-row > div { flex: 1; }

.change-row {
    display: flex; justify-content: space-between; align-items: center;
    padding: 6px 0;
}
.change-row .label { font-size: 13px; color: #555; }
.change-row .value { font-weight: 700; font-size: 15px; color: #111; }

.btn-save-print {
    width: 100%; height: 40px;
    background: #a5d6a7; border: none; border-radius: 4px;
    font-weight: 600; font-size: 13px; color: #1b5e20;
    cursor: pointer;
}
.btn-save-print:hover { background: #81c784; }
.btn-other-credit {
    width: 100%; height: 40px;
    background: #f5f5f5; border: 1px solid #ccc; border-radius: 4px;
    font-weight: 600; font-size: 13px; color: #333; cursor: pointer;
}
.btn-other-credit:hover { background: #e0e0e0; }

.bottom-links {
    display: flex; justify-content: space-between;
    font-size: 12px; color: #1976d2;
}
.bottom-links button {
    background: none; border: none; cursor: pointer;
    color: #1976d2; font-size: 12px; padding: 0;
}
.bottom-links button:hover { text-decoration: underline; }

/* Customer dropdown */
.customer-wrap { position: relative; }
.customer-dropdown {
    display: none;
    position: absolute;
    top: calc(100% + 2px); left: 0; right: 0;
    background: #fff;
    border: 1px solid #ddd;
    border-radius: 4px;
    box-shadow: 0 4px 12px rgba(0,0,0,.10);
    z-index: 200;
    max-height: 180px; overflow-y: auto;
}
.customer-dropdown.show { display: block; }
.customer-dropdown .add-new {
    padding: 8px 12px; color: #1976d2; cursor: pointer;
    font-weight: 600; font-size: 12px;
    border-bottom: 1px solid #f0f0f0;
}
.customer-dropdown .add-new:hover { background: #f5f5f5; }
.customer-dropdown .cust-item {
    padding: 7px 12px; font-size: 12px; cursor: pointer;
}
.customer-dropdown .cust-item:hover { background: #e3f2fd; }

/* MODALS */
.modal-overlay {
    display: none;
    position: fixed; inset: 0;
    background: rgba(0,0,0,0.35);
    z-index: 500;
    align-items: center; justify-content: center;
}
.modal-overlay.show { display: flex; }
.modal-box {
    background: #fff;
    border-radius: 12px;
    padding: 28px 28px 24px;
    min-width: 340px;
    max-width: 480px;
    width: 100%;
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
    position: relative;
}
.modal-box h2 { font-size: 18px; font-weight: 700; margin-bottom: 16px; color: #111; }
.modal-close {
    position: absolute; top: 16px; right: 16px;
    background: none; border: none; cursor: pointer;
    font-size: 15px; color: #555; display: flex; align-items: center; gap: 4px;
}
.modal-close:hover { color: #e53935; }
.modal-field { margin-bottom: 14px; }
.modal-field label { font-size: 13px; color: #555; margin-bottom: 4px; display: block; }
.modal-field input, .modal-field select, .modal-field textarea {
    width: 100%; padding: 8px 12px;
    border: 1.5px solid #1976d2;
    border-radius: 6px; font-size: 14px;
    outline: none;
}
.modal-row { display: flex; align-items: center; gap: 12px; }
.modal-row .modal-field { flex: 1; }
.modal-row .sep { color: #555; font-size: 13px; font-weight: 600; padding-top: 18px; }
.modal-item-name { font-size: 13px; color: #444; margin-bottom: 12px; }
.modal-item-name strong { color: #111; }
.modal-total { font-size: 14px; margin-bottom: 14px; color: #333; }
.modal-total strong { font-size: 17px; color: #111; font-weight: 700; }
.weighing-link {
    display: inline-block; margin-top: 2px; margin-bottom: 14px;
    color: #1976d2; font-size: 13px; text-decoration: none; cursor: pointer;
}
.modal-actions { display: flex; gap: 10px; margin-top: 4px; }
.btn-modal-save {
    flex: 1; height: 40px; background: #a5d6a7;
    border: none; border-radius: 6px;
    font-weight: 600; font-size: 14px; color: #1b5e20; cursor: pointer;
}
.btn-modal-save:hover { background: #81c784; }
.btn-modal-cancel {
    flex: 1; height: 40px; background: #f5f5f5;
    border: 1px solid #ccc; border-radius: 6px;
    font-weight: 600; font-size: 14px; color: #333; cursor: pointer;
}
.btn-modal-cancel:hover { background: #e0e0e0; }
#modalAddCustomer .modal-box { min-width: 520px; }
.cust-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
.cust-grid .span-full { grid-column: 1 / -1; }
.cust-grid textarea { resize: none; height: 72px; }

/* Shortcut feedback */
#shortcut-feedback {
    position: fixed; left: 16px; bottom: 16px; z-index: 2000;
    background: rgba(15,23,42,.92); color: #fff;
    padding: 8px 14px; border-radius: 8px; font-size: 12px;
    opacity: 0; transform: translateY(8px); pointer-events: none;
    transition: opacity 140ms, transform 140ms;
}
#shortcut-feedback.show { opacity: 1; transform: translateY(0); }
.shortcut-flash { outline: 2px solid #38bdf8; outline-offset: 2px; }
</style>
@endpush

@section('content')

{{-- ── TOP MENU BAR ── --}}
<div class="app-menu-bar">
    <div class="d-flex align-items-center gap-2">
        <div class="brand">
            <svg viewBox="0 0 24 24" fill="none">
                <path d="M12 2L2 7l10 5 10-5-10-5z" fill="#e53935"/>
                <path d="M2 17l10 5 10-5M2 12l10 5 10-5" stroke="#e53935" stroke-width="2" stroke-linecap="round"/>
            </svg>
        </div>
        <div class="menu-items">
            <button>Company</button>
            <button>Help</button>
            <button>Versions</button>
            <button>Shortcuts</button>
            <button title="Refresh"><i class="bi bi-arrow-clockwise"></i></button>
        </div>
    </div>
    <div class="support-bar">
        <span>Customer Support: <i class="bi bi-telephone-fill" style="color:#1976d2"></i>
            <strong>(+91) 9333 911 911</strong></span>
        <span>|</span>
        <a href="#">Get Instant Online Support</a>
    </div>
    <div class="win-controls">
        <button title="Minimize"><i class="bi bi-dash-lg"></i></button>
        <button title="Restore"><i class="bi bi-stop"></i></button>
        <button class="close-btn" title="Close"><i class="bi bi-x-lg"></i></button>
    </div>
</div>

{{-- ── TAB BAR ── --}}
<div class="tab-bar" id="tab-strip">
    <div class="tab active" id="tab-1">
        <span>#1</span>
        <span style="font-size:11px;color:#999">Ctrl+W</span>
        <button class="close-tab border-0 bg-transparent p-0 ms-1" onclick="closeTab(1)">✕</button>
    </div>
    <button class="new-tab-btn" id="add-tab-btn" onclick="addTab()" title="New Bill [Ctrl+T]">
        <i class="bi bi-plus-lg"></i> New Bill [Ctrl+T]
    </button>
</div>

{{-- ── POS MAIN ── --}}
<div class="pos-main">

    {{-- Search Bar --}}
    <div class="pos-search-bar">
        <div class="search-wrap" style="position:relative">
            <input type="text" id="pos-search-input"
                   placeholder="Search by item name, item code, hsn code, mrp, sale price, purchase price... [F1]"
                   autofocus
                   oninput="handleSearch(this.value)"
                   onkeydown="handleSearchKey(event)"/>
            <i class="bi bi-search search-icon"></i>
            <div class="search-dropdown" id="search-dropdown">
                <table>
                    <thead>
                        <tr>
                            <th>ITEM CODE</th>
                            <th>ITEM NAME</th>
                            <th>STOCK</th>
                            <th>SALE PRICE (Rs)</th>
                            <th>PURCHASE PRICE (Rs)</th>
                        </tr>
                    </thead>
                    <tbody id="search-results"></tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Content Split --}}
    <div class="pos-content">

        {{-- LEFT PANEL --}}
        <div class="pos-left">
            <div class="pos-table-wrap">
                <table class="pos-table">
                    <thead>
                        <tr>
                            <th style="width:40px">#</th>
                            <th>ITEM CODE</th>
                            <th>ITEM NAME</th>
                            <th style="width:80px">QTY</th>
                            <th style="width:70px">UNIT</th>
                            <th style="width:120px">PRICE/UNIT (Rs)</th>
                            <th style="width:110px">TOTAL (Rs)</th>
                        </tr>
                    </thead>
                    <tbody id="bill-tbody">
                        <tr class="empty-row">
                            <td colspan="7">Start scanning items to add them to the bill.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Shortcut Buttons --}}
            <div class="pos-shortcuts">
                <div class="shortcut-row">
                    <button class="btn-sc" onclick="openChangeQty()">
                        Change Quantity<span>[F2]</span>
                    </button>
                    <button class="btn-sc" onclick="openItemDiscount()">
                        Item Discount<span>[F3]</span>
                    </button>
                    <button class="btn-sc" onclick="removeSelectedItem()">
                        Remove Item<span>[F4]</span>
                    </button>
                    <button class="btn-sc" onclick="openChangeUnit()">
                        Change Unit<span>[F6]</span>
                    </button>
                </div>
                <div class="shortcut-row">
                    <button class="btn-sc" onclick="showFeedback('Additional Charges','F8')">
                        Additional Charges<span>[F8]</span>
                    </button>
                    <button class="btn-sc" onclick="openBillDiscount()">
                        Bill Discount<span>[F9]</span>
                    </button>
                    <button class="btn-sc" onclick="showFeedback('Loyalty Points','F10')">
                        Loyalty Points<span>[F10]</span>
                    </button>
                    <button class="btn-sc" onclick="showFeedback('Remarks','F12')">
                        Remarks<span>[F12]</span>
                    </button>
                </div>
            </div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="pos-right">

            <div>
                <label>Date</label>
                <input type="date" id="bill-date" />
            </div>

            <div>
                <label>Customer [F11]</label>
                <div class="customer-wrap">
                    <input type="text" id="customer-input"
                           placeholder="Search for a customer by name, phone number [F11]"
                           oninput="filterCustomers(this.value)"
                           onfocus="showCustomerDrop()" />
                    <div class="customer-dropdown" id="customer-dropdown">
                        <div class="add-new" onclick="openAddCustomer()">+ Add New Customer</div>
                        @foreach($parties ?? [] as $party)
                            <div class="cust-item"
                                 onclick="selectCustomer('{{ $party->name }}','{{ $party->phone ?? '' }}')">
                                {{ $party->name }} — {{ $party->phone ?? '' }}
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="summary-box">
                <div class="summary-total-row">
                    <div class="summary-icon"><i class="bi bi-receipt"></i></div>
                    <div class="summary-total" id="summary-total">Total Rs 0.00</div>
                    <a class="summary-breakup" onclick="showFeedback('Full Breakup','Ctrl+F')">
                        Full Breakup<br>[Ctrl+F]
                    </a>
                </div>
                <div class="summary-meta" id="summary-meta">Items: 0 , Quantity: 0</div>
            </div>

            <div class="payment-row">
                <div>
                    <label>Payment Mode</label>
                    <select id="payment-mode">
                        <option>Cash</option>
                        <option>Card</option>
                        <option>UPI</option>
                        <option>HBL</option>
                        <option>Credit</option>
                    </select>
                </div>
                <div>
                    <label>Amount Received</label>
                    <div style="display:flex;align-items:center;border:1px solid #ccc;border-radius:4px;overflow:hidden;height:34px">
                        <span style="padding:0 8px;background:#f5f5f5;border-right:1px solid #ccc;color:#555;font-size:13px">Rs</span>
                        <input type="number" id="amount-received"
                               style="border:none;outline:none;width:100%;padding:0 8px;font-size:13px;height:100%"
                               value="0.00" oninput="calcChange()"/>
                    </div>
                </div>
            </div>

            <div class="change-row">
                <span class="label">Change to Return:</span>
                <span class="value" id="change-to-return">Rs 0.00</span>
            </div>

            <button class="btn-save-print" onclick="saveBill()">
                Save &amp; Print Bill [Ctrl+P]
            </button>
            <button class="btn-other-credit" onclick="showFeedback('Other Credit/Payments','Ctrl+M')">
                Other/Credit Payments [Ctrl+M]
            </button>

            <div class="bottom-links">
                <button onclick="addTab()">New Bill [Ctrl+T]</button>
                <button onclick="window.location.href='{{ route('items') }}'">Items Master</button>
            </div>
        </div>
    </div>
</div>

{{-- ── MODALS ── --}}

{{-- Change Quantity --}}
<div class="modal-overlay" id="modalChangeQty">
    <div class="modal-box" style="max-width:400px">
        <h2>Change Quantity</h2>
        <button class="modal-close" onclick="closeModal('modalChangeQty')">
            <i class="bi bi-x-lg"></i> [Esc]
        </button>
        <p class="modal-item-name">Item Name: <strong id="cqty-item-name">—</strong></p>
        <div class="modal-field">
            <label>Enter New Quantity</label>
            <input type="number" id="cqty-input" value="1" min="0.001" step="any"/>
        </div>
        <a class="weighing-link">Connect Weighing Scale &rsaquo;</a>
        <div class="modal-actions">
            <button class="btn-modal-save" onclick="saveChangeQty()">Save</button>
            <button class="btn-modal-cancel" onclick="closeModal('modalChangeQty')">Cancel</button>
        </div>
    </div>
</div>

{{-- Item Discount --}}
<div class="modal-overlay" id="modalItemDiscount">
    <div class="modal-box" style="max-width:440px">
        <h2>Item Discount</h2>
        <button class="modal-close" onclick="closeModal('modalItemDiscount')">
            <i class="bi bi-x-lg"></i> [Esc]
        </button>
        <p class="modal-item-name">Item Name: <strong id="idsc-item-name">—</strong></p>
        <div class="modal-total">Total: <strong id="idsc-total">Rs 0.00</strong></div>
        <div class="modal-row">
            <div class="modal-field">
                <label>Discount in %</label>
                <div style="display:flex;align-items:center;border:1.5px solid #1976d2;border-radius:6px;overflow:hidden;height:40px">
                    <span style="padding:0 8px;background:#f5f5f5;border-right:1px solid #ccc;color:#555">%</span>
                    <input type="number" id="idsc-pct" style="border:none;outline:none;width:100%;padding:0 8px;font-size:14px" value="0" oninput="syncItemDiscountPct()"/>
                </div>
            </div>
            <div class="sep">OR</div>
            <div class="modal-field">
                <label>Discount in Rs</label>
                <div style="display:flex;align-items:center;border:1.5px solid #ccc;border-radius:6px;overflow:hidden;height:40px">
                    <span style="padding:0 8px;background:#f5f5f5;border-right:1px solid #ccc;color:#555">Rs</span>
                    <input type="number" id="idsc-rs" style="border:none;outline:none;width:100%;padding:0 8px;font-size:14px" value="0" oninput="syncItemDiscountRs()"/>
                </div>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn-modal-save" onclick="saveItemDiscount()">Save</button>
            <button class="btn-modal-cancel" onclick="closeModal('modalItemDiscount')">Cancel</button>
        </div>
    </div>
</div>

{{-- Bill Discount --}}
<div class="modal-overlay" id="modalBillDiscount">
    <div class="modal-box" style="max-width:440px">
        <h2>Bill Discount</h2>
        <button class="modal-close" onclick="closeModal('modalBillDiscount')">
            <i class="bi bi-x-lg"></i> [Esc]
        </button>
        <div class="modal-total">Total: <strong id="bdsc-total">Rs 0.00</strong></div>
        <div class="modal-row">
            <div class="modal-field">
                <label>Discount in %</label>
                <div style="display:flex;align-items:center;border:1.5px solid #1976d2;border-radius:6px;overflow:hidden;height:40px">
                    <span style="padding:0 8px;background:#f5f5f5;border-right:1px solid #ccc;color:#555">%</span>
                    <input type="number" id="bdsc-pct" style="border:none;outline:none;width:100%;padding:0 8px;font-size:14px" value="0" oninput="syncBillDiscountPct()"/>
                </div>
            </div>
            <div class="sep">OR</div>
            <div class="modal-field">
                <label>Discount in Rs</label>
                <div style="display:flex;align-items:center;border:1.5px solid #ccc;border-radius:6px;overflow:hidden;height:40px">
                    <span style="padding:0 8px;background:#f5f5f5;border-right:1px solid #ccc;color:#555">Rs</span>
                    <input type="number" id="bdsc-rs" style="border:none;outline:none;width:100%;padding:0 8px;font-size:14px" value="0" oninput="syncBillDiscountRs()"/>
                </div>
            </div>
        </div>
        <div class="modal-actions">
            <button class="btn-modal-save" onclick="saveBillDiscount()">Save</button>
            <button class="btn-modal-cancel" onclick="closeModal('modalBillDiscount')">Cancel</button>
        </div>
    </div>
</div>

{{-- Change Unit --}}
<div class="modal-overlay" id="modalChangeUnit">
    <div class="modal-box" style="max-width:400px">
        <h2>Change Unit</h2>
        <button class="modal-close" onclick="closeModal('modalChangeUnit')">
            <i class="bi bi-x-lg"></i> [Esc]
        </button>
        <p class="modal-item-name">Item Name: <strong id="cunit-item-name">—</strong></p>
        <div class="modal-field">
            <label>Select Unit</label>
            <select id="cunit-select" style="border:1.5px solid #1976d2;border-radius:6px;height:44px;font-size:14px">
                <option>KILOGRAMS (Kg)</option>
                <option>GRAMS (g)</option>
                <option>PIECES (Pcs)</option>
                <option>LITERS (L)</option>
                <option>METERS (m)</option>
            </select>
        </div>
        <div class="modal-actions">
            <button class="btn-modal-save" onclick="saveChangeUnit()">Save</button>
            <button class="btn-modal-cancel" onclick="closeModal('modalChangeUnit')">Cancel</button>
        </div>
    </div>
</div>

{{-- Add Customer --}}
<div class="modal-overlay" id="modalAddCustomer">
    <div class="modal-box" style="max-width:540px">
        <h2>Add Customer</h2>
        <button class="modal-close" onclick="closeModal('modalAddCustomer')">
            <i class="bi bi-x-lg"></i> [Esc]
        </button>
        <div class="cust-grid">
            <div class="modal-field">
                <label>Customer Name <span style="color:red">*</span></label>
                <input type="text" id="cust-name" placeholder="Customer Name" />
            </div>
            <div class="modal-field">
                <label>Phone Number</label>
                <input type="text" id="cust-phone" placeholder="Phone Number" />
            </div>
            <div class="modal-field span-full">
                <label>Billing Address</label>
                <textarea id="cust-billing" placeholder="Billing Address"
                    style="width:100%;border:1.5px solid #ccc;border-radius:6px;padding:8px 12px;font-size:13px;outline:none;resize:none;height:68px"></textarea>
            </div>
            <div class="modal-field span-full">
                <label>Shipping Address</label>
                <textarea id="cust-shipping" placeholder="Shipping Address"
                    style="width:100%;border:1.5px solid #ccc;border-radius:6px;padding:8px 12px;font-size:13px;outline:none;resize:none;height:68px"></textarea>
            </div>
            <div></div>
            <div style="display:flex;align-items:center;gap:8px;padding-top:4px">
                <input type="checkbox" id="same-billing" onchange="syncShipping()"/>
                <label for="same-billing" style="margin:0;cursor:pointer">Same as billing address</label>
            </div>
        </div>
        <div class="modal-actions" style="margin-top:16px">
            <button class="btn-modal-save" onclick="saveCustomer()" style="background:#4caf50;color:#fff">Save</button>
            <button class="btn-modal-cancel" onclick="closeModal('modalAddCustomer')">Cancel</button>
        </div>
    </div>
</div>

{{-- Shortcut Feedback --}}
<div id="shortcut-feedback"></div>

@endsection

@push('scripts')
<script>
// ── State ──
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
let billItems   = [];
let selectedRow = -1;
let tabCount    = 1;
let billDiscount = 0;

// Products from Laravel (loaded from DB)
const products = @json($items ?? []);

// ── Init ──
document.addEventListener('DOMContentLoaded', () => {
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('bill-date').value = today;
    renderBill();
});

// ── Search ──
function handleSearch(q) {
    const dd = document.getElementById('search-dropdown');
    const tb = document.getElementById('search-results');
    if (!q.trim()) { dd.classList.remove('show'); return; }
    const filtered = products.filter(p =>
        (p.name || '').toLowerCase().includes(q.toLowerCase()) ||
        (p.item_code || '').includes(q)
    );
    if (!filtered.length) { dd.classList.remove('show'); return; }
    tb.innerHTML = filtered.map(p => `
        <tr onclick="addItemToBill(${p.id})">
            <td>${p.item_code ?? '—'}</td>
            <td class="item-bold">${p.name}</td>
            <td>${p.opening_qty ?? 0} ${p.unit ?? ''}</td>
            <td>${p.sale_price ?? 0}</td>
            <td>${p.purchase_price ?? 0}</td>
        </tr>
    `).join('');
    dd.classList.add('show');
}

function handleSearchKey(e) {
    if (e.key === 'Escape') document.getElementById('search-dropdown').classList.remove('show');
    if (e.key === 'Enter') {
        const q = e.target.value.trim();
        const p = products.find(p =>
            p.item_code === q || (p.name || '').toLowerCase() === q.toLowerCase()
        );
        if (p) { addItemToBill(p.id); document.getElementById('search-dropdown').classList.remove('show'); }
    }
}

document.addEventListener('click', e => {
    if (!e.target.closest('.search-wrap'))    document.getElementById('search-dropdown').classList.remove('show');
    if (!e.target.closest('.customer-wrap'))  document.getElementById('customer-dropdown').classList.remove('show');
});

// ── Bill Items ──
function addItemToBill(id) {
    const p = products.find(x => x.id === id);
    if (!p) return;
    const exist = billItems.find(x => x.id === id);
    if (exist) { exist.qty += 1; }
    else { billItems.push({ ...p, qty: 1, discount: 0, unit: p.unit ?? 'Nos' }); }
    document.getElementById('pos-search-input').value = '';
    document.getElementById('search-dropdown').classList.remove('show');
    selectedRow = billItems.length - 1;
    renderBill();
}

function removeSelectedItem() {
    if (selectedRow < 0 || !billItems[selectedRow]) return;
    billItems.splice(selectedRow, 1);
    selectedRow = Math.min(selectedRow, billItems.length - 1);
    renderBill();
}

function renderBill() {
    const tb = document.getElementById('bill-tbody');
    if (!billItems.length) {
        tb.innerHTML = '<tr class="empty-row"><td colspan="7">Start scanning items to add them to the bill.</td></tr>';
    } else {
        tb.innerHTML = billItems.map((item, i) => `
            <tr class="${i === selectedRow ? 'selected-row' : ''}" onclick="selectBillRow(${i})">
                <td>${i + 1}</td>
                <td>${item.item_code ?? '—'}</td>
                <td>${item.name}</td>
                <td>${item.qty.toFixed(2)}</td>
                <td>${item.unit}</td>
                <td>${parseFloat(item.sale_price ?? 0).toFixed(2)}</td>
                <td>${((item.qty * parseFloat(item.sale_price ?? 0)) - item.discount).toFixed(2)}</td>
            </tr>
        `).join('');
    }
    updateSummary();
}

function selectBillRow(i) { selectedRow = i; renderBill(); }

// ── Summary ──
function getSubtotal() {
    return billItems.reduce((s, item) => s + (item.qty * parseFloat(item.sale_price ?? 0)) - item.discount, 0);
}
function getTotal() { return Math.max(0, getSubtotal() - billDiscount); }

function updateSummary() {
    const total = getTotal();
    const items = billItems.length;
    const qty   = billItems.reduce((s, i) => s + i.qty, 0);
    document.getElementById('summary-total').textContent = `Total Rs ${total.toFixed(2)}`;
    document.getElementById('summary-meta').textContent  = `Items: ${items} , Quantity: ${qty}`;
    calcChange();
}

function calcChange() {
    const received = parseFloat(document.getElementById('amount-received').value) || 0;
    const change   = Math.max(0, received - getTotal());
    document.getElementById('change-to-return').textContent = `Rs ${change.toFixed(2)}`;
}

// ── Save Bill ──
function saveBill() {
    if (!billItems.length) { showFeedback('No items in bill!', ''); return; }
    const payload = {
        date:         document.getElementById('bill-date').value,
        customer:     document.getElementById('customer-input').value,
        payment_mode: document.getElementById('payment-mode').value,
        amount_received: document.getElementById('amount-received').value,
        bill_discount: billDiscount,
        items: billItems.map(i => ({
            id:       i.id,
            name:     i.name,
            qty:      i.qty,
            unit:     i.unit,
            price:    i.sale_price,
            discount: i.discount,
            total:    (i.qty * parseFloat(i.sale_price ?? 0)) - i.discount,
        })),
        total: getTotal(),
    };
    fetch('{{ route("sale.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': CSRF,
        },
        body: JSON.stringify(payload),
    })
    .then(async r => {
        if (r.ok) {
            showFeedback('Bill Saved!', '');
            billItems = []; selectedRow = -1; billDiscount = 0;
            document.getElementById('amount-received').value = '0.00';
            renderBill();
        } else {
            const d = await r.json();
            showFeedback(d.message || 'Failed to save bill', '');
        }
    })
    .catch(() => showFeedback('Network error', ''));
}

// ── Modals ──
function openModal(id)  { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

function openChangeQty() {
    if (selectedRow < 0 || !billItems[selectedRow]) { showFeedback('Select an item first', ''); return; }
    document.getElementById('cqty-item-name').textContent = billItems[selectedRow].name;
    document.getElementById('cqty-input').value = billItems[selectedRow].qty;
    openModal('modalChangeQty');
    setTimeout(() => document.getElementById('cqty-input').focus(), 80);
}
function saveChangeQty() {
    const v = parseFloat(document.getElementById('cqty-input').value);
    if (!isNaN(v) && v > 0) { billItems[selectedRow].qty = v; renderBill(); }
    closeModal('modalChangeQty');
}

function openItemDiscount() {
    if (selectedRow < 0 || !billItems[selectedRow]) { showFeedback('Select an item first', ''); return; }
    const item = billItems[selectedRow];
    const base = item.qty * parseFloat(item.sale_price ?? 0);
    document.getElementById('idsc-item-name').textContent = item.name;
    document.getElementById('idsc-total').textContent = `Rs ${base.toFixed(2)}`;
    document.getElementById('idsc-pct').value = base > 0 ? ((item.discount / base) * 100).toFixed(2) : 0;
    document.getElementById('idsc-rs').value  = item.discount.toFixed(2);
    openModal('modalItemDiscount');
}
function syncItemDiscountPct() {
    const item = billItems[selectedRow]; if (!item) return;
    const pct  = parseFloat(document.getElementById('idsc-pct').value) || 0;
    document.getElementById('idsc-rs').value = ((item.qty * parseFloat(item.sale_price ?? 0) * pct) / 100).toFixed(2);
}
function syncItemDiscountRs() {
    const item = billItems[selectedRow]; if (!item) return;
    const rs   = parseFloat(document.getElementById('idsc-rs').value) || 0;
    const base = item.qty * parseFloat(item.sale_price ?? 0);
    document.getElementById('idsc-pct').value = base > 0 ? ((rs / base) * 100).toFixed(2) : 0;
}
function saveItemDiscount() {
    const rs = parseFloat(document.getElementById('idsc-rs').value) || 0;
    billItems[selectedRow].discount = rs;
    renderBill();
    closeModal('modalItemDiscount');
}

function openBillDiscount() {
    document.getElementById('bdsc-total').textContent = `Rs ${getSubtotal().toFixed(2)}`;
    document.getElementById('bdsc-pct').value = getSubtotal() > 0 ? ((billDiscount / getSubtotal()) * 100).toFixed(2) : 0;
    document.getElementById('bdsc-rs').value  = billDiscount.toFixed(2);
    openModal('modalBillDiscount');
}
function syncBillDiscountPct() {
    const pct = parseFloat(document.getElementById('bdsc-pct').value) || 0;
    document.getElementById('bdsc-rs').value = ((getSubtotal() * pct) / 100).toFixed(2);
}
function syncBillDiscountRs() {
    const rs   = parseFloat(document.getElementById('bdsc-rs').value) || 0;
    const base = getSubtotal();
    document.getElementById('bdsc-pct').value = base > 0 ? ((rs / base) * 100).toFixed(2) : 0;
}
function saveBillDiscount() {
    billDiscount = parseFloat(document.getElementById('bdsc-rs').value) || 0;
    updateSummary();
    closeModal('modalBillDiscount');
}

function openChangeUnit() {
    if (selectedRow < 0 || !billItems[selectedRow]) { showFeedback('Select an item first', ''); return; }
    document.getElementById('cunit-item-name').textContent = billItems[selectedRow].name;
    openModal('modalChangeUnit');
}
function saveChangeUnit() {
    const sel   = document.getElementById('cunit-select');
    const txt   = sel.options[sel.selectedIndex].text;
    const match = txt.match(/\(([^)]+)\)/);
    if (match) billItems[selectedRow].unit = match[1];
    renderBill();
    closeModal('modalChangeUnit');
}

// ── Customer ──
function showCustomerDrop() { document.getElementById('customer-dropdown').classList.add('show'); }
function filterCustomers(q) { document.getElementById('customer-dropdown').classList.add('show'); }
function selectCustomer(name, phone) {
    document.getElementById('customer-input').value = phone ? `${name} (${phone})` : name;
    document.getElementById('customer-dropdown').classList.remove('show');
}
function openAddCustomer() {
    document.getElementById('customer-dropdown').classList.remove('show');
    openModal('modalAddCustomer');
}
function syncShipping() {
    const cb   = document.getElementById('same-billing');
    const ship = document.getElementById('cust-shipping');
    if (cb.checked) { ship.value = document.getElementById('cust-billing').value; ship.disabled = true; }
    else { ship.value = ''; ship.disabled = false; }
}
document.addEventListener('input', e => {
    if (e.target.id === 'cust-billing' && document.getElementById('same-billing').checked)
        document.getElementById('cust-shipping').value = e.target.value;
});
function saveCustomer() {
    const name  = document.getElementById('cust-name').value.trim();
    const phone = document.getElementById('cust-phone').value.trim();
    if (!name) { alert('Customer name is required.'); return; }
    // Save to DB via API
    fetch('{{ route("parties.store") }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ name, phone })
    })
    .then(r => r.json())
    .then(d => {
        selectCustomer(name, phone);
        closeModal('modalAddCustomer');
        showFeedback('Customer saved!', '');
    })
    .catch(() => {
        selectCustomer(name, phone);
        closeModal('modalAddCustomer');
    });
}

// ── Tabs ──
function addTab() {
    tabCount++;
    const strip  = document.getElementById('tab-strip');
    const newBtn = strip.querySelector('.new-tab-btn');
    const tab    = document.createElement('div');
    tab.className = 'tab';
    tab.id = `tab-${tabCount}`;
    tab.innerHTML = `<span>#${tabCount}</span>
        <span style="font-size:11px;color:#999">Ctrl+W</span>
        <button class="close-tab border-0 bg-transparent p-0 ms-1" onclick="closeTab(${tabCount})">✕</button>`;
    tab.onclick = () => activateTab(tabCount);
    strip.insertBefore(tab, newBtn);
    activateTab(tabCount);
    showFeedback('New Bill', 'Ctrl+T');
}
function activateTab(n) {
    document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
    const t = document.getElementById(`tab-${n}`);
    if (t) t.classList.add('active');
}
function closeTab(n) {
    const t = document.getElementById(`tab-${n}`);
    if (t) t.remove();
}

// ── Shortcut Feedback ──
let fbTimer;
function showFeedback(label, key) {
    const el = document.getElementById('shortcut-feedback');
    el.textContent = key ? `${label} [${key}]` : label;
    el.classList.add('show');
    clearTimeout(fbTimer);
    fbTimer = setTimeout(() => el.classList.remove('show'), 1300);
}

// ── Keyboard Shortcuts ──
document.addEventListener('keydown', e => {
    if (e.defaultPrevented) return;
    const fMap = {
        F2: openChangeQty, F3: openItemDiscount, F4: removeSelectedItem,
        F6: openChangeUnit, F9: openBillDiscount,
        F11: () => { document.getElementById('customer-input').focus(); showCustomerDrop(); }
    };
    if (fMap[e.code]) { e.preventDefault(); fMap[e.code](); return; }
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.show').forEach(m => m.classList.remove('show'));
        return;
    }
    const ctrl = e.ctrlKey || e.metaKey;
    if (!ctrl) return;
    const k = (e.key || '').toLowerCase();
    const cmap = {
        p: saveBill,
        m: () => showFeedback('Other Credit/Payments', 'Ctrl+M'),
        f: () => showFeedback('Full Breakup', 'Ctrl+F'),
        t: addTab
    };
    if (cmap[k]) { e.preventDefault(); cmap[k](); }
});
</script>
@endpush