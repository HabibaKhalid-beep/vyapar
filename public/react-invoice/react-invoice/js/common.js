/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Common Logic (shared across pages)
 * ═══════════════════════════════════════════
 */

$(document).ready(function () {

  // ─── Sidebar Dropdown Toggle ──────────────────
  $(document).on('click', '.sidebar-dropdown-toggle', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const submenu = $(this).next('.sidebar-submenu');

    // Close other submenus
    $('.sidebar-submenu').not(submenu).removeClass('open');
    $('.sidebar-dropdown-toggle').not(this).removeClass('expanded');

    // Toggle this one
    $(this).toggleClass('expanded');
    submenu.toggleClass('open');
  });

  // ─── Sidebar Badge "+" → Open Modal (if on same page) ──
  $(document).on('click', '.badge-plus[data-modal]', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const modalId = $(this).data('modal');
    const modalEl = document.getElementById(modalId);
    if (modalEl) {
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
    }
  });

  // ─── Keyboard Shortcut: Ctrl+F → Focus Sidebar Search ─
  $(document).on('keydown', function (e) {
    if (e.ctrlKey && e.key === 'f') {
      if (!$(e.target).is('input, textarea, select')) {
        e.preventDefault();
        $('#sidebarSearch').focus();
      }
    }
  });

  // ─── Business Name Bar (if present) ─────────
  $('#saveBusinessName').on('click', function () {
    const name = $('#businessNameInput').val().trim();
    if (name) {
      $('.brand-logo').html('<i class="fa-solid fa-bolt"></i> ' + name);
      $('#businessNameBar').slideUp(200);
    }
  });

  // ─── Clear validation on input ────────────────
  $(document).on('input', '.is-invalid', function () {
    $(this).removeClass('is-invalid');
  });

});
