@extends('layouts.app')

@section('title', 'Vyapar — Items')
@section('description', 'Manage your product and service items, pricing, and stock in Vyapar accounting software.')
@section('page', 'items')

@section('content')

  <div class="split-pane">
    <!-- Left: Item List -->
    <div class="split-left">
      <div class="list-panel-header">
        <input type="text" class="form-control search-input" placeholder="Search items..." id="itemSearchInput">
        <button class="btn-add-entity" data-bs-toggle="modal" data-bs-target="#addItemModal">
          <i class="fa-solid fa-plus me-1"></i> Add Item
        </button>
      </div>
      <ul class="entity-list" id="itemList">
        <li class="active" data-item="rice-5kg">
          <span class="entity-name">Rice (5kg)</span>
          <span class="entity-balance positive">₹ 250.00</span>
        </li>
        <li data-item="sugar-1kg">
          <span class="entity-name">Sugar (1kg)</span>
          <span class="entity-balance positive">₹ 45.00</span>
        </li>
        <li data-item="cooking-oil">
          <span class="entity-name">Cooking Oil (1L)</span>
          <span class="entity-balance positive">₹ 180.00</span>
        </li>
      </ul>
    </div>
    <!-- Right: Item Details -->
    <div class="split-right">
      <div class="detail-panel-header">
        <div>
          <div class="entity-detail-name" id="itemDetailName">Rice (5kg)</div>
          <div class="entity-detail-meta"><i class="fa-solid fa-tags me-1"></i> Sale Price: ₹ 250.00</div>
        </div>
        <div class="action-buttons">
          <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen me-1"></i> Edit</button>
          <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-ellipsis-vertical"></i></button>
        </div>
      </div>
      <div class="detail-panel-body">
        <div class="empty-state">
          <i class="fa-regular fa-folder-open d-block"></i>
          <h5>No Transactions Yet</h5>
          <p>Transactions for this item will appear here once you create an invoice.</p>
        </div>
      </div>
    </div>
  </div>

@endsection

@section('modals')
<!-- MODAL: ADD ITEM -->
<div class="modal fade" id="addItemModal" tabindex="-1" aria-labelledby="addItemModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addItemModalLabel"><i class="fa-solid fa-box-open me-2"></i>Add Item</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Product / Service Toggle -->
        <div class="d-flex align-items-center gap-3 mb-4">
          <span class="fw-600" style="font-size:13px;">Type:</span>
          <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="itemTypeSwitch">
            <label class="form-check-label fw-600" for="itemTypeSwitch" id="itemTypeLabel">Product</label>
          </div>
        </div>
        <!-- Main Inputs -->
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-600">Item Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" placeholder="Enter item name" id="itemNameInput">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-600">Category</label>
            <select class="form-select" id="itemCategorySelect">
              <option selected disabled>Select</option>
              <option>General</option>
              <option>Groceries</option>
              <option>Electronics</option>
              <option>Clothing</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-600">Item Code</label>
            <input type="text" class="form-control" placeholder="SKU / HSN">
          </div>
        </div>
        <div class="row g-0">
          <div class="col-md-8">
            <!-- Tabs -->
            <ul class="nav nav-tabs" id="itemModalTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="item-pricing-tab" data-bs-toggle="tab"
                  data-bs-target="#itemPricingPane" type="button" role="tab">
                  <i class="fa-solid fa-tags me-1"></i> Pricing
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="item-stock-tab" data-bs-toggle="tab" data-bs-target="#itemStockPane"
                  type="button" role="tab">
                  <i class="fa-solid fa-warehouse me-1"></i> Stock
                </button>
              </li>
            </ul>
            <div class="tab-content pt-3" id="itemModalTabContent">
              <!-- Pricing Tab -->
              <div class="tab-pane fade show active" id="itemPricingPane" role="tabpanel">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Sale Price</label>
                    <div class="input-group">
                      <span class="input-group-text">₹</span>
                      <input type="number" class="form-control" placeholder="0.00">
                    </div>
                  </div>
                  <div class="col-md-6">
                    <label class="form-label">Purchase Price</label>
                    <div class="input-group">
                      <span class="input-group-text">₹</span>
                      <input type="number" class="form-control" placeholder="0.00">
                    </div>
                  </div>
                  <div class="col-12">
                    <a href="#" class="text-primary-blue" style="font-size:13px;">
                      <i class="fa-solid fa-plus me-1"></i> Add Wholesale Price
                    </a>
                  </div>
                </div>
              </div>
              <!-- Stock Tab -->
              <div class="tab-pane fade" id="itemStockPane" role="tabpanel">
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label">Opening Stock</label>
                    <input type="number" class="form-control" placeholder="0">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">As Of Date</label>
                    <input type="date" class="form-control" value="2026-03-10">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label">Min Stock Level</label>
                    <input type="number" class="form-control" placeholder="0">
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- Image Placeholder -->
          <div class="col-md-4 d-flex align-items-start justify-content-center pt-4">
            <div class="text-center"
              style="border:2px dashed var(--border-color);border-radius:10px;padding:30px 20px;width:100%;cursor:pointer;">
              <i class="fa-regular fa-image d-block" style="font-size:36px;color:#ccc;margin-bottom:10px;"></i>
              <p class="mb-0" style="font-size:12px;color:var(--text-muted);">Add Item Image</p>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-primary" id="btnSaveNewItem">
          <i class="fa-solid fa-plus me-1"></i> Save & New
        </button>
        <button type="button" class="btn btn-primary" id="btnSaveItem">
          <i class="fa-solid fa-check me-1"></i> Save
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script src="{{ asset('js/items.js') }}"></script>
@endpush
