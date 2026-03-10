@extends('layouts.app')

@section('title', 'Vyapar — Parties')
@section('description', 'Manage your business parties, customers, and suppliers in Vyapar accounting software.')
@section('page', 'parties')

@section('content')

  <div class="split-pane">
    <!-- Left: Party List -->
    <div class="split-left">
      <div class="list-panel-header">
        <input type="text" class="form-control search-input" placeholder="Search parties..." id="partySearchInput">
        <button class="btn-add-entity" data-bs-toggle="modal" data-bs-target="#addPartyModal">
          <i class="fa-solid fa-plus me-1"></i> Add Party
        </button>
      </div>
      <ul class="entity-list" id="partyList">
        <li class="active" data-party="abc">
          <span class="entity-name">abc</span>
          <span class="entity-balance positive">₹ 500.00</span>
        </li>
        <li data-party="xyz-enterprises">
          <span class="entity-name">XYZ Enterprises</span>
          <span class="entity-balance negative">₹ -1,200.00</span>
        </li>
        <li data-party="raj-traders">
          <span class="entity-name">Raj Traders</span>
          <span class="entity-balance positive">₹ 3,450.00</span>
        </li>
        <li data-party="nisha-foods">
          <span class="entity-name">Nisha Foods</span>
          <span class="entity-balance positive">₹ 0.00</span>
        </li>
      </ul>
    </div>
    <!-- Right: Party Details -->
    <div class="split-right">
      <div class="detail-panel-header">
        <div>
          <div class="entity-detail-name" id="partyDetailName">abc</div>
          <div class="entity-detail-meta"><i class="fa-solid fa-phone me-1"></i> +91 98765 43210</div>
        </div>
        <div class="action-buttons">
          <button class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-pen me-1"></i> Edit</button>
          <button class="btn btn-sm btn-outline-secondary"><i class="fa-solid fa-ellipsis-vertical"></i></button>
        </div>
      </div>
      <div class="detail-panel-body">
        <h6 class="fw-600 mb-3">Transactions</h6>
        <table class="txn-table" id="partyTxnTable">
          <thead>
            <tr>
              <th>Type</th>
              <th>Number</th>
              <th>Date</th>
              <th>Total</th>
              <th>Balance</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="badge bg-primary bg-opacity-10 text-primary">Sale</span></td>
              <td>#001</td>
              <td>10/03/2026</td>
              <td>₹ 500.00</td>
              <td class="text-success-green fw-600">₹ 500.00</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

@endsection

@section('modals')
<!-- MODAL: ADD PARTY -->
<div class="modal fade" id="addPartyModal" tabindex="-1" aria-labelledby="addPartyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPartyModalLabel"><i class="fa-solid fa-user-plus me-2"></i>Add Party</h5>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-sm btn-outline-secondary" title="Settings"><i class="fa-solid fa-gear"></i></button>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body">
        <!-- Top Inputs -->
        <div class="row g-3 mb-4">
          <div class="col-md-6">
            <label class="form-label fw-600">Party Name <span class="text-danger">*</span></label>
            <input type="text" class="form-control" placeholder="Enter party name" id="partyNameInput">
          </div>
          <div class="col-md-6">
            <label class="form-label fw-600">Phone Number</label>
            <div class="input-group">
              <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
              <input type="tel" class="form-control" placeholder="Enter phone number" id="partyPhoneInput">
            </div>
          </div>
        </div>
        <!-- Tabs -->
        <ul class="nav nav-tabs" id="partyModalTabs" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="party-address-tab" data-bs-toggle="tab" data-bs-target="#partyAddressPane" type="button" role="tab">
              <i class="fa-solid fa-location-dot me-1"></i> Address
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="party-credit-tab" data-bs-toggle="tab" data-bs-target="#partyCreditPane" type="button" role="tab">
              <i class="fa-solid fa-credit-card me-1"></i> Credit & Balance
            </button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="party-additional-tab" data-bs-toggle="tab" data-bs-target="#partyAdditionalPane" type="button" role="tab">
              <i class="fa-solid fa-sliders me-1"></i> Additional Fields
            </button>
          </li>
        </ul>
        <div class="tab-content pt-3" id="partyModalTabContent">
          <!-- Address Tab -->
          <div class="tab-pane fade show active" id="partyAddressPane" role="tabpanel">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Email ID</label>
                <input type="email" class="form-control" placeholder="example@email.com">
              </div>
              <div class="col-md-6"></div>
              <div class="col-md-6">
                <label class="form-label">Billing Address</label>
                <textarea class="form-control" rows="3" placeholder="Enter billing address"></textarea>
              </div>
              <div class="col-md-6">
                <label class="form-label">Shipping Address</label>
                <textarea class="form-control" rows="3" placeholder="Enter shipping address"></textarea>
              </div>
            </div>
          </div>
          <!-- Credit & Balance Tab -->
          <div class="tab-pane fade" id="partyCreditPane" role="tabpanel">
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">Opening Balance</label>
                <div class="input-group">
                  <span class="input-group-text">₹</span>
                  <input type="number" class="form-control" placeholder="0.00">
                </div>
              </div>
              <div class="col-md-4">
                <label class="form-label">As Of Date</label>
                <input type="date" class="form-control" value="2026-03-10">
              </div>
              <div class="col-md-4">
                <label class="form-label d-block">Credit Limit</label>
                <div class="form-check form-switch mt-2">
                  <input class="form-check-input" type="checkbox" id="creditLimitSwitch">
                  <label class="form-check-label" for="creditLimitSwitch">Enable</label>
                </div>
              </div>
            </div>
          </div>
          <!-- Additional Fields Tab -->
          <div class="tab-pane fade" id="partyAdditionalPane" role="tabpanel">
            <p class="text-muted mb-3" style="font-size:13px;">Add custom fields to track additional information.</p>
            <div class="row g-3">
              <div class="col-md-6">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" id="customField1Check">
                  <label class="form-check-label" for="customField1Check">Custom Field 1</label>
                </div>
                <input type="text" class="form-control form-control-sm" placeholder="Field name">
              </div>
              <div class="col-md-6">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" id="customField2Check">
                  <label class="form-check-label" for="customField2Check">Custom Field 2</label>
                </div>
                <input type="text" class="form-control form-control-sm" placeholder="Field name">
              </div>
              <div class="col-md-6">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" id="customField3Check">
                  <label class="form-check-label" for="customField3Check">Custom Field 3</label>
                </div>
                <input type="text" class="form-control form-control-sm" placeholder="Field name">
              </div>
              <div class="col-md-6">
                <div class="form-check mb-2">
                  <input class="form-check-input" type="checkbox" id="customField4Check">
                  <label class="form-check-label" for="customField4Check">Custom Field 4</label>
                </div>
                <input type="text" class="form-control form-control-sm" placeholder="Field name">
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-primary" id="btnSaveNewParty">
          <i class="fa-solid fa-plus me-1"></i> Save & New
        </button>
        <button type="button" class="btn btn-primary" id="btnSaveParty">
          <i class="fa-solid fa-check me-1"></i> Save
        </button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
  <script src="{{ asset('js/parties.js') }}"></script>
@endpush
