<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VyaPar POS</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/posstyle.css') }}">
    <!-- Form Styles -->
    <link rel="stylesheet" href="{{ asset('css/pos1.css') }}">
    <style>

        html, body { height: 100%; }


        .container-fluid {
            height: 100vh;
        }


        .tab-strip-wrapper {
            height: auto !important;
        }

        #content-area {
            flex: 1;
            min-height: 0;
            overflow: hidden;
        }

        /* Tab panes should be allowed to shrink inside flex containers */
        .tab-pane {
            min-height: 0;
        }

        /* POS layout inside the active tab */
        .pos-body {
            height: 100%;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .pos-wrapper {
            height: 100% !important;
            min-height: 0;
            flex: 1;
        }

        .pos-left,
        .pos-right {
            min-height: 0;
        }

        /* Make the table scroller fill the available space */
        .pos-table-scroll {
            height: 100% !important;
        }

        /* Shortcut feedback */
        #shortcut-feedback {
            position: fixed;
            left: 16px;
            bottom: 16px;
            z-index: 2000;
            background: rgba(15, 23, 42, 0.92);
            color: #fff;
            padding: 10px 12px;
            border-radius: 10px;
            font-size: 0.9rem;
            box-shadow: 0 8px 22px rgba(0, 0, 0, 0.25);
            opacity: 0;
            transform: translateY(8px);
            pointer-events: none;
            transition: opacity 140ms ease, transform 140ms ease;
        }

        #shortcut-feedback.show {
            opacity: 1;
            transform: translateY(0);
        }

        .shortcut-flash {
            outline: 3px solid rgba(56, 189, 248, 0.95);
            outline-offset: 2px;
        }

        .shortcut-btn {
            border: none;
            background: transparent;
            padding: 0;
            color: inherit;
            text-decoration: none;
            cursor: pointer;
            line-height: inherit;
        }
    </style>
</head>

