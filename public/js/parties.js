/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Parties Page Logic
 * ═══════════════════════════════════════════
 */

$(document).ready(function () {

  // ─── Party List Selection ─────────────────────
  $(document).on('click', '#partyList li', function () {
    $('#partyList li').removeClass('active');
    $(this).addClass('active');

    const name = $(this).find('.entity-name').text();
    $('#partyDetailName').text(name);

    // Show or hide transactions based on party
    const partyKey = $(this).data('party');
    if (partyKey === 'abc') {
      $('#partyTxnTable tbody').html(`
        <tr>
          <td><span class="badge bg-primary bg-opacity-10 text-primary">Sale</span></td>
          <td>#001</td>
          <td>10/03/2026</td>
          <td>₹ 500.00</td>
          <td class="text-success-green fw-600">₹ 500.00</td>
        </tr>
      `);
    } else {
      $('#partyTxnTable tbody').html(`
        <tr>
          <td colspan="5" class="text-center py-5">
            <div class="empty-state" style="padding:20px;">
              <i class="fa-regular fa-folder-open d-block" style="font-size:40px;color:#d0d0d0;margin-bottom:10px;"></i>
              <h6 style="font-size:14px;font-weight:600;">No Transactions</h6>
              <p style="font-size:12px;color:var(--text-muted);">No transactions found for this party.</p>
            </div>
          </td>
        </tr>
      `);
    }
  });

  // ─── Party Search Filter ──────────────────────
  $('#partySearchInput').on('input', function () {
    const query = $(this).val().toLowerCase();
    $('#partyList li').each(function () {
      const name = $(this).find('.entity-name').text().toLowerCase();
      $(this).toggle(name.includes(query));
    });
  });

  // ─── Modal: Save Party ────────────────────────
  $('#btnSaveParty').on('click', function () {
    const name = $('#partyNameInput').val().trim();
    if (!name) {
      $('#partyNameInput').addClass('is-invalid').focus();
      return;
    }
    $('#partyList').append(`
      <li data-party="${name.toLowerCase().replace(/\s+/g, '-')}">
        <span class="entity-name">${name}</span>
        <span class="entity-balance positive">₹ 0.00</span>
      </li>
    `);

    bootstrap.Modal.getInstance(document.getElementById('addPartyModal')).hide();
    $('#partyNameInput').val('').removeClass('is-invalid');
    $('#partyPhoneInput').val('');
  });

  $('#btnSaveNewParty').on('click', function () {
    const name = $('#partyNameInput').val().trim();
    if (!name) {
      $('#partyNameInput').addClass('is-invalid').focus();
      return;
    }
    $('#partyList').append(`
      <li data-party="${name.toLowerCase().replace(/\s+/g, '-')}">
        <span class="entity-name">${name}</span>
        <span class="entity-balance positive">₹ 0.00</span>
      </li>
    `);
    $('#partyNameInput').val('').focus();
    $('#partyPhoneInput').val('');
  });

});
