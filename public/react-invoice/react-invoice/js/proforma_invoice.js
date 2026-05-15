/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Estimate / Quotation Page Logic
 * ═══════════════════════════════════════════
 */

$(document).ready(function () {

  // Live update of "Bill To"
  $('#estimateCustomerName').on('input', function () {
    const val = $(this).val().trim() || '—';
    $('#previewEstimateBillTo').text(val);
  });

  // Keep total in sync with amount
  $('#estimateAmount').on('input', function () {
    const amount = parseFloat($(this).val()) || 0;
    $('#estimateTotalCell').text('₹ ' + amount.toFixed(2));
    $('#btnSaveEstimate').prop('disabled', amount <= 0);
  });

  // Add sample item helper
  let sampleAdded = false;
  $('#btnAddEstimateItem').on('click', function () {
    if (sampleAdded) return;
    sampleAdded = true;

    $('#estimateItemsBody').html(`
      <tr>
        <td>1</td>
        <td>Sample Item</td>
        <td>1</td>
        <td>₹ 100.00</td>
        <td>—</td>
        <td style="text-align:right;">₹ 100.00</td>
      </tr>
    `);

    $('#estimateAmount').val('100.00').trigger('input');

    $(this).html('<i class="fa-solid fa-check-circle me-1"></i> Sample Item Added')
      .css({
        borderColor: 'var(--success-green)',
        color: 'var(--success-green)',
        background: 'var(--success-bg)'
      });
  });

});

