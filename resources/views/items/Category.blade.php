@extends('layouts.app')
@section('title', 'Category')
@section('page', 'items')

@push('styles')
<style>
*{box-sizing:border-box;margin:0;padding:0;}
.cat-page{display:flex;flex-direction:column;height:100vh;background:#fff;overflow:hidden;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif;}
.cat-tabs{display:flex;border-bottom:1px solid #e5e7eb;background:#fff;flex-shrink:0;}
.cat-tab{flex:1;text-align:center;padding:16px 0;font-size:13px;font-weight:600;letter-spacing:.06em;color:#9ca3af;cursor:pointer;border-bottom:2px solid transparent;text-transform:uppercase;}
.cat-tab:hover{color:#4b5563;}
.cat-tab.active{color:#3b82f6;border-bottom-color:#3b82f6;font-weight:700;}
.cat-body{display:flex;flex:1;min-height:0;overflow:hidden;}

/* LEFT */
.cat-left{width:380px;flex-shrink:0;border-right:1px solid #e2e8f0;display:flex;flex-direction:column;}
.cat-toolbar{display:flex;align-items:center;gap:10px;padding:12px 14px;border-bottom:1px solid #f3f4f6;}
.cat-search-btn{width:36px;height:36px;border:1.5px solid #e5e7eb;border-radius:50%;background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;}
.cat-search-btn:hover{border-color:#93c5fd;}
.cat-search-wrap{flex:1;position:relative;display:none;}
.cat-search-wrap.open{display:block;}
.cat-search-input{width:100%;border:1.5px solid #3b82f6;border-radius:7px;padding:7px 10px 7px 28px;font-size:13px;outline:none;}
.cat-si{position:absolute;left:8px;top:50%;transform:translateY(-50%);}
.cat-add-btn{display:inline-flex;align-items:center;gap:6px;background:#f59e0b;color:#fff;border:none;border-radius:22px;padding:9px 18px;font-size:13px;font-weight:700;cursor:pointer;margin-left:auto;white-space:nowrap;}
.cat-add-btn:hover{background:#d97706;}
.cat-list-hdr{display:flex;align-items:center;padding:10px 16px;border-bottom:1px solid #f3f4f6;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.07em;color:#6b7280;}
.cat-list{flex:1;overflow-y:auto;}
.cat-row{display:flex;align-items:center;padding:13px 16px;border-bottom:1px solid #f3f4f6;cursor:pointer;transition:background .12s;}
.cat-row:hover{background:#f9fafb;}
.cat-row.active{background:#dbeafe;}
.cat-row-name{flex:1;font-size:14px;color:#111827;font-weight:600;}
.cat-row.active .cat-row-name{color:#1d4ed8;}
.cat-row-count{width:36px;text-align:right;font-size:14px;color:#374151;font-weight:500;}
.cat-row-more{position:relative;width:28px;flex-shrink:0;margin-left:4px;}
.cat-row-more-btn{width:28px;height:28px;display:flex;align-items:center;justify-content:center;color:#9ca3af;cursor:pointer;border-radius:4px;background:none;border:none;font-size:16px;}
.cat-row-more-btn:hover{background:#f3f4f6;color:#374151;}
.cat-row-dd{position:absolute;right:0;top:calc(100% + 2px);background:#fff;border:1px solid #e5e7eb;border-radius:7px;box-shadow:0 6px 20px rgba(0,0,0,.12);z-index:600;min-width:130px;display:none;}
.cat-row-dd.open{display:block;}
.cat-dd-item{padding:10px 15px;cursor:pointer;font-size:13px;color:#374151;}
.cat-dd-item:hover{background:#f9fafb;}
.cat-dd-item.danger{color:#ef4444;}
.cat-dd-item.danger:hover{background:#fef2f2;}

/* RIGHT */
.cat-right{flex:1;display:flex;flex-direction:column;background:#fff;min-width:0;}
.cat-info-bar{display:flex;align-items:center;justify-content:space-between;padding:14px 24px;border-bottom:1px solid #e2e8f0;flex-shrink:0;}
.cat-info-title{font-size:12px;font-weight:800;letter-spacing:.08em;color:#374151;text-transform:uppercase;}
.cat-info-count{font-size:14px;color:#374151;margin-top:3px;}
.cat-move-btn{background:#3b82f6;color:#fff;border:none;border-radius:7px;padding:9px 22px;font-size:13px;font-weight:700;cursor:pointer;}
.cat-move-btn:hover{background:#2563eb;}
.cat-items-hdr{display:flex;align-items:center;justify-content:space-between;padding:14px 22px 12px;border-bottom:1px solid #f0f0f0;}
.cat-items-title{font-size:12px;font-weight:800;letter-spacing:.08em;color:#374151;text-transform:uppercase;}
.cat-items-search{border:1px solid #e5e7eb;border-radius:7px;padding:7px 10px 7px 30px;font-size:13px;outline:none;width:250px;background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='13' fill='none' viewBox='0 0 24 24' stroke='%23b0b8c4' stroke-width='2'%3E%3Ccircle cx='11' cy='11' r='8'/%3E%3Cpath stroke-linecap='round' d='M21 21l-4.35-4.35'/%3E%3C/svg%3E") no-repeat 9px center;}
.cat-items-search:focus{border-color:#3b82f6;outline:none;}
.cat-tbl-wrap{flex:1;overflow-y:auto;}
.cat-tbl{width:100%;border-collapse:collapse;}
.cat-tbl th{padding:10px 16px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.07em;color:#9ca3af;background:#fff;border-bottom:1px solid #ebebeb;text-align:left;white-space:nowrap;}
.cat-tbl th .thi{display:inline-flex;align-items:center;gap:5px;}
.cat-tbl th .tfi{color:#c9d0d9;font-size:10px;cursor:pointer;}
.cat-tbl th .tfi:hover{color:#e53e3e;}
.cat-tbl td{padding:13px 16px;font-size:13px;color:#374151;border-bottom:1px solid #f3f4f6;}
.cat-tbl tbody tr:hover td{background:#fafafa;}
.cat-norows{text-align:center;color:#9ca3af;padding:70px 0;font-size:14px;}

/* MODALS */
.cat-overlay{position:fixed;inset:0;z-index:2000;background:rgba(0,0,0,.45);display:none;align-items:center;justify-content:center;}
.cat-overlay.open{display:flex;}
.cat-mbox{background:#fff;border-radius:10px;box-shadow:0 10px 40px rgba(0,0,0,.2);width:440px;max-width:95vw;animation:cpop .15s ease-out;}
@keyframes cpop{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
.cat-mhdr{display:flex;align-items:center;justify-content:space-between;padding:22px 26px 18px;}
.cat-mtitle{font-size:18px;font-weight:700;color:#111827;}
.cat-mclose{background:none;border:none;cursor:pointer;color:#9ca3af;font-size:20px;width:28px;height:28px;display:flex;align-items:center;justify-content:center;border-radius:5px;}
.cat-mclose:hover{background:#f3f4f6;color:#374151;}
.cat-mbody{padding:0 26px 26px;}
.cat-mlabel{font-size:13px;color:#374151;font-weight:500;margin-bottom:8px;display:block;}
.cat-minput{width:100%;border:1.5px solid #3b82f6;border-radius:7px;padding:11px 14px;font-size:14px;color:#374151;outline:none;}
.cat-minput:focus{border-color:#2563eb;box-shadow:0 0 0 3px rgba(59,130,246,.15);}
.cat-minput::placeholder{color:#d1d5db;}
.cat-mcreate{width:100%;margin-top:22px;background:#ef4444;color:#fff;border:none;border-radius:28px;padding:13px 0;font-size:15px;font-weight:700;cursor:pointer;}
.cat-mcreate:hover{background:#dc2626;}

/* DELETE MODAL */
#cdel-overlay{position:fixed;inset:0;z-index:2100;background:rgba(0,0,0,.45);display:none;align-items:center;justify-content:center;}
#cdel-overlay.open{display:flex;}
#cdel-box{background:#fff;border-radius:8px;box-shadow:0 10px 40px rgba(0,0,0,.25);width:420px;max-width:95vw;animation:cpop .15s ease-out;}
.cdel-hdr{display:flex;align-items:center;justify-content:space-between;padding:18px 20px 14px;background:#e8f0fb;border-radius:8px 8px 0 0;}
.cdel-hdr span{font-size:14px;font-weight:700;color:#1a2a4a;}
.cdel-hdr button{background:none;border:none;cursor:pointer;font-size:16px;color:#6b7280;width:24px;height:24px;display:flex;align-items:center;justify-content:center;border-radius:4px;}
.cdel-hdr button:hover{background:#d1d5db;}
.cdel-body{padding:20px 24px;font-size:14px;color:#374151;}
.cdel-foot{display:flex;justify-content:flex-end;gap:10px;padding:14px 20px 18px;}
.cdel-btn{background:#5b9bd5;border:none;border-radius:5px;padding:8px 26px;font-size:13px;font-weight:600;color:#fff;cursor:pointer;}
.cdel-btn:hover{background:#3a7bbf;}

#cat-toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(20px);background:#111827;color:#fff;padding:10px 22px;border-radius:8px;font-size:13px;opacity:0;transition:all .25s;z-index:9999;pointer-events:none;}
#cat-toast.show{opacity:1;transform:translateX(-50%) translateY(0);}
</style>
@endpush

@section('content')
<div class="cat-page">

    <div class="cat-tabs">
        <div class="cat-tab" onclick="location.href='/items'">PRODUCTS</div>
        <div class="cat-tab" onclick="location.href='/items/services'">SERVICES</div>
        <div class="cat-tab active">CATEGORY</div>
        <div class="cat-tab" onclick="location.href='/items/units'">UNITS</div>
    </div>

    <div class="cat-body">

        {{-- LEFT --}}
        <div class="cat-left">
            <div class="cat-toolbar">
                <button class="cat-search-btn" onclick="toggleSearch()">
                    <svg width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="#6b7280" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                </button>
                <div class="cat-search-wrap" id="sw">
                    <svg class="cat-si" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="#9ca3af" stroke-width="2"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" d="M21 21l-4.35-4.35"/></svg>
                    <input class="cat-search-input" id="sq" placeholder="Search categories..." oninput="filterList()"/>
                </div>
                <button class="cat-add-btn" onclick="openAdd()">
                    <svg width="11" height="11" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="3"><path stroke-linecap="round" d="M12 4v16m8-8H4"/></svg>
                    Add Category
                </button>
            </div>
            <div class="cat-list-hdr">
                <span style="flex:1;">CATEGORY</span>
                <span style="width:60px;text-align:right;padding-right:34px;">ITEM</span>
            </div>
            <div class="cat-list" id="cat-list"></div>
        </div>

        {{-- RIGHT --}}
        <div class="cat-right">
            <div class="cat-info-bar">
                <div>
                    <div class="cat-info-title" id="r-title">ITEMS NOT IN ANY CATEGORY</div>
                    <div class="cat-info-count" id="r-count">0</div>
                </div>
                <button class="cat-move-btn" onclick="openMoveModal()">Move To This Category</button>
            </div>
            <div class="cat-items-hdr">
                <span class="cat-items-title">ITEMS</span>
                <input class="cat-items-search" placeholder="Search items..." oninput="filterItems(this.value)"/>
            </div>
            <div class="cat-tbl-wrap">
                <table class="cat-tbl">
                    <thead><tr>
                        <th><span class="thi">NAME <i class="fa-solid fa-filter tfi"></i></span></th>
                        <th style="width:160px;"><span class="thi">QUANTITY <i class="fa-solid fa-filter tfi"></i></span></th>
                        <th style="width:200px;"><span class="thi">STOCK VALUE <i class="fa-solid fa-filter tfi"></i></span></th>
                    </tr></thead>
                    <tbody id="items-tbody"><tr><td colspan="3" class="cat-norows">No Rows To Show</td></tr></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div id="cat-toast"></div>

{{-- ADD MODAL --}}
<div class="cat-overlay" id="add-overlay" onclick="if(event.target===this)closeAdd()">
    <div class="cat-mbox">
        <div class="cat-mhdr">
            <span class="cat-mtitle">Add Category</span>
            <button class="cat-mclose" onclick="closeAdd()">✕</button>
        </div>
        <div class="cat-mbody">
            <label class="cat-mlabel">Enter Category Name</label>
            <input class="cat-minput" id="add-input" placeholder="e.g., Grocery" onkeydown="if(event.key==='Enter')saveAdd()"/>
            <button class="cat-mcreate" onclick="saveAdd()">Create</button>
        </div>
    </div>
</div>

{{-- EDIT MODAL --}}
<div class="cat-overlay" id="edit-overlay" onclick="if(event.target===this)closeEdit()">
    <div class="cat-mbox">
        <div class="cat-mhdr">
            <span class="cat-mtitle">Edit Category</span>
            <button class="cat-mclose" onclick="closeEdit()">✕</button>
        </div>
        <div class="cat-mbody">
            <input type="hidden" id="edit-id"/>
            <label class="cat-mlabel">Category Name</label>
            <input class="cat-minput" id="edit-input" placeholder="Category name" onkeydown="if(event.key==='Enter')saveEdit()"/>
            <button class="cat-mcreate" onclick="saveEdit()">Update</button>
        </div>
    </div>
</div>

{{-- DELETE MODAL --}}
<div id="cdel-overlay">
    <div id="cdel-box">
        <div class="cdel-hdr">
            <span>Are you sure you want to delete this Category?</span>
            <button onclick="closeDel()">✕</button>
        </div>
        <div class="cdel-body">This category will be permanently deleted.</div>
        <div class="cdel-foot">
            <button class="cdel-btn" onclick="confirmDel()">YES</button>
            <button class="cdel-btn" onclick="closeDel()">NO</button>
        </div>
    </div>
</div>
{{-- MOVE MODAL --}}
<div class="cat-overlay" id="move-overlay" onclick="if(event.target===this)closeMoveModal()">
    <div class="cat-mbox" style="width:560px;">
        <div class="cat-mhdr">
            <span class="cat-mtitle">Select Items</span>
            <button class="cat-mclose" onclick="closeMoveModal()">✕</button>
        </div>
        <div style="padding:0 26px 10px;">
            <input placeholder="Search items..."
                oninput="document.querySelectorAll('#move-items-tbody tr').forEach(r=>r.style.display=r.textContent.toLowerCase().includes(this.value.toLowerCase())?'':'none')"
                style="width:100%;border:1.5px solid #3b82f6;border-radius:7px;padding:10px 14px;font-size:13px;outline:none;margin-bottom:10px;"/>
        </div>
        <div style="max-height:300px;overflow-y:auto;border-top:1px solid #f3f4f6;">
            <table style="width:100%;border-collapse:collapse;">
                <thead><tr>
                    <th style="padding:10px 16px;font-size:11px;color:#9ca3af;border-bottom:1px solid #f3f4f6;"></th>
                    <th style="padding:10px 16px;font-size:11px;color:#9ca3af;text-align:left;border-bottom:1px solid #f3f4f6;">ITEM NAME</th>
                    <th style="padding:10px 16px;font-size:11px;color:#9ca3af;text-align:right;border-bottom:1px solid #f3f4f6;">QUANTITY</th>
                </tr></thead>
                <tbody id="move-items-tbody"></tbody>
            </table>
        </div>
        <div style="padding:14px 26px;border-top:1px solid #f3f4f6;display:flex;align-items:center;gap:10px;">
            <input type="checkbox" id="remove-existing-cb" style="width:16px;height:16px;accent-color:#3b82f6;">
            <label for="remove-existing-cb" style="font-size:13px;color:#374151;">Remove selected items from existing category</label>
        </div>
        <div style="display:flex;justify-content:flex-end;gap:10px;padding:14px 26px;border-top:1px solid #f3f4f6;">
            <button onclick="closeMoveModal()" style="background:#f3f4f6;border:none;border-radius:7px;padding:10px 24px;font-size:13px;font-weight:600;cursor:pointer;">Cancel</button>
            <button onclick="confirmMove()" style="background:#ef4444;color:#fff;border:none;border-radius:7px;padding:10px 24px;font-size:13px;font-weight:700;cursor:pointer;">Move to this category</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]')?.content;
let cats = @json($categories ?? []);
let selIdx = 0, delId = null;

document.addEventListener('DOMContentLoaded', () => {
    renderList(cats);
    selectCat(0);
    document.addEventListener('click', () => document.querySelectorAll('.cat-row-dd.open').forEach(d => d.classList.remove('open')));
});

function toast(m) {
    const t = document.getElementById('cat-toast');
    t.textContent = m; t.classList.add('show');
    setTimeout(() => t.classList.remove('show'), 2500);
}

function toggleSearch() {
    const w = document.getElementById('sw');
    w.classList.toggle('open');
    if (w.classList.contains('open')) document.getElementById('sq').focus();
}
function filterList() {
    const q = document.getElementById('sq').value.toLowerCase();
    renderList(cats.filter(c => c.name.toLowerCase().includes(q)));
}

function renderList(list) {
    const rows = [{ id: null, name: 'Items not in any Category', items_count: 0 }, ...list];
    document.getElementById('cat-list').innerHTML = rows.map((c, i) => `
        <div class="cat-row ${selIdx===i?'active':''}" onclick="selectCat(${i})">
            <span class="cat-row-name">${esc(c.name)}</span>
            <span class="cat-row-count">${c.items_count??0}</span>
            <div class="cat-row-more" onclick="event.stopPropagation()">
                <button class="cat-row-more-btn" onclick="toggleDD(event,${i})">⋮</button>
                <div class="cat-row-dd" id="dd-${i}">
                    ${c.id ? `<div class="cat-dd-item" onclick="openEdit(${i})">Edit</div>
                               <div class="cat-dd-item danger" onclick="openDel(${c.id})">Delete</div>`
                            : `<div class="cat-dd-item" style="color:#9ca3af;pointer-events:none;">No actions</div>`}
                </div>
            </div>
        </div>`).join('');
}

function toggleDD(e, i) {
    e.stopPropagation();
    document.querySelectorAll('.cat-row-dd.open').forEach(d => d.classList.remove('open'));
    document.getElementById(`dd-${i}`).classList.toggle('open');
}

function selectCat(i) {
    selIdx = i;
    const rows = [{ id: null, name: 'Items not in any Category', items_count: 0 }, ...cats];
    const c = rows[i];
    document.querySelectorAll('.cat-row').forEach((r, ri) => r.classList.toggle('active', ri === i));
    document.getElementById('r-title').textContent = c.id ? c.name.toUpperCase() : 'ITEMS NOT IN ANY CATEGORY';
    document.getElementById('r-count').textContent = c.items_count ?? 0;
    loadItems(c.id);
}

function loadItems(catId) {
    const tbody = document.getElementById('items-tbody');
    tbody.innerHTML = '<tr><td colspan="3" class="cat-norows">Loading...</td></tr>';
    const url = catId ? `/items?category_id=${catId}&json=1` : `/items?uncategorized=1&json=1`;
    fetch(url, { headers: { 'Accept': 'application/json' } })
        .then(r => r.json())
        .then(data => {
            const items = Array.isArray(data) ? data : (data.items ?? []);
            if (!items.length) { tbody.innerHTML = '<tr><td colspan="3" class="cat-norows">No Rows To Show</td></tr>'; return; }
            tbody.innerHTML = items.map(it => `<tr>
                <td>${esc(it.name)}</td>
                <td>${it.opening_qty??0}</td>
                <td>Rs ${(parseFloat(it.purchase_price||0)*parseFloat(it.opening_qty||0)).toFixed(2)}</td>
            </tr>`).join('');
        })
        .catch(() => { tbody.innerHTML = '<tr><td colspan="3" class="cat-norows">No Rows To Show</td></tr>'; });
}

function filterItems(q) {
    document.querySelectorAll('#items-tbody tr').forEach(r => {
        r.style.display = r.textContent.toLowerCase().includes(q.toLowerCase()) ? '' : 'none';
    });
}

/* ADD */
function openAdd() { document.getElementById('add-input').value=''; document.getElementById('add-overlay').classList.add('open'); setTimeout(()=>document.getElementById('add-input').focus(),80); }
function closeAdd() { document.getElementById('add-overlay').classList.remove('open'); }
function saveAdd() {
    const name = document.getElementById('add-input').value.trim();
    if (!name) { toast('Please enter a category name.'); return; }
    fetch('/items/category', {
        method:'POST',
        headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},
        body:JSON.stringify({name})
    }).then(r=>r.json()).then(d=>{
        const cat = d.category || d;
        if (cat?.id) { cat.items_count=0; cats.push(cat); renderList(cats); closeAdd(); toast('Category created!'); }
        else toast(d.message||'Failed to create.');
    }).catch(()=>toast('Network error.'));
}

/* EDIT */
function openEdit(i) {
    const rows=[{id:null,name:''},...cats]; const c=rows[i];
    if (!c?.id) return;
    document.getElementById('edit-id').value=c.id;
    document.getElementById('edit-input').value=c.name;
    document.querySelectorAll('.cat-row-dd.open').forEach(d=>d.classList.remove('open'));
    document.getElementById('edit-overlay').classList.add('open');
    setTimeout(()=>document.getElementById('edit-input').focus(),80);
}
function closeEdit() { document.getElementById('edit-overlay').classList.remove('open'); }
function saveEdit() {
    const id=document.getElementById('edit-id').value;
    const name=document.getElementById('edit-input').value.trim();
    if (!name) { toast('Please enter a name.'); return; }
    fetch(`/items/category/${id}`,{
        method:'PUT',
        headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':CSRF},
        body:JSON.stringify({name})
    }).then(r=>r.json()).then(d=>{
        if (d.success||d.id||d.category) {
            const i=cats.findIndex(c=>c.id==id);
            if (i!==-1) cats[i].name=name;
            renderList(cats); closeEdit(); toast('Category updated!');
        } else toast(d.message||'Failed to update.');
    }).catch(()=>toast('Network error.'));
}

/* DELETE */
function openDel(id) { delId=id; document.querySelectorAll('.cat-row-dd.open').forEach(d=>d.classList.remove('open')); document.getElementById('cdel-overlay').classList.add('open'); }
function closeDel() { document.getElementById('cdel-overlay').classList.remove('open'); delId=null; }
function confirmDel() {
    if (!delId) return;
    fetch(`/items/category/${delId}`, {
        method: 'DELETE',
        headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(d => {
        if (d.success) {
            cats = cats.filter(c => c.id != delId);
            selIdx = 0; renderList(cats); selectCat(0);
            toast('Category deleted.');
        } else {
            toast(d.message || 'Failed to delete.');
        }
        closeDel();
    })
    .catch(() => { toast('Network error.'); closeDel(); });
}

function openMoveModal() {
    fetch('/items?json=1', { headers: { 'Accept': 'application/json' } })
    .then(r => r.json())
    .then(data => {
        const items = Array.isArray(data) ? data : (data.items ?? []);
        const tbody = document.getElementById('move-items-tbody');
        tbody.innerHTML = !items.length
            ? '<tr><td colspan="3" style="text-align:center;padding:30px;color:#9ca3af;">No items available</td></tr>'
            : items.map(it => `<tr>
                <td style="width:40px;padding:10px 16px;"><input type="checkbox" value="${it.id}" style="width:16px;height:16px;accent-color:#3b82f6;"></td>
                <td style="font-size:14px;color:#111827;padding:10px 16px;">${esc(it.name)}</td>
                <td style="width:80px;text-align:right;font-size:14px;color:#10b981;padding:10px 16px;">${it.opening_qty??0}</td>
            </tr>`).join('');
        document.getElementById('move-overlay').classList.add('open');
    })
    .catch(() => toast('Failed to load items.'));
}
function closeMoveModal() { document.getElementById('move-overlay').classList.remove('open'); }
function confirmMove() {
    const rows = [{ id: null }, ...cats];
    const targetCat = rows[selIdx];
    if (!targetCat?.id) { toast('Please select a category first.'); return; }
    const checked = [...document.querySelectorAll('#move-items-tbody input[type=checkbox]:checked')].map(c => c.value);
    if (!checked.length) { toast('Please select at least one item.'); return; }
    Promise.all(checked.map(itemId =>
        fetch(`/items/${itemId}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
            body: JSON.stringify({ category_id: targetCat.id })
        })
    )).then(() => {
        closeMoveModal(); toast('Items moved successfully!'); selectCat(selIdx);
        const i = cats.findIndex(c => c.id == targetCat.id);
        if (i !== -1) { cats[i].items_count = (cats[i].items_count ?? 0) + checked.length; renderList(cats); }
    }).catch(() => toast('Failed to move some items.'));
}

function esc(s){return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');}
</script>
@endpush