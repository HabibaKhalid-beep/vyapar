@extends('layouts.app')
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"> -->



@section('title', 'Vyapar — Parties')
@section('description', 'Manage your business parties, customers, and suppliers in Vyapar accounting software.')
@section('page', 'parties')
 <link rel="stylesheet" href="{{ asset('css/parties.css') }}">
@section('content')

<!-- uper panel -->
<div class="uper-panel">
  <div class="panel-main">

    <!-- Left: Header + Arrow -->
    <div class="text">
      <div class="header-dropdown">
        <h1>Parties</h1>
        <i class="fa fa-chevron-down arrow-icon" onclick="toggleHeaderDropdown(this)"></i>

      <div class="header-dropdown-menu">
  <label class="dropdown-item">
    Parties
    <i class="fa fa-check tick-icon"></i>
  </label>
</div>
      </div>
    </div>

    <!-- Right: Buttons -->
    <div class="action-buttons">
      <button class="btn-add-entity" data-bs-toggle="modal" data-bs-target="#addPartyModal">
        <i class="fa-solid fa-plus me-1"></i> Add Party
      </button>
      <button class="btn-add-entity btn-party-transfer" type="button" data-bs-toggle="modal" data-bs-target="#partyTransferModal">
        <i class="fa-solid fa-right-left me-1"></i> Party Transfer
      </button>

      <button class="btn-settings" id="partySettingsTrigger" title="Settings">
        <i class="fa-solid fa-gear"></i>
      </button>

      <button class="btn-ellipsis" id="partyMoreOptionsTrigger" title="More Options">
        <i class="fa-solid fa-ellipsis-vertical"></i>
      </button>
    </div>

  </div>
</div>

  <div class="split-pane">


    <!-- Left: Party List -->
    <div class="split-left">
      <div class="list-panel-header">
      <div class="search-box">
  <i class="fa fa-search"></i>
  <input type="text" class="form-control search-input" placeholder="Search Party Name" id="partySearchInput">
</div>

      </div>
      <ul class="entity-list" id="partyList">
        <li class="active" data-party="abc">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="filter-wrapper">
<div class="parent-arrows" onclick="this.classList.toggle('active')">
  <span class="entity-balance positive" style="color: gray !important;">Party Name</span>
  <div class="counter-arrows">
    <i class="fa fa-chevron-up increment"></i>
    <i class="fa fa-chevron-down decrement"></i>
  </div>
</div>


<i class="fa fa-filter filter-icon" onclick="toggleFilter()"></i>

<div class="filter-dropdown" id="filterDropdown">

<label><input type="checkbox"> All</label>
<label><input type="checkbox"> Active</label>
<label><input type="checkbox"> Inactive</label>
<label><input type="checkbox"> To Receive</label>
<label><input type="checkbox"> To Pay</label>

<div class="filter-actions">
<button class="clear-btn">Clear</button>
<button class="apply-btn">Apply</button>
</div>

</div>

</div>
          <!-- Vertical separator -->
    <div class="separator"></div>
 <div class="parent-arrows" onclick="this.classList.toggle('active')">
  <span class="entity-balance positive" style="color: gray !important;">Amount</span>
  <div class="counter-arrows">
    <i class="fa fa-chevron-up increment"></i>
    <i class="fa fa-chevron-down decrement"></i>
  </div>
</div>



</li>
   <ul id="partiesList">
  @foreach($parties as $party)
    <li class="party-item"
        data-id="{{ $party->id }}"
        data-name="{{ $party->name }}"
        data-phone="{{ $party->phone }}"
        data-phone-number-2="{{ $party->phone_number_2 }}"
        data-ptcl-number="{{ $party->ptcl_number }}"
        data-email="{{ $party->email }}"
        data-city="{{ $party->city }}"
        data-address="{{ $party->address }}"
        data-billing-address="{{ $party->billing_address }}"
        data-shipping-address="{{ $party->shipping_address }}"
        data-opening-balance="{{ $party->opening_balance }}"
        data-current-balance="{{ $party->current_balance }}"
        data-as-of-date="{{ $party->as_of_date }}"
        data-transaction-type="{{ $party->transaction_type }}"
        data-party-type="{{ $party->party_type }}"
        data-party-group="{{ $party->party_group }}"
        data-due-days="{{ $party->due_days }}"
        data-credit-limit-enabled="{{ $party->credit_limit_enabled }}"
        data-credit-limit-amount="{{ $party->credit_limit_amount }}"
        data-custom-fields="{{ json_encode($party->custom_fields ?? []) }}">
      <span class="entity-name">{{ $party->name }}</span>
      <span class="entity-balance {{ $party->current_balance < 0 ? 'negative' : 'positive' }}">
        Rs {{ number_format($party->current_balance, 2) }}
      </span>
    </li>
  @endforeach
</ul>
    </div>
    <!-- Right: Party Details -->
    <div class="split-right">
      <div class="detail-panel-header">
        <div>
          <div style="display: flex;">
          <div class="entity-detail-name" id="partyDetailName" style="font-weight: 400;">abc

          </div>
           <button class="btn-icon"  id="editPartyBtn" title="Edit">  <i class="fa-solid fa-pen"></i>

</button>
</div>

         <div class="entity-detail-meta-row">

  <div class="entity-detail-meta">
    <div class="meta-heading">Phone Number</div>
    <div class="meta-value"  id="partyPhone"> +91 98765 43210</div>
  </div>

  <div class="entity-detail-meta">
    <div class="meta-heading">Email</div>
    <div class="meta-value" id="partyEmail"> example@email.com</div>
  </div>

  <div class="entity-detail-meta">
    <div class="meta-heading">Billing Address</div>
    <div class="meta-value" id="partyAddress"> 123, Main Street, City</div>
  </div>

  <div class="entity-detail-meta">
    <div class="meta-heading">City / PTCL</div>
    <div class="meta-value" id="partyCityPtcl"> City - PTCL</div>
  </div>

</div>
        </div>
        <div class="action-buttons">

        </div>
      </div>


    <div class="detail-panel-body">
  <div class="table-header">
    <div style="display:flex;align-items:center;gap:12px;">
      <h6 class="fw-600 mb-3" style="font-size: 14px !important; margin-bottom:0 !important;">Transactions</h6>
            <button type="button" class="btn btn-sm btn-outline-primary" id="openLedgerModalBtn">
              <i class="fa-solid fa-book-open me-1"></i> Payment History
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" id="openTransferHistoryModalBtn">
              <i class="fa-solid fa-right-left me-1"></i> Transfer History
            </button>
    </div>
    <div class="header-icons">
      <i class="fa fa-search" title="Search" id="txnSearchToggle"></i>
      <i class="fa fa-file-excel" title="Export to Excel" id="txnExcelTrigger"></i>
      <i class="fa fa-print" title="Print" id="txnPrintTrigger"></i>
    </div>
  </div>
  <div class="txn-toolbar" id="txnToolbar" style="display:none;">
    <div class="search-box txn-search-box" style="max-width: 280px;">
      <i class="fa fa-search"></i>
      <input type="text" class="form-control search-input" id="txnSearchInput" placeholder="Search transactions">
    </div>
  </div>


        <table class="txn-table" id="partyTxnTable">
          <thead>
            <tr>
           <th style="width:180px">
  <div class="table-main" onclick="toggleSort(this)">
    <span>Type</span>

    <div class="sort-arrows">
      <i class="fa-solid fa-sort-up"></i>
      <i class="fa-solid fa-sort-down"></i>
    </div>

 <div class="filter-wrapper">
  <i class="fa-solid fa-filter table-filter-icon" onclick="toggleFilterDropdown(this)"></i>

  <div class="filter-dropdown">
    <div class="filter-options">
      <!-- 24 checkboxes -->
      <label><input type="checkbox"> Sale </label>
      <label><input type="checkbox"> Sale(e-invoice) </label>
      <label><input type="checkbox"> Purchase </label>
      <label><input type="checkbox"> Credit Note</label>
      <label><input type="checkbox"> Credit Note(e-invoice)</label>
      <label><input type="checkbox"> Debit Note </label>
      <label><input type="checkbox"> Sale order</label>
      <label><input type="checkbox"> Purchase Order</label>
      <label><input type="checkbox"> Payment-In </label>
      <label><input type="checkbox"> Payment-Out </label>
      <label><input type="checkbox"> Estimate </label>
      <label><input type="checkbox"> Performance Invoice </label>
      <label><input type="checkbox"> Delivery Challan </label>
      <label><input type="checkbox"> Receivable Opening Balance </label>
      <label><input type="checkbox"> Payable Opening Balance</label>
      <label><input type="checkbox"> Sale FA</label>
      <label><input type="checkbox"> Purchase FA</label>
      <label><input type="checkbox"> Sale[Cancelled]</label>
      <label><input type="checkbox"> Job work out(Challan)</label>
      <label><input type="checkbox"> Purchase(Job work)</label>
      <label><input type="checkbox"> Journal Entry</label>

    </div>

    <div class="filter-actions">
      <button class="clear-btn">Clear</button>
      <button class="apply-btn">Apply</button>
    </div>
  </div>
</div>

  </div>
</th>

   <th style="width:100px">
  <div class="table-main" onclick="toggleSort(this)">
    <span style="margin-left: -7px"> Number</span>

    <div class="sort-arrows">
      <i class="fa-solid fa-sort-up"></i>
      <i class="fa-solid fa-sort-down"></i>
    </div>

 <div class="filter-wrapper">
  <i class="fa-solid fa-filter table-filter-icon" onclick="toggleFilterDropdown(this)"></i>

  <div class="filter-dropdown"  style="width:242px; text-align:;">
    <div class="filter-options">
      <!-- 24 checkboxes -->
    <div class="dropdown-container">
  <label style="color: #9ca3af; width:176px;">Select Category</label>
  <input type="text" readonly class="dropdown-input" placeholder="Select..." >
  <div class="dropdown-options">
    <div class="dropdown-option">Contains</div>
    <div class="dropdown-option">Exact Match</div>
  </div>
</div>

        <label style="color: #9ca3af; margin-top:6px; width:176px;"> Number</label>
      <input type="text" style="border:1px solid #d9dfe5; border-radius:6px;height:5vh"
      >

    </div>

    <div class="filter-actions">
      <button class="clear-btn">Clear</button>
      <button class="apply-btn">Apply</button>
    </div>
  </div>
</div>

  </div>
</th>
<!-- date -->
  <th style="width:100px">
  <div class="table-main" onclick="toggleSort(this)">
    <span>Date</span>

    <div class="sort-arrows">
      <i class="fa-solid fa-sort-up"></i>
      <i class="fa-solid fa-sort-down"></i>
    </div>

 <div class="filter-wrapper" style=" position: relative !important;
  overflow: visible;">
  <i class="fa-solid fa-filter table-filter-icon" onclick="toggleFilterDropdown(this)"></i>

  <div class="filter-dropdown"  style="width:242px;">
    <div class="filter-options">
      <!-- 24 checkboxes -->
    <div class="dropdown-container">
  <input type="text" readonly class="dropdown-input" placeholder="Select..." >
  <div class="dropdown-options" style="position:absolute; z-index:100!important;">
    <div class="dropdown-option"> Equal To</div>
    <div class="dropdown-option">Less Than</div>
     <div class="dropdown-option">Greater Than</div>
      <div class="dropdown-option">Range</div>
  </div>
</div>

        <label style="color: #9ca3af; margin-top:6px; width:176px;">Select Date</label>
      <input type="date" style="border:1px solid #d9dfe5; border-radius:6px;height:5vh;color:#9ca3af;padding:6px" >

    </div>

    <div class="filter-actions" style="position: relative;">
      <button class="clear-btn">Clear</button>
      <button class="apply-btn">Apply</button>
    </div>
  </div>
</div>

  </div>
</th>

<!-- total -->
    <th style="width:100px">
  <div class="table-main" onclick="toggleSort(this)">
    <span>Total</span>

    <div class="sort-arrows">
      <i class="fa-solid fa-sort-up"></i>
      <i class="fa-solid fa-sort-down"></i>
    </div>

 <div class="filter-wrapper">
  <i class="fa-solid fa-filter table-filter-icon" onclick="toggleFilterDropdown(this)"></i>

  <div class="filter-dropdown"  style="width:242px; text-align:;">
    <div class="filter-options">
      <!-- 24 checkboxes -->
    <div class="dropdown-container">
  <label style="color: #9ca3af; width:176px;">Select Category</label>
  <input type="text" readonly class="dropdown-input" placeholder="Select..." >
  <div class="dropdown-options" style="position:absolute; z-index:100!important;">
    <div class="dropdown-option"> Equal To</div>
    <div class="dropdown-option">Less Than</div>
     <div class="dropdown-option">Greater Than</div>
  </div>
</div>

        <label style="color: #9ca3af; margin-top:6px; width:176px;">Total</label>
      <input type="text" style="border:1px solid #d9dfe5; border-radius:6px;height:5vh"
      >

    </div>

    <div class="filter-actions">
      <button class="clear-btn">Clear</button>
      <button class="apply-btn">Apply</button>
    </div>
  </div>
</div>

  </div>
</th>

<!-- blance -->

    <th style="width:100px">
  <div class="table-main" onclick="toggleSort(this)">
    <span>Balance</span>

    <div class="sort-arrows">
      <i class="fa-solid fa-sort-up"></i>
      <i class="fa-solid fa-sort-down"></i>
    </div>

 <div class="filter-wrapper">
  <i class="fa-solid fa-filter table-filter-icon" onclick="toggleFilterDropdown(this)"></i>

  <div class="filter-dropdown"  style="width:242px; right: 12px !important;">
    <div class="filter-options">
      <!-- 24 checkboxes -->
    <div class="dropdown-container">
  <label style="color: #9ca3af; width:176px;">Select Category</label>
  <input type="text" readonly class="dropdown-input" placeholder="Select..." >
  <div class="dropdown-options" style="position:absolute; z-index:100!important;">
    <div class="dropdown-option"> Equal To</div>
    <div class="dropdown-option">Less Than</div>
     <div class="dropdown-option">Greater Than</div>
  </div>
</div>

        <label style="color: #9ca3af; margin-top:6px; width:176px;">Balance</label>
      <input type="text" style="border:1px solid #d9dfe5; border-radius:6px;height:5vh"
      >

    </div>

    <div class="filter-actions">
      <button class="clear-btn">Clear</button>
      <button class="apply-btn">Apply</button>
    </div>
  </div>
</div>

  </div>
</th>
<th style="width:100px">
  <div class="table-main" onclick="toggleSort(this)">
    <span>Status</span>
    <div class="sort-arrows">
      <i class="fa-solid fa-sort-up"></i>
      <i class="fa-solid fa-sort-down"></i>
    </div>
  </div>
</th>
<th style="width:110px">
  <div class="table-main">
    <span>Actions</span>
  </div>
