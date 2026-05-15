/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Purchase Bill Page Logic
 * ═══════════════════════════════════════════
 */

$(document).ready(function () {

  // Supplier live preview
  $('#purchaseSupplierName').on('input', function () {
    const val = $(this).val().trim() || '—';
    $('#previewPurchaseSupplier').text(val);
  });

  // Amount / paid / balance
  function updatePurchaseBalance() {
    const amount = parseFloat($('#purchaseBillAmount').val()) || 0;
    const paid = parseFloat($('#purchaseBillPaid').val()) || 0;
    const balance = amount - paid;

    $('#purchaseBillBalance').text('₹ ' + balance.toFixed(2));
    $('#purchaseTotalCell').text('₹ ' + amount.toFixed(2));
    $('#btnSavePurchaseBill').prop('disabled', amount <= 0);
  }

  $('#purchaseBillAmount, #purchaseBillPaid').on('input', updatePurchaseBalance);

  // Sample item shortcut
  let sampleAdded = false;
  $('#btnAddPurchaseItem').on('click', function () {
    if (sampleAdded) return;
    sampleAdded = true;

    $('#purchaseItemsBody').html(`
      <tr>
        <td>1</td>
        <td>Sample Purchase Item</td>
        <td>10</td>
        <td>₹ 50.00</td>
        <td>GST 18%</td>
        <td style="text-align:right;">₹ 500.00</td>
      </tr>
    `);

    $('#purchaseBillAmount').val('500.00');
    updatePurchaseBalance();

    $(this).html('<i class="fa-solid fa-check-circle me-1"></i> Sample Item Added')
      .css({
        borderColor: 'var(--success-green)',
        color: 'var(--success-green)',
        background: 'var(--success-bg)'
      });
  });

});

