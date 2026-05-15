/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Payment Out Page Logic
 * ═══════════════════════════════════════════
 */

$(document).ready(function () {

  // Select voucher from left list
  $(document).on('click', '#paymentOutList li', function () {
    $('#paymentOutList li').removeClass('active');
    $(this).addClass('active');

    const title = $(this).find('.entity-name').text();
    const amount = $(this).find('.entity-balance').text().replace('-', '');
    $('#paymentOutDetailTitle').text(title);
    $('#paymentOutTxnBody').html(`
      <tr>
        <td>Bank</td>
        <td>Auto</td>
        <td>Sample details for ${title}</td>
        <td style="text-align:right;">${amount}</td>
      </tr>
    `);
  });

  // Search filter
  $('#paymentOutSearchInput').on('input', function () {
    const query = $(this).val().toLowerCase();
    $('#paymentOutList li').each(function () {
      const name = $(this).find('.entity-name').text().toLowerCase();
      $(this).toggle(name.includes(query));
    });
  });

});

