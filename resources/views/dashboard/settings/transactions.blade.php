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
        <a class="sidebar__nav-item" href="{{ route('settings.transaction-messages') }}" data-nav="transaction-message">TRANSACTION MESSAGE</a>
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

      <div class="main-grid">
        <!-- Column 1 (top): Application -->
        <section class="section section--application">
          <div class="section__title">Transaction Header</div>

          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Invoice/ Bill No.</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>





          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Add Time on Transaction</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Cash Sale by Default</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Billing Name of Parties</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Customer P.O. Details for transactions</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
        </section>

        <!-- Column 1 (bottom): More Transactions -->
        <section class="section section--more-transactions">
          <div class="section__title">More Transactions Features</div>

          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" checked />
            <span class="check-row__label">Quick Entry </span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" checked />
            <span class="check-row__label">Do not show Invoice Preview</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" checked />
            <span class="check-row__label">Enable passcode for transactions edit/delete</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Discount during payments</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Link payments to invoices</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" id="deliveryChallanCheck" />
            <span class="check-row__label">Due dates and payment terms</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" id="deliveryChallanCheck" />
            <span class="check-row__label">Show profit while making sale invoices</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

        </section>

        <!-- Column 2 (top): Multi Firm -->
        <section class="section section--multi-firm">
          <div class="section__title">Items table</div>
          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Inclusive/Exclusive Tax on Rate(Price/Unit)</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Display Purchase Price of items</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Show Last 5 Sale Prices of items</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Free item quantity</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" id="countCheckbox" />
            <span class="check-row__label">Count</span>
            <span class="ps-4 text-muted" id="changeTextBtn" style="font-size: 12px; transition: color 0.2s;">Change
              text</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
        </section>

        <!-- Column 2 (bottom): Stock Transfer Between Stores -->
        <section class="section section--stock-transfer">
          <div class="section__title pb-2">Transaction Prefixes</div>

          <div class="prefix-settings-wrapper">
            <!-- Firm Dropdown -->
            <div class="custom-fieldset mb-4">
              <label class="custom-label">Firm</label>
              <select class="custom-select prefix-select" id="firmSelect">
                <option value="Grocery Store" selected>Grocery Store</option>
              </select>
              <svg class="dropdown-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M4 6L8 10L12 6" stroke="#A1A1A1" stroke-width="1.5" stroke-linecap="round"
                  stroke-linejoin="round" />
              </svg>
            </div>

            <!-- Prefixes Container -->
            <div class="custom-fieldset custom-fieldset--large mb-0">
              <label class="custom-label mb-0" style="left: 14px;">Prefixes</label>

              <div class="row g-3 pt-1">
                <!-- Sale -->
                <div class="col-md-6">
                  <div class="custom-fieldset mb-0">
                    <label class="custom-label">Sale</label>
                    <select class="custom-select prefix-select">
                      <option value="None">None</option>
                      <option value="Standard">INV (for invoice, EST)</option>
                      <option value="Custom">GS (Firm name initials)</option>

                    </select>
                    <svg class="dropdown-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M4 6L8 10L12 6" stroke="#A1A1A1" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>
                  </div>
                </div>
                <!-- Credit Note -->
                <div class="col-md-6">
                  <div class="custom-fieldset mb-0">
                    <label class="custom-label">Credit Note</label>
                    <select class="custom-select prefix-select">
                      <option value="None">None</option>
                      <option value="Standard">INV (for invoice, EST)</option>
                      <option value="Custom">GS (Firm name initials)</option>
                    </select>
                    <svg class="dropdown-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M4 6L8 10L12 6" stroke="#A1A1A1" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>
                  </div>
                </div>
                <!-- Sale Order -->
                <div class="col-md-6">
                  <div class="custom-fieldset mb-0">
                    <label class="custom-label">Sale Order</label>
                    <select class="custom-select prefix-select">
                      <option value="None">None</option>
                      <option value="Standard">INV (for invoice, EST)</option>
                      <option value="Custom">GS (Firm name initials)</option>
                    </select>
                    <svg class="dropdown-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M4 6L8 10L12 6" stroke="#A1A1A1" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>
                  </div>
                </div>
                <!-- Purchase Order -->
                <div class="col-md-6">
                  <div class="custom-fieldset mb-0">
                    <label class="custom-label">Purchase Order</label>
                    <select class="custom-select prefix-select">
                      <option value="None">None</option>
                      <option value="Standard">INV (for invoice, EST)</option>
                      <option value="Custom">GS (Firm name initials)</option>
                    </select>
                    <svg class="dropdown-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M4 6L8 10L12 6" stroke="#A1A1A1" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>
                  </div>
                </div>
                <!-- Estimate -->
                <div class="col-md-6">
                  <div class="custom-fieldset mb-0">
                    <label class="custom-label">Estimate</label>
                    <select class="custom-select prefix-select">
                      <option value="None">None</option>
                      <option value="Standard">INV (for invoice, EST)</option>
                      <option value="Custom">GS (Firm name initials)</option>
                    </select>
                    <svg class="dropdown-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M4 6L8 10L12 6" stroke="#A1A1A1" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>
                  </div>
                </div>
                <!-- Proforma Invoice -->
                <div class="col-md-6">
                  <div class="custom-fieldset mb-0">
                    <label class="custom-label">Proforma Invoice</label>
                    <select class="custom-select prefix-select">
                      <option value="None">None</option>
                      <option value="Standard">INV (for invoice, EST)</option>
                      <option value="Custom">GS (Firm name initials)</option>
                    </select>
                    <svg class="dropdown-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M4 6L8 10L12 6" stroke="#A1A1A1" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>
                  </div>
                </div>
                <!-- Delivery Challan -->
                <div class="col-md-6">
                  <div class="custom-fieldset mb-0">
                    <label class="custom-label">Delivery Challan</label>
                    <select class="custom-select prefix-select">
                      <option value="None">None</option>
                      <option value="Standard">INV (for invoice, EST)</option>
                      <option value="Custom">GS (Firm name initials)</option>
                    </select>
                    <svg class="dropdown-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M4 6L8 10L12 6" stroke="#A1A1A1" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>
                  </div>
                </div>
                <!-- Payment In -->
                <div class="col-md-6">
                  <div class="custom-fieldset mb-0">
                    <label class="custom-label">Payment In</label>
                    <select class="custom-select prefix-select">
                      <option value="None">None</option>
                      <option value="Standard">INV (for invoice, EST)</option>
                      <option value="Custom">GS (Firm name initials)</option>
                    </select>
                    <svg class="dropdown-icon" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                      <path d="M4 6L8 10L12 6" stroke="#A1A1A1" stroke-width="1.5" stroke-linecap="round"
                        stroke-linejoin="round" />
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </section>

        <!-- Column 3 (top): Backup & History -->
        <section class="section section--backup">
          <div class="section__title">Taxes, Discount &amp; Total</div>

          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Transaction wise tax</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Transaction wise discount</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" id="roundTotalCheck" />
            <span class="check-row__label">Round of total</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <div id="roundTotalOptions" class="d-none ms-4 mb-3 mt-1">
            <select name="" id="" style="border:none; border-bottom:1px solid black; padding-bottom:2px;">
              <option value="nearest">Nearest</option>
              <option value="down-to" selected>Down to</option>
              <option value="up-to">Up to</option>
            </select>
            <span class="mx-3 text-dark">
              To
            </span>
            <select name="" id="" style="border:none; border-bottom:1px solid black; padding-bottom:2px;">
              <option value="nearest">1</option>
              <option value="down-to">10</option>
              <option value="up-to">50</option>
              <option value="up-to" selected>100</option>
              <option value="up-to">1000</option>
            </select>
          </div>




        </section>

        <section class="section section--customize ps-5">
          <div class="section__title">Billing Type</div>

          <label class="check-row check-row--sm">
            <input type="radio" class="radio-row__input" name="billingType" id="liteSale" checked />
            <span class="check-row__label">Lite Sale</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row check-row--sm">
            <input type="radio" class="radio-row__input" name="billingType" id="fullSale" />
            <span class="check-row__label">Full Sale</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>


        </section>
      </div>
    </main>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Add Firm</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body d-flex">
          <div class="col-6 py-5 d-flex justify-content-center align-items-center">
            <div id="addLogoContainer" style="cursor: pointer; position: relative;">
              <p id="addLogoText" class="h4 text-secondary border rounded p-4 text-center m-0">Add logo</p>
              <img id="logoPreview" class="d-none border rounded"
                style="max-width: 100%; max-height: 200px; object-fit: contain;" alt="" />
              <input type="file" id="logoInput" accept="image/*" style="display: none;">
            </div>
          </div>
          <div class="col-6 py-5">
            <form class="row g-3 needs-validation" novalidate>
              <div class="col-12">
                <label for="validationCustom01" class="form-label">Business name</label>
                <input type="text" class="form-control" id="validationCustom01" required>

              </div>
              <div class="col-12">
                <label for="validationCustom02" class="form-label">Phone No.</label>
                <input type="text" class="form-control" id="validationCustom02" required>

              </div>
              <div class="col-12">
                <label for="validationCustomUsername" class="form-label">Email ID</label>
                <div class="input-group has-validation">
                  <input type="email" class="form-control" id="validationCustomUsername"
                    aria-describedby="inputGroupPrepend" required>
                  <div class="invalid-feedback">
                    Please choose a username.
                  </div>
                </div>
              </div>

            </form>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Change Text Modal -->
  <div class="modal fade" id="changeTextModal" tabindex="-1" aria-labelledby="changeTextModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
      <div class="modal-content" style="width: 100% !important;">
        <div class="modal-header">
          <h1 class="modal-title fs-5" id="changeTextModalLabel">Edit text</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="text" class="form-control" id="changeTextInput" placeholder="Enter new text" value="Count">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-primary">Save</button>
        </div>
      </div>
    </div>
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
    })();
  </script>
</body>

</html>
