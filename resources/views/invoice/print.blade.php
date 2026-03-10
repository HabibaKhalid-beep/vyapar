@extends('layouts.app')

@section('title', 'Vyapar — Invoice Preview')
@section('description', 'Preview, print, and share your invoices from Vyapar billing software.')
@section('page', 'print-preview')

@section('content')

  <div class="print-preview-wrapper">
    <div class="print-invoice">
      <!-- Company Header -->
      <div class="company-header">
        <div>
          <div class="company-name">Grocery Store</div>
          <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">GSTIN: 22AAAAA0000A1Z5</div>
        </div>
        <div class="company-contact">
          123, Market Road, Sector 5<br>
          New Delhi, 110001<br>
          Phone: +91 98765 43210<br>
          Email: store@grocery.com
        </div>
      </div>

      <div class="invoice-title-print">Tax Invoice</div>

      <div class="bill-details-grid">
        <div>
          <div class="detail-label">Bill To</div>
          <div class="detail-value fw-600">abc</div>
          <div class="detail-value" style="font-size:12px;color:var(--text-muted);">+91 98765 43210</div>
        </div>
        <div style="text-align:right;">
          <div class="detail-label">Invoice No.</div>
          <div class="detail-value">#001</div>
          <div class="detail-label mt-2">Date</div>
          <div class="detail-value">10/03/2026</div>
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
            <td>Rice (5kg)</td>
            <td>2</td>
            <td>₹ 250.00</td>
            <td>GST 18%</td>
            <td style="text-align:right;">₹ 500.00</td>
          </tr>
          <tr>
            <td>2</td>
            <td>Sugar (1kg)</td>
            <td>5</td>
            <td>₹ 45.00</td>
            <td>GST 5%</td>
            <td style="text-align:right;">₹ 225.00</td>
          </tr>
        </tbody>
      </table>

      <div style="font-size:11px;color:var(--text-muted);margin-bottom:12px;">
        <strong>Amount in words:</strong> Seven Hundred and Twenty Five Rupees Only
      </div>

      <div class="preview-totals">
        <table>
          <tr><td>Sub Total</td><td>₹ 725.00</td></tr>
          <tr><td>SGST</td><td>₹ 50.63</td></tr>
          <tr><td>CGST</td><td>₹ 50.63</td></tr>
          <tr class="total-row"><td>Total</td><td>₹ 826.25</td></tr>
          <tr class="balance-row"><td>Balance Due</td><td>₹ 826.25</td></tr>
        </table>
      </div>

      <!-- Terms -->
      <div class="terms-section">
        <h6>Terms & Conditions</h6>
        <p>1. Goods once sold will not be taken back or exchanged.<br>
           2. All disputes are subject to New Delhi jurisdiction only.<br>
           3. Payment due within 30 days of invoice date.</p>
      </div>

      <!-- Signatory -->
      <div class="signatory-box">
        <p>Authorized Signatory</p>
      </div>
    </div>
  </div>

  <!-- Floating Actions -->
  <div class="floating-actions" id="printFloatingActions" style="display:none;">
    <button class="btn btn-outline-secondary"><i class="fa-solid fa-print me-1"></i> Print</button>
    <button class="btn btn-primary"><i class="fa-solid fa-download me-1"></i> Download</button>
    <button class="btn btn-whatsapp"><i class="fa-brands fa-whatsapp me-1"></i> Share on WhatsApp</button>
  </div>

@endsection

@push('scripts')
  <script src="{{ asset('js/print-preview.js') }}"></script>
@endpush
