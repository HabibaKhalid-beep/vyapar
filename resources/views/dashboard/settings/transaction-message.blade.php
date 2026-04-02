<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Settings Dashboard</title>

  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Font Awesome (required icon class names like fa-pencil-alt, fa-times, fa-crown) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />

  <link href="{{ asset('css/setting/styles.css') }}" rel="stylesheet" />
</head>

<body>
  <div class="settings-layout">
    <aside class="sidebar">
      <div class="sidebar__header">
        <div class="sidebar__title">Settings</div>
        <i class="fa fa-search sidebar__search" aria-hidden="true"></i>
      </div>

      <nav class="sidebar__nav" aria-label="Settings navigation">
        <a class="sidebar__nav-item " href="{{ route('settings.general') }}" data-nav="general">GENERAL</a>
        <a class="sidebar__nav-item  is-active" href="{{ route('settings.transactions') }}" data-nav="transaction">TRANSACTION</a>
        <a class="sidebar__nav-item" href="{{ route('settings.print-layout') }}" data-nav="print">PRINT</a>
        <a class="sidebar__nav-item" href="{{ route('settings.taxes') }}" data-nav="taxes">TAXES</a>
        <a class="sidebar__nav-item" href="{{ route('settings.transaction-messages') }}" data-nav="transaction-message">TRANSACTION
          MESSAGE</a>
        <a class="sidebar__nav-item" href="{{ route('settings.parties') }}" data-nav="party">PARTY</a>
        <a class="sidebar__nav-item" href="{{ route('settings.items') }}" data-nav="item">ITEM</a>
        <a class="sidebar__nav-item" href="#" data-nav="service-reminders">
          <span>SERVICE REMINDERS</span>
          <i class="fa fa-crown sidebar__crown" aria-hidden="true"></i>
        </a>
      </nav>
    </aside>

    <main class="main-content">
      <!-- <button class="main-close" type="button" aria-label="Close">
        <i class="fa fa-times" aria-hidden="true"></i>
      </button> -->

      <div class="main-grid" style="display: block;">
        <style>
          .main-content {
            background-color: #F8FAFC !important;
          }

          .dashed-border {
            border: 1.5px dashed #3B82F6 !important;
            background-color: #eff6ff !important;
          }

          .dashed-border:focus {
            border-style: solid !important;
          }

          .info-icon-gray {
            color: #fff;
            background-color: #cbd5e1;
            border-radius: 50%;
            font-size: 10px;
            width: 14px;
            height: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            line-height: 1;
            cursor: pointer;
          }

          .wa-bubble::before {
            content: "";
            position: absolute;
            top: 0;
            left: -8px;
            width: 0;
            height: 0;
            border-top: 10px solid #DCFCE7;
            border-left: 10px solid transparent;
          }

          .login-btn {
            background-color: #e0f2fe;
            color: #0284c7;
            transition: background-color 0.2s ease;
            border: none;
          }

          .login-btn:hover {
            background-color: #bae6fd;
          }

          .custom-check .form-check-input {
            width: 1.15em;
            height: 1.15em;
            cursor: pointer;
            border-color: #cbd5e1;
            transition: background-color 0.15s ease-in-out, border-color 0.15s ease-in-out;
          }

          .custom-check .form-check-input:checked {
            background-color: #0d6efd;
            border-color: #0d6efd;
          }

          .custom-check-label {
            color: #64748b;
            cursor: pointer;
          }

          .red-dot {
            position: absolute;
            top: -2px;
            right: -2px;
            width: 6px;
            height: 6px;
            background-color: #ef4444;
            border-radius: 50%;
            border: 1px solid #fff;
          }

          .info-icon-wrapper {
            position: relative;
            display: inline-block;
          }
        </style>

        <div class="container-fluid p-0">
          <div class="row">
            <!-- Left Column: Settings -->
            <div class="col-md-7 border-end pe-4" style="border-color: #E2E8F0 !important;">
              <h4 class="mb-4 text-secondary fw-semibold" style="color: #475569 !important;">WhatsApp Settings</h4>

              <!-- Section 1: Select Message Type -->
              <div class="card mb-4 border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                  <h6 class="fw-bold mb-2" style="color: #4b5563;">Select Message Type</h6>
                  <hr class="opacity-25 mb-4">

                  <div class="mb-4">
                    <select id="transactionType" class="form-select w-50" style="color: #334155;">
                      <option value="sales">Sales (Invoice/Bill)</option>
                      <option value="purchase">Purchase (Bill)</option>
                      <option value="payment">Payment In/Out</option>
                    </select>
                  </div>

                  <!-- WhatsApp Card -->
                  <div class="d-inline-flex align-items-center border rounded-3 px-3 py-2 bg-white"
                    style="border-color: #cbd5e1 !important;">
                    <i class="fab fa-whatsapp text-success fs-4 me-2"></i>
                    <span class="fw-semibold me-3" style="color: #4b5563; font-size: 14px;">Send via Personal
                      WhatsApp</span>
                    <div class="vr me-3" style="opacity: 0.15; height: 24px;"></div>
                    <button class="btn btn-sm login-btn fw-bold px-3 py-1 rounded-pill">Login</button>
                  </div>
                </div>
              </div>

              <!-- Section 2: Message Recipient Settings -->
              <div class="card mb-4 border-0 shadow-sm rounded-3">
                <div class="card-body p-4">
                  <h6 class="fw-bold mb-2" style="color: #4b5563;">Message Recipient Settings</h6>
                  <hr class="opacity-25 mb-4">

                  <div class="row gy-3">
                    <div class="col-md-6 d-flex align-items-center">
                      <div class="form-check m-0 custom-check w-100">
                        <input type="checkbox" class="form-check-input mt-0" id="sendParty" checked>
                        <label class="form-check-label d-flex align-items-center custom-check-label w-100"
                          for="sendParty" style="font-size: 14.5px;">
                          Send Message to Party
                          <span class="info-icon-wrapper ms-2" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Message goes to Party number">
                            <i class="fas fa-info info-icon-gray"></i>
                          </span>
                        </label>
                      </div>
                    </div>

                    <div class="col-md-6 d-flex align-items-center">
                      <div class="form-check m-0 custom-check w-100">
                        <input type="checkbox" class="form-check-input mt-0" id="sendTxnUpdate" checked>
                        <label class="form-check-label d-flex align-items-center custom-check-label w-100"
                          for="sendTxnUpdate" style="font-size: 14.5px;">
                          Send Transaction Update Message
                          <span class="info-icon-wrapper ms-2" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="Alert when transaction modifies">
                            <i class="fas fa-info info-icon-gray"></i>
                            <span class="red-dot"></span>
                          </span>
                        </label>
                      </div>
                    </div>

                    <div class="col-md-12 d-flex align-items-center mt-3">
                      <div class="form-check m-0 custom-check">
                        <input type="checkbox" class="form-check-input mt-0" id="sendSelf">
                        <label class="form-check-label d-flex align-items-center custom-check-label" for="sendSelf"
                          style="font-size: 14.5px;">
                          Send Message Copy to Self
                          <span class="info-icon-wrapper ms-2" data-bs-toggle="tooltip" data-bs-placement="top"
                            title="BCC yourself on messages">
                            <i class="fas fa-info info-icon-gray"></i>
                          </span>
                        </label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Message Content (Remaining interactive fields) -->
              <div class="card mb-4 border-1 shadow-sm rounded-3"
                style="border-color: #e2e8f0 !important; border-radius: 8px !important;">
                <div class="card-body p-4">
                  <h6 class="fw-bold mb-2" style="color: #64748b;">Message Content</h6>
                  <hr class="opacity-25 mb-4">

                  <div class="row mb-4">
                    <div class="col-md-6 mb-3 mb-md-0">
                      <div class="form-check m-0 custom-check d-flex align-items-center">
                        <input type="checkbox" class="form-check-input mt-0 me-3" id="chkPartyBalance"
                          name="partyBalance">
                        <label class="form-check-label d-flex align-items-center custom-check-label w-100"
                          for="chkPartyBalance" style="font-size: 14.5px; color: #475569;">
                          Party Current Balance
                          <span class="info-icon-wrapper ms-2" data-bs-toggle="tooltip"
                            title="Include party's current balance">
                            <i class="fas fa-info info-icon-gray"></i>
                          </span>
                        </label>
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="form-check m-0 custom-check d-flex align-items-center">
                        <input type="checkbox" class="form-check-input mt-0 me-3" id="chkInvoiceLink" name="invoiceLink"
                          checked>
                        <label class="form-check-label d-flex align-items-center custom-check-label w-100"
                          for="chkInvoiceLink" style="font-size: 14.5px; color: #475569;">
                          Web invoice link in Message
                          <span class="info-icon-wrapper ms-2" data-bs-toggle="tooltip"
                            title="Include a link to the web invoice">
                            <i class="fas fa-info info-icon-gray"></i>
                          </span>
                        </label>
                      </div>
                    </div>
                  </div>


                </div>
              </div>

              <!-- Send Automatic Message for -->
              <div class="card mb-4 border-1 shadow-sm rounded-3"
                style="border-color: #e2e8f0 !important; border-radius: 8px !important;">
                <div class="card-body p-4">
                  <h6 class="fw-bold mb-2" style="color: #64748b;">Send Automatic Message for</h6>
                  <hr class="opacity-25 mb-4">

                  <div class="row row-cols-3 gy-4 px-1 pb-2">
                    <div class="col">
                      <div class="form-check custom-check d-flex align-items-center"><input
                          class="form-check-input me-3 mt-0 auto-msg-check" type="checkbox" id="auto1" name="sales"
                          checked><label class="form-check-label custom-check-label w-100"
                          style="font-size: 14.5px; color: #475569;" for="auto1">Sales</label></div>
                    </div>
                    <div class="col">
                      <div class="form-check custom-check d-flex align-items-center"><input
                          class="form-check-input me-3 mt-0 auto-msg-check" type="checkbox" id="auto2"
                          name="estimate"><label class="form-check-label custom-check-label w-100"
                          style="font-size: 14.5px; color: #475569;" for="auto2">Estimate</label></div>
                    </div>
                    <div class="col">
                      <div class="form-check custom-check d-flex align-items-center"><input
                          class="form-check-input me-3 mt-0 auto-msg-check" type="checkbox" id="auto3" name="paymentIn"
                          checked><label class="form-check-label custom-check-label w-100"
                          style="font-size: 14.5px; color: #475569;" for="auto3">Payment In</label></div>
                    </div>

                    <div class="col">
                      <div class="form-check custom-check d-flex align-items-center"><input
                          class="form-check-input me-3 mt-0 auto-msg-check" type="checkbox" id="auto4" name="purchase"
                          checked><label class="form-check-label custom-check-label w-100"
                          style="font-size: 14.5px; color: #475569;" for="auto4">Purchase</label></div>
                    </div>
                    <div class="col">
                      <div class="form-check custom-check d-flex align-items-center"><input
                          class="form-check-input me-3 mt-0 auto-msg-check" type="checkbox" id="auto5"
                          name="purchaseOrder"><label class="form-check-label custom-check-label w-100"
                          style="font-size: 14.5px; color: #475569;" for="auto5">Purchase Order</label></div>
                    </div>
                    <div class="col">
                      <div class="form-check custom-check d-flex align-items-center"><input
                          class="form-check-input me-3 mt-0 auto-msg-check" type="checkbox" id="auto6" name="paymentOut"
                          checked><label class="form-check-label custom-check-label w-100"
                          style="font-size: 14.5px; color: #475569;" for="auto6">Payment Out</label></div>
                    </div>

                    <div class="col">
                      <div class="form-check custom-check d-flex align-items-center"><input
                          class="form-check-input me-3 mt-0 auto-msg-check" type="checkbox" id="auto7" name="saleReturn"
                          checked><label class="form-check-label custom-check-label w-100"
                          style="font-size: 14.5px; color: #475569;" for="auto7">Sale Return</label></div>
                    </div>
                    <div class="col">
                      <div class="form-check custom-check d-flex align-items-center"><input
                          class="form-check-input me-3 mt-0 auto-msg-check" type="checkbox" id="auto8"
                          name="deliveryChallan"><label class="form-check-label custom-check-label w-100"
                          style="font-size: 14.5px; color: #475569;" for="auto8">Delivery Challan</label></div>
                    </div>
                    <div class="col">
                      <div class="form-check custom-check d-flex align-items-center"><input
                          class="form-check-input me-3 mt-0 auto-msg-check" type="checkbox" id="auto9"
                          name="expense"><label class="form-check-label custom-check-label w-100"
                          style="font-size: 14.5px; color: #475569;" for="auto9">Expense</label></div>
                    </div>

                    <div class="col">
                      <div class="form-check custom-check d-flex align-items-center"><input
                          class="form-check-input me-3 mt-0 auto-msg-check" type="checkbox" id="auto10"
                          name="purchaseReturn" checked><label class="form-check-label custom-check-label w-100"
                          style="font-size: 14.5px; color: #475569;" for="auto10">Purchase Return</label></div>
                    </div>
                    <div class="col">
                      <div class="form-check custom-check d-flex align-items-center"><input
                          class="form-check-input me-3 mt-0 auto-msg-check" type="checkbox" id="auto11"
                          name="proformaInvoice"><label class="form-check-label custom-check-label w-100"
                          style="font-size: 14.5px; color: #475569;" for="auto11">Proforma Invoice</label></div>
                    </div>
                    <div class="col">
                      <div class="form-check custom-check d-flex align-items-center"><input
                          class="form-check-input me-3 mt-0 auto-msg-check" type="checkbox" id="auto12"
                          name="transactionUpdate"><label class="form-check-label custom-check-label w-100"
                          style="font-size: 14.5px; color: #475569;" for="auto12">Transaction Update</label></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Right Column: Preview -->
            <div class="col-md-5 ps-4">
              <div class="sticky-top pt-2" style="top: 20px; z-index: 1;">
                <!-- Transaction Preview Header -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                  <span class="fw-bold" style="color: #64748b; font-size: 15px;">Transaction Type :</span>
                  <div class="dropdown">
                    <button
                      class="btn btn-light rounded-pill px-4 py-2 d-flex align-items-center fw-semibold border-0 shadow-none dropdown-toggle-custom"
                      type="button" id="previewTxnBtn" data-bs-toggle="dropdown" aria-expanded="false"
                      style="background-color: #e0f2fe; color: #1e293b; font-size: 14px;">
                      Sale Order
                      <i class="fas fa-chevron-down ms-4" style="color: #64748b; font-size: 12px;"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="previewTxnBtn"
                      style="border-radius: 8px; border: 1px solid #e2e8f0; font-size: 14px; box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1) !important;">
                      <li><a class="dropdown-item py-2 dropdown-txn-item" href="#" data-txn="sales">Sales
                          (Invoice/Bill)</a></li>
                      <li><a class="dropdown-item py-2 dropdown-txn-item" href="#" data-txn="estimate">Estimate</a></li>
                      <li><a class="dropdown-item py-2 dropdown-txn-item" href="#" data-txn="paymentIn">Payment In</a>
                      </li>
                      <li><a class="dropdown-item py-2 dropdown-txn-item" href="#" data-txn="purchase">Purchase</a></li>
                      <li><a class="dropdown-item py-2 dropdown-txn-item" href="#" data-txn="purchaseOrder">Purchase
                          Order</a></li>
                      <li><a class="dropdown-item py-2 dropdown-txn-item" href="#" data-txn="paymentOut">Payment Out</a>
                      </li>
                      <li><a class="dropdown-item py-2 dropdown-txn-item" href="#" data-txn="saleReturn">Sale Return</a>
                      </li>
                      <li><a class="dropdown-item py-2 dropdown-txn-item" href="#" data-txn="deliveryChallan">Delivery
                          Challan</a></li>
                      <li><a class="dropdown-item py-2 dropdown-txn-item" href="#" data-txn="expense">Expense</a></li>
                      <li><a class="dropdown-item py-2 dropdown-txn-item" href="#" data-txn="purchaseReturn">Purchase
                          Return</a></li>
                      <li><a class="dropdown-item py-2 dropdown-txn-item" href="#" data-txn="proformaInvoice">Proforma
                          Invoice</a></li>
                      <li><a class="dropdown-item py-2 dropdown-txn-item active bg-light text-dark fw-semibold"
                          style="background-color: #f1f5f9 !important;" href="#" data-txn="saleOrder">Sale Order</a>
                      </li>
                    </ul>
                  </div>
                </div>

                <hr class="mb-3" style="opacity: 0.1;">

                <h6 class="fw-semibold mb-2 text-secondary" style="font-size: 14px;">Edit Message</h6>

                <div class="mb-2">
                  <label class="form-label text-muted fw-semibold mb-1" style="font-size: 12px;">Header</label>
                  <textarea id="msgHeader" class="form-control form-control-sm" rows="1" style="color: #1E293B; font-size: 13px; resize: none;"></textarea>
                </div>
                <div class="mb-2">
                  <label class="form-label text-muted fw-semibold mb-1" style="font-size: 12px;">Body</label>
                  <textarea id="msgBody" class="form-control form-control-sm" rows="2" style="color: #1E293B; font-size: 13px; resize: none;"></textarea>
                </div>
                <div class="mb-3">
                  <label class="form-label text-muted fw-semibold mb-1" style="font-size: 12px;">Footer</label>
                  <textarea id="msgFooter" class="form-control form-control-sm dashed-border" rows="1"
                    style="color: #1E293B; font-size: 13px; resize: none;"></textarea>
                </div>

                <h4 class="mb-4 text-secondary fw-semibold invisible d-none">Message Preview</h4>

                <div class="preview-container p-4 rounded-4 shadow-sm"
                  style="background-color: #E6E2DA; background-image: url('https://user-images.githubusercontent.com/15075759/28719144-86dc0f70-73b1-11e7-911d-60d70fcded21.png'); background-blend-mode: overlay; min-height: 400px;">
                  <h6 class="text-center mb-4"><span
                      class="badge bg-secondary opacity-50 fw-normal py-2 px-3">Today</span></h6>
                  <!-- Whatsapp Bubble -->
                  <div class="wa-bubble px-3 py-2 rounded-3 shadow-sm mx-auto ms-0"
                    style="background-color: #DCFCE7; max-width: 90%; font-size: 14.5px; line-height: 1.5; color: #111827; position: relative;">
                    <div id="previewHeader" class="fw-bold mb-2"></div>
                    <div id="previewBody" class="mb-2" style="white-space: pre-wrap;"></div>
                    <div id="previewFooter" class="text-muted small border-top pt-2 mt-2"
                      style="border-color: rgba(0,0,0,0.06) !important; font-size: 13px;"></div>

                    <div class="text-end text-muted mt-1" style="font-size: 11px;">12:00 PM <i
                        class="fas fa-check-double text-info ms-1"></i></div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>





  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    (() => {
      const navItems = document.querySelectorAll('.sidebar__nav-item');
      // Auto-highlight active nav based on URL
      const currentPath = window.location.pathname.split('/').pop() || 'general.html';

      navItems.forEach((a) => {
        const href = a.getAttribute('href');
        if (href === currentPath) {
          a.classList.add('is-active');
        } else {
          a.classList.remove('is-active');
        }

        // Prevent default only for empty links
        if (href === '#') {
          a.addEventListener('click', (e) => e.preventDefault());
        }
      });

      const slider = document.getElementById('zoomRange');
      const applyBtn = document.getElementById('applyBtn');
      const ticks = document.querySelectorAll('.zoom-tick');

      if (slider && applyBtn) {
        const min = 70;
        const max = 130;
        const tickValues = Array.from(ticks).map((t) => Number(t.dataset.value));

        const clamp = (n, a, b) => Math.max(a, Math.min(b, n));
        const setActiveTick = (value) => {
          ticks.forEach((t) => t.classList.remove('is-active'));
          const match = [...ticks].find((t) => Number(t.dataset.value) === value);
          if (match) match.classList.add('is-active');
        };

        const setZoomFromSlider = () => {
          const value = clamp(Number(slider.value), min, max);
          const mainGrid = document.querySelector('.main-grid');
          if (mainGrid) {
            mainGrid.style.zoom = `${value}%`;
          }
          // Highlight the nearest labeled tick so the "displayed value" visually follows the knob.
          const nearest = tickValues.reduce((best, v) => {
            const db = Math.abs(best - value);
            const dv = Math.abs(v - value);
            return dv < db ? v : best;
          }, tickValues[0]);
          setActiveTick(nearest);
        };

        // Position ticks (absolute) based on min/max so labels align with knob positions.
        const positionTicks = () => {
          ticks.forEach((t) => {
            const v = Number(t.dataset.value);
            const leftPct = ((v - min) / (max - min)) * 100;
            t.style.left = `${leftPct}%`;
            t.style.transform = 'translateX(-50%)';
          });
        };

        positionTicks();
        setZoomFromSlider();
        slider.addEventListener('input', setZoomFromSlider);
        applyBtn.addEventListener('click', setZoomFromSlider);
      }

      // Multi Firm logic
      const multiFirmCheckbox = document.getElementById('multiFirmCheckbox');
      const addFirmBtn = document.getElementById('addFirmBtn');
      const multiFirmBox = document.getElementById('multiFirmBox');

      if (multiFirmCheckbox) {
        multiFirmCheckbox.addEventListener('change', (e) => {
          if (addFirmBtn) addFirmBtn.classList.toggle('d-none', !e.target.checked);
        });
      }

      if (addFirmBtn) {
        addFirmBtn.addEventListener('click', (e) => {
          e.preventDefault(); // Prevents the click from bubbling and unchecking the multi-firm label
        });
      }

      // Add Logo Upload Logic
      const logoContainer = document.getElementById('addLogoContainer');
      const logoInput = document.getElementById('logoInput');
      const logoPreview = document.getElementById('logoPreview');
      const logoText = document.getElementById('addLogoText');

      if (logoContainer && logoInput) {
        logoContainer.addEventListener('click', () => logoInput.click());

        logoInput.addEventListener('change', (e) => {
          if (e.target.files && e.target.files[0]) {
            const reader = new FileReader();
            reader.onload = function (evt) {
              logoPreview.src = evt.target.result;
              logoPreview.classList.remove('d-none');
              logoText.classList.add('d-none');
            };
            reader.readAsDataURL(e.target.files[0]);
          }
        });
      }

      // Delivery Challan Logic
      const deliveryChallanCheck = document.getElementById('deliveryChallanCheck');
      const deliveryChallanOptions = document.getElementById('deliveryChallanOptions');
      if (deliveryChallanCheck && deliveryChallanOptions) {
        deliveryChallanCheck.addEventListener('change', (e) => {
          deliveryChallanOptions.classList.toggle('d-none', !e.target.checked);
        });
      }

      // Round Total Logic
      const roundTotalCheck = document.getElementById('roundTotalCheck');
      const roundTotalOptions = document.getElementById('roundTotalOptions');
      if (roundTotalCheck && roundTotalOptions) {
        roundTotalCheck.addEventListener('change', (e) => {
          roundTotalOptions.classList.toggle('d-none', !e.target.checked);
        });
      }

      // Count Change Text Logic
      const countCheckbox = document.getElementById('countCheckbox');
      const changeTextBtn = document.getElementById('changeTextBtn');
      const changeTextModalEl = document.getElementById('changeTextModal');

      if (countCheckbox && changeTextBtn && changeTextModalEl) {
        const toggleChangeText = () => {
          if (countCheckbox.checked) {
            changeTextBtn.classList.remove('text-muted');
            changeTextBtn.classList.add('text-primary');
            changeTextBtn.style.cursor = 'pointer';
          } else {
            changeTextBtn.classList.add('text-muted');
            changeTextBtn.classList.remove('text-primary');
            changeTextBtn.style.cursor = 'default';
          }
        };

        // Initial state
        toggleChangeText();

        countCheckbox.addEventListener('change', toggleChangeText);

        changeTextBtn.addEventListener('click', (e) => {
          if (!countCheckbox.checked) return; // Do nothing if inactive
          e.preventDefault(); // Prevent bubbling up to the label!
          e.stopPropagation(); // Stop label from toggling checkbox again

          const changeTextModal = new bootstrap.Modal(changeTextModalEl);
          changeTextModal.show();
        });
      }

      // Prefix Settings Logic
      const prefixSelects = document.querySelectorAll('.prefix-select');
      prefixSelects.forEach(select => {
        const updateColor = () => {
          if (select.value === 'None') {
            select.style.color = '#757575'; // muted gray for None
          } else {
            select.style.color = '#212529'; // dark gray for other options
          }
        };
        updateColor();
        select.addEventListener('change', updateColor);
      });

      // WhatsApp Settings Logic
      const msgTemplates = {
        saleOrder: {
          header: "Dear Sir/Madam,",
          bodyText: "Thanks for placing order with us.\nOrder Amount: Rs. 792.00",
          footer: "We will notify you once dispatched."
        },
        sales: {
          header: "Dear Customer,",
          bodyText: "Your invoice number INV-1234 for amount Rs. 1500.00 has been generated.",
          footer: "Thank you for doing business with us.\nVyapar App"
        },
        purchase: {
          header: "Dear Vendor,",
          bodyText: "We have registered purchase bill PUR-5678 for amount Rs. 5000.00.",
          footer: "Thanks & Regards,\nVyapar App"
        },
        payment: {
          header: "Dear Sir/Madam,",
          bodyText: "Payment of Rs. 1500.00 has been successfully received/made.",
          footer: "Regards,\nVyapar App"
        },
        estimate: { header: "Dear Customer,", bodyText: "Estimate EST-100 for Rs. 500.00 generated.", footer: "Regards." },
        paymentIn: { header: "Dear Customer,", bodyText: "Payment received.", footer: "Regards." },
        purchaseOrder: { header: "Dear Vendor,", bodyText: "Purchase order created.", footer: "Regards." },
        paymentOut: { header: "Dear Vendor,", bodyText: "Payment made.", footer: "Regards." },
        saleReturn: { header: "Dear Customer,", bodyText: "Sale return processed.", footer: "Regards." },
        deliveryChallan: { header: "Dear Customer,", bodyText: "Delivery challan generated.", footer: "Regards." },
        expense: { header: "Dear Employee,", bodyText: "Expense recorded.", footer: "Regards." },
        purchaseReturn: { header: "Dear Vendor,", bodyText: "Purchase return created.", footer: "Regards." },
        proformaInvoice: { header: "Dear Customer,", bodyText: "Proforma invoice generated.", footer: "Regards." }
      };

      const waState = {
        type: 'saleOrder',
        chkBalance: true,
        chkLink: true
      };

      const transactionType = document.getElementById('transactionType');
      const previewTxnBtn = document.getElementById('previewTxnBtn');
      const chkPartyBalance = document.getElementById('chkPartyBalance');
      const chkInvoiceLink = document.getElementById('chkInvoiceLink');

      const msgHeaderObj = document.getElementById('msgHeader');
      const msgBodyObj = document.getElementById('msgBody');
      const msgFooterObj = document.getElementById('msgFooter');

      const prevHeader = document.getElementById('previewHeader');
      const prevBody = document.getElementById('previewBody');
      const prevFooter = document.getElementById('previewFooter');

      if (msgHeaderObj && prevHeader) {
        // Generate body text from state & template
        function generateBodyText() {
          const tpl = msgTemplates[waState.type] || msgTemplates['saleOrder'];
          let fullBodyText = tpl.bodyText;

          if (waState.chkBalance) {
            fullBodyText += "\n\nCurrent Balance: Rs. 2500.00 Dr";
          }
          if (waState.chkLink) {
            fullBodyText += "\n\nView Invoice: https://vyapar.app/inv/123xyz";
          }
          return fullBodyText;
        }

        // Render Preview from Textareas
        function renderPreview() {
          prevHeader.innerText = msgHeaderObj.value;
          prevBody.innerText = msgBodyObj.value;
          prevFooter.innerText = msgFooterObj.value;
        }

        // Load Template into Textareas
        function loadTemplate() {
          const tpl = msgTemplates[waState.type] || msgTemplates['saleOrder'];
          msgHeaderObj.value = tpl.header;
          msgBodyObj.value = generateBodyText();
          msgFooterObj.value = tpl.footer;
          renderPreview();
        }

        // Dropdown toggle sync function
        function syncPreviewDropdownBtn() {
          if (!previewTxnBtn) return;
          const item = document.querySelector(`.dropdown-txn-item[data-txn="${waState.type}"]`);
          if (item) {
            previewTxnBtn.innerHTML = `${item.innerText.trim()} <i class="fas fa-chevron-down ms-4" style="color: #64748b; font-size: 12px;"></i>`;
            document.querySelectorAll('.dropdown-txn-item').forEach(el => {
              el.classList.remove('bg-light', 'fw-semibold', 'active', 'text-dark');
              el.style.backgroundColor = '';
            });
            item.classList.add('bg-light', 'fw-semibold', 'active', 'text-dark');
            item.style.backgroundColor = '#f1f5f9';
          }
        }

        // Listeners for new Pill Dropdown
        document.querySelectorAll('.dropdown-txn-item').forEach(item => {
          item.addEventListener('click', (e) => {
            e.preventDefault();
            waState.type = item.getAttribute('data-txn');
            syncPreviewDropdownBtn();

            // Sync top dropdown if present
            if (transactionType && [...transactionType.options].some(o => o.value === waState.type)) {
              transactionType.value = waState.type;
            }
            loadTemplate();
          });
        });

        // Listeners for top dropdown (legacy fallback)
        if (transactionType) {
          transactionType.addEventListener('change', (e) => {
            waState.type = e.target.value;
            loadTemplate();
            syncPreviewDropdownBtn();
          });
        }

        // Listeners for checkboxes
        chkPartyBalance.addEventListener('change', (e) => {
          waState.chkBalance = e.target.checked;
          msgBodyObj.value = generateBodyText();
          renderPreview();
        });

        chkInvoiceLink.addEventListener('change', (e) => {
          waState.chkLink = e.target.checked;
          msgBodyObj.value = generateBodyText();
          renderPreview();
        });

        // Live update preview on textarea input
        msgHeaderObj.addEventListener('input', renderPreview);
        msgBodyObj.addEventListener('input', renderPreview);
        msgFooterObj.addEventListener('input', renderPreview);

        // Init Interactions & Tooltips
        loadTemplate();

        // Bootstrap tooltips init
        const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
        [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

        // Info Icons Click Logic
        document.querySelectorAll('.info-icon-wrapper').forEach(iconWrap => {
          iconWrap.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            const labelText = e.target.closest('label')?.innerText.trim() || 'Unknown Setting';
            console.log("Info icon clicked for:", labelText);
          });
        });

        // Configuration Logging
        const configState = {};
        const updateLogConfig = () => {
          document.querySelectorAll('.auto-msg-check, #chkPartyBalance, #chkInvoiceLink').forEach(chk => {
            configState[chk.name || chk.id] = chk.checked;
          });
          console.log("Current Configuration:", JSON.parse(JSON.stringify(configState)));
        };
        // Initialize log state silently
        document.querySelectorAll('.auto-msg-check, #chkPartyBalance, #chkInvoiceLink').forEach(chk => {
          configState[chk.name || chk.id] = chk.checked;
        });

        // Attach change listener for logging
        document.querySelectorAll('.auto-msg-check').forEach(chk => {
          chk.addEventListener('change', updateLogConfig);
        });
        if (chkPartyBalance) chkPartyBalance.addEventListener('change', updateLogConfig);
        if (chkInvoiceLink) chkInvoiceLink.addEventListener('change', updateLogConfig);
      }
    })();
  </script>
</body>

</html>
