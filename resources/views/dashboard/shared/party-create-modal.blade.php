<div class="modal fade" id="addPartyModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fa-solid fa-user-plus me-2"></i>Add Party</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="addPartyForm">
          @csrf
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-600">Party Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" placeholder="Enter party name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Phone Number</label>
              <input type="tel" name="phone" class="form-control" placeholder="Enter phone number">
            </div>
            <div class="col-md-6">
              <label class="form-label">Billing Address</label>
              <textarea name="billing_address" class="form-control" rows="3" placeholder="Billing address"></textarea>
            </div>
            <div class="col-md-6">
              <label class="form-label">Shipping Address</label>
              <textarea name="shipping_address" class="form-control" rows="3" placeholder="Shipping address"></textarea>
            </div>
            <div class="col-md-4">
              <label class="form-label">Opening Balance</label>
              <input type="number" name="opening_balance" class="form-control" min="0" step="0.01" value="0">
            </div>
            <div class="col-md-4">
              <label class="form-label">Transaction Type</label>
              <select name="transaction_type" class="form-select">
                <option value="">None</option>
                <option value="receive">To Receive</option>
                <option value="pay">To Pay</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">Party Type</label>
              <select name="party_type" class="form-select">
                <option value="customer">Customer</option>
                <option value="supplier">Supplier</option>
              </select>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-primary" id="btnSaveNewParty">Save & New</button>
        <button type="button" class="btn btn-primary" id="btnSaveParty">Save</button>
      </div>
    </div>
  </div>
</div>
