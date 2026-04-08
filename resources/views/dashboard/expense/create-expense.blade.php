<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($saleReturn) ? 'Edit' : 'Create' }} Sale Return | Vyapar</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/expenseform_style.css') }}">
</head>

<body>
    <div class="container-fluid min-vh-100 d-flex flex-column p-0">
        <header class="tab-system-header">
            <div class="tab-strip-wrapper justify-content-between">
                <div class="d-flex align-items-end flex-grow-1 overflow-hidden">
                    <div id="tab-strip" class="tab-strip d-flex align-items-end"></div>
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
            <div class="browser-toolbar d-flex align-items-center px-3">
                <p class="mt-3 ms-3 mb-0 me-3 mb-2">Sale Return / Credit Note</p>
            </div>
        </header>

        <main id="content-area">
            <template id="form-template">
                <div class="invoice-container">
                    <div class="invoice-form invoice-card">
                        <div class="header-section">
                            <div class="header-left">
                                <div class="input-group">
                                    <select class="input-control party-select">
                                        <option value="" selected disabled>Select Party</option>
                                        @foreach($parties as $party)
                                            <option
                                                value="{{ $party->id }}"
                                                data-phone="{{ e($party->phone) }}"
                                                data-billing="{{ e($party->billing_address) }}"
                                                data-shipping="{{ e($party->shipping_address) }}"
                                            >
                                                {{ $party->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label class="party-label">Party *</label>
                                </div>

                                <div class="input-group mt-2">
                                    <input type="text" class="input-control phone-input" readonly>
                                    <label>Phone No.</label>
                                </div>

                                <div class="input-group mt-2">
                                    <textarea class="input-control billing-address" rows="2" readonly></textarea>
                                    <label>Billing Address</label>
                                </div>

                                <div class="input-group mt-2">
                                    <textarea class="input-control shipping-address" rows="2" readonly></textarea>
                                    <label>Shipping Address</label>
                                </div>
                            </div>

                            <div class="header-right w-25">
                                <div class="input-group">
                                    <span>Order No.</span>
                                    <input type="text" class="input-control underline-input bill-number" value="{{ $nextInvoiceNumber ?? 'Auto' }}" readonly>
                                </div>
                                <div class="input-group mt-2">
                                    <span>Order Date</span>
                                    <input type="date" class="input-control underline-input order-date">
                                </div>
                                <div class="input-group mt-2">
                                    <span>Due Date</span>
                                    <input type="date" class="input-control underline-input due-date">
                                </div>
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
                                        <th>AMOUNT</th>
                                        <th class="add-col" style="position: relative;">
                                            <button type="button" class="btn-add-circle table-settings-btn">
                                                <i class="fa-solid fa-plus"></i>
                                            </button>
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
                                    <tr class="item-row">
                                        <td class="row-num">
                                            <span class="row-index-text">1</span>
                                            <div class="delete-row-icon"><i class="fa-solid fa-trash-can"></i></div>
                                        </td>
                                        <td>
                                            <select class="form-select item-name">
                                                <option value="" selected disabled>Select Item</option>
                                                @foreach($items as $item)
                                                    <option value="{{ $item->id }}" data-price="{{ $item->price }}" data-sale-price="{{ $item->sale_price }}" data-unit="{{ $item->unit }}">{{ $item->name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="col-category d-none"><input type="text" class="item-category" placeholder="Category"></td>
                                        <td class="col-item-code d-none"><input type="text" class="item-code" placeholder="Item Code"></td>
                                        <td class="col-description d-none"><input type="text" class="item-desc" placeholder="Description"></td>
                                        <td class="col-discount d-none"><input type="number" class="item-discount" value="0"></td>
                                        <td><input type="number" class="item-qty" value="1"></td>
                                        <td class="custom-size-td">
                                            <select class="item-unit">
                                                <option>NONE</option>
                                                <option>PCS</option>
                                                <option>BOX</option>
                                            </select>
                                        </td>
                                        <td><input type="number" class="item-price" value="0"></td>
                                        <td class="col-amount"><input type="text" class="item-amount" value="0" readonly></td>
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

                        <div class="bottom-section">
                            <div class="bottom-left">
                                <div class="payment-section">
                                    <div class="payment-entry d-flex align-items-center gap-2 mb-2">
                                        <select class="input-control default-payment-type">
                                            <option value="" selected disabled>Select Payment Type</option>
                                            @foreach($bankAccounts as $bank)
                                                <option value="bank-{{ $bank->id }}">{{ $bank->display_with_account }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" class="input-control default-payment-amount d-none" placeholder="Amount" min="0" step="0.01">
                                        <input type="text" class="input-control default-payment-reference d-none" placeholder="Reference">
                                    </div>

                                    <div class="payment-entries"></div>

                                    <div class="payment-total d-flex justify-content-between align-items-center mt-2">
                                        <span class="text-muted">Total payment:</span>
                                        <span class="fw-bold payment-total-amount">0</span>
                                    </div>

                                    <a href="#" class="link-text add-payment-entry">+ Add Payment type</a>
                                </div>

                                <template id="payment-entry-template">
                                    <div class="payment-entry d-flex align-items-center gap-2 mb-2">
                                        <select class="input-control payment-type-entry">
                                            <option value="" selected disabled>Select Bank Account</option>
                                            @foreach($bankAccounts as $bank)
                                                <option value="bank-{{ $bank->id }}">{{ $bank->display_with_account }}</option>
                                            @endforeach
                                        </select>
                                        <input type="number" class="input-control payment-amount" placeholder="Amount" min="0" step="0.01">
                                        <input type="text" class="input-control payment-reference" placeholder="Reference">
                                        <button type="button" class="btn btn-outline-danger btn-sm remove-payment-entry" title="Remove">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </template>

                                <button type="button" class="btn-action-light w-50 add-description">
                                    <i class="fa-solid fa-align-left"></i>
                                    ADD DESCRIPTION
                                </button>
                                <button type="button" class="btn-action-light w-50 add-image">
                                    <i class="fa-solid fa-camera"></i>
                                    ADD IMAGE
                                </button>
                                <button type="button" class="btn-action-light w-50 add-document">
                                    <i class="fa-solid fa-file-lines"></i>
                                    ADD DOCUMENT
                                </button>

                                <div class="description-pane d-none mt-2">
                                    <label class="form-label">Description</label>
                                    <textarea class="form-control description-input" rows="3" placeholder="Enter a remark or description"></textarea>
                                </div>

                                <div class="image-upload-section mt-2">
                                    <div class="image-preview d-none">
                                        <img class="image-preview-img" src="" alt="Selected Image" />
                                        <div class="image-preview-actions mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-secondary replace-image">Replace</button>
                                            <button type="button" class="btn btn-sm btn-outline-danger remove-image">Remove</button>
                                        </div>
                                    </div>
                                    <div class="image-placeholder text-center p-3 border border-dashed rounded" style="cursor:pointer;">
                                        <div class="text-muted">Click to select an image</div>
                                        <div class="small text-muted">(PNG/JPG, up to 5MB)</div>
                                    </div>
                                    <div class="selected-document-name text-muted mt-2"></div>
                                </div>

                                <input type="file" class="d-none image-input" accept="image/*" />
                                <input type="file" class="d-none document-input" accept=".pdf,.doc,.docx" />
                            </div>

                            <div class="bottom-right">
                                <div class="calc-row">
                                    <div class="calc-label">Discount</div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input discount-pct" placeholder="%">
                                        <span>-</span>
                                        <input type="number" class="mini-input discount-rs" placeholder="Rs">
                                    </div>
                                </div>

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

                                <div class="calc-row">
                                    <div class="checkbox-group">
                                        <input type="checkbox" class="custom-checkbox round-off-check" checked>
                                        <label class="link-text">Round Off</label>
                                    </div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input round-off-val" value="0" readonly>
                                    </div>
                                </div>

                                <div class="final-total-group">
                                    <div class="calc-row" style="margin-bottom: 5px;">
                                        <div class="calc-label" style="font-weight: 700;">Total</div>
                                    </div>
                                    <input type="text" class="total-input-large grand-total" value="0" readonly>
                                </div>

                                <div class="calc-row">
                                    <div class="calc-label">Received Amount</div>
                                    <div class="calc-inputs">
                                        <input type="number" class="mini-input advance-amount" value="0" readonly>
                                    </div>
                                </div>

                                <div class="calc-row">
                                    <div class="calc-label">Balance</div>
                                    <div class="calc-inputs">
                                        <span class="fw-bold balance-amount">0</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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

    <div class="modal fade" id="closeConfirmModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content bg-dark text-dark border-secondary">
                <div class="modal-header border-secondary">
                    <h5 class="modal-title">Close Tab?</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to close this tab? Your sale return will not be saved.</p>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="confirm-close-btn" class="btn btn-danger">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    @if(isset($saleReturn))
        <script>
            window.items = @json($items ?? []);
            window.parties = @json($parties ?? []);
            window.bankAccounts = @json($bankAccounts ?? []);
            window.saleReturnStoreUrl = "{{ route('sale-return.update', $saleReturn->id) }}";
            window.saleReturnMethod = 'PUT';
            window.editSaleReturnData = @json($saleReturn->load(['items', 'payments'])->toArray());
            window.docType = 'sale_return';
        </script>
    @elseif(isset($duplicateSaleReturn))
        <script>
            window.items = @json($items ?? []);
            window.parties = @json($parties ?? []);
            window.bankAccounts = @json($bankAccounts ?? []);
            window.saleReturnStoreUrl = "{{ route('sale-return.store') }}";
            window.saleReturnMethod = 'POST';
            window.editSaleReturnData = @json(array_merge($duplicateSaleReturn->load(['items', 'payments'])->toArray(), ['bill_number' => $nextInvoiceNumber]));
            window.docType = 'sale_return';
        </script>
    @else
        <script>
            window.items = @json($items ?? []);
            window.parties = @json($parties ?? []);
            window.bankAccounts = @json($bankAccounts ?? []);
            window.saleReturnStoreUrl = "{{ route('sale-return.store') }}";
            window.saleReturnMethod = 'POST';
            window.editSaleReturnData = null;
            window.docType = 'sale_return';
        </script>
    @endif

    <div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1080;">
        <div id="sale-toast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body"></div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/expense/expenseform.js') }}"></script>
    <script src="{{ asset('js/expense/scriptexpense.js') }}"></script>
</body>

</html>


