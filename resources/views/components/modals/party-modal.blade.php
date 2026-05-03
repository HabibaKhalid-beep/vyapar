@php
    $partyGroups = $partyGroups ?? \App\Models\PartyGroup::orderBy('name')->get();
@endphp

<div class="modal fade" id="addPartyModal" tabindex="-1" aria-labelledby="addPartyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPartyModalLabel"><i class="fa-solid fa-user-plus me-2"></i>Add Party</h5>
        <div class="d-flex align-items-center gap-2" style="margin-left:auto;">
          <button class="btn btn-sm btn-outline-secondary" type="button" id="partyModalSettingsTrigger" title="Settings">
            <i class="fa-solid fa-gear"></i>
          </button>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>

      <div class="modal-body">
        <form id="addPartyForm">
          @csrf
          <div class="row g-3 mb-4">
            <div class="col-md-4" data-party-setting="name">
              <label class="form-label fw-600">Party Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" placeholder="Enter party name" id="partyNameInput" required>
            </div>
            <div class="col-md-4" data-party-setting="phone">
              <label class="form-label fw-600">Phone Number</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                <input type="tel" name="phone" class="form-control" placeholder="Enter phone number" id="partyPhoneInput">
              </div>
            </div>
            <div class="col-md-4" data-party-setting="phone_2">
              <label class="form-label fw-600">Phone Number 2</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-phone-volume"></i></span>
                <input type="tel" name="phone_number_2" class="form-control" placeholder="Enter second phone number" id="partyPhone2Input">
              </div>
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">PTCL Number</label>
              <input type="text" name="ptcl_number" class="form-control" placeholder="Enter PTCL number" id="partyPtclInput">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">City</label>
              <input type="text" name="city" class="form-control" placeholder="Enter city" id="partyCityInput">
            </div>
            <div class="col-md-4">
              <label class="form-label fw-600">Party Group</label>
              <div class="position-relative">
                <button type="button" class="form-control text-start" id="partyGroupTrigger">
                  <span id="partyGroupText">Select group</span>
                  <i class="fa fa-chevron-down float-end mt-1"></i>
                </button>
                <input type="hidden" name="party_group" id="partyGroupInput">
                <div id="partyGroupMenu" class="border bg-white position-absolute w-100 mt-1 d-none" style="z-index:999;">
                  <button type="button" class="dropdown-item text-primary" id="addNewGroupBtn">+ New Group</button>
                  <div id="partyGroupList">
                    @foreach($partyGroups as $partyGroup)
                      <button type="button" class="dropdown-item" data-group="{{ $partyGroup->name }}">{{ $partyGroup->name }}</button>
                    @endforeach
                  </div>
                </div>
              </div>
            </div>
          </div>

          <ul class="nav nav-tabs" id="partyModalTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="party-address-tab" data-bs-toggle="tab" data-bs-target="#partyAddressPane" type="button" role="tab" aria-controls="partyAddressPane" aria-selected="true">
                <i class="fa-solid fa-location-dot me-1"></i> Address
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="party-credit-tab" data-bs-toggle="tab" data-bs-target="#partyCreditPane" type="button" role="tab" aria-controls="partyCreditPane" aria-selected="false">
                <i class="fa-solid fa-credit-card me-1"></i> Credit & Balance
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="party-additional-tab" data-bs-toggle="tab" data-bs-target="#partyAdditionalPane" type="button" role="tab" aria-controls="partyAdditionalPane" aria-selected="false">
                <i class="fa-solid fa-sliders me-1"></i> Additional Fields
              </button>
            </li>
          </ul>

          <div class="tab-content pt-3" id="partyModalTabContent">
            <div class="tab-pane fade show active" id="partyAddressPane" role="tabpanel" aria-labelledby="party-address-tab">
              <div class="row g-3">
                <div class="col-md-6" data-party-setting="email">
                  <label class="form-label">Email ID</label>
                  <input type="email" name="email" class="form-control" placeholder="example@email.com">
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-6">
                  <label class="form-label">Address</label>
                  <textarea id="partyAddressInput" class="form-control" name="address" rows="3" placeholder="Enter address"></textarea>
                </div>
                <div class="col-md-6" data-party-setting="billing_address">
                  <label class="form-label">Billing Address</label>
                  <textarea id="billingAddress" class="form-control" name="billing_address" rows="3" placeholder="Enter billing address"></textarea>
                </div>
                <div class="col-md-6" data-party-setting="shipping_address">
                  <label class="form-label">Shipping Address</label>
                  <textarea id="shippingAddress" class="form-control" name="shipping_address" rows="3" placeholder="Enter shipping address"></textarea>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="partyCreditPane" role="tabpanel" aria-labelledby="party-credit-tab">
              <div class="row g-3">
                <div class="col-md-4" data-party-setting="opening_balance">
                  <label class="form-label">Opening Balance</label>
                  <div class="input-group">
                    <span class="input-group-text">Rs</span>
                    <input type="number" name="opening_balance" class="form-control" placeholder="0.00">
                  </div>
                </div>
                <div class="col-md-4" data-party-setting="as_of_date">
                  <label class="form-label">As Of Date</label>
                  <input type="date" name="as_of_date" class="form-control" value="{{ date('Y-m-d') }}">
                </div>
                <div class="col-md-4" data-party-setting="credit_limit">
                  <label class="form-label d-block">Credit Limit</label>
                  <div class="form-check form-switch mt-2">
                    <input class="form-check-input" name="credit_limit_enabled" type="checkbox" id="creditLimitSwitch">
                    <label class="form-check-label" for="creditLimitSwitch">Enable</label>
                  </div>
                  <div class="input-group mt-2 is-hidden" id="creditLimitAmountWrap">
                    <span class="input-group-text">Rs</span>
                    <input type="number" name="credit_limit_amount" class="form-control" placeholder="Enter credit limit" id="creditLimitAmountInput" min="0" step="0.01">
                  </div>
                </div>
                <div class="col-md-4" data-party-setting="due_days">
                  <label class="form-label">Due Days</label>
                  <input type="number" name="due_days" class="form-control" placeholder="e.g. 5, 10, 30" min="1" max="100" id="partyDueDaysInput">
                </div>
              </div>

              <div class="mt-4" data-party-setting="transaction_type">
                <label class="form-label d-block">Transaction Type</label>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="toReceive" value="receive">
                  <label class="form-check-label" for="toReceive">To Receive</label>
                </div>
                <div class="form-check form-check-inline">
                  <input class="form-check-input" type="checkbox" id="toPay" value="pay">
                  <label class="form-check-label" for="toPay">To Pay</label>
                </div>
              </div>

              <div class="row g-3 mt-3" data-party-setting="party_type">
                <div class="col-md-6">
                  <label class="form-label fw-600">Party Type</label>
                  <div class="form-check">
                    <input class="form-check-input party-type-checkbox" type="checkbox" name="party_type[]" id="customerParty" value="customer">
                    <label class="form-check-label" for="customerParty">Customer</label>
                  </div>
                  <div class="form-check">
                    <input class="form-check-input party-type-checkbox" type="checkbox" name="party_type[]" id="supplierParty" value="supplier">
                    <label class="form-check-label" for="supplierParty">Supplier</label>
                  </div>
                </div>
              </div>
            </div>

            <div class="tab-pane fade" id="partyAdditionalPane" role="tabpanel" aria-labelledby="party-additional-tab" data-party-setting="additional_fields">
              <p class="text-muted mb-3" style="font-size:13px;">Add custom fields to track additional information.</p>
              <div class="row g-3">
                @for($i = 1; $i <= 4; $i++)
                  <div class="col-md-6">
                    <div class="form-check mb-2">
                      <input class="form-check-input" type="checkbox" id="customField{{ $i }}Check">
                      <label class="form-check-label" for="customField{{ $i }}Check">Custom Field {{ $i }}</label>
                    </div>
                    <input type="text" name="custom_fields[]" class="form-control form-control-sm" placeholder="Field name">
                  </div>
                @endfor
                <input type="hidden" id="transactionTypeValue" name="transaction_type">
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
            <button type="button" class="btn btn-primary" id="btnUpdateParty" style="display:none;">Update</button>
            <button type="button" class="btn btn-danger" id="btnDeleteParty" style="display:none;">Delete</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="partyGroupModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-sm modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h6 class="modal-title">New Party Group</h6>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="text" id="newGroupName" class="form-control" placeholder="Enter group name">
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal" type="button">Cancel</button>
        <button class="btn btn-primary btn-sm" id="saveGroupBtn" type="button">Save</button>
      </div>
    </div>
  </div>
</div>
