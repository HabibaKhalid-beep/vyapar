@php
    $modalCategories = $categories ?? \App\Models\Category::orderBy('name')->get();
    $modalUnits = [];

    if (\Illuminate\Support\Facades\Schema::hasTable('item_units')) {
        $modalUnits = \App\Models\ItemUnit::query()
            ->where('is_active', true)
            ->orderBy('short_name')
            ->get(['name', 'short_name']);
    }

@endphp

<div class="modal fade" id="addItemModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header align-items-start justify-content-between">
        <div>
          <h5 class="modal-title">Add Item</h5>
          <p class="text-muted small mb-0">Create item details, pricing, stock and description.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span id="newItemProductLabel" class="text-primary fw-semibold">Product</span>
          <div class="form-check form-switch mb-0">
            <input class="form-check-input" type="checkbox" id="newItemTypeToggle">
            <label class="form-check-label" for="newItemTypeToggle">Service</label>
          </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addItemForm">
          <input type="hidden" id="newItemType" name="item_type" value="product">
          <div class="row g-3">
            <div class="col-md-5">
              <label for="newItemName" class="form-label" id="newItemNameLabel">Item Name *</label>
              <input type="text" class="form-control" id="newItemName" required>
            </div>
            <div class="col-md-4">
              <label for="newItemCategory" class="form-label">Category</label>
              <select class="form-select" id="newItemCategory">
                <option value="">Select Category</option>
                @foreach($modalCategories as $category)
                  <option value="{{ $category->id }}">{{ $category->name }}</option>
                @endforeach
                <option value="__add_new__">+ Add Category</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">Unit</label>
              <div class="w-100">
                <button class="btn btn-outline-primary w-100 text-start" type="button" id="newItemUnitBtn">
                  Select Unit
                </button>
                <input type="hidden" id="newItemUnit" name="unit">
                <input type="hidden" id="newItemSecondaryUnit" name="secondary_unit">
                <input type="hidden" id="newItemUnitConversionRate" name="unit_conversion_rate">
              </div>
            </div>
            <div class="col-md-6">
              <label for="newItemCode" class="form-label">Item Code</label>
              <div class="input-group">
                <input type="text" class="form-control" id="newItemCode" placeholder="Enter item code">
                <button type="button" class="btn btn-outline-secondary" id="assignItemCodeBtn">Assign</button>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">Item Image</label>
              <div class="border rounded-3 p-3 text-center h-100 d-flex flex-column justify-content-center align-items-center open-item-image-picker" style="cursor:pointer;">
                <div id="newItemImageThumb" style="width:68px; height:68px; border:1.5px solid #93c5fd; border-radius:12px; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, #dbeafe 0%, #e0f2fe 100%); overflow:hidden;">
                  <i class="fa-regular fa-image fa-2x text-secondary"></i>
                </div>
                <div class="text-secondary mt-2" id="newItemImageLabel">Click to choose image</div>
                <input type="file" class="form-control d-none" id="newItemImage" accept="image/*">
              </div>
            </div>
          </div>

          <ul class="nav nav-tabs mt-4" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="pricing-tab" data-bs-toggle="tab" data-bs-target="#pricing-tab-pane" type="button" role="tab" aria-selected="true">Pricing</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="stock-tab" data-bs-toggle="tab" data-bs-target="#stock-tab-pane" type="button" role="tab" aria-selected="false">Stock</button>
            </li>
          </ul>

          <div class="tab-content pt-3">
            <div class="tab-pane fade show active" id="pricing-tab-pane" role="tabpanel">
              <div class="row g-3">
                <div class="col-md-6">
                  <label for="newItemSalePrice" class="form-label">Sale Price</label>
                  <input type="number" class="form-control" id="newItemSalePrice" min="0" step="0.01" placeholder="Sale Price">
                </div>
                <div class="col-md-6" id="purchase-sec">
                  <label for="newItemPurchasePrice" class="form-label">Purchase Price</label>
                  <input type="number" class="form-control" id="newItemPurchasePrice" min="0" step="0.01" placeholder="Purchase Price">
                </div>
                <div class="col-12">
                  <div class="border rounded-3 p-3 bg-light">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                      <div class="fw-semibold">Wholesale Pricing</div>
                      <button type="button" class="btn btn-link btn-sm p-0" id="toggleWholesalePricing">+ Add Wholesale Price</button>
                    </div>
                    <div class="row g-2 wholesale-pricing d-none">
                      <div class="col-md-6">
                        <label for="newItemWholesalePrice" class="form-label">Wholesale Price</label>
                        <input type="number" class="form-control" id="newItemWholesalePrice" min="0" step="0.01" placeholder="Wholesale Price">
                      </div>
                      <div class="col-md-6">
                        <label for="newItemWholesaleMinQty" class="form-label">Minimum Wholesale Qty</label>
                        <input type="number" class="form-control" id="newItemWholesaleMinQty" min="0" step="1" placeholder="Minimum Qty">
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="tab-pane fade" id="stock-tab-pane" role="tabpanel">
              <div class="row g-3">
                <div class="col-md-4">
                  <label for="newItemStock" class="form-label">Opening Quantity</label>
                  <input type="number" class="form-control" id="newItemStock" min="0" step="1" placeholder="Opening Qty">
                </div>
                <div class="col-md-4">
                  <label for="newItemAtPrice" class="form-label">At Price</label>
                  <input type="number" class="form-control" id="newItemAtPrice" min="0" step="0.01" placeholder="At Price">
                </div>
                <div class="col-md-4">
                  <label for="newItemAsOfDate" class="form-label">As Of Date</label>
                  <input type="date" class="form-control" id="newItemAsOfDate" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-6">
                  <label for="newItemMinStock" class="form-label">Min Stock To Maintain</label>
                  <input type="number" class="form-control" id="newItemMinStock" min="0" step="1" placeholder="Min Stock">
                </div>
                <div class="col-md-6">
                  <label for="newItemLocation" class="form-label">Location</label>
                  <input type="text" class="form-control" id="newItemLocation" placeholder="Location">
                </div>
                <div class="col-12">
                  <label class="form-label fw-semibold">Item Images</label>
                  <div class="item-stock-images-trigger open-item-stock-images-picker">
                    <span><i class="fa-regular fa-camera me-2"></i>Add Item Images</span>
                  </div>
                  <input type="file" class="d-none" id="newItemStockImages" accept="image/*" multiple>
                  <div id="newItemStockImagesList" class="item-stock-images-list"></div>
                </div>
              </div>
            </div>
          </div>

          <div class="row g-3 mt-4">
            <div class="col-12">
              <label for="newItemDescription" class="form-label">Description</label>
              <textarea class="form-control" id="newItemDescription" rows="4" placeholder="Item description"></textarea>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveNewItemBtn">Save Item</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-stack-top" id="selectItemUnitModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Select Unit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="newItemBaseUnitSelect" class="form-label text-uppercase small fw-bold">Base Unit</label>
            <select class="form-select" id="newItemBaseUnitSelect">
              <option value="">Select Base Unit</option>
              @foreach($modalUnits as $unit)
                @php
                  $unitShortName = strtoupper($unit['short_name'] ?? $unit->short_name ?? '');
                  $unitName = strtoupper($unit['name'] ?? $unit->name ?? '');
                  $unitLabel = $unitName && $unitName !== $unitShortName ? $unitName . ' (' . $unitShortName . ')' : $unitShortName;
                @endphp
                <option value="{{ $unitShortName }}">{{ $unitLabel }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label for="newItemSecondaryUnitSelect" class="form-label text-uppercase small fw-bold">Secondary Unit</label>
            <select class="form-select" id="newItemSecondaryUnitSelect">
              <option value="">Select Secondary Unit</option>
              @foreach($modalUnits as $unit)
                @php
                  $unitShortName = strtoupper($unit['short_name'] ?? $unit->short_name ?? '');
                  $unitName = strtoupper($unit['name'] ?? $unit->name ?? '');
                  $unitLabel = $unitName && $unitName !== $unitShortName ? $unitName . ' (' . $unitShortName . ')' : $unitShortName;
                @endphp
                <option value="{{ $unitShortName }}">{{ $unitLabel }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-link text-primary p-0 open-add-unit-from-selector">+ Add Unit</button>
          </div>
          <div class="col-12">
            <label for="newItemUnitConversionInput" class="form-label fw-semibold">Conversion Rate</label>
            <div class="item-unit-conversion-row">
              <span class="base-unit-preview">1 Base Unit</span>
              <span>=</span>
              <input type="number" class="form-control" id="newItemUnitConversionInput" min="0" step="0.0001" value="0">
              <span class="secondary-unit-preview">Secondary Unit</span>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="saveSelectedUnitsBtn">Save</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-stack-top" id="addCategoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Category</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="mb-3">
          <label for="quickCategoryName" class="form-label">Category Name</label>
          <input type="text" class="form-control" id="quickCategoryName" placeholder="Enter category name">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveQuickCategoryBtn">Save Category</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade modal-stack-top" id="addUnitModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Unit</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="row g-3">
          <div class="col-md-8">
            <label for="quickUnitName" class="form-label">Unit Name</label>
            <input type="text" class="form-control" id="quickUnitName" placeholder="e.g. KILOGRAMS">
          </div>
          <div class="col-md-4">
            <label for="quickUnitShortName" class="form-label">Short Name</label>
            <input type="text" class="form-control" id="quickUnitShortName" placeholder="e.g. KG">
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveQuickUnitBtn">Save Unit</button>
      </div>
    </div>
  </div>
</div>
