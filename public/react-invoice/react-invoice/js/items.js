/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Items Page Logic
 * ═══════════════════════════════════════════
 */

$(document).ready(function () {

  // ─── Item List Selection ──────────────────────
  $(document).on('click', '#itemList li', function () {
    $('#itemList li').removeClass('active');
    $(this).addClass('active');

    const name = $(this).find('.entity-name').text();
    const price = $(this).find('.entity-balance').text();
    $('#itemDetailName').text(name);
    $('.split-right .entity-detail-meta').html('<i class="fa-solid fa-tags me-1"></i> Sale Price: ' + price);
  });

  // ─── Item Search Filter ───────────────────────
  $('#itemSearchInput').on('input', function () {
    const query = $(this).val().toLowerCase();
    $('#itemList li').each(function () {
      const name = $(this).find('.entity-name').text().toLowerCase();
      $(this).toggle(name.includes(query));
    });
  });

  // ─── Item Type Toggle (Product / Service) ─────
  $('#itemTypeSwitch').on('change', function () {
    $('#itemTypeLabel').text(this.checked ? 'Service' : 'Product');
  });

  // ─── Modal: Save Item ─────────────────────────
  $('#btnSaveItem').on('click', function () {
    const name = $('#itemNameInput').val().trim();
    if (!name) {
      $('#itemNameInput').addClass('is-invalid').focus();
      return;
    }
    $('#itemList').append(`
      <li data-item="${name.toLowerCase().replace(/\s+/g, '-')}">
        <span class="entity-name">${name}</span>
        <span class="entity-balance positive">₹ 0.00</span>
      </li>
    `);

    bootstrap.Modal.getInstance(document.getElementById('addItemModal')).hide();
    $('#itemNameInput').val('').removeClass('is-invalid');
  });

  $('#btnSaveNewItem').on('click', function () {
    const name = $('#itemNameInput').val().trim();
    if (!name) {
      $('#itemNameInput').addClass('is-invalid').focus();
      return;
    }
    $('#itemList').append(`
      <li data-item="${name.toLowerCase().replace(/\s+/g, '-')}">
        <span class="entity-name">${name}</span>
        <span class="entity-balance positive">₹ 0.00</span>
      </li>
    `);
    $('#itemNameInput').val('').focus();
  });

});
