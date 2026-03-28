<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <title>POS - Vyapar Clone</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <!-- <link rel="stylesheet" href="pos_style.css" /> -->
  <link rel="stylesheet" href="{{ asset('css/pos.css') }}" />
</head>

<body class="pos-body">
  <div class="pos-wrapper">
    <div class="pos-left">
      <div class="pos-search mb-3">
        <div class="input-group input-group-lg">
          <input type="text" class="form-control border-primary"
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
            <button class="btn btn-shortcut">Change Quantity <span>[F2]</span></button>
            <button class="btn btn-shortcut">Item Discount <span>[F3]</span></button>
            <button class="btn btn-shortcut">Order Discount <span>[F4]</span></button>
            <button class="btn btn-shortcut">Delete Item <span>[F6]</span></button>
            <button class="btn btn-shortcut">Save Bill <span>[F8]</span></button>
            <button class="btn btn-shortcut">Hold Bill <span>[F9]</span></button>
            <button class="btn btn-shortcut">Recent Bills <span>[F10]</span></button>
            <button class="btn btn-shortcut">Select Customer <span>[F11]</span></button>
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
                <input type="text" class="form-control form-control-sm dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false" placeholder="Search customer by Name or Phone No" />
                <ul class="dropdown-menu w-100 shadow-sm mt-1">
                  <li><a class="dropdown-item text-primary fw-bold" href="#" data-bs-toggle="modal"
                      data-bs-target="#addCustomerModal">+ Add Customer</a></li>
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
            <button class="btn btn-link p-0 small text-decoration-none">
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

          <button class="btn btn-save-print w-100 mb-2">
            Save &amp; Print Bill [Ctrl+P]
          </button>
          <button class="btn btn-save-print-dark w-100 mb-2">
            Other Credit/ Payments [Ctrl+M]
          </button>

          <div class="d-flex justify-content-between small text-muted">
            <span>New Bill [Ctrl+T]</span>
            <span>Items Master [Ctrl+M]</span>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Customer Modal -->
  <div class="modal fade custom-modal" id="addCustomerModal" tabindex="-1" aria-labelledby="addCustomerModalLabel"
    aria-hidden="true" data-bs-keyboard="false" data-bs-backdrop="static">
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
                <input type="text" class="custom-input" placeholder="Customer Name" autofocus />
              </div>

              <div class="form-group">
                <label>Phone Number</label>
                <input type="text" class="custom-input" placeholder="Phone Number" />
              </div>

              <div class="form-group">
                <label>Billing Address</label>
                <textarea class="custom-input" id="billingAddress" placeholder="Billing Address"></textarea>
              </div>

              <div class="form-group">
                <label>Shipping Address</label>
                <textarea class="custom-input" id="shippingAddress" placeholder="Shipping Address"></textarea>
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

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/pos-shortcuts.js') }}"></script>
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
</body>

</html>
