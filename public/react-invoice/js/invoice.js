/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Create Invoice Page Logic
 * ═══════════════════════════════════════════
 */

$(document).ready(function () {

  // ─── Invoice Form: Live Preview Update ────────
  $('#invoiceCustomerName').on('input', function () {
    const val = $(this).val().trim() || '—';
    $('#previewBillTo').text(val);
  });

  // ─── Invoice Calculation: Balance = Amount - Received ──
  function updateBalance() {
    const amount = parseFloat($('#invoiceAmount').val()) || 0;
    const received = parseFloat($('#invoiceReceived').val()) || 0;
    const balance = amount - received;
    $('.balance-box .balance-value').text('₹ ' + balance.toFixed(2));

    // Enable/disable create button
    if (amount > 0) {
      $('#btnCreateInvoice').prop('disabled', false);
    } else {
      $('#btnCreateInvoice').prop('disabled', true);
    }
  }
  $('#invoiceAmount, #invoiceReceived').on('input', updateBalance);

  // ─── Add Sample Item ──────────────────────────
  let sampleItemAdded = false;
  $('#btnAddSampleItem').on('click', function () {
    if (sampleItemAdded) return;
    sampleItemAdded = true;

    $(this).html('<i class="fa-solid fa-check-circle me-1"></i> Sample Item Added')
      .css({ borderColor: 'var(--success-green)', color: 'var(--success-green)', background: 'var(--success-bg)' });

    // Update amount
    $('#invoiceAmount').val('100.00');
    updateBalance();
  });

  // ─── Create Invoice → Navigate to Print Preview ─
  $('#btnCreateInvoice').on('click', function () {
    window.location.href = '/invoice/print';
  });

});
