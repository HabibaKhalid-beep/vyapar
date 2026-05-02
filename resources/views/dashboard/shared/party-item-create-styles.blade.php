<style>
.item-picker { position: relative; min-width: 260px; flex: 1; overflow: visible; }
.item-picker-input { width: 100%; border: 1px solid #cfd8e3; border-radius: 6px; padding: 10px 14px; font-size: 14px; background: #fff; transition: border-color 0.2s ease, box-shadow 0.2s ease; min-width: 240px; }
.item-picker-input:focus { outline: none; border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1); }
.item-picker-panel { position: fixed; top: calc(100% + 4px); left: 0; width: 100%; min-width: 320px; background: white; border: 1px solid #e1e8ed; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); z-index: 1055; display: none; overflow: hidden; }
.item-picker-panel.open { display: block !important; }
.item-picker-list { max-height: 320px; overflow-y: auto; }
.item-picker-add { display: flex; align-items: center; gap: 8px; padding: 12px 18px; color: #2563eb; font-weight: 600; cursor: pointer; transition: background-color 0.2s ease; border-bottom: 1px solid #e1e8ed; }
.item-picker-add:hover, .item-picker-row:hover { background: #f8fbff; }
.item-picker-head, .item-picker-row { display: grid; grid-template-columns: minmax(0, 2fr) 100px 110px 80px; gap: 12px; align-items: center; }
.item-picker-head { padding: 10px 18px; font-size: 12px; font-weight: 700; color: #97a3b6; text-transform: uppercase; background: #f8fbff; border-bottom: 1px solid #e1e8ed; }
.item-picker-row { padding: 12px 18px; cursor: pointer; border-top: 1px solid #f4f7fb; }
.item-picker-name small { color: #8a94a6; margin-left: 6px; }
.item-picker-stock.neg { color: #dc3545; }
.item-picker-empty { padding: 14px 18px; color: #8a94a6; font-size: 13px; }
.dropdown-header-search { position: sticky; top: 0; z-index: 2; background: #fff; }
.unit-menu-scroll { max-height: 260px; overflow-y: auto; }
</style>
