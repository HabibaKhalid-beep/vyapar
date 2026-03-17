/**
 * ═══════════════════════════════════════════
 *  VYAPAR — Sale Page Logic
 * ═══════════════════════════════════════════
 */

$(document).ready(function () {
  const storageKey = 'vyaparSearchTerm';
  const $input = $('#searchTransactionsInput');
  const $dropdowns = $('.sale-dropdown');

  // Filter variables
  const $periodSelect = $('#salesPeriodSelect');
  const $firmSelect = $('#salesFirmSelect');
  const $dateRangeDisplay = $('#salesDateRangeDisplay');
  const $customDateRange = $('#customDateRange');
  const $customFrom = $('#salesCustomFrom');
  const $customTo = $('#salesCustomTo');

  let periodFilter = $periodSelect.val() || 'all';
  let firmFilter = $firmSelect.val() || '';
  let customFrom = null;
  let customTo = null;

  // Global search term and column-specific filters
  let globalSearch = '';
  const columnFilters = {};

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

  function parseDateDMY(value) {
    const parts = (value || '').split('/');
    if (parts.length !== 3) return null;
    const day = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10) - 1;
    const year = parseInt(parts[2], 10);
    if (isNaN(day) || isNaN(month) || isNaN(year)) return null;
    return new Date(year, month, day);
  }

  function updateRangeDisplay(from, to) {
    if (!from || !to) return;
    const fmt = (d) => {
      const dd = String(d.getDate()).padStart(2, '0');
      const mm = String(d.getMonth() + 1).padStart(2, '0');
      const yyyy = d.getFullYear();
      return `${dd}/${mm}/${yyyy}`;
    };
    $dateRangeDisplay.text(`${fmt(from)} To ${fmt(to)}`);
  }

  function getPeriodRange(period) {
    const now = new Date();
    let start = null;
    let end = null;

    if (period === 'this_month') {
      start = new Date(now.getFullYear(), now.getMonth(), 1);
      end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    } else if (period === 'last_month') {
      start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
      end = new Date(now.getFullYear(), now.getMonth(), 0);
    } else if (period === 'this_quarter') {
      const quarterStartMonth = Math.floor(now.getMonth() / 3) * 3;
      start = new Date(now.getFullYear(), quarterStartMonth, 1);
      end = new Date(now.getFullYear(), quarterStartMonth + 3, 0);
    } else if (period === 'this_year') {
      start = new Date(now.getFullYear(), 0, 1);
      end = new Date(now.getFullYear(), 11, 31);
    }

    return { start, end };
  }

  function applyFilters() {
    const normalizedSearch = (globalSearch || '').toString().toLowerCase().trim();

    $('table.txn-table tbody tr').each(function () {
      const $row = $(this);
      const rowText = $row.text().toLowerCase();

      let visible = true;

      if (normalizedSearch && rowText.indexOf(normalizedSearch) === -1) {
        visible = false;
      }

      if (visible) {
        for (const colIndex in columnFilters) {
          const filterVal = (columnFilters[colIndex] || '').toString().toLowerCase().trim();
          if (!filterVal) continue;

          const cellText = $row.find('td').eq(parseInt(colIndex, 10)).text().toLowerCase();
          if (cellText.indexOf(filterVal) === -1) {
            visible = false;
            break;
          }
        }
      }

      // Firm filter (party name) - will match the Party Name column (index 2)
      if (visible && firmFilter) {
        const partyName = $row.find('td').eq(2).text().trim().toLowerCase();
        if (partyName !== (firmFilter || '').toLowerCase()) {
          visible = false;
        }
      }

      // Date range filter
      if (visible && periodFilter && periodFilter !== 'all') {
        const dateText = $row.find('td').eq(0).text().trim();
        const rowDate = parseDateDMY(dateText);
        if (!rowDate) {
          visible = false;
        } else {
          let rangeStart = null;
          let rangeEnd = null;

          if (periodFilter === 'custom') {
            rangeStart = customFrom ? new Date(customFrom) : null;
            rangeEnd = customTo ? new Date(customTo) : null;
          } else {
            const range = getPeriodRange(periodFilter);
            rangeStart = range.start;
            rangeEnd = range.end;
          }

          if (rangeStart && rangeEnd) {
            // Normalize time for comparisons
            rangeStart.setHours(0, 0, 0, 0);
            rangeEnd.setHours(23, 59, 59, 999);
            if (rowDate < rangeStart || rowDate > rangeEnd) {
              visible = false;
            }
          }
        }
      }

      $row.toggle(visible);
    });
  }

  function filterTransactions(term) {
    globalSearch = (term || '').toString();
    applyFilters();
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
  globalSearch = getSearchTerm();
  $input.val(globalSearch);

  // Helper to toggle between the display span and the custom date inputs
  function setCustomMode(isCustom) {
    if (isCustom) {
      $dateRangeDisplay.hide();
      $customDateRange.show();
    } else {
      $dateRangeDisplay.show();
      $customDateRange.hide();
    }
  }

  // Initialize period filter display
  const initRange = getPeriodRange(periodFilter);

  if (periodFilter === 'custom') {
    // Default custom to today
    const today = new Date();
    const iso = (d) => d.toISOString().split('T')[0];
    $customFrom.val(iso(today));
    $customTo.val(iso(today));
    customFrom = $customFrom.val();
    customTo = $customTo.val();
    updateRangeDisplay(today, today);
    setCustomMode(true);
  } else if (initRange.start && initRange.end) {
    updateRangeDisplay(initRange.start, initRange.end);
    setCustomMode(false);
  } else {
    setCustomMode(false);
  }

  applyFilters();

  $input.on('input', function () {
    const val = $(this).val();
    setSearchTerm(val);
    filterTransactions(val);
  });

  $periodSelect.on('change', function () {
    periodFilter = $(this).val();

    if (periodFilter === 'custom') {
      $customDateRange.show();
      const today = new Date();
      // Default both from/to to today when custom is selected
      $customFrom.val(iso(today));
      $customTo.val(iso(today));
      customFrom = $customFrom.val();
      customTo = $customTo.val();
      updateRangeDisplay(new Date(customFrom), new Date(customTo));
    } else {
      $customDateRange.hide();
      const range = getPeriodRange(periodFilter);
      if (range.start && range.end) {
        updateRangeDisplay(range.start, range.end);
      }
    }

    applyFilters();
  });

  $firmSelect.on('change', function () {
    firmFilter = $(this).val() || '';
    applyFilters();
  });

  $customFrom.on('change', function () {
    customFrom = $(this).val();
    applyFilters();
  });

  $customTo.on('change', function () {
    customTo = $(this).val();
    applyFilters();
  });

  // Make the search icon clickable/usable
  $('.sale-search-icon').on('click', function () {
    $input.focus();
  });

  // Action buttons
  $('#exportExcel').on('click', function () {
    const rows = [];
    $('table.txn-table thead tr').each(function () {
      const cols = $(this).find('th').not(':last').map(function () {
        return $(this).text().trim();
      }).get();
      rows.push(cols.join(','));
    });
    $('table.txn-table tbody tr:visible').each(function () {
      const cols = $(this).find('td').not(':last').map(function () {
        return '"' + $(this).text().trim().replace(/"/g, '""') + '"';
      }).get();
      rows.push(cols.join(','));
    });
    const csvContent = rows.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const url = URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.setAttribute('href', url);
    link.setAttribute('download', 'transactions.csv');
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
  });

  $('#printTable').on('click', function () {
    window.print();
  });

  $('#signalBtn').on('click', function () {
    const total = $('table.txn-table tbody tr:visible').length;
    alert('Showing ' + total + ' transaction(s) (signal action placeholder).');
  });

  // Row action dropdown items
  $(document).on('click', '.sale-action-menu .dropdown-item', function (e) {
    e.preventDefault();
    const action = $(this).data('action');

    if (action === 'view') {
      const saleId = $(this).closest('.sale-action-menu').data('sale-id');
      if (saleId) {
        window.location.href = `/dashboard/sales/${saleId}/edit`;
      }
    } else if (action === 'convert-return') {
      alert('Convert to Return (placeholder).');
    } else if (action === 'preview-delivery') {
      alert('Preview Delivery Challan (placeholder).');
    } else if (action === 'payment-history') {
      alert('Payment History (placeholder).');
    } else if (action === 'cancel') {
      alert('Cancel Invoice (placeholder).');
    } else if (action === 'delete') {
      const saleId = $(this).closest('.sale-action-menu').data('sale-id');
      if (!saleId) return;

      if (!confirm('Are you sure you want to delete this sale?')) {
        return;
      }

      const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      fetch(`/dashboard/sales/${saleId}`, {
        method: 'DELETE',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': token,
          'Accept': 'application/json',
        },
      })
        .then(res => res.json())
        .then(data => {
          if (data && data.success) {
            // Remove the row from the table
            $(this).closest('tr').remove();
            alert(data.message || 'Sale deleted successfully');
          } else {
            throw new Error((data && data.message) ? data.message : 'Unable to delete sale');
          }
        })
        .catch(err => {
          console.error(err);
          alert('Error deleting sale. See console for details.');
        });
    } else if (action === 'duplicate') {
      alert('Duplicate (placeholder).');
    } else if (action === 'pdf') {
      alert('View PDF (placeholder).');
    } else if (action === 'preview') {
      alert('Preview (placeholder).');
    } else if (action === 'print') {
      window.print();
    } else if (action === 'history') {
      alert('View History (placeholder).');
    }
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
      } else if (action === 'view') {
        alert('View/Edit invoice (placeholder action).');
      } else if (action === 'receive-payment') {
        alert('Receive payment (placeholder action).');
      } else if (action === 'convert-return') {
        alert('Convert to return (placeholder action).');
      } else if (action === 'preview-delivery') {
        alert('Preview delivery challan (placeholder action).');
      } else if (action === 'cancel') {
        alert('Cancel invoice (placeholder action).');
      } else if (action === 'delete') {
        alert('Delete invoice (placeholder action).');
      } else if (action === 'duplicate') {
        alert('Duplicate invoice (placeholder action).');
      } else if (action === 'pdf') {
        alert('Open PDF (placeholder action).');
      } else if (action === 'preview') {
        alert('Preview (placeholder action).');
      } else if (action === 'print') {
        alert('Print (placeholder action).');
      } else if (action === 'history') {
        alert('View history (placeholder action).');
      }
    });
  });

  // Column filter dropdown toggles
  $(document).on('click', '.filter-icon-btn', function (e) {
    e.preventDefault();
    e.stopPropagation();
    const $btn = $(this);
    const $dropdown = $btn.closest('th').find('.column-filter-dropdown');
    const isOpen = $dropdown.hasClass('open');
    closeAllPopups();
    if (!isOpen) {
      $dropdown.addClass('open');
    }
  });

  // Column filter apply / clear actions
  $(document).on('click', '.column-filter-apply', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $btn = $(this);
    const colIndex = $btn.data('column-index');
    const $dropdown = $btn.closest('.column-filter-dropdown');
    const filterValue = $dropdown.find('.column-filter-input').val() || '';

    if (filterValue.trim() === '') {
      delete columnFilters[colIndex];
    } else {
      columnFilters[colIndex] = filterValue;
    }

    applyFilters();
    $dropdown.removeClass('open');
  });

  $(document).on('click', '.column-filter-clear', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const $btn = $(this);
    const colIndex = $btn.data('column-index');
    const $dropdown = $btn.closest('.column-filter-dropdown');

    delete columnFilters[colIndex];
    $dropdown.find('.column-filter-input').val('');

    applyFilters();
    $dropdown.removeClass('open');
  });

  // Row-level action buttons
  $(document).on('click', '.row-action-print', function () {
    // Print the current page or invoice
    window.print();
  });

  $(document).on('click', '.row-action-share', function () {
    const rowText = $(this).closest('tr').find('td').map(function () {
      return $(this).text().trim();
    }).get().join(' | ');

    if (navigator.share) {
      navigator.share({
        title: 'Invoice details',
        text: rowText,
      }).catch(() => {
        // ignore
      });
    } else {
      alert('Share is not supported in this browser.');
    }
  });

  $(document).on('click', function (e) {
    // Keep dropdowns open while interacting with their content (inputs/buttons)
    if ($(e.target).closest('.sale-dropdown, .column-filter-header, .column-filter-dropdown').length === 0) {
      closeAllPopups();
    }
  });
});
