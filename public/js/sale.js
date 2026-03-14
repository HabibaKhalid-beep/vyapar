/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Sale Page Logic
 * ═══════════════════════════════════════════
 */

$(document).ready(function () {
  const storageKey = 'vyaparSearchTerm';
  const $input = $('#searchTransactionsInput');
  const $dropdowns = $('.sale-dropdown');

  function getSearchTerm() {
    return localStorage.getItem(storageKey) || '';
  }

  function setSearchTerm(value) {
    const trimmed = (value || '').trim();
    if (trimmed) {
      localStorage.setItem(storageKey, trimmed);
    } else {
      localStorage.removeItem(storageKey);
    }
  }

  function closeAllDropdowns() {
    $('.sale-dropdown').removeClass('open');
  }

  function closeAllColumnFilters() {
    $('.column-filter-dropdown').removeClass('open');
  }

  function closeAllPopups() {
    closeAllDropdowns();
    closeAllColumnFilters();
  }

  // Initialize
  $input.val(getSearchTerm());

  $input.on('focus', function () {
    $('.sale-topbar').addClass('search-active');
  });

  $input.on('blur', function () {
    setTimeout(() => {
      $('.sale-topbar').removeClass('search-active');
    }, 150);
  });

  $input.on('input', function () {
    setSearchTerm($(this).val());
  });

  $dropdowns.each(function () {
    const $dropdown = $(this);
    const $toggle = $dropdown.find('.sale-dropdown-toggle');
    const $menu = $dropdown.find('.sale-dropdown-menu');

    $toggle.on('click', function (e) {
      e.stopPropagation();
      const isOpen = $dropdown.hasClass('open');
      closeAllPopups();
      if (!isOpen) {
        $dropdown.addClass('open');
      }
    });

    $menu.on('click', 'button', function (e) {
      e.stopPropagation();
      const action = $(this).data('action');
      closeAllPopups();

      if (action === 'notifications') {
        alert('No notifications yet.');
      } else if (action === 'settings') {
        alert('Settings coming soon.');
      } else if (action === 'all') {
        alert('Showing all invoices.');
      } else if (action === 'paid') {
        alert('Showing paid invoices.');
      } else if (action === 'unpaid') {
        alert('Showing unpaid invoices.');
      }
    });
  });

  // Column filter dropdown toggles
  $(document).on('click', '.filter-icon-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const $btn = $(this);
    const $dropdown = $btn.closest('.column-filter-header').find('.column-filter-dropdown');
    const isOpen = $dropdown.hasClass('open');
    closeAllPopups();
    if (!isOpen) {
      $dropdown.addClass('open');
    }
  });

  $(document).on('click', function (e) {
    if ($(e.target).closest('.sale-dropdown, .column-filter-header').length === 0) {
      closeAllPopups();
    }
  });
});
