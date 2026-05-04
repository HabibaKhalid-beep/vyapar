/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Estimate / Quotation Page Logic
 * ═══════════════════════════════════════════
 */

$(document).ready(function () {

  // Party search/filter functionality
  $(document).on('input', '.party-search-input', function(e) {
    e.stopPropagation();
    const searchValue = $(this).val().toLowerCase().trim();
    const $partyOptions = $('.party-option');

    $partyOptions.each(function() {
        const $this = $(this);
        const partyName = $.trim($this.find('span').first().text()).toLowerCase();
        const partyPhone = $this.data('phone') ? String($this.data('phone')).toLowerCase() : '';

        if (searchValue === '' || partyName.includes(searchValue) || partyPhone.includes(searchValue)) {
            $this.closest('li').removeClass('d-none');
        } else {
            $this.closest('li').addClass('d-none');
        }
    });
  });

  // Prevent dropdown from closing when clicking on search input
  $(document).on('click', '.party-search-input', function(e) {
    e.stopPropagation();
  });

  // Prevent dropdown from closing when typing in search
  $(document).on('keydown keyup', '.party-search-input', function(e) {
    e.stopPropagation();
  });

  // Clear search input when dropdown closes
  $(document).on('hidden.bs.dropdown', '#partyDropdownMenu', function() {
    $('.party-search-input').val('');
    $('.party-option').closest('li').removeClass('d-none');
  });

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