</th>


            </tr>
          </thead>
          <tbody id="txnTableBody">
    <!-- Transactions will be loaded here dynamically -->
    <tr id="noTxnRow">
        <td colspan="7" class="text-center text-muted" style="padding: 40px;">
            <i class="fa-solid fa-receipt" style="font-size: 40px; color: #d1d5db;"></i>
            <p class="mt-2">No Transactions Found</p>
            <p style="font-size: 12px; color: #9ca3af;">Select a party to view transactions</p>
        </td>
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
        <div class="d-flex align-items-center gap-2" style="margin-left:79%;">
          <button class="btn btn-sm btn-outline-secondary" type="button" id="partyModalSettingsTrigger" title="Settings"><i class="fa-solid fa-gear"></i></button>
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
            <div class="col-md-4 is-hidden" data-party-setting="party_grouping">
              <label class="form-label fw-600">Party Group</label>
              <div class="party-group-dropdown" id="partyGroupDropdown">
                <button type="button" class="form-control party-group-trigger" id="partyGroupTrigger">
                  <span id="partyGroupTriggerText">Select party group</span>
                  <i class="fa-solid fa-chevron-down"></i>
                </button>
                <input type="hidden" name="party_group" id="partyGroupInput" value="">
                <div class="party-group-menu" id="partyGroupMenu">
                  <button type="button" class="party-group-add-btn" id="openPartyGroupModal">+ New Group</button>
                  <div class="party-group-options" id="partyGroupOptions"></div>
                </div>
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
                  <textarea  id="shippingAddress" class="form-control" name="shipping_address" rows="3" placeholder="Enter shipping address"></textarea>
                </div>
              </div>
            </div>

            <!-- Credit & Balance Tab -->
          <div class="tab-pane fade" id="partyCreditPane" role="tabpanel">
  <div class="row g-3">
    <div class="col-md-4" data-party-setting="opening_balance">
      <label class="form-label">Opening Balance</label>
      <div class="input-group">
        <span class="input-group-text">₹</span>
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

  <!-- To Receive / To Pay Options at the bottom -->
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
</div>
<div class="col-md-6" data-party-setting="party_type">
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

            <!-- Additional Fields Tab -->
            <div class="tab-pane fade" id="partyAdditionalPane" role="tabpanel" data-party-setting="additional_fields">
              <p class="text-muted mb-3" style="font-size:13px;">Add custom fields to track additional information.</p>
              <div class="row g-3">
                @for($i=1; $i<=4; $i++)
                <div class="col-md-6">
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="customField{{$i}}Check">
                    <label class="form-check-label" for="customField{{$i}}Check">Custom Field {{$i}}</label>
                  </div>
                  <input type="text" name="custom_fields[]" class="form-control form-control-sm" placeholder="Field name">
                </div>

                <input type="hidden" id="transactionTypeValue" name="transaction_type">
                @endfor

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

        <div class="txn-option-modal" id="partyGroupModal">
          <div class="txn-option-backdrop" data-close-party-group="true"></div>
          <div class="txn-option-dialog">
            <h3 class="txn-option-title">New Party Group</h3>
            <div>
              <label class="form-label fw-600" for="partyGroupNameInput">Enter Party Group Name</label>
              <input type="text" class="form-control" id="partyGroupNameInput" placeholder="e.g. Wholesale">
            </div>
            <div class="txn-option-actions">
              <button type="button" class="txn-option-btn cancel" id="partyGroupCancel">Cancel</button>
              <button type="button" class="txn-option-btn ok" id="partyGroupSave">OK</button>
            </div>
          </div>
        </div>
      </div>
    </div>
</div>
</div>

<div class="party-settings-drawer" id="partySettingsDrawer">
  <div class="party-settings-backdrop" data-close-party-settings="true"></div>
  <div class="party-settings-panel">
    <div class="party-settings-header">
      <h4>Party Settings</h4>
      <button type="button" class="party-settings-close" id="partySettingsClose">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>
    <div class="party-settings-group">
      <div class="party-settings-group-title">General</div>
      <label class="party-settings-item">
        <span>Party Grouping <i class="fa-regular fa-circle-info party-settings-info"></i></span>
        <input type="checkbox" class="party-setting-toggle" data-setting-target="party_grouping" checked>
      </label>
      <label class="party-settings-item">
        <span>Shipping Address <i class="fa-regular fa-circle-info party-settings-info"></i></span>
        <input type="checkbox" class="party-setting-toggle" data-setting-target="shipping_address" checked>
      </label>
      <label class="party-settings-item">
        <span>Print Shipping Address <i class="fa-regular fa-circle-info party-settings-info"></i></span>
        <input type="checkbox" class="party-setting-toggle" data-setting-target="print_shipping_address" checked>
      </label>
      <label class="party-settings-item">
        <span>Manage Party Status <i class="fa-regular fa-circle-info party-settings-info"></i></span>
        <input type="checkbox" class="party-setting-toggle" data-setting-target="party_status" checked>
      </label>
      <label class="party-settings-item">
        <span>Enable Payment Reminder <i class="fa-regular fa-circle-info party-settings-info"></i></span>
        <input type="checkbox" class="party-setting-toggle" data-setting-target="payment_reminder" checked>
      </label>
      <div class="party-settings-subtext">Remind me for payment due in <i class="fa-regular fa-circle-info party-settings-info"></i></div>
      <div class="party-settings-reminder-row">
        <input type="number" min="1" value="1" id="partyReminderDays" class="party-settings-reminder-input">
        <span class="party-settings-reminder-suffix">(Days)</span>
      </div>
    </div>
    <div class="party-settings-group">
      <div class="party-settings-group-title">Additional fields <i class="fa-regular fa-circle-info party-settings-info"></i></div>
      <div class="party-settings-extra-field">
        <label class="party-settings-item">
          <span>Additional Field 1</span>
          <input type="checkbox" class="party-setting-toggle" data-setting-target="additional_field_1">
        </label>
        <input type="text" class="party-settings-extra-input" id="partyAdditionalField1Name" placeholder="Enter Field Name">
        <label class="party-settings-switch-row">
          <span>Show In Print</span>
          <input type="checkbox" class="party-settings-switch" id="partyAdditionalField1Print">
        </label>
      </div>
      <div class="party-settings-extra-field">
        <label class="party-settings-item">
          <span>Additional Field 2</span>
          <input type="checkbox" class="party-setting-toggle" data-setting-target="additional_field_2">
        </label>
        <input type="text" class="party-settings-extra-input" id="partyAdditionalField2Name" placeholder="Enter Field Name">
        <label class="party-settings-switch-row">
          <span>Show In Print</span>
          <input type="checkbox" class="party-settings-switch" id="partyAdditionalField2Print">
        </label>
      </div>
      <button type="button" class="party-settings-more-btn">
        <i class="fa-solid fa-gear"></i> More Settings
      </button>
    </div>
  </div>
</div>

<div class="party-more-menu" id="partyMoreMenu">
  <button type="button" class="party-more-menu-item" id="importExcelOption">Import from Excel</button>
  <button type="button" class="party-more-menu-item" id="importContactsOption">Import from Contacts</button>
  <button type="button" class="party-more-menu-item" id="generateQrOption">Generate QR Code</button>
</div>

<input type="file" id="partyExcelImportInput" accept=".csv,.xls,.xlsx" hidden>
<input type="file" id="partyContactsImportInput" accept=".vcf,text/vcard,.csv" hidden>

<div class="txn-option-modal" id="partyQrModal">
  <div class="txn-option-backdrop" data-close-party-qr="true"></div>
  <div class="txn-option-dialog">
    <h3 class="txn-option-title">Party QR Code</h3>
    <div style="display:flex;flex-direction:column;align-items:center;gap:14px;">
      <img id="partyQrImage" alt="Party QR Code" style="width:220px;height:220px;border-radius:12px;border:1px solid #e5e7eb;padding:10px;background:#fff;">
      <p id="partyQrText" style="margin:0;text-align:center;color:#6b7280;font-size:13px;"></p>
    </div>
    <div class="txn-option-actions">
      <button type="button" class="txn-option-btn ok" id="partyQrClose">Close</button>
    </div>
  </div>
</div>

<div class="modal fade" id="partyTransferModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content party-transfer-modal">
      <div class="modal-header party-transfer-header">
        <h5 class="modal-title">Party To Party Transfer</h5>
        <div class="party-transfer-header-right">
          <div class="party-transfer-date-wrap">
            <label for="partyTransferDate">Date</label>
            <input type="date" id="partyTransferDate" class="form-control" value="{{ date('Y-m-d') }}">
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
      </div>
      <div class="modal-body party-transfer-body">
        <div class="party-transfer-grid party-transfer-grid-head">
          <div>#</div>
          <div>Entry Type</div>
          <div>Customer Name</div>
          <div>Amount</div>
        </div>

        @for($row = 1; $row <= 2; $row++)
        <div class="party-transfer-grid party-transfer-row" data-transfer-row="{{ $row }}">
          <div class="party-transfer-index">{{ $row }}</div>
          <div>
            <div class="party-transfer-toggle" data-transfer-toggle>
              <button type="button" class="{{ $row === 1 ? 'active' : '' }}" data-transfer-type="received">Received</button>
              <button type="button" class="{{ $row === 2 ? 'active' : '' }}" data-transfer-type="paid">Paid</button>
            </div>
          </div>
          <div>
            <div class="party-transfer-party-select">
              <input type="text" class="form-control transfer-party-input" placeholder="Customer Name" autocomplete="off" data-selected-party-id="">
              <button type="button" class="party-transfer-dropdown-btn"><i class="fa-solid fa-chevron-down"></i></button>
              <div class="party-transfer-party-menu">
                <button type="button" class="party-transfer-add-link" data-bs-toggle="modal" data-bs-target="#addPartyModal">
                  <i class="fa-solid fa-circle-plus"></i> Add Party
                </button>
                <div class="party-transfer-party-list">
                  @foreach($parties as $party)
                    <button type="button" class="party-transfer-party-option" data-party-id="{{ $party->id }}" data-party-name="{{ $party->name }}" data-party-balance="{{ number_format((float) $party->current_balance, 2) }}">
                      <span>{{ $party->name }}</span>
                      <small>{{ number_format((float) $party->current_balance, 2) }}</small>
                    </button>
                  @endforeach
                </div>
              </div>
            </div>
            <div class="party-transfer-balance">Party Balance: <span class="transfer-balance-value">0.00</span></div>
          </div>
          <div>
            <input type="number" min="0" step="0.01" class="form-control transfer-amount-input" placeholder="0.00">
          </div>
        </div>
        @endfor

        <div class="party-transfer-bottom">
          <div class="party-transfer-side-tools">
            <button type="button" class="party-transfer-tool-btn" id="partyTransferDescriptionToggle">
              <i class="fa-regular fa-file-lines"></i> Add Description
            </button>
            <button type="button" class="party-transfer-tool-btn icon-only" id="partyTransferImageTrigger">
              <i class="fa-solid fa-camera"></i>
            </button>
            <input type="file" id="partyTransferImageInput" class="d-none" accept="image/*">
            <div class="party-transfer-extra-panel is-hidden" id="partyTransferDescriptionWrap">
              <textarea id="partyTransferDescription" class="form-control" rows="4" placeholder="Write transfer note or description"></textarea>
            </div>
            <div class="party-transfer-extra-panel is-hidden" id="partyTransferImagePreviewWrap">
              <div class="party-transfer-image-preview-card">
                <img id="partyTransferImagePreview" src="" alt="Transfer attachment preview">
                <button type="button" class="party-transfer-image-remove" id="partyTransferImageRemove">Remove</button>
              </div>
            </div>
          </div>
          <div class="party-transfer-summary">
            <div class="party-transfer-summary-card">
              <span>Total Transfer Amount</span>
              <strong id="partyTransferTotal">Rs 0.00</strong>
            </div>
            <div class="party-transfer-summary-card">
              <span>Selected Parties</span>
              <strong id="partyTransferSelectionCount">0</strong>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer party-transfer-footer">
        <button type="button" class="btn btn-outline-primary" id="partyTransferSaveNew">Save & New</button>
        <button type="button" class="btn btn-primary" id="partyTransferSave">Save</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="partyTxnPreviewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="partyTxnPreviewModalTitle">Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0" style="min-height:70vh;">
        <iframe id="partyTxnPreviewFrame" title="Preview" style="width:100%; min-height:70vh; border:0;"></iframe>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="partyTxnHistoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="partyTxnHistoryModalTitle">History</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="partyTxnHistoryModalBody">
        <div class="text-muted">Loading...</div>
      </div>
    </div>
  </div>
</div>

<div class="txn-option-modal" id="txnOptionModal">
  <div class="txn-option-backdrop" data-close="true"></div>
  <div class="txn-option-dialog">
    <h3 class="txn-option-title" id="txnOptionTitle">Show Options</h3>
    <div class="txn-option-list">
      <label class="txn-option-item">
        <span>Type</span>
        <input type="checkbox" class="txn-export-column" value="type" checked>
      </label>
      <label class="txn-option-item">
        <span>Number</span>
        <input type="checkbox" class="txn-export-column" value="number" checked>
      </label>
      <label class="txn-option-item">
        <span>Date</span>
        <input type="checkbox" class="txn-export-column" value="date" checked>
      </label>
      <label class="txn-option-item">
        <span>Total</span>
        <input type="checkbox" class="txn-export-column" value="total" checked>
      </label>
      <label class="txn-option-item">
        <span>Balance</span>
        <input type="checkbox" class="txn-export-column" value="balance" checked>
      </label>
      <label class="txn-option-item">
        <span>Status</span>
        <input type="checkbox" class="txn-export-column" value="status" checked>
      </label>
    </div>
    <div class="txn-option-actions">
      <button type="button" class="txn-option-btn cancel" id="txnOptionCancel">Cancel</button>
      <button type="button" class="txn-option-btn ok" id="txnOptionConfirm">OK</button>
    </div>
  </div>
</div>

