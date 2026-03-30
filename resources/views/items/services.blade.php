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
.il-tab.active { color: #2563eb !important; border-bottom-color: #2563eb !important; }

/* ── BODY ── */
.il-body { display: flex; flex: 1; min-height: 0; overflow: hidden; }

/* ── LEFT PANEL ── */
.il-left {
    width: 320px; flex-shrink: 0;
    border-right: 1px solid #e5e7eb;
    display: flex; flex-direction: column; background: #fff;
}

/* Bulk banner */
.bulk-banner {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 14px; background: #f0f4ff;
    border-bottom: 1px solid #e5e7eb; cursor: pointer;
    flex-shrink: 0;
}
.bulk-banner:hover { background: #e8effe; }
.bulk-banner-icon {
    width: 32px; height: 32px; background: #e53e3e; border-radius: 6px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.bulk-banner-text { flex: 1; }
.bulk-banner-title { font-size: 12px; font-weight: 700; color: #1a1a1a; }
.bulk-banner-sub { font-size: 11px; color: #6b7280; }

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

.il-add-btn {
    display: inline-flex; align-items: center; gap: 6px;
    background: #f59e0b; color: #fff; border: none;
    border-radius: 6px; padding: 8px 16px;
    font-size: 13px; font-weight: 600; cursor: pointer;
    transition: background .15s; white-space: nowrap; height: 36px;
    margin-left: auto;
}
.il-add-btn:hover { background: #d97706; }

/* Three dots */
.bulk-more-wrap { position: relative; flex-shrink: 0; }
.bulk-more-btn {
    background: none; border: 1.5px solid #e5e7eb;
    border-radius: 6px; width: 32px; height: 32px;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: #6b7280; transition: border-color .15s;
}
.bulk-more-btn:hover { border-color: #93c5fd; }
.bulk-dd {
    position: absolute; top: calc(100% + 4px); right: 0;
    background: #fff; border: 1px solid #e5e7eb;
    border-radius: 6px; box-shadow: 0 6px 20px rgba(0,0,0,.12);
    z-index: 500; min-width: 190px; display: none;
}
.bulk-dd.open { display: block; }
.bulk-dd-item {
    padding: 11px 16px; cursor: pointer; font-size: 13px; color: #374151;
}
.bulk-dd-item:hover { background: #f9fafb; }

.il-list-header {
    display: flex; align-items: center;
    padding: 9px 14px; background: #f9fafb;
    border-bottom: 1px solid #f3f4f6;
    font-size: 11px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .06em; color: #9ca3af;
}
.col-item { flex: 1; display: flex; align-items: center; gap: 6px; }
.col-price-hdr { width: 80px; text-align: right; }

.il-list { flex: 1; overflow-y: auto; }
.il-list::-webkit-scrollbar { width: 4px; }
.il-list::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

.il-item-row {
    display: flex; align-items: center; padding: 12px 14px;
    border-bottom: 1px solid #f8f9fa; cursor: pointer; transition: background .12s;
    position: relative;
}
.il-item-row:hover { background: #f9fafb; }
.il-item-row.active { background: #eff6ff; }
.il-item-dot { width: 8px; height: 8px; border-radius: 50%; background: #9ca3af; margin-right: 8px; flex-shrink: 0; }
.il-item-name { flex: 1; font-size: 14px; color: #111827; font-weight: 500; }
.il-item-price { width: 70px; text-align: right; font-size: 13px; color: #16a34a; font-weight: 600; }

.il-item-more-wrap { position: relative; width: 24px; height: 24px; flex-shrink: 0; }
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
.il-item-dd-item { padding: 11px 16px; cursor: pointer; font-size: 13px; color: #374151; }
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

.il-stats {
    display: flex; align-items: stretch;
    border-bottom: 1px solid #f3f4f6; flex-shrink: 0;
}
.il-stat-left {
    display: flex; flex-direction: column;
    padding: 10px 24px; gap: 5px; flex: 1; justify-content: center;
}
.il-stat-item { display: flex; align-items: center; gap: 6px; }
.il-stat-label { font-size: 12px; color: #6b7280; font-weight: 500; text-transform: uppercase; letter-spacing: .04em; }
.il-stat-value { font-size: 13px; font-weight: 700; color: #16a34a; }

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

.il-export-btn {
    background: none; border: none; cursor: pointer; padding: 2px;
    display: flex; align-items: center; justify-content: center;
}
.il-export-btn .excel-icon {
    width: 26px; height: 26px; background: #1d6fcc; border-radius: 4px;
    display: flex; align-items: center; justify-content: center;
    color: #fff; font-size: 13px; font-weight: 800;
}

.il-tbl-wrap { flex: 1; overflow-y: auto; overflow-x: auto; }
.il-tbl-wrap::-webkit-scrollbar { width: 4px; height: 4px; }
.il-tbl-wrap::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

.il-tbl { width: 100%; border-collapse: collapse; table-layout: fixed; }
.il-tbl th {
    padding: 12px 16px; font-size: 12px; font-weight: 500;
    text-transform: capitalize; color: #9ca3af;
    background: #f9fafb; border-bottom: 1px solid #ebebeb;
    border-right: 1px solid #d1d5db;
    text-align: left; white-space: nowrap;
    position: relative; overflow: hidden; user-select: none;
}
.il-tbl th[data-col="dot"] { padding: 0; }
.il-tbl th .th-inner { display: inline-flex; align-items: center; gap: 3px; cursor: pointer; }
.th-sort-arrow {
    display: inline-flex; align-items: center;
    color: #4a4a4a; flex-shrink: 0; font-size: 10px; font-style: normal;
    opacity: 0; transition: opacity .1s; line-height: 1;
}
.il-tbl th.sort-asc  .th-sort-arrow,
.il-tbl th.sort-desc .th-sort-arrow { opacity: 1; }
.th-sort-arrow::after { content: '↑'; }
.il-tbl th.sort-desc .th-sort-arrow::after { content: '↓'; }
.il-tbl th .th-filter-icon {
    color: #b8bec7; flex-shrink: 0; cursor: pointer; transition: color .15s; font-size: 10px;
}
.il-tbl th .th-filter-icon:hover  { color: #e53e3e; }
.il-tbl th .th-filter-icon.active { color: #e53e3e; }

.col-resize-handle {
    position: absolute; right: 0; top: 0; bottom: 0;
    width: 5px; cursor: col-resize; z-index: 10;
}
.col-resize-handle:hover,
.col-resize-handle.resizing { background: #2563eb; opacity: .4; }

.il-tbl td {
    padding: 12px 10px; font-size: 13px; color: #374151; font-weight: 400;
    border-bottom: 1px solid #f3f4f6; vertical-align: middle;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.il-tbl td.td-dot { padding: 0 0 0 10px; width: 28px; vertical-align: middle; }
.il-tbl td.td-price { color: #16a34a; }
.il-tbl td.td-status { color: #9ca3af; }
.il-tbl td.td-actions { padding: 2px 4px; width: 36px; }
.il-tbl tbody tr:hover td { background: #fafafa; }
.il-tbl tbody tr.txn-selected td { background: #dbeafe; }
.il-tbl tbody tr:last-child td { border-bottom: none; }

.il-row-menu-wrap { position: relative; }
.il-row-menu-btn {
    background: none; border: none; cursor: pointer; color: #9ca3af;
    padding: 4px 6px; border-radius: 4px; font-size: 18px; line-height: 1;
}
.il-row-menu-btn:hover { color: #374151; background: #f3f4f6; }
.il-row-menu {
    position: fixed; background: #fff; border: 1px solid #e5e7eb;
    border-radius: 6px; box-shadow: 0 6px 20px rgba(0,0,0,.12);
    z-index: 9000; min-width: 150px; display: none;
}
.il-row-menu.open { display: block; }
.il-row-menu-item { padding: 11px 16px; cursor: pointer; font-size: 13px; color: #374151; }
.il-row-menu-item:hover { background: #f9fafb; }
.il-row-menu-item.danger { color: #ef4444; }
.il-row-menu-item.danger:hover { background: #fef2f2; }

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

.il-empty-wrap { flex: 1; display: flex; align-items: center; justify-content: center; background: #fff; }
.il-empty-content { display: flex; flex-direction: column; align-items: center; gap: 18px; text-align: center; }
.il-illustration { width: 220px; height: 220px; background: #dbeafe; border-radius: 50%; position: relative; }
.il-icon-card {
    position: absolute; background: #fff;
    border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,.13);
    display: flex; align-items: center; justify-content: center;
}

/* Bulk modal */
.bulk-overlay {
    position: fixed; inset: 0; z-index: 2000;
    background: rgba(0,0,0,.45);
    display: none; align-items: center; justify-content: center;
}
.bulk-overlay.open { display: flex; }
.bulk-modal {
    background: #fff; border-radius: 10px;
    box-shadow: 0 10px 40px rgba(0,0,0,.2);
    width: 500px; max-width: 95vw;
    animation: popIn .15s ease-out;
}
@keyframes popIn { from{opacity:0;transform:scale(.96)} to{opacity:1;transform:scale(1)} }
.bulk-modal-hdr {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px; border-bottom: 1px solid #f3f4f6;
}
.bulk-modal-title { font-size: 17px; font-weight: 700; color: #111827; }
.bulk-modal-close {
    background: none; border: none; cursor: pointer;
    font-size: 20px; color: #9ca3af; width: 28px; height: 28px;
    display: flex; align-items: center; justify-content: center; border-radius: 5px;
}
.bulk-modal-close:hover { background: #f3f4f6; color: #374151; }
.bulk-modal-search {
    width: 100%; border: 1.5px solid #3b82f6; border-radius: 7px;
    padding: 10px 14px 10px 36px; font-size: 13px; outline: none;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='none' viewBox='0 0 24 24' stroke='%239ca3af' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath stroke-linecap='round' d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat 11px center;
}
.bulk-modal-search:focus { border-color: #2563eb; }
.bulk-modal-search::placeholder { color: #9ca3af; }
.bulk-info-bar {
    display: flex; align-items: center; gap: 8px;
    padding: 10px 24px; background: #f0f7ff; border-top: 1px solid #f3f4f6;
    font-size: 12px; color: #374151;
}

#delete-overlay {
    position: fixed; inset: 0; z-index: 2000;
    background: rgba(0,0,0,.45);
    display: none; align-items: center; justify-content: center;
}
#delete-overlay.open { display: flex; }
#delete-modal {
    background: #fff; border-radius: 8px;
    box-shadow: 0 10px 40px rgba(0,0,0,.25);
    width: 420px; max-width: 95vw; animation: popIn .15s ease-out;
}
.del-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 20px 14px; background: #e8f0fb; border-radius: 8px 8px 0 0;
}
.del-header-title { font-size: 15px; font-weight: 700; color: #1a2a4a; }
.del-header-close {
    background: none; border: none; cursor: pointer;
    font-size: 18px; color: #6b7280; line-height: 1;
    width: 24px; height: 24px; display: flex; align-items: center; justify-content: center; border-radius: 4px;
}
.del-header-close:hover { background: #d1d5db; color: #111; }
.del-body { padding: 22px 24px 20px; }
.del-body p { font-size: 14px; font-weight: 600; color: #1a2a4a; }
.del-footer { display: flex; justify-content: flex-end; gap: 12px; padding: 14px 20px 18px; }
.del-btn-yes, .del-btn-no {
    background: #5b9bd5; border: none; border-radius: 5px;
    padding: 9px 28px; font-size: 14px; font-weight: 600; color: #fff; cursor: pointer;
}
.del-btn-yes:hover, .del-btn-no:hover { background: #3a7bbf; }

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
        <div class="il-tab" onclick="window.location.href='{{ route("items") }}'">PRODUCTS</div>
        <div class="il-tab active" onclick="window.location.href='{{ route("items.services") }}'">SERVICES</div>
        <div class="il-tab" onclick="window.location.href='{{ route("items.category") }}'">CATEGORY</div>
        <div class="il-tab" onclick="window.location.href='{{ route("items.units") }}'">UNITS</div>
    </div>

    @if(count($services) === 0)

    <div class="il-empty-wrap">
        <div class="il-empty-content">
            <div class="il-illustration">
                <div class="il-icon-card" style="width:52px;height:52px;top:14px;left:50%;transform:translateX(-50%);font-size:26px;">🧺</div>
                <div class="il-icon-card" style="width:48px;height:48px;top:50%;left:8px;transform:translateY(-50%);font-size:22px;">🖨️</div>
                <div class="il-icon-card" style="width:48px;height:48px;top:50%;right:8px;transform:translateY(-50%);font-size:22px;">🫖</div>
                <div class="il-icon-card" style="width:46px;height:46px;bottom:24px;left:26px;font-size:20px;">🧵</div>
                <div class="il-icon-card" style="width:46px;height:46px;bottom:24px;right:26px;font-size:20px;">📦</div>
                <div class="il-icon-card" style="width:54px;height:54px;top:50%;left:50%;transform:translate(-50%,-50%);font-size:26px;box-shadow:0 4px 16px rgba(0,0,0,.16);z-index:2;border-radius:12px;">📋</div>
            </div>
            <p style="font-size:14px;color:#6b7280;max-width:420px;line-height:1.65;">
                Add services you provide to your customers and create Sale invoices for them faster.
            </p>
            <button onclick="window.location.href='{{ route("items.create") }}?type=service'" style="display:inline-flex;align-items:center;background:#f59e0b;color:#fff;border:none;border-radius:6px;padding:13px 32px;font-size:14px;font-weight:600;cursor:pointer;">
                Add Your First Service
            </button>
        </div>
    </div>

    @else

    <div class="il-body">

        {{-- LEFT PANEL --}}
        <div class="il-left">

            {{-- Bulk Items Update Banner --}}
            <div class="bulk-banner" onclick="openBulkModal('bulk-update')">
                <div class="bulk-banner-icon">
                    <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="bulk-banner-text">
                    <div class="bulk-banner-title">Bulk Items Update</div>
                    <div class="bulk-banner-sub">Update/Edit multiple items at a time.</div>
                </div>
                <svg width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>

            <div class="il-left-toolbar">
                <button class="il-search-btn" onclick="toggleSearch()" title="Search">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                </button>
                <div class="il-search-wrap" id="search-wrap">
                    <svg class="il-search-icon" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                    <input type="text" class="il-search-input" id="search-input" placeholder="Search services..." oninput="filterItems()"/>
                </div>
                <button class="il-add-btn" onclick="window.location.href='{{ route("items.create") }}?type=service'">
                    <svg width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.8"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                    Add Service
                </button>

                {{-- Three dots --}}
                <div class="bulk-more-wrap">
                    <button class="bulk-more-btn" onclick="toggleBulkDD(event)" title="More options">
                        <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="5" r="1"/><circle cx="12" cy="12" r="1"/><circle cx="12" cy="19" r="1"/>
                        </svg>
                    </button>
                    <div class="bulk-dd" id="bulk-dd">
                        <div class="bulk-dd-item" onclick="openBulkModal('inactive')">Bulk Inactive</div>
                        <div class="bulk-dd-item" onclick="openBulkModal('active')">Bulk Active</div>
                        <div class="bulk-dd-item" onclick="openBulkModal('assign-code')">Bulk Assign Code</div>
                        <div class="bulk-dd-item" onclick="openBulkModal('assign-units')">Assign Units</div>
                        <div class="bulk-dd-item" onclick="openBulkModal('bulk-update')">Bulk Update Items</div>
                    </div>
                </div>
            </div>

            <div class="il-list-header">
                <span class="col-item">
                    ITEM
                    <i class="fa-solid fa-filter" style="color:#e53e3e;font-size:11px;cursor:pointer;"></i>
                </span>
                <span class="col-price-hdr">PRICE</span>
                <span style="width:24px;"></span>
            </div>

            <div class="il-list" id="items-list"></div>
        </div>

        {{-- RIGHT PANEL --}}
        <div class="il-right">

            <div class="il-no-selection" id="no-selection">
                <div class="il-no-sel-icon">
                    <svg width="28" height="28" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="1.5"><path stroke-linecap="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                </div>
                <div style="font-size:15px;color:#6b7280;font-weight:500;">Select a service to view details</div>
                <div style="font-size:13px;color:#9ca3af;">or add a new service using the button above</div>
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
                </div>

                <div class="il-stats">
                    <div class="il-stat-left">
                        <div class="il-stat-item">
                            <span class="il-stat-label">SALE PRICE:</span>
                            <span class="il-stat-value" id="detail-sale">—</span>
                        </div>
                        <div class="il-stat-item">
                            <span class="il-stat-label">CATEGORY:</span>
                            <span class="il-stat-value" id="detail-category" style="color:#374151;">—</span>
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

{{-- COLUMN FILTER DROPDOWNS --}}
<div class="col-filter-dd" id="cf-type" onclick="event.stopPropagation()">
    <div class="cfd-title">Select Category</div>
    <label class="cfd-cb-row"><input type="checkbox" value="Sale" onchange="applyColFilters()"> Sale</label>
    <label class="cfd-cb-row"><input type="checkbox" value="Sale (e-Invoice)" onchange="applyColFilters()"> Sale (e-Invoice)</label>
    <label class="cfd-cb-row"><input type="checkbox" value="Purchase" onchange="applyColFilters()"> Purchase</label>
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
    <select class="cfd-select" id="cf-qty-op"><option value="equal">Equal to</option><option value="lt">Less Than</option><option value="gt">Greater Than</option></select>
    <input type="number" class="cfd-input" id="cf-qty-val" placeholder="QUANTITY" oninput="applyColFilters()"/>
    <div class="cfd-actions">
        <button class="cfd-clear" onclick="clearColFilter('cf-qty')">Clear</button>
        <button class="cfd-apply" onclick="applyColFilters();closeAllColFilters()">Apply</button>
    </div>
</div>
<div class="col-filter-dd" id="cf-price" onclick="event.stopPropagation()">
    <div class="cfd-title">Select Category</div>
    <select class="cfd-select" id="cf-price-op"><option value="equal">Equal to</option><option value="lt">Less Than</option><option value="gt">Greater Than</option></select>
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

{{-- BULK MODAL --}}
<div class="bulk-overlay" id="bulk-overlay" onclick="if(event.target===this)closeBulkModal()">
    <div class="bulk-modal" onclick="event.stopPropagation()">
        <div class="bulk-modal-hdr">
            <span class="bulk-modal-title" id="bulk-modal-title">Bulk Action</span>
            <button class="bulk-modal-close" onclick="closeBulkModal()">✕</button>
        </div>
        <div style="padding:14px 24px;">
            <input class="bulk-modal-search" id="bulk-search" placeholder="Search items..."
                oninput="document.querySelectorAll('#bulk-tbody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(this.value.toLowerCase())?'':'none')"/>
        </div>
        <div style="max-height:300px;overflow-y:auto;border-top:1px solid #f3f4f6;">
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr>
                        <th style="width:44px;padding:10px 16px;border-bottom:1px solid #f3f4f6;">
                            <input type="checkbox" id="bulk-check-all" style="width:15px;height:15px;accent-color:#2563eb;" onchange="toggleAllBulk(this)">
                        </th>
                        <th style="padding:10px 16px;font-size:11px;color:#9ca3af;text-align:left;border-bottom:1px solid #f3f4f6;font-weight:700;letter-spacing:.06em;">ITEM NAME</th>
                        <th style="width:100px;padding:10px 16px;font-size:11px;color:#9ca3af;text-align:right;border-bottom:1px solid #f3f4f6;font-weight:700;letter-spacing:.06em;">QUANTITY</th>
                    </tr>
                </thead>
                <tbody id="bulk-tbody"></tbody>
            </table>
        </div>
        <div class="bulk-info-bar">
            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
            <span id="bulk-info-text">Showing all services</span>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:10px;padding:14px 24px;border-top:1px solid #f3f4f6;">
            <button onclick="closeBulkModal()" style="background:#f3f4f6;border:none;border-radius:7px;padding:10px 24px;font-size:13px;font-weight:600;cursor:pointer;color:#374151;">Cancel</button>
            <button id="bulk-action-btn" style="background:#e53e3e;color:#fff;border:none;border-radius:7px;padding:10px 24px;font-size:13px;font-weight:700;cursor:pointer;">Apply</button>
        </div>
    </div>
</div>

{{-- DELETE MODAL --}}
<div id="delete-overlay">
    <div id="delete-modal" onclick="event.stopPropagation()">
        <div class="del-header">
            <span class="del-header-title">Are you sure you want to delete this Service?</span>
            <button class="del-header-close" onclick="closeDeleteModal()">✕</button>
        </div>
        <div class="del-body"><p>This Service will be Deleted.</p></div>
        <div class="del-footer">
            <button class="del-btn-yes" onclick="confirmDelete()">YES</button>
            <button class="del-btn-no" onclick="closeDeleteModal()">NO</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
let allItems     = @json($services ?? []);
let transactions = {};
let selectedIdx  = null;
let txnSortCol   = null;
let txnSortAsc   = true;

/* ── Column resize ── */
(function initColResize() {
    let isResizing = false, startX = 0, startW = 0, th = null, handle = null;
    document.addEventListener('mousedown', function(e) {
        if (!e.target.classList.contains('col-resize-handle')) return;
        e.preventDefault();
        handle = e.target; th = handle.closest('th');
        isResizing = true; startX = e.clientX; startW = th.offsetWidth;
        handle.classList.add('resizing');
        document.body.style.cursor = 'col-resize'; document.body.style.userSelect = 'none';
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

function updateSortArrows(col, asc) {
    document.querySelectorAll('#txn-thead-row th').forEach(th => th.classList.remove('sort-asc','sort-desc'));
    if (!col) return;
    const th = document.querySelector(`#txn-thead-row th[data-col="${col}"]`);
    if (th) th.classList.add(asc ? 'sort-asc' : 'sort-desc');
}

document.addEventListener('DOMContentLoaded', () => {
    renderList(allItems);
    if (allItems.length > 0) selectItem(0);
    document.addEventListener('click', () => {
        closeSharePopup();
        closeAllColFilters();
        document.getElementById('bulk-dd')?.classList.remove('open');
        document.querySelectorAll('.il-row-menu.open, .il-item-dd.open').forEach(m => m.classList.remove('open'));
    });
});

function showToast(msg) {
    const t = document.getElementById('toast');
    t.textContent = msg; t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}

/* ── Bulk dropdown ── */
function toggleBulkDD(e) {
    e.stopPropagation();
    document.getElementById('bulk-dd').classList.toggle('open');
}

/* ── Bulk modal ── */
const bulkConfig = {
    'inactive':    { title: 'Bulk Inactive',    btnLabel: 'Mark as Inactive', info: 'Showing only active items' },
    'active':      { title: 'Bulk Active',      btnLabel: 'Mark as Active',   info: 'Showing only inactive items' },
    'assign-code': { title: 'Bulk Assign Code', btnLabel: 'Assign Code',      info: "Showing items that don't have item code" },
    'assign-units':{ title: 'Assign Units',     btnLabel: 'Assign Units',     info: 'Showing all services' },
    'bulk-update': { title: 'Bulk Items Update',btnLabel: 'Update Items',     info: 'Showing all services' },
};

function openBulkModal(type) {
    document.getElementById('bulk-dd')?.classList.remove('open');
    const cfg = bulkConfig[type] || { title: 'Bulk Action', btnLabel: 'Apply', info: 'Showing all services' };
    document.getElementById('bulk-modal-title').textContent = cfg.title;
    document.getElementById('bulk-action-btn').textContent  = cfg.btnLabel;
    document.getElementById('bulk-info-text').textContent   = cfg.info;
    document.getElementById('bulk-search').value = '';
    document.getElementById('bulk-check-all').checked = false;

    const tbody = document.getElementById('bulk-tbody');
    if (!allItems.length) {
        tbody.innerHTML = `
            <tr><td colspan="3" style="text-align:center;padding:50px 0;">
                <div style="display:flex;flex-direction:column;align-items:center;gap:12px;color:#9ca3af;">
                    <div style="width:70px;height:70px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:32px;">📋</div>
                    <span style="font-size:14px;">No Items to Show</span>
                </div>
            </td></tr>`;
    } else {
        tbody.innerHTML = allItems.map((item, i) => `
            <tr>
                <td style="width:44px;padding:10px 16px;">
                    <input type="checkbox" data-idx="${i}" style="width:15px;height:15px;accent-color:#2563eb;">
                </td>
                <td style="font-size:14px;color:#111827;padding:10px 16px;">${esc(item.name)}</td>
                <td style="width:100px;text-align:right;font-size:14px;color:#16a34a;padding:10px 16px;">
                    ${item.sale_price ? 'Rs ' + parseFloat(item.sale_price).toFixed(2) : '—'}
                </td>
            </tr>`).join('');
    }

    document.getElementById('bulk-overlay').classList.add('open');
}

function closeBulkModal() {
    document.getElementById('bulk-overlay').classList.remove('open');
}

function toggleAllBulk(el) {
    document.querySelectorAll('#bulk-tbody input[type=checkbox]').forEach(cb => cb.checked = el.checked);
}

/* ── Share popup ── */
function toggleSharePopup(e) { e.stopPropagation(); document.getElementById('share-popup').classList.toggle('open'); }
function closeSharePopup() { document.getElementById('share-popup')?.classList.remove('open'); }
function shareVia(method) {
    closeSharePopup();
    const name = allItems[selectedIdx]?.name || 'service';
    if (method === 'copy') { navigator.clipboard.writeText(window.location.href).then(() => showToast('Link copied!')); }
    else if (method === 'email') { window.open(`mailto:?subject=Service: ${name}&body=${window.location.href}`); }
    else if (method === 'whatsapp') { window.open(`https://wa.me/?text=Service: ${name} - ${window.location.href}`); }
    else if (method === 'sms') { window.open(`sms:?body=Service: ${name}`); }
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
        c.innerHTML = `<div style="padding:32px 16px;text-align:center;color:#9ca3af;font-size:13px;">No services found</div>`;
        return;
    }
    c.innerHTML = items.map((item, i) => `
        <div class="il-item-row ${selectedIdx === i ? 'active' : ''}" onclick="selectItem(${i})">
            <span class="il-item-dot"></span>
            <span class="il-item-name">${esc(item.name)}</span>
            <span class="il-item-price">${item.sale_price ? 'Rs ' + parseFloat(item.sale_price).toFixed(2) : '—'}</span>
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
function editItemNav(i) { window.location.href = '{{ url("dashboard/items") }}/' + (allItems[i].id || i) + '/edit'; }

/* ── Delete modal ── */
let deleteTargetIdx = null;
function deleteItem(i) {
    deleteTargetIdx = i;
    document.getElementById('delete-overlay').classList.add('open');
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
    const formData = new FormData();
    formData.append('_method', 'DELETE');
    formData.append('_token', csrfToken);
    fetch(`{{ url("dashboard/items") }}/${item.id}`, { method: 'POST', headers: { 'Accept': 'application/json' }, body: formData })
    .then(async r => {
        if (r.ok) {
            allItems.splice(i, 1);
            selectedIdx = null;
            document.getElementById('no-selection').style.display = 'flex';
            document.getElementById('item-detail').style.display  = 'none';
            renderList(allItems);
            showToast('Service deleted successfully');
        } else {
            let msg = 'Failed to delete service';
            try { const data = await r.json(); if (data.message) msg = data.message; } catch(e) {}
            showToast(msg);
        }
    })
    .catch(() => showToast('Network error. Please try again.'));
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
    document.getElementById('detail-sale').textContent     = item.sale_price ? 'Rs ' + parseFloat(item.sale_price).toFixed(2) : '—';
    document.getElementById('detail-category').textContent = item.category || '—';
    renderTxns(idx);
}

/* ── Transactions ── */
function renderTxns(idx) {
    const tbody = document.getElementById('txn-tbody');
    const txns  = transactions[idx] || [];
    if (!txns.length) {
        tbody.innerHTML = `<tr><td colspan="9" style="text-align:center;color:#9ca3af;padding:48px 0;font-size:13px;">No transactions to show</td></tr>`;
        return;
    }
    tbody.innerHTML = txns.map((t, ti) => `
        <tr id="txn-row-${idx}-${ti}" onclick="selectTxnRow(${idx},${ti})" style="cursor:pointer;user-select:none;">
            <td class="td-dot"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#111111;"></span></td>
            <td>${esc(t.type)}</td><td></td>
            <td>${esc(t.details || '')}</td>
            <td>${esc(t.date)}</td>
            <td>${t.qty || '—'} ${esc(t.unit || '')}</td>
            <td class="td-price">${t.price ? 'Rs ' + parseFloat(t.price).toFixed(2) : '—'}</td>
            <td class="td-status">—</td>
            <td class="td-actions">
                <div class="il-row-menu-wrap">
                    <button class="il-row-menu-btn" onclick="toggleRowMenu(event,'row-menu-${idx}-${ti}')">⋮</button>
                    <div class="il-row-menu" id="row-menu-${idx}-${ti}">
                        <div class="il-row-menu-item danger" onclick="deleteTxn(${idx},${ti})">Delete</div>
                    </div>
                </div>
            </td>
        </tr>`).join('');
}

function selectTxnRow(idx, ti) {
    document.querySelectorAll('#txn-tbody tr').forEach(r => r.classList.remove('txn-selected'));
    const row = document.getElementById(`txn-row-${idx}-${ti}`);
    if (row) row.classList.add('txn-selected');
}
function filterTxns(q) { renderTxns(selectedIdx); }
function sortTxnCol(col) {
    if (txnSortCol === col) { txnSortAsc = !txnSortAsc; } else { txnSortCol = col; txnSortAsc = true; }
    updateSortArrows(col, txnSortAsc);
    renderTxns(selectedIdx);
}
function toggleRowMenu(e, id) {
    e.stopPropagation();
    const btn = e.currentTarget; const rect = btn.getBoundingClientRect();
    document.querySelectorAll('.il-row-menu.open').forEach(m => { if(m.id!==id) m.classList.remove('open'); });
    const menu = document.getElementById(id); const isOpen = menu.classList.contains('open');
    menu.classList.remove('open');
    if (!isOpen) {
        menu.style.top = (rect.bottom + window.scrollY + 2) + 'px'; menu.style.left = rect.left + 'px';
        menu.classList.add('open');
        requestAnimationFrame(() => { const mRect = menu.getBoundingClientRect(); menu.style.left = (rect.right - mRect.width) + 'px'; });
    }
}
function deleteTxn(idx, ti) {
    if (!confirm('Delete this transaction?')) return;
    transactions[idx].splice(ti, 1); selectItem(idx); showToast('Transaction deleted');
}

/* ── Column filters ── */
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
function applyColFilters() { if (selectedIdx !== null) renderTxns(selectedIdx); }

/* ── Export ── */
function exportToExcel() {
    if (selectedIdx === null) { showToast('Please select a service first.'); return; }
    const item = allItems[selectedIdx]; const txns = transactions[selectedIdx] || [];
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet([['Service Name', item.name||'—'],['Sale Price', item.sale_price||'—']]);
    XLSX.utils.book_append_sheet(wb, ws, 'Summary');
    const dateStr = new Date().toISOString().slice(0,10);
    const safeName = (item.name||'service').replace(/[^a-zA-Z0-9_\-]/g,'_');
    XLSX.writeFile(wb, `${safeName}_${dateStr}.xlsx`);
    showToast(`Downloaded: ${safeName}_${dateStr}.xlsx`);
}

function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
</script>
@endpush