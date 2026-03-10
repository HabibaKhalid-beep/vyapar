@extends('layouts.app')

@section('title', 'Vyapar — Create Invoice')
@section('description', 'Create a professional sale invoice with live preview in Vyapar billing software.')
@section('page', 'invoice')

@section('content')

  <div class="row g-4">
    <!-- Left: Invoice Form -->
    <div class="col-md-6">
      <div class="invoice-form-card">
        <h5 class="mb-1 fw-600">Enter details to make your first Sale</h5>
        <p class="text-muted mb-4" style="font-size:13px;">Fill in the form below to create a professional invoice.</p>

        <!-- Invoice Details -->
        <div class="section-title"><i class="fa-solid fa-file-invoice me-2"></i>Invoice Details</div>
        <div class="row g-3 mb-3">
          <div class="col-6">
            <label class="form-label">Invoice No.</label>
            <input type="text" class="form-control" value="1" readonly>
          </div>
          <div class="col-6">
            <label class="form-label">Invoice Date</label>
            <input type="date" class="form-control" value="2026-03-10">
          </div>
        </div>

        <!-- Bill To -->
        <div class="section-title"><i class="fa-solid fa-user me-2"></i>Bill To</div>
        <div class="mb-3">
          <label class="form-label">Customer Name</label>
          <input type="text" class="form-control" placeholder="Enter customer name" id="invoiceCustomerName">
        </div>

        <!-- Add Item -->
        <button class="add-item-btn" id="btnAddSampleItem">
          <i class="fa-solid fa-plus-circle"></i> Add Sample Item
        </button>

        <!-- Calculation -->
        <div class="section-title"><i class="fa-solid fa-calculator me-2"></i>Invoice Calculation</div>
        <div class="row g-3 mb-2">
          <div class="col-6">
            <label class="form-label">Invoice Amount</label>
            <div class="input-group">
              <span class="input-group-text">₹</span>
              <input type="number" class="form-control" value="0.00" id="invoiceAmount">
            </div>
          </div>
          <div class="col-6">
            <label class="form-label">Received</label>
            <div class="input-group">
              <span class="input-group-text">₹</span>
              <input type="number" class="form-control" value="0.00" id="invoiceReceived">
            </div>
          </div>
        </div>
        <div class="balance-box">
          <span class="balance-label"><i class="fa-solid fa-wallet me-2"></i>Balance</span>
          <span class="balance-value">₹ 0.00</span>
        </div>

        <div class="text-center mt-4">
          <button class="btn btn-primary btn-lg px-5" id="btnCreateInvoice" disabled>
            <i class="fa-solid fa-file-circle-plus me-2"></i>Create Your First Invoice
          </button>
        </div>
      </div>
    </div>

    <!-- Right: Invoice Preview -->
    <div class="col-md-6">
      <div class="invoice-preview-wrapper">
        <p class="preview-label"><i class="fa-regular fa-eye me-1"></i> Live Invoice Preview</p>
        <div class="invoice-preview">
          <div class="sample-ribbon">⚡ SAMPLE INVOICE</div>

          <div class="invoice-header-section">
            <div class="invoice-type-title">Tax Invoice</div>
          </div>

          <div class="bill-details-grid">
            <div>
              <div class="detail-label">Bill To</div>
              <div class="detail-value" id="previewBillTo">—</div>
            </div>
            <div style="text-align:right;">
              <div class="detail-label">Invoice Details</div>
              <div class="detail-value">No: 1 &nbsp;|&nbsp; Date: 10/03/2026</div>
            </div>
          </div>

          <table class="preview-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Tax</th>
                <th style="text-align:right;">Amount</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Sample Item</td>
                <td>1</td>
                <td>₹ 100.00</td>
                <td>—</td>
                <td style="text-align:right;">₹ 100.00</td>
              </tr>
            </tbody>
          </table>

          <div style="font-size:11px;color:var(--text-muted);margin-bottom:12px;">
            <strong>Amount in words:</strong> One Hundred Rupees Only
          </div>

          <div class="preview-totals">
            <table>
              <tr><td>Sub Total</td><td>₹ 100.00</td></tr>
              <tr><td>SGST (9%)</td><td>₹ 9.00</td></tr>
              <tr><td>CGST (9%)</td><td>₹ 9.00</td></tr>
              <tr class="total-row"><td>Total</td><td>₹ 118.00</td></tr>
              <tr class="balance-row"><td>Balance Due</td><td>₹ 118.00</td></tr>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script src="{{ asset('js/invoice.js') }}"></script>
@endpush
