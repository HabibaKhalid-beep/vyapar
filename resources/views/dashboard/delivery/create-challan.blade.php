<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($challan) ? 'Edit' : 'Create' }} Delivery Challan | Vyapar</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/estimateform_style.css') }}">
    @include('dashboard.shared.party-item-create-styles')
    <style>
        #partyDropdownMenu, #brokerDropdownMenu, #warehouseDropdownMenu { min-width: 250px; max-width: 100%; }
        .party-option span, .warehouse-option span { display: inline-block; width: 100%; }
        .party-option span:first-child, .warehouse-option span:first-child { width: 65%; }
        .party-option span:last-child, .warehouse-option span:last-child { width: 35%; text-align: right; }
        .dropdown-header { font-weight: 600; font-size: 0.9rem; background: #f8f9fa; border-bottom: 1px solid #ddd; }
        .dropdown-item.party-option:hover, .dropdown-item.warehouse-option:hover, .dropdown-item.broker-option:hover { background-color: #e2f0ff; }
        .party-dropdown-wrapper, .broker-dropdown-wrapper, .warehouse-dropdown-wrapper { width: 100%; }
        .header-section { display: grid; grid-template-columns: minmax(0, 1fr) 420px; gap: 28px; align-items: start; }
        .header-left { display: grid; grid-template-columns: minmax(220px, 1.3fr) repeat(4, minmax(120px, 1fr)); gap: 12px; min-width: 0; }
        .party-selector-group { margin-top: 0 !important; }
        .party-selector-panel { background: transparent; border: none; border-radius: 0; padding: 0; box-shadow: none; }
        .party-dropdown-wrapper .btn.dropdown-toggle, .broker-dropdown-wrapper .btn.dropdown-toggle, .warehouse-dropdown-wrapper .btn.dropdown-toggle { width: 100%; min-height: 48px; border-radius: 8px; border-color: #cbd5e1; display: flex; align-items: center; justify-content: space-between; font-weight: 500; background: #fff; }
        #partyBalanceDisplay { margin-top: 6px !important; font-size: 15px; }
        .party-meta-field { display: flex; flex-direction: column; gap: 6px; }
        .party-meta-field label { color: #334155; font-size: 12px; font-weight: 600; line-height: 1; text-transform: uppercase; letter-spacing: 0.04em; }
        .party-meta-field .meta-control { width: 100%; min-height: 48px; padding: 12px 14px; border: 1px solid #d7e0ea; border-radius: 10px; background: #fbfdff; color: #111827; resize: none; font-size: 15px; }
        .party-meta-field textarea.meta-control { min-height: 82px; }
        .party-meta-grid { display: contents; }
        .party-meta-field.address-field { order: 3; }
        .header-right.w-25 { width: 420px !important; min-width: 420px; justify-content: flex-end; background: #ffffff; border: 1px solid #dbe4f0; border-radius: 16px; padding: 18px 20px; box-shadow: 0 10px 24px rgba(15, 23, 42, 0.04); }
        .broker-cell .meta-control {
            width: 100%;
            min-height: 40px;
            padding: 9px 11px;
            border: 1px solid #d7e0ea;
            border-radius: 8px;
            background: #fff;
            color: #111827;
            font-size: 13px;
        }


/* ✅ GRID container (yahan lagna chahiye, inputs pe nahi) */
.warehouse-compact-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr); /* 3 fields ek line me */
    gap: 10px;
    margin-top: 8px;
}

/* ✅ Compact fields */
.warehouse-compact-grid .meta-control,
.warehouse-compact-grid select,
.warehouse-compact-grid input {
    height: 28px;
    min-height: 28px;
    padding: 3px 6px;
    font-size: 12px;
    border-radius: 4px;
    border: 1px solid #d7e0ea;
    width: 100%;
}

/* ✅ Labels choti */
.warehouse-compact-grid .party-meta-field label {
    font-size: 10px;
    margin-bottom: 2px;
}

/* ✅ Dropdown button fix */
.warehouse-compact-grid .dropdown-toggle {
    height: 28px;
    padding: 3px 6px;
    font-size: 12px;
    border-radius: 4px;
    width: 100%;
}

/* spacing */
.warehouse-compact-grid .party-meta-field {
    gap: 2px;
}
        .bottom-right .calc-row {
            gap: 6px;
            margin-bottom: 4px;
        }
        .bottom-right .calc-label {
            min-width: 120px;
        }
        .bottom-right .total-input-large {
            width: 70%;
            font-size: 16px;
            padding: 8px 10px;
        }
        .image-gallery { display:grid; grid-template-columns:repeat(auto-fill,minmax(90px,1fr)); gap:10px; margin-top:10px; }
        .image-card { border:1px solid #dbe4f0; border-radius:10px; overflow:hidden; background:#fff; }
        .image-card img { width:100%; height:80px; object-fit:cover; }
        .image-card-body { padding:8px; }
        .image-card-name { font-size:11px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .compression-status { font-size: 12px; color: #64748b; }
        @media (max-width: 991px) {
            .header-section { grid-template-columns: 1fr; }
            .header-left { grid-template-columns: 1fr; }
            .header-right.w-25 { width: 100% !important; min-width: 0; }
            .warehouse-compact-grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <div class="container-fluid min-vh-100 d-flex flex-column p-0">
        <header class="tab-system-header">
            <div class="tab-strip-wrapper justify-content-between">
                <div class="d-flex align-items-end flex-grow-1 overflow-hidden">
                    <div id="tab-strip" class="tab-strip d-flex align-items-end"></div>
                    <button id="add-tab-btn" class="btn add-tab-btn" title="New Tab"><i class="bi bi-plus-lg"></i></button>
                </div>
                <div class="window-controls d-flex align-items-center px-2 gap-3">
                    <i id="calc-icon" class="fa-solid fa-calculator" title="Calculator"></i>
                    <i class="fa-solid fa-gear" title="Settings"></i>
                    <i class="fa-solid fa-xmark close-app-icon" title="Close Window"></i>
                </div>
            </div>
            <div class="browser-toolbar d-flex align-items-center px-3">
                <p class="mt-3 ms-3 mb-0 me-3 mb-2">Delivery Challan</p>
            </div>
        </header>

        <main id="content-area">
            <template id="form-template">
                <div class="invoice-container">
                    <div class="invoice-form invoice-card">
                        <div class="header-section">
                            <div class="header-left">
                                <div class="input-group party-selector-group">
                                    <div class="party-selector-panel">
                                        <div class="party-dropdown-wrapper" style="position: relative; display: inline-block;">
                                            <button class="btn btn-outline-secondary dropdown-toggle w-200 text-start" type="button" id="partyDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">Select Party</button>
                                            <div id="partyBalanceDisplay" style="color: #007bff; font-weight: 600; margin-top: 4px;">No party selected</div>
                                            <ul class="dropdown-menu w-100" aria-labelledby="partyDropdownBtn" id="partyDropdownMenu">
                                                <li class="dropdown-header-search px-2 py-2"><input type="text" class="form-control form-control-sm party-search-input" placeholder="Search party..." style="font-size: 13px;"></li>
                                                <li class="dropdown-header d-flex justify-content-between px-3"><span>Party Name</span><span>Opening Balance</span></li>
                                                @foreach($parties as $party)
                                                <li>
                                                    <a class="dropdown-item d-flex justify-content-between party-option" href="#" data-id="{{ $party->id }}" data-phone="{{ $party->phone }}" data-billing="{{ addslashes($party->billing_address ?? '') }}" data-shipping="{{ addslashes($party->shipping_address ?? '') }}" data-opening="{{ $party->opening_balance ?? 0 }}" data-type="{{ $party->transaction_type }}">
                                                        <span>{{ $party->name }}</span>
                                                        <span>{{ number_format($party->opening_balance ?? 0, 2) }}</span>
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

                                <div class="party-meta-grid">
                                    <div class="party-meta-field"><label>Phone No.</label><input type="text" class="meta-control phone-input"></div>
                                    <div class="party-meta-field address-field"><label>Billing Address</label><textarea class="meta-control billing-address" rows="2"></textarea></div>
                                    <div class="party-meta-field address-field"><label>Remarks</label><textarea class="meta-control description-input" rows="2" placeholder="Description"></textarea></div>
                                </div>
                            </div>

                            <div class="header-right w-25">
                                <div class="input-group"><span>Challan No.</span><input type="text" class="input-control underline-input bill-number" value="{{ $nextInvoiceNumber ?? 'Auto' }}" readonly></div>
                                <div class="input-group date-wrapper mt-2"><span>Invoice Date</span><input type="date" class="input-control underline-input invoice-date"></div>
                                <div class="input-group date-wrapper mt-2"><span>Due Date</span><input type="date" class="input-control underline-input due-date"></div>
                            </div>
                        </div>

                        <div class="alert alert-success d-none sale-success-msg"></div>

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
                                        <th style="min-width: 220px;">BROKER NAME</th>
                                        <th style="min-width: 180px;">BROKER PHONE</th>
                                        <th>AMOUNT</th>
                                        <th class="add-col" style="position: relative;">
                                            <button type="button" class="btn-add-circle table-settings-btn"><i class="fa-solid fa-plus"></i></button>
                                            <div class="settings-box">
                                                <div class="settings-item"><input type="checkbox" class="check-category"><label>Item Category</label></div>
                                                <div class="settings-item"><input type="checkbox" class="check-item-code"><label>Item Code</label></div>
                                                <div class="settings-item"><input type="checkbox" class="check-description"><label>Description</label></div>
                                                <div class="settings-item"><input type="checkbox" class="check-discount"><label>Discount</label></div>
                                            </div>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="item-rows">
                                    <tr class="item-row">
                                        <td class="row-num"><span class="row-index-text">1</span><div class="delete-row-icon"><i class="fa-solid fa-trash-can"></i></div></td>
                                        <td><select class="form-select item-name"><option value="" selected disabled>Select Item</option>@foreach($items as $item)<option value="{{ $item->id }}" data-price="{{ $item->price }}" data-sale-price="{{ $item->sale_price }}" data-stock="{{ $item->opening_qty }}" data-location="{{ $item->location }}" data-label="{{ $item->name }}" data-rich-label="{{ $item->name }} | Sale: {{ $item->sale_price ?? $item->price ?? 0 }} | Stock: {{ $item->opening_qty ?? 0 }} | Location: {{ $item->location ?? '' }}" data-unit="{{ $item->unit }}">{{ $item->name }} | Sale: {{ $item->sale_price ?? $item->price ?? 0 }} | Stock: {{ $item->opening_qty ?? 0 }} | Location: {{ $item->location ?? '' }}</option>@endforeach</select></td>
                                        <td class="col-category d-none"><input type="text" class="item-category" placeholder="Category"></td>
                                        <td class="col-item-code d-none"><input type="text" class="item-code" placeholder="Item Code"></td>
                                        <td class="col-description d-none"><input type="text" class="item-desc" placeholder="Description"></td>
                                        <td class="col-discount d-none"><input type="number" class="item-discount" value="0"></td>
                                        <td><input type="number" class="item-qty" value="1"></td>
                                        <td class="custom-size-td"><select class="item-unit"><option value="">Select Unit</option><option value="PCS">PCS (Pieces)</option><option value="BOX">BOX</option><option value="PACK">PACK</option><option value="SET">SET</option><option value="KG">KG (Kilogram)</option><option value="G">Gram</option><option value="M">Meter</option><option value="FT">Feet</option><option value="L">Liter</option><option value="ML">Milliliter</option></select></td>
                                        <td><input type="number" class="item-price" value="0"></td>
                                        <td class="broker-cell">
                                            <select class="meta-control broker-select">
                                                <option value="">Select Broker</option>
                                                @foreach($brokers as $broker)
                                                    <option value="broker-{{ $broker->id }}" data-id="{{ $broker->id }}" data-name="{{ $broker->name }}" data-phone="{{ $broker->phone }}" data-source="broker">
                                                        {{ $broker->name }}{{ $broker->phone ? ' | ' . $broker->phone : '' }}
                                                    </option>
                                                @endforeach
                                                @foreach($parties as $party)
                                                    <option value="party-{{ $party->id }}" data-id="" data-name="{{ $party->name }}" data-phone="{{ $party->phone }}" data-source="party">
                                                        {{ $party->name }}{{ $party->phone ? ' | ' . $party->phone : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <input type="hidden" class="broker-id" name="broker_id">
                                            <input type="hidden" class="broker-name-input" value="">
                                        </td>
                                        <td class="broker-cell"><input type="text" class="meta-control broker-phone-input"></td>
                                        <td class="col-amount"><input type="text" class="item-amount" value="0" readonly></td>
                                        <td class="add-col"></td>
                                    </tr>
                                </tbody>
                            </table>
                            <div class="table-footer">
                                <button type="button" class="btn-add-row add-row-btn">ADD ROW</button>
                                <div class="footer-totals">
                                    <div><span class="total-label">TOTAL QTY</span><span class="total-qty">0</span></div>
                                    <div><span class="total-label">TOTAL AMOUNT</span><span class="total-base-amount">0</span></div>
                                </div>
                            </div>
                        </div>

                        <div class="bottom-section">
                            <div class="bottom-left">


                                <button type="button" class="btn-action-light w-50 add-description">
                                    <i class="fa-solid fa-align-left"></i>
                                    ADD DESCRIPTION
                                </button>

                                <div class="description-pane d-none mt-2 w-50">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control description-input" rows="3" placeholder="Enter a remark or description"></textarea>
                                </div>

                                <button type="button" class="btn-action-light w-50 add-image">
                                    <i class="fa-solid fa-camera"></i>
                                    ADD IMAGE
                                </button>
                                <button type="button" class="btn-action-light w-50 add-document">
                                    <i class="fa-solid fa-align-left "></i>
                                    ADD DOCUMENT
                                </button>
                                <div class="warehouse-compact-grid">
                                    <div class="party-meta-field warehouse-field">
                                        <label>Warehouse Name</label>
                                        <div class="warehouse-dropdown-wrapper" style="position: relative; display: inline-block;">
                                            <button class="btn btn-outline-secondary dropdown-toggle w-200 text-start" type="button" id="warehouseDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">Select Warehouse</button>
                                            <ul class="dropdown-menu w-100" aria-labelledby="warehouseDropdownBtn" id="warehouseDropdownMenu">
                                                <li class="dropdown-header d-flex justify-content-between px-3"><span>Warehouse</span><span>Phone</span></li>
                                                @foreach($warehouses as $warehouse)
                                                <li>
                                                    <a class="dropdown-item d-flex justify-content-between warehouse-option" href="#" data-id="{{ $warehouse->id }}" data-name="{{ $warehouse->name }}" data-phone="{{ $warehouse->phone }}" data-handler-name="{{ $warehouse->handler_name }}" data-handler-phone="{{ $warehouse->handler_phone }}" data-user-id="{{ $warehouse->responsible_user_id }}">
                                                        <span>{{ $warehouse->name }}</span>
                                                        <span>{{ $warehouse->phone ?: '-' }}</span>
                                                    </a>
                                                </li>
                                                @endforeach
                                                <li><hr class="dropdown-divider"></li>
                                                <li><a class="dropdown-item text-primary add-warehouse-option" href="#">+ Add New Warehouse</a></li>
                                            </ul>
                                        </div>
                                        <input type="hidden" class="warehouse-id" name="warehouse_id">
                                    </div>
                                    <div class="party-meta-field"><label>Warehouse Phone</label><input type="text" class="meta-control warehouse-phone-input"></div>
                                    <div class="party-meta-field warehouse-handler-field"><label>Handler Name</label><input type="text" class="meta-control warehouse-handler-input"></div>
                                    <div class="party-meta-field warehouse-handler-phone-field"><label>Handler Phone</label><input type="text" class="meta-control warehouse-handler-phone-input"></div>
                                    <div class="party-meta-field vehicle-field"><label>Vehicle / Car</label><input type="text" class="meta-control vehicle-number-input"></div>
                                    <div class="party-meta-field destination-field"><label>Destination</label><input type="text" class="meta-control destination-input"></div>
                                    <div class="party-meta-field handler-user-field"><label>Related Handler User</label><select class="meta-control responsible-user-select"><option value="">Select related handler user</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }} @if($user->email) ({{ $user->email }}) @endif</option>@endforeach</select></div>
                                </div>
                                <div class="image-gallery"></div>
                                <div class="compression-status mt-2"></div>
                                <input type="file" class="d-none image-input" accept="image/*" multiple>
                            </div>
                            <div class="bottom-right">
                                <div class="calc-row"><div class="calc-label">Discount</div><div class="calc-inputs"><input type="number" class="mini-input discount-pct" placeholder="%"><span>-</span><input type="number" class="mini-input discount-rs" placeholder="Rs"></div></div>
                                <div class="calc-row" style="align-items: center;"><div class="calc-label" style="min-width: auto;"><input type="checkbox" class="custom-checkbox round-off-check" checked style="width: 16px; height: 16px; margin-right: 4px;"><label class="link-text" style="margin: 0; font-size: 12px;">Round Off</label></div><div class="calc-inputs"><input type="number" class="mini-input round-off-val" value="0" readonly style="width: 50px;"></div></div>
                                <div class="calc-row"><div class="calc-label">Delivery Expense</div><div class="calc-inputs"><input type="number" class="mini-input delivery-expense" placeholder="Rs" min="0" step="0.01"></div></div>
                                <div class="calc-row"><div class="calc-label">Brokerage Type</div><div class="calc-inputs"><select class="mini-input brokerage-type"><option value="">Select</option><option value="full">Poori Brokerage</option><option value="half">Aadhi Brokerage</option></select></div></div>
                                <div class="calc-row"><div class="calc-label">Brokerage Amount</div><div class="calc-inputs"><input type="number" class="mini-input brokerage-amount" placeholder="Rs" min="0" step="0.01"></div></div>
                                <div class="calc-row"><div class="calc-label">Tax</div><div class="calc-inputs"><select class="mini-input tax-select" style="width: 100px;"><option value="0">NONE</option><option value="5">GST@5%</option><option value="12">GST@12%</option><option value="18">GST@18%</option></select><span class="tax-amount-display">0</span></div></div>
                                <div class="final-total-group"><div class="calc-row" style="margin-bottom: 2px;"><div class="calc-label" style="font-weight: 700;">Total</div></div><input type="text" class="total-input-large grand-total" value="0" readonly></div>
                            </div>
                        </div>
                    </div>

                    <div class="sticky-actions">
                        <div class="btn-share"><button class="btn-share-main" type="button">Share</button><button class="btn-share-arrow" type="button"><i class="fa-solid fa-chevron-down"></i></button></div>
                        <button class="btn-save" type="button">Save</button>
                    </div>
                </div>
            </template>
        </main>
    </div>

    <div class="modal fade" id="warehouseModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header"><h5 class="modal-title">Add New Warehouse</h5><button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button></div>
                <div class="modal-body">
                    <form id="warehouseForm">
                        <div class="row g-3">
                            <div class="col-md-6"><label class="form-label">Warehouse Name</label><input type="text" name="name" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label">Warehouse Phone</label><input type="text" name="phone" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Handler Name</label><input type="text" name="handler_name" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Handler Phone</label><input type="text" name="handler_phone" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label">Related User</label><select name="responsible_user_id" class="form-select"><option value="">Select related handler user</option>@foreach($users as $user)<option value="{{ $user->id }}">{{ $user->name }} @if($user->email) ({{ $user->email }}) @endif</option>@endforeach</select></div>
                            <div class="col-md-6"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" class="btn btn-primary save-warehouse-btn">Save Warehouse</button></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="tabLimitModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content bg-dark text-dark border-secondary"><div class="modal-body text-center p-4"><i class="bi bi-exclamation-triangle text-warning display-4 mb-3"></i><h5>Maximum Limit Reached</h5><p>You can open a maximum of 10 transactions at a time.</p><button type="button" class="btn btn-primary px-4 mt-2" data-bs-dismiss="modal">OK</button></div></div></div></div>
    <div class="modal fade" id="closeConfirmModal" tabindex="-1" aria-hidden="true"><div class="modal-dialog modal-dialog-centered"><div class="modal-content bg-dark text-dark border-secondary"><div class="modal-header border-secondary"><h5 class="modal-title">Close Tab?</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button></div><div class="modal-body"><p>Are you sure you want to close this tab? Your delivery challan will not be saved.</p></div><div class="modal-footer border-secondary"><button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button><button type="button" id="confirm-close-btn" class="btn btn-danger">Close</button></div></div></div></div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    @php
        $challanPayload = isset($challan) ? $challan->load(['items', 'party', 'broker', 'challanDetail'])->toArray() : null;
        $duplicatePayload = isset($duplicateChallan) ? array_merge($duplicateChallan->load(['items', 'party', 'broker', 'challanDetail'])->toArray(), ['bill_number' => $nextInvoiceNumber]) : null;
    @endphp
    <script>
        window.items = @json($items ?? []);
        window.parties = @json($parties ?? []);
        window.brokers = @json($brokers ?? []);
        window.bankAccounts = @json($bankAccounts ?? []);
        window.responsibleUsers = @json($users ?? []);
        window.warehouses = @json($warehouses ?? []);
        window.warehouseStoreUrl = "{{ route('warehouses.store') }}";
        window.partyStoreUrl = "{{ route('parties.store') }}";
        window.partyGroupStoreUrl = "{{ route('party-groups.store') }}";
        window.itemRoutes = { index: "{{ url('dashboard/items') }}", store: "{{ url('dashboard/items') }}", unitsIndex: "{{ url('dashboard/items/units') }}", unitsStore: "{{ url('dashboard/items/units') }}", categoryStore: "{{ url('dashboard/items/category') }}" };
        window.saleStoreUrl = "{{ isset($challan) ? route('delivery-challan.update', $challan->id) : route('delivery-challan.store') }}";
        window.saleHttpMethod = "{{ isset($challan) ? 'PUT' : 'POST' }}";
        window.challanId = @json($challan->id ?? null);
        window.editSaleData = @json($challanPayload ?? $duplicatePayload);
        window.docType = 'delivery_challan';
    </script>
    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;"><div id="sale-toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body"></div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div></div>
    @include('components.modals.party-modal')
    @include('components.modals.item-modal')
    <script src="{{ asset('js/challanform_script.js') }}"></script>
    <script src="{{ asset('js/challanscript.js') }}"></script>
    <script src="{{ asset('js/shared-party-item-create.js') }}"></script>
</body>
</html>
