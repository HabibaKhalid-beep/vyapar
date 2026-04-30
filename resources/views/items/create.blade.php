@extends('layouts.app')

@section('title', 'Items')
@section('page', 'items')

@push('styles')
<style>
* { box-sizing: border-box; }

/* ═══════════════════════════════════════
   OVERALL PAGE WRAPPER
═══════════════════════════════════════ */
.vy-page {
    background: #fff;
    height: 100vh;
    max-height: 100vh;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* ── Header ── */
.vy-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 18px 28px;
    border-bottom: 1px solid #e5e7eb;
    flex-shrink: 0;
    background: #fff;
}
.vy-header-left {
    display: flex;
    align-items: center;
    gap: 20px;
}
.vy-title {
    font-size: 20px;
    font-weight: 700;
    color: #1a1a1a;
}
.vy-toggle-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
}
.vy-toggle-lbl {
    font-size: 14px;
    font-weight: 500;
}
.vy-header-right {
    display: flex;
    align-items: center;
    gap: 4px;
}
.vy-icon-btn {
    background: none; border: none;
    padding: 8px; cursor: pointer;
    color: #9ca3af; border-radius: 4px;
    line-height: 1;
    transition: background .12s;
}
.vy-icon-btn:hover { background: #f3f4f6; }

.vy-settings-overlay {
    position: fixed;
    inset: 0;
    background: rgba(17, 24, 39, 0.24);
    z-index: 2400;
    opacity: 0;
    pointer-events: none;
    transition: opacity .2s ease;
}
.vy-settings-overlay.open {
    opacity: 1;
    pointer-events: auto;
}
.vy-settings-drawer {
    position: absolute;
    top: 0;
    right: 0;
    width: 420px;
    max-width: 96vw;
    height: 100%;
    background: #fff;
    display: flex;
    flex-direction: column;
    transform: translateX(100%);
    transition: transform .22s ease;
    box-shadow: -14px 0 40px rgba(15, 23, 42, 0.18);
}
.vy-settings-overlay.open .vy-settings-drawer {
    transform: translateX(0);
}
.vy-settings-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 22px 22px 20px;
    border-bottom: 1px solid #e5e7eb;
}
.vy-settings-title {
    font-size: 18px;
    font-weight: 700;
    color: #1f2937;
}
.vy-settings-body {
    flex: 1;
    overflow-y: auto;
}
.vy-settings-row {
    border-bottom: 1px solid #f1f5f9;
}
.vy-settings-toggle {
    width: 100%;
    border: none;
    background: transparent;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 22px;
    cursor: pointer;
    color: #1f2937;
    font-size: 17px;
    text-align: left;
}
.vy-settings-toggle small {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 18px;
    height: 18px;
    border: 1.5px solid #94a3b8;
    border-radius: 50%;
    color: #64748b;
    font-size: 12px;
    margin-left: 6px;
}
.vy-settings-chevron {
    transition: transform .18s ease;
    color: #94a3b8;
}
.vy-settings-row.open .vy-settings-chevron {
    transform: rotate(90deg);
}
.vy-settings-panel {
    display: none;
    padding: 0 22px 22px;
}
.vy-settings-row.open .vy-settings-panel {
    display: block;
}
.vy-setting-item {
    display: grid;
    grid-template-columns: 1fr auto auto;
    gap: 12px;
    align-items: center;
    padding: 12px 0;
}
.vy-setting-item label {
    font-size: 15px;
    color: #1f2937;
}
.vy-setting-item input[type="text"],
.vy-setting-item input[type="number"],
.vy-setting-item input[type="month"],
.vy-setting-item input[type="date"],
.vy-setting-item select,
.vy-custom-name {
    width: 150px;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 14px;
    color: #374151;
    background: #fff;
    outline: none;
}
.vy-setting-item input:disabled,
.vy-setting-item select:disabled,
.vy-custom-name:disabled {
    background: #f8fafc;
    color: #94a3b8;
}
.vy-setting-section-title {
    font-size: 15px;
    font-weight: 700;
    color: #111827;
    padding-top: 8px;
    margin-top: 8px;
}
.vy-check {
    appearance: none;
    width: 26px;
    height: 26px;
    border: 2px solid #94a3b8;
    border-radius: 4px;
    background: #fff;
    cursor: pointer;
    position: relative;
}
.vy-check:checked {
    background: #1976d2;
    border-color: #1976d2;
}
.vy-check:checked::after {
    content: '';
    position: absolute;
    left: 8px;
    top: 3px;
    width: 6px;
    height: 12px;
    border: solid #fff;
    border-width: 0 2px 2px 0;
    transform: rotate(45deg);
}
.vy-custom-item {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 12px 14px;
    align-items: start;
    padding: 14px 0;
}
.vy-custom-meta {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.vy-custom-meta label {
    font-size: 15px;
    color: #64748b;
}
.vy-custom-print {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #64748b;
    font-size: 14px;
}
.vy-switch {
    position: relative;
    width: 38px;
    height: 22px;
    display: inline-block;
}
.vy-switch input {
    opacity: 0;
    width: 0;
    height: 0;
}
.vy-switch-slider {
    position: absolute;
    inset: 0;
    border-radius: 999px;
    background: #e5e7eb;
    transition: .18s;
}
.vy-switch-slider::before {
    content: '';
    position: absolute;
    width: 16px;
    height: 16px;
    left: 3px;
    top: 3px;
    background: #fff;
    border-radius: 50%;
    box-shadow: 0 1px 2px rgba(0,0,0,.18);
    transition: .18s;
}
.vy-switch input:checked + .vy-switch-slider {
    background: #1976d2;
}
.vy-switch input:checked + .vy-switch-slider::before {
    transform: translateX(16px);
}
.vy-settings-footer {
    border-top: 1px solid #e5e7eb;
    padding: 14px 22px 18px;
    text-align: center;
}
.vy-more-settings {
    border: none;
    background: #1976d2;
    border-radius: 999px;
    padding: 10px 18px;
    color: #1976d2;
    font-size: 16px;
    font-weight: 700;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}
.vy-more-settings:hover {
    background: #1565c0;
}
.vy-more-settings,
.vy-more-settings svg {
    color: #fff;
}
.vy-settings-save {
    margin-top: 14px;
    width: 100%;
    border: none;
    border-radius: 8px;
    background: #1976d2;
    color: #fff;
    font-size: 15px;
    font-weight: 700;
    padding: 12px 16px;
    cursor: pointer;
}
.vy-settings-save:hover {
    background: #1565c0;
}
.vy-extra-sec {
    margin: 0 28px 16px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 10px;
    padding: 18px 20px;
    display: none;
}
.vy-extra-sec.show {
    display: block;
}
.vy-extra-title {
    font-size: 15px;
    font-weight: 700;
    color: #111827;
    margin-bottom: 14px;
}
.vy-extra-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 14px;
}
.vy-extra-grid textarea {
    min-height: 98px;
    resize: vertical;
}
@media (max-width: 768px) {
    .vy-settings-drawer {
        width: 100%;
        max-width: 100%;
    }
    .vy-setting-item {
        grid-template-columns: 1fr;
    }
    .vy-extra-grid {
        grid-template-columns: 1fr;
    }
}

/* ── Toggle Switch — ALWAYS BLUE ── */
.vy-toggle {
    position: relative; display: inline-block;
    width: 46px; height: 26px;
}
.vy-toggle input { opacity: 0; width: 0; height: 0; }
.vy-slider {
    position: absolute; cursor: pointer; inset: 0;
    background: #2563eb;
    border-radius: 26px; transition: .2s;
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
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 20px 28px 0;
    flex-shrink: 0;
}

