<style>
.item-picker {
    position: relative;
    min-width: 260px;
    flex: 1;
    overflow: visible;
}

.item-picker-input {
    width: 100%;
    border: 1px solid #cfd8e3;
    border-radius: 6px;
    padding: 10px 14px;
    font-size: 14px;
    background: #fff;
    transition: border-color 0.2s ease, box-shadow 0.2s ease;
}

.item-picker-input:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.item-picker-panel {
    position: fixed;
    top: calc(100% + 4px);
    left: 0;
    width: 100%;
    min-width: 320px;
    background: white;
    border: 1px solid #e1e8ed;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1055;
    display: none;
    overflow: hidden;
    max-width: none;
}

.item-picker-panel.open {
    display: block !important;
}

.item-picker-list {
    max-height: 320px;
    overflow-y: auto;
}

.item-picker-list::-webkit-scrollbar {
    width: 8px;
}

.item-picker-list::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.item-picker-list::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.item-picker-list::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.item-picker-add {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 12px 18px;
    color: #2563eb;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.item-picker-add:hover {
    background: #f8fbff;
}

.item-picker-head,
.item-picker-row {
    display: grid;
    grid-template-columns: minmax(0, 2fr) 100px 110px 80px;
    gap: 12px;
    align-items: center;
}

.item-picker-row > div {
    min-width: 0;
}

.item-picker-name {
    display: block;
    min-width: 0;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

@media (max-width: 768px) {
    .item-picker-head,
    .item-picker-row {
        grid-template-columns: minmax(0, 2fr) 80px 90px 70px;
        gap: 8px;
    }

    .item-picker-panel {
        max-width: 400px;
    }
}

@media (max-width: 576px) {
    .item-picker {
        min-width: 200px;
    }

    .item-picker-head,
    .item-picker-row {
        grid-template-columns: 1fr;
        gap: 4px;
    }

    .item-picker-head span:nth-child(2),
    .item-picker-head span:nth-child(3),
    .item-picker-head span:nth-child(4),
    .item-picker-row > div:nth-child(2),
    .item-picker-row > div:nth-child(3),
    .item-picker-row > div:nth-child(4) {
        display: none;
    }

    .item-picker-panel {
        max-width: 300px;
    }
}

.item-picker-head {
    padding: 10px 18px;
    font-size: 12px;
    font-weight: 700;
    color: #97a3b6;
    text-transform: uppercase;
}

.item-picker-row {
    padding: 12px 18px;
    cursor: pointer;
    border-top: 1px solid #f4f7fb;
}

.item-picker-row:hover {
    background: #f8fbff;
}

.item-picker-name small {
    color: #8a94a6;
    margin-left: 6px;
}

.item-picker-stock.zero {
    color: #dc3545;
    font-weight: 600;
}

.item-picker-stock.pos {
    color: #16a34a;
    font-weight: 600;
}

.item-picker-empty {
    padding: 14px 18px;
    color: #8a94a6;
}
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

@media (max-width: 768px) {
  .item-picker-head, .item-picker-row { grid-template-columns: minmax(0, 2fr) 80px 90px 70px; gap: 8px; }
  .item-picker-panel { max-width: 400px; }
}

@media (max-width: 576px) {
  .item-picker { min-width: 200px; }
  .item-picker-panel { min-width: min(320px, calc(100vw - 16px)); width: min(320px, calc(100vw - 16px)); }
  .item-picker-head, .item-picker-row { grid-template-columns: 1fr; gap: 4px; }
  .item-picker-head span:nth-child(2),
  .item-picker-head span:nth-child(3),
  .item-picker-head span:nth-child(4),
  .item-picker-row > div:nth-child(2),
  .item-picker-row > div:nth-child(3),
  .item-picker-row > div:nth-child(4) { display: none; }
  .item-picker-panel { max-width: 300px; }
}
</style>
