@extends('layouts.app')
@section('title', 'Units')
@section('page', 'items')

@push('styles')
<style>
* { box-sizing: border-box; margin: 0; padding: 0; }
.units-page { display: flex; flex-direction: column; height: 100vh; background: #fff; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif; overflow: hidden; }

/* TABS */
.units-tabs { display: flex; border-bottom: 1.5px solid #e5e7eb; background: #fff; flex-shrink: 0; }
.units-tab { flex: 1; text-align: center; padding: 16px 0; font-size: 13px; font-weight: 600; letter-spacing: .08em; color: #b0b8c4; cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -1.5px; transition: all .15s; text-transform: uppercase; }
.units-tab:hover { color: #6b7280; }
.units-tab.active { color: #3b82f6; border-bottom-color: #3b82f6; }

/* BODY */
.units-body { display: flex; flex: 1; min-height: 0; overflow: hidden; }

/* LEFT */
.units-left { width: 300px; flex-shrink: 0; border-right: 1px solid #e2e8f0; display: flex; flex-direction: column; }
.units-toolbar { display: flex; align-items: center; gap: 10px; padding: 12px 14px; border-bottom: 1px solid #f3f4f6; }
.units-search-btn { width: 36px; height: 36px; border: 1.5px solid #e5e7eb; border-radius: 50%; background: #fff; display: flex; align-items: center; justify-content: center; cursor: pointer; flex-shrink: 0; }
.units-search-btn:hover { border-color: #93c5fd; }
.units-search-wrap { flex: 1; display: none; }
.units-search-wrap.open { display: block; }
.units-search-input { width: 100%; border: 1.5px solid #3b82f6; border-radius: 7px; padding: 7px 10px; font-size: 13px; outline: none; }
.units-add-btn { display: inline-flex; align-items: center; gap: 6px; background: #f59e0b; color: #fff; border: none; border-radius: 22px; padding: 9px 18px; font-size: 13px; font-weight: 700; cursor: pointer; margin-left: auto; white-space: nowrap; }
.units-add-btn:hover { background: #d97706; }
.units-list-hdr { display: flex; align-items: center; padding: 10px 16px; border-bottom: 1px solid #f3f4f6; font-size: 11px; font-weight: 800; text-transform: uppercase; letter-spacing: .07em; color: #6b7280; }
.units-list { flex: 1; overflow-y: auto; }
.unit-row { display: flex; align-items: center; padding: 13px 16px; border-bottom: 1px solid #f3f4f6; cursor: pointer; transition: background .12s; }
.unit-row:hover { background: #f9fafb; }
.unit-row.active { background: #dbeafe; }
.unit-row-name { flex: 1; font-size: 14px; color: #111827; font-weight: 600; }
.unit-row.active .unit-row-name { color: #1d4ed8; }
.unit-row-short { width: 60px; font-size: 14px; color: #374151; }
.unit-row-more { position: relative; width: 28px; flex-shrink: 0; }
.unit-row-more-btn { width: 28px; height: 28px; display: flex; align-items: center; justify-content: center; color: #9ca3af; cursor: pointer; border-radius: 4px; background: none; border: none; font-size: 16px; }
.unit-row-more-btn:hover { background: #f3f4f6; color: #374151; }
.unit-row-dd { position: absolute; right: 0; top: calc(100% + 2px); background: #fff; border: 1px solid #e5e7eb; border-radius: 7px; box-shadow: 0 6px 20px rgba(0,0,0,.12); z-index: 600; min-width: 130px; display: none; }
.unit-row-dd.open { display: block; }
.unit-dd-item { padding: 10px 15px; cursor: pointer; font-size: 13px; color: #374151; }
.unit-dd-item:hover { background: #f9fafb; }
.unit-dd-item.danger { color: #ef4444; }
.unit-dd-item.danger:hover { background: #fef2f2; }

/* RIGHT */
.units-right { flex: 1; display: flex; flex-direction: column; background: #fff; min-width: 0; }
.units-right-hdr { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-bottom: 1px solid #e2e8f0; flex-shrink: 0; }
.units-right-title { font-size: 14px; font-weight: 800; letter-spacing: .08em; color: #374151; text-transform: uppercase; }
.units-conv-btn { background: #3b82f6; color: #fff; border: none; border-radius: 7px; padding: 9px 22px; font-size: 13px; font-weight: 700; cursor: pointer; }
.units-conv-btn:hover { background: #2563eb; }
.units-conv-hdr { display: flex; align-items: center; justify-content: space-between; padding: 14px 22px 12px; border-bottom: 1px solid #f0f0f0; }
.units-conv-title { font-size: 12px; font-weight: 800; letter-spacing: .08em; color: #374151; text-transform: uppercase; }
.units-conv-search { border: 1px solid #e5e7eb; border-radius: 7px; padding: 7px 10px 7px 30px; font-size: 13px; outline: none; width: 220px; background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' fill='none' viewBox='0 0 24 24' stroke='%23b0b8c4' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath stroke-linecap='round' d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat 9px center; }
.units-conv-search:focus { border-color: #3b82f6; }
.units-tbl-wrap { flex: 1; overflow-y: auto; }
.units-tbl { width: 100%; border-collapse: collapse; }
.units-tbl th { padding: 10px 16px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .07em; color: #9ca3af; background: #fff; border-bottom: 1px solid #ebebeb; text-align: left; }
.units-tbl td { padding: 13px 16px; font-size: 13px; color: #374151; border-bottom: 1px solid #f3f4f6; }
.units-norows { text-align: center; color: #9ca3af; padding: 70px 0; font-size: 14px; }

/* MODALS */
.u-overlay { position: fixed; inset: 0; z-index: 2000; background: rgba(0,0,0,.45); display: none; align-items: center; justify-content: center; }
.u-overlay.open { display: flex; }
.u-mbox { background: #fff; border-radius: 10px; box-shadow: 0 10px 40px rgba(0,0,0,.2); width: 480px; max-width: 95vw; animation: upop .15s ease-out; }
@keyframes upop { from{opacity:0;transform:scale(.95)} to{opacity:1;transform:scale(1)} }
.u-mhdr { background: #dbeafe; padding: 18px 24px; border-radius: 10px 10px 0 0; font-size: 16px; font-weight: 700; color: #1e3a8a; display: flex; align-items: center; justify-content: space-between; }
.u-mclose { background: none; border: none; cursor: pointer; color: #6b7280; font-size: 18px; }
.u-mbody { padding: 24px; }
.u-mlabel { font-size: 12px; font-weight: 700; color: #3b82f6; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 8px; display: block; }
.u-minput { width: 100%; border: 1.5px solid #3b82f6; border-radius: 7px; padding: 11px 14px; font-size: 14px; color: #374151; outline: none; }
.u-minput:focus { box-shadow: 0 0 0 3px rgba(59,130,246,.15); }
.u-mfoot { display: flex; justify-content: flex-end; gap: 10px; padding: 16px 24px; border-top: 1px solid #e5e7eb; }
.u-mbtn { border: none; border-radius: 7px; padding: 10px 28px; font-size: 14px; font-weight: 700; cursor: pointer; }
.u-mbtn-save { background: #3b82f6; color: #fff; }
.u-mbtn-save:hover { background: #2563eb; }
.u-mbtn-new { background: #3b82f6; color: #fff; }
.u-mbtn-new:hover { background: #2563eb; }

/* CONVERSION MODAL */
.conv-fields { display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
.conv-select { flex: 1; min-width: 140px; border: 1.5px solid #d1d5db; border-radius: 7px; padding: 11px 14px; font-size: 14px; color: #374151; outline: none; background: #fff; cursor: pointer; }
.conv-select:focus { border-color: #3b82f6; }
.conv-rate { width: 90px; border: 1.5px solid #d1d5db; border-radius: 7px; padding: 11px 14px; font-size: 14px; color: #374151; outline: none; text-align: center; }
.conv-rate:focus { border-color: #3b82f6; }
.conv-eq { font-size: 18px; font-weight: 700; color: #374151; }

#u-toast { position: fixed; bottom: 28px; left: 50%; transform: translateX(-50%) translateY(20px); background: #111827; color: #fff; padding: 10px 22px; border-radius: 8px; font-size: 13px; opacity: 0; transition: all .25s; z-index: 9999; pointer-events: none; }
#u-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
</style>
@endpush

@section('content')
<div class="units-page">

    {{-- TABS --}}
    <div class="units-tabs">
        <div class="units-tab" onclick="location.href='{{ route("items") }}'">PRODUCTS</div>
        <div class="units-tab" onclick="location.href='{{ route("items.services") }}'">SERVICES</div>
        <div class="units-tab" onclick="location.href='{{ route("items.category") }}'">CATEGORY</div>
        <div class="units-tab active">UNITS</div>
    </div>

    <div class="units-body">

        {{-- LEFT --}}
        <div class="units-left">
            <div class="units-toolbar">
                <button class="units-search-btn" onclick="toggleSearch()">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                </button>
                <div class="units-search-wrap" id="usw">
                    <input class="units-search-input" id="usq" placeholder="Search units..." oninput="filterUnits()"/>
                </div>
                <button class="units-add-btn" onclick="openAddUnit()">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="3"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                    Add Units
                </button>
            </div>
            <div class="units-list-hdr">
                <span style="flex:1;">FULLNAME</span>
                <span style="width:60px;">SHORTNAME</span>
                <span style="width:28px;"></span>
            </div>
            <div class="units-list" id="units-list"></div>
        </div>

        {{-- RIGHT --}}
        <div class="units-right">
            <div class="units-right-hdr">
                <span class="units-right-title" id="r-unit-title">BAGS</span>
                <button class="units-conv-btn" onclick="openAddConversion()">Add Conversion</button>
            </div>
            <div class="units-conv-hdr">
                <span class="units-conv-title">UNITS</span>
                <input class="units-conv-search" placeholder="Search..." oninput="filterConversions(this.value)"/>
            </div>
            <div class="units-tbl-wrap">
                <table class="units-tbl">
                    <thead><tr>
                        <th></th>
                        <th>CONVERSION</th>
                    </tr></thead>
                    <tbody id="conv-tbody">
                        <tr><td colspan="2" class="units-norows">No Rows To Show</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

<div id="u-toast"></div>

{{-- ADD UNIT MODAL --}}
<div class="u-overlay" id="add-unit-overlay" onclick="if(event.target===this)closeAddUnit()">
    <div class="u-mbox">
        <div class="u-mhdr">
            <span id="add-unit-title">New Unit</span>
            <button class="u-mclose" onclick="closeAddUnit()">✕</button>
        </div>
        <div class="u-mbody">
            <input type="hidden" id="edit-unit-id"/>
            <div style="display:flex;gap:16px;">
                <div style="flex:1;">
                    <label class="u-mlabel">Unit Name</label>
                    <input class="u-minput" id="unit-name-input" placeholder="e.g. KILOGRAMS" onkeydown="if(event.key==='Enter')saveUnit()"/>
                </div>
                <div style="width:140px;">
                    <label class="u-mlabel">Short Name</label>
                    <input class="u-minput" id="unit-short-input" placeholder="e.g. Kg" onkeydown="if(event.key==='Enter')saveUnit()"/>
                </div>
            </div>
        </div>
        <div class="u-mfoot">
            <button class="u-mbtn u-mbtn-new" onclick="saveUnitAndNew()">SAVE NEW</button>
            <button class="u-mbtn u-mbtn-save" onclick="saveUnit()">SAVE</button>
        </div>
    </div>
</div>

{{-- ADD CONVERSION MODAL --}}
<div class="u-overlay" id="add-conv-overlay" onclick="if(event.target===this)closeAddConversion()">
    <div class="u-mbox">
        <div class="u-mhdr">
            <span>Add Conversion</span>
            <button class="u-mclose" onclick="closeAddConversion()">✕</button>
        </div>
        <div class="u-mbody">
    <div class="conv-fields" style="display:flex; align-items:flex-end; gap:10px; flex-wrap:nowrap;">
        <div style="flex:1;">
            <label class="u-mlabel">Base Unit</label>
            <select class="conv-select" id="conv-base"></select>
        </div>
        <div style="display:flex; align-items:center; padding-bottom:11px;">
            <span class="conv-eq">=</span>
        </div>
        <div style="width:80px;">
            <label class="u-mlabel">Rate</label>
            <input type="number" class="conv-rate" id="conv-rate-input" value="0" min="0" style="width:100%;"/>
        </div>
        <div style="flex:1;">
            <label class="u-mlabel">Secondary Unit</label>
            <select class="conv-select" id="conv-secondary"></select>
        </div>
    </div>
</div>
        <div class="u-mfoot">
            <button class="u-mbtn u-mbtn-new" onclick="saveConversionAndNew()">SAVE & NEW</button>
            <button class="u-mbtn u-mbtn-save" onclick="saveConversion()">SAVE</button>
        </div>
    </div>
</div>

{{-- DELETE MODAL --}}
<div class="u-overlay" id="del-overlay">
    <div class="u-mbox" style="width:400px;">
        <div class="u-mhdr" style="background:#fee2e2;">
            <span style="color:#991b1b;">Delete Unit?</span>
            <button class="u-mclose" onclick="closeDel()">✕</button>
        </div>
        <div class="u-mbody" style="font-size:14px;color:#374151;">This unit will be permanently deleted.</div>
        <div class="u-mfoot">
            <button class="u-mbtn" style="background:#e5e7eb;color:#374151;" onclick="closeDel()">NO</button>
            <button class="u-mbtn u-mbtn-save" style="background:#ef4444;" onclick="confirmDel()">YES</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;

// Default units
let units = [
    {id:'bag',  name:'BAGS',     short:'Bag'},
    {id:'btl',  name:'BOTTLES',  short:'Btl'},
    {id:'box',  name:'BOX',      short:'Box'},
    {id:'bdl',  name:'BUNDLES',  short:'Bdl'},
    {id:'can',  name:'CANS',     short:'Can'},
    {id:'ctn',  name:'CARTONS',  short:'Ctn'},
    {id:'dzn',  name:'DOZENS',   short:'Dzn'},
    {id:'gm',   name:'GRAMMES',  short:'Gm'},
    {id:'kg',   name:'KILOGRAMS',short:'Kg'},
    {id:'ltr',  name:'LITRE',    short:'Ltr'},
    {id:'mtr',  name:'METERS',   short:'Mtr'},
    {id:'ml',   name:'MILILITRE',short:'Ml'},
    {id:'nos',  name:'NUMBERS',  short:'Nos'},
    {id:'pac',  name:'PACKS',    short:'Pac'},
];
let conversions = {};
let selUnitId = units[0].id;
let delUnitId = null;

document.addEventListener('DOMContentLoaded', () => {
    renderUnits(units);
    selectUnit(units[0].id);
    document.addEventListener('click', () => {
        document.querySelectorAll('.unit-row-dd.open').forEach(d => d.classList.remove('open'));
    });
});

function toast(m) {
    const t = document.getElementById('u-toast');
    t.textContent = m; t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}

function toggleSearch() {
    const w = document.getElementById('usw');
    w.classList.toggle('open');
    if (w.classList.contains('open')) document.getElementById('usq').focus();
}
function filterUnits() {
    const q = document.getElementById('usq').value.toLowerCase();
    renderUnits(units.filter(u => u.name.toLowerCase().includes(q) || u.short.toLowerCase().includes(q)));
}

function renderUnits(list) {
    document.getElementById('units-list').innerHTML = list.map(u => `
        <div class="unit-row ${selUnitId===u.id?'active':''}" onclick="selectUnit('${u.id}')">
            <span class="unit-row-name">${esc(u.name)}</span>
            <span class="unit-row-short">${esc(u.short)}</span>
            <div class="unit-row-more" onclick="event.stopPropagation()">
                <button class="unit-row-more-btn" onclick="toggleDD(event,'udd-${u.id}')">⋮</button>
                <div class="unit-row-dd" id="udd-${u.id}">
                    <div class="unit-dd-item" onclick="openEditUnit('${u.id}')">Edit</div>
                    <div class="unit-dd-item danger" onclick="openDel('${u.id}')">Delete</div>
                </div>
            </div>
        </div>`).join('');
}

function toggleDD(e, id) {
    e.stopPropagation();
    document.querySelectorAll('.unit-row-dd.open').forEach(d => d.classList.remove('open'));
    document.getElementById(id)?.classList.toggle('open');
}

function selectUnit(id) {
    selUnitId = id;
    const u = units.find(x => x.id === id);
    if (!u) return;
    document.querySelectorAll('.unit-row').forEach(r => r.classList.remove('active'));
    document.querySelectorAll('.unit-row').forEach(r => {
        if (r.querySelector('.unit-row-name')?.textContent === u.name) r.classList.add('active');
    });
    document.getElementById('r-unit-title').textContent = u.name;
    renderConversions(id);
    populateConvSelects();
}

function renderConversions(id) {
    const tbody = document.getElementById('conv-tbody');
    const convs = conversions[id] || [];
    if (!convs.length) {
        tbody.innerHTML = '<tr><td colspan="2" class="units-norows">No Rows To Show</td></tr>';
        return;
    }
    tbody.innerHTML = convs.map((c, i) => `
        <tr>
            <td style="width:40px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:#374151;"></span></td>
            <td>1 ${esc(c.base)} = ${c.rate} ${esc(c.secondary)}</td>
        </tr>`).join('');
}

function filterConversions(q) {
    const convs = (conversions[selUnitId] || []).filter(c =>
        c.base.toLowerCase().includes(q.toLowerCase()) || c.secondary.toLowerCase().includes(q.toLowerCase())
    );
    const tbody = document.getElementById('conv-tbody');
    if (!convs.length) { tbody.innerHTML = '<tr><td colspan="2" class="units-norows">No Rows To Show</td></tr>'; return; }
    tbody.innerHTML = convs.map(c => `<tr><td style="width:40px;"></td><td>1 ${esc(c.base)} = ${c.rate} ${esc(c.secondary)}</td></tr>`).join('');
}

/* ADD UNIT */
function openAddUnit() {
    document.getElementById('add-unit-title').textContent = 'New Unit';
    document.getElementById('edit-unit-id').value = '';
    document.getElementById('unit-name-input').value = '';
    document.getElementById('unit-short-input').value = '';
    document.getElementById('add-unit-overlay').classList.add('open');
    setTimeout(() => document.getElementById('unit-name-input').focus(), 80);
}
function closeAddUnit() { document.getElementById('add-unit-overlay').classList.remove('open'); }

function saveUnit(andNew = false) {
    const name  = document.getElementById('unit-name-input').value.trim().toUpperCase();
    const short = document.getElementById('unit-short-input').value.trim();
    const editId = document.getElementById('edit-unit-id').value;
    if (!name) { toast('Please enter a unit name.'); return; }
    if (editId) {
        const i = units.findIndex(u => u.id === editId);
        if (i !== -1) { units[i].name = name; units[i].short = short; }
        toast('Unit updated!');
    } else {
        const id = name.toLowerCase().replace(/\s+/g,'_') + '_' + Date.now();
        units.push({ id, name, short });
        selUnitId = id;
        toast('Unit added!');
    }
    renderUnits(units);
    selectUnit(selUnitId);
    if (andNew) {
        document.getElementById('unit-name-input').value = '';
        document.getElementById('unit-short-input').value = '';
        document.getElementById('edit-unit-id').value = '';
        document.getElementById('add-unit-title').textContent = 'New Unit';
        document.getElementById('unit-name-input').focus();
    } else {
        closeAddUnit();
    }
}
function saveUnitAndNew() { saveUnit(true); }

function openEditUnit(id) {
    const u = units.find(x => x.id === id);
    if (!u) return;
    document.getElementById('add-unit-title').textContent = 'Edit Unit';
    document.getElementById('edit-unit-id').value = id;
    document.getElementById('unit-name-input').value = u.name;
    document.getElementById('unit-short-input').value = u.short;
    document.querySelectorAll('.unit-row-dd.open').forEach(d => d.classList.remove('open'));
    document.getElementById('add-unit-overlay').classList.add('open');
    setTimeout(() => document.getElementById('unit-name-input').focus(), 80);
}

/* DELETE */
function openDel(id) {
    delUnitId = id;
    document.querySelectorAll('.unit-row-dd.open').forEach(d => d.classList.remove('open'));
    document.getElementById('del-overlay').classList.add('open');
}
function closeDel() { document.getElementById('del-overlay').classList.remove('open'); delUnitId = null; }
function confirmDel() {
    units = units.filter(u => u.id !== delUnitId);
    delete conversions[delUnitId];
    closeDel();
    selUnitId = units[0]?.id || null;
    renderUnits(units);
    if (selUnitId) selectUnit(selUnitId);
    toast('Unit deleted.');
}

/* CONVERSION */
function populateConvSelects() {
    const opts = units.map(u => `<option value="${esc(u.short)}">${esc(u.name)} (${esc(u.short)})</option>`).join('');
    document.getElementById('conv-base').innerHTML = opts;
    document.getElementById('conv-secondary').innerHTML = '<option value="">None</option>' + opts;
    const cur = units.find(u => u.id === selUnitId);
    if (cur) document.getElementById('conv-base').value = cur.short;
}
function openAddConversion() {
    populateConvSelects();
    document.getElementById('conv-rate-input').value = '0';
    document.getElementById('add-conv-overlay').classList.add('open');
}
function closeAddConversion() { document.getElementById('add-conv-overlay').classList.remove('open'); }
function saveConversion(andNew = false) {
    const base = document.getElementById('conv-base').value;
    const sec  = document.getElementById('conv-secondary').value;
    const rate = parseFloat(document.getElementById('conv-rate-input').value);
    if (!sec)  { toast('Please select a secondary unit.'); return; }
    if (base === sec) { toast('Base and secondary unit cannot be the same.'); return; }
    if (!rate || rate <= 0) { toast('Please enter a valid rate.'); return; }
    if (!conversions[selUnitId]) conversions[selUnitId] = [];
    conversions[selUnitId].push({ base, secondary: sec, rate });
    renderConversions(selUnitId);
    toast('Conversion added!');
    if (andNew) {
        document.getElementById('conv-rate-input').value = '0';
        document.getElementById('conv-secondary').value = '';
    } else {
        closeAddConversion();
    }
}
function saveConversionAndNew() { saveConversion(true); }

function esc(s) { return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
</script>
@endpush