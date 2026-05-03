<style>
.item-picker { position: relative; min-width: 260px; flex: 1; overflow: visible; }
.item-picker-input { width: 100%; border: 1px solid #d6dce5; border-radius: 8px; padding: 10px 14px; font-size: 14px; background: #fff; transition: border-color 0.2s ease, box-shadow 0.2s ease; min-width: 240px; }
.item-picker-input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
.item-picker-panel { position: fixed; top: calc(100% + 4px); left: 0; width: 100%; min-width: 320px; background: white; border: 1px solid #d9e1ec; border-radius: 12px; box-shadow: 0 18px 45px rgba(15, 23, 42, 0.12); z-index: 1055; display: none; overflow: hidden; }
.item-picker-panel.open { display: block !important; }
.item-picker-list { max-height: 320px; overflow-y: auto; }
.item-picker-add { display: flex; align-items: center; gap: 10px; padding: 14px 20px; color: #2563eb; font-size: 18px; font-weight: 600; cursor: pointer; transition: background-color 0.2s ease; border-bottom: 1px solid #e8edf5; }
.item-picker-add i { font-size: 22px; line-height: 1; }
.item-picker-add:hover, .item-picker-row:hover { background: #f8fbff; }
.item-picker-head, .item-picker-row { display: grid; grid-template-columns: minmax(0, 2fr) 100px 110px 80px; gap: 12px; align-items: center; }
.item-picker-head { padding: 14px 20px; font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; background: #f8fbff; border-bottom: 1px solid #e5edf7; }
.item-picker-row { padding: 16px 20px; cursor: pointer; border-top: 1px solid #eef3f8; font-size: 16px; color: #334155; }
.item-picker-name small { color: #8a94a6; margin-left: 6px; }
.item-picker-stock.neg { color: #dc3545; }
.item-picker-empty { padding: 18px 20px; color: #8a94a6; font-size: 15px; }
.dropdown-header-search { position: sticky; top: 0; z-index: 2; background: #fff; }
.unit-menu-scroll { max-height: 260px; overflow-y: auto; }
.unit-menu-divider { margin: 0; }
.unit-add-action { position: sticky; bottom: 0; background: #fff; border-top: 1px solid #e8edf5; }
.unit-add-action .dropdown-item { padding: 12px 16px; font-weight: 600; color: #2563eb; }
.item-stock-images-trigger { display:flex; align-items:center; justify-content:flex-start; gap:10px; padding:12px 16px; border:1px solid #dbe3ef; border-radius:10px; color:#52637a; cursor:pointer; background:#fff; max-width:220px; }
.item-stock-images-trigger:hover { border-color:#2563eb; color:#2563eb; }
.item-stock-images-list { display:flex; flex-wrap:wrap; gap:12px; margin-top:12px; }
.item-stock-image-card { width:92px; }
.item-stock-image-card img { width:92px; height:92px; object-fit:cover; border-radius:10px; border:1px solid #dbe3ef; display:block; }
.item-stock-image-card .name { font-size:12px; color:#64748b; margin-top:6px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.item-unit-conversion-row { display:grid; grid-template-columns:auto auto minmax(120px, 160px) auto; gap:12px; align-items:center; }
.base-unit-preview, .secondary-unit-preview { font-weight:600; color:#475569; }
</style>
