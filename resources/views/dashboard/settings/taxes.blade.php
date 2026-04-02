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

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" />

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
          <div class="section__title fs-4">Tax Rates


            <!-- Button trigger modal -->
            <button type="button"
              class="btn mb-2 rounded-circle border border-secondary d-inline-flex justify-content-center align-items-center"
              style="width: 25px; height: 25px; padding: 0;" data-bs-toggle="modal" data-bs-target="#addTaxRateModal">
              <i class="fas fa-plus text-secondary" style="font-size: 14px;"></i>
            </button>

            <!-- Modal -->
            <div class="modal fade" id="addTaxRateModal" tabindex="-1" aria-labelledby="addTaxRateModalLabel"
              aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content" style="width: 96% !important;">
                  <div class="modal-header border-bottom-0 pb-0">
                    <h1 class="modal-title" id="addTaxRateModalLabel" style="font-size: 16px; font-weight: 500;">Add Tax
                    </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form action="">
                      <div class="mb-3">

                        <input type="text" class="form-control form-control-sm" id="taxName" required
                          placeholder="Tax Name">
                        <div class="row mt-2">
                          <div class="col-8">
                            <input type="number" class="form-control" placeholder="Rate" aria-label="First name">
                          </div>
                          <div class="col-4">
                            <select class="form-select form-select prefix-select">
                              <option value="other">Other</option>

                            </select>
                          </div>
                        </div>
                      </div>
                    </form>

                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Save</button>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <table class="table table-borderless table-sm align-middle m-0" style="font-size: 14px;">
            <tbody>
              <tr>
                <td>
                  <label for="tax1" class="mb-0" style="cursor: pointer;">Tax 1</label>
                </td>
                <td class="text-end text-secondary">200%</td>
                <td class="text-end d-flex" style="width: 30px;">
                  <button type="button" class="btn mb-2 d-inline-flex justify-content-center align-items-center"
                    style="width: 25px; height: 25px; padding: 0;" data-bs-toggle="modal"
                    data-bs-target="#addTaxRateModal">
                    <i class="fas fa-pencil text-secondary" style="font-size: 12px;"></i>
                  </button>
                  <button type="button" class="btn mb-2 d-inline-flex justify-content-center align-items-center ms-3"
                    style="width: 25px; height: 25px; padding: 0;" data-bs-toggle="modal"
                    data-bs-target="#deleteTaxRateModal">
                    <i class="fas fa-trash text-secondary" style="font-size: 12px;"></i>
                  </button>

                  <div class="modal fade" id="deleteTaxRateModal" tabindex="-1" aria-labelledby="deleteTaxRateModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content text-start" style="width:90% !important;">
                        <div class="modal-header">
                          <h1 class="modal-title fs-5" id="deleteTaxRateModalLabel">Vyapar</h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          Do you want to delete this tax rate?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="button" class="btn btn-primary">OK</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>

            </tbody>
          </table>





        </section>



        <!-- Column 2 (top): Multi Firm -->
        <section class="section section--multi-firm">
          <div class="section__title fs-4">Tax Group


            <!-- Button trigger modal -->
            <button type="button"
              class="btn mb-2 rounded-circle border border-secondary d-inline-flex justify-content-center align-items-center"
              style="width: 25px; height: 25px; padding: 0;" data-bs-toggle="modal" data-bs-target="#addTaxGroupModal">
              <i class="fas fa-plus text-secondary" style="font-size: 14px;"></i>
            </button>

            <!-- Modal -->
            <div class="modal fade" id="addTaxGroupModal" tabindex="-1" aria-labelledby="addTaxGroupModalLabel"
              aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content" style="width: 96% !important;">
                  <div class="modal-header border-bottom-0 pb-0">
                    <h1 class="modal-title" id="addTaxGroupModalLabel" style="font-size: 16px; font-weight: 500;">Add Tax
                      Group</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                  </div>
                  <div class="modal-body">
                    <form action="">
                      <div class="mb-3">
                        <label for="taxGroupName" class="form-label text-secondary mb-1" style="font-size: 13px;">Tax
                          Group Name</label>
                        <input type="text" class="form-control form-control-sm" id="taxGroupName" required>
                      </div>
                    </form>
                    <div class="mt-2" style="width: 100%; height: 200px; overflow-y: auto;">
                      <div class="text-secondary mb-2" style="font-size: 13px;">Select Taxes</div>
                      <table class="table table-borderless table-sm align-middle m-0" style="font-size: 14px;">
                        <tbody>
                          <tr>
                            <td>
                              <label for="tax1" class="mb-0" style="cursor: pointer;">Tax 1</label>
                            </td>
                            <td class="text-end text-secondary">200%</td>
                            <td class="text-end" style="width: 30px;">
                              <input class="form-check-input m-0" type="checkbox" value="" id="tax1"
                                style="cursor: pointer;">
                            </td>
                          </tr>
                          <tr>
                            <td>
                              <label for="tax2" class="mb-0" style="cursor: pointer;">Tax 2</label>
                            </td>
                            <td class="text-end text-secondary">10%</td>
                            <td class="text-end" style="width: 30px;">
                              <input class="form-check-input m-0" type="checkbox" value="" id="tax2"
                                style="cursor: pointer;">
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Save</button>
                  </div>
                </div>
              </div>
            </div>

          </div>

          <table class="table table-borderless table-sm align-middle m-0" style="font-size: 14px;">
            <tbody>
              <tr>
                <td>
                  <label for="tax1" class="mb-0" style="cursor: pointer;">Tax 1</label>
                </td>
                <td class="text-end text-secondary">200%</td>
                <td class="text-end d-flex" style="width: 30px;">
                  <button type="button" class="btn mb-2 d-inline-flex justify-content-center align-items-center"
                    style="width: 25px; height: 25px; padding: 0;" data-bs-toggle="modal"
                    data-bs-target="#addTaxGroupModal">
                    <i class="fas fa-pencil text-secondary" style="font-size: 12px;"></i>
                  </button>
                  <button type="button" class="btn mb-2 d-inline-flex justify-content-center align-items-center ms-3"
                    style="width: 25px; height: 25px; padding: 0;" data-bs-toggle="modal"
                    data-bs-target="#deleteTaxGroupModal">
                    <i class="fas fa-trash text-secondary" style="font-size: 12px;"></i>
                  </button>

                  <div class="modal fade" id="deleteTaxGroupModal" tabindex="-1" aria-labelledby="deleteTaxGroupModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content text-start" style="width:90% !important;">
                        <div class="modal-header">
                          <h1 class="modal-title fs-5" id="deleteTaxGroupModalLabel">Vyapar</h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          Do you want to delete this tax group?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="button" class="btn btn-primary">OK</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
              <tr>
                <td>
                  <label for="tax2" class="mb-0" style="cursor: pointer;">Tax 2</label>
                </td>
                <td class="text-end text-secondary">10%</td>
                <td class="text-end d-flex" style="width: 30px;">
                  <button type="button" class="btn mb-2 d-inline-flex justify-content-center align-items-center"
                    style="width: 25px; height: 25px; padding: 0;" data-bs-toggle="modal"
                    data-bs-target="#addTaxGroupModal">
                    <i class="fas fa-pencil text-secondary" style="font-size: 12px;"></i>
                  </button>
                  <button type="button" class="btn mb-2 d-inline-flex justify-content-center align-items-center ms-3"
                    style="width: 25px; height: 25px; padding: 0;" data-bs-toggle="modal"
                    data-bs-target="#deleteTaxGroupModal">
                    <i class="fas fa-trash text-secondary" style="font-size: 12px;"></i>
                  </button>

                  <div class="modal fade" id="deleteTaxGroupModal" tabindex="-1" aria-labelledby="deleteTaxGroupModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                      <div class="modal-content text-start" style="width:90% !important;">
                        <div class="modal-header">
                          <h1 class="modal-title fs-5" id="deleteTaxGroupModalLabel">Vyapar</h1>
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                          Do you want to delete this tax group?
                        </div>
                        <div class="modal-footer">
                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                          <button type="button" class="btn btn-primary">OK</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>




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
