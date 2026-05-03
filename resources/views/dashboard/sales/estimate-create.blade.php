<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
    @if(isset($estimate))
        Edit
    @else
        Create
    @endif
    Estimate | Vyapar
</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <!-- Form Styles -->
    <link rel="stylesheet" href="{{ asset('css/estimateform_style.css') }}">
    @include('dashboard.shared.party-item-create-styles')
</head>

<body>

    <div class="container-fluid min-vh-100 d-flex flex-column p-0">
        <!-- Explorer / Tab Bar Area -->
        <header class="tab-system-header">
            <div class="tab-strip-wrapper justify-content-between">
                <div class="d-flex align-items-end flex-grow-1 overflow-hidden">
                    <div id="tab-strip" class="tab-strip d-flex align-items-end">
                        <!-- Tabs will be dynamically inserted here -->
                    </div>
                    <button id="add-tab-btn" class="btn add-tab-btn" title="New Tab">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>

                <div class="window-controls d-flex align-items-center px-2 gap-3">
                    <i id="calc-icon" class="fa-solid fa-calculator" title="Calculator"></i>
                    <i class="fa-solid fa-gear" title="Settings"></i>
                    <i class="fa-solid fa-xmark close-app-icon" title="Close Window"></i>
                </div>
            </div>
            <!-- Browser Toolbar / Heading Area -->
            <div class="browser-toolbar d-flex align-items-center px-3">
                <p class="mt-3 ms-3 mb-0 me-3 mb-2">Estimate  </p>

            </div>
        </header>

        <!-- Content Area -->
        <main id="content-area" class="">
            <!-- Tab contents will be dynamically inserted here
            <button id="global-save-btn" class="btn btn-primary position-absolute bottom-0 end-0 m-4 shadow-lg z-3">
                <i class="bi bi-save me-2"></i>Save
            </button> -->
            <!-- Form Template -->
            <template id="form-template">
                <div class="invoice-container">
                    <div class="invoice-form invoice-card">

                        <!-- Header Section -->
                        <div class="header-section">
                            <div class="header-left">
                                   <div class="input-group">
                                <!-- Party dropdown button -->
<div class="party-dropdown-wrapper" style="position: relative; display: inline-block;">
    <button class="btn btn-outline-secondary dropdown-toggle w-200 text-start" type="button" id="partyDropdownBtn" data-bs-toggle="dropdown" data-bs-auto-close="outside" aria-expanded="false">
        Select Party
    </button>
    <!-- Balance display -->
    <div id="partyBalanceDisplay" style="color: #007bff; font-weight: 600; margin-top: 4px;">
        <!-- JS will populate balance here -->
    </div>

    <!-- Dropdown menu (existing) -->
    <ul class="dropdown-menu w-110" aria-labelledby="partyDropdownBtn" id="partyDropdownMenu">
        <li class="dropdown-header-search px-2 py-2">
            <input type="text" class="form-control form-control-sm party-search-input" placeholder="Search party..." style="font-size: 13px;">
        </li>
        <li class="dropdown-header d-flex justify-content-between px-3">
            <span>Party Name</span>
            <span>Opening Balance</span>
        </li>
          @foreach($parties as $party)
    <li>
        <a class="dropdown-item d-flex justify-content-between party-option" href="#"
           data-id="{{ $party->id }}"
           data-phone="{{ $party->phone }}"
           data-billing="{{ addslashes($party->billing_address ?? '') }}"
           data-opening="{{ $party->opening_balance ?? 0 }}"
           data-type="{{ $party->transaction_type }}">
            <span>{{ $party->name }}</span>
         <span
    @if($party->transaction_type == 'pay')
        class="text-danger"
    @elseif($party->transaction_type == 'receive')
        class="text-success"
    @endif
>
    @if($party->transaction_type == 'pay')
        <i class="fa-solid fa-arrow-up me-1"></i>
    @elseif($party->transaction_type == 'receive')
        <i class="fa-solid fa-arrow-down me-1"></i>
    @endif

    ₹{{ number_format($party->opening_balance ?? 0, 2) }}
</span>
        </a>
    </li>
@endforeach
        <li><hr class="dropdown-divider"></li>
        <li><a class="dropdown-item text-primary" href="#" id="addNewPartyBtn">+ Add New Party</a></li>
    </ul>
