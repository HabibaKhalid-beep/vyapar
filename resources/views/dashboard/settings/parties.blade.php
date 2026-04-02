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
          <div class="section__title">Party Settings</div>

          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Party Grouping</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>





          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Shipping Address</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Print Shipping Address</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Manage Party Status</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <label class="check-row" style="cursor: pointer;">
            <input type="checkbox" class="check-row__input" id="paymentReminderCheck" />
            <span class="check-row__label">Enable Payment Remainder</span>
            <i class="fa fa-info-circle check-row__info" aria-hidden="true"></i>
          </label>

          <div class="d-none ms-4" id="paymentReminderOptions">
            <div class="d-flex align-items-center">
              <p class="mb-0 text-secondary" style="font-size: 14px;">Remind me for payment due in <input type="number"
                  value="1"
                  style="width: 30px; border:none; border-bottom:1px solid black; background:transparent; text-align:center;">
                days</p>
            </div>
          </div>

          <!-- Button trigger modal -->
          <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#messageModal">
            <span class="text-primary">Reminder messaga ></span>
          </button>

          <!-- Modal -->
          <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                <div class="modal-header">
                  <h1 class="modal-title fs-5" id="messageModalLabel">Add/ Edit Remainder Message</h1>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <form action="" class="form-control">
                    <p>Dear [Party Name],</p>
                    <p>Your payment of [Amount] is pending with [Business Name]</p>
                    <textarea name="" id="" class="form-contral" placeholder="Type Additional Message" cols="30"
                      rows="10" style="width: 100%;"></textarea>
                    <p>If you already have made the payment, kindly ignore this message</p>

                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="reset" class="btn btn-primary">Reset Default</button>
                  <button type="button" class="btn btn-primary">Save</button>
                </div>
                </form>
              </div>
            </div>
          </div>
        </section>



        <!-- Column 2 (top): Multi Firm -->
        <section class="section section--multi-firm">
          <div class="section__title">Additional Fields</div>

          <style>
            .additional-field-row {
              margin-bottom: 20px;
              font-family: 'Inter', 'Roboto', sans-serif;
            }
            .additional-field-row .form-control {
              border: 1px solid #D1D5DB;
              padding: 10px 14px;
              border-radius: 6px;
              outline: none;
              transition: all 0.2s ease;
              font-size: 14px;
              box-shadow: none !important;
            }
            .additional-field-row .form-control::placeholder {
              color: #9CA3AF;
            }
            .additional-field-row .input-wrapper.disabled {
              background-color: #F3F4F6;
              opacity: 0.5;
              pointer-events: none;
              border-radius: 6px;
            }
            .additional-field-row .toggle-wrapper {
              display: flex;
              justify-content: flex-end;
              align-items: center;
              margin-top: 8px;
            }
            .additional-field-row .toggle-wrapper.disabled {
              opacity: 0.5;
              pointer-events: none;
            }
          </style>

          <!-- Row 1 -->
          <div class="additional-field-row" id="row-af-1">
            <div class="d-flex align-items-center mb-1">
              <input class="form-check-input me-3 af-checkbox" type="checkbox" id="check-af-1" style="width: 20px; height: 20px; cursor: pointer; flex-shrink: 0;" data-target="1">
              <div class="input-wrapper w-100 disabled" id="wrapper-af-1">
                <input type="text" class="form-control w-100" placeholder="Additional Field 1" id="input-af-1" disabled>
              </div>
            </div>
            <div class="toggle-wrapper disabled" id="toggle-wrapper-af-1">
              <div class="form-check form-switch mb-0 d-flex align-items-center">
                <input class="form-check-input me-2 mt-0" type="checkbox" role="switch" id="toggle-af-1" disabled style="height: 18px; width: 36px; cursor:pointer;">
                <label class="form-check-label text-secondary mb-0" for="toggle-af-1" style="font-size: 14px; cursor: pointer;">Show In Print</label>
              </div>
            </div>
          </div>

          <!-- Row 2 -->
          <div class="additional-field-row" id="row-af-2">
            <div class="d-flex align-items-center mb-1">
              <input class="form-check-input me-3 af-checkbox" type="checkbox" id="check-af-2" style="width: 20px; height: 20px; cursor: pointer; flex-shrink: 0;" data-target="2">
              <div class="input-wrapper w-100 disabled" id="wrapper-af-2">
                <input type="text" class="form-control w-100" placeholder="Additional Field 2" id="input-af-2" disabled>
              </div>
            </div>
            <div class="toggle-wrapper disabled" id="toggle-wrapper-af-2">
              <div class="form-check form-switch mb-0 d-flex align-items-center">
                <input class="form-check-input me-2 mt-0" type="checkbox" role="switch" id="toggle-af-2" disabled style="height: 18px; width: 36px; cursor:pointer;">
                <label class="form-check-label text-secondary mb-0" for="toggle-af-2" style="font-size: 14px; cursor: pointer;">Show In Print</label>
              </div>
            </div>
          </div>

          <!-- Row 3 -->
          <div class="additional-field-row" id="row-af-3">
            <div class="d-flex align-items-center mb-1">
              <input class="form-check-input me-3 af-checkbox" type="checkbox" id="check-af-3" style="width: 20px; height: 20px; cursor: pointer; flex-shrink: 0;" data-target="3">
              <div class="input-wrapper w-100 disabled" id="wrapper-af-3">
                <input type="text" class="form-control w-100" placeholder="Additional Field 3" id="input-af-3" disabled>
              </div>
            </div>
            <div class="toggle-wrapper disabled" id="toggle-wrapper-af-3">
              <div class="form-check form-switch mb-0 d-flex align-items-center">
                <input class="form-check-input me-2 mt-0" type="checkbox" role="switch" id="toggle-af-3" disabled style="height: 18px; width: 36px; cursor:pointer;">
                <label class="form-check-label text-secondary mb-0" for="toggle-af-3" style="font-size: 14px; cursor: pointer;">Show In Print</label>
              </div>
            </div>
          </div>

          <!-- Row 4 -->
          <div class="additional-field-row" id="row-af-4" style="margin-bottom: 0;">
            <div class="d-flex align-items-center mb-1">
              <input class="form-check-input me-3 af-checkbox" type="checkbox" id="check-af-4" style="width: 20px; height: 20px; cursor: pointer; flex-shrink: 0;" data-target="4">
              <div class="input-wrapper w-100 disabled" id="wrapper-af-4" style="display: grid; grid-template-columns: 2fr 1fr; gap: 10px;">
                <input type="text" class="form-control w-100" placeholder="Additional Field 4" id="input-af-4" disabled>
                <!-- Because a raw date input looks ugly if not styled well, I'll use text input acting like Date using focus -->
                <input type="text" class="form-control w-100" placeholder="dd/mm/yy" id="input-date-af-4" disabled onfocus="(this.type='date')" onblur="if(!this.value) this.type='text'">
              </div>
            </div>
            <div class="toggle-wrapper disabled" id="toggle-wrapper-af-4">
              <div class="form-check form-switch mb-0 d-flex align-items-center">
                <input class="form-check-input me-2 mt-0" type="checkbox" role="switch" id="toggle-af-4" disabled style="height: 18px; width: 36px; cursor:pointer;">
                <label class="form-check-label text-secondary mb-0" for="toggle-af-4" style="font-size: 14px; cursor: pointer;">Show In Print</label>
              </div>
            </div>
          </div>        </section>



        <!-- Column 3 (top): Backup & History -->
        <section class="section section--backup">
          <div class="section__title">Enable Loyalty Point</div>

          <label class="check-row check-row--sm">
            <input type="checkbox" class="check-row__input" />
            <span class="check-row__label">Enable loyalty Point</span>
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

      // Payment Reminder Logic
      const paymentReminderCheck = document.getElementById('paymentReminderCheck');
      const paymentReminderOptions = document.getElementById('paymentReminderOptions');
      if (paymentReminderCheck && paymentReminderOptions) {
        paymentReminderCheck.addEventListener('change', (e) => {
          paymentReminderOptions.classList.toggle('d-none', !e.target.checked);
          if (e.target.checked) paymentReminderOptions.classList.add('d-flex');
          else paymentReminderOptions.classList.remove('d-flex');
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

      // Additional Fields Logic
      const afCheckboxes = document.querySelectorAll('.af-checkbox');
      afCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', (e) => {
          const targetId = e.target.getAttribute('data-target');
          const isChecked = e.target.checked;

          const textInput = document.getElementById('input-af-' + targetId);
          const wrapper = document.getElementById('wrapper-af-' + targetId);
          const toggleWrap = document.getElementById('toggle-wrapper-af-' + targetId);
          const toggleInput = document.getElementById('toggle-af-' + targetId);
          const dateInput = document.getElementById('input-date-af-' + targetId);

          if(textInput) textInput.disabled = !isChecked;
          if(toggleInput) toggleInput.disabled = !isChecked;
          if(dateInput) dateInput.disabled = !isChecked;

          if(isChecked) {
            if(wrapper) wrapper.classList.remove('disabled');
            if(toggleWrap) toggleWrap.classList.remove('disabled');
          } else {
            if(wrapper) wrapper.classList.add('disabled');
            if(toggleWrap) toggleWrap.classList.add('disabled');
            // Uncheck toggle if row disabled
            if(toggleInput) toggleInput.checked = false;
          }
        });
      });
    })();
  </script>
</body>

</html>
