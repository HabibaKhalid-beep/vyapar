<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Print Settings</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
  <link href="{{ asset('css/setting/styles.css') }}" rel="stylesheet" />
</head>

<body>
  <div class="print-page">
    <aside class="print-sidebar">
      <div class="print-sidebar__header">
        <span>Settings</span>
        <i class="fa fa-search"></i>
      </div>
      <nav class="print-sidebar__nav">
        <a href="{{ route('settings.general') }}">GENERAL</a>
        <a href="{{ route('settings.transactions') }}">TRANSACTION</a>
        <a class="active" href="{{ route('settings.print-layout') }}">PRINT</a>
        <a href="{{ route('settings.taxes') }}">TAXES</a>
        <a href="{{ route('settings.transaction-messages') }}">TRANSACTION MESSAGE</a>
        <a href="{{ route('settings.parties') }}">PARTY</a>
        <a href="{{ route('settings.items') }}">ITEM</a>
        <a href="#">SERVICE REMINDERS <i class="fa fa-crown text-primary ms-1"></i></a>
      </nav>
    </aside>

    <main class="print-main">
      <!-- <button class="print-close-btn" type="button" aria-label="Close">
        <i class="fa fa-times"></i>
      </button> -->

      <div class="print-main__layout">
        <section class="print-config">
          <div class="printer-tabs">
            <button class="printer-tab active" data-tab="regular" type="button">REGULAR PRINTER</button>
            <button class="printer-tab" data-tab="thermal" type="button">THERMAL PRINTER</button>
          </div>
          <div class="config-split-head">
            <button type="button" id="changeLayoutTab" class="config-tab is-active">CHANGE LAYOUT</button>
            <button type="button" id="changeColorsTab" class="config-tab">CHANGE COLOURS</button>
          </div>
          <div class="theme-strip" id="themeStrip">
            <button class="theme-nav-btn" id="themePrev" type="button" aria-label="Previous themes"><i
                class="fa fa-chevron-left"></i></button>
            <div class="theme-list" id="themeList"></div>
            <button class="theme-nav-btn" id="themeNext" type="button" aria-label="Next themes"><i
                class="fa fa-chevron-right"></i></button>
          </div>

          <div class="color-strip d-none" id="colorStrip" aria-label="Color palette">
            <div class="color-row">
              <button class="color-dot" type="button" data-color="#8b86d8" style="--dot:#8b86d8"></button>
              <button class="color-dot" type="button" data-color="#0ea5e9" style="--dot:#0ea5e9"></button>
              <button class="color-dot" type="button" data-color="#9ca3af" style="--dot:#9ca3af"></button>
              <button class="color-dot" type="button" data-color="#6b7280" style="--dot:#6b7280"></button>
              <button class="color-dot" type="button" data-color="#a3a55d" style="--dot:#a3a55d"></button>
              <button class="color-dot" type="button" data-color="#3b82f6" style="--dot:#3b82f6"></button>
              <button class="color-dot" type="button" data-color="#22c55e" style="--dot:#22c55e"></button>
              <button class="color-dot" type="button" data-color="#16a34a" style="--dot:#16a34a"></button>
              <button class="color-dot" type="button" data-color="#84cc16" style="--dot:#84cc16"></button>
              <button class="color-dot" type="button" data-color="#7c2d12" style="--dot:#7c2d12"></button>
              <button class="color-dot" type="button" data-color="#6b21a8" style="--dot:#6b21a8"></button>
              <button class="color-dot" type="button" data-color="#be185d" style="--dot:#be185d"></button>
              <button class="color-dot" type="button" data-color="#c2410c" style="--dot:#c2410c"></button>
              <button class="color-dot" type="button" data-color="#a855f7" style="--dot:#a855f7"></button>
              <button class="color-dot" type="button" data-color="#db2777" style="--dot:#db2777"></button>
              <button class="color-dot" type="button" data-color="#d97706" style="--dot:#d97706"></button>
              <button class="color-dot" type="button" data-color="#f59e0b" style="--dot:#f59e0b"></button>
              <button class="color-dot" type="button" data-color="#111827" style="--dot:#111827"></button>
              <button class="color-dot is-selected" type="button" data-color="#ffffff" style="--dot:#ffffff"></button>
            </div>
          </div>
          <div id="regularConfig">
          <div class="config-block">
            <h3>Print Company Info / Header</h3>
            <label class="cfg-check"><input type="checkbox" checked /> Make Regular Printer Default <i
                class="fa fa-info-circle"></i></label>
            <label class="cfg-check"><input type="checkbox" checked /> Print repeat header in all pages <i
                class="fa fa-info-circle"></i></label>
            <div class="cfg-field"><label>Company Name</label><input type="text" value="Grocery Store" /><i
                class="fa fa-info-circle"></i></div>
            <div class="cfg-check with-link"><input type="checkbox" checked /><span>Company Logo <a href="#"
                  class="small-link">(Change)</a></span><i class="fa fa-info-circle"></i></div>
            <div class="cfg-field"><input type="text" value="Address" /><i class="fa fa-info-circle"></i></div>
            <div class="cfg-field"><input type="email" value="Email" /><i class="fa fa-info-circle"></i></div>
            <div class="cfg-field"><label>Phone Number</label><input type="text" value="3023308556" /><i
                class="fa fa-info-circle"></i></div>
          </div>
          <div class="config-grid">
            <div class="cfg-inline"><label>Paper Size</label><select>
                <option>A4</option>
                <option>A5</option>
                <option>Letter</option>
              </select><i class="fa fa-info-circle"></i></div>
            <div class="cfg-inline"><label>Orientation</label><select>
                <option>Portrait</option>
                <option>Landscape</option>
              </select><i class="fa fa-info-circle"></i></div>
            <div class="cfg-inline"><label>Company Name Text Size</label><select>
                <option>Large</option>
                <option>Medium</option>
                <option>Small</option>
              </select><i class="fa fa-info-circle"></i></div>
            <div class="cfg-inline"><label>Invoice Text Size</label><select>
                <option>Medium</option>
                <option>Large</option>
                <option>Small</option>
              </select><i class="fa fa-info-circle"></i></div>
          </div>
          <div class="config-bottom">
            <label class="cfg-check"><input type="checkbox" /> Print Original/Duplicate <i
                class="fa fa-info-circle"></i></label>
            <div class="cfg-inline compact"><label>Extra space on Top of PDF</label>

              <input type="number" min="1" max="50" style="width: 50px;">
            </div>
            <!-- Button trigger modal -->
            <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#exampleModal">
              <span class="text-primary">Change transaction names > </span>
            </button>

            <div class="print-modal-extra mt-4">
              <h3 class="print-modal-extra__title">Item Table</h3>
              <hr class="print-modal-extra__divider" />

              <div class="print-modal-extra__row">
                <label>Min No. of Rows in Item Table <i class="fa fa-info-circle"></i></label>
                <div class="print-modal-extra__spinner">
                  <input type="number" value="14" min="1" />
                </div>
              </div>

              <a href="#" class="print-modal-extra__link">Item Table Customization &gt;</a>

              <h3 class="print-modal-extra__title mt-4">Totals &amp; Taxes</h3>
              <hr class="print-modal-extra__divider" />

              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>Total Item Quantity <i
                    class="fa fa-info-circle"></i></span></label>
              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>Amount with Decimal
                  <small>e.g. 0.00</small> <i class="fa fa-info-circle"></i></span></label>
              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>Received Amount <i
                    class="fa fa-info-circle"></i></span></label>
              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>Balance Amount <i
                    class="fa fa-info-circle"></i></span></label>
              <label class="print-modal-extra__check"><input type="checkbox" /> <span>Current Balance of Party <i
                    class="fa fa-info-circle"></i></span></label>
              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>Tax Details <i
                    class="fa fa-info-circle"></i></span></label>
              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>You Saved</span></label>
              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>Print Amount with Grouping
                  <i class="fa fa-info-circle"></i></span></label>

              <div class="print-modal-extra__row mt-2">
                <label>Amount in Words <i class="fa fa-info-circle"></i></label>
                <select>
                  <option selected>Indian</option>
                  <option>International</option>
                </select>
              </div>

              <h3 class="print-modal-extra__title mt-4">Footer</h3>
              <hr class="print-modal-extra__divider" />

              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>Print Description <i
                    class="fa fa-info-circle"></i></span></label>
              <a href="#" class="print-modal-extra__link">Terms and Conditions &gt;</a>
              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>Print Received by details
                  <i class="fa fa-info-circle"></i></span></label>
              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>Print Delivered by details
                  <i class="fa fa-info-circle"></i></span></label>
              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>Print Signature Text

                  <input id="signatureTextInput" type="text" value="Authorized Signatory"
                    style="width: 150px; height: 25px; background-color:transparent; border: 1px solid grey; outline: none; border-radius: 6px;">

                  <i class="fa fa-info-circle"></i></span>
                <button id="signatureChangeBtn" type="button" class="text-primary btn-sm bg-transparent border-0" style="font-size: 8px;">Change Signature</button>
                <input id="signatureFileInput" type="file" accept="image/*" hidden />
              </label>
              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>Payment Mode
                  <i class="fa fa-info-circle"></i></span></label>
              <label class="print-modal-extra__check"><input type="checkbox" checked /> <span>Print Acknowledgement
                  <i class="fa fa-info-circle"></i></span></label>


            </div>
          </div><!-- /config-bottom -->
          </div><!-- /regularConfig -->

          <div id="thermalConfig" class="d-none">
            <div class="thermal-layout-head">CHANGE LAYOUT</div>
            <div class="thermal-theme-strip">
              <div class="thermal-theme-list" id="thermalThemeList"></div>
              <button class="thermal-next" id="thermalNext" type="button" aria-label="Next themes">
                <i class="fa fa-chevron-right"></i>
              </button>
            </div>

            <label class="thermal-check"><input type="checkbox" /> Make Thermal Printer Default <i class="fa fa-info-circle"></i></label>

            <div class="thermal-row">
              <div class="thermal-row__label">Page Size</div>
              <div class="thermal-size" id="thermalSize">
                <button type="button" class="thermal-size__btn" data-size="58">2 Inch<br><span>58mm</span></button>
                <button type="button" class="thermal-size__btn is-active" data-size="68">3 Inch<br><span>68mm</span></button>
                <button type="button" class="thermal-size__btn" data-size="88">4 Inch<br><span>88mm</span></button>
                <button type="button" class="thermal-size__btn" data-size="48">Custom<br><span>48 (Chro)</span></button>
              </div>
              <i class="fa fa-info-circle"></i>
            </div>

            <div class="thermal-row">
              <div class="thermal-row__label">Printing Type</div>
              <select class="thermal-select">
                <option selected>Text Printing</option>
                <option>Image Printing</option>
              </select>
              <i class="fa fa-info-circle"></i>
            </div>

            <label class="thermal-check"><input type="checkbox" checked /> Use Text Styling(Bold) <i class="fa fa-info-circle"></i></label>
            <label class="thermal-check"><input type="checkbox" /> Auto Cut Paper After Printing <i class="fa fa-info-circle"></i></label>
            <label class="thermal-check"><input type="checkbox" /> Open Cash Drawer After Printing <i class="fa fa-info-circle"></i></label>

            <div class="thermal-row">
              <div class="thermal-row__label">Extra lines at the end</div>
              <input class="thermal-input" type="number" value="0" />
              <i class="fa fa-info-circle"></i>
            </div>
            <div class="thermal-row">
              <div class="thermal-row__label">Number of copies</div>
              <input class="thermal-input" type="number" value="1" />
              <i class="fa fa-info-circle"></i>
            </div>

            <div class="thermal-section-title">Print Company Info / Header</div>
            <hr class="thermal-divider" />

            <div class="thermal-field">
              <label>Company Name</label>
              <div class="thermal-field__row">
                <input type="checkbox" checked />
                <input class="thermal-field__input" type="text" value="Grocery Store" />
                <i class="fa fa-info-circle"></i>
              </div>
            </div>
            <div class="thermal-field">
              <div class="thermal-field__row">
                <input type="checkbox" checked />
                <div class="thermal-field__label">Company Logo <a href="#" class="small-link">(Change)</a></div>
                <i class="fa fa-info-circle"></i>
              </div>
            </div>
            <div class="thermal-field">
              <div class="thermal-field__row">
                <input type="checkbox" checked />
                <input class="thermal-field__input" type="text" value="Address" />
                <i class="fa fa-info-circle"></i>
              </div>
            </div>
            <div class="thermal-field">
              <div class="thermal-field__row">
                <input type="checkbox" checked />
                <input class="thermal-field__input" type="text" value="Email" />
                <i class="fa fa-info-circle"></i>
              </div>
            </div>
            <div class="thermal-field">
              <label>Phone Number</label>
              <div class="thermal-field__row">
                <input type="checkbox" checked />
                <input class="thermal-field__input" type="text" value="3023308556" />
                <i class="fa fa-info-circle"></i>
              </div>
            </div>

            <button type="button" class="btn btn-link p-0 small-link mt-1" data-bs-toggle="modal" data-bs-target="#exampleModal">
              Change Transaction Names &gt;
            </button>

            <div class="thermal-extra mt-3">
              <h4 class="thermal-extra__title">Item table</h4>
              <hr class="thermal-divider" />
              <label class="thermal-check"><input type="checkbox" checked /> S.No <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> Units of Measurement <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> MRP <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> Description <i class="fa fa-info-circle"></i></label>

              <h4 class="thermal-extra__title mt-3">Additional Item Details</h4>
              <label class="thermal-check"><input type="checkbox" checked /> Batch No. <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> Exp. Date <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> Mfg. Date <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> Size <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> Model No. <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> Serial No. <i class="fa fa-info-circle"></i></label>

              <h4 class="thermal-extra__title mt-3">Totals &amp; Taxes</h4>
              <hr class="thermal-divider" />
              <label class="thermal-check"><input type="checkbox" checked /> Total Item Quantity <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> Amount with Decimal <small>e.g. 0.00</small> <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> Received Amount <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> Balance Amount <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" /> Current Balance of Party <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> Tax Details <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> You Saved <i class="fa fa-info-circle"></i></label>
              <label class="thermal-check"><input type="checkbox" checked /> Print Amount with Grouping <i class="fa fa-info-circle"></i></label>

              <div class="thermal-row">
                <div class="thermal-row__label">Amount in Words <i class="fa fa-info-circle"></i></div>
                <select class="thermal-select thermal-select--small">
                  <option selected>Indian</option>
                  <option>International</option>
                </select>
                <span></span>
              </div>

              <h4 class="thermal-extra__title mt-3">Footer</h4>
              <hr class="thermal-divider" />
              <label class="thermal-check"><input type="checkbox" checked /> Print Description <i class="fa fa-info-circle"></i></label>
              <a href="#" class="small-link d-inline-block mb-2">Terms and Conditions &gt;</a>

              <h4 class="thermal-extra__title mt-3">Vyapar Printer Setup</h4>
              <hr class="thermal-divider" />
              <button type="button" class="thermal-setup-btn">2 Inch (VYPRTP2001) - Quick Setup <i class="fa fa-download"></i></button>
              <button type="button" class="thermal-setup-btn">3 Inch (VYPRTP3001) - Quick Setup <i class="fa fa-download"></i></button>
              <button type="button" class="thermal-setup-btn">2 Inch (VYPRTP2002) - Quick Setup <i class="fa fa-download"></i></button>
            </div>
          </div>

          <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel"
              aria-hidden="true">
              <div class="modal-dialog modal-dialog-centered modal-xl print-modal-dialog">
                <div class="modal-content print-modal">
                  <div class="modal-header border-0 pb-0">
                    <h1 class="modal-title fs-5 fw-bold" id="exampleModalLabel">Change Transaction Names</h1>
                    <button type="button" class="print-modal__close" data-bs-dismiss="modal" aria-label="Close">
                      <i class="fa fa-times" aria-hidden="true"></i>
                    </button>
                  </div>

                  <div class="modal-body pt-3">
                    <div class="row g-3">
                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Sale</label>
                        <input class="form-control print-modal__input" value="Invoice" />
                      </div>
                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Purchase</label>
                        <input class="form-control print-modal__input" value="Bill" />
                      </div>

                      <div class="col-12">
                        <div class="form-check">
                          <input class="form-check-input" type="checkbox" id="billSupplyNonTax" />
                          <label class="form-check-label print-modal__check" for="billSupplyNonTax">
                            Bill of Supply for Non Tax Transaction
                          </label>
                        </div>
                      </div>

                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Payment-In</label>
                        <input class="form-control print-modal__input" value="Payment Receipt" />
                      </div>
                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Payment-Out</label>
                        <input class="form-control print-modal__input" value="Payment Out" />
                      </div>

                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Expense</label>
                        <input class="form-control print-modal__input" value="Expense" />
                      </div>
                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Other Income</label>
                        <input class="form-control print-modal__input" value="Other Income" />
                      </div>

                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Sale Order</label>
                        <input class="form-control print-modal__input" value="Sale Order" />
                      </div>
                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Purchase Order</label>
                        <input class="form-control print-modal__input" value="Purchase Order" />
                      </div>

                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Estimate</label>
                        <input class="form-control print-modal__input" value="Estimate" />
                      </div>
                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Proforma Invoice</label>
                        <input class="form-control print-modal__input" value="Proforma Invoice" />
                      </div>

                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Delivery Challan</label>
                        <input class="form-control print-modal__input" value="Delivery Challan" />
                      </div>
                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Credit Note</label>
                        <input class="form-control print-modal__input" value="Credit Note" />
                      </div>

                      <div class="col-12 col-md-6">
                        <label class="form-label print-modal__label">Debit Note</label>
                        <input class="form-control print-modal__input" value="Debit Note" />
                      </div>
                    </div>

                  </div>

                  <div class="modal-footer border-0 pt-3">
                    <button type="button" class="btn btn-outline-primary px-4" data-bs-dismiss="modal">CANCEL</button>
                    <button type="button" class="btn btn-primary px-4">SAVE</button>
                  </div>
                </div>
              </div>
            </div>
        </section>
        <section class="invoice-preview-wrap">
          <div class="invoice-controls">
            <label for="dataSetSelect">Invoice Data</label>
            <select id="dataSetSelect"></select>
          </div>
          <div class="invoice-canvas" id="invoiceCanvas"></div>
        </section>
      </div>
    </main>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/print.js') }}"></script>
</body>

</html>