<div class="modal fade" id="partyLedgerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-1" id="partyLedgerModalTitle">Party Ledger</h5>
          <div class="text-muted" style="font-size:13px;">Debit / Credit / Running Balance</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead style="background:#f8fafc;">
              <tr>
                <th>Date</th>
                <th>Transaction ID</th>
                <th>Ref No.</th>
                <th>Type</th>
                <th>Description</th>
                <th class="text-end">Debit</th>
                <th class="text-end">Credit</th>
                <th class="text-end">Running Balance</th>
              </tr>
            </thead>
            <tbody id="partyLedgerTableBody">
              <tr>
                <td colspan="8" class="text-center text-muted py-4">Select a party to view payment ledger.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="partyTransferHistoryModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <div>
          <h5 class="modal-title mb-1" id="partyTransferHistoryModalTitle">Party Transfer History</h5>
          <div class="text-muted" style="font-size:13px;">Party to party transfer entries only</div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead style="background:#f8fafc;">
              <tr>
                <th>Date</th>
                <th>Transaction ID</th>
                <th>Ref No.</th>
                <th>Type</th>
                <th>Counter Party</th>
                <th class="text-end">Amount</th>
                <th>Status</th>
                <th>Description</th>
              </tr>
            </thead>
            <tbody id="partyTransferHistoryTableBody">
              <tr>
                <td colspan="8" class="text-center text-muted py-4">Select a party to view transfer history.</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  .txn-toolbar {
    display: flex;
    justify-content: flex-end;
    margin: 0 0 14px;
  }

  .header-icons i {
    cursor: pointer;
  }

  .btn-party-transfer {
    background: #eff6ff;
    color: #2563eb;
  }

  .btn-party-transfer:hover {
    background: #dbeafe;
    color: #1d4ed8;
  }

  .txn-option-modal {
    position: fixed;
    inset: 0;
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1080;
  }

  .txn-option-modal.active {
    display: flex;
  }

  .txn-option-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.28);
    z-index: 0;
  }

  .txn-option-dialog {
    position: relative;
    z-index: 1;
    pointer-events: auto;
    width: min(360px, calc(100vw - 32px));
    background: #fff;
    border-radius: 12px;
    padding: 24px 28px;
    box-shadow: 0 18px 50px rgba(15, 23, 42, 0.2);
  }

  .txn-option-title {
    margin: 0 0 20px;
    font-size: 18px;
    font-weight: 600;
    color: #111827;
  }

  .txn-option-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
  }

  .txn-option-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    font-size: 15px;
    color: #374151;
  }

  .txn-option-item input[type="checkbox"] {
    width: 22px;
    height: 22px;
    accent-color: #4f46e5;
    cursor: pointer;
  }

  .txn-option-actions {
    display: flex;
    justify-content: flex-end;
    gap: 12px;
    margin-top: 28px;
  }

  .txn-option-btn {
    border: none;
    background: transparent;
    padding: 8px 10px;
    font-size: 14px;
    font-weight: 600;
    letter-spacing: 0.02em;
    text-transform: uppercase;
    cursor: pointer;
  }

  .txn-option-btn.cancel,
  .txn-option-btn.ok {
    color: #4f46e5;
  }

  .party-settings-drawer {
    position: fixed;
    inset: 0;
    display: none;
    z-index: 1090;
  }

  .party-settings-drawer.active {
    display: block;
  }

  .party-settings-backdrop {
    position: absolute;
    inset: 0;
    background: rgba(15, 23, 42, 0.2);
  }

  .party-settings-panel {
    position: absolute;
    top: 0;
    right: 0;
    width: min(380px, 100vw);
    height: 100%;
    background: #fff;
    box-shadow: -10px 0 30px rgba(15, 23, 42, 0.12);
    padding: 24px 22px;
    overflow-y: auto;
  }

  .party-settings-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 20px;
  }

  .party-settings-header h4 {
    margin: 0;
    font-size: 24px;
    font-weight: 600;
    color: #111827;
  }

  .party-settings-close {
    border: none;
    background: transparent;
    color: #6b7280;
    font-size: 20px;
    cursor: pointer;
  }

  .party-settings-group-title {
    background: #f3f4f6;
    color: #374151;
    font-size: 15px;
    font-weight: 600;
    padding: 10px 12px;
    border-radius: 10px;
    margin-bottom: 14px;
  }

  .party-settings-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    padding: 12px 4px;
    color: #374151;
    font-size: 15px;
  }

  .party-settings-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #2563eb;
    cursor: pointer;
  }

  .party-settings-info {
    color: #9ca3af;
    font-size: 13px;
  }

  .party-settings-subtext {
    margin: 4px 4px 10px 4px;
    color: #6b7280;
    font-size: 13px;
  }

  .party-settings-reminder-row {
    display: flex;
    align-items: center;
    gap: 8px;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 10px 14px;
    margin: 0 4px 16px;
  }

  .party-settings-reminder-input,
  .party-settings-extra-input {
    width: 100%;
    border: none;
    outline: none;
    color: #111827;
    font-size: 15px;
    background: transparent;
  }

  .party-settings-reminder-suffix {
    color: #9ca3af;
    font-size: 14px;
    white-space: nowrap;
  }

  .party-settings-extra-field {
    border-bottom: 1px solid #f1f5f9;
    padding-bottom: 14px;
    margin-bottom: 14px;
  }

  .party-settings-extra-input {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px 14px;
    margin: 0 4px 10px;
    width: calc(100% - 8px);
  }

  .party-settings-switch-row {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
    margin: 0 6px;
    color: #6b7280;
    font-size: 14px;
  }

  .party-settings-switch {
    appearance: none;
    width: 34px;
    height: 20px;
    border-radius: 999px;
    background: #e5e7eb;
    position: relative;
    cursor: pointer;
    transition: background 0.2s ease;
  }

  .party-settings-switch::after {
    content: "";
    position: absolute;
    top: 2px;
    left: 2px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 3px rgba(15, 23, 42, 0.2);
    transition: transform 0.2s ease;
  }

  .party-settings-switch:checked {
    background: #dbeafe;
  }

  .party-settings-switch:checked::after {
    transform: translateX(14px);
    background: #2563eb;
  }

  .party-settings-more-btn {
    width: 100%;
    border: none;
    background: #fff;
    color: #4b5563;
    border-radius: 14px;
    padding: 14px 16px;
    font-size: 18px;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    cursor: pointer;
  }

  .party-transfer-modal {
    border-radius: 18px;
    overflow: hidden;
  }

  .party-transfer-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    padding: 22px 24px 18px;
    border-bottom: 1px solid #eef2f7;
  }

  .party-transfer-header .modal-title {
    font-size: 20px;
    font-weight: 700;
    color: #111827;
  }

  .party-transfer-header-right {
    display: flex;
    align-items: center;
    gap: 16px;
  }

  .party-transfer-source,
  .party-transfer-date-wrap {
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .party-transfer-source-label,
  .party-transfer-date-wrap label {
    margin: 0;
    font-size: 13px;
    color: #64748b;
    white-space: nowrap;
  }

  .party-transfer-source select,
  .party-transfer-date-wrap input {
    min-width: 180px;
    border-radius: 10px;
  }

  .party-transfer-body {
    padding: 0 0 18px;
  }

  .party-transfer-grid {
    display: grid;
    grid-template-columns: 72px 260px 1fr 180px;
    gap: 18px;
    align-items: start;
  }

  .party-transfer-grid-head {
    padding: 14px 24px;
    border-bottom: 1px solid #eef2f7;
    background: #fafafa;
    color: #475569;
    font-size: 13px;
    font-weight: 700;
    text-transform: uppercase;
  }

  .party-transfer-row {
    padding: 18px 24px;
    border-bottom: 1px solid #eef2f7;
  }

  .party-transfer-index {
    color: #94a3b8;
    font-size: 18px;
    font-weight: 600;
    padding-top: 8px;
  }

  .party-transfer-toggle {
    display: inline-flex;
    align-items: center;
    background: #f1f5f9;
    border-radius: 999px;
    padding: 4px;
    gap: 4px;
  }

  .party-transfer-toggle button {
    border: none;
    background: transparent;
    color: #64748b;
    border-radius: 999px;
    padding: 8px 16px;
    font-size: 13px;
    font-weight: 700;
  }

  .party-transfer-toggle button.active[data-transfer-type="received"] {
    background: #10b981;
    color: #fff;
  }

  .party-transfer-toggle button.active[data-transfer-type="paid"] {
    background: #ef4444;
    color: #fff;
  }

  .party-transfer-party-select {
    position: relative;
  }

  .party-transfer-dropdown-btn {
    position: absolute;
    top: 50%;
    right: 12px;
    transform: translateY(-50%);
    border: none;
    background: transparent;
    color: #94a3b8;
  }

  .party-transfer-party-menu {
    position: absolute;
    top: calc(100% + 10px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #dbe4f0;
    border-radius: 14px;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
    padding: 10px 0;
    display: none;
    z-index: 30;
  }

  .party-transfer-party-select.open .party-transfer-party-menu {
    display: block;
  }

  .party-transfer-add-link,
  .party-transfer-party-option {
    width: 100%;
    border: none;
    background: transparent;
    padding: 10px 16px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 10px;
    text-align: left;
  }

  .party-transfer-add-link {
    color: #2563eb;
    font-weight: 600;
    border-bottom: 1px solid #eef2f7;
  }

  .party-transfer-party-list {
    max-height: 178px;
    overflow-y: auto;
  }

  .party-transfer-party-option:hover,
  .party-transfer-add-link:hover {
    background: #f8fafc;
  }

  .party-transfer-party-option span {
    color: #111827;
    font-size: 14px;
  }

  .party-transfer-party-option small {
    color: #64748b;
    font-size: 12px;
  }

  .party-transfer-balance {
    margin-top: 10px;
    color: #64748b;
    font-size: 13px;
  }

  .party-transfer-balance .transfer-balance-value {
    color: #111827;
    font-weight: 700;
  }

  .party-transfer-bottom {
    display: grid;
    grid-template-columns: minmax(0, 1fr) 280px;
    gap: 20px;
    padding: 20px 24px 0;
  }

  .party-transfer-side-tools {
    display: flex;
    flex-direction: column;
    gap: 12px;
  }

  .party-transfer-tool-btn {
    width: fit-content;
    border: none;
    background: transparent;
    color: #6b7280;
    font-size: 14px;
    font-weight: 600;
    padding: 0;
  }

  .party-transfer-tool-btn.icon-only {
    font-size: 22px;
  }

  .party-transfer-extra-panel.is-hidden {
    display: none;
  }

  .party-transfer-image-preview-card {
    display: inline-flex;
    flex-direction: column;
    gap: 10px;
    padding: 12px;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    background: #f8fafc;
    width: min(280px, 100%);
  }

  .party-transfer-image-preview-card img {
    width: 100%;
    max-height: 180px;
    object-fit: cover;
    border-radius: 10px;
    border: 1px solid #dbe3ef;
  }

  .party-transfer-image-remove {
    align-self: flex-end;
    border: none;
    background: transparent;
    color: #dc2626;
    font-size: 13px;
    font-weight: 600;
  }

  .party-transfer-summary {
    display: grid;
    gap: 12px;
  }

  .party-transfer-summary-card {
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 16px;
    background: #f8fafc;
  }

  .party-transfer-summary-card span {
    display: block;
    color: #64748b;
    font-size: 13px;
    margin-bottom: 6px;
  }

  .party-transfer-summary-card strong {
    color: #111827;
    font-size: 22px;
  }

  .party-transfer-footer {
    padding: 0 24px 22px;
    border-top: none;
    gap: 12px;
  }

  .party-group-dropdown {
    position: relative;
  }

  @media (max-width: 991px) {
    .party-transfer-header,
    .party-transfer-header-right {
      flex-direction: column;
      align-items: stretch;
    }

    .party-transfer-grid {
      grid-template-columns: 1fr;
    }

    .party-transfer-grid-head {
      display: none;
    }

    .party-transfer-row {
      border: 1px solid #eef2f7;
      border-radius: 14px;
      margin: 14px 14px 0;
    }

    .party-transfer-bottom {
      grid-template-columns: 1fr;
      padding: 20px 14px 0;
    }
  }

  .party-group-trigger {
    display: flex;
    align-items: center;
    justify-content: space-between;
    text-align: left;
  }

  .party-group-menu {
    position: absolute;
    top: calc(100% + 6px);
    left: 0;
    right: 0;
    background: #fff;
    border: 1px solid #dbe3ea;
    border-radius: 10px;
    box-shadow: 0 14px 30px rgba(15, 23, 42, 0.12);
    padding: 8px 0;
    display: none;
    z-index: 15;
  }

  .party-group-menu.active {
    display: block;
  }

  .party-group-options {
    max-height: 160px;
    overflow-y: auto;
  }

  .party-group-add-btn,
  .party-group-option {
    width: 100%;
    border: none;
    background: transparent;
    text-align: left;
    padding: 10px 14px;
    font-size: 15px;
    cursor: pointer;
  }

  .party-group-add-btn {
    color: #2563eb;
    font-weight: 600;
  }

  .party-group-option {
    color: #374151;
  }

  .party-group-add-btn:hover,
  .party-group-option:hover {
    background: #f8fafc;
  }

  .party-more-menu {
    position: absolute;
    top: 70px;
    right: 24px;
    min-width: 260px;
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    box-shadow: 0 20px 45px rgba(15, 23, 42, 0.15);
    padding: 10px 0;
    display: none;
    z-index: 1085;
  }

  .party-more-menu.active {
    display: block;
  }

  .party-more-menu-item {
    width: 100%;
    border: none;
    background: transparent;
    text-align: left;
    padding: 14px 18px;
    font-size: 15px;
    color: #374151;
    cursor: pointer;
  }

  .party-more-menu-item:hover {
    background: #f8fafc;
  }

  [data-party-setting].is-hidden {
    display: none !important;
  }

  .party-txn-action-menu .dropdown-menu {
    min-width: 210px;
  }

  .party-txn-action-btn {
    border: none;
    background: transparent;
    color: #64748b;
  }
</style>
@endpush
@push('scripts')
<script>
// ============ GLOBAL FUNCTIONS (filter, sort, dropdown) ============
function toggleFilter(){
    let dropdown = document.getElementById("filterDropdown");
    if(dropdown.style.display === "block"){
        dropdown.style.display = "none";
    }else{
        dropdown.style.display = "block";
    }
}

function toggleHeaderDropdown(element) {
    const dropdownMenu = element.nextElementSibling;
    const isVisible = dropdownMenu.style.display === 'block';
    dropdownMenu.style.display = isVisible ? 'none' : 'block';
    element.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
}

function toggleSort(el){
    el.classList.toggle("active");
}

function toggleParentArrows(el){
    el.classList.toggle('active');
}

function toggleFilterDropdown(icon){
    const dropdown = icon.nextElementSibling;
    dropdown.style.display = dropdown.style.display === 'flex' ? 'none' : 'flex';
}

document.addEventListener('click', function(e){
    document.querySelectorAll('.filter-dropdown').forEach(dd=>{
        if(!dd.contains(e.target) && !dd.previousElementSibling?.contains(e.target)){
            dd.style.display = 'none';
        }
    });
});

// ============ MAIN PARTY CRUD ============
document.addEventListener("DOMContentLoaded", function () {

    const saveBtn = document.getElementById("btnSaveParty");
    const saveNewBtn = document.getElementById("btnSaveNewParty");
    const updateBtn = document.getElementById("btnUpdateParty");
    const deleteBtn = document.getElementById("btnDeleteParty");
    const partyList = document.getElementById("partiesList");
    const addModalEl = document.getElementById('addPartyModal');
    const addModal = new bootstrap.Modal(addModalEl);
    const txnToolbar = document.getElementById("txnToolbar");
    const txnSearchToggle = document.getElementById("txnSearchToggle");
    const txnSearchInput = document.getElementById("txnSearchInput");
    const txnPrintTrigger = document.getElementById("txnPrintTrigger");
    const txnExcelTrigger = document.getElementById("txnExcelTrigger");
    const txnOptionModal = document.getElementById("txnOptionModal");
    const txnOptionTitle = document.getElementById("txnOptionTitle");
    const txnOptionCancel = document.getElementById("txnOptionCancel");
    const txnOptionConfirm = document.getElementById("txnOptionConfirm");
    const partyTxnPreviewModalEl = document.getElementById("partyTxnPreviewModal");
    const partyTxnPreviewModal = partyTxnPreviewModalEl ? bootstrap.Modal.getOrCreateInstance(partyTxnPreviewModalEl) : null;
    const partyTxnPreviewFrame = document.getElementById("partyTxnPreviewFrame");
    const partyTxnPreviewModalTitle = document.getElementById("partyTxnPreviewModalTitle");
    const partyTxnHistoryModalEl = document.getElementById("partyTxnHistoryModal");
    const partyTxnHistoryModal = partyTxnHistoryModalEl ? bootstrap.Modal.getOrCreateInstance(partyTxnHistoryModalEl) : null;
    const partyTxnHistoryModalTitle = document.getElementById("partyTxnHistoryModalTitle");
    const partyTxnHistoryModalBody = document.getElementById("partyTxnHistoryModalBody");
    const partySettingsTrigger = document.getElementById("partySettingsTrigger");
    const partyModalSettingsTrigger = document.getElementById("partyModalSettingsTrigger");
    const partySettingsDrawer = document.getElementById("partySettingsDrawer");
    const partySettingsClose = document.getElementById("partySettingsClose");
    const partyMoreOptionsTrigger = document.getElementById("partyMoreOptionsTrigger");
    const partyMoreMenu = document.getElementById("partyMoreMenu");
    const partyExcelImportInput = document.getElementById("partyExcelImportInput");
    const partyContactsImportInput = document.getElementById("partyContactsImportInput");
    const importExcelOption = document.getElementById("importExcelOption");
    const importContactsOption = document.getElementById("importContactsOption");
    const generateQrOption = document.getElementById("generateQrOption");
    const partyQrModal = document.getElementById("partyQrModal");
    const partyQrImage = document.getElementById("partyQrImage");
    const partyQrText = document.getElementById("partyQrText");
    const partyQrClose = document.getElementById("partyQrClose");
    const partyGroupInput = document.getElementById("partyGroupInput");
    const partyGroupTrigger = document.getElementById("partyGroupTrigger");
    const partyGroupTriggerText = document.getElementById("partyGroupTriggerText");
    const partyGroupMenu = document.getElementById("partyGroupMenu");
    const partyGroupOptions = document.getElementById("partyGroupOptions");
    const openPartyGroupModal = document.getElementById("openPartyGroupModal");
    const partyGroupModal = document.getElementById("partyGroupModal");
    const partyGroupNameInput = document.getElementById("partyGroupNameInput");
    const partyGroupCancel = document.getElementById("partyGroupCancel");
    const partyGroupSave = document.getElementById("partyGroupSave");
    const creditLimitAmountWrap = document.getElementById("creditLimitAmountWrap");
    const creditLimitAmountInput = document.getElementById("creditLimitAmountInput");
    const partyReminderDays = document.getElementById("partyReminderDays");
    const partyAdditionalField1Name = document.getElementById("partyAdditionalField1Name");
    const partyAdditionalField2Name = document.getElementById("partyAdditionalField2Name");
    const partyTransferModalEl = document.getElementById("partyTransferModal");
    const partyTransferModal = partyTransferModalEl ? bootstrap.Modal.getOrCreateInstance(partyTransferModalEl) : null;
    const partyTransferSave = document.getElementById("partyTransferSave");
    const partyTransferSaveNew = document.getElementById("partyTransferSaveNew");
    const partyTransferDate = document.getElementById("partyTransferDate");
    const partyTransferTotal = document.getElementById("partyTransferTotal");
    const partyTransferSelectionCount = document.getElementById("partyTransferSelectionCount");
    const partyTransferDescriptionToggle = document.getElementById("partyTransferDescriptionToggle");
    const partyTransferDescriptionWrap = document.getElementById("partyTransferDescriptionWrap");
    const partyTransferDescriptionInput = document.getElementById("partyTransferDescription");
    const partyTransferImageTrigger = document.getElementById("partyTransferImageTrigger");
    const partyTransferImageInput = document.getElementById("partyTransferImageInput");
    const partyTransferImagePreviewWrap = document.getElementById("partyTransferImagePreviewWrap");
    const partyTransferImagePreview = document.getElementById("partyTransferImagePreview");
    const partyTransferImageRemove = document.getElementById("partyTransferImageRemove");
    const openLedgerModalBtn = document.getElementById("openLedgerModalBtn");
    const openTransferHistoryModalBtn = document.getElementById("openTransferHistoryModalBtn");
    const partyLedgerModalEl = document.getElementById("partyLedgerModal");
    const partyLedgerModal = partyLedgerModalEl ? bootstrap.Modal.getOrCreateInstance(partyLedgerModalEl) : null;
    const partyLedgerTableBody = document.getElementById("partyLedgerTableBody");
    const partyLedgerModalTitle = document.getElementById("partyLedgerModalTitle");
    const partyTransferHistoryModalEl = document.getElementById("partyTransferHistoryModal");
    const partyTransferHistoryModal = partyTransferHistoryModalEl ? bootstrap.Modal.getOrCreateInstance(partyTransferHistoryModalEl) : null;
    const partyTransferHistoryTableBody = document.getElementById("partyTransferHistoryTableBody");
    const partyTransferHistoryModalTitle = document.getElementById("partyTransferHistoryModalTitle");

    let currentPartyId = null;
    let transactionsState = [];
    let filteredTransactionsState = [];
    let pendingTxnAction = null;
    const PARTY_GROUPS_STORAGE_KEY = 'partyGroups';
    const existingPartyGroups = Array.from(document.querySelectorAll('.party-item'))
        .map((item) => (item.dataset.partyGroup || '').trim())
        .filter(Boolean);
    let partyGroups = Array.from(new Set(['General', ...existingPartyGroups]));
    let partySettingsState = {
        party_grouping: true,
        shipping_address: true,
        print_shipping_address: true,
        party_status: true,
        payment_reminder: true,
        additional_field_1: false,
        additional_field_2: false
    };
    const exportColumns = [
        { key: 'type', label: 'Type' },
        { key: 'number', label: 'Number' },
        { key: 'date', label: 'Date' },
        { key: 'total', label: 'Total' },
        { key: 'balance', label: 'Balance' },
        { key: 'status', label: 'Status' }
    ];

    // Checkbox mutually exclusive
    const toReceive = document.getElementById('toReceive');
    const toPay = document.getElementById('toPay');
    const creditLimitSwitch = document.getElementById("creditLimitSwitch");
    const transactionTypeValue = document.getElementById('transactionTypeValue');

    [toReceive, toPay].forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            if (this.checked) {
                [toReceive, toPay].forEach(cb => {
                    if (cb !== this) cb.checked = false;
                });
                transactionTypeValue.value = this.value;
            } else {
                transactionTypeValue.value = '';
            }
        });
    });
    creditLimitSwitch.addEventListener('change', syncCreditLimitVisibility);

    document.querySelectorAll('[data-transfer-toggle]').forEach((toggle) => {
        toggle.addEventListener('click', function (event) {
            const button = event.target.closest('[data-transfer-type]');
            if (!button) return;
            const currentRow = button.closest('.party-transfer-row');
            const currentRowNumber = Number(currentRow?.dataset.transferRow || 0);
            const oppositeRow = document.querySelector(`.party-transfer-row[data-transfer-row="${currentRowNumber === 1 ? 2 : 1}"]`);
            const selectedType = button.dataset.transferType;
            const oppositeType = selectedType === 'received' ? 'paid' : 'received';

            toggle.querySelectorAll('[data-transfer-type]').forEach((item) => item.classList.remove('active'));
            button.classList.add('active');

            if (oppositeRow) {
                oppositeRow.querySelectorAll('[data-transfer-type]').forEach((item) => item.classList.remove('active'));
                oppositeRow.querySelector(`[data-transfer-type="${oppositeType}"]`)?.classList.add('active');
            }
        });
    });

    document.querySelectorAll('.party-transfer-dropdown-btn').forEach((button) => {
        button.addEventListener('click', function () {
            const select = button.closest('.party-transfer-party-select');
            const shouldOpen = !select.classList.contains('open');
            closeAllTransferMenus();
            if (shouldOpen) {
                select.classList.add('open');
            }
        });
    });

    document.querySelectorAll('.party-transfer-party-option').forEach((option) => {
        option.addEventListener('click', function () {
            const select = option.closest('.party-transfer-party-select');
            const input = select.querySelector('.transfer-party-input');
            const balance = select.parentElement.querySelector('.transfer-balance-value');
            if (input) {
                input.value = option.dataset.partyName || '';
                input.dataset.selectedPartyId = option.dataset.partyId || '';
            }
            if (balance) balance.textContent = option.dataset.partyBalance || '0.00';
            select.classList.remove('open');
            updatePartyTransferSummary();
        });
    });

    document.querySelectorAll('.transfer-party-input').forEach((input) => {
        input.addEventListener('focus', function () {
            closeAllTransferMenus();
            input.closest('.party-transfer-party-select')?.classList.add('open');
        });

        input.addEventListener('input', function () {
            const query = input.value.trim().toLowerCase();
            const select = input.closest('.party-transfer-party-select');
            input.dataset.selectedPartyId = '';
            const balance = select?.parentElement.querySelector('.transfer-balance-value');
            if (balance) balance.textContent = '0.00';
            select?.querySelectorAll('.party-transfer-party-option').forEach((option) => {
                option.style.display = (option.dataset.partyName || '').toLowerCase().includes(query) ? '' : 'none';
            });
            updatePartyTransferSummary();
        });
    });

    document.querySelectorAll('.transfer-amount-input').forEach((input) => {
        input.addEventListener('input', updatePartyTransferSummary);
    });

    document.querySelectorAll('.party-transfer-add-link').forEach((button) => {
        button.addEventListener('click', function () {
            partyTransferModal?.hide();
            closeAllTransferMenus();
        });
    });

    partyTransferModalEl?.addEventListener('show.bs.modal', resetPartyTransferModal);
    partyTransferSave?.addEventListener('click', () => persistPartyTransfer(true));
    partyTransferSaveNew?.addEventListener('click', () => persistPartyTransfer(false));
    partyTransferDescriptionToggle?.addEventListener('click', togglePartyTransferDescription);
    partyTransferImageTrigger?.addEventListener('click', () => partyTransferImageInput?.click());
    partyTransferImageInput?.addEventListener('change', handlePartyTransferImageSelection);
    partyTransferImageRemove?.addEventListener('click', clearPartyTransferImage);

    document.addEventListener('click', function (event) {
        if (!event.target.closest('.party-transfer-party-select')) {
            closeAllTransferMenus();
        }
    });

    // Clear filter buttons
    document.querySelectorAll('.clear-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const checkboxes = this.closest('.filter-dropdown')?.querySelectorAll('input[type="checkbox"]');
            if (checkboxes) checkboxes.forEach(cb => cb.checked = false);
        });
    });

    // RESET MODAL
    function resetModal() {
        document.getElementById("addPartyForm").reset();
        renderPartyGroupOptions();
        syncCreditLimitVisibility();
        saveBtn.style.display = "inline-block";
        saveNewBtn.style.display = "inline-block";
        updateBtn.style.display = "none";
        deleteBtn.style.display = "none";
        currentPartyId = null;
    }

    function closeAllTransferMenus() {
        document.querySelectorAll('.party-transfer-party-select.open').forEach((menu) => {
            menu.classList.remove('open');
        });
    }

    function updatePartyTransferSummary() {
        let total = 0;
        let selected = 0;

        document.querySelectorAll('.transfer-amount-input').forEach((input) => {
            total += Number(input.value || 0);
        });

        document.querySelectorAll('.transfer-party-input').forEach((input) => {
            if ((input.value || '').trim()) {
                selected += 1;
            }
        });

        if (partyTransferTotal) {
            partyTransferTotal.textContent = `Rs ${total.toFixed(2)}`;
        }

        if (partyTransferSelectionCount) {
            partyTransferSelectionCount.textContent = String(selected);
        }
    }

    function togglePartyTransferDescription(forceVisible = null) {
        if (!partyTransferDescriptionWrap) return;

        const shouldShow = forceVisible === null
            ? partyTransferDescriptionWrap.classList.contains('is-hidden')
            : forceVisible;

        partyTransferDescriptionWrap.classList.toggle('is-hidden', !shouldShow);

        if (shouldShow) {
            partyTransferDescriptionInput?.focus();
        }
    }

    function clearPartyTransferImage() {
        if (partyTransferImageInput) {
            partyTransferImageInput.value = '';
        }

        if (partyTransferImagePreview) {
            partyTransferImagePreview.src = '';
        }

        partyTransferImagePreviewWrap?.classList.add('is-hidden');
    }

    function handlePartyTransferImageSelection(event) {
        const file = event.target.files?.[0];

        if (!file) {
            clearPartyTransferImage();
            return;
        }

        const reader = new FileReader();
        reader.onload = function (loadEvent) {
            if (partyTransferImagePreview) {
                partyTransferImagePreview.src = loadEvent.target?.result || '';
            }
            partyTransferImagePreviewWrap?.classList.remove('is-hidden');
        };
        reader.readAsDataURL(file);
    }

    function resetPartyTransferModal() {
        if (!partyTransferModalEl) return;

        if (partyTransferDate) {
            partyTransferDate.value = "{{ date('Y-m-d') }}";
        }

        const descriptionBox = document.getElementById("partyTransferDescription");
        if (descriptionBox) {
            descriptionBox.value = '';
        }
        togglePartyTransferDescription(false);
        clearPartyTransferImage();

        document.querySelectorAll('.party-transfer-row').forEach((row, index) => {
            const partyInput = row.querySelector('.transfer-party-input');
            const amountInput = row.querySelector('.transfer-amount-input');
            const balanceLabel = row.querySelector('.transfer-balance-value');

            if (partyInput) {
                partyInput.value = '';
                partyInput.dataset.selectedPartyId = '';
            }
            if (amountInput) amountInput.value = '';
            if (balanceLabel) balanceLabel.textContent = '0.00';

            row.querySelectorAll('[data-transfer-type]').forEach((button) => button.classList.remove('active'));
            const defaultType = index === 0 ? 'received' : 'paid';
            row.querySelector(`[data-transfer-type="${defaultType}"]`)?.classList.add('active');
        });

        closeAllTransferMenus();
        updatePartyTransferSummary();
    }

    // GET FORM DATA
    function getPartyData() {
        const selectedPartyTypes = Array.from(document.querySelectorAll('input[name="party_type[]"]:checked'))
            .map((input) => input.value);

        return {
            name: document.getElementById("partyNameInput").value,
            phone: document.getElementById("partyPhoneInput").value,
            phone_number_2: document.getElementById("partyPhone2Input").value,
            ptcl_number: document.getElementById("partyPtclInput").value,
            party_group: partyGroupInput?.value || '',
            email: document.querySelector('#partyAddressPane input[type="email"]').value,
            city: document.getElementById("partyCityInput").value,
            address: document.getElementById("partyAddressInput").value,
            billing_address: document.getElementById("billingAddress").value,
            shipping_address: document.getElementById("shippingAddress").value,
            due_days: document.getElementById("partyDueDaysInput")?.value || '',
            opening_balance: document.querySelector('#partyCreditPane input[type="number"]').value,
            as_of_date: document.querySelector('#partyCreditPane input[type="date"]').value,
            credit_limit_enabled: document.getElementById("creditLimitSwitch").checked ? 1 : 0,
            credit_limit_amount: creditLimitAmountInput?.value || '',
            transaction_type: document.getElementById("toReceive").checked
                ? 'receive'
                : document.getElementById("toPay").checked
                    ? 'pay'
                    : null,
            party_type: selectedPartyTypes,
        };
    }

    function formatTxnType(rawType) {
        if (rawType === 'pay') return 'Payable Opening Balance';
        if (rawType === 'receive') return 'Receivable Opening Balance';
        return rawType || '-';
    }

    function formatTxnStatus(rawStatus) {
        const normalizedStatusText = (rawStatus || '').toLowerCase();

        if (normalizedStatusText === 'receive') return 'To Receive';
        if (normalizedStatusText === 'pay') return 'To Pay';
        if (['paid', 'completed', 'closed', 'converted'].includes(normalizedStatusText)) return 'Paid';
        if (['partial', 'pending', 'confirmed'].includes(normalizedStatusText)) return rawStatus;
        return rawStatus || 'Open';
    }

    function getTransactionExportRows() {
        return filteredTransactionsState.map(txn => ({
            type: formatTxnType(txn.type),
            number: txn.number || '-',
            date: txn.date || '-',
            total: `Rs ${txn.total ?? 0}`,
            balance: `Rs ${txn.balance ?? 0}`,
            status: formatTxnStatus(txn.status)
        }));
    }

    function renderPaymentLedgerTable(rows, partyName = 'Party') {
        if (!partyLedgerTableBody) return;

        if (partyLedgerModalTitle) {
            partyLedgerModalTitle.textContent = `${partyName} Payment Ledger`;
        }

        if (!rows.length) {
            partyLedgerTableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No payment ledger entries found.</td>
                </tr>
            `;
            return;
        }

        partyLedgerTableBody.innerHTML = rows.map((row) => `
            <tr>
                <td>${escapeHtml(row.date || '-')}</td>
                <td>${escapeHtml(row.id || '-')}</td>
                <td>${escapeHtml(row.number || '-')}</td>
                <td>${escapeHtml(row.type || '-')}</td>
                <td>${escapeHtml(row.description || '-')}</td>
                <td class="text-end">${escapeHtml(row.debit || '0.00')}</td>
                <td class="text-end">${escapeHtml(row.credit || '0.00')}</td>
                <td class="text-end">${escapeHtml(row.running_balance || row.balance || '0.00')}</td>
            </tr>
        `).join('');
    }

    function openLedgerModal() {
        if (!currentPartyId) {
            alert('Select Party First');
            return;
        }

        if (partyLedgerTableBody) {
            partyLedgerTableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Loading payment ledger...</td>
                </tr>
            `;
        }

        partyLedgerModal?.show();

        fetch(`/dashboard/parties/${currentPartyId}/ledger`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    throw new Error('Unable to load payment ledger.');
                }

                renderPaymentLedgerTable(Array.isArray(data.ledger) ? data.ledger : [], data.party_name || 'Party');
            })
            .catch(error => {
                console.error('Payment Ledger Load Error:', error);
                if (partyLedgerTableBody) {
                    partyLedgerTableBody.innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center text-danger py-4">Unable to load payment ledger.</td>
                        </tr>
                    `;
                }
            });
    }

    function renderTransferHistoryTable(rows, partyName = 'Party') {
        if (!partyTransferHistoryTableBody) return;

        if (partyTransferHistoryModalTitle) {
            partyTransferHistoryModalTitle.textContent = `${partyName} Transfer History`;
        }

        if (!rows.length) {
            partyTransferHistoryTableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">No transfer history found.</td>
                </tr>
            `;
            return;
        }

        partyTransferHistoryTableBody.innerHTML = rows.map((row) => `
            <tr>
                <td>${escapeHtml(row.date || '-')}</td>
                <td>${escapeHtml(row.id || '-')}</td>
                <td>${escapeHtml(row.ref_no || '-')}</td>
                <td>${escapeHtml(row.type || '-')}</td>
                <td>${escapeHtml(row.counter_party || '-')}</td>
                <td class="text-end">${escapeHtml(row.amount || '0.00')}</td>
                <td>${escapeHtml(row.status || '-')}</td>
                <td>${escapeHtml(row.description || '-')}</td>
            </tr>
        `).join('');
    }

    function openTransferHistoryModal() {
        if (!currentPartyId) {
            alert('Select Party First');
            return;
        }

        if (partyTransferHistoryTableBody) {
            partyTransferHistoryTableBody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Loading transfer history...</td>
                </tr>
            `;
        }

        partyTransferHistoryModal?.show();

        fetch(`/dashboard/parties/${currentPartyId}/transfer-history`)
            .then(res => res.json())
            .then(data => {
                if (!data.success) {
                    throw new Error('Unable to load transfer history.');
                }

                renderTransferHistoryTable(Array.isArray(data.transfers) ? data.transfers : [], data.party_name || 'Party');
            })
            .catch(error => {
                console.error('Transfer History Load Error:', error);
                if (partyTransferHistoryTableBody) {
                    partyTransferHistoryTableBody.innerHTML = `
                        <tr>
                            <td colspan="8" class="text-center text-danger py-4">Unable to load transfer history.</td>
                        </tr>
                    `;
                }
            });
    }

    function escapeHtml(value) {
        return String(value ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function persistPartyTransfer(closeAfterSave = true) {
        const rows = Array.from(document.querySelectorAll('.party-transfer-row')).map((row) => {
            const partyInput = row.querySelector('.transfer-party-input');
            return {
                party_id: partyInput?.dataset.selectedPartyId || '',
                party_name: partyInput?.value.trim() || '',
                type: row.querySelector('[data-transfer-type].active')?.dataset.transferType || '',
                amount: Number(row.querySelector('.transfer-amount-input')?.value || 0),
            };
        }).filter((row) => row.party_name || row.amount > 0);

        if (!rows.length) {
            alert('Please fill at least one transfer row.');
            return;
        }

        const invalidRow = rows.find((row) => !row.party_id || row.amount <= 0);
        if (invalidRow) {
            alert('Please select a valid party and enter amount for each transfer row.');
            return;
        }

        const paidRows = rows.filter((row) => row.type === 'paid');
        const receivedRows = rows.filter((row) => row.type === 'received');

        if (paidRows.length !== 1 || receivedRows.length !== 1) {
            alert('One party must be Paid and one party must be Received.');
            return;
        }

        if (String(paidRows[0].party_id) === String(receivedRows[0].party_id)) {
            alert('Paid party and Received party cannot be same.');
            return;
        }

        if (Number(paidRows[0].amount) !== Number(receivedRows[0].amount)) {
            alert('Paid amount and Received amount must be equal.');
            return;
        }

        const formData = new FormData();
        formData.append('transfer_date', partyTransferDate?.value || '');
        formData.append('description', partyTransferDescriptionInput?.value || '');
        formData.append('rows', JSON.stringify(rows.map(({ party_id, type, amount }) => ({ party_id, type, amount }))));

        if (partyTransferImageInput?.files?.[0]) {
            formData.append('attachment', partyTransferImageInput.files[0]);
        }

        fetch("{{ route('parties.transfer.store') }}", {
            method: "POST",
            headers: {
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json"
            },
            body: formData
        })
        .then(async (response) => {
            const data = await response.json();
            if (!response.ok || !data.success) {
                throw new Error(data?.message || 'Transfer save failed.');
            }
            return data;
        })
        .then((data) => {
            alert(data.message || 'Party transfer saved successfully.');

            if (closeAfterSave) {
                partyTransferModal?.hide();
            }

            resetPartyTransferModal();
            window.location.reload();
        })
        .catch((error) => {
            console.error('Party Transfer Error:', error);
            alert(error.message || 'Unable to save party transfer.');
        });
    }

    async function fetchJson(url, options = {}) {
        const response = await fetch(url, {
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                ...(options.headers || {}),
            },
            ...options,
        });

        const data = await response.json();
        if (!response.ok) {
            throw new Error(data?.message || 'Request failed.');
        }

        return data;
    }

    function openPartyTxnPreview(url, title) {
        if (!url) {
            alert('Preview is not available for this transaction.');
            return;
        }

        if (!partyTxnPreviewModal || !partyTxnPreviewFrame) {
            window.open(url, '_blank');
            return;
        }

        partyTxnPreviewModalTitle.textContent = title || 'Preview';
        partyTxnPreviewFrame.src = url;
        partyTxnPreviewModal.show();
    }

    function openPartyTxnHistory(title, rows) {
        if (!partyTxnHistoryModal || !partyTxnHistoryModalBody) {
            return;
        }

        partyTxnHistoryModalTitle.textContent = title || 'History';

        if (!rows.length) {
            partyTxnHistoryModalBody.innerHTML = `<div class="text-muted">No records found.</div>`;
            partyTxnHistoryModal.show();
            return;
        }

        const tableRows = rows.map((row, index) => `
            <tr>
                <td>${index + 1}</td>
                <td>${escapeHtml(row.bank_name || row.bank || '-')}</td>
                <td>${escapeHtml(row.transaction_type || row.type || '-')}</td>
                <td>${escapeHtml(row.amount || '-')}</td>
                <td>${escapeHtml(row.reference_no || row.reference || '-')}</td>
                <td>${escapeHtml(row.payment_date || row.created_at || '-')}</td>
            </tr>
        `).join('');

        partyTxnHistoryModalBody.innerHTML = `
            <div class="table-responsive">
                <table class="table table-bordered table-sm history-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Bank</th>
                            <th>Type</th>
                            <th>Amount</th>
                            <th>Reference</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>${tableRows}</tbody>
                </table>
            </div>
        `;

        partyTxnHistoryModal.show();
    }

    function getPartyTxnActionsHtml(txn) {
        const hasActions = txn.actions && Object.values(txn.actions).some(value => Boolean(value));

        if (!hasActions) {
            return `<span style="color:#94a3b8;font-size:13px;">-</span>`;
        }

        return `
            <div class="dropdown party-txn-action-menu"
                 data-id="${escapeHtml(txn.id)}"
                 data-type="${escapeHtml(txn.raw_type || '')}"
                 data-number="${escapeHtml(txn.number || '-')}"
                 data-view-url="${escapeHtml(txn.actions?.view || '')}"
                 data-delete-url="${escapeHtml(txn.actions?.delete || '')}"
                 data-cancel-url="${escapeHtml(txn.actions?.cancel || '')}"
                 data-duplicate-url="${escapeHtml(txn.actions?.duplicate || '')}"
                 data-pdf-url="${escapeHtml(txn.actions?.pdf || '')}"
                 data-preview-url="${escapeHtml(txn.actions?.preview || '')}"
                 data-print-url="${escapeHtml(txn.actions?.print || '')}"
                 data-preview-delivery-url="${escapeHtml(txn.actions?.preview_delivery || '')}"
                 data-convert-return-url="${escapeHtml(txn.actions?.convert_return || '')}"
                 data-history-url="${escapeHtml(txn.actions?.history || '')}">
              <button class="btn btn-sm party-txn-action-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-ellipsis-vertical"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="#" data-action="view">View / Edit</a></li>
                <li><a class="dropdown-item" href="#" data-action="delete">Delete</a></li>
                <li><a class="dropdown-item" href="#" data-action="cancel">Cancel</a></li>
                <li><a class="dropdown-item" href="#" data-action="duplicate">Duplicate</a></li>
                <li><a class="dropdown-item" href="#" data-action="pdf">Open PDF</a></li>
                <li><a class="dropdown-item" href="#" data-action="preview">Preview</a></li>
                <li><a class="dropdown-item" href="#" data-action="print">Print</a></li>
                <li><a class="dropdown-item" href="#" data-action="preview-delivery">Preview Delivery Challan</a></li>
                <li><a class="dropdown-item" href="#" data-action="convert-return">Convert to Return</a></li>
                <li><a class="dropdown-item" href="#" data-action="history">View History</a></li>
              </ul>
            </div>
        `;
    }

    function showTxnMessage(iconClass, title, subtitle) {
        const tbody = document.getElementById("txnTableBody");
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center" style="padding: 40px;">
                    <i class="${iconClass}" style="font-size: 40px; color: #d1d5db;"></i>
                    <p class="mt-2" style="color: #6b7280;">${title}</p>
                    <p style="font-size: 12px; color: #9ca3af;">${subtitle}</p>
                </td>
            </tr>
        `;
    }

    function renderTransactionsTable(transactions) {
        const tbody = document.getElementById("txnTableBody");
        filteredTransactionsState = [...transactions];

        if (!transactions.length) {
            const hasSearch = txnSearchInput && txnSearchInput.value.trim() !== '';
            showTxnMessage(
                'fa-solid fa-receipt',
                hasSearch ? 'No matching transactions' : 'No transactions yet',
                hasSearch ? 'Try a different search keyword' : 'Create a sale or purchase for this party'
            );
            return;
        }

        tbody.innerHTML = '';

        transactions.forEach(txn => {
            const row = document.createElement('tr');
            const typeText = formatTxnType(txn.type);
            const normalizedStatusText = (txn.status || '').toLowerCase();

            const cleanStatusBadge = normalizedStatusText === 'receive'
                ? `<span style="display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;background:#ecfdf5;color:#15803d;font-size:12px;font-weight:600;">To Receive</span>`
                : normalizedStatusText === 'pay'
                    ? `<span style="display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;background:#fef2f2;color:#dc2626;font-size:12px;font-weight:600;">To Pay</span>`
                    : ['paid', 'completed', 'closed', 'converted'].includes(normalizedStatusText)
                        ? `<span style="display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;background:#ecfdf5;color:#15803d;font-size:12px;font-weight:600;">Paid</span>`
                        : ['partial', 'pending', 'confirmed'].includes(normalizedStatusText)
                            ? `<span style="display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;background:#fff7ed;color:#d97706;font-size:12px;font-weight:600;text-transform:capitalize;">${txn.status}</span>`
                            : `<span style="display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;background:#eff6ff;color:#2563eb;font-size:12px;font-weight:600;text-transform:capitalize;">${txn.status || 'Open'}</span>`;

            const cleanTypeColors = {
                'Receivable Opening Balance': { bg: '#f8fafc', color: '#475569' },
                'Payable Opening Balance': { bg: '#f8fafc', color: '#475569' },
                'Sale': { bg: '#eff6ff', color: '#2563eb' },
                'Purchase': { bg: '#fffbeb', color: '#d97706' },
                'Estimate': { bg: '#fff7ed', color: '#ea580c' },
                'Sale Order': { bg: '#ecfeff', color: '#0891b2' },
                'Proforma Invoice': { bg: '#f5f3ff', color: '#7c3aed' },
                'Delivery Challan': { bg: '#ecfdf5', color: '#15803d' },
                'Credit Note': { bg: '#fef2f2', color: '#dc2626' },
                'POS': { bg: '#fdf2f8', color: '#be185d' },
            };
            const cleanTypeStyle = cleanTypeColors[typeText] || { bg: '#f8fafc', color: '#334155' };
            const cleanTypeBadge = `<span style="display:inline-flex;align-items:center;padding:7px 12px;border-radius:999px;background:${cleanTypeStyle.bg};color:${cleanTypeStyle.color};font-size:12px;font-weight:600;white-space:nowrap;">${typeText}</span>`;
            const cleanBalanceColor = normalizedStatusText === 'receive' ? '#16a34a' : normalizedStatusText === 'pay' ? '#dc2626' : '#475569';

            row.innerHTML = `
                <td style="background:#fff;color:#334155;font-size:14px;padding:14px 16px;border-bottom:1px solid #eef2f7;">${cleanTypeBadge}</td>
                <td style="background:#fff;color:#64748b;font-size:14px;padding:14px 16px;border-bottom:1px solid #eef2f7;">${txn.number || '-'}</td>
                <td style="background:#fff;color:#64748b;font-size:14px;padding:14px 16px;border-bottom:1px solid #eef2f7;">${txn.date}</td>
                <td style="background:#fff;color:#475569;font-size:14px;padding:14px 16px;border-bottom:1px solid #eef2f7;font-weight:500;">Rs ${txn.total}</td>
                <td style="background:#fff;color:${cleanBalanceColor};font-size:14px;padding:14px 16px;border-bottom:1px solid #eef2f7;font-weight:600;">Rs ${txn.balance}</td>
                <td style="background:#fff;padding:14px 16px;border-bottom:1px solid #eef2f7;">${cleanStatusBadge}</td>
                <td style="background:#fff;padding:14px 16px;border-bottom:1px solid #eef2f7;">${getPartyTxnActionsHtml(txn)}</td>
            `;

            tbody.appendChild(row);
        });
    }

    function applyTransactionSearch() {
        const keyword = txnSearchInput ? txnSearchInput.value.trim().toLowerCase() : '';

        if (!keyword) {
            renderTransactionsTable(transactionsState);
            return;
        }

        const filteredRows = transactionsState.filter(txn => {
            const values = [
                formatTxnType(txn.type),
                txn.number,
                txn.date,
                txn.total,
                txn.balance,
                formatTxnStatus(txn.status)
            ];

            return values.some(value => String(value ?? '').toLowerCase().includes(keyword));
        });

        renderTransactionsTable(filteredRows);
    }

    function toggleTransactionSearch() {
        if (!txnToolbar || !txnSearchInput) return;

        const shouldShow = txnToolbar.style.display === 'none' || txnToolbar.style.display === '';
        txnToolbar.style.display = shouldShow ? 'flex' : 'none';

        if (shouldShow) {
            txnSearchInput.focus();
        } else {
            txnSearchInput.value = '';
            applyTransactionSearch();
        }
    }

    function openTxnOptionModal(actionType) {
        pendingTxnAction = actionType;
        txnOptionTitle.textContent = actionType === 'print' ? 'Print Options' : 'Show Options';
        txnOptionModal.classList.add('active');
    }

    function closeTxnOptionModal() {
        pendingTxnAction = null;
        txnOptionModal.classList.remove('active');
    }

    function getSelectedExportColumns() {
        const selected = Array.from(document.querySelectorAll('.txn-export-column:checked'))
            .map(input => input.value);

        return exportColumns.filter(column => selected.includes(column.key));
    }

    function exportTransactionsToExcel(columns, rows) {
        if (!rows.length) {
            alert('No transactions available for Excel export.');
            return;
        }

        const csvLines = [
            columns.map(column => `"${column.label.replace(/"/g, '""')}"`).join(',')
        ];

        rows.forEach(row => {
            csvLines.push(
                columns
                    .map(column => `"${String(row[column.key] ?? '').replace(/"/g, '""')}"`)
                    .join(',')
            );
        });

        const blob = new Blob(["\uFEFF" + csvLines.join('\n')], { type: 'text/csv;charset=utf-8;' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.download = `party-transactions-${currentPartyId || 'export'}.csv`;
        document.body.appendChild(link);
        link.click();
        link.remove();
        URL.revokeObjectURL(url);
    }

    function printTransactions(columns, rows) {
        if (!rows.length) {
            alert('No transactions available to print.');
            return;
        }

        const partyName = document.getElementById("partyDetailName")?.textContent?.trim() || 'Party Transactions';
        const tableHead = columns.map(column => `<th>${escapeHtml(column.label)}</th>`).join('');
        const tableRows = rows.map(row => `
            <tr>
                ${columns.map(column => `<td>${escapeHtml(row[column.key])}</td>`).join('')}
            </tr>
        `).join('');

        const printWindow = window.open('', '_blank', 'width=900,height=700');
        if (!printWindow) {
            alert('Please allow popups to print transactions.');
            return;
        }

        printWindow.document.write(`
            <html>
                <head>
                    <title>${escapeHtml(partyName)} - Transactions</title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 24px; color: #1f2937; }
                        h2 { margin: 0 0 6px; }
                        p { margin: 0 0 18px; color: #6b7280; }
                        table { width: 100%; border-collapse: collapse; }
                        th, td { border: 1px solid #d1d5db; padding: 10px 12px; text-align: left; font-size: 13px; }
                        th { background: #f8fafc; }
                    </style>
                </head>
                <body>
                    <h2>${escapeHtml(partyName)}</h2>
                    <p>Transactions Print Preview</p>
                    <table>
                        <thead>
                            <tr>${tableHead}</tr>
                        </thead>
                        <tbody>${tableRows}</tbody>
                    </table>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }

    function runPendingTxnAction() {
        const columns = getSelectedExportColumns();
        const rows = getTransactionExportRows();

        if (!columns.length) {
            alert('Please select at least one column.');
            return;
        }

        if (pendingTxnAction === 'print') {
            printTransactions(columns, rows);
        } else if (pendingTxnAction === 'excel') {
            exportTransactionsToExcel(columns, rows);
        }

        closeTxnOptionModal();
    }

    function renderPartyGroupOptions(selectedValue = '') {
        if (!partyGroupOptions || !partyGroupInput || !partyGroupTriggerText) return;

        partyGroupOptions.innerHTML = '';
        partyGroupInput.value = selectedValue || '';
        partyGroupTriggerText.textContent = selectedValue || 'Select party group';

        partyGroups.forEach(group => {
            const option = document.createElement('button');
            option.type = 'button';
            option.className = 'party-group-option';
            option.textContent = group;
            option.addEventListener('click', function () {
                partyGroupInput.value = group;
                partyGroupTriggerText.textContent = group;
                partyGroupMenu.classList.remove('active');
            });
            partyGroupOptions.appendChild(option);
        });
    }

    function persistPartyGroups() {
        localStorage.setItem(PARTY_GROUPS_STORAGE_KEY, JSON.stringify(partyGroups));
    }

    function hydratePartyGroups() {
        try {
            const savedGroups = JSON.parse(localStorage.getItem(PARTY_GROUPS_STORAGE_KEY) || '[]');
            if (Array.isArray(savedGroups) && savedGroups.length) {
                partyGroups = Array.from(new Set(['General', ...partyGroups, ...savedGroups.map((group) => String(group).trim()).filter(Boolean)]));
            }
        } catch (error) {
            console.warn('Unable to load saved party groups.', error);
        }
    }

    function openPartyGroupCreateModal() {
        partyGroupMenu?.classList.remove('active');
        partyGroupNameInput.value = '';
        partyGroupModal.classList.add('active');
        setTimeout(() => {
            partyGroupNameInput.focus();
            partyGroupNameInput.click();
        }, 0);
    }

    function closePartyGroupCreateModal() {
        partyGroupModal.classList.remove('active');
    }

    function savePartyGroupLocally() {
        const groupName = partyGroupNameInput.value.trim();
        if (!groupName) {
            alert('Enter party group name.');
            return;
        }

        if (!partyGroups.includes(groupName)) {
            partyGroups.push(groupName);
        }

        persistPartyGroups();
        renderPartyGroupOptions(groupName);
        closePartyGroupCreateModal();
        partyGroupMenu.classList.remove('active');
    }

    function syncCreditLimitVisibility() {
        const isEnabled = document.getElementById("creditLimitSwitch").checked;
        creditLimitAmountWrap.classList.toggle('is-hidden', !isEnabled);
        creditLimitAmountInput.disabled = !isEnabled;
        if (!isEnabled) {
            creditLimitAmountInput.value = '';
        }
    }

    function getDisplayBalanceValue(partyData, fallbackBalance = 0) {
        return parseFloat(fallbackBalance ?? partyData.current_balance ?? partyData.opening_balance ?? 0).toFixed(2);
    }

    function updatePartySidebarBalance(partyId, balanceValue) {
        const sidebarParty = document.querySelector(`.party-item[data-id="${partyId}"]`);
        if (!sidebarParty) return;

        const numericBalance = parseFloat(String(balanceValue ?? 0).replace(/,/g, ''));
        const normalizedBalance = Number.isFinite(numericBalance) ? numericBalance.toFixed(2) : '0.00';
        sidebarParty.dataset.currentBalance = normalizedBalance;

        const balanceEl = sidebarParty.querySelector(".entity-balance");
        if (!balanceEl) return;

        balanceEl.textContent = `Rs ${normalizedBalance}`;
        balanceEl.classList.remove('positive', 'negative');
        balanceEl.classList.add(parseFloat(normalizedBalance) < 0 ? 'negative' : 'positive');
    }

    function applyPartySettings() {
        document.querySelectorAll('[data-party-setting="party_grouping"]').forEach(section => {
            section.classList.toggle('is-hidden', !partySettingsState.party_grouping);
            section.querySelectorAll('input, textarea, select, button').forEach(field => {
                if (field.type !== 'hidden') field.disabled = !partySettingsState.party_grouping;
            });
        });

        document.querySelectorAll('[data-party-setting="shipping_address"]').forEach(section => {
            section.classList.toggle('is-hidden', !partySettingsState.shipping_address);
            section.querySelectorAll('input, textarea, select').forEach(field => {
                if (field.type !== 'hidden') field.disabled = !partySettingsState.shipping_address;
            });
        });

        const additionalPane = document.querySelector('[data-party-setting="additional_fields"]');
        if (additionalPane) {
            const shouldShowAdditionalPane = partySettingsState.additional_field_1 || partySettingsState.additional_field_2;
            additionalPane.classList.toggle('is-hidden', !shouldShowAdditionalPane);
        }

        const additionalFieldCheckboxes = document.querySelectorAll('#partyAdditionalPane .form-check-input[type="checkbox"]');
        const additionalFieldInputs = document.querySelectorAll('#partyAdditionalPane input[type="text"]');

        [partySettingsState.additional_field_1, partySettingsState.additional_field_2].forEach((enabled, index) => {
            const fieldWrap = additionalFieldInputs[index]?.closest('.col-md-6');
            if (fieldWrap) fieldWrap.classList.toggle('is-hidden', !enabled);
            if (additionalFieldCheckboxes[index]) additionalFieldCheckboxes[index].disabled = !enabled;
            if (additionalFieldInputs[index]) {
                additionalFieldInputs[index].disabled = !enabled;
            }
        });

        if (additionalFieldInputs[0]) {
            additionalFieldInputs[0].placeholder = partyAdditionalField1Name?.value || 'Field name';
        }

        if (additionalFieldInputs[1]) {
            additionalFieldInputs[1].placeholder = partyAdditionalField2Name?.value || 'Field name';
        }

        if (partyReminderDays) {
            partyReminderDays.disabled = !partySettingsState.payment_reminder;
        }
    }

    function openPartySettingsDrawer() {
        partySettingsDrawer.classList.add('active');
    }

    function closePartySettingsDrawer() {
        partySettingsDrawer.classList.remove('active');
    }

    function togglePartyMoreMenu() {
        partyMoreMenu.classList.toggle('active');
    }

    function closePartyMoreMenu() {
        partyMoreMenu.classList.remove('active');
    }

    function openPartyQrModal() {
        const partyName = document.getElementById("partyDetailName")?.textContent?.trim();
        const partyPhone = document.getElementById("partyPhone")?.textContent?.trim();
        const partyEmail = document.getElementById("partyEmail")?.textContent?.trim();

        if (!partyName) {
            alert('Select a party first to generate QR code.');
            return;
        }

        const qrPayload = `Party: ${partyName}\nPhone: ${partyPhone || '-'}\nEmail: ${partyEmail || '-'}`;
        partyQrImage.src = `https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=${encodeURIComponent(qrPayload)}`;
        partyQrText.textContent = qrPayload.replace(/\n/g, ' | ');
        partyQrModal.classList.add('active');
    }

    function closePartyQrModal() {
        partyQrModal.classList.remove('active');
    }

    function importContactFile(file) {
        const reader = new FileReader();
        reader.onload = function (event) {
            const content = String(event.target?.result || '');
            const fullName = content.match(/FN:(.*)/)?.[1]?.trim() || file.name.replace(/\.[^.]+$/, '');
            const phone = content.match(/TEL[^:]*:(.*)/)?.[1]?.trim() || '';
            const email = content.match(/EMAIL[^:]*:(.*)/)?.[1]?.trim() || '';

            resetModal();
            document.getElementById("partyNameInput").value = fullName;
            document.getElementById("partyPhoneInput").value = phone;
            document.getElementById("partyPtclInput").value = '';
            const emailInput = document.querySelector('#partyAddressPane input[type="email"]');
            if (emailInput) emailInput.value = email;
            addModal.show();
        };
        reader.readAsText(file);
    }

    function importExcelFile(file) {
        const reader = new FileReader();
        reader.onload = function (event) {
            const content = String(event.target?.result || '');
            const rows = content.split(/\r?\n/).filter(Boolean);

            if (rows.length < 2) {
                alert('Excel import ke liye CSV file me header aur kam az kam ek row honi chahiye.');
                return;
            }

            const headers = rows[0].split(',').map(value => value.trim().toLowerCase());
            const values = rows[1].split(',').map(value => value.trim());
            const rowData = headers.reduce((acc, header, index) => {
                acc[header] = values[index] || '';
                return acc;
            }, {});

            resetModal();
            document.getElementById("partyNameInput").value = rowData.name || rowData.party || '';
            document.getElementById("partyPhoneInput").value = rowData.phone || rowData.mobile || '';
            document.getElementById("partyPhone2Input").value = rowData.phone_number_2 || rowData.phone2 || '';
            document.getElementById("partyPtclInput").value = rowData.ptcl_number || '';
            document.getElementById("partyCityInput").value = rowData.city || '';
            const emailInput = document.querySelector('#partyAddressPane input[type="email"]');
            if (emailInput) emailInput.value = rowData.email || '';
            document.getElementById("partyAddressInput").value = rowData.address || '';
            document.getElementById("billingAddress").value = rowData.billing_address || rowData.address || '';
            document.getElementById("shippingAddress").value = rowData.shipping_address || '';
            addModal.show();
        };
        reader.readAsText(file);
    }

    // ADD PARTY
    function addParty(closeModal = true) {
        const partyData = getPartyData();

        fetch("{{ route('parties.store') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify(partyData)
        })
        .then(async res => {
            const data = await res.json();
            if (!res.ok) {
                throw new Error(data.message || "Unable to save party.");
            }
            return data;
        })
        .then(data => {
            if (data.success) {
                const party = data.party;
                const li = document.createElement("li");
                li.className = "party-item";
                li.dataset.id = party.id;
                li.dataset.name = party.name;
                li.dataset.phone = party.phone || "";
                li.dataset.phoneNumber2 = party.phone_number_2 || "";
                li.dataset.ptclNumber = party.ptcl_number || "";
                li.dataset.partyGroup = partyData.party_group || "";
                li.dataset.email = party.email || "";
                li.dataset.city = party.city || "";
                li.dataset.address = party.address || "";
                li.dataset.billingAddress = party.billing_address || "";
                li.dataset.shippingAddress = party.shipping_address || "";
                li.dataset.openingBalance = party.opening_balance || 0;
                li.dataset.currentBalance = getDisplayBalanceValue(partyData, party.current_balance || 0);
                li.dataset.asOfDate = party.as_of_date || "";
                li.dataset.transactionType = party.transaction_type || "";
                li.dataset.partyType = Array.isArray(partyData.party_type) ? partyData.party_type.join(',') : (party.party_type || "");
                li.dataset.creditLimitEnabled = party.credit_limit_enabled || 0;
                li.dataset.creditLimitAmount = partyData.credit_limit_amount || "";
                li.dataset.customFields = JSON.stringify(party.custom_fields || []);

                li.innerHTML = `
                    <span class="entity-name">${party.name}</span>
                    <span class="entity-balance">Rs ${getDisplayBalanceValue(partyData, party.current_balance || 0)}</span>
                `;

                partyList.prepend(li);

                if (closeModal) {
                    addModal.hide();
                    resetModal();
                } else {
                    document.getElementById("addPartyForm").reset();
                }

                alert("Party created successfully!");
            }
        })
        .catch(err => {
            console.error("Add Party Error:", err);
            alert(err.message || "Unable to save party.");
        });
    }

    saveBtn.addEventListener("click", () => addParty(true));
    saveNewBtn.addEventListener("click", () => addParty(false));
    openLedgerModalBtn?.addEventListener("click", openLedgerModal);
    openTransferHistoryModalBtn?.addEventListener("click", openTransferHistoryModal);
    txnSearchToggle.addEventListener("click", toggleTransactionSearch);
    txnSearchInput.addEventListener("input", applyTransactionSearch);
    txnPrintTrigger.addEventListener("click", () => openTxnOptionModal('print'));
    txnExcelTrigger.addEventListener("click", () => openTxnOptionModal('excel'));
    txnOptionCancel.addEventListener("click", closeTxnOptionModal);
    txnOptionConfirm.addEventListener("click", runPendingTxnAction);
    txnOptionModal.addEventListener("click", function (e) {
        if (e.target.dataset.close === 'true') {
            closeTxnOptionModal();
        }
    });
    partyGroupTrigger?.addEventListener("click", function (e) {
        e.stopPropagation();
        partyGroupMenu.classList.toggle('active');
    });
    openPartyGroupModal?.addEventListener("click", openPartyGroupCreateModal);
    partyGroupCancel?.addEventListener("click", closePartyGroupCreateModal);
    partyGroupSave?.addEventListener("click", savePartyGroupLocally);
    partyGroupModal?.addEventListener("click", function (e) {
        if (e.target.dataset.closePartyGroup === 'true') {
            closePartyGroupCreateModal();
        }
    });
    partySettingsTrigger.addEventListener("click", openPartySettingsDrawer);
    partyModalSettingsTrigger.addEventListener("click", openPartySettingsDrawer);
    partySettingsClose.addEventListener("click", closePartySettingsDrawer);
    partySettingsDrawer.addEventListener("click", function (e) {
        if (e.target.dataset.closePartySettings === 'true') {
            closePartySettingsDrawer();
        }
    });
    document.querySelectorAll('.party-setting-toggle').forEach(toggle => {
        toggle.addEventListener('change', function () {
            partySettingsState[this.dataset.settingTarget] = this.checked;
            applyPartySettings();
        });
    });
    partyAdditionalField1Name?.addEventListener('input', applyPartySettings);
    partyAdditionalField2Name?.addEventListener('input', applyPartySettings);
    partyMoreOptionsTrigger.addEventListener("click", function (e) {
        e.stopPropagation();
        togglePartyMoreMenu();
    });
    importExcelOption.addEventListener("click", function () {
        closePartyMoreMenu();
        partyExcelImportInput.click();
    });
    importContactsOption.addEventListener("click", function () {
        closePartyMoreMenu();
        partyContactsImportInput.click();
    });
    generateQrOption.addEventListener("click", function () {
        closePartyMoreMenu();
        openPartyQrModal();
    });
    partyExcelImportInput.addEventListener("change", function () {
        const file = this.files?.[0];
        if (file) {
            importExcelFile(file);
        }
        this.value = '';
    });
    partyContactsImportInput.addEventListener("change", function () {
        const file = this.files?.[0];
        if (file) {
            importContactFile(file);
        }
        this.value = '';
    });
    partyQrClose.addEventListener("click", closePartyQrModal);
    partyQrModal.addEventListener("click", function (e) {
        if (e.target.dataset.closePartyQr === 'true') {
            closePartyQrModal();
        }
    });
    document.addEventListener("click", function (e) {
        if (!document.getElementById("partyGroupDropdown")?.contains(e.target)) {
            partyGroupMenu?.classList.remove('active');
        }
        if (!partyMoreMenu.contains(e.target) && !partyMoreOptionsTrigger.contains(e.target)) {
            closePartyMoreMenu();
        }
    });
    document.addEventListener('click', async function (e) {
        const actionItem = e.target.closest('.party-txn-action-menu .dropdown-item');
        if (!actionItem) return;

        e.preventDefault();

        const menu = actionItem.closest('.party-txn-action-menu');
        const action = actionItem.dataset.action;
        const txnNumber = menu.dataset.number || 'Transaction';
        const viewUrl = menu.dataset.viewUrl;
        const deleteUrl = menu.dataset.deleteUrl;
        const cancelUrl = menu.dataset.cancelUrl;
        const duplicateUrl = menu.dataset.duplicateUrl;
        const pdfUrl = menu.dataset.pdfUrl;
        const previewUrl = menu.dataset.previewUrl;
        const printUrl = menu.dataset.printUrl;
        const previewDeliveryUrl = menu.dataset.previewDeliveryUrl;
        const convertReturnUrl = menu.dataset.convertReturnUrl;
        const historyUrl = menu.dataset.historyUrl;

        if (action === 'view') {
            if (!viewUrl) return alert('View/Edit is not available for this transaction.');
            window.location.href = viewUrl;
            return;
        }

        if (action === 'delete') {
            if (!deleteUrl) return alert('Delete is not available for this transaction.');
            if (!confirm('Are you sure you want to delete this transaction?')) return;

            try {
                const data = await fetchJson(deleteUrl, { method: 'DELETE' });
                alert(data.message || 'Transaction deleted successfully.');
                if (currentPartyId) loadPartyTransactions(currentPartyId);
            } catch (error) {
                alert(error.message || 'Unable to delete transaction.');
            }
            return;
        }

        if (action === 'cancel') {
            if (!cancelUrl) return alert('Cancel is not available for this transaction.');
            if (!confirm('Are you sure you want to cancel this transaction?')) return;

            try {
                const data = await fetchJson(cancelUrl, { method: 'POST' });
                alert(data.message || 'Transaction cancelled successfully.');
                if (currentPartyId) loadPartyTransactions(currentPartyId);
            } catch (error) {
                alert(error.message || 'Unable to cancel transaction.');
            }
            return;
        }

        if (action === 'duplicate') {
            if (!duplicateUrl) return alert('Duplicate is not available for this transaction.');
            window.location.href = duplicateUrl;
            return;
        }

        if (action === 'pdf') {
            if (!pdfUrl) return alert('PDF is not available for this transaction.');
            window.open(pdfUrl, '_blank');
            return;
        }

        if (action === 'preview') {
            return openPartyTxnPreview(previewUrl, `Preview - ${txnNumber}`);
        }

        if (action === 'print') {
            if (!printUrl) return alert('Print is not available for this transaction.');
            window.open(printUrl, '_blank');
            return;
        }

        if (action === 'preview-delivery') {
            return openPartyTxnPreview(previewDeliveryUrl, `Delivery Challan - ${txnNumber}`);
        }

        if (action === 'convert-return') {
            if (!convertReturnUrl) return alert('Convert to return is not available for this transaction.');
            window.location.href = convertReturnUrl;
            return;
        }

        if (action === 'history') {
            if (!historyUrl) return alert('View history is not available for this transaction.');
            try {
                const data = await fetchJson(historyUrl);
                openPartyTxnHistory(`History - ${txnNumber}`, data.entries || data.history || data.transactions || data.bank_history || []);
            } catch (error) {
                alert(error.message || 'Unable to load history.');
            }
        }
    });
    hydratePartyGroups();
    renderPartyGroupOptions();
    applyPartySettings();

    // PARTY CLICK → RIGHT PANEL + SELECT
    partyList.addEventListener("click", function (e) {
        const li = e.target.closest(".party-item");
        if (!li) return;

        // Remove active from all, add to clicked
        document.querySelectorAll('.party-item').forEach(item => item.classList.remove('active'));
        li.classList.add('active');

        currentPartyId = li.dataset.id;

        console.log("✅ Party Selected - ID:", currentPartyId);

        document.getElementById("partyDetailName").textContent = li.dataset.name || '';
        document.getElementById("partyPhone").textContent = [li.dataset.phone, li.dataset.phoneNumber2].filter(Boolean).join(' / ');
        document.getElementById("partyEmail").textContent = li.dataset.email || '';
        document.getElementById("partyAddress").textContent = li.dataset.billingAddress || '';
        document.getElementById("partyCityPtcl").textContent = `${li.dataset.city || '-'} / ${li.dataset.ptclNumber || '-'}`;
    });

    // OPEN ADD MODAL
    document.querySelector(".btn-add-entity").addEventListener("click", function () {
        resetModal();
        addModal.show();
    });

    // POPULATE MODAL FOR EDITING
   function populatePartyModal(party) {
    currentPartyId = party.id;

    document.getElementById("partyNameInput").value = party.name || '';
    document.getElementById("partyPhoneInput").value = party.phone || '';
    document.getElementById("partyPhone2Input").value = party.phone_number_2 || '';
    renderPartyGroupOptions(party.party_group || '');
    document.querySelector('#partyAddressPane input[type="email"]').value = party.email || '';
    document.getElementById("partyCityInput").value = party.city || '';
    document.getElementById("partyPtclInput").value = party.ptcl_number || '';
    document.getElementById("partyAddressInput").value = party.address || '';
    document.getElementById("billingAddress").value = party.billing_address || '';
    document.getElementById("shippingAddress").value = party.shipping_address || '';
    document.getElementById("partyDueDaysInput").value = party.due_days || '';
    document.querySelector('#partyCreditPane input[type="number"]').value = party.opening_balance || 0;

    // ✅ FIX: Date ko properly format karein
    let dateValue = party.as_of_date || '';
    if (dateValue && dateValue.includes('T')) {
        dateValue = dateValue.split('T')[0]; // "2025-01-15T00:00:00Z" → "2025-01-15"
    }
    document.querySelector('#partyCreditPane input[type="date"]').value = dateValue;
    if (creditLimitAmountInput) {
        creditLimitAmountInput.value = party.credit_limit_amount || '';
    }

    // ✅ FIX: Credit limit switch - string "1" / "true" dono handle karein
    const creditSwitch = document.getElementById("creditLimitSwitch");
    creditSwitch.checked = (party.credit_limit_enabled == 1 || party.credit_limit_enabled === 'true' || party.credit_limit_enabled === true);
    syncCreditLimitVisibility();
    creditSwitch.disabled = false; // ✅ Make sure it's NOT disabled

    // Transaction type
    if (party.transaction_type === 'receive') {
       statusBadge = `<span class="badge">Receivable Opening Balance</span>`;
        toReceive.checked = true;
        toPay.checked = false;
    } else if (party.transaction_type === 'pay') {
      statusBadge = `<span class="badge">Payable Opening Balance</span>`;
        toReceive.checked = false;
        toPay.checked = true;
    } else {
        toReceive.checked = false;
        toPay.checked = false;
    }

    document.querySelectorAll('input[name="party_type[]"]').forEach((checkbox) => {
        checkbox.checked = false;
    });

    const selectedPartyTypes = Array.isArray(party.party_type)
        ? party.party_type
        : String(party.party_type || '')
            .split(',')
            .map((value) => value.trim())
            .filter(Boolean);

    selectedPartyTypes.forEach((value) => {
        const checkbox = document.querySelector(`input[name="party_type[]"][value="${value}"]`);
        if (checkbox) checkbox.checked = true;
    });

    // ✅ FIX: Additional fields / Custom fields
    const customFieldInputs = document.querySelectorAll('#partyAdditionalPane input[type="text"]');
    const customFieldChecks = document.querySelectorAll('#partyAdditionalPane input[type="checkbox"]');

    if (party.custom_fields && Array.isArray(party.custom_fields)) {
        party.custom_fields.forEach((field, index) => {
            if (customFieldInputs[index]) {
                customFieldInputs[index].value = field || '';
            }
            if (customFieldChecks[index]) {
                customFieldChecks[index].checked = field ? true : false;
            }
        });
    }

    saveBtn.style.display = "none";
    saveNewBtn.style.display = "none";
    updateBtn.style.display = "inline-block";
    deleteBtn.style.display = "inline-block";

    addModal.show();
}
    // EDIT PARTY BUTTON
    document.getElementById("editPartyBtn").addEventListener("click", function () {
        if (!currentPartyId) return alert("Select Party First");

        const li = document.querySelector(`.party-item[data-id='${currentPartyId}']`);
        if (!li) return alert("Party nahi mili!");

        console.log("🔍 Edit - All dataset:", JSON.stringify({...li.dataset}));

        const party = {
            id: li.dataset.id,
            name: li.dataset.name,
            phone: li.dataset.phone,
            phone_number_2: li.dataset.phoneNumber2,
            ptcl_number: li.dataset.ptclNumber,
            party_group: li.dataset.partyGroup,
            email: li.dataset.email,
            city: li.dataset.city,
            address: li.dataset.address,
            billing_address: li.dataset.billingAddress,
            shipping_address: li.dataset.shippingAddress,
            opening_balance: li.dataset.openingBalance,
            as_of_date: li.dataset.asOfDate,
            party_type: li.dataset.partyType,
            credit_limit_enabled: li.dataset.creditLimitEnabled,
            credit_limit_amount: li.dataset.creditLimitAmount,
            transaction_type: li.dataset.transactionType || '',
             custom_fields: li.dataset.customFields ? JSON.parse(li.dataset.customFields) : []  // ✅ ADD
        };

        populatePartyModal(party);
    });

    // ✅ UPDATE PARTY
    updateBtn.addEventListener("click", function (e) {
        e.preventDefault();
        console.log("🔄 Update clicked! currentPartyId:", currentPartyId);

        if (!currentPartyId) {
            alert("No party selected!");
            return;
        }

        const partyData = getPartyData();
        console.log("📤 Sending data:", partyData);
fetch(`/dashboard/parties/${currentPartyId}`, {
    method: "PUT",
            credentials: "same-origin",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest"
            },
            body: JSON.stringify(partyData)
        })
        .then(async res => {
            console.log("📥 Response status:", res.status);
            const text = await res.text();
            let data = null;

            try {
                data = JSON.parse(text);
            } catch (error) {
                console.error("Update non-JSON response:", text);
                throw new Error(text.includes("<!DOCTYPE") ? "Server returned HTML error page. Check validation/server error." : text);
            }

            if (!res.ok) {
                throw new Error(data.message || "Update failed");
            }

            return data;
        })
        .then(data => {
            console.log("📥 Response data:", data);

            if (data.success) {
                const li = document.querySelector(`.party-item[data-id="${currentPartyId}"]`);

                li.dataset.name = partyData.name;
                li.dataset.phone = partyData.phone;
                li.dataset.phoneNumber2 = partyData.phone_number_2;
                li.dataset.ptclNumber = partyData.ptcl_number;
                li.dataset.partyGroup = partyData.party_group;
                li.dataset.email = partyData.email;
                li.dataset.city = partyData.city;
                li.dataset.address = partyData.address;
                li.dataset.billingAddress = partyData.billing_address;
                li.dataset.shippingAddress = partyData.shipping_address;
                li.dataset.dueDays = partyData.due_days;
                li.dataset.openingBalance = partyData.opening_balance;
                li.dataset.currentBalance = getDisplayBalanceValue(partyData, data.party.current_balance || partyData.opening_balance || 0);
                li.dataset.asOfDate = partyData.as_of_date;
                li.dataset.partyType = Array.isArray(partyData.party_type) ? partyData.party_type.join(',') : '';
                li.dataset.creditLimitEnabled = partyData.credit_limit_enabled;
                li.dataset.creditLimitAmount = partyData.credit_limit_amount;
                li.dataset.transactionType = partyData.transaction_type;

                li.querySelector(".entity-name").textContent = partyData.name;
                li.querySelector(".entity-balance").textContent = "Rs " + getDisplayBalanceValue(partyData, data.party.current_balance || partyData.opening_balance || 0);

                document.getElementById("partyDetailName").textContent = partyData.name;
                document.getElementById("partyPhone").textContent = [partyData.phone, partyData.phone_number_2].filter(Boolean).join(' / ');
                document.getElementById("partyEmail").textContent = partyData.email;
                document.getElementById("partyAddress").textContent = partyData.billing_address;
                document.getElementById("partyCityPtcl").textContent = `${partyData.city || '-'} / ${partyData.ptcl_number || '-'}`;

                alert("✅ Party updated successfully!");
                addModal.hide();
                resetModal();
            } else {
                alert("❌ Update failed: " + JSON.stringify(data));
            }
        })
        .catch(err => {
            console.error("❌ Update Error:", err);
            alert("❌ Network error: " + err.message);
        });
    });
    // ============ PARTY CLICK → LOAD TRANSACTIONS ============
partyList.addEventListener("click", function (e) {
    const li = e.target.closest(".party-item");
    if (!li) return;

    // Remove active from all, add to clicked
    document.querySelectorAll('.party-item').forEach(item => item.classList.remove('active'));
    li.classList.add('active');

    currentPartyId = li.dataset.id;

    console.log("✅ Party Selected - ID:", currentPartyId);

    // Update right panel header info
    document.getElementById("partyDetailName").textContent = li.dataset.name || '';
    document.getElementById("partyPhone").textContent = [li.dataset.phone, li.dataset.phoneNumber2].filter(Boolean).join(' / ');
    document.getElementById("partyEmail").textContent = li.dataset.email || '';
    document.getElementById("partyAddress").textContent = li.dataset.billingAddress || '';
    document.getElementById("partyCityPtcl").textContent = `${li.dataset.city || '-'} / ${li.dataset.ptclNumber || '-'}`;

    // ✅ LOAD TRANSACTIONS FOR THIS PARTY
    loadPartyTransactions(currentPartyId);
});

// ============ FETCH & RENDER TRANSACTIONS ============
function loadPartyTransactions(partyId) {
    const tbody = document.getElementById("txnTableBody");
    transactionsState = [];
    filteredTransactionsState = [];
    if (txnSearchInput) {
        txnSearchInput.value = '';
    }
    showTxnMessage('fa fa-spinner fa-spin', 'Loading transactions...', 'Please wait while we fetch party transactions');

    fetch(`/dashboard/parties/${partyId}/transactions`)
        .then(res => res.json())
        .then(data => {
          if (data.success) {
                updatePartySidebarBalance(partyId, data.total_balance || 0);
                transactionsState = Array.isArray(data.transactions) ? data.transactions : [];
                renderTransactionsTable(transactionsState);
                return;
          } else {
                showTxnMessage('fa-solid fa-receipt', 'No transactions yet', 'Create a sale or purchase for this party');
                return;
          }
          if (data.success && data.transactions.length > 0) {
    tbody.innerHTML = '';

    data.transactions.forEach(txn => {
        const row = document.createElement('tr');

        // Status badge
        let statusBadge = '';
        const badgeStyleBase = `
            color:#6b7280;
            border-radius:8px;
            font-size:13px;
            font-weight:500;
        `;
        if (txn.status === 'receive') {
            statusBadge = `<span class="badge" style="${badgeStyleBase}">To Receive</span>`;
        } else if (txn.status === 'pay') {
            statusBadge = `<span class="badge" style="${badgeStyleBase}">To Pay</span>`;
        } else if (['paid', 'completed', 'closed', 'converted'].includes((txn.status || '').toLowerCase())) {
            statusBadge = `<span class="badge" style="color:#2563eb; border-radius:12px; font-size:13px;">Paid</span>`;
        } else if (['partial', 'pending', 'confirmed'].includes((txn.status || '').toLowerCase())) {
            statusBadge = `<span class="badge" style="color:#d97706; border-radius:12px; font-size:13px;">${txn.status}</span>`;
        } else {
            statusBadge = `<span class="badge" style="color:#6b7280; border-radius:12px; font-size:13px;">${txn.status || 'Open'}</span>`;
        }

        // Transaction Type badge
        let typeText = txn.type === 'pay'
            ? 'Payable Opening Balance'
            : txn.type === 'receive'
                ? 'Receivable Opening Balance'
                : txn.type;

        const typeColors = {
            'Receivable Opening Balance': { color: 'gray' },
            'Payable Opening Balance': { color: 'gray' },
            'Sale': { bg: '#dbeafe', color: '#2563eb' },
            'Purchase': { bg: '#fef3c7', color: '#d97706' },
            'Estimate': { bg: '#fef3c7', color: '#d97706' },
            'Sale Order': { bg: '#e0f2fe', color: '#0369a1' },
            'Proforma Invoice': { bg: '#ede9fe', color: '#7c3aed' },
            'Delivery Challan': { bg: '#dcfce7', color: '#15803d' },
            'Credit Note': { bg: '#fee2e2', color: '#dc2626' },
            'POS': { bg: '#fce7f3', color: '#be185d' },
        };
        const typeStyle = typeColors[typeText] || { bg: '#f3f4f6', color: '#374151' };

      typeBadge = `<span style="
    background:${typeStyle.bg};
    color:${typeStyle.color};

    border-radius:12px;
    font-size:13px;
    display:inline-block;
    margin-left:2px;
   padding-top:12px;
    white-space: nowrap; /* prevents wrapping */
"> ${typeText} </span>`;
        // Balance color
        let balanceColor = txn.status === 'receive' ? '#16a34a' : txn.status === 'pay' ? '#dc2626' : '#6b7280';

        // Row HTML with flex inside first <td> to force left alignment
        row.innerHTML = `
            <td style="display:flex; justify-content:flex-start; align-items:center;">${typeBadge}</td>
          <td style="color:#6b7280; font-size:14px;">${txn.number || '-'}</td>
            <td style="color:#6b7280; font-size:14px;">${txn.date}</td>
            <td style="color:#6b7280; font-size:14px;">₹ ${txn.total}</td>
            <td style="color:${balanceColor}; font-size:14px; font-weight:600;">
                ₹ ${txn.balance}
                <br>${statusBadge}
            </td>
        `;

        const normalizedStatusText = (txn.status || '').toLowerCase();
        const cleanStatusBadge = normalizedStatusText === 'receive'
            ? `<span style="display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;background:#ecfdf5;color:#15803d;font-size:12px;font-weight:600;">To Receive</span>`
            : normalizedStatusText === 'pay'
                ? `<span style="display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;background:#fef2f2;color:#dc2626;font-size:12px;font-weight:600;">To Pay</span>`
                : ['paid', 'completed', 'closed', 'converted'].includes(normalizedStatusText)
                    ? `<span style="display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;background:#ecfdf5;color:#15803d;font-size:12px;font-weight:600;">Paid</span>`
                    : ['partial', 'pending', 'confirmed'].includes(normalizedStatusText)
                        ? `<span style="display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;background:#fff7ed;color:#d97706;font-size:12px;font-weight:600;text-transform:capitalize;">${txn.status}</span>`
                        : `<span style="display:inline-flex;align-items:center;padding:6px 12px;border-radius:999px;background:#eff6ff;color:#2563eb;font-size:12px;font-weight:600;text-transform:capitalize;">${txn.status || 'Open'}</span>`;

        const cleanTypeColors = {
            'Receivable Opening Balance': { bg: '#f8fafc', color: '#475569' },
            'Payable Opening Balance': { bg: '#f8fafc', color: '#475569' },
            'Sale': { bg: '#eff6ff', color: '#2563eb' },
            'Purchase': { bg: '#fffbeb', color: '#d97706' },
            'Estimate': { bg: '#fff7ed', color: '#ea580c' },
            'Sale Order': { bg: '#ecfeff', color: '#0891b2' },
            'Proforma Invoice': { bg: '#f5f3ff', color: '#7c3aed' },
            'Delivery Challan': { bg: '#ecfdf5', color: '#15803d' },
            'Credit Note': { bg: '#fef2f2', color: '#dc2626' },
            'POS': { bg: '#fdf2f8', color: '#be185d' },
        };
        const cleanTypeStyle = cleanTypeColors[typeText] || { bg: '#f8fafc', color: '#334155' };
        const cleanTypeBadge = `<span style="display:inline-flex;align-items:center;padding:7px 12px;border-radius:999px;background:${cleanTypeStyle.bg};color:${cleanTypeStyle.color};font-size:12px;font-weight:600;white-space:nowrap;">${typeText}</span>`;
        const cleanBalanceColor = normalizedStatusText === 'receive' ? '#16a34a' : normalizedStatusText === 'pay' ? '#dc2626' : '#475569';

        row.innerHTML = `
            <td style="background:#fff;color:#334155;font-size:14px;padding:14px 16px;border-bottom:1px solid #eef2f7;">${cleanTypeBadge}</td>
            <td style="background:#fff;color:#64748b;font-size:14px;padding:14px 16px;border-bottom:1px solid #eef2f7;">${txn.number || '-'}</td>
            <td style="background:#fff;color:#64748b;font-size:14px;padding:14px 16px;border-bottom:1px solid #eef2f7;">${txn.date}</td>
            <td style="background:#fff;color:#475569;font-size:14px;padding:14px 16px;border-bottom:1px solid #eef2f7;font-weight:500;">Rs ${txn.total}</td>
            <td style="background:#fff;color:${cleanBalanceColor};font-size:14px;padding:14px 16px;border-bottom:1px solid #eef2f7;font-weight:600;">Rs ${txn.balance}</td>
            <td style="background:#fff;padding:14px 16px;border-bottom:1px solid #eef2f7;">${cleanStatusBadge}</td>
        `;

        tbody.appendChild(row);
    });
} else{                // No transactions
                tbody.innerHTML = `
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 40px;">
                            <i class="fa-solid fa-receipt" style="font-size: 40px; color: #d1d5db;"></i>
                            <p class="mt-2" style="color: #6b7280;">No transactions yet</p>
                            <p style="font-size: 12px; color: #9ca3af;">Create a sale or purchase for this party</p>
                        </td>
                    </tr>
                `;
            }
        })
        .catch(err => {
            console.error("❌ Transaction Load Error:", err);
            showTxnMessage('fa-solid fa-exclamation-triangle', 'Error loading transactions', 'Please try again in a moment');
            return;
            tbody.innerHTML = `
                <tr>
                    <td colspan="5" class="text-center text-danger" style="padding: 30px;">
                        <i class="fa-solid fa-exclamation-triangle" style="font-size: 30px;"></i>
                        <p class="mt-2">Error loading transactions</p>
                    </td>
                </tr>
            `;
        });
}

    // DELETE PARTY
    deleteBtn.addEventListener("click", function () {
        if (!currentPartyId) return;
        if (!confirm("Delete this party?")) return;


      fetch(`/dashboard/parties/${currentPartyId}`, {
    method: "DELETE",
            headers: {
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            }
        })
        .then(res => res.json())
        .then(data => {
          alert("❌Party Deleted Successfully")
            if (data.success) {
                const li = document.querySelector(`.party-item[data-id="${currentPartyId}"]`);
                li.remove();
                document.getElementById("partyDetailName").textContent = "";
                document.getElementById("partyPhone").textContent = "";
                document.getElementById("partyEmail").textContent = "";
                document.getElementById("partyAddress").textContent = "";
                document.getElementById("partyCityPtcl").textContent = "";
                currentPartyId = null;
                addModal.hide();
                resetModal();
            }
        })
        .catch(err => console.error("Delete Error:", err));
    });

});
</script>
@endpush