<body>

    <div class="container-fluid d-flex flex-column p-0">
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

        </header>

        <!-- Content Area -->
        <main id="content-area" class="">
            <!-- Tab contents will be dynamically inserted here
            <button id="global-save-btn" class="btn btn-primary position-absolute bottom-0 end-0 m-4 shadow-lg z-3">
                <i class="bi bi-save me-2"></i>Save
            </button> -->

            <!-- TabManager will copy this template into a .tab-pane -->
            <template id="form-template">
                <div class="pos-body">
                <div class="pos-wrapper">
                    <div class="pos-left">
                        <div class="pos-search mb-3">
                            <div class="input-group input-group-lg">
                                <input id="pos-search-input" type="text" class="form-control border-primary"
                                    placeholder="Scan or search by item code, model no or item name" autofocus />
                            </div>
                        </div>

                        <div class="pos-table-wrapper card shadow-sm border-0">
                            <div class="card-body p-0">
                                <div class="table-responsive pos-table-scroll">
                                    <table class="table mb-0 align-middle table-clean pos-table">
                                        <thead class="table-light sticky-top">
                                            <tr>
                                                <th style="width: 40px;">#</th>
                                                <th>ITEM CODE</th>
                                                <th>ITEM NAME</th>
                                                <th style="width: 80px;">QTY</th>
                                                <th style="width: 80px;">UNIT</th>
                                                <th class="text-end" style="width: 130px;">PRICE/UNIT (Rs)</th>
                                                <th class="text-end" style="width: 130px;">TOTAL (Rs)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td colspan="7" class="text-center text-muted py-4">
                                                    Start scanning items to add them to the bill.
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="pos-shortcuts card shadow-sm border-0 mt-3">
                            <div class="card-body p-2">
                                <div class="shortcut-grid">
                                    <button type="button" class="btn btn-shortcut" data-shortcut="F2" data-shortcut-label="Change Quantity">
                                        Change Quantity <span>[F2]</span>
                                    </button>
                                    <button type="button" class="btn btn-shortcut" data-shortcut="F3" data-shortcut-label="Item Discount">
                                        Item Discount <span>[F3]</span>
                                    </button>
                                    <button type="button" class="btn btn-shortcut" data-shortcut="F4" data-shortcut-label="Order Discount">
                                        Order Discount <span>[F4]</span>
                                    </button>
                                    <button type="button" class="btn btn-shortcut" data-shortcut="F6" data-shortcut-label="Delete Item">
                                        Delete Item <span>[F6]</span>
                                    </button>
                                    <button type="button" class="btn btn-shortcut" data-shortcut="F8" data-shortcut-label="Save Bill">
                                        Save Bill <span>[F8]</span>
                                    </button>
                                    <button type="button" class="btn btn-shortcut" data-shortcut="F9" data-shortcut-label="Hold Bill">
                                        Hold Bill <span>[F9]</span>
                                    </button>
                                    <button type="button" class="btn btn-shortcut" data-shortcut="F10" data-shortcut-label="Recent Bills">
                                        Recent Bills <span>[F10]</span>
                                    </button>
                                    <button type="button" class="btn btn-shortcut" data-shortcut="F11" data-shortcut-label="Select Customer">
                                        Select Customer <span>[F11]</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pos-right">
                        <div class="card shadow-sm border-0 mb-3">
                            <div class="card-body">
                                <div class="row g-2 mb-3">
                                    <div class="col-12">
                                        <label class="form-label form-label-sm mb-1">Date</label>
                                        <input type="date" class="form-control form-control-sm" />
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label form-label-sm mb-1">Customer [F11]</label>
                                        <div class="dropdown">
                                            <input type="text" class="form-control form-control-sm dropdown-toggle"
                                                id="customerDropdownToggle"
                                                data-bs-toggle="dropdown" aria-expanded="false"
                                                placeholder="Search customer by Name or Phone No" />
                                            <ul class="dropdown-menu w-100 shadow-sm mt-1">
                                                <li><a class="dropdown-item text-primary fw-bold" href="#"
                                                        data-bs-toggle="modal" data-bs-target="#addCustomerModal">+ Add
                                                        Customer</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <div class="pos-summary-card p-3 rounded-3 mb-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="text-muted">Total:</span>
                                        <span class="fw-bold fs-5">Rs 0.00</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-1 small">
                                        <span>Items:</span>
                                        <span>0</span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-3 small">
                                        <span>Quantity:</span>
                                        <span>0</span>
                                    </div>
                                    <button type="button" class="btn btn-link p-0 small text-decoration-none"
                                        data-shortcut="CTRL+F" data-shortcut-label="Full Breakup">
                                        Full Breakup [Ctrl+F]
                                    </button>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="form-label form-label-sm mb-1">Payment Mode</label>
                                        <select class="form-select form-select-sm">
                                            <option>Cash</option>
                                            <option>Card</option>
                                            <option>UPI</option>
                                            <option>Credit</option>
                                        </select>
                                    </div>
                                    <div class="col-6">
                                        <label class="form-label form-label-sm mb-1">Amount Received</label>
                                        <input type="number" class="form-control form-control-sm" placeholder="0.00" />
                                    </div>
                                </div>

                                <div class="pos-change mb-2 d-flex justify-content-between mb-4">
                                    <div class="small text-muted mb-1">Change to Return:</div>
                                    <div class="fs-5 fw-bold text-dark">Rs 0.00</div>
                                </div>

                                <button type="button" class="btn btn-save-print w-100 mb-2"
                                    data-shortcut="CTRL+P" data-shortcut-label="Save & Print Bill">
                                    Save &amp; Print Bill [Ctrl+P]
                                </button>
                                <button type="button" class="btn btn-save-print-dark w-100 mb-2"
                                    data-shortcut="CTRL+M" data-shortcut-label="Other Credit/Payments">
                                    Other Credit/ Payments [Ctrl+M]
                                </button>

                                <div class="d-flex justify-content-between small text-muted">
                                    <button type="button" class="shortcut-btn" data-shortcut="CTRL+T" data-shortcut-label="New Bill">New Bill [Ctrl+T]</button>
                                    <button type="button" class="shortcut-btn" data-shortcut="CTRL+M" data-shortcut-label="Items Master">Items Master [Ctrl+M]</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                </div>
            </template>

            <!-- Add Customer Modal -->
                <div class="modal fade custom-modal" id="addCustomerModal" tabindex="-1"
                    aria-labelledby="addCustomerModalLabel" aria-hidden="true" data-bs-keyboard="false"
                    data-bs-backdrop="static">
                    <div class="modal-dialog modal-dialog-centered" style="max-width: 600px;">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="addCustomerModalLabel">Add Customer</h5>
                                <button type="button" class="btn-close-custom" id="closeAddCustomerBtn">
                                    <span aria-hidden="true">&times; [Esc]</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form id="addCustomerForm">
                                    <div class="customer-form-grid">

                                        <div class="form-group">
                                            <label>Customer Name <span class="required">*</span></label>
                                            <input type="text" class="custom-input" placeholder="Customer Name"
                                                autofocus />
                                        </div>

                                        <div class="form-group">
                                            <label>Phone Number</label>
                                            <input type="text" class="custom-input" placeholder="Phone Number" />
                                        </div>

                                        <div class="form-group">
                                            <label>Billing Address</label>
                                            <textarea class="custom-input" id="billingAddress"
                                                placeholder="Billing Address"></textarea>
                                        </div>

                                        <div class="form-group">
                                            <label>Shipping Address</label>
                                            <textarea class="custom-input" id="shippingAddress"
                                                placeholder="Shipping Address"></textarea>
                                        </div>

                                        <div class="empty-cell"></div>

                                        <div class="form-group checkbox-group">
                                            <label class="custom-checkbox">
                                                <input type="checkbox" id="sameAsBilling" />
                                                Same as billing address
                                            </label>
                                        </div>

                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn-save bg-success">Save</button>
                                <button type="button" class="btn-cancel" id="cancelAddCustomerBtn">Cancel</button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const billingAddress = document.getElementById('billingAddress');
                    const shippingAddress = document.getElementById('shippingAddress');
                    const sameAsBilling = document.getElementById('sameAsBilling');

                    if (sameAsBilling) {
                        sameAsBilling.addEventListener('change', (e) => {
                            if (e.target.checked) {
                                shippingAddress.value = billingAddress.value;
                                shippingAddress.disabled = true;
                            } else {
                                shippingAddress.value = '';
                                shippingAddress.disabled = false;
                            }
                        });

                        billingAddress.addEventListener('input', () => {
                            if (sameAsBilling.checked) {
                                shippingAddress.value = billingAddress.value;
                            }
                        });
                    }

                    const modalEl = document.getElementById('addCustomerModal');
                    const cancelBtn = document.getElementById('cancelAddCustomerBtn');
                    const closeBtn = document.getElementById('closeAddCustomerBtn');

                    const hideModal = () => {
                        const bsModal = bootstrap.Modal.getInstance(modalEl);
                        if (bsModal) {
                            bsModal.hide();
                        }
                    };

                    if (cancelBtn) cancelBtn.addEventListener('click', hideModal);
                    if (closeBtn) closeBtn.addEventListener('click', hideModal);

                    // Escape key listener on document, since focus might not be on modal
                    document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && modalEl.classList.contains('show')) {
                            hideModal();
                        }
                    });
                });
            </script>
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

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Form Logic -->

    <!-- Custom JS -->
    <script src="{{ asset('js/posscript.js') }}"></script>

    <!-- Keyboard shortcuts wiring (F2/F3/... and Ctrl+P etc.) -->
    <div id="shortcut-feedback" aria-live="polite"></div>
    <script>
        (function () {
            const activePane = () => document.querySelector('.tab-pane.active') || document;
            const searchInput = () => activePane().querySelector('#pos-search-input');
            const customerToggle = () => activePane().querySelector('#customerDropdownToggle');

            let feedbackTimer = null;

            function showFeedback(text) {
                const el = document.getElementById('shortcut-feedback');
                if (!el) return;
                el.textContent = text;
                el.classList.add('show');
                if (feedbackTimer) clearTimeout(feedbackTimer);
                feedbackTimer = setTimeout(() => el.classList.remove('show'), 1200);
            }

            function flashShortcuts(keyId) {
                const targets = activePane().querySelectorAll('[data-shortcut="' + keyId + '"]');
                if (!targets || !targets.length) return;
                targets.forEach(t => t.classList.add('shortcut-flash'));
                setTimeout(() => targets.forEach(t => t.classList.remove('shortcut-flash')), 320);
            }

            function openCustomerDropdown() {
                const toggle = customerToggle();
                if (!toggle) return;
                toggle.focus?.();
                toggle.dispatchEvent(new MouseEvent('click', { bubbles: true, cancelable: true }));
            }

            function activateShortcut(keyId) {
                // UI feedback + highlight
                const targets = activePane().querySelectorAll('[data-shortcut="' + keyId + '"]');
                let label = keyId;
                if (targets && targets.length && targets[0].dataset.shortcutLabel) {
                    label = targets[0].dataset.shortcutLabel;
                }
                showFeedback(label + ' [' + keyId + ']');
                flashShortcuts(keyId);

                // Basic UI focus/actions so shortcuts do something visible
                switch (keyId) {
                    case 'F11':
                        openCustomerDropdown();
                        break;
                    case 'CTRL+T':
                        // New bill -> focus the search box for the next transaction
                        searchInput()?.focus?.();
                        break;
                    default:
                        // For most POS shortcuts we at least keep focus on the search field
                        searchInput()?.focus?.();
                        break;
                }
            }

            // Click on shortcut buttons should also work
            document.addEventListener('click', (e) => {
                const btn = e.target.closest('[data-shortcut]');
                if (!btn) return;
                activateShortcut(btn.getAttribute('data-shortcut'));
            });

            // Keyboard shortcuts
            document.addEventListener('keydown', (e) => {
                if (e.defaultPrevented) return;

                // Ignore while typing in inputs except function keys
                const isTyping =
                    document.activeElement &&
                    (document.activeElement.tagName === 'INPUT' ||
                        document.activeElement.tagName === 'TEXTAREA' ||
                        document.activeElement.isContentEditable);

                // F-keys should work regardless
                const code = e.code;
                const fKeys = new Set(['F2', 'F3', 'F4', 'F6', 'F8', 'F9', 'F10', 'F11']);
                const isFKey = fKeys.has(code);

                if (isFKey) {
                    e.preventDefault();
                    activateShortcut(code);
                    return;
                }

                const ctrl = e.ctrlKey || e.metaKey;
                if (!ctrl) return;

                const k = (e.key || '').toLowerCase();
                if (k === 'p') {
                    e.preventDefault();
                    activateShortcut('CTRL+P');
                    return;
                }
                if (k === 'm') {
                    e.preventDefault();
                    activateShortcut('CTRL+M');
                    return;
                }
                if (k === 'f') {
                    e.preventDefault();
                    activateShortcut('CTRL+F');
                    return;
                }
                if (k === 't') {
                    e.preventDefault();
                    activateShortcut('CTRL+T');
                    return;
                }
            });
        })();
    </script>
</body>

</html>
