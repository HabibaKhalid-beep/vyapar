@extends('layouts.app')

@section('title', 'Vyapar — Invoice Preview')
@section('description', 'Preview, print, and share your invoices from Vyapar billing software.')
@section('page', 'print-preview')

@section('content')
  @php($activePaymentIn = $paymentIn ?? null)

  <div class="print-preview-wrapper">
    <div class="print-invoice">
      <!-- Company Header -->
      <div class="company-header">
        <div>
          <div class="company-name">{{ config('app.name', 'Vyapar') }}</div>
          <div style="font-size:12px;color:var(--text-muted);margin-top:4px;">{{ $activePaymentIn ? 'Payment In Receipt' : 'GSTIN: 22AAAAA0000A1Z5' }}</div>
        </div>
        <div class="company-contact">
          @if($activePaymentIn)
            {{ $activePaymentIn->party?->billing_address ?: 'Party address not available' }}<br>
            Phone: {{ $activePaymentIn->party?->phone ?: 'N/A' }}<br>
            Bank: {{ $activePaymentIn->bankAccount?->display_name ?: 'Cash / Not selected' }}
          @else
            123, Market Road, Sector 5<br>
            New Delhi, 110001<br>
            Phone: +91 98765 43210<br>
            Email: store@grocery.com
          @endif
        </div>
      </div>

      <div class="invoice-title-print">{{ $activePaymentIn ? 'Payment In Receipt' : 'Tax Invoice' }}</div>

      <div class="bill-details-grid">
        <div>
          <div class="detail-label">Bill To</div>
          <div class="detail-value fw-600">{{ $activePaymentIn?->party?->name ?: 'abc' }}</div>
          <div class="detail-value" style="font-size:12px;color:var(--text-muted);">{{ $activePaymentIn?->party?->phone ?: '+91 98765 43210' }}</div>
        </div>
        <div style="text-align:right;">
          <div class="detail-label">Invoice No.</div>
          <div class="detail-value">#{{ $activePaymentIn?->receipt_no ?: '001' }}</div>
          <div class="detail-label mt-2">Date</div>
          <div class="detail-value">{{ $activePaymentIn?->date ? \Carbon\Carbon::parse($activePaymentIn->date)->format('d/m/Y') : '10/03/2026' }}</div>
        </div>
      </div>

      <table class="preview-table">
        <thead>
          <tr>
            <th>#</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Ref</th>
            <th style="text-align:right;">Amount</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>{{ $activePaymentIn ? ucfirst($activePaymentIn->payment_type) . ' Payment' : 'Rice (5kg)' }}</td>
            <td>1</td>
            <td>₹ {{ number_format((float) ($activePaymentIn?->amount ?: 250), 2) }}</td>
            <td>{{ $activePaymentIn?->reference_no ?: 'GST 18%' }}</td>
            <td style="text-align:right;">₹ {{ number_format((float) ($activePaymentIn?->amount ?: 500), 2) }}</td>
          </tr>
        </tbody>
      </table>

      <div style="font-size:11px;color:var(--text-muted);margin-bottom:12px;">
        <strong>Amount in words:</strong> {{ $activePaymentIn?->description ?: 'Seven Hundred and Twenty Five Rupees Only' }}
      </div>

      <div class="preview-totals">
        <table>
          <tr><td>Sub Total</td><td>₹ {{ number_format((float) ($activePaymentIn?->amount ?: 725), 2) }}</td></tr>
          <tr><td>Received</td><td>₹ {{ number_format((float) ($activePaymentIn?->amount ?: 50.63), 2) }}</td></tr>
          <tr class="total-row"><td>Total</td><td>₹ {{ number_format((float) ($activePaymentIn?->amount ?: 826.25), 2) }}</td></tr>
          <tr class="balance-row"><td>Balance Due</td><td>₹ 0.00</td></tr>
        </table>
      </div>

      <!-- Terms -->
      <div class="terms-section">
        <h6>Terms & Conditions</h6>
        <p>
          @if($activePaymentIn)
            1. Payment has been received from the selected party.<br>
            2. Receipt No: {{ $activePaymentIn->receipt_no ?: 'N/A' }}<br>
            3. Reference No: {{ $activePaymentIn->reference_no ?: 'N/A' }}
          @else
            1. Goods once sold will not be taken back or exchanged.<br>
            2. All disputes are subject to New Delhi jurisdiction only.<br>
            3. Payment due within 30 days of invoice date.
          @endif
        </p>
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