</div>
<input type="hidden" class="party-id" name="party_id">

                                </div>


                            </div>

                            <div class="header-right w-25">
                                <div class="input-group">
                                    <span>Estimate No.</span>
                                    <input type="text" class="input-control underline-input bill-number" value="{{ $nextInvoiceNumber ?? 'Auto' }}" readonly>
                                </div>
                                <div class="input-group date-wrapper">
                                    <span>Estimate Date</span>
                                    <input type="date" class="input-control underline-input invoice-date">
                                </div>

                            </div>
                        </div>

                        <div class="alert alert-success d-none sale-success-msg"></div>

                        <!-- Table Section -->
                        <div class="table-container">
                            <table class="item-table">
                                <thead>
                                    <tr>
                                        <th class="row-num">#</th>
                                        <th style="width: 30%;">ITEM</th>
                                        <th class="col-category d-none">CATEGORY</th>
                                        <th class="col-item-code d-none">ITEM CODE</th>
                                        <th class="col-description d-none">DESCRIPTION</th>
                                        <th class="col-discount d-none">DISCOUNT</th>
                                        <th>QTY</th>
                                        <th class="custom-size-th">UNIT</th>
                                        <th>PRICE/UNIT</th>
                                        <th>AMOUNT</th>
                                        <th class="add-col" style="position: relative;">
                                            <button type="button" class="btn-add-circle table-settings-btn"><i
                                                    class="fa-solid fa-plus"></i></button>
                                            <!-- Settings Box -->
                                            <div class="settings-box">
                                                <div class="settings-item">
                                                    <input type="checkbox" class="check-category">
                                                    <label>Item Category</label>
                                                </div>
                                                <div class="settings-item">
                                                    <input type="checkbox" class="check-item-code">
                                                    <label>Item Code</label>
                                                </div>
                                                <div class="settings-item">
                                                    <input type="checkbox" class="check-description">
                                                    <label>Description</label>
                                                </div>
                                                <div class="settings-item">
                                                    <input type="checkbox" class="check-discount">
                                                    <label>Discount</label>
                                                </div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="item-rows">
                                    <!-- Row 1 -->
                                    <tr class="item-row">
                                        <td class="row-num">
                                            <span class="row-index-text">1</span>
                                            <div class="delete-row-icon"><i class="fa-solid fa-trash-can"></i></div>
                                        </td>
                                        <td>
                                            <select class="form-select item-name">
                                                <option value="" selected disabled>Select Item</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}" data-price="{{ $item->price }}" data-sale-price="{{ $item->sale_price }}" data-stock="{{ $item->opening_qty }}" data-location="{{ $item->location }}" data-label="{{ $item->name }}" data-rich-label="{{ $item->name }} | Sale: {{ $item->sale_price ?? $item->price ?? 0 }} | Stock: {{ $item->opening_qty ?? 0 }} | Location: {{ $item->location ?? '' }}" data-unit="{{ $item->unit }}">{{ $item->name }} | Sale: {{ $item->sale_price ?? $item->price ?? 0 }} | Stock: {{ $item->opening_qty ?? 0 }} | Location: {{ $item->location ?? '' }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="col-category d-none"><input type="text" class="item-category"
                                                placeholder="Category"></td>
                                        <td class="col-item-code d-none"><input type="text" class="item-code"
                                                placeholder="Item Code"></td>
                                        <td class="col-description d-none"><input type="text" class="item-desc"
                                                placeholder="Description"></td>
                                        <td class="col-discount d-none"><input type="number" class="item-discount"
                                                value="0">
                                        </td>
                                        <td><input type="number" class="item-qty" value="1"></td>
                                        <td class="custom-size-td">
                                            <select class="item-unit">
                                                <option value="">Select Unit</option>
                                                <option value="PCS">PCS (Pieces)</option>
                                                <option value="BOX">BOX</option>
                                                <option value="PACK">PACK</option>
                                                <option value="SET">SET</option>
                                                <option value="KG">KG (Kilogram)</option>
                                                <option value="G">Gram</option>
                                                <option value="M">Meter</option>
                                                <option value="FT">Feet</option>
                                                <option value="L">Liter</option>
                                                <option value="ML">Milliliter</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="item-price" value="0"></td>
                                        <td class="col-amount"><input type="text" class="item-amount" value="0"
                                                readonly></td>
                                        <td class="add-col"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="table-footer">
                                <button type="button" class="btn-add-row add-row-btn">ADD ROW</button>
                                <div class="footer-totals">
                                    <div>
                                        <span class="total-label">TOTAL QTY</span>
                                        <span class="total-qty">0</span>
                                    </div>
                                    <div>
                                        <span class="total-label">TOTAL AMOUNT</span>
                                        <span class="total-base-amount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Split Section -->
                        <div class="bottom-section">
                            <!-- Left Column -->
                            <div class="bottom-left">




                                <div class="description-action-group mb-2">
                                    <button type="button" class="btn-action-light action-btn add-description">
                                        <i class="fa-solid fa-align-left"></i>
                                        ADD DESCRIPTION
                                    </button>
                                    <div class="description-pane d-none mt-2">
                                        <label class="form-label">Description</label>
                                        <textarea class="form-control description-input" rows="3" placeholder="Enter a remark or description"></textarea>
                                    </div>
                                </div>

                                <div class="action-buttons d-flex flex-wrap gap-2 mb-2">
                                    <button type="button" class="btn-action-light action-btn add-image">
                                        <i class="fa-solid fa-camera"></i>
                                        ADD IMAGE
                                    </button>

                                    <button type="button" class="btn-action-light action-btn add-document">
                                        <i class="fa-solid fa-align-left"></i>
                                        ADD DOCUMENT
                                    </button>
                                </div>

                                <div class="image-upload-section mt-2">
                                    <div class="image-placeholder text-center p-3 border border-dashed rounded" style="cursor:pointer;">
                                        <div class="text-muted">Click to select image(s)</div>
                                        <div class="small text-muted">(PNG/JPG, up to 5MB each)</div>
                                    </div>
                                    <div class="image-files-list d-flex flex-wrap gap-2 mt-2"></div>
                                    <div class="document-files-list list-group mt-2"></div>
                                </div>

                                <input type="file" class="d-none image-input" accept="image/*" multiple />
                                <input type="file" class="d-none document-input" accept=".pdf,.doc,.docx" multiple />
                            </div>

                            <!-- Right Column -->
                            <div class="bottom-right">
                                <!-- Discount -->
                                <div class="calc-row">
                                    <div class="calc-label">Discount</div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input discount-pct" placeholder="%">
                                        <span>-</span>
                                        <input type="number" class="mini-input discount-rs" placeholder="Rs">
                                    </div>
                                </div>

                                <!-- Tax -->
                                <div class="calc-row">
                                    <div class="calc-label">Tax</div>
                                    <div class="calc-inputs">
                                        <select class="mini-input tax-select" style="width: 100px;">
                                            <option value="0">NONE</option>
                                            <option value="5">GST@5%</option>
                                            <option value="12">GST@12%</option>
                                            <option value="18">GST@18%</option>
                                        </select>
                                        <span class="tax-amount-display">0</span>
                                    </div>
                                </div>

                                <!-- Round Off -->
                                <div class="calc-row">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="custom-checkbox round-off-check" checked>
                                        <label class="link-text">Round Off</label>
                                    </div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input round-off-val" value="0" readonly>
                                    </div>
                                </div>

                                <!-- Final Total -->
                                <div class="final-total-group">
                                    <div class="calc-row" style="margin-bottom: 5px;">
                                        <div class="calc-label" style="font-weight: 700;">Total</div>
                                    </div>
                                    <input type="text" class="total-input-large grand-total" value="0" readonly>
                                </div>


                            </div>
                        </div>
                    </div>

                    <!-- Fixed Action Bar -->
                    <div class="sticky-actions">
                        <div class="btn-share">
                            <button class="btn-share-main">Share</button>
                            <button class="btn-share-arrow"><i class="fa-solid fa-chevron-down"></i></button>
                        </div>
                        <button class="btn-save" type="button">Save</button>
                    </div>
                </div>
            </template>
        </main>
    </div>



    <!-- Tab Limit Modal -->
    <div class="modal fade" id="tabLimitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-dark border-secondary">
                <div class="modal-body text-center p-4">
                    <i class="bi bi-exclamation-triangle text-warning display-4 mb-3"></i>
                    <h5>Maximum Limit Reached</h5>
                    <p>You can open a maximum of 10 transactions at a time.</p>
                    <button type="button" class="btn btn-primary px-4 mt-2" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Close Confirmation Modal -->
    <div class="modal fade" id="closeConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-dark border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Close Tab?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to close this tab? Your purchase will not be saved. Use the Save button on
                        the bottom right of the screen to save.</p>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm-close-btn" class="btn btn-danger">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Party Modal -->
    <div class="modal fade" id="addPartyModal" tabindex="-1" aria-labelledby="addPartyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addPartyModalLabel"><i class="fa-solid fa-user-plus me-2"></i>Add Party</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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

          <!-- Tabs -->
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
            <!-- Address Tab -->
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
                  <textarea  id="shippingAddress" class="form-control" name="shipping_address" rows="3" placeholder="Enter shipping address"></textarea>
                </div>
              </div>
            </div>

            <!-- Credit & Balance Tab -->
          <div class="tab-pane fade" id="partyCreditPane" role="tabpanel" aria-labelledby="party-credit-tab">
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

            <!-- Additional Fields Tab -->
            <div class="tab-pane fade" id="partyAdditionalPane" role="tabpanel" aria-labelledby="party-additional-tab" data-party-setting="additional_fields">
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

      </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @isset($estimate)
        <script>
            window.items = @json($items ?? []);
            window.parties = @json($parties ?? []);
            window.partyStoreUrl = "{{ route('parties.store') }}";
            window.itemRoutes = { index: "{{ url('dashboard/items') }}", store: "{{ url('dashboard/items') }}", unitsIndex: "{{ url('dashboard/items/units') }}", unitsStore: "{{ url('dashboard/items/units') }}", categoryStore: "{{ url('dashboard/items/category') }}" };
            window.saleStoreUrl = "{{ route('sale.update', $estimate) }}";
            window.saleMethod = 'PUT';
            window.estimateId = {{ $estimate->id }};
            window.editSaleData = @json($estimate->load(['items.item'])->toArray());
            window.docType = 'estimate';
        </script>
    @elseif(isset($prefilledEstimateData))
        <script>
            window.items = @json($items ?? []);
            window.parties = @json($parties ?? []);
            window.partyStoreUrl = "{{ route('parties.store') }}";
            window.itemRoutes = { index: "{{ url('dashboard/items') }}", store: "{{ url('dashboard/items') }}", unitsIndex: "{{ url('dashboard/items/units') }}", unitsStore: "{{ url('dashboard/items/units') }}", categoryStore: "{{ url('dashboard/items/category') }}" };
            window.saleStoreUrl = "{{ route('sale.store') }}";
            window.saleMethod = 'POST';
            window.estimateId = null;
            window.editSaleData = @json($prefilledEstimateData);
            window.docType = 'estimate';
        </script>
    @else
        <script>
            window.items = @json($items ?? []);
            window.parties = @json($parties ?? []);
            window.partyStoreUrl = "{{ route('parties.store') }}";
            window.itemRoutes = { index: "{{ url('dashboard/items') }}", store: "{{ url('dashboard/items') }}", unitsIndex: "{{ url('dashboard/items/units') }}", unitsStore: "{{ url('dashboard/items/units') }}", categoryStore: "{{ url('dashboard/items/category') }}" };
            window.saleStoreUrl = "{{ route('sale.store') }}";
            window.saleMethod = 'POST';
            window.estimateId = null;
            window.editSaleData = null;
            window.docType = 'estimate';
        </script>
    @endisset

    <!-- Toast container -->
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="sale-toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    @include('dashboard.shared.item-create-modals')

    <div class="modal fade" id="partyGroupModal" tabindex="-1">
      <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h6 class="modal-title">New Party Group</h6>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="text" id="newGroupName" class="form-control" placeholder="Enter group name">
          </div>
          <div class="modal-footer">
            <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</button>
            <button class="btn btn-primary btn-sm" id="saveGroupBtn">Save</button>
          </div>
        </div>
      </div>
    </div>

    @include('components.modals.item-modal')

    <!-- Form Logic -->
    <script src="{{ asset('js/estimateform_script.js') }}"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="{{ asset('js/shared-party-item-create.js') }}"></script>

    <!-- Add Party Modal Handler -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const addNewPartyBtn = document.getElementById("addNewPartyBtn");
        const addPartyModal = document.getElementById("addPartyModal");
        const btnSaveParty = document.getElementById("btnSaveParty");
        const addPartyForm = document.getElementById("addPartyForm");

        // Show modal when "Add New Party" is clicked
        if (addNewPartyBtn && addPartyModal) {
            addNewPartyBtn.addEventListener("click", function(e) {
                e.preventDefault();
                addPartyForm.reset();
                const modal = new bootstrap.Modal(addPartyModal);
                modal.show();
            });
        }

        // Save new party
        if (btnSaveParty && addPartyForm) {
            btnSaveParty.addEventListener("click", function() {
                const form = addPartyForm;
                const data = new FormData(form);

                // Transaction type fix
                const toReceive = document.getElementById("toReceive").checked;
                const toPay = document.getElementById("toPay").checked;
                if(toReceive) data.set("transaction_type", "receive");
                else if(toPay) data.set("transaction_type", "pay");
                else data.set("transaction_type", "receive");

                fetch("{{ route('parties.store') }}", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: data
                })
                .then(res => res.json())
                .then(res => {
                    if(res.success) {
                        const party = res.party || {};
                        const partyRecord = {
                            id: party.id || '',
                            name: party.name || data.get('name') || '',
                            phone: party.phone || data.get('phone') || '',
                            email: party.email || data.get('email') || '',
                            city: party.city || data.get('city') || '',
                            address: party.address || data.get('address') || '',
                            billing_address: party.billing_address || data.get('billing_address') || '',
                            opening_balance: party.opening_balance || data.get('opening_balance') || 0,
                            transaction_type: party.transaction_type || data.get('transaction_type') || 'receive',
                            party_group: party.party_group || data.get('party_group') || ''
                        };

                        if (partyRecord.id) {
                            window.parties = Array.isArray(window.parties) ? window.parties.filter(p => String(p.id) !== String(partyRecord.id)) : [];
                            window.parties.unshift(partyRecord);
                        }

                        if (partyRecord.party_group) {
                            window.salePartyGroups = Array.isArray(window.salePartyGroups) ? window.salePartyGroups : [];
                            if (!window.salePartyGroups.includes(partyRecord.party_group)) {
                                window.salePartyGroups.push(partyRecord.party_group);
                            }

                            const partyGroupList = document.getElementById('partyGroupList');
                            if (partyGroupList && !partyGroupList.querySelector(`[data-group="${partyRecord.party_group}"]`)) {
                                const groupBtn = document.createElement('button');
                                groupBtn.type = 'button';
                                groupBtn.className = 'dropdown-item';
                                groupBtn.dataset.group = partyRecord.party_group;
                                groupBtn.textContent = partyRecord.party_group;
                                groupBtn.onclick = () => {
                                    const groupInput = document.getElementById('partyGroupInput');
                                    const groupText = document.getElementById('partyGroupText');
                                    if (groupInput) groupInput.value = partyRecord.party_group;
                                    if (groupText) groupText.textContent = partyRecord.party_group;
                                };
                                partyGroupList.appendChild(groupBtn);
                            }
                        }

                        // Close modal
                        const modal = bootstrap.Modal.getInstance(addPartyModal);
                        if (modal) modal.hide();

                        // Reset form
                        form.reset();

                        // Dispatch event to refresh dropdown
                        window.dispatchEvent(new Event('partiesUpdated'));

                        // Show success message
                        const toast = document.getElementById('sale-toast');
                        if (toast) {
                            toast.querySelector('.toast-body').textContent = 'Party added successfully!';
                            const bsToast = new bootstrap.Toast(toast);
                            bsToast.show();
                        }
                    } else {
                        alert('Error: ' + (res.message || 'Failed to save party'));
                    }
                })
                .catch(err => {
                    console.error('Error:', err);
                    alert('An error occurred while saving the party');
                });
            });
        }

        // Ensure Party modal opens with Address tab active on first show
        if (addPartyModal) {
            addPartyModal.addEventListener('shown.bs.modal', function () {
                const addressTabEl = document.getElementById('party-address-tab');
                const addressPaneEl = document.getElementById('partyAddressPane');
                const creditTabEl = document.getElementById('party-credit-tab');
                const creditPaneEl = document.getElementById('partyCreditPane');
                const additionalTabEl = document.getElementById('party-additional-tab');
                const additionalPaneEl = document.getElementById('partyAdditionalPane');

                [addressTabEl, creditTabEl, additionalTabEl].forEach(tab => {
                    if (tab) {
                        tab.classList.remove('active');
                        tab.setAttribute('aria-selected', 'false');
                    }
                });

                [addressPaneEl, creditPaneEl, additionalPaneEl].forEach(pane => {
                    if (pane) {
                        pane.classList.remove('show', 'active');
                    }
                });

                if (addressTabEl) {
                    addressTabEl.classList.add('active');
                    addressTabEl.setAttribute('aria-selected', 'true');
                }
                if (addressPaneEl) {
                    addressPaneEl.classList.add('show', 'active');
                    addressPaneEl.style.display = '';
                }

                if (addressTabEl && bootstrap.Tab) {
                    const addressTab = bootstrap.Tab.getOrCreateInstance(addressTabEl);
                    addressTab.show();
                }
            });
        }

        // Item modal initialization and service/product toggle
        const unitButtons = document.querySelectorAll('.unit-option');
        const unitInput = document.getElementById('newItemUnit');
        const unitBtn = document.getElementById('newItemUnitBtn');
        const assignCodeBtn = document.getElementById('assignItemCodeBtn');
        const itemNameInput = document.getElementById('newItemName');
        const wholesaleToggle = document.getElementById('toggleWholesalePricing');
        const wholesaleSection = document.querySelector('.wholesale-pricing');

        if (!document.getElementById('selectItemUnitModal') && unitButtons && unitInput && unitBtn) {
            unitButtons.forEach(btn => {
                btn.addEventListener('click', function () {
                    const unit = this.dataset.unit || '';
                    unitInput.value = unit;
                    unitBtn.textContent = unit || 'Select Unit';
                });
            });
        }

        if (assignCodeBtn && itemNameInput) {
            assignCodeBtn.addEventListener('click', function () {
                const name = itemNameInput.value.trim();
                const slug = name ? name.toUpperCase().replace(/[^A-Z0-9]+/g, '') : 'ITEM';
                const suffix = Math.floor(Math.random() * 9000) + 1000;
                document.getElementById('newItemCode').value = `${slug ? slug.substring(0, 6) : 'ITEM'}-${suffix}`;
            });
        }

        const itemTypeToggle = document.getElementById('newItemTypeToggle');
        const itemTypeHidden = document.getElementById('newItemType');
        const productLabel = document.getElementById('newItemProductLabel');
        const itemNameLabel = document.getElementById('newItemNameLabel');
        const stockTabButton = document.getElementById('stock-tab');
        const stockTabPane = document.getElementById('stock-tab-pane');
        const purchaseSection = document.getElementById('purchase-sec');

        if (itemTypeToggle && itemTypeHidden) {
            itemTypeToggle.addEventListener('change', function () {
                const isService = this.checked;
                itemTypeHidden.value = isService ? 'service' : 'product';
                if (productLabel) {
                    productLabel.textContent = isService ? 'Service' : 'Product';
                }
                if (itemNameLabel) {
                    itemNameLabel.textContent = isService ? 'Service Name *' : 'Item Name *';
                }
                if (stockTabButton) {
                    stockTabButton.style.display = isService ? 'none' : '';
                }
                if (stockTabPane) {
                    stockTabPane.style.display = isService ? 'none' : '';
                }
                if (purchaseSection) {
                    purchaseSection.style.display = isService ? 'none' : '';
                }
                const pricingTabEl = document.getElementById('pricing-tab');
                if (pricingTabEl) {
                    const pricingTab = bootstrap.Tab.getOrCreateInstance(pricingTabEl);
                    pricingTab.show();
                }
            });
        }

        const newItemImageInput = document.getElementById('newItemImage');
        const newItemImageThumb = document.getElementById('newItemImageThumb');
        const newItemImageLabel = document.getElementById('newItemImageLabel');

        if (newItemImageInput) {
            newItemImageInput.addEventListener('change', function (event) {
                const file = event.target.files[0];
                if (!file) {
                    if (newItemImageThumb) {
                        newItemImageThumb.innerHTML = '<i class="fa-regular fa-image fa-2x text-secondary"></i>';
                    }
                    if (newItemImageLabel) {
                        newItemImageLabel.textContent = 'Click to choose image';
                    }
                    return;
                }
                const reader = new FileReader();
                reader.onload = function (e) {
                    if (newItemImageThumb) {
                        newItemImageThumb.innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;"/>`;
                    }
                    if (newItemImageLabel) {
                        newItemImageLabel.textContent = file.name;
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        if (wholesaleToggle && wholesaleSection) {
            wholesaleToggle.addEventListener('click', function () {
                wholesaleSection.classList.toggle('d-none');
                this.textContent = wholesaleSection.classList.contains('d-none') ? '+ Add Wholesale Price' : '- Remove Wholesale Price';
            });
        }
    });
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function () {
        const trigger = document.getElementById("partyGroupTrigger");
        const menu = document.getElementById("partyGroupMenu");
        const list = document.getElementById("partyGroupList");
        const input = document.getElementById("partyGroupInput");
        const text = document.getElementById("partyGroupText");

        const groupModal = new bootstrap.Modal(document.getElementById('partyGroupModal'));
        window.salePartyGroups = window.salePartyGroups || Array.from((list?.querySelectorAll('.dropdown-item') || []))
            .filter((btn) => btn.id !== 'addNewGroupBtn')
            .map((btn) => btn.textContent.trim())
            .filter(Boolean);

        if (!trigger || !menu || !list || !input || !text) {
            return;
        }

        // Toggle dropdown
        trigger.addEventListener("click", () => {
            menu.classList.toggle("d-none");
        });

        // Close outside
        document.addEventListener("click", (e) => {
            if (!trigger.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add("d-none");
            }
        });

        // Render groups
        function renderGroups() {
            list.innerHTML = "";
            window.salePartyGroups.forEach(g => {
                const btn = document.createElement("button");
                btn.type = "button";
                btn.className = "dropdown-item";
                btn.dataset.group = g;
                btn.textContent = g;

                btn.onclick = () => {
                    input.value = g;
                    text.textContent = g;
                    menu.classList.add("d-none");
                };

                list.appendChild(btn);
            });
        }

        renderGroups();

        list.addEventListener("click", function (event) {
            const btn = event.target.closest("button.dropdown-item");
            if (!btn || btn.id === "addNewGroupBtn") return;

            input.value = btn.dataset.group || btn.textContent.trim();
            text.textContent = btn.dataset.group || btn.textContent.trim();
            menu.classList.add("d-none");
        });

        // Open modal
        const addNewGroupBtn = document.getElementById("addNewGroupBtn");
        if (addNewGroupBtn) {
            addNewGroupBtn.onclick = () => {
                groupModal.show();
            };
        }

        const partyGroupsStoreUrl = '{{ route("party-groups.store") }}';

        // Save group
        const saveGroupBtn = document.getElementById("saveGroupBtn");
        if (saveGroupBtn) {
            saveGroupBtn.onclick = async () => {
                const nameEl = document.getElementById("newGroupName");
                const name = nameEl.value.trim();

                if (!name) return alert("Enter group name");

                try {
                    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const response = await fetch(partyGroupsStoreUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token || '',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({ name }),
                    });

                    const result = await response.json();
                    if (!response.ok || !result.success) {
                        throw new Error(result.message || 'Unable to save party group');
                    }

                    const groupName = result.partyGroup?.name || name;
                    if (!window.salePartyGroups.includes(groupName)) {
                        window.salePartyGroups.push(groupName);
                    }
                    renderGroups();

                    input.value = groupName;
                    text.textContent = groupName;

                    nameEl.value = "";
                    groupModal.hide();
                } catch (error) {
                    console.error(error);
                    alert('Could not save party group. Please try again.');
                }
            };
        }
    });
    </script>
</body>


</html>
