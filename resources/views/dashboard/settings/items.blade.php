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
        <a class="sidebar__nav-item is-active" href="{{ route('settings.general') }}" data-nav="general">GENERAL</a>
        <a class="sidebar__nav-item" href="{{ route('settings.transactions') }}" data-nav="transaction">TRANSACTION</a>
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
          <div class="section__title">Item settings</div>

          <label class="check-row">
            <input type="checkbox" class="check-row__input" checked />
            <span class="check-row__label">Enable Item</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <div class="d-flex">
            <p>what do you sell?</p>
            <select name="" id="" class="form-control" style="width: 150px; height
           : 40px;">
              <option value="">Product</option>
              <option value="">Service</option>
              <option value="">Product/Service</option>
            </select>
          </div>



          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Barcode Scan</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Stock Maintainance</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Manufacturing</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Show low stock Dialog</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Item Unit</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Default Unit</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Item Category</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Party wise item rate</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" id="countCheckbox" />
            <span class="check-row__label">Description</span>
            <span class="ps-4 text-muted" id="changeTextBtn" style="font-size: 12px; transition: color 0.2s;">Change
              text</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <!-- Change Text Modal -->
          <div class="modal fade" id="changeTextModal" tabindex="-1" aria-labelledby="changeTextModalLabel"
            aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
              <div class="modal-content" style="width: 100% !important;">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="changeTextModalLabel">Edit text</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <input type="text" class="form-control" id="changeTextInput" placeholder="Enter new text"
                    value="Descritpion">
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-primary" data-bs-dismiss="modal">Cancel</button>
                  <button type="button" class="btn btn-primary">Save</button>
                </div>
              </div>
            </div>
          </div>
          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">item wise tax</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">item wise discount</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Update sale price from transaction</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <div class="d-flex">
            <div>
              <p class="m-0">Quantity</p>
              <p style="font-size: 9px;">(upto decimal places)</p>
            </div>
            <div class="ps-1">
              <input type="number" id="qtyDecimalInput" value="2" style="width: 35px;" max="4" min="1">
              <span id="qtyDecimalExample" style="font-size: 12px;">eg 0.00</span>
            </div>
          </div>
          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Whole Sale Price</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>
        </section>



        <!-- Column 2 (top): Multi Firm -->
        <section class="section section--multi-firm">

          <div class="section__title">Additional item fields</div>

          <style>
            .inv-section {
              margin-top: 30px;
              font-family: 'Inter', 'Roboto', sans-serif;
            }

            .inv-section:first-of-type {
              margin-top: 10px;
            }

            .inv-heading {
              font-size: 16px;
              font-weight: 600;
              color: #4B5563;
              margin-bottom: 15px;
              display: flex;
              align-items: center;
              gap: 8px;
            }

            .inv-info-icon {
              color: #9CA3AF;
              font-size: 14px;
            }

            .inv-row {
              display: flex;
              align-items: center;
              padding: 6px 0;
              margin-bottom: 12px;
            }

            .inv-row__left {
              display: flex;
              align-items: center;
              width: 140px;
              /* fixed width for alignment */
              flex-shrink: 0;
            }

            .inv-row__checkbox {
              width: 18px;
              height: 18px;
              margin-right: 12px;
              cursor: pointer;
            }

            .inv-row__label {
              font-size: 14px;
              color: #374151;
              display: flex;
              align-items: center;
              gap: 6px;
            }

            .inv-row__controls {
              display: flex;
              align-items: center;
              gap: 12px;
              flex-grow: 1;
              justify-content: flex-end;
              transition: all 0.2s ease;
            }

            .inv-input,
            .inv-select {
              border: 1px solid #E5E7EB;
              border-radius: 4px;
              padding: 8px 12px;
              font-size: 14px;
              outline: none;
              color: #111827;
              box-shadow: none;
              transition: all 0.2s ease;
              width: 100%;
            }

            .inv-input::placeholder,
            .inv-select:invalid {
              color: #9CA3AF;
            }

            .inv-row__controls>input,
            .inv-row__controls>select {
              max-width: 150px;
              /* responsive width constraint */
            }

            .inv-row__controls.is-disabled {
              background-color: #F9FAFB;
              opacity: 0.6;
              pointer-events: none;
              border-radius: 4px;
            }

            .inv-row__controls.is-disabled .inv-input,
            .inv-row__controls.is-disabled .inv-select {
              color: #9CA3AF;
              background-color: transparent;
            }
          </style>

          <!-- Section 1: MRP/Price -->
          <div class="inv-section">
            <div class="inv-heading">MRP/Price</div>
            <div class="inv-row">
              <div class="inv-row__left">
                <input type="checkbox" class="inv-row__checkbox inv-trigger" data-target="mrp-controls" />
                <span class="inv-row__label">MRP <i class="fa fa-info-circle inv-info-icon"
                    aria-hidden="true"></i></span>
              </div>
              <div class="inv-row__controls is-disabled" id="mrp-controls">
                <input type="text" class="inv-input" placeholder="MRP" disabled />
              </div>
            </div>
          </div>

          <!-- Section 2: Serial No. Tracking -->
          <div class="inv-section">
            <div class="inv-heading">Serial No. Tracking <i class="fa fa-info-circle inv-info-icon"
                aria-hidden="true"></i></div>
            <div class="inv-row">
              <div class="inv-row__left" style="width: 220px;">
                <input type="checkbox" class="inv-row__checkbox inv-trigger" data-target="serial-controls" />
                <span class="inv-row__label">Serial No./ IMEI No. etc</span>
              </div>
              <div class="inv-row__controls is-disabled" id="serial-controls">
                <input type="text" class="inv-input" placeholder="Serial No." disabled />
              </div>
            </div>
          </div>

          <!-- Section 3: Batch Tracking -->
          <div class="inv-section">
            <div class="inv-heading">Batch Tracking <i class="fa fa-info-circle inv-info-icon" aria-hidden="true"></i>
            </div>

            <div class="inv-row">
              <div class="inv-row__left">
                <input type="checkbox" class="inv-row__checkbox inv-trigger" data-target="batch-controls" />
                <span class="inv-row__label">Batch No.</span>
              </div>
              <div class="inv-row__controls is-disabled" id="batch-controls">
                <input type="text" class="inv-input" placeholder="Batch No." disabled />
              </div>
            </div>

            <div class="inv-row">
              <div class="inv-row__left">
                <input type="checkbox" class="inv-row__checkbox inv-trigger" data-target="exp-controls" />
                <span class="inv-row__label">Exp Date</span>
              </div>
              <div class="inv-row__controls is-disabled" id="exp-controls">
                <select class="inv-select" disabled>
                  <option value="" disabled selected hidden>mm/yy</option>
                  <option value="1">01/24</option>
                  <option value="2">02/24</option>
                </select>
                <input type="text" class="inv-input" placeholder="Exp. Date" disabled />
              </div>
            </div>

            <div class="inv-row">
              <div class="inv-row__left">
                <input type="checkbox" class="inv-row__checkbox inv-trigger" data-target="mfg-controls" />
                <span class="inv-row__label">Mfg Date</span>
              </div>
              <div class="inv-row__controls is-disabled" id="mfg-controls">
                <select class="inv-select" disabled>
                  <option value="" disabled selected hidden>dd/mm/yy</option>
                  <option value="1">01/01/24</option>
                  <option value="2">02/01/24</option>
                </select>
                <input type="text" class="inv-input" placeholder="Mfg. Date" disabled />
              </div>
            </div>

            <div class="inv-row">
              <div class="inv-row__left">
                <input type="checkbox" class="inv-row__checkbox inv-trigger" data-target="model-controls" />
                <span class="inv-row__label">Model No.</span>
              </div>
              <div class="inv-row__controls is-disabled" id="model-controls">
                <input type="text" class="inv-input" placeholder="Model No." disabled />
              </div>
            </div>

            <div class="inv-row">
              <div class="inv-row__left">
                <input type="checkbox" class="inv-row__checkbox inv-trigger" data-target="size-controls" />
                <span class="inv-row__label">Size</span>
              </div>
              <div class="inv-row__controls is-disabled" id="size-controls">
                <input type="text" class="inv-input" placeholder="Size" disabled />
              </div>
            </div>
          </div>
        </section>


        <!-- Column 3 (top): Backup & History -->
        <section class="section section--backup">
          <div class="section__title">Item Custom Fields</div>

          <!-- Button trigger modal -->
          <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#customFieldModal">
            <span class="text-primary">Additional Custom Fields ></span>
          </button>

          <!-- Modal -->
          <div class="modal fade" id="customFieldModal" tabindex="-1" aria-labelledby="exampleModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="exampleModalLabel">Add Custom Fields</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="custom-fields-grid">
                    <!-- Field 1 -->
                    <div class="cf-group is-disabled">
                      <div class="cf-title">Custom Field 1<span class="cf-asterisk">&ast;</span></div>
                      <div class="cf-input-row">
                        <input type="checkbox" name="cf_check_1" class="cf-checkbox cf-trigger" id="cf-check-1" />
                        <input type="text" name="custom_field_1" class="cf-input" id="cf-input-1" placeholder="Custom Field 1" disabled aria-disabled="true" />
                      </div>
                      <div class="cf-toggle-row">
                        <label class="cf-toggle">
                          <input type="checkbox" name="cf_toggle_1" id="cf-toggle-input-1" disabled aria-disabled="true" />
                          <span class="cf-slider"></span>
                        </label>
                        <span class="cf-toggle-text">Show in print</span>
                      </div>
                    </div>
                    <!-- Field 2 -->
                    <div class="cf-group is-disabled">
                      <div class="cf-title">Custom Field 2<span class="cf-asterisk">&ast;</span></div>
                      <div class="cf-input-row">
                        <input type="checkbox" name="cf_check_2" class="cf-checkbox cf-trigger" id="cf-check-2" />
                        <input type="text" name="custom_field_2" class="cf-input" id="cf-input-2" placeholder="Custom Field 2" disabled aria-disabled="true" />
                      </div>
                      <div class="cf-toggle-row">
                        <label class="cf-toggle">
                          <input type="checkbox" name="cf_toggle_2" id="cf-toggle-input-2" disabled aria-disabled="true" />
                          <span class="cf-slider"></span>
                        </label>
                        <span class="cf-toggle-text">Show in print</span>
                      </div>
                    </div>
                    <!-- Field 3 -->
                    <div class="cf-group is-disabled">
                      <div class="cf-title">Custom Field 3<span class="cf-asterisk">&ast;</span></div>
                      <div class="cf-input-row">
                        <input type="checkbox" name="cf_check_3" class="cf-checkbox cf-trigger" id="cf-check-3" />
                        <input type="text" name="custom_field_3" class="cf-input" id="cf-input-3" placeholder="Custom Field 3" disabled aria-disabled="true" />
                      </div>
                      <div class="cf-toggle-row">
                        <label class="cf-toggle">
                          <input type="checkbox" name="cf_toggle_3" id="cf-toggle-input-3" disabled aria-disabled="true" />
                          <span class="cf-slider"></span>
                        </label>
                        <span class="cf-toggle-text">Show in print</span>
                      </div>
                    </div>
                    <!-- Field 4 -->
                    <div class="cf-group is-disabled">
                      <div class="cf-title">Custom Field 4<span class="cf-asterisk">&ast;</span></div>
                      <div class="cf-input-row">
                        <input type="checkbox" name="cf_check_4" class="cf-checkbox cf-trigger" id="cf-check-4" />
                        <input type="text" name="custom_field_4" class="cf-input" id="cf-input-4" placeholder="Custom Field 4" disabled aria-disabled="true" />
                      </div>
                      <div class="cf-toggle-row">
                        <label class="cf-toggle">
                          <input type="checkbox" name="cf_toggle_4" id="cf-toggle-input-4" disabled aria-disabled="true" />
                          <span class="cf-slider"></span>
                        </label>
                        <span class="cf-toggle-text">Show in print</span>
                      </div>
                    </div>
                    <!-- Field 5 -->
                    <div class="cf-group is-disabled">
                      <div class="cf-title">Custom Field 5<span class="cf-asterisk">&ast;</span></div>
                      <div class="cf-input-row">
                        <input type="checkbox" name="cf_check_5" class="cf-checkbox cf-trigger" id="cf-check-5" />
                        <input type="text" name="custom_field_5" class="cf-input" id="cf-input-5" placeholder="Custom Field 5" disabled aria-disabled="true" />
                      </div>
                      <div class="cf-toggle-row">
                        <label class="cf-toggle">
                          <input type="checkbox" name="cf_toggle_5" id="cf-toggle-input-5" disabled aria-disabled="true" />
                          <span class="cf-slider"></span>
                        </label>
                        <span class="cf-toggle-text">Show in print</span>
                      </div>
                    </div>
                    <!-- Field 6 -->
                    <div class="cf-group is-disabled">
                      <div class="cf-title">Custom Field 6<span class="cf-asterisk">&ast;</span></div>
                      <div class="cf-input-row">
                        <input type="checkbox" name="cf_check_6" class="cf-checkbox cf-trigger" id="cf-check-6" />
                        <input type="text" name="custom_field_6" class="cf-input" id="cf-input-6" placeholder="Custom Field 6" disabled aria-disabled="true" />
                      </div>
                      <div class="cf-toggle-row">
                        <label class="cf-toggle">
                          <input type="checkbox" name="cf_toggle_6" id="cf-toggle-input-6" disabled aria-disabled="true" />
                          <span class="cf-slider"></span>
                        </label>
                        <span class="cf-toggle-text">Show in print</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Close</button>
                  <button type="button" class="btn btn-danger rounded-pill">Save</button>
                </div>
              </div>
            </div>
          </div>


        </section>


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

      // Change Text Logic
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

      // Quantity Decimal Example Logic
      const qtyDecimalInput = document.getElementById('qtyDecimalInput');
      const qtyDecimalExample = document.getElementById('qtyDecimalExample');

      if (qtyDecimalInput && qtyDecimalExample) {
        qtyDecimalInput.addEventListener('input', (e) => {
          let val = parseInt(e.target.value) || 0;
          if (val > 4) val = 4;
          if (val < 1) val = 1;
          qtyDecimalExample.textContent = `eg 0.${'0'.repeat(val)}`;
        });
      }

      // Additional Item Fields Logic - Made bulletproof
      const invTriggers = document.querySelectorAll('.inv-trigger');
      const toggleRowState = (checkbox) => {
        const targetId = checkbox.getAttribute('data-target');
        const controlsWrapper = document.getElementById(targetId);
        if (controlsWrapper) {
          const inputs = controlsWrapper.querySelectorAll('input, select');
          if (checkbox.checked) {
            controlsWrapper.classList.remove('is-disabled');
            inputs.forEach(input => input.disabled = false);
          } else {
            controlsWrapper.classList.add('is-disabled');
            inputs.forEach(input => input.disabled = true);
          }
        }
      };

      invTriggers.forEach(checkbox => {
        // Run once on load to establish baseline
        toggleRowState(checkbox);

        // Listen to change
        checkbox.addEventListener('change', () => toggleRowState(checkbox));
      });

      // Custom Fields Logic
      const cfTriggers = document.querySelectorAll('.cf-trigger');
      cfTriggers.forEach(trigger => {
        trigger.addEventListener('change', (e) => {
          const group = e.target.closest('.cf-group');
          const input = group.querySelector('.cf-input');
          const toggle = group.querySelector('.cf-toggle input');

          if (e.target.checked) {
            group.classList.remove('is-disabled');
            input.removeAttribute('disabled');
            input.setAttribute('aria-disabled', 'false');
            toggle.removeAttribute('disabled');
            toggle.setAttribute('aria-disabled', 'false');
          } else {
            group.classList.add('is-disabled');
            input.setAttribute('disabled', 'true');
            input.setAttribute('aria-disabled', 'true');
            toggle.setAttribute('disabled', 'true');
            toggle.setAttribute('aria-disabled', 'true');
          }
        });
      });
    })();
  </script>
</body>

</html>
