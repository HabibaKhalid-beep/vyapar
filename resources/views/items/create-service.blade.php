@extends('layouts.app')

@section('title', 'Items')
@section('page', 'items')

@push('styles')
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }

.il-page {
    display: flex; flex-direction: column;
    height: 100vh; max-height: 100vh;
    background: #fff; overflow: hidden;
}
/* ══════════════════════════════
   DELETE CONFIRMATION MODAL
══════════════════════════════ */
#delete-overlay {
    position: fixed; inset: 0; z-index: 2000;
    background: rgba(0,0,0,.45);
    display: none; align-items: center; justify-content: center;
}
#delete-overlay.open { display: flex; }
#delete-modal {
    background: #fff; border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0,0,0,.25);
    width: 420px; max-width: 95vw;
    animation: popIn .15s ease-out;
}
.del-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 20px 14px;
    background: #e8f0fb;
    border-radius: 8px 8px 0 0;
}
.del-header-title {
    font-size: 15px; font-weight: 700; color: #1a2a4a;
}
.del-header-close {
    background: none; border: none; cursor: pointer;
    font-size: 18px; color: #6b7280; line-height: 1;
    width: 24px; height: 24px; display: flex; align-items: center; justify-content: center;
    border-radius: 4px;
}
.del-header-close:hover { background: #d1d5db; color: #111; }
.del-body {
    padding: 22px 24px 20px;
}
.del-body p { font-size: 14px; font-weight: 600; color: #1a2a4a; }
.del-footer {
    display: flex; justify-content: flex-end; gap: 12px;
    padding: 14px 20px 18px;
}
.del-btn-yes {
    background: #5b9bd5; border: none; border-radius: 5px;
    padding: 9px 28px; font-size: 14px; font-weight: 600;
    color: #fff; cursor: pointer; transition: background .15s;
}
.del-btn-yes:hover { background: #3a7bbf; }
.del-btn-no {
    background: #5b9bd5; border: none; border-radius: 5px;
    padding: 9px 28px; font-size: 14px; font-weight: 600;
    color: #fff; cursor: pointer; transition: background .15s;
}
.del-btn-no:hover { background: #3a7bbf; }

/* ── TOP TABS ── */
.il-tabs {
    display: flex; border-bottom: 1px solid #e5e7eb;
    flex-shrink: 0; background: #fff;
}
.il-tab {
    flex: 1; text-align: center; padding: 16px 0;
    font-size: 13px; font-weight: 600; letter-spacing: .06em;
    color: #9ca3af; cursor: pointer;
    border-bottom: 2px solid transparent; transition: all .15s;
    user-select: none;
}
.il-tab:hover { color: #4b5563; }
.il-tab.active { color: #e53e3e; border-bottom-color: #e53e3e; }

/* ── BODY ── */
.il-body { display: flex; flex: 1; min-height: 0; overflow: hidden; }

/* ── LEFT PANEL ── */
.il-left {
    width: 320px; flex-shrink: 0;
    border-right: 1px solid #e5e7eb;
    display: flex; flex-direction: column; background: #fff;
}
.il-left-toolbar {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 14px; border-bottom: 1px solid #f3f4f6; flex-shrink: 0;
}
.il-search-btn {
    width: 34px; height: 34px; border: 1.5px solid #e5e7eb;
    border-radius: 6px; background: #fff;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; flex-shrink: 0; transition: border-color .15s;
}
.il-search-btn:hover { border-color: #93c5fd; }
.il-search-wrap { flex: 1; position: relative; display: none; }
.il-search-wrap.open { display: block; }
.il-search-input {
    width: 100%; border: 1.5px solid #2563eb; border-radius: 6px;
    padding: 7px 10px 7px 30px; font-size: 13px; outline: none; color: #374151;
}
.il-search-icon { position: absolute; left: 9px; top: 50%; transform: translateY(-50%); }

.il-add-group { display: flex; align-items: center; margin-left: auto; position: relative; }
.il-add-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f59e0b; color: #fff; border: none;
    border-radius: 6px 0 0 6px; padding: 8px 14px;
    font-size: 13px; font-weight: 600; cursor: pointer;
    transition: background .15s; white-space: nowrap; height: 36px;
}
.il-add-btn:hover { background: #d97706; }
.il-add-chevron {
    background: #d97706; border: none;
    border-radius: 0 6px 6px 0; padding: 0 10px;
    cursor: pointer; color: #fff; display: flex; align-items: center; justify-content: center;
    transition: background .15s; height: 36px;
}
.il-add-chevron:hover { background: #b45309; }
.il-add-dd {
    position: absolute; top: calc(100% + 4px); right: 28px;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 6px; box-shadow: 0 6px 20px rgba(0,0,0,.12);
    z-index: 500; min-width: 150px; display: none;
}
.il-add-dd.open { display: block; }
.il-add-dd-item {
    display: flex; align-items: center; gap: 10px;
    padding: 11px 16px; cursor: pointer; font-size: 13px; color: #374151;
}
.il-add-dd-item:hover { background: #f9fafb; }

.il-more-btn {
    background: none; border: 1.5px solid #e5e7eb;
    border-radius: 6px; width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: #6b7280; flex-shrink: 0;
    transition: border-color .15s; position: relative;
}
.il-more-btn:hover { border-color: #93c5fd; }
.il-bulk-dd {
    position: absolute; top: calc(100% + 4px); right: 0;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 6px; box-shadow: 0 6px 20px rgba(0,0,0,.12);
    z-index: 500; min-width: 190px; display: none;
}
.il-bulk-dd.open { display: block; }
.il-bulk-dd-item {
    padding: 11px 16px; cursor: pointer; font-size: 13px; color: #374151;
}
.il-bulk-dd-item:hover { background: #f9fafb; }

.il-list { flex: 1; overflow-y: auto; }
.il-list::-webkit-scrollbar { width: 4px; }
.il-list::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

.il-list-header {
    display: flex; align-items: center;
    padding: 9px 14px; background: #f9fafb;
    border-bottom: 1px solid #f3f4f6;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em; color: #9ca3af;
    position: relative;
}
.col-item { flex: 1; }
.col-filter {
    width: 20px; display: flex; align-items: center;
    justify-content: center; cursor: pointer; color: #e53e3e; position: relative;
    font-size: 12px;
}
.col-qty { width: 70px; text-align: right; }
.col-sort { width: 20px; display: flex; align-items: center; justify-content: center; cursor: pointer; color: #6b7280; }

/* Left panel filter dropdown */
.il-filter-dd {
    position: absolute; top: calc(100% + 2px); left: 50%;
    transform: translateX(-50%);
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 10px; box-shadow: 0 8px 30px rgba(0,0,0,.15);
    z-index: 500; min-width: 160px; display: none; padding: 14px 14px 10px;
}
.il-filter-dd.open { display: block; }
.il-filter-row {
    display: flex; align-items: center; gap: 8px;
    padding: 6px 0; font-size: 13px; color: #374151; cursor: pointer;
}
.il-filter-row input[type=checkbox] { accent-color: #2563eb; width: 15px; height: 15px; }
.il-filter-actions { display: flex; gap: 8px; margin-top: 10px; }
.il-filter-clear {
    flex: 1; border: 1.5px solid #e5e7eb; background: #fff;
    border-radius: 20px; padding: 7px 0; font-size: 12px;
    color: #6b7280; cursor: pointer; font-weight: 500;
}
.il-filter-apply {
    flex: 1; border: none; background: #e53e3e;
    border-radius: 20px; padding: 7px 0; font-size: 12px;
    color: #fff; cursor: pointer; font-weight: 600;
}

.il-item-row {
    display: flex; align-items: center; padding: 12px 14px;
    border-bottom: 1px solid #f8f9fa; cursor: pointer; transition: background .12s;
    position: relative;
}
.il-item-row:hover { background: #f9fafb; }
.il-item-row.active { background: #eff6ff; }
.il-item-dot { width: 8px; height: 8px; border-radius: 50%; background: #9ca3af; margin-right: 8px; flex-shrink: 0; }
.il-item-name { flex: 1; font-size: 14px; color: #111827; font-weight: 500; }
.il-item-qty { width: 50px; text-align: right; font-size: 14px; color: #10b981; font-weight: 600; }

.il-item-more-wrap {
    position: relative; width: 24px; height: 24px; flex-shrink: 0;
}
.il-item-more-btn {
    width: 24px; height: 24px; display: flex; align-items: center;
    justify-content: center; color: #9ca3af; cursor: pointer;
    border-radius: 4px; background: none; border: none;
}
.il-item-more-btn:hover { background: #f3f4f6; color: #374151; }
.il-item-dd {
    position: absolute; right: 0; top: calc(100% + 2px);
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 6px; box-shadow: 0 6px 20px rgba(0,0,0,.12);
    z-index: 600; min-width: 140px; display: none;
}
.il-item-dd.open { display: block; }
.il-item-dd-item {
    padding: 11px 16px; cursor: pointer; font-size: 13px; color: #374151;
}
.il-item-dd-item:hover { background: #f9fafb; }
.il-item-dd-item.danger { color: #ef4444; }
.il-item-dd-item.danger:hover { background: #fef2f2; }

/* ── RIGHT PANEL ── */
.il-right { flex: 1; display: flex; flex-direction: column; background: #fff; min-width: 0; }

.il-no-selection {
    flex: 1; display: flex; flex-direction: column;
    align-items: center; justify-content: center; color: #9ca3af; gap: 12px;
}
.il-no-sel-icon {
    width: 64px; height: 64px; background: #f3f4f6; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
}

.il-detail-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 24px 12px; border-bottom: 1px solid #f3f4f6; flex-shrink: 0;
}
.il-detail-name-row { display: flex; align-items: center; gap: 10px; position: relative; }
.il-detail-name { font-size: 17px; font-weight: 700; color: #111827; }
.il-icon-btn {
    background: none; border: none; cursor: pointer;
    color: #6b7280; padding: 4px; border-radius: 4px; transition: color .12s;
}
.il-icon-btn:hover { color: #2563eb; }
.il-adjust-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: #2563eb; color: #fff; border: none;
    border-radius: 6px; padding: 9px 16px; font-size: 13px;
    font-weight: 600; cursor: pointer; transition: background .15s;
}
.il-adjust-btn:hover { background: #1d4ed8; }

.il-stats {
    display: flex; align-items: stretch;
    border-bottom: 1px solid #f3f4f6; flex-shrink: 0;
}
.il-stat-left {
    display: flex; flex-direction: column;
    padding: 10px 24px; gap: 5px; flex: 1; justify-content: center;
}
.il-stat-right {
    display: flex; flex-direction: column;
    padding: 10px 24px; gap: 5px;
    align-items: flex-end; justify-content: center;
}
.il-stat-item { display: flex; align-items: center; gap: 6px; }
.il-stat-label { font-size: 12px; color: #6b7280; font-weight: 500; text-transform: uppercase; letter-spacing: .04em; }
.il-stat-value { font-size: 13px; font-weight: 700; color: #16a34a; }
.il-stat-value.neutral { color: #16a34a; }

.il-share-popup {
    position: absolute; top: calc(100% + 6px); left: 0;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 8px; box-shadow: 0 6px 24px rgba(0,0,0,.15);
    z-index: 700; display: none; padding: 12px 8px; min-width: 260px;
}
.il-share-popup.open { display: flex; gap: 4px; }
.il-share-option {
    display: flex; flex-direction: column; align-items: center; gap: 6px;
    padding: 10px 14px; cursor: pointer; border-radius: 6px; flex: 1;
    font-size: 11px; color: #374151; font-weight: 500; transition: background .12s;
}
.il-share-option:hover { background: #f3f4f6; }
.il-share-option .share-icon {
    width: 36px; height: 36px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center; font-size: 18px;
}
.share-email { background: #fff0f0; color: #e53e3e; }
.share-sms   { background: #f0fdf4; color: #16a34a; }
.share-wa    { background: #f0fdf4; color: #25d366; }
.share-copy  { background: #f5f3ff; color: #7c3aed; }

/* ── TRANSACTIONS SECTION ── */
.il-txn-section { flex: 1; display: flex; flex-direction: column; min-height: 0; overflow: hidden; }
.il-txn-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 14px 20px 10px; border-bottom: 1px solid #f0f0f0; flex-shrink: 0;
}
.il-txn-title { font-size: 12px; font-weight: 700; letter-spacing: .08em; color: #374151; text-transform: uppercase; }
.il-txn-right { display: flex; align-items: center; gap: 8px; }
.il-txn-search {
    border: 1px solid #e5e7eb; border-radius: 6px;
    padding: 7px 10px 7px 34px; font-size: 13px; outline: none; width: 260px; color: #374151;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' viewBox='0 0 24 24' stroke='%23b0b8c4' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath stroke-linecap='round' d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat 11px center;
}
.il-txn-search::placeholder { color: #b0b8c4; }
.il-txn-search:focus { border-color: #2563eb; outline: none; }

/* ── BLUE Excel icon button (matching Vyapar image) ── */
.il-export-btn {
    background: none; border: none; cursor: pointer; padding: 2px;
    display: flex; align-items: center; justify-content: center;
}
.il-export-btn .excel-icon {
    width: 26px; height: 26px; background: #1d6fcc; border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 13px; font-weight: 800; letter-spacing: -1px;
    font-family: Arial, sans-serif;
}

/* ── RESIZABLE TABLE WRAPPER ── */
.il-tbl-wrap { flex: 1; overflow-y: auto; overflow-x: auto; }
.il-tbl-wrap::-webkit-scrollbar { width: 4px; height: 4px; }
.il-tbl-wrap::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

/* ── TABLE ── */
.il-tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }

.il-tbl th {
    padding: 12px 16px; font-size: 12px; font-weight: 500;
    text-transform: capitalize; color: #9ca3af;
    background: #f9fafb; border-bottom: 1px solid #ebebeb;
    border-right: 1px solid #d1d5db;
    text-align: left; white-space: nowrap;
    position: relative; overflow: hidden; user-select: none;
}
.il-tbl th[data-col="dot"] { padding: 0; border-bottom: 1px solid #ebebeb; }
.il-tbl th .th-inner {
    display: inline-flex; align-items: center; gap: 3px; cursor: pointer;
}
.th-sort-arrow {
    display: inline-flex; align-items: center;
    color: #4a4a4a; flex-shrink: 0; font-size: 10px; font-style: normal;
    opacity: 0; transition: opacity .1s; line-height: 1;
}
.il-tbl th.sort-asc  .th-sort-arrow,
.il-tbl th.sort-desc .th-sort-arrow { opacity: 1; }
.th-sort-arrow::after               { content: '↑'; }
.il-tbl th.sort-desc .th-sort-arrow::after { content: '↓'; }
.il-tbl th .th-filter-icon {
    color: #b8bec7; flex-shrink: 0; cursor: pointer; transition: color .15s;
    font-size: 10px;
}
.il-tbl th .th-filter-icon:hover  { color: #e53e3e; }
.il-tbl th .th-filter-icon.active { color: #e53e3e; }

/* ── COLUMN RESIZE HANDLE ── */
.col-resize-handle {
    position: absolute; right: 0; top: 0; bottom: 0;
    width: 5px; cursor: col-resize; z-index: 10;
}
.col-resize-handle:hover,
.col-resize-handle.resizing { background: #2563eb; opacity: .4; }

/* ── TABLE CELLS ── */
.il-tbl td {
    padding: 12px 10px; font-size: 13px; color: #374151; font-weight: 400;
    border-bottom: 1px solid #f3f4f6; vertical-align: middle;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.il-tbl td.td-dot { padding: 0 0 0 10px; width: 28px; vertical-align: middle; }
.il-tbl td.td-price, .il-tbl th.th-price-right { text-align: right; }
.il-tbl td.td-price { color: #16a34a; }
.il-tbl td.td-status { color: #9ca3af; }
.il-tbl td.td-actions { padding: 2px 4px; width: 36px; }
.il-tbl tbody tr:hover td { background: #fafafa; }
.il-tbl tbody tr.txn-selected td { background: #dbeafe; }
.il-tbl tbody tr:last-child td { border-bottom: none; }

/* Row action menu */
.il-row-menu-wrap { position: relative; }
.il-row-menu-btn {
    background: none; border: none; cursor: pointer; color: #9ca3af;
    padding: 4px 6px; border-radius: 4px; font-size: 18px; line-height: 1;
}
.il-row-menu-btn:hover { color: #374151; background: #f3f4f6; }
.il-row-menu {
    position: fixed;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 6px; box-shadow: 0 6px 20px rgba(0,0,0,.12);
    z-index: 9000; min-width: 150px; display: none;
}
.il-row-menu.open { display: block; }
.il-row-menu-item {
    padding: 11px 16px; cursor: pointer; font-size: 13px; color: #374151;
}
.il-row-menu-item:hover { background: #f9fafb; }
.il-row-menu-item.danger { color: #ef4444; }
.il-row-menu-item.danger:hover { background: #fef2f2; }

/* ══════════════════════════════
   COLUMN FILTER DROPDOWNS
══════════════════════════════ */
.col-filter-dd {
    display: none; position: fixed;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 10px; box-shadow: 0 8px 30px rgba(0,0,0,.15);
    z-index: 9999; min-width: 220px; padding: 16px 16px 12px;
}
.col-filter-dd.open { display: block; }
.cfd-title { font-size: 12px; font-weight: 600; color: #374151; margin-bottom: 12px; }
.cfd-cb-row {
    display: flex; align-items: center; gap: 10px;
    padding: 7px 2px; font-size: 13px; color: #374151; cursor: pointer;
}
.cfd-cb-row input[type=checkbox] { width: 15px; height: 15px; accent-color: #2563eb; flex-shrink: 0; }
.cfd-select {
    width: 100%; border: 1.5px solid #e5e7eb; border-radius: 6px;
    padding: 9px 10px; font-size: 13px; color: #374151;
    background: #fff; outline: none; cursor: pointer; margin-bottom: 10px;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 10px center; padding-right: 28px;
}
.cfd-input {
    width: 100%; border: 1.5px solid #e5e7eb; border-radius: 6px;
    padding: 9px 10px; font-size: 13px; color: #374151;
    outline: none; box-sizing: border-box;
}
.cfd-input:focus { border-color: #2563eb; }
.cfd-input::placeholder { color: #9ca3af; }
.cfd-date-lbl { font-size: 11px; color: #9ca3af; margin-bottom: 6px; }
.cfd-actions { display: flex; gap: 8px; margin-top: 14px; }
.cfd-clear {
    flex: 1; border: 1.5px solid #e5e7eb; background: #fff;
    border-radius: 20px; padding: 8px 0; font-size: 12px;
    color: #6b7280; cursor: pointer; font-weight: 500;
}
.cfd-apply {
    flex: 1; border: none; background: #e53e3e;
    border-radius: 20px; padding: 8px 0; font-size: 12px;
    color: #fff; cursor: pointer; font-weight: 600;
}
.cfd-clear:hover { background: #f3f4f6; }
.cfd-apply:hover { background: #c53030; }

/* ══════════════════════════════
   STOCK ADJUSTMENT MODAL
══════════════════════════════ */
#adj-overlay {
    position: fixed; inset: 0; z-index: 1000;
    background: rgba(0,0,0,.45);
    display: none; align-items: center; justify-content: center;
}
#adj-overlay.open { display: flex; }
#adj-modal {
    background: #fff; border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0,0,0,.2);
    width: 780px; max-width: 96vw;
    animation: popIn .15s ease-out;
}
@keyframes popIn {
    from { opacity:0; transform:scale(.96); }
    to   { opacity:1; transform:scale(1); }
}
.adj-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 28px 16px; border-bottom: 1px solid #f3f4f6;
}
.adj-title { font-size: 17px; font-weight: 700; color: #111827; }
.adj-toggle-row { display: flex; align-items: center; gap: 14px; }
.adj-toggle-lbl { font-size: 14px; font-weight: 600; color: #2563eb; cursor: pointer; }
.adj-toggle-lbl.inactive { color: #9ca3af; font-weight: 500; }
.adj-switch { position: relative; display: inline-block; width: 44px; height: 24px; }
.adj-switch input { opacity:0; width:0; height:0; }
.adj-slider {
    position: absolute; cursor: pointer; inset: 0;
    background: #2563eb; border-radius: 24px; transition: .2s;
}
.adj-slider:before {
    content: ""; position: absolute;
    width: 18px; height: 18px; left: 3px; bottom: 3px;
    background: white; border-radius: 50%; transition: .2s;
    box-shadow: 0 1px 3px rgba(0,0,0,.3);
}
input:checked + .adj-slider:before { transform: translateX(20px); }
.adj-close { background: none; border: none; cursor: pointer; color: #6b7280; padding: 4px; }
.adj-close:hover { color: #111827; }
.adj-body { padding: 20px 28px; }
.adj-item-name { font-size: 14px; color: #374151; margin-bottom: 20px; }
.adj-fields { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.adj-input {
    border: 1.5px solid #d1d5db; border-radius: 6px;
    padding: 11px 14px; font-size: 14px; color: #374151;
    outline: none; background: #fff; transition: border-color .15s;
}
.adj-input:focus { border-color: #2563eb; }
.adj-input::placeholder { color: #9ca3af; }
.adj-qty-wrap { display: flex; align-items: center; position: relative; }
.adj-qty-input { width: 130px; border-radius: 6px 0 0 6px !important; }
.adj-unit-sel {
    border: 1.5px solid #d1d5db; border-left: none;
    border-radius: 0 6px 6px 0; padding: 11px 10px 11px 8px;
    font-size: 14px; color: #374151; background: #f9fafb;
    outline: none; cursor: pointer; min-width: 70px; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='10' height='10' fill='none' viewBox='0 0 24 24' stroke='%236b7280' stroke-width='2.5'%3E%3Cpolyline points='6 9 12 15 18 9'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 8px center; padding-right: 26px;
}
.adj-price-input { width: 160px; }
.adj-details-input { flex: 1; min-width: 180px; }
.adj-date-wrap { position: relative; }
.adj-date-label {
    position: absolute; top: 4px; left: 14px;
    font-size: 10px; color: #9ca3af; pointer-events: none; z-index: 1;
}
.adj-date-input { width: 180px; padding-top: 20px !important; padding-bottom: 6px !important; }
.adj-footer {
    display: flex; justify-content: flex-end;
    padding: 16px 28px; border-top: 1px solid #f3f4f6; gap: 10px;
}
.adj-cancel {
    background: #fff; border: 1.5px solid #d1d5db; border-radius: 6px;
    padding: 10px 24px; font-size: 14px; color: #6b7280; cursor: pointer;
}
.adj-save {
    background: #2563eb; border: none; border-radius: 6px;
    padding: 10px 32px; font-size: 14px; font-weight: 700;
    color: #fff; cursor: pointer; transition: background .15s;
}
.adj-save:hover { background: #1d4ed8; }

#toast {
    position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%) translateY(20px);
    background: #111827; color: #fff; padding: 10px 22px;
    border-radius: 8px; font-size: 13px; font-weight: 500;
    opacity: 0; transition: all .25s; z-index: 9999; pointer-events: none;
}
#toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>
@endpush

@section('content')

<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

<div class="il-page">

    {{-- TOP TABS --}}
    <div class="il-tabs">
        <div class="il-tab active" onclick="switchMainTab('products',this)">PRODUCTS</div>
        <div class="il-tab" onclick="window.location.href='/items/services'">SERVICES</div>
        <div class="il-tab" onclick="switchMainTab('category',this)">CATEGORY</div>
        <div class="il-tab" onclick="switchMainTab('units',this)">UNITS</div>
    </div>

    @if(count($products) === 0)

    {{-- ══════════════════════════════
         EMPTY STATE
    ══════════════════════════════ --}}
    <div style="flex:1;display:flex;align-items:center;justify-content:center;background:#fff;">
        <div style="display:flex;flex-direction:column;align-items:center;gap:18px;text-align:center;">
            <div style="width:220px;height:220px;background:#dbeafe;border-radius:50%;position:relative;">
                <div style="position:absolute;background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.13);display:flex;align-items:center;justify-content:center;width:52px;height:52px;top:14px;left:50%;transform:translateX(-50%);font-size:26px;">🧺</div>
                <div style="position:absolute;background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.13);display:flex;align-items:center;justify-content:center;width:48px;height:48px;top:50%;left:8px;transform:translateY(-50%);font-size:22px;">🖨️</div>
                <div style="position:absolute;background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.13);display:flex;align-items:center;justify-content:center;width:48px;height:48px;top:50%;right:8px;transform:translateY(-50%);font-size:22px;">🫖</div>
                <div style="position:absolute;background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.13);display:flex;align-items:center;justify-content:center;width:46px;height:46px;bottom:24px;left:26px;font-size:20px;">🧵</div>
                <div style="position:absolute;background:#fff;border-radius:10px;box-shadow:0 2px 10px rgba(0,0,0,.13);display:flex;align-items:center;justify-content:center;width:46px;height:46px;bottom:24px;right:26px;font-size:20px;">📦</div>
                <div style="position:absolute;background:#fff;border-radius:12px;box-shadow:0 4px 16px rgba(0,0,0,.16);display:flex;align-items:center;justify-content:center;width:54px;height:54px;top:50%;left:50%;transform:translate(-50%,-50%);font-size:26px;z-index:2;">📋</div>
            </div>
            <p style="font-size:14px;color:#6b7280;max-width:420px;line-height:1.65;">
                Add products you sell to your customers and create Sale invoices for them faster.
            </p>
            <button onclick="window.location.href='/items/create'" style="display:inline-flex;align-items:center;background:#f59e0b;color:#fff;border:none;border-radius:6px;padding:13px 32px;font-size:14px;font-weight:600;cursor:pointer;">
                Add Your First Product
            </button>
        </div>
    </div>

    @else

    {{-- ══════════════════════════════
         NORMAL LAYOUT (products exist)
    ══════════════════════════════ --}}
    <div class="il-body">

        {{-- LEFT PANEL --}}
        <div class="il-left">
            <div class="il-left-toolbar">
                <button class="il-search-btn" onclick="toggleSearch()" title="Search">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                </button>
                <div class="il-search-wrap" id="search-wrap">
                    <svg class="il-search-icon" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" class="il-search-input" id="search-input" placeholder="Search items..." oninput="filterItems()"/>
                </div>

                <div class="il-add-group" id="add-group">
                    <button class="il-add-btn" onclick="goToAddItem()">
                        <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.8"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                        Add Item
                    </button>
                    <button class="il-add-chevron" onclick="toggleAddDD(event)" title="More options">
                        <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
                    </button>
                    <div class="il-add-dd" id="add-dd">
                        <div class="il-add-dd-item" onclick="closeAddDD()">
                            <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><path stroke-linecap="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Import Items
                        </div>
                    </div>
                </div>

                <div class="il-more-btn" id="bulk-wrap" onclick="toggleBulkDD(event)">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                    <div class="il-bulk-dd" id="bulk-dd">
                        <div class="il-bulk-dd-item" onclick="bulkAction('bulk-inactive')">Bulk Inactive</div>
                        <div class="il-bulk-dd-item" onclick="bulkAction('bulk-active')">Bulk Active</div>
                        <div class="il-bulk-dd-item" onclick="bulkAction('bulk-assign-code')">Bulk Assign Code</div>
                        <div class="il-bulk-dd-item" onclick="bulkAction('assign-units')">Assign Units</div>
                        <div class="il-bulk-dd-item" onclick="bulkAction('bulk-update')">Bulk Update Items</div>
                    </div>
                </div>
            </div>

            <div class="il-list-header">
                <span class="col-item">ITEM</span>
                <span class="col-filter" onclick="toggleFilterDD(event)" title="Filter">
                    <i class="fa-solid fa-filter"></i>
                    <div class="il-filter-dd" id="filter-dd" onclick="event.stopPropagation()">
                        <label class="il-filter-row">
                            <input type="checkbox" id="filter-active" checked onchange="applyFilter()"> Active
                        </label>
                        <label class="il-filter-row">
                            <input type="checkbox" id="filter-inactive" onchange="applyFilter()"> InActive
                        </label>
                        <div class="il-filter-actions">
                            <button class="il-filter-clear" onclick="clearFilter()">Clear</button>
                            <button class="il-filter-apply" onclick="closeFilterDD()">Apply</button>
                        </div>
                    </div>
                </span>
                <span class="col-qty">QUANTITY</span>
                <span class="col-sort" id="qty-sort-arrow" onclick="sortByQty()" title="Sort by quantity" style="font-size:12px;color:#6b7280;">↕</span>
                <span style="width:24px;"></span>
            </div>

            <div class="il-list" id="items-list"></div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="il-right">

            <div class="il-no-selection" id="no-selection">
                <div class="il-no-sel-icon">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="1.5"><path stroke-linecap="round" d="M20 7H4a2 2 0 00-2 2v9a2 2 0 002 2h16a2 2 0 002-2V9a2 2 0 00-2-2z"/><path stroke-linecap="round" d="M16 3H8l-2 4h12l-2-4z"/></svg>
                </div>
                <div style="font-size:15px;color:#6b7280;font-weight:500;">Select an item to view details</div>
                <div style="font-size:13px;color:#9ca3af;">or add a new item using the button above</div>
            </div>

            <div id="item-detail" style="display:none;flex-direction:column;flex:1;min-height:0;">

                <div class="il-detail-header">
                    <div class="il-detail-name-row">
                        <span class="il-detail-name" id="detail-name">—</span>
                        <button class="il-icon-btn" title="Share/Export" onclick="toggleSharePopup(event)">
                            <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-4.553M19.553 5.447L15 5m4.553.447V10M4 12v7a1 1 0 001 1h14a1 1 0 001-1v-3M4 12V5a1 1 0 011-1h7"/></svg>
                        </button>
                        <div class="il-share-popup" id="share-popup" onclick="event.stopPropagation()">
                            <div class="il-share-option" onclick="shareVia('email')">
                                <div class="share-icon share-email">✉️</div><span>EMAIL</span>
                            </div>
                            <div class="il-share-option" onclick="shareVia('sms')">
                                <div class="share-icon share-sms">💬</div><span>SMS</span>
                            </div>
                            <div class="il-share-option" onclick="shareVia('whatsapp')">
                                <div class="share-icon share-wa">
                                    <svg width="20" height="20" viewBox="0 0 32 32" fill="#25d366"><path d="M16 3C9 3 3 9 3 16c0 2.3.6 4.5 1.8 6.5L3 29l6.7-1.8C11.6 28.4 13.8 29 16 29c7 0 13-6 13-13S23 3 16 3zm6.5 18.2c-.3.8-1.5 1.5-2.1 1.6-.5.1-1.2.1-1.9-.1-.5-.1-1.1-.3-1.8-.6-3.2-1.4-5.3-4.6-5.5-4.8-.2-.2-1.4-1.9-1.4-3.6 0-1.7.9-2.5 1.2-2.8.3-.3.7-.4 1-.4h.7c.2 0 .5 0 .7.6l.9 2.3c.1.2.1.5 0 .7l-.5.6-.4.5c.2.4.9 1.5 1.8 2.3 1 .9 1.9 1.3 2.3 1.4.3-.4.7-.9.9-1.1.2-.2.4-.2.6-.1l2.1 1c.2.1.4.2.5.4.1.3.1 1-.2 1.8z"/></svg>
                                </div>
                                <span>WHATSAPP</span>
                            </div>
                            <div class="il-share-option" onclick="shareVia('copy')">
                                <div class="share-icon share-copy">🔗</div><span>COPY LINK</span>
                            </div>
                        </div>
                    </div>
                    <button class="il-adjust-btn" onclick="openAdjModal()">
                        <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><path stroke-linecap="round" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                        ADJUST ITEM
                    </button>
                </div>

                <div class="il-stats">
                    <div class="il-stat-left">
                        <div class="il-stat-item">
                            <span class="il-stat-label">SALE PRICE:</span>
                            <span class="il-stat-value" id="detail-sale">—</span>
                        </div>
                        <div class="il-stat-item">
                            <span class="il-stat-label">PURCHASE PRICE:</span>
                            <span class="il-stat-value" id="detail-purchase">—</span>
                        </div>
                    </div>
                    <div class="il-stat-right">
                        <div class="il-stat-item">
                            <span class="il-stat-label">STOCK QUANTITY:</span>
                            <span class="il-stat-value neutral" id="detail-stock-qty">0</span>
                        </div>
                        <div class="il-stat-item">
                            <span class="il-stat-label">STOCK VALUE:</span>
                            <span class="il-stat-value neutral" id="detail-stock-val">Rs 0.00</span>
                        </div>
                    </div>
                </div>

                <div class="il-txn-section">
                    <div class="il-txn-header">
                        <span class="il-txn-title">TRANSACTIONS</span>
                        <div class="il-txn-right">
                            <input type="text" class="il-txn-search" placeholder="Search transactions..." oninput="filterTxns(this.value)"/>
                            <button class="il-export-btn" title="Export to Excel" onclick="exportToExcel()">
                                <div class="excel-icon">X</div>
                            </button>
                        </div>
                    </div>

                    <div class="il-tbl-wrap">
                        <table class="il-tbl" id="txn-table">
                            <thead>
                                <tr id="txn-thead-row">
                                    <th style="width:28px;padding:0;" data-col="dot"></th>
                                    <th data-col="type" style="width:160px;">
                                        <span class="th-inner" onclick="sortTxnCol('type')">
                                            TYPE <span class="th-sort-arrow"></span>
                                            <i class="fa-solid fa-filter th-filter-icon" onclick="toggleColFilter(event,'cf-type')"></i>
                                        </span>
                                        <div class="col-resize-handle" data-col="type"></div>
                                    </th>
                                    <th data-col="invoice" style="width:150px;">
                                        <span class="th-inner" onclick="sortTxnCol('invoice')">
                                            INVOICE/REF. NO <span class="th-sort-arrow"></span>
                                            <i class="fa-solid fa-filter th-filter-icon" onclick="toggleColFilter(event,'cf-invoice')"></i>
                                        </span>
                                        <div class="col-resize-handle" data-col="invoice"></div>
                                    </th>
                                    <th data-col="name" style="width:160px;">
                                        <span class="th-inner" onclick="sortTxnCol('name')">
                                            NAME <span class="th-sort-arrow"></span>
                                            <i class="fa-solid fa-filter th-filter-icon" onclick="toggleColFilter(event,'cf-name')"></i>
                                        </span>
                                        <div class="col-resize-handle" data-col="name"></div>
                                    </th>
                                    <th data-col="date" style="width:130px;">
                                        <span class="th-inner" onclick="sortTxnCol('date')">
                                            DATE <span class="th-sort-arrow"></span>
                                            <i class="fa-solid fa-filter th-filter-icon" onclick="toggleColFilter(event,'cf-date')"></i>
                                        </span>
                                        <div class="col-resize-handle" data-col="date"></div>
                                    </th>
                                    <th data-col="qty" style="width:120px;">
                                        <span class="th-inner" onclick="sortTxnCol('qty')">
                                            QUANTITY <span class="th-sort-arrow"></span>
                                            <i class="fa-solid fa-filter th-filter-icon" onclick="toggleColFilter(event,'cf-qty')"></i>
                                        </span>
                                        <div class="col-resize-handle" data-col="qty"></div>
                                    </th>
                                    <th data-col="price" style="width:130px;" class="th-price-right">
                                        <span class="th-inner" onclick="sortTxnCol('price')" style="justify-content:flex-end;width:100%;">
                                            PRICE/ UNIT <span class="th-sort-arrow"></span>
                                            <i class="fa-solid fa-filter th-filter-icon" onclick="toggleColFilter(event,'cf-price')"></i>
                                        </span>
                                        <div class="col-resize-handle" data-col="price"></div>
                                    </th>
                                    <th data-col="status" style="width:110px;">
                                        <span class="th-inner" onclick="sortTxnCol('status')">
                                            STATUS <span class="th-sort-arrow"></span>
                                            <i class="fa-solid fa-filter th-filter-icon" onclick="toggleColFilter(event,'cf-status')"></i>
                                        </span>
                                        <div class="col-resize-handle" data-col="status"></div>
                                    </th>
                                    <th style="width:36px;" data-col="actions"></th>
                                </tr>
                            </thead>
                            <tbody id="txn-tbody"></tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @endif

</div>

{{-- Toast --}}
<div id="toast"></div>

{{-- ══════════════════════════════
     COLUMN FILTER DROPDOWNS
══════════════════════════════ --}}
<div class="col-filter-dd" id="cf-type" onclick="event.stopPropagation()">
    <div class="cfd-title">Select Category</div>
    <label class="cfd-cb-row"><input type="checkbox" value="Sale" onchange="applyColFilters()"> Sale</label>
    <label class="cfd-cb-row"><input type="checkbox" value="Sale (e-Invoice)" onchange="applyColFilters()"> Sale (e-Invoice)</label>
    <label class="cfd-cb-row"><input type="checkbox" value="Purchase" onchange="applyColFilters()"> Purchase</label>
    <label class="cfd-cb-row"><input type="checkbox" value="Add Adjustment" onchange="applyColFilters()"> Add Adjustment</label>
    <label class="cfd-cb-row"><input type="checkbox" value="Reduce Adjustment" onchange="applyColFilters()"> Reduce Adjustment</label>
    <label class="cfd-cb-row"><input type="checkbox" value="Opening Stock" onchange="applyColFilters()"> Opening Stock</label>
    <div class="cfd-actions">
        <button class="cfd-clear" onclick="clearColFilter('cf-type')">Clear</button>
        <button class="cfd-apply" onclick="applyColFilters();closeAllColFilters()">Apply</button>
    </div>
</div>

<div class="col-filter-dd" id="cf-invoice" onclick="event.stopPropagation()">
    <div class="cfd-title">Select Category</div>
    <select class="cfd-select" id="cf-invoice-op"><option value="contains">Contains</option><option value="exact">Exact match</option></select>
    <input type="text" class="cfd-input" id="cf-invoice-val" placeholder="INVOICE/REF. NO" oninput="applyColFilters()"/>
    <div class="cfd-actions">
        <button class="cfd-clear" onclick="clearColFilter('cf-invoice')">Clear</button>
        <button class="cfd-apply" onclick="applyColFilters();closeAllColFilters()">Apply</button>
    </div>
</div>

<div class="col-filter-dd" id="cf-name" onclick="event.stopPropagation()">
    <div class="cfd-title">Select Category</div>
    <select class="cfd-select" id="cf-name-op"><option value="contains">Contains</option><option value="exact">Exact match</option></select>
    <input type="text" class="cfd-input" id="cf-name-val" placeholder="NAME" oninput="applyColFilters()"/>
    <div class="cfd-actions">
        <button class="cfd-clear" onclick="clearColFilter('cf-name')">Clear</button>
        <button class="cfd-apply" onclick="applyColFilters();closeAllColFilters()">Apply</button>
    </div>
</div>

<div class="col-filter-dd" id="cf-date" onclick="event.stopPropagation()">
    <div class="cfd-title">Select Category</div>
    <select class="cfd-select" id="cf-date-op"><option value="equal">Equal To</option><option value="before">Before</option><option value="after">After</option></select>
    <div class="cfd-date-lbl">Select Date</div>
    <input type="date" class="cfd-input" id="cf-date-val" oninput="applyColFilters()"/>
    <div class="cfd-actions">
        <button class="cfd-clear" onclick="clearColFilter('cf-date')">Clear</button>
        <button class="cfd-apply" onclick="applyColFilters();closeAllColFilters()">Apply</button>
    </div>
</div>

<div class="col-filter-dd" id="cf-qty" onclick="event.stopPropagation()">
    <div class="cfd-title">Select Category</div>
    <select class="cfd-select" id="cf-qty-op">
        <option value="equal">Equal to</option>
        <option value="lt">Less Than</option>
        <option value="gt">Greater Than</option>
    </select>
    <input type="number" class="cfd-input" id="cf-qty-val" placeholder="QUANTITY" oninput="applyColFilters()"/>
    <div class="cfd-actions">
        <button class="cfd-clear" onclick="clearColFilter('cf-qty')">Clear</button>
        <button class="cfd-apply" onclick="applyColFilters();closeAllColFilters()">Apply</button>
    </div>
</div>

<div class="col-filter-dd" id="cf-price" onclick="event.stopPropagation()">
    <div class="cfd-title">Select Category</div>
    <select class="cfd-select" id="cf-price-op">
        <option value="equal">Equal to</option>
        <option value="lt">Less Than</option>
        <option value="gt">Greater Than</option>
    </select>
    <input type="number" class="cfd-input" id="cf-price-val" placeholder="PRICE/ UNIT" step="0.01" oninput="applyColFilters()"/>
    <div class="cfd-actions">
        <button class="cfd-clear" onclick="clearColFilter('cf-price')">Clear</button>
        <button class="cfd-apply" onclick="applyColFilters();closeAllColFilters()">Apply</button>
    </div>
</div>

<div class="col-filter-dd" id="cf-status" onclick="event.stopPropagation()">
    <label class="cfd-cb-row"><input type="checkbox" value="Unpaid" onchange="applyColFilters()"> Unpaid</label>
    <label class="cfd-cb-row"><input type="checkbox" value="Partial" onchange="applyColFilters()"> Partial</label>
    <label class="cfd-cb-row"><input type="checkbox" value="Paid" onchange="applyColFilters()"> Paid</label>
    <label class="cfd-cb-row"><input type="checkbox" value="Cancelled" onchange="applyColFilters()"> Cancelled</label>
    <div class="cfd-actions">
        <button class="cfd-clear" onclick="clearColFilter('cf-status')">Clear</button>
        <button class="cfd-apply" onclick="applyColFilters();closeAllColFilters()">Apply</button>
    </div>
</div>

{{-- STOCK ADJUSTMENT MODAL --}}
<div id="adj-overlay" onclick="if(event.target.id==='adj-overlay')closeAdjModal()">
    <div id="adj-modal" onclick="event.stopPropagation()">
        <div class="adj-header">
            <span class="adj-title">Stock Adjustment</span>
            <div class="adj-toggle-row">
                <span class="adj-toggle-lbl" id="lbl-add-stock">Add Stock</span>
                <label class="adj-switch">
                    <input type="checkbox" id="adj-toggle" onchange="handleAdjToggle()">
                    <span class="adj-slider"></span>
                </label>
                <span class="adj-toggle-lbl inactive" id="lbl-reduce-stock">Reduce Stock</span>
            </div>
            <button class="adj-close" onclick="closeAdjModal()">
                <svg width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2"><path stroke-linecap="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>
        <div class="adj-body">
            <div class="adj-item-name" id="adj-item-name">Item Name</div>
            <div class="adj-fields">
                <div class="adj-qty-wrap">
                    <input type="number" class="adj-input adj-qty-input" id="adj-qty" placeholder="Total Qty" min="0"/>
                    <select class="adj-unit-sel" id="adj-unit">
                        <option>Kg</option><option>Nos</option><option>Bag</option>
                        <option>Box</option><option>Ltr</option><option>Mtr</option><option>Pcs</option>
                    </select>
                </div>
                <input type="number" class="adj-input adj-price-input" id="adj-price" placeholder="At Price" min="0" step="0.01"/>
                <input type="text" class="adj-input adj-details-input" id="adj-details" placeholder="Details"/>
                <div class="adj-date-wrap">
                    <span class="adj-date-label">Adjustment Date</span>
                    <input type="date" class="adj-input adj-date-input" id="adj-date"/>
                </div>
            </div>
        </div>
        <div class="adj-footer">
            <button class="adj-cancel" onclick="closeAdjModal()">Cancel</button>
            <button class="adj-save" onclick="saveAdjustment()">Save</button>
        </div>
    </div>
</div>
{{-- DELETE CONFIRMATION MODAL --}}
<div id="delete-overlay">
    <div id="delete-modal" onclick="event.stopPropagation()">
        <div class="del-header">
            <span class="del-header-title">Are you sure you want to delete this Item?</span>
            <button class="del-header-close" onclick="closeDeleteModal()">✕</button>
        </div>
        <div class="del-body">
            <p>This Item will be Deleted.</p>
        </div>
        <div class="del-footer">
            <button class="del-btn-yes" onclick="confirmDelete()">YES</button>
            <button class="del-btn-no" onclick="closeDeleteModal()">NO</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
/* ── State ── */
let allItems     = @json($products ?? []);
let transactions = {};
let selectedIdx  = null;
let sortAsc      = true;

/* ── Sort state for transactions table ── */
let txnSortCol = null;
let txnSortAsc = true;

/* ══════════════════════════════════════════
   COLUMN RESIZE
══════════════════════════════════════════ */
(function initColResize() {
    let isResizing = false, startX = 0, startW = 0, th = null, handle = null;
    document.addEventListener('mousedown', function(e) {
        if (!e.target.classList.contains('col-resize-handle')) return;
        e.preventDefault();
        handle = e.target; th = handle.closest('th');
        isResizing = true; startX = e.clientX; startW = th.offsetWidth;
        handle.classList.add('resizing');
        document.body.style.cursor = 'col-resize';
        document.body.style.userSelect = 'none';
    });
    document.addEventListener('mousemove', function(e) {
        if (!isResizing) return;
        const newW = Math.max(60, startW + (e.clientX - startX));
        th.style.width = newW + 'px'; th.style.minWidth = newW + 'px';
    });
    document.addEventListener('mouseup', function() {
        if (!isResizing) return;
        isResizing = false;
        if (handle) handle.classList.remove('resizing');
        document.body.style.cursor = ''; document.body.style.userSelect = '';
        handle = null; th = null;
    });
})();

/* ── Sort arrow helper ── */
function updateSortArrows(col, asc) {
    document.querySelectorAll('#txn-thead-row th').forEach(th => th.classList.remove('sort-asc','sort-desc'));
    if (!col) return;
    const th = document.querySelector(`#txn-thead-row th[data-col="${col}"]`);
    if (th) th.classList.add(asc ? 'sort-asc' : 'sort-desc');
}

/* ── Init ── */
document.addEventListener('DOMContentLoaded', () => {
    renderList(allItems);
    if (allItems.length > 0) selectItem(0);
    const d = new Date();
    const adjDate = document.getElementById('adj-date');
    if (adjDate) adjDate.value = d.getFullYear() + '-' + String(d.getMonth()+1).padStart(2,'0') + '-' + String(d.getDate()).padStart(2,'0');
    document.addEventListener('click', () => {
        closeAddDD(); closeBulkDD(); closeFilterDD(); closeSharePopup();
        closeAllColFilters();
        document.querySelectorAll('.il-row-menu.open, .il-item-dd.open').forEach(m => m.classList.remove('open'));
    });
});

/* ── Toast ── */
function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}

/* ── Share popup ── */
function toggleSharePopup(e) { e.stopPropagation(); document.getElementById('share-popup').classList.toggle('open'); }
function closeSharePopup() { document.getElementById('share-popup')?.classList.remove('open'); }
function shareVia(method) {
    closeSharePopup();
    const name = allItems[selectedIdx]?.name || 'item';
    if (method === 'copy') { navigator.clipboard.writeText(window.location.href).then(() => showToast('Link copied!')); }
    else if (method === 'email') { window.open(`mailto:?subject=Item: ${name}&body=View item: ${window.location.href}`); }
    else if (method === 'whatsapp') { window.open(`https://wa.me/?text=Item: ${name} - ${window.location.href}`); }
    else if (method === 'sms') { window.open(`sms:?body=Item: ${name}`); }
}

/* ── Main tab switch ── */
function switchMainTab(tab, el) {
    if (tab === 'services') { window.location.href = '/items/services'; return; }
    if (tab === 'category') { window.location.href = '/items/category'; return; }
    if (tab === 'units') { window.location.href = '/items/units'; return; }
    document.querySelectorAll('.il-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
}

/* ── Search ── */
function toggleSearch() {
    const w = document.getElementById('search-wrap');
    w.classList.toggle('open');
    if (w.classList.contains('open')) document.getElementById('search-input').focus();
}
function filterItems() {
    const q = document.getElementById('search-input').value.toLowerCase();
    renderList(allItems.filter(i => i.name.toLowerCase().includes(q)));
}

/* ── Render list ── */
function renderList(items) {
    const c = document.getElementById('items-list');
    if (!c) return;
    if (!items.length) {
        c.innerHTML = `<div style="padding:32px 16px;text-align:center;color:#9ca3af;font-size:13px;">No items found</div>`;
        return;
    }
    c.innerHTML = items.map((item, i) => `
        <div class="il-item-row ${selectedIdx === i ? 'active' : ''}" onclick="selectItem(${i})">
            <span class="il-item-dot"></span>
            <span class="il-item-name">${esc(item.name)}</span>
            <span class="il-item-qty">${getTotalQty(i)}</span>
            <div class="il-item-more-wrap" onclick="event.stopPropagation()">
                <button class="il-item-more-btn" onclick="toggleItemDD(event,${i})" title="Options">
                    <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/>
                    </svg>
                </button>
                <div class="il-item-dd" id="item-dd-${i}">
                    <div class="il-item-dd-item" onclick="editItemNav(${i})">View/Edit</div>
                    <div class="il-item-dd-item danger" onclick="deleteItem(${i})">Delete</div>
                </div>
            </div>
        </div>
    `).join('');
}

function toggleItemDD(e, i) {
    e.stopPropagation();
    document.querySelectorAll('.il-item-dd.open').forEach(d => d.classList.remove('open'));
    document.getElementById(`item-dd-${i}`).classList.toggle('open');
}
function editItemNav(i) { window.location.href = '/items/' + (allItems[i].id || i) + '/edit'; }
/* ── Delete modal state ── */
let deleteTargetIdx = null;

function deleteItem(i) {
    deleteTargetIdx = i;
    document.getElementById('delete-overlay').classList.add('open');
    // Close the item dropdown
    document.querySelectorAll('.il-item-dd.open').forEach(d => d.classList.remove('open'));
}

function closeDeleteModal() {
    document.getElementById('delete-overlay').classList.remove('open');
    deleteTargetIdx = null;
}

function confirmDelete() {
    const i = deleteTargetIdx;
    if (i === null) return;
    closeDeleteModal();

    const item = allItems[i];
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    if (!csrfToken) { showToast('CSRF token missing.'); return; }

    // Use POST with _method=DELETE for broader server compatibility
    const formData = new FormData();
    formData.append('_method', 'DELETE');
    formData.append('_token', csrfToken);

    fetch(`/items/${item.id}`, {
        method: 'POST',
        headers: { 'Accept': 'application/json' },
        body: formData
    })
    .then(async r => {
        if (r.ok) {
            allItems.splice(i, 1);
            delete transactions[i];
            selectedIdx = null;
            document.getElementById('no-selection').style.display = 'flex';
            document.getElementById('item-detail').style.display  = 'none';
            renderList(allItems);
            showToast('Item deleted successfully');
        } else {
            // Show the actual error message from server if available
            let msg = 'Failed to delete item';
            try {
                const data = await r.json();
                if (data.message) msg = data.message;
            } catch(e) {}
            showToast(msg);
        }
    })
    .catch(() => showToast('Network error. Please try again.'));
}

function getTotalQty(idx) {
    const base = parseFloat(allItems[idx]?.opening_qty || 0);
    const txns = transactions[idx] || [];
    return base + txns.reduce((sum, t) => sum + (t.isAdd ? t.qty : -t.qty), 0);
}

/* ── Select item ── */
function selectItem(idx) {
    selectedIdx = idx;
    const item = allItems[idx];
    document.querySelectorAll('.il-item-row').forEach((r, i) => r.classList.toggle('active', i === idx));
    document.getElementById('no-selection').style.display = 'none';
    const detail = document.getElementById('item-detail');
    detail.style.display = 'flex';
    document.getElementById('detail-name').textContent     = item.name;
    document.getElementById('detail-sale').textContent     = item.sale_price     ? 'Rs ' + parseFloat(item.sale_price).toFixed(2)     : '—';
    document.getElementById('detail-purchase').textContent = item.purchase_price ? 'Rs ' + parseFloat(item.purchase_price).toFixed(2) : '—';
    const qty = getTotalQty(idx);
    document.getElementById('detail-stock-qty').textContent = qty;
    document.getElementById('detail-stock-val').textContent = 'Rs ' + (parseFloat(item.purchase_price || 0) * qty).toFixed(2);
    renderTxns(idx);
}

/* ── Transactions ── */
let selectedTxnIdx = null;

function renderTxns(idx) {
    const tbody = document.getElementById('txn-tbody');
    const txns  = transactions[idx] || [];
    if (!txns.length) {
        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;color:#9ca3af;padding:48px 0;font-size:13px;">No transactions to show</td></tr>`;
        return;
    }
    tbody.innerHTML = txns.map((t, ti) => `
        <tr id="txn-row-${idx}-${ti}" onclick="selectTxnRow(${idx},${ti})" ondblclick="openAdjModalForTxn(${idx},${ti})" style="cursor:pointer;user-select:none;">
            <td class="td-dot"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#111111;"></span></td>
            <td>${esc(t.type)}</td>
            <td></td>
            <td>${esc(t.details || '')}</td>
            <td>${esc(t.date)}</td>
            <td>${t.qty} ${esc(t.unit)}</td>
            <td class="td-price">${t.price ? 'Rs ' + parseFloat(t.price).toFixed(2) : '—'}</td>
            <td class="td-status">—</td>
            <td class="td-actions">
                <div class="il-row-menu-wrap">
                    <button class="il-row-menu-btn" onclick="toggleRowMenu(event,'row-menu-${idx}-${ti}')">⋮</button>
                    <div class="il-row-menu" id="row-menu-${idx}-${ti}">
                        <div class="il-row-menu-item" onclick="openAdjModalForTxn(${idx},${ti})">View/Edit</div>
                        <div class="il-row-menu-item danger" onclick="deleteTxn(${idx},${ti})">Delete</div>
                        <div class="il-row-menu-item" onclick="viewHistory(${idx},${ti})">View History</div>
                    </div>
                </div>
            </td>
        </tr>
    `).join('');
}

function selectTxnRow(idx, ti) {
    document.querySelectorAll('#txn-tbody tr').forEach(r => r.classList.remove('txn-selected'));
    const row = document.getElementById(`txn-row-${idx}-${ti}`);
    if (row) row.classList.add('txn-selected');
    selectedTxnIdx = ti;
}

function openAdjModalForTxn(idx, ti) {
    selectTxnRow(idx, ti);
    const t = transactions[idx][ti];
    document.getElementById('adj-item-name').textContent = allItems[idx].name;
    document.getElementById('adj-qty').value     = t.qty;
    document.getElementById('adj-price').value   = t.price || '';
    document.getElementById('adj-details').value = t.details || '';
    document.getElementById('adj-unit').value    = t.unit || 'Kg';
    document.getElementById('adj-toggle').checked = !t.isAdd;
    handleAdjToggle();
    if (t.date) {
        const parts = t.date.split('/');
        if (parts.length === 3) document.getElementById('adj-date').value = `${parts[2]}-${parts[1]}-${parts[0]}`;
    }
    const overlay = document.getElementById('adj-overlay');
    overlay.dataset.editIdx = idx; overlay.dataset.editTi = ti; overlay.dataset.isEdit = '1';
    overlay.classList.add('open');
}

function filterTxns(q) {
    const tbody = document.getElementById('txn-tbody');
    const txns  = (transactions[selectedIdx] || []).filter(t =>
        t.type.toLowerCase().includes(q.toLowerCase()) || (t.details || '').toLowerCase().includes(q.toLowerCase())
    );
    if (!txns.length) {
        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;color:#9ca3af;padding:48px 0;font-size:13px;">No transactions found</td></tr>`;
        return;
    }
    tbody.innerHTML = txns.map((t, ti) => `
        <tr>
            <td class="td-dot"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#111111;"></span></td>
            <td>${esc(t.type)}</td><td></td><td>${esc(t.details || '')}</td><td>${esc(t.date)}</td>
            <td>${t.qty} ${esc(t.unit)}</td>
            <td class="td-price">${t.price ? 'Rs ' + parseFloat(t.price).toFixed(2) : '—'}</td>
            <td class="td-status">—</td><td class="td-actions"></td>
        </tr>
    `).join('');
}

/* ── Column filter dropdowns ── */
function toggleColFilter(e, id) {
    e.stopPropagation();
    const rect = e.currentTarget.getBoundingClientRect();
    const dd   = document.getElementById(id);
    const isOpen = dd.classList.contains('open');
    closeAllColFilters();
    if (!isOpen) {
        dd.style.top = (rect.bottom + 6) + 'px'; dd.style.left = rect.left + 'px'; dd.style.right = 'auto';
        dd.classList.add('open');
        const ddRect = dd.getBoundingClientRect();
        if (ddRect.right > window.innerWidth - 8) dd.style.left = (window.innerWidth - ddRect.width - 8) + 'px';
    }
}
function closeAllColFilters() { document.querySelectorAll('.col-filter-dd.open').forEach(d => d.classList.remove('open')); }
function clearColFilter(id) {
    const dd = document.getElementById(id);
    dd.querySelectorAll('input[type=checkbox]').forEach(c => c.checked = false);
    dd.querySelectorAll('input[type=text], input[type=number], input[type=date]').forEach(i => i.value = '');
    dd.querySelectorAll('select').forEach(s => s.selectedIndex = 0);
    applyColFilters();
}
function applyColFilters() {
    if (selectedIdx === null) return;
    let txns = transactions[selectedIdx] || [];
    const typeChecked = [...document.querySelectorAll('#cf-type input[type=checkbox]:checked')].map(c => c.value);
    if (typeChecked.length) txns = txns.filter(t => typeChecked.includes(t.type));
    const invoiceOp = document.getElementById('cf-invoice-op')?.value;
    const invoiceVal = (document.getElementById('cf-invoice-val')?.value || '').toLowerCase();
    if (invoiceVal) txns = txns.filter(t => { const v = (t.invoice||'').toLowerCase(); return invoiceOp==='exact'?v===invoiceVal:v.includes(invoiceVal); });
    const nameOp = document.getElementById('cf-name-op')?.value;
    const nameVal = (document.getElementById('cf-name-val')?.value || '').toLowerCase();
    if (nameVal) txns = txns.filter(t => { const v = (t.details||'').toLowerCase(); return nameOp==='exact'?v===nameVal:v.includes(nameVal); });
    const dateOp = document.getElementById('cf-date-op')?.value;
    const dateVal = document.getElementById('cf-date-val')?.value;
    if (dateVal) txns = txns.filter(t => {
        if (!t.date) return false;
        const parts = t.date.split('/');
        const txnDate = parts.length===3 ? `${parts[2]}-${parts[1]}-${parts[0]}` : t.date;
        if (dateOp==='before') return txnDate < dateVal;
        if (dateOp==='after')  return txnDate > dateVal;
        return txnDate === dateVal;
    });
    const qtyOp = document.getElementById('cf-qty-op')?.value;
    const qtyVal = document.getElementById('cf-qty-val')?.value;
    if (qtyVal !== '' && qtyVal !== undefined) { const n = parseFloat(qtyVal); if (!isNaN(n)) txns = txns.filter(t => { const v=parseFloat(t.qty); if(qtyOp==='lt')return v<n; if(qtyOp==='gt')return v>n; return v===n; }); }
    const priceOp = document.getElementById('cf-price-op')?.value;
    const priceVal = document.getElementById('cf-price-val')?.value;
    if (priceVal !== '' && priceVal !== undefined) { const n = parseFloat(priceVal); if (!isNaN(n)) txns = txns.filter(t => { const v=parseFloat(t.price||0); if(priceOp==='lt')return v<n; if(priceOp==='gt')return v>n; return v===n; }); }
    const statusChecked = [...document.querySelectorAll('#cf-status input[type=checkbox]:checked')].map(c => c.value);
    if (statusChecked.length) txns = txns.filter(t => statusChecked.includes(t.status||''));
    renderFilteredTxns(txns);
}
function renderFilteredTxns(txns) {
    const idx = selectedIdx;
    const tbody = document.getElementById('txn-tbody');
    if (!txns.length) { tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;color:#9ca3af;padding:48px 0;font-size:13px;">No transactions found</td></tr>`; return; }
    tbody.innerHTML = txns.map((t, ti) => `
        <tr style="cursor:pointer;user-select:none;" onclick="selectTxnRow(${idx},${ti})">
            <td class="td-dot"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#111111;"></span></td>
            <td>${esc(t.type)}</td><td></td><td>${esc(t.details||'')}</td><td>${esc(t.date)}</td>
            <td>${t.qty} ${esc(t.unit)}</td>
            <td class="td-price">${t.price?'Rs '+parseFloat(t.price).toFixed(2):'—'}</td>
            <td class="td-status">—</td>
            <td class="td-actions">
                <div class="il-row-menu-wrap">
                    <button class="il-row-menu-btn" onclick="toggleRowMenu(event,'row-menu-f-${ti}')">⋮</button>
                    <div class="il-row-menu" id="row-menu-f-${ti}">
                        <div class="il-row-menu-item" onclick="openAdjModalForTxn(${idx},${ti})">View/Edit</div>
                        <div class="il-row-menu-item danger" onclick="deleteTxn(${idx},${ti})">Delete</div>
                    </div>
                </div>
            </td>
        </tr>
    `).join('');
}

/* ── Sort ── */
function sortTxnCol(col) {
    if (txnSortCol === col) { txnSortAsc = !txnSortAsc; } else { txnSortCol = col; txnSortAsc = true; }
    updateSortArrows(col, txnSortAsc);
    const txns = transactions[selectedIdx] || [];
    txns.sort((a, b) => {
        if (col==='qty')   return txnSortAsc ? parseFloat(a.qty)-parseFloat(b.qty) : parseFloat(b.qty)-parseFloat(a.qty);
        if (col==='price') return txnSortAsc ? parseFloat(a.price||0)-parseFloat(b.price||0) : parseFloat(b.price||0)-parseFloat(a.price||0);
        const av = (col==='type'?a.type:col==='name'?a.details:col==='date'?a.date:col==='invoice'?a.invoice:a.status)||'';
        const bv = (col==='type'?b.type:col==='name'?b.details:col==='date'?b.date:col==='invoice'?b.invoice:b.status)||'';
        return txnSortAsc ? String(av).localeCompare(String(bv)) : String(bv).localeCompare(String(av));
    });
    renderTxns(selectedIdx);
}

/* ── Row menu ── */
function toggleRowMenu(e, id) {
    e.stopPropagation();
    const btn = e.currentTarget; const rect = btn.getBoundingClientRect();
    document.querySelectorAll('.il-row-menu.open').forEach(m => { if(m.id!==id) m.classList.remove('open'); });
    const menu = document.getElementById(id); const isOpen = menu.classList.contains('open');
    menu.classList.remove('open');
    if (!isOpen) {
        menu.style.top = (rect.bottom + window.scrollY + 2) + 'px'; menu.style.left = rect.left + 'px';
        menu.classList.add('open');
        requestAnimationFrame(() => { const mRect = menu.getBoundingClientRect(); menu.style.left = (rect.right - mRect.width) + 'px'; if(parseFloat(menu.style.left)<0) menu.style.left='4px'; });
    }
}
function deleteTxn(idx, ti) {
    if (!confirm('Delete this transaction?')) return;
    transactions[idx].splice(ti, 1); selectItem(idx); renderList(allItems); showToast('Transaction deleted');
}
function viewHistory(idx, ti) { alert('View History for transaction ' + (ti+1)); }

/* ── Dropdowns ── */
function toggleAddDD(e) { e.stopPropagation(); document.getElementById('add-dd').classList.toggle('open'); closeBulkDD(); }
function closeAddDD()   { document.getElementById('add-dd')?.classList.remove('open'); }
function toggleBulkDD(e) { e.stopPropagation(); document.getElementById('bulk-dd').classList.toggle('open'); closeAddDD(); }
function closeBulkDD()   { document.getElementById('bulk-dd')?.classList.remove('open'); }
function bulkAction(action) { closeBulkDD(); alert('Bulk action: ' + action); }
function toggleFilterDD(e) { e.stopPropagation(); document.getElementById('filter-dd').classList.toggle('open'); }
function closeFilterDD()   { document.getElementById('filter-dd')?.classList.remove('open'); }
function applyFilter()     { renderList(allItems); }
function clearFilter()     {
    document.getElementById('filter-active').checked   = true;
    document.getElementById('filter-inactive').checked = false;
    renderList(allItems);
}
function sortByQty() {
    sortAsc = !sortAsc;
    allItems.sort((a, b) => sortAsc ? parseFloat(a.opening_qty||0)-parseFloat(b.opening_qty||0) : parseFloat(b.opening_qty||0)-parseFloat(a.opening_qty||0));
    const arrow = document.getElementById('qty-sort-arrow');
    if (arrow) arrow.textContent = sortAsc ? '↑' : '↓';
    renderList(allItems);
}
function goToAddItem() { window.location.href = '/items/create'; }

/* ── Excel Export ── */
function exportToExcel() {
    if (selectedIdx === null) { showToast('Please select an item first.'); return; }
    const item = allItems[selectedIdx]; const txns = transactions[selectedIdx] || [];
    const summaryData = [
        ['Item Summary'],[],
        ['Item Name', item.name||'—'],
        ['Sale Price', item.sale_price ? 'Rs '+parseFloat(item.sale_price).toFixed(2) : '—'],
        ['Purchase Price', item.purchase_price ? 'Rs '+parseFloat(item.purchase_price).toFixed(2) : '—'],
        ['Stock Quantity', getTotalQty(selectedIdx)],
        ['Stock Value', 'Rs '+(parseFloat(item.purchase_price||0)*getTotalQty(selectedIdx)).toFixed(2)],
        ['Exported On', new Date().toLocaleDateString('en-GB')],
    ];
    const txnHeader = ['#','Type','Invoice/Ref.','Name','Date','Quantity','Unit','Price/Unit (Rs)','Status'];
    const txnRows = txns.length ? txns.map((t,i)=>[i+1,t.type,'—',t.details||'—',t.date,t.qty,t.unit,t.price?parseFloat(t.price).toFixed(2):'—','—']) : [['No transactions recorded']];
    const wb = XLSX.utils.book_new();
    const wsSummary = XLSX.utils.aoa_to_sheet(summaryData); wsSummary['!cols']=[{wch:20},{wch:24}];
    XLSX.utils.book_append_sheet(wb, wsSummary, 'Summary');
    const wsTxn = XLSX.utils.aoa_to_sheet([txnHeader,...txnRows]); wsTxn['!cols']=[{wch:4},{wch:18},{wch:14},{wch:14},{wch:12},{wch:10},{wch:8},{wch:16},{wch:10}];
    XLSX.utils.book_append_sheet(wb, wsTxn, 'Transactions');
    const dateStr = new Date().toISOString().slice(0,10);
    const safeName = (item.name||'item').replace(/[^a-zA-Z0-9_\-]/g,'_');
    XLSX.writeFile(wb, `${safeName}_${dateStr}.xlsx`);
    showToast(`Downloaded: ${safeName}_${dateStr}.xlsx`);
}

/* ── Stock Adjustment Modal ── */
function openAdjModal() {
    if (selectedIdx === null) return;
    document.getElementById('adj-item-name').textContent = allItems[selectedIdx].name;
    document.getElementById('adj-qty').value = ''; document.getElementById('adj-price').value = '';
    document.getElementById('adj-details').value = ''; document.getElementById('adj-toggle').checked = false;
    handleAdjToggle();
    const d = new Date();
    document.getElementById('adj-date').value = d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
    const overlay = document.getElementById('adj-overlay');
    overlay.dataset.isEdit='0'; overlay.dataset.editIdx=''; overlay.dataset.editTi='';
    overlay.classList.add('open');
}
function closeAdjModal() { document.getElementById('adj-overlay').classList.remove('open'); }
function handleAdjToggle() {
    const isReduce = document.getElementById('adj-toggle').checked;
    document.getElementById('lbl-add-stock').classList.toggle('inactive', isReduce);
    document.getElementById('lbl-reduce-stock').classList.toggle('inactive', !isReduce);
}
function saveAdjustment() {
    const qty = parseFloat(document.getElementById('adj-qty').value);
    if (!qty || qty <= 0) { showToast('Please enter a valid quantity.'); return; }
    const price = document.getElementById('adj-price').value;
    const unit  = document.getElementById('adj-unit').value;
    const date  = document.getElementById('adj-date').value;
    const details = document.getElementById('adj-details').value;
    const isAdd = !document.getElementById('adj-toggle').checked;
    const overlay = document.getElementById('adj-overlay');
    const isEdit = overlay.dataset.isEdit === '1';
    const editIdx = parseInt(overlay.dataset.editIdx); const editTi = parseInt(overlay.dataset.editTi);
    const txnData = { type: isAdd?'Add Adjustment':'Reduce Adjustment', qty, unit, price, details, date: formatDate(date), isAdd };
    if (isEdit && !isNaN(editIdx) && !isNaN(editTi)) {
        transactions[editIdx][editTi] = txnData; showToast('Transaction updated');
    } else {
        if (!transactions[selectedIdx]) transactions[selectedIdx] = [];
        transactions[selectedIdx].push(txnData); showToast('Stock adjustment saved');
    }
    overlay.dataset.isEdit='0'; overlay.dataset.editIdx=''; overlay.dataset.editTi='';
    closeAdjModal(); selectItem(selectedIdx); renderList(allItems);
}
function formatDate(d) { if(!d)return''; const[y,m,day]=d.split('-'); return day+'/'+m+'/'+y; }
function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
@endpush