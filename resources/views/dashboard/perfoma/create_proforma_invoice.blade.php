<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>
    @if(isset($proforma))
        Edit
    @else
        Create
    @endif
    Proforma Invoice | Vyapar
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

<style>
    /* Dropdown with two columns and scrollbar */
    #partyDropdownMenu {
        min-width: 250px; /* Adjust as needed */
        max-width: 100%;  /* Never exceed container */
        max-height: 350px; /* Add max-height for scrollbar */
        overflow-y: auto; /* Enable vertical scrolling */
        overflow-x: hidden; /* Hide horizontal scroll */
    }

    /* Scrollbar styling for responsive dropdown */
    #partyDropdownMenu::-webkit-scrollbar {
        width: 8px;
    }

    #partyDropdownMenu::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    #partyDropdownMenu::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 4px;
    }

    #partyDropdownMenu::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* Firefox scrollbar */
    #partyDropdownMenu {
        scrollbar-width: thin;
        scrollbar-color: #888 #f1f1f1;
    }

    .party-option span {
        display: inline-block;
        width: 100%;
    }
    .party-option span:first-child {
        width: 60%; /* Party name */
    }
    .party-option span:last-child {
        width: 40%; /* Opening balance */
        text-align: right;
    }
</style>

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
                <p class="mt-3 ms-3 mb-0 me-3 mb-2">Proforma Invoice</p>

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
    <ul class="dropdown-menu w-100" aria-labelledby="partyDropdownBtn" id="partyDropdownMenu">
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
           data-name="{{ $party->name }}"
           data-phone="{{ $party->phone }}"
           data-phone-number-2="{{ $party->phone_number_2 }}"
           data-city="{{ $party->city }}"
           data-ptcl="{{ $party->ptcl_number }}"
           data-email="{{ $party->email }}"
           data-address="{{ addslashes($party->address ?? '') }}"
           data-billing="{{ addslashes($party->billing_address ?? '') }}"
           data-shipping="{{ addslashes($party->shipping_address ?? '') }}"
           data-party-group="{{ $party->party_group }}"
           data-due-days="{{ $party->due_days ?? '' }}"
           data-opening="{{ $party->opening_balance ?? 0 }}"
           data-type="{{ $party->transaction_type }}"
           data-party-type="{{ is_array($party->party_type) ? implode(',', $party->party_type) : ($party->party_type ?? '') }}"
           data-credit-limit-enabled="{{ $party->credit_limit_enabled ?? 0 }}"
           data-credit-limit-amount="{{ $party->credit_limit_amount ?? '' }}"
           data-custom-fields="{{ e(json_encode($party->custom_fields ?? [])) }}">
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

                            <div class="header-right w-25">
                                <div class="input-group">
                                    <span>Ref No.</span>
                                    <input type="text" class="input-control underline-input bill-number" value="{{ $nextInvoiceNumber ?? 'Auto' }}" readonly>
                                </div>
                                <div class="input-group date-wrapper">
                                    <span>Invoice  Date</span>
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
                                            <select class="item-unit"><option value="">Select Unit</option><option value="PCS">PCS (Pieces)</option><option value="BOX">BOX</option><option value="PACK">PACK</option><option value="SET">SET</option><option value="KG">KG (Kilogram)</option><option value="G">Gram</option><option value="M">Meter</option><option value="FT">Feet</option><option value="L">Liter</option><option value="ML">Milliliter</option></select>
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
                                            @foreach($parties->pluck('party_group')->filter()->unique()->values() as $partyGroup)
                                                <button type="button" class="dropdown-item" data-group="{{ $partyGroup }}">{{ $partyGroup }}</button>
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
                                        <textarea id="shippingAddress" class="form-control" name="shipping_address" rows="3" placeholder="Enter shipping address"></textarea>
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

    <!-- Party Group Modal -->
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
                    <button class="btn btn-primary btn-sm" id="btnSaveGroup">Save</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @isset($proforma)
        <script>
            window.items = @json($items ?? []);
            window.parties = @json($parties ?? []);
            window.partyStoreUrl = "{{ route('parties.store') }}";
            window.itemRoutes = { index: "{{ url('dashboard/items') }}", store: "{{ url('dashboard/items') }}", unitsIndex: "{{ url('dashboard/items/units') }}", unitsStore: "{{ url('dashboard/items/units') }}", categoryStore: "{{ url('dashboard/items/category') }}" };
            window.saleStoreUrl = "{{ route('proforma-invoice.update', $proforma->id) }}";
            window.saleMethod = 'PUT';
            window.proformaId = {{ $proforma->id }};
            window.editSaleData = @json($proforma->load(['items', 'party'])->toArray());
            window.docType = 'proforma';
        </script>
    @elseif(isset($duplicateProforma))
        <script>
            window.items = @json($items ?? []);
            window.parties = @json($parties ?? []);
            window.partyStoreUrl = "{{ route('parties.store') }}";
            window.itemRoutes = { index: "{{ url('dashboard/items') }}", store: "{{ url('dashboard/items') }}", unitsIndex: "{{ url('dashboard/items/units') }}", unitsStore: "{{ url('dashboard/items/units') }}", categoryStore: "{{ url('dashboard/items/category') }}" };
            window.saleStoreUrl = "{{ route('proforma-invoice.store') }}";
            window.saleMethod = 'POST';
            window.proformaId = null;
            window.editSaleData = @json(array_merge($duplicateProforma->load(['items', 'party'])->toArray(), ['bill_number' => $nextInvoiceNumber]));
            window.docType = 'proforma';
        </script>
    @else
        <script>
            window.items = @json($items ?? []);
            window.parties = @json($parties ?? []);
            window.partyStoreUrl = "{{ route('parties.store') }}";
            window.itemRoutes = { index: "{{ url('dashboard/items') }}", store: "{{ url('dashboard/items') }}", unitsIndex: "{{ url('dashboard/items/units') }}", unitsStore: "{{ url('dashboard/items/units') }}", categoryStore: "{{ url('dashboard/items/category') }}" };
            window.saleStoreUrl = "{{ route('proforma-invoice.store') }}";
            window.saleMethod = 'POST';
            window.proformaId = null;
            window.editSaleData = null;
            window.docType = 'proforma';
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

    <!-- Form Logic -->
    <script src="{{ asset('js/perfomaform_script.js') }}"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/perfoma_script.js') }}"></script>
    <script src="{{ asset('js/shared-party-item-create.js') }}"></script>

    <!-- Add Party Modal Handler -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const addModalEl = document.getElementById('addPartyModal');
        const addModal = new bootstrap.Modal(addModalEl);
        const saveBtn = document.getElementById("btnSaveParty");
        const saveNewBtn = document.getElementById("btnSaveNewParty");

        // Handle modal close - clean up backdrops to prevent black screen
        addModalEl.addEventListener('hidden.bs.modal', function () {
            // Remove any remaining backdrops
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
            // Remove modal-open class from body
            document.body.classList.remove('modal-open');
            // Reset overflow if it was set
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });

        // Handle modal show - ensure clean state
        addModalEl.addEventListener('show.bs.modal', function () {
            // Remove any orphaned backdrops before opening
            document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
        });




        function getPartyData() {
            const form = document.getElementById("addPartyForm");
            return new FormData(form);
        }

       function applyPartyDueDays(partyRecord = {}) {
        const dealDaysSelect = document.querySelector(".due-days-select");
        const dealDaysCustomInput = document.querySelector(".due-days-custom");
        const dueDateInput = document.querySelector(".due-date");
        const orderDateInput = document.querySelector(".order-date");
        if (!dealDaysSelect || !dueDateInput || !orderDateInput) {
            return;
        }

        const dueDays = Number(partyRecord.due_days || 0);
        const orderDateValue = orderDateInput.value;
        const allowedDays = ['0', '5', '10', '15', '30', '45'];

        if (dueDays > 0) {
            if (allowedDays.includes(String(dueDays))) {
                dealDaysSelect.value = String(dueDays);
                dealDaysCustomInput?.classList.add('d-none');
                if (dealDaysCustomInput) dealDaysCustomInput.value = '';
            } else {
                dealDaysSelect.value = 'custom';
                dealDaysCustomInput?.classList.remove('d-none');
                if (dealDaysCustomInput) dealDaysCustomInput.value = dueDays;
            }
        } else {
            dealDaysSelect.value = '0';
            dealDaysCustomInput?.classList.add('d-none');
            if (dealDaysCustomInput) dealDaysCustomInput.value = '';
        }

        if (!orderDateValue || dueDays <= 0) {
            return;
        }

        const dueDate = new Date(orderDateValue);
        if (Number.isNaN(dueDate.getTime())) {
            return;
        }

        dueDate.setDate(dueDate.getDate() + dueDays);
        const yyyy = dueDate.getFullYear();
        const mm = String(dueDate.getMonth() + 1).padStart(2, '0');
        const dd = String(dueDate.getDate()).padStart(2, '0');
        dueDateInput.value = `${yyyy}-${mm}-${dd}`;
       }

       function saveParty(closeAfterSave = true) {
        const form = document.getElementById("addPartyForm");
        const data = new FormData(form);

        // Transaction type fix
        const toReceive = document.getElementById("toReceive").checked;
        const toPay = document.getElementById("toPay").checked;
        if(toReceive) data.set("transaction_type", "receive");
        else if(toPay) data.set("transaction_type", "pay");

        // Credit limit fix
        const creditSwitch = document.getElementById("creditLimitSwitch");
        data.set("credit_limit_enabled", creditSwitch.checked ? 1 : 0);

        fetch("{{ route('parties.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"   // important!
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
                    phone_number_2: party.phone_number_2 || data.get('phone_number_2') || '',
                    ptcl_number: party.ptcl_number || data.get('ptcl_number') || '',
                    email: party.email || data.get('email') || '',
                    city: party.city || data.get('city') || '',
                    address: party.address || data.get('address') || '',
                    billing_address: party.billing_address || data.get('billing_address') || '',
                    shipping_address: party.shipping_address || data.get('shipping_address') || '',
                    party_group: party.party_group || data.get('party_group') || '',
                    due_days: party.due_days || data.get('due_days') || '',
                    opening_balance: party.opening_balance || data.get('opening_balance') || 0,
                    credit_limit_enabled: party.credit_limit_enabled || data.get('credit_limit_enabled') || 0,
                    credit_limit_amount: party.credit_limit_amount || data.get('credit_limit_amount') || '',
                    custom_fields: party.custom_fields || data.getAll('custom_fields[]') || [],
                    party_type: party.party_type || data.getAll('party_type[]') || [],
                    transaction_type: party.transaction_type || data.get('transaction_type') || '',
                };

                if (partyRecord.party_group) {
                    window.salePartyGroups = window.salePartyGroups || [];
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

                if (partyRecord.id) {
                    window.parties = Array.isArray(window.parties) ? window.parties.filter(p => String(p.id) !== String(partyRecord.id)) : [];
                    window.parties.push(partyRecord);

                    const dropdownMenu = document.getElementById("partyDropdownMenu");
                    const partyIdInput = document.querySelector(".party-id");
                    const dropdownBtn = document.getElementById("partyDropdownBtn");
                    const balanceDisplay = document.getElementById("partyBalanceDisplay");

                    if (dropdownMenu) {
                        const existing = dropdownMenu.querySelector(`.party-option[data-id="${partyRecord.id}"]`);
                        if (existing) {
                            existing.closest('li')?.remove();
                        }

                        const optionHtml = `
                          <li>
                            <a class="dropdown-item d-flex justify-content-between party-option"
                               href="#"
                               data-id="${partyRecord.id}"
                               data-name="${partyRecord.name}"
                               data-phone="${partyRecord.phone}"
                               data-phone-number-2="${partyRecord.phone_number_2 || ''}"
                               data-city="${partyRecord.city}"
                               data-ptcl="${partyRecord.ptcl_number}"
                               data-email="${partyRecord.email || ''}"
                               data-address="${partyRecord.address.replace(/"/g, '&quot;')}"
                               data-billing="${partyRecord.billing_address.replace(/"/g, '&quot;')}"
                               data-shipping="${partyRecord.shipping_address.replace(/"/g, '&quot;') }"
                               data-party-group="${partyRecord.party_group || ''}"
                               data-due-days="${partyRecord.due_days}"
                               data-opening="${partyRecord.opening_balance}"
                               data-type="${partyRecord.transaction_type}"
                               data-party-type="${Array.isArray(partyRecord.party_type) ? partyRecord.party_type.join(',') : partyRecord.party_type || ''}"
                               data-credit-limit-enabled="${partyRecord.credit_limit_enabled || 0}"
                               data-credit-limit-amount="${partyRecord.credit_limit_amount || ''}"
                               data-custom-fields="${String(JSON.stringify(partyRecord.custom_fields || [])).replace(/"/g, '&quot;')}">
                                <span>${partyRecord.name}</span>
                                <span class="text-success">0</span>
                            </a>
                          </li>
                        `;

                        const divider = dropdownMenu.querySelector('li > hr.dropdown-divider');
                        if (divider) {
                            divider.closest('li')?.insertAdjacentHTML('beforebegin', optionHtml);
                        } else {
                            dropdownMenu.insertAdjacentHTML('beforeend', optionHtml);
                        }
                    }

                    if (partyIdInput) partyIdInput.value = partyRecord.id;
                    if (dropdownBtn) dropdownBtn.textContent = partyRecord.name || 'Select Party';
                    if (balanceDisplay) {
                        balanceDisplay.textContent = partyRecord.transaction_type === 'pay'
                            ? `To Pay Rs ${partyRecord.opening_balance || 0}`
                            : `To Receive Rs ${partyRecord.opening_balance || 0}`;
                        balanceDisplay.className = partyRecord.transaction_type === 'pay' ? 'text-danger small' : 'text-success small';
                    }
                }

                // Close modal first, then show success message
                if(closeAfterSave) {
                    addModal.hide();
                    // Wait for modal to close, then reset
                    setTimeout(() => {
                        form.reset();
                        // Clean up any leftover backdrops
                        document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                        document.body.classList.remove('modal-open');
                    }, 300);
                } else {
                    form.reset();
                }

                // Show success message without blocking UI
                const toast = document.createElement('div');
                toast.className = 'toast-notification';
                toast.innerHTML = `
                    <div style="background: #28a745; color: white; padding: 12px 20px; border-radius: 4px; margin: 10px; position: fixed; top: 20px; right: 20px; z-index: 9999; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                        <i class="fa-solid fa-check me-2"></i> Party saved successfully!
                    </div>
                `;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 3000);
            } else {
                const errorToast = document.createElement('div');
                errorToast.innerHTML = `
                    <div style="background: #dc3545; color: white; padding: 12px 20px; border-radius: 4px; margin: 10px; position: fixed; top: 20px; right: 20px; z-index: 9999; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                        <i class="fa-solid fa-exclamation me-2"></i> Error saving party
                    </div>
                `;
                document.body.appendChild(errorToast);
                setTimeout(() => errorToast.remove(), 3000);
            }
        })
        .catch(err => {
            console.error(err);
            const errorToast = document.createElement('div');
            errorToast.innerHTML = `
                <div style="background: #dc3545; color: white; padding: 12px 20px; border-radius: 4px; margin: 10px; position: fixed; top: 20px; right: 20px; z-index: 9999; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
                        <i class="fa-solid fa-exclamation me-2"></i> Something went wrong! Check console.
                    </div>
                `;
            document.body.appendChild(errorToast);
            setTimeout(() => errorToast.remove(), 3000);
        });
    }
        saveBtn.addEventListener('click', function () {
            saveParty(true); // close modal after save
        });

        saveNewBtn.addEventListener('click', function () {
            saveParty(false); // reset modal for new entry
        });
    });

    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const dropdownBtn = document.getElementById("partyDropdownBtn");
        const dropdownMenu = document.getElementById("partyDropdownMenu");
        const partyIdInput = document.querySelector(".party-id");
        const balanceDisplay = document.getElementById("partyBalanceDisplay");
        const addModalEl = document.getElementById('addPartyModal');

        const addModal = new bootstrap.Modal(addModalEl);

        const setPartyFieldValues = (partyRecord = {}) => {
            // For proforma, we don't have all these fields, so we check if they exist first
            const phoneInput = document.querySelector(".phone-input");
            const cityInput = document.querySelector(".city-input");
            const ptclInput = document.querySelector(".ptcl-input");
            const addressInput = document.querySelector(".address-input");
            const billingAddress = document.querySelector(".billing-address");
            const shippingAddress = document.querySelector(".shipping-address");

            if (phoneInput) phoneInput.value = partyRecord.phone || "";
            if (cityInput) cityInput.value = partyRecord.city || "";
            if (ptclInput) ptclInput.value = partyRecord.ptcl_number || partyRecord.ptcl || "";
            if (addressInput) addressInput.value = partyRecord.address || "";
            if (billingAddress) billingAddress.value = partyRecord.billing_address || partyRecord.billing || "";
            if (shippingAddress) shippingAddress.value = partyRecord.shipping_address || partyRecord.shipping || "";
        };

        const setDueDateFromParty = (partyRecord = {}) => {
            const dealDaysSelect = document.querySelector(".due-days-select");
            const dealDaysCustomInput = document.querySelector(".due-days-custom");
            const dueDateInput = document.querySelector(".due-date");
            const orderDateInput = document.querySelector(".order-date");
            if (!dueDateInput || !orderDateInput || !dealDaysSelect) return;

            const dueDays = Number(partyRecord.due_days || partyRecord.dueDays || 0);
            const orderDateValue = orderDateInput.value;

            if (dueDays > 0) {
                const allowedDays = ['0', '5', '10', '15', '30', '45'];
                if (allowedDays.includes(String(dueDays))) {
                    dealDaysSelect.value = String(dueDays);
                    dealDaysCustomInput?.classList.add('d-none');
                    if (dealDaysCustomInput) dealDaysCustomInput.value = '';
                } else {
                    dealDaysSelect.value = 'custom';
                    dealDaysCustomInput?.classList.remove('d-none');
                    if (dealDaysCustomInput) dealDaysCustomInput.value = dueDays;
                }
            } else {
                dealDaysSelect.value = '0';
                dealDaysCustomInput?.classList.add('d-none');
                if (dealDaysCustomInput) dealDaysCustomInput.value = '';
            }

            if (!orderDateValue) {
                return;
            }

            const dueDate = new Date(orderDateValue);
            if (Number.isNaN(dueDate.getTime())) return;

            dueDate.setDate(dueDate.getDate() + dueDays);
            const yyyy = dueDate.getFullYear();
            const mm = String(dueDate.getMonth() + 1).padStart(2, '0');
            const dd = String(dueDate.getDate()).padStart(2, '0');
            dueDateInput.value = `${yyyy}-${mm}-${dd}`;
        };


        dropdownMenu.addEventListener("click", function(e) {
            if(e.target.closest(".party-option")) {
                e.preventDefault();
                const option = e.target.closest(".party-option");
                const name = option.querySelector("span:first-child").textContent;
                let opening = parseFloat(option.dataset.opening) || 0;
                const type = option.dataset.type;
                const id = option.dataset.id;
                const selectedParty = (window.parties || []).find((party) => String(party.id) === String(id)) || {};
                const partyRecord = {
                    phone: selectedParty.phone ?? option.dataset.phone ?? "",
                    city: selectedParty.city ?? option.dataset.city ?? "",
                    ptcl_number: selectedParty.ptcl_number ?? option.dataset.ptcl ?? "",
                    address: selectedParty.address ?? option.dataset.address ?? "",
                    billing_address: selectedParty.billing_address ?? option.dataset.billing ?? "",
                    shipping_address: selectedParty.shipping_address ?? option.dataset.shipping ?? "",
                    due_days: selectedParty.due_days ?? option.dataset.dueDays ?? "",
                };

                // Button pe sirf party name
                dropdownBtn.textContent = name;

                // Show balance below button with color
              if(type === "pay"){
        balanceDisplay.innerHTML = `
            <i class="fa-solid fa-arrow-up text-danger me-1"></i>
            ₹${opening.toFixed(2)}
        `;
    }
    else if(type === "receive"){
        balanceDisplay.innerHTML = `
            <i class="fa-solid fa-arrow-down text-success me-1"></i>
            ₹${opening.toFixed(2)}
        `;
    }
    else {
        balanceDisplay.innerHTML = `₹${opening.toFixed(2)}`;
    }

                // Save selected party id
                partyIdInput.value = id;

                // Populate detail fields
                setPartyFieldValues(partyRecord);
                setDueDateFromParty(partyRecord);
            }
            else if(e.target.id === "addNewPartyBtn") {
                addModal.show();
                document.getElementById("addPartyForm").reset();
                balanceDisplay.textContent = "";
                setPartyFieldValues({});
            }
        });

    });

    // Party Group Dropdown Functionality
    document.addEventListener("DOMContentLoaded", function() {
        const trigger = document.getElementById("partyGroupTrigger");
        const menu = document.getElementById("partyGroupMenu");
        const list = document.getElementById("partyGroupList");
        const input = document.getElementById("partyGroupInput");
        const text = document.getElementById("partyGroupText");

        const groupModal = new bootstrap.Modal(document.getElementById('partyGroupModal'));
        window.salePartyGroups = window.salePartyGroups || Array.from((list?.querySelectorAll('.dropdown-item') || []))
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

        // Save group
        const saveGroupBtn = document.getElementById("btnSaveGroup");
        if (saveGroupBtn) {
          saveGroupBtn.onclick = () => {
            const name = document.getElementById("newGroupName").value.trim();

            if (!name) return alert("Enter group name");

            if (!window.salePartyGroups.includes(name)) {
                window.salePartyGroups.push(name);
            }
            renderGroups();

            // auto select
            input.value = name;
            text.textContent = name;

            document.getElementById("newGroupName").value = "";
            groupModal.hide();
          };
        }

    });

    // Credit Limit Toggle
    document.addEventListener("DOMContentLoaded", function() {
        const creditLimitSwitch = document.getElementById("creditLimitSwitch");
        const creditLimitAmountWrap = document.getElementById("creditLimitAmountWrap");

        if (creditLimitSwitch) {
            creditLimitSwitch.addEventListener("change", function() {
                if (this.checked) {
                    creditLimitAmountWrap.classList.remove("is-hidden");
                } else {
                    creditLimitAmountWrap.classList.add("is-hidden");
                }
            });
        }
    });

    // Add New Party button in dropdown
    document.addEventListener("DOMContentLoaded", function() {
        const addNewPartyBtn = document.getElementById("addNewPartyBtn");
        const addPartyModal = document.getElementById("addPartyModal");

        if (addNewPartyBtn && addPartyModal) {
            addNewPartyBtn.addEventListener("click", function(e) {
                e.preventDefault();
                const modal = new bootstrap.Modal(addPartyModal);
                modal.show();
            });
        }
    });
    </script>
</body>


</html>