/* Item Name — floating label + blue border */
.vy-name-wrap {
    position: relative;
    width: 230px;
    flex-shrink: 0;
}
.vy-name-wrap .floating-label {
    position: absolute;
    top: -9px;
    left: 10px;
    background: #fff;
    padding: 0 4px;
    font-size: 11px;
    color: #2563eb;
    font-weight: 500;
    pointer-events: none;
    z-index: 1;
    white-space: nowrap;
}
.vy-name-input {
    width: 100%;
    border: 1.5px solid #2563eb;
    border-radius: 4px;
    padding: 13px 14px;
    font-size: 14px;
    color: #374151;
    background: #fff;
    outline: none;
    transition: border-color .15s, box-shadow .15s;
}
.vy-name-input:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37,99,235,.10);
}

/* Category */
.vy-cat-wrap { position: relative; width: 185px; flex-shrink: 0; }
.vy-cat-btn {
    display: flex; align-items: center; justify-content: space-between;
    border: 1.5px solid #d1d5db; border-radius: 4px;
    padding: 13px 12px;
    font-size: 14px; background: #fff;
    cursor: pointer; width: 100%; outline: none;
    transition: border-color .15s;
    color: #9ca3af;
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
    padding: 11px 14px; cursor: pointer;
    font-size: 13px; color: #374151;
}
.cat-row:hover { background: #f9fafb; }
.cat-cb {
    width: 16px; height: 16px; flex-shrink: 0;
    border: 1.5px solid #d1d5db; border-radius: 3px;
    display: flex; align-items: center; justify-content: center;
}
.cat-cb.on { background: #2563eb; border-color: #2563eb; }

/* Select Unit */
.vy-unit-btn {
    background: #dbeafe;
    color: #2563eb;
    border: 1.5px solid #93c5fd;
    border-radius: 4px;
    padding: 13px 22px;
    font-size: 14px; font-weight: 600;
    white-space: nowrap; cursor: pointer; flex-shrink: 0;
    transition: background .15s;
}
.vy-unit-btn:hover { background: #bfdbfe; }
.vy-unit-btn.chosen { background: #dbeafe; color: #2563eb; border-color: #93c5fd; }

/* Add Item Image */
.vy-img-area {
    display: flex; align-items: center; gap: 10px;
    cursor: pointer; flex-shrink: 0;
    color: #6b7280; font-size: 14px; font-weight: 400;
    white-space: nowrap;
    margin-left: 18px;
}
.vy-img-icon {
    width: 36px; height: 36px; flex-shrink: 0;
    border: 1.5px solid #d1d5db; border-radius: 4px;
    background: #f9fafb;
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
}
.vy-image-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(92px, 92px));
    gap: 10px;
    margin-top: 12px;
}
.vy-image-preview-item {
    width: 92px;
    height: 92px;
    border-radius: 8px;
    overflow: hidden;
    border: 1px solid #dbe3ef;
    background: #fff;
}
.vy-image-preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

/* ── Item Code — fused box ── */
.vy-code-row {
    padding: 14px 28px 0;
    flex-shrink: 0;
}
.vy-code-wrap {
    display: inline-flex; align-items: center;
    border: 1.5px solid #d1d5db; border-radius: 4px;
    overflow: hidden;
    width: 300px;
    background: #fff;
}
.vy-code-wrap input {
    flex: 1; border: none; outline: none;
    padding: 11px 14px;
    font-size: 14px; color: #374151;
    background: transparent;
    min-width: 0;
}
.vy-code-wrap input::placeholder { color: #9ca3af; }
.vy-assign-btn {
    flex-shrink: 0;
    border: none;
    background: #dbeafe;
    padding: 7px 14px;
    margin: 4px 5px 4px 0;
    font-size: 12px; font-weight: 600;
    color: #2563eb;
    cursor: pointer; white-space: nowrap;
    border-radius: 20px;
    transition: background .12s;
}
.vy-assign-btn:hover { background: #bfdbfe; }
.vy-custom-inline {
    padding: 14px 28px 0;
    display: none;
}
.vy-custom-inline.show {
    display: block;
}
.vy-custom-inline-grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 240px));
    gap: 14px;
}
.vy-custom-inline-item {
    display: none;
}
.vy-custom-inline-item.show {
    display: block;
}
.vy-custom-inline-label {
    display: block;
    font-size: 12px;
    font-weight: 600;
    color: #64748b;
    margin-bottom: 6px;
}
@media (max-width: 900px) {
    .vy-custom-inline-grid {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

/* ── Tabs ── */
.vy-tabs {
    display: flex; gap: 28px;
    padding: 0 28px; margin-top: 14px;
    border-bottom: 1px solid #e5e7eb;
    flex-shrink: 0;
}
.vy-tab {
    padding: 12px 0;
    font-size: 15px; font-weight: 500;
    cursor: pointer; border: none;
    border-bottom: 2px solid transparent;
    background: none; color: #9ca3af;
    margin-bottom: -1px; transition: all .15s;
}
.vy-tab.active { color: #e53e3e; border-bottom-color: #e53e3e; }
.vy-tab:hover:not(.active) { color: #4b5563; }

/* ── Scrollable body ── */
.vy-body {
   flex: 0 1 auto;
    overflow-y: auto;
    min-height: 0;
}
.vy-body::-webkit-scrollbar { width: 5px; }
.vy-body::-webkit-scrollbar-track { background: transparent; }
.vy-body::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

/* Price sections */
.vy-price-sec {
    margin: 16px 28px;
    background: #f8f9fb;
    border-radius: 6px;
    padding: 18px 20px 20px;
    border: 1px solid #ebebeb;
}
.vy-price-title {
    font-size: 15px; font-weight: 600;
    color: #1f2937; margin-bottom: 14px;
}
.vy-price-input {
    border: 1.5px solid #d1d5db; border-radius: 4px;
    padding: 11px 14px;
    font-size: 14px; color: #374151;
    background: #fff; outline: none; width: 240px;
    transition: border-color .15s;
}
.vy-price-input:focus { border-color: #2563eb; }
.vy-price-input::placeholder { color: #9ca3af; }
.vy-ws-link {
    display: inline-flex; align-items: center; gap: 6px;
    color: #2563eb; font-size: 14px; font-weight: 500;
    cursor: pointer; background: none; border: none;
    padding: 0; margin-top: 12px;
}
.vy-ws-link:hover { color: #1d4ed8; }

/* ── Footer ── */
.vy-footer {
    display: flex; align-items: center; justify-content: flex-end;
    gap: 12px; padding: 16px 28px;
    border-top: 1px solid #e5e7eb;
    background: #fff; flex-shrink: 0;
}
.vy-btn-snew {
    background: #fff; border: 1.5px solid #d1d5db;
    border-radius: 4px; padding: 11px 24px;
    font-size: 14px; color: #6b7280;
    cursor: pointer; transition: background .12s;
}
.vy-btn-snew:hover { background: #f3f4f6; }
.vy-btn-save {
    background: #9ca3af; border: none;
    border-radius: 4px; padding: 11px 32px;
    font-size: 14px; font-weight: 700;
    color: #fff; cursor: pointer;
    transition: background .15s;
}
.vy-btn-save.ready { background: #2563eb; }
.vy-btn-save.ready:hover { background: #1d4ed8; }

/* ═══════════════════════════════════════
   ITEMS LIST
═══════════════════════════════════════ */
.vy-list-page {
    background: #f0f2f5;
    height: 100%;
    display: none;
    flex-direction: column;
}
.vy-list-page.active { display: flex; }
.vy-list-topbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 22px 28px 16px;
    background: #f0f2f5;
}
.vy-list-title { font-size: 20px; font-weight: 600; color: #1f2937; }
.vy-add-btn {
    background: #2563eb; color: #fff;
    border: none; border-radius: 4px;
    padding: 11px 18px; font-size: 14px; font-weight: 500;
    cursor: pointer; display: inline-flex; align-items: center; gap: 6px;
    transition: background .15s;
}
.vy-add-btn:hover { background: #1d4ed8; }
.vy-tbl-wrap {
    margin: 0 20px 20px;
    background: #fff; border-radius: 6px;
    border: 1px solid #e5e7eb; overflow: hidden;
}
.vy-tbl-top {
    padding: 16px 24px; border-bottom: 1px solid #f3f4f6;
    font-size: 15px; font-weight: 500; color: #374151;
}
.vy-tbl { width: 100%; border-collapse: collapse; }
.vy-tbl th {
    padding: 13px 24px; font-size: 12px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
    color: #9ca3af; background: #f9fafb;
    border-bottom: 1px solid #f3f4f6; text-align: left;
}
.vy-tbl td {
    padding: 15px 24px; font-size: 14px; color: #374151;
    border-bottom: 1px solid #f8f9fa; vertical-align: middle;
}
.vy-tbl tbody tr:last-child td { border-bottom: none; }
.vy-tbl tbody tr:hover td { background: #fafafa; }

/* ═══════════════════════════════════════
   UNIT MODAL
═══════════════════════════════════════ */
#unit-overlay {
    position: fixed; inset: 0; z-index: 2000;
    background: rgba(0,0,0,.45);
    display: none;
    align-items: center; justify-content: center;
}
#unit-overlay.open { display: flex; }
#unit-modal {
    background: #fff; border-radius: 6px;
    box-shadow: 0 10px 40px rgba(0,0,0,.22);
    width: 500px; max-width: 95vw;
    animation: popIn .15s ease-out;
}
@keyframes popIn {
    from { opacity:0; transform:scale(.96); }
    to   { opacity:1; transform:scale(1); }
}
.unit-hdr {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 26px; background: #e8f4fd;
    border-bottom: 1px solid #bfdbfe;
    border-radius: 6px 6px 0 0;
    font-size: 16px; font-weight: 600; color: #1e3a8a;
}
.unit-lbl {
    font-size: 11px; font-weight: 700; color: #2563eb;
    text-transform: uppercase; letter-spacing: .08em;
    margin-bottom: 8px;
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
</style>
@endpush

@section('content')

<div class="vy-page" id="page-form">

    {{-- Header --}}
    <div class="vy-header">
        <div class="vy-header-left">
            <span class="vy-title">Add Item</span>
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
            <button class="vy-icon-btn" title="Settings" onclick="openSettingsDrawer()">
                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </button>
            <button class="vy-icon-btn" title="Close" onclick="goToList()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
    </div>

    {{-- Fields Row --}}
    <div class="vy-fields">

        <div class="vy-name-wrap">
            {{-- ADD id="name-floating-label" --}}
            <span class="floating-label" id="name-floating-label">Item Name *</span>
            <input type="text" id="item-name" class="vy-name-input"
                   oninput="updateSaveBtn()"/>
        </div>

        <div class="vy-cat-wrap" id="cat-wrapper">
            <button type="button" class="vy-cat-btn" onclick="toggleCatDD()">
                <span id="cat-label">Category</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div id="cat-dropdown" class="vy-cat-dd">
            <div id="cat-add-btn" class="cat-add" onclick="event.stopPropagation(); showCatInput()">
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
            <button type="button" id="unit-trigger-btn" class="vy-unit-btn" onclick="openUnitModal()">
                Select Unit
            </button>
            <span id="unit-conv-label" style="font-size:12px;color:#6b7280;position:absolute;top:100%;margin-top:4px;left:50%;transform:translateX(-50%);white-space:nowrap;"></span>
        </div>
    </div>

    {{-- Item Code & Image --}}
    <div class="vy-code-row">
        <div class="vy-code-wrap">
            <input type="text" id="item-code" placeholder="Item Code"/>
            <button type="button" class="vy-assign-btn" onclick="assignCode()">Assign Code</button>
        </div>
        <div style="display: inline-flex; align-items: center; gap: 12px; margin-left: 16px; padding: 8px 12px; background: linear-gradient(135deg, #f0f9ff 0%, #f9fafb 100%); border: 1.5px solid #e0e7ff; border-radius: 6px; transition: all 0.2s ease;">
            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #2563eb; font-size: 14px; font-weight: 500; transition: color 0.2s ease;" onmouseover="this.style.color='#1d4ed8'" onmouseout="this.style.color='#2563eb'">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8" style="transition: transform 0.2s ease;">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Item Image</span>
                <input type="file" id="item-image" name="item_image" accept="image/*" style="display: none;" onchange="previewItemImage(event)"/>
            </label>
            <div style="width: 1px; height: 20px; background: #e0e7ff;"></div>
            <div id="item-image-thumb" style="width: 40px; height: 40px; border: 1.5px solid #93c5fd; border-radius: 6px; background: linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%); display: flex; align-items: center; justify-content: center; overflow: hidden; flex-shrink: 0; cursor: pointer; transition: all 0.2s ease;" onclick="document.getElementById('item-image').click()" title="Click to change image">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <div id="custom-inline-fields" class="vy-custom-inline">
        <div class="vy-custom-inline-grid">
            @for ($i = 1; $i <= 6; $i++)
                <div id="custom-inline-item-{{ $i }}" class="vy-custom-inline-item">
                    <label id="custom-inline-label-{{ $i }}" class="vy-custom-inline-label">Custom Field {{ $i }}</label>
                    <input type="text" id="custom-inline-value-{{ $i }}" class="vy-price-input" placeholder="Custom Field {{ $i }}">
                </div>
            @endfor
        </div>
    </div>

    {{-- Tabs --}}
    <div class="vy-tabs">
        <button type="button" id="tab-pricing" class="vy-tab active" onclick="switchTab('pricing')">Pricing</button>
        <button type="button" id="tab-stock"   class="vy-tab"        onclick="switchTab('stock')">Stock</button>
    </div>

    {{-- Scrollable Body --}}
    <div class="vy-body">

        {{-- PRICING --}}
        <div id="pane-pricing">
            <div class="vy-price-sec">
                <div class="vy-price-title">Sale Price</div>
                <input type="number" id="sale-price" placeholder="Sale Price" min="0" step="0.01" class="vy-price-input"/>
                <div>
                    <button type="button" class="vy-ws-link" onclick="toggleWholesale()">
                        <span id="ws-icon" style="font-size:17px;line-height:1;font-weight:700;">+</span>
                        <span id="ws-label">Add Wholesale Price</span>
                    </button>
                </div>
                <div id="wholesale-row" style="display:none;margin-top:12px;">
    <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap;">
        <input type="number" id="wholesale-price" placeholder="Wholesale Price" min="0" step="0.01" class="vy-price-input"/>
        <span style="color:#9ca3af;font-size:18px;line-height:1;">—</span>
        <div style="position:relative;display:flex;align-items:center;">
            <input type="number" id="min-wholesale-qty" placeholder="Minimum Wholesale Qty" min="0" step="1" class="vy-price-input" style="padding-right:36px;"/>
            <span title="Minimum quantity required to apply wholesale price"
                  style="position:absolute;right:10px;cursor:help;color:#9ca3af;display:flex;align-items:center;">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <path stroke-linecap="round" d="M12 8v4m0 4h.01"/>
                </svg>
            </span>
        </div>
    </div>
</div>
            </div>
            {{-- ADD id="purchase-sec" --}}
            <div class="vy-price-sec" id="purchase-sec">
                <div class="vy-price-title">Purchase Price</div>
                <input type="number" id="purchase-price" placeholder="Purchase Price" min="0" step="0.01" class="vy-price-input"/>
            </div>
        </div>

        {{-- STOCK --}}
        <div id="pane-stock" style="display:none;padding:20px 28px;">
            <div style="display:flex;flex-wrap:wrap;gap:14px;">
                <input type="number" id="opening-qty"  placeholder="Opening Quantity" min="0" class="vy-price-input"/>
                <input type="number" id="at-price"     placeholder="At Price" min="0" step="0.01" class="vy-price-input"/>
                <div style="position:relative;width:240px;">
                    <label style="position:absolute;top:4px;left:14px;font-size:10px;color:#9ca3af;pointer-events:none;z-index:1;">As Of Date</label>
                    <input type="date" id="as-of-date" class="vy-price-input" style="padding-top:20px;padding-bottom:6px;width:240px;"/>
                </div>
            </div>
            <div style="display:flex;flex-wrap:wrap;gap:14px;margin-top:14px;">
                <input type="text" id="min-stock"  placeholder="Min Stock To Maintain" class="vy-price-input"/>
                <input type="text" id="location"   placeholder="Location"              class="vy-price-input"/>
            </div>
            <div style="margin-top:18px;">
                <div class="vy-extra-title" style="margin-bottom:10px;">Item Images</div>
                <div class="vy-img-area" style="margin-left:0;" onclick="document.getElementById('img-file').click()">
                    <div class="vy-img-icon" id="img-thumb">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                            <path d="M18.5 4.5 a2.5 2.5 0 0 1 2 1.5" stroke-width="1.3"/>
                            <polyline points="20.5 3.5 20.5 6 18 6" stroke-width="1.3"/>
                        </svg>
                    </div>
                    <span id="img-label">Add Item Images</span>
                    <input type="file" id="img-file" name="images[]" accept="image/*" multiple style="display:none;" onchange="previewImg(event)"/>
                </div>
                <div id="image-preview-list" class="vy-image-preview-grid" style="display:none;"></div>
            </div>
            <div id="description-sec" style="margin-top:18px;">
                <div class="vy-extra-title" style="margin-bottom:10px;">Description</div>
                <div class="vy-extra-grid" style="grid-template-columns:1fr;">
                    <textarea id="item-description" class="vy-price-input" placeholder="Description"></textarea>
                </div>
            </div>
        </div>

        <div id="mrp-sec" class="vy-extra-sec">
            <div class="vy-extra-title">MRP / Price</div>
            <div class="vy-extra-grid">
                <input type="text" id="mrp-label" value="MRP" class="vy-price-input" placeholder="MRP Label"/>
                <input type="number" id="mrp-value" class="vy-price-input" placeholder="MRP" min="0" step="0.01"/>
            </div>
        </div>

        <div id="barcode-sec" class="vy-extra-sec">
            <div class="vy-extra-title">Barcode Scan</div>
            <div class="vy-extra-grid">
                <input type="text" id="barcode-value" class="vy-price-input" placeholder="Barcode Value"/>
            </div>
        </div>

        <div id="serial-sec" class="vy-extra-sec">
            <div class="vy-extra-title">Serial No. Tracking</div>
            <div class="vy-extra-grid">
                <input type="text" id="serial-label" class="vy-price-input" placeholder="Serial No. / IMEI etc."/>
            </div>
        </div>

        <div id="batch-sec" class="vy-extra-sec">
            <div class="vy-extra-title">Batch Tracking</div>
            <div class="vy-extra-grid">
                <input type="text" id="batch-label" class="vy-price-input" placeholder="Batch No."/>
                <input type="month" id="batch-exp-date" class="vy-price-input" placeholder="Exp. Date"/>
                <input type="date" id="batch-mfg-date" class="vy-price-input" placeholder="Mfg. Date"/>
                <input type="text" id="model-no" class="vy-price-input" placeholder="Model No."/>
                <input type="text" id="size-value" class="vy-price-input" placeholder="Size"/>
            </div>
        </div>

        <div id="custom-fields-sec" class="vy-extra-sec">
            <div class="vy-extra-title">Item Custom Fields</div>
            <div class="vy-extra-grid">
                <input type="text" id="custom-preview-1" class="vy-price-input" placeholder="Custom Field 1" readonly/>
                <input type="text" id="custom-preview-2" class="vy-price-input" placeholder="Custom Field 2" readonly/>
                <input type="text" id="custom-preview-3" class="vy-price-input" placeholder="Custom Field 3" readonly/>
                <input type="text" id="custom-preview-4" class="vy-price-input" placeholder="Custom Field 4" readonly/>
                <input type="text" id="custom-preview-5" class="vy-price-input" placeholder="Custom Field 5" readonly/>
                <input type="text" id="custom-preview-6" class="vy-price-input" placeholder="Custom Field 6" readonly/>
            </div>
        </div>

    </div>

    {{-- Footer --}}
    <div class="vy-footer">
        <button type="button" class="vy-btn-snew" onclick="saveAndNew()">Save &amp; New</button>
        <button type="button" class="vy-btn-save" id="save-btn" onclick="saveItem()">Save</button>
    </div>

</div>{{-- /page-form --}}


{{-- ITEMS LIST --}}
<div class="vy-list-page" id="page-list">
    <div class="vy-list-topbar">
        <span class="vy-list-title">Items</span>
        <button class="vy-add-btn" onclick="goToForm()">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.8">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4"/>
            </svg>
            + Add Item
        </button>
    </div>
    <div class="vy-tbl-wrap">
        <div class="vy-tbl-top">All Items</div>
        <table class="vy-tbl">
            <thead>
                <tr>
                    <th>Item Name</th><th>Category</th><th>Unit</th>
                    <th>Sale Price</th><th>Purchase Price</th><th>Stock Qty</th>
                </tr>
            </thead>
            <tbody id="items-tbody"></tbody>
        </table>
    </div>
</div>

@endsection

@section('modals')
{{-- Unit Modal --}}
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

        {{-- Conversion Rates --}}
        <div id="conversion-row" style="display:none;padding:16px 28px 0;">
            <div style="font-size:13px;font-weight:600;color:#374151;margin-bottom:12px;">Conversion Rates</div>
            <div style="display:flex;align-items:center;gap:10px;">
                <input type="radio" checked style="accent-color:#2563eb;width:16px;height:16px;">
                <span style="font-size:13px;color:#374151;">1</span>
                <span id="conv-base-lbl" style="font-size:13px;color:#374151;font-weight:500;"></span>
                <span style="font-size:13px;color:#374151;">=</span>
                <input type="number" id="conv-rate" value="0" min="0"
                    style="width:90px;border:1.5px solid #d1d5db;border-radius:4px;padding:8px 10px;font-size:14px;outline:none;text-align:center;"/>
                <span id="conv-sec-lbl" style="font-size:13px;color:#374151;font-weight:500;"></span>
            </div>
        </div>

        {{-- Same unit error --}}
        <div id="same-unit-error" style="display:none;margin:12px 28px 0;background:#e0f0ff;border:1px solid #93c5fd;border-radius:6px;padding:12px 16px;display:none;align-items:center;gap:10px;">
            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>
            <span style="font-size:13px;color:#1e40af;">Base and secondary Unit can not be same. Please select different unit.</span>
        </div>

        <div style="display:flex;justify-content:flex-end;padding:14px 28px;border-top:1px solid #e5e7eb;">
            <button onclick="saveUnit()" style="background:#2563eb;color:#fff;border:none;border-radius:4px;padding:11px 32px;font-size:14px;font-weight:700;cursor:pointer;letter-spacing:.04em;">SAVE</button>
        </div>
    </div>
</div>

<div id="settings-overlay" class="vy-settings-overlay" onclick="if(event.target.id==='settings-overlay')closeSettingsDrawer()">
    <div class="vy-settings-drawer" onclick="event.stopPropagation()">
        <div class="vy-settings-head">
            <span class="vy-settings-title">Item Settings</span>
            <button class="vy-icon-btn" type="button" onclick="closeSettingsDrawer()">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>
        <div class="vy-settings-body">
            <div class="vy-settings-row" id="additional-fields-row">
                <button class="vy-settings-toggle" type="button" onclick="toggleSettingsSection('additional-fields-row')">
                    <span>Additional Item Fields</span>
                    <svg class="vy-settings-chevron" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><polyline points="9 6 15 12 9 18"/></svg>
                </button>
                <div class="vy-settings-panel">
                    <div class="vy-setting-item">
                        <label for="setting-mrp-enabled">MRP</label>
                        <input type="text" id="setting-mrp-text" value="MRP" oninput="syncSettingsText('setting-mrp-text','mrp-label')">
                        <input type="checkbox" id="setting-mrp-enabled" class="vy-check" onchange="toggleSettingField('mrp-sec', this.checked)">
                    </div>
                    <div class="vy-setting-item">
                        <label for="setting-mrp-calc">Calculate Sale Price From MRP &amp; Disc.</label>
                        <span></span>
                        <input type="checkbox" id="setting-mrp-calc" class="vy-check">
                    </div>
                    <div class="vy-setting-item">
                        <label for="setting-batch-mrp">Use MRP For Batch Tracking</label>
                        <span></span>
                        <input type="checkbox" id="setting-batch-mrp" class="vy-check">
                    </div>

                    <div class="vy-setting-section-title">Serial No. Tracking</div>
                    <div class="vy-setting-item">
                        <label for="setting-serial-enabled">Serial No. / IMEI etc.</label>
                        <input type="text" id="setting-serial-text" value="Serial No." oninput="syncSettingsText('setting-serial-text','serial-label')">
                        <input type="checkbox" id="setting-serial-enabled" class="vy-check" onchange="toggleSettingField('serial-sec', this.checked)">
                    </div>

                    <div class="vy-setting-section-title">Batch Tracking</div>
                    <div class="vy-setting-item">
                        <label for="setting-batch-enabled">Batch No.</label>
                        <input type="text" id="setting-batch-text" value="Batch No." oninput="syncSettingsText('setting-batch-text','batch-label')">
                        <input type="checkbox" id="setting-batch-enabled" class="vy-check" onchange="toggleSettingField('batch-sec', this.checked)">
                    </div>
                    <div class="vy-setting-item">
                        <label for="setting-exp-enabled">Exp Date</label>
                        <select id="setting-exp-format">
                            <option value="month">mm/yy</option>
                            <option value="date">dd/mm/yy</option>
                        </select>
                        <input type="checkbox" id="setting-exp-enabled" class="vy-check" onchange="toggleBatchField('batch-exp-date', this.checked)">
                    </div>
                    <div class="vy-setting-item">
                        <label for="setting-mfg-enabled">Mfg Date</label>
                        <select id="setting-mfg-format">
                            <option value="date">dd/mm/yy</option>
                            <option value="month">mm/yy</option>
                        </select>
                        <input type="checkbox" id="setting-mfg-enabled" class="vy-check" onchange="toggleBatchField('batch-mfg-date', this.checked)">
                    </div>
                    <div class="vy-setting-item">
                        <label for="setting-model-enabled">Model No.</label>
                        <input type="text" id="setting-model-text" value="Model No." oninput="syncSettingsText('setting-model-text','model-no')">
                        <input type="checkbox" id="setting-model-enabled" class="vy-check" onchange="toggleBatchField('model-no', this.checked)">
                    </div>
                    <div class="vy-setting-item">
                        <label for="setting-size-enabled">Size</label>
                        <input type="text" id="setting-size-text" value="Size" oninput="syncSettingsText('setting-size-text','size-value')">
                        <input type="checkbox" id="setting-size-enabled" class="vy-check" onchange="toggleBatchField('size-value', this.checked)">
                    </div>
                    <button type="button" class="vy-settings-save" onclick="saveAdditionalFieldsSettings()">Save</button>
                </div>
            </div>

            <div class="vy-settings-row" id="custom-fields-row">
                <button class="vy-settings-toggle" type="button" onclick="toggleSettingsSection('custom-fields-row')">
                    <span>Item Custom Fields <small>i</small></span>
                    <svg class="vy-settings-chevron" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.4"><polyline points="9 6 15 12 9 18"/></svg>
                </button>
                <div class="vy-settings-panel">
                    @for ($i = 1; $i <= 6; $i++)
                        <div class="vy-custom-item">
                            <input type="checkbox" id="custom-field-enabled-{{ $i }}" class="vy-check" onchange="syncCustomField({{ $i }})">
                            <div class="vy-custom-meta">
                                <label for="custom-field-name-{{ $i }}">Custom Field {{ $i }} <span style="color:#ef4444">*</span></label>
                                <input type="text" id="custom-field-name-{{ $i }}" class="vy-custom-name" placeholder="{{ [1 => 'E.g: Colour', 2 => 'E.g: Material', 3 => 'E.g: Mfg. Date', 4 => 'E.g: Exp. Date', 5 => 'E.g: Size', 6 => 'E.g: Brand'][$i] ?? 'E.g: Custom Field' }}" oninput="syncCustomField({{ $i }})">
                                <label class="vy-custom-print">
                                    <span class="vy-switch">
                                        <input type="checkbox" id="custom-field-print-{{ $i }}">
                                        <span class="vy-switch-slider"></span>
                                    </span>
                                    <span>Show in print</span>
                                </label>
                            </div>
                        </div>
                    @endfor
                    <button type="button" class="vy-settings-save" onclick="saveCustomFieldsSettings()">Save</button>
                </div>
            </div>

            <div class="vy-settings-row">
                <div class="vy-setting-item" style="padding:22px;">
                    <label for="setting-wholesale-toggle">Wholesale Price</label>
                    <span></span>
                    <input type="checkbox" id="setting-wholesale-toggle" class="vy-check" checked onchange="toggleSettingField('wholesale-row', this.checked, true)">
                </div>
                <div class="vy-setting-item" style="padding:0 22px 22px;">
                    <label for="setting-barcode-toggle">Barcode Scan</label>
                    <span></span>
                    <input type="checkbox" id="setting-barcode-toggle" class="vy-check" onchange="saveAdditionalFieldsSettings()">
                </div>
                <div class="vy-setting-item" style="padding:0 22px 22px;">
                    <label for="setting-category-toggle">Item Category</label>
                    <span></span>
                    <input type="checkbox" id="setting-category-toggle" class="vy-check" checked onchange="toggleCategoryField(this.checked)">
                </div>
                <div class="vy-setting-item" style="padding:0 22px 22px;">
                    <label for="setting-description-toggle">Description</label>
                    <span></span>
                    <input type="checkbox" id="setting-description-toggle" class="vy-check" onchange="saveAdditionalFieldsSettings()">
                </div>
            </div>
        </div>
        <div class="vy-settings-footer">
            <button type="button" class="vy-more-settings" onclick="closeSettingsDrawer()">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m6-6H6"/>
                </svg>
                More Options
            </button>
        </div>
    </div>
</div>
@endsection

@push('scripts')

<script>
/* ─── State ─────────────────────────────── */
let cats=[], selCats=[], baseUnit='', secUnit='';
let settingsState = {
    wholesale: true,
    category: true,
    mrp: false,
    barcode: false,
    description: true,
    serial: false,
    batch: false
};

function loadCats(){
    fetch('{{ route("items.category.list") }}', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => { cats = data; renderCats(); })
    .catch(() => {});
}
let wsOpen=false, iType='product', codeN=1000;

/* ─── Page switching ─────────────────────── */
function goToList(){
    window.location.href = iType === 'service' ? '{{ route("items.services") }}' : '{{ route("items") }}';
}
function showToast(msg){
    const t = document.createElement('div');
    t.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;background:#1e3a8a;color:#fff;padding:12px 18px;border-radius:6px;font-size:13px;display:flex;align-items:center;gap:8px;box-shadow:0 4px 12px rgba(0,0,0,.2);';
    t.innerHTML = `<svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" d="M12 8v4m0 4h.01"/></svg>${msg}`;
    document.body.appendChild(t);
    setTimeout(()=>t.remove(), 3000);
}

function openSettingsDrawer(){
    document.getElementById('settings-overlay').classList.add('open');
}

function closeSettingsDrawer(){
    document.getElementById('settings-overlay').classList.remove('open');
}

function toggleSettingsSection(id){
    document.getElementById(id).classList.toggle('open');
}

function toggleSettingField(id, enabled, isWholesale = false){
    return;
}

function toggleCategoryField(enabled){
    return;
}

function toggleBatchField(id, enabled){
    return;
}

function syncSettingsText(sourceId, targetId){
    return;
}

function syncCustomField(index){
    return;
}

function saveAdditionalFieldsSettings(silent = false){
    settingsState.wholesale = !!document.getElementById('setting-wholesale-toggle')?.checked;
    settingsState.category = !!document.getElementById('setting-category-toggle')?.checked;
    settingsState.mrp = !!document.getElementById('setting-mrp-enabled')?.checked;
    settingsState.barcode = !!document.getElementById('setting-barcode-toggle')?.checked;
    settingsState.description = true;
    settingsState.serial = !!document.getElementById('setting-serial-enabled')?.checked;
    settingsState.batch = !!document.getElementById('setting-batch-enabled')?.checked;

    wsOpen = settingsState.wholesale;
    document.getElementById('wholesale-row').style.display = settingsState.wholesale ? 'block' : 'none';
    document.getElementById('ws-icon').textContent = settingsState.wholesale ? '−' : '+';
    document.getElementById('ws-label').textContent = settingsState.wholesale ? 'Remove Wholesale Price' : 'Add Wholesale Price';

    document.getElementById('cat-wrapper').style.display = settingsState.category ? '' : 'none';
    document.getElementById('mrp-sec').classList.toggle('show', settingsState.mrp);
    document.getElementById('barcode-sec').classList.toggle('show', settingsState.barcode);
    document.getElementById('serial-sec').classList.toggle('show', settingsState.serial);
    document.getElementById('batch-sec').classList.toggle('show', settingsState.batch);

    document.getElementById('mrp-label').value = document.getElementById('setting-mrp-text')?.value || 'MRP';
    document.getElementById('mrp-label').placeholder = document.getElementById('setting-mrp-text')?.value || 'MRP';
    document.getElementById('serial-label').placeholder = document.getElementById('setting-serial-text')?.value || 'Serial No.';
    document.getElementById('batch-label').placeholder = document.getElementById('setting-batch-text')?.value || 'Batch No.';
    document.getElementById('model-no').placeholder = document.getElementById('setting-model-text')?.value || 'Model No.';
    document.getElementById('size-value').placeholder = document.getElementById('setting-size-text')?.value || 'Size';

    document.getElementById('batch-exp-date').style.display = document.getElementById('setting-exp-enabled')?.checked ? '' : 'none';
    document.getElementById('batch-mfg-date').style.display = document.getElementById('setting-mfg-enabled')?.checked ? '' : 'none';
    document.getElementById('model-no').style.display = document.getElementById('setting-model-enabled')?.checked ? '' : 'none';
    document.getElementById('size-value').style.display = document.getElementById('setting-size-enabled')?.checked ? '' : 'none';

    if (!silent) showToast('Additional item fields saved');
}

function saveCustomFieldsSettings(silent = false){
    const inlineWrap = document.getElementById('custom-inline-fields');
    let hasActiveFields = false;

    for (let idx = 1; idx <= 6; idx++) {
        const rowEnabled = document.getElementById(`custom-field-enabled-${idx}`)?.checked;
        const rowName = document.getElementById(`custom-field-name-${idx}`)?.value.trim() || `Custom Field ${idx}`;
        const preview = document.getElementById(`custom-preview-${idx}`);
        const inlineItem = document.getElementById(`custom-inline-item-${idx}`);
        const inlineLabel = document.getElementById(`custom-inline-label-${idx}`);
        const inlineInput = document.getElementById(`custom-inline-value-${idx}`);

        if (preview) {
            preview.value = rowEnabled ? rowName : '';
        }
        if (inlineItem && inlineLabel && inlineInput) {
            const shouldShow = !!rowEnabled;
            inlineItem.classList.toggle('show', shouldShow);
            inlineLabel.textContent = rowName || `Custom Field ${idx}`;
            inlineInput.placeholder = rowName || `Custom Field ${idx}`;
            if (!shouldShow) inlineInput.value = '';
            if (shouldShow) hasActiveFields = true;
        }
    }

    document.getElementById('custom-fields-sec').classList.toggle('show', hasActiveFields);
    if (inlineWrap) inlineWrap.classList.toggle('show', hasActiveFields);
    if (!silent) showToast('Custom fields saved');
}

/* ─── Save button state ──────────────────── */
function updateSaveBtn(){
    const ok = document.getElementById('item-name').value.trim().length > 0;
    document.getElementById('save-btn').classList.toggle('ready', ok);
}

/* ─── Type toggle ────────────────────────── */
function handleTypeToggle(){
    const s = document.getElementById('type-toggle').checked;
    iType = s ? 'service' : 'product';
    applyTypeUI(s);
}
function applyTypeUI(isService) {
    document.getElementById('lbl-product').style.color      = isService ? '#9ca3af' : '#2563eb';
    document.getElementById('lbl-product').style.fontWeight = isService ? '500'     : '600';
    document.getElementById('lbl-service').style.color      = isService ? '#2563eb' : '#9ca3af';
    document.getElementById('lbl-service').style.fontWeight = isService ? '600'     : '500';
    document.getElementById('name-floating-label').textContent = isService ? 'Service Name *' : 'Item Name *';
    document.getElementById('item-code').placeholder = isService ? 'Service Code' : 'Item Code';
    document.getElementById('tab-stock').style.display = isService ? 'none' : '';
    document.getElementById('purchase-sec').style.display = isService ? 'none' : '';
    document.getElementById('setting-batch-mrp').disabled = isService;
    document.getElementById('setting-exp-enabled').disabled = isService;
    document.getElementById('setting-mfg-enabled').disabled = isService;
    if (isService) switchTab('pricing');
}

/* ─── Tabs ───────────────────────────────── */
function switchTab(t){
    ['pricing','stock'].forEach(x=>{
        document.getElementById('tab-'+x).classList.toggle('active', x===t);
        document.getElementById('pane-'+x).style.display = x===t ? 'block' : 'none';
    });
}

/* ─── Category dropdown ──────────────────── */
function toggleCatDD(){
    document.getElementById('cat-dropdown').classList.toggle('open');
    renderCats();
}
function closeCatDD(){
    document.getElementById('cat-dropdown').classList.remove('open');
    document.getElementById('cat-input-row').style.display='none';
    document.getElementById('cat-add-btn').style.display='flex';
}
function renderCats(){
    const list = document.getElementById('cat-list');
    if(!cats.length){
        list.innerHTML='<p style="padding:12px 14px;font-size:13px;color:#9ca3af;margin:0;">No categories yet</p>';
        return;
    }
    list.innerHTML = cats.map(c=>`
        <div class="cat-row" onclick="toggleCat('${esc(c.name)}')">
            <div class="cat-cb ${selCats.includes(c.name)?'on':''}">
                ${selCats.includes(c.name)
                    ?'<svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>'
                    :''}
            </div>
            <span>${esc(c.name)}</span>
        </div>`).join('');
}

function toggleCat(cat){
    selCats = selCats.includes(cat) ? [] : [cat];
    renderCats();
    const l = document.getElementById('cat-label');
    l.textContent = selCats[0] || 'Category';
    l.style.color = selCats[0] ? '#374151' : '#9ca3af';
    closeCatDD();
}
function showCatInput(){
    document.getElementById('cat-add-btn').style.display='none';
    document.getElementById('cat-input-row').style.display='block';
    setTimeout(()=>document.getElementById('new-cat-text').focus(),40);
}
function saveCat(){
    const v = document.getElementById('new-cat-text').value.trim();
    if(!v) return;
    fetch('{{ route("items.category.store") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ name: v })
    })
    .then(r => r.json())
    .then(d => {
        const cat = d.category || d;
        if(cat?.id){
            if(!cats.find(c => c.id == cat.id)) cats.push(cat);
            toggleCat(cat.name);
        } else {
            showToast(d.message || 'Failed to create category.');
        }
    })
    .catch(() => showToast('Network error.'));

    document.getElementById('new-cat-text').value='';
    document.getElementById('cat-input-row').style.display='none';
    document.getElementById('cat-add-btn').style.display='flex';
}
document.addEventListener('click', e=>{
    const w = document.getElementById('cat-wrapper');
    if(w && !w.contains(e.target)) closeCatDD();
});
document.addEventListener('keydown', e=>{
    if(e.key==='Enter' && document.activeElement?.id==='new-cat-text'){ e.preventDefault(); saveCat(); }
});

/* ─── Assign Code ────────────────────────── */
function assignCode(){
    document.getElementById('item-code').value = 'ITM-'+(++codeN);
}

/* ─── Wholesale ──────────────────────────── */
function toggleWholesale(){
    wsOpen = !wsOpen;
    document.getElementById('wholesale-row').style.display = wsOpen ? 'block' : 'none';
    document.getElementById('ws-icon').textContent = wsOpen ? '−' : '+';
    document.getElementById('ws-label').textContent = wsOpen ? 'Remove Wholesale Price' : 'Add Wholesale Price';
    const wholesaleToggle = document.getElementById('setting-wholesale-toggle');
    if (wholesaleToggle) wholesaleToggle.checked = wsOpen;
}

/* ─── Unit Modal ─────────────────────────── */
function onUnitChange(){
    const b = document.getElementById('base-unit').value;
    const s = document.getElementById('secondary-unit').value;
    const convRow   = document.getElementById('conversion-row');
    const sameError = document.getElementById('same-unit-error');
    convRow.style.display   = 'none';
    sameError.style.display = 'none';
    if(!b || !s) return;
    if(b === s){ sameError.style.display = 'flex'; return; }
    const baseName = b.split('(')[0].trim();
    const secName  = s.split('(')[0].trim();
    document.getElementById('conv-base-lbl').textContent = baseName.toUpperCase();
    document.getElementById('conv-sec-lbl').textContent  = secName.toUpperCase();
    convRow.style.display = 'block';
}
function openUnitModal(){
    document.getElementById('unit-overlay').classList.add('open');
    document.getElementById('base-unit').value = baseUnit;
    document.getElementById('secondary-unit').value = secUnit;
    onUnitChange();
}
function closeUnitModal(){
    document.getElementById('unit-overlay').classList.remove('open');
}
function saveUnit(){
    const b = document.getElementById('base-unit').value;
    const s = document.getElementById('secondary-unit').value;
    if(b && s && b === s){ document.getElementById('same-unit-error').style.display = 'flex'; return; }
    if(b && s && b !== s){
        const rate = parseFloat(document.getElementById('conv-rate').value);
        if(!rate || rate <= 0){ showToast('Please enter conversion rate.'); return; }
    }
    baseUnit = b; secUnit = s;
    const btn = document.getElementById('unit-trigger-btn');
    if(baseUnit){
        const baseName = baseUnit.match(/\(([^)]+)\)/)?.[1] || baseUnit;
        const secName  = secUnit ? secUnit.match(/\(([^)]+)\)/)?.[1] || secUnit : '';
        const convRate = document.getElementById('conv-rate').value;
        if(secUnit && secUnit !== baseUnit){
            btn.innerHTML = 'Edit Unit';
            document.getElementById('unit-conv-label').textContent = `1 ${baseName} = ${convRate} ${secName}`;
        } else {
            btn.innerHTML = baseUnit;
            btn.classList.add('chosen');
            document.getElementById('unit-conv-label').textContent = '';
        }
    } else {
        btn.innerHTML = 'Select Unit';
        btn.classList.remove('chosen');
        btn.style.background=''; btn.style.color=''; btn.style.borderColor='';
    }
    closeUnitModal();
}

/* ─── Image preview ──────────────────────── */
function previewImg(e){
    const files = Array.from(e.target.files || []);
    const f = files[0];
    if(!f) return;
    const previewList = document.getElementById('image-preview-list');
    const r = new FileReader();
    r.onload = ev => {
        document.getElementById('img-thumb').innerHTML =
            `<img src="${ev.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:3px;"/>`;
        document.getElementById('img-label').textContent = files.length > 1
            ? `${files.length} images selected`
            : '1 image selected';
    };
    r.readAsDataURL(f);
    if (previewList) {
        previewList.innerHTML = files.map(file => {
            const src = URL.createObjectURL(file);
            return `<div class="vy-image-preview-item"><img src="${src}" alt="Selected image"></div>`;
        }).join('');
        previewList.style.display = files.length ? 'grid' : 'none';
    }
}

/* ─── Validation & Data ──────────────────── */
function validate(){
    const n = document.getElementById('item-name');
    if(!n.value.trim()){
        n.focus(); n.style.borderColor='#ef4444';
        setTimeout(()=>{ n.style.borderColor='#2563eb'; },2000);
        return false;
    }
    return true;
}
function collectData(){
    return {
        type:           iType,
        name:           document.getElementById('item-name').value.trim(),
        category:       settingsState.category ? (selCats[0]||'') : '',
        unit:           baseUnit,
        sale_price:     document.getElementById('sale-price').value,
        purchase_price: document.getElementById('purchase-price').value,
        opening_qty:    document.getElementById('opening-qty').value,
    };
}
function buildItemFormData(){
    const d = collectData();
    const fd = new FormData();
    const getVal = (id) => document.getElementById(id)?.value ?? '';
    Object.entries(d).forEach(([key, value]) => fd.append(key, value ?? ''));
    fd.append('item_code', getVal('item-code'));
    fd.append('wholesale_price', getVal('wholesale-price'));
    fd.append('min_wholesale_qty', getVal('min-wholesale-qty'));
    fd.append('at_price', getVal('at-price'));
    fd.append('as_of_date', getVal('as-of-date'));
    fd.append('min_stock', getVal('min-stock'));
    fd.append('location', getVal('location'));
    fd.append('mrp_label', getVal('mrp-label'));
    fd.append('mrp_value', getVal('mrp-value'));
    fd.append('barcode_value', getVal('barcode-value'));
    fd.append('description', getVal('item-description'));
    fd.append('serial_label', getVal('serial-label'));
    fd.append('batch_label', getVal('batch-label'));
    fd.append('batch_exp_date', getVal('batch-exp-date'));
    fd.append('batch_mfg_date', getVal('batch-mfg-date'));
    fd.append('model_no', getVal('model-no'));
    fd.append('size_value', getVal('size-value'));
    fd.append('mrp_enabled', document.getElementById('setting-mrp-enabled')?.checked ? '1' : '0');
    fd.append('barcode_enabled', document.getElementById('setting-barcode-toggle')?.checked ? '1' : '0');
    fd.append('description_enabled', '1');
    fd.append('serial_enabled', document.getElementById('setting-serial-enabled')?.checked ? '1' : '0');
    fd.append('batch_enabled', document.getElementById('setting-batch-enabled')?.checked ? '1' : '0');
    for (let index = 1; index <= 6; index++) {
        fd.append(`custom_field_${index}_enabled`, document.getElementById(`custom-field-enabled-${index}`)?.checked ? '1' : '0');
        fd.append(`custom_field_${index}_name`, getVal(`custom-field-name-${index}`));
        fd.append(`custom_field_${index}_print`, document.getElementById(`custom-field-print-${index}`)?.checked ? '1' : '0');
        fd.append(`custom_field_${index}_value`, getVal(`custom-inline-value-${index}`));
    }
    const imageFiles = Array.from(document.getElementById('img-file').files || []);
    imageFiles.forEach(file => fd.append('images[]', file));

    // Add item image (main image)
    const itemImageInput = document.getElementById('item-image');
    if (itemImageInput && itemImageInput.files && itemImageInput.files.length) {
        fd.append('item_image', itemImageInput.files[0]);
    }

    return fd;
}
function resetForm(){
    ['item-name','item-code','sale-price','purchase-price','wholesale-price',
     'opening-qty','at-price','min-stock','location','mrp-label','mrp-value',
     'barcode-value','item-description','serial-label','batch-label','batch-exp-date',
     'batch-mfg-date','model-no','size-value','min-wholesale-qty'].forEach(id=>{
        const el=document.getElementById(id); if(el) el.value='';
    });
    selCats=[]; baseUnit=''; secUnit=''; wsOpen=false;
    document.getElementById('wholesale-row').style.display='none';
    document.getElementById('ws-icon').textContent='+';
    document.getElementById('ws-label').textContent='Add Wholesale Price';
    const cl=document.getElementById('cat-label');
    cl.textContent='Category'; cl.style.color='#9ca3af';
    const ub=document.getElementById('unit-trigger-btn');
    ub.innerHTML='Select Unit'; ub.classList.remove('chosen');
    ub.style.background=''; ub.style.color=''; ub.style.borderColor='';
    document.getElementById('unit-conv-label').textContent='';
    document.getElementById('img-thumb').innerHTML=
        '<svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="#6b7280" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>';
    document.getElementById('img-label').textContent = 'Add Item Images';
    const imageInput = document.getElementById('img-file');
    if (imageInput) imageInput.value = '';
    const itemImageInput = document.getElementById('item-image');
    if (itemImageInput) itemImageInput.value = '';
    const itemImageThumb = document.getElementById('item-image-thumb');
    if (itemImageThumb) {
        itemImageThumb.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>';
        itemImageThumb.style.border = '1.5px solid #93c5fd';
        itemImageThumb.style.boxShadow = 'none';
    }
    const previewList = document.getElementById('image-preview-list');
    if (previewList) {
        previewList.innerHTML = '';
        previewList.style.display = 'none';
    }
    document.getElementById('type-toggle').checked=false;
    iType='product';
    applyTypeUI(false);
    settingsState = { wholesale: true, category: true, mrp: false, barcode: false, description: true, serial: false, batch: false };
    document.getElementById('setting-wholesale-toggle').checked = true;
    document.getElementById('setting-category-toggle').checked = true;
    document.getElementById('setting-mrp-enabled').checked = false;
    ['setting-barcode-toggle','setting-serial-enabled','setting-batch-enabled','setting-exp-enabled','setting-mfg-enabled','setting-model-enabled','setting-size-enabled'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.checked = false;
    });
    const descriptionToggle = document.getElementById('setting-description-toggle');
    if (descriptionToggle) descriptionToggle.checked = true;
    for (let index = 1; index <= 6; index++) {
        const enabled = document.getElementById(`custom-field-enabled-${index}`);
        const name = document.getElementById(`custom-field-name-${index}`);
        const print = document.getElementById(`custom-field-print-${index}`);
        const preview = document.getElementById(`custom-preview-${index}`);
        const inlineItem = document.getElementById(`custom-inline-item-${index}`);
        const inlineInput = document.getElementById(`custom-inline-value-${index}`);
        if (enabled) enabled.checked = false;
        if (name) name.value = '';
        if (print) print.checked = false;
        if (preview) preview.value = '';
        if (inlineInput) inlineInput.value = '';
        if (inlineItem) inlineItem.classList.remove('show');
    }
    document.getElementById('custom-fields-sec').classList.remove('show');
    document.getElementById('custom-inline-fields').classList.remove('show');
    saveAdditionalFieldsSettings(true);
    saveCustomFieldsSettings(true);
    switchTab('pricing'); renderCats(); updateSaveBtn();
}

/* ─── Preview Item Image ────────────────── */
function previewItemImage(event) {
    const file = event.target.files[0];
    const thumb = document.getElementById('item-image-thumb');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            thumb.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover;"/>`;
            thumb.style.border = '1.5px solid #60a5fa';
            thumb.style.boxShadow = '0 0 0 2px #e0f2fe';
        };
        reader.readAsDataURL(file);
    } else {
        thumb.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="#2563eb" stroke-width="1.8">
            <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>`;
        thumb.style.border = '1.5px solid #93c5fd';
        thumb.style.boxShadow = 'none';
    }
}

/* ─── SAVE & NEW (stay on form) ─────────── */
function saveAndNew(){
    if(!validate()) return;
    const formData = buildItemFormData();
    fetch('{{ route("items.store") }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(async res => {
        const raw = await res.text();
        let data = {};
        try { data = raw ? JSON.parse(raw) : {}; } catch (e) {}
        if(!res.ok) throw new Error(data.message || ('Server error: ' + res.status));
        if (window.parent && window.parent !== window) {
            window.parent.postMessage({ type: 'item-saved', item: data.item || null }, '*');
        }
        return data;
    })
    .then(() => {
        resetForm();
        showToast('Saved! Add another item.');
        setTimeout(()=>document.getElementById('item-name').focus(),50);
    })
    .catch(err => {
        showToast('Error: ' + err.message);
        console.error(err);
    });
}

function saveItem(){
    if(!validate()) return;
    const formData = buildItemFormData();
    fetch('{{ route("items.store") }}', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(async res => {
        const raw = await res.text();
        let data = {};
        try { data = raw ? JSON.parse(raw) : {}; } catch (e) {}
        if(!res.ok) throw new Error(data.message || ('Server error: ' + res.status));
        if (window.parent && window.parent !== window) {
            window.parent.postMessage({ type: 'item-saved', item: data.item || null }, '*');
        }
        return data;
    })
    .then(() => {
        if (window.parent && window.parent !== window) {
            showToast('Saved!');
            return;
        }
        showToast('Saved! Redirecting...');
        setTimeout(goToList, 500);
    })
    .catch(err => {
        showToast('Error: ' + err.message);
        console.error(err);
    });
}

function esc(s){
    return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

document.addEventListener('DOMContentLoaded', ()=>{
    const d=new Date();
    const asOf = document.getElementById('as-of-date');
    if(asOf) asOf.value = d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-'+String(d.getDate()).padStart(2,'0');
    updateSaveBtn();
    loadCats();
    saveAdditionalFieldsSettings(true);
    saveCustomFieldsSettings(true);

    // Auto-switch to service tab if ?type=service in URL
    const params = new URLSearchParams(window.location.search);
    if(params.get('type') === 'service'){
        iType = 'service';
        const toggle = document.getElementById('type-toggle');
        toggle.checked = true;
        applyTypeUI(true);
    }
});
</script>
@endpush
