/**
 * VYAPAR — Bank Accounts Page Logic
 */

document.addEventListener('DOMContentLoaded', () => {
  const list = document.getElementById('bankList');
  const sidebarSearch = document.getElementById('bankSearchInput');
  const tableSearch = document.getElementById('tableSearchInput');

  const detailName = document.getElementById('bankDetailName');
  const detailAccountNumber = document.getElementById('bankDetailAccountNumber');
  const detailBankName = document.getElementById('bankDetailBankName');
  const detailOpeningBalance = document.getElementById('bankDetailOpeningBalance');

  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || window.App?.csrfToken || '';

  // Table element used for filtering & actions
  const bankTable = document.getElementById('bankTable');

  // Keep track of whether a date filter is currently active (clicking the date column)
  let activeFilterDate = null;

  function selectBankItem(item) {
    if (!item) return;

    list.querySelectorAll('li').forEach(li => li.classList.remove('active'));
    item.classList.add('active');

    const bankId = item.dataset.bank;
    const name = item.querySelector('.entity-name')?.textContent?.trim() ?? '';
    const accountNumber = item.dataset.accountNumber || '-';
    const bankName = item.dataset.bankName || '-';
    const openingBalance = item.dataset.openingBalance ? Number(item.dataset.openingBalance) : 0;

    detailName.textContent = name || 'Select a bank account';
    detailAccountNumber.textContent = accountNumber || '-';
    detailBankName.textContent = bankName || '-';
    detailOpeningBalance.textContent = `₹ ${openingBalance.toFixed(2)}`;

    // Reset any date-based table filter when selecting a different bank
    activeFilterDate = null;

    // Filter the table to only show the selected bank account
    filterTableByBankId(bankId);
  }

  function filterTableByBankId(bankId) {
    if (!bankTable) return;
    const rows = Array.from(bankTable.tBodies[0].rows);

    rows.forEach(row => {
      if (!bankId) {
        row.style.display = '';
        row.classList.remove('active-row');
        return;
      }

      const match = row.dataset.bankId === bankId;
      row.style.display = match ? '' : 'none';
      row.classList.toggle('active-row', match);
    });
  }

  const addBankButton = document.querySelector('.btn-add-entity');
  const bankForm = document.getElementById('bankForm');
  const bankFormMethod = document.getElementById('bankFormMethod');
  const bankIdField = document.getElementById('bankIdField');
  const modalTitle = document.getElementById('addBankModalLabel');

  if (list) {
    list.addEventListener('click', (event) => {
      const item = event.target.closest('li');
      if (!item) return;
      selectBankItem(item);
    });

    // Auto-select first item on load
    const first = list.querySelector('li.active') || list.querySelector('li');
    if (first) {
      selectBankItem(first);
    }
  }

  if (addBankButton) {
    addBankButton.addEventListener('click', () => {
      openBankModal('add');
    });
  }

  // Make the detail panel edit icon open the selected bank in edit mode
  const detailEditButton = document.querySelector('.entity-detail-name .btn-icon');
  if (detailEditButton) {
    detailEditButton.addEventListener('click', () => {
      const activeItem = document.querySelector('li.active[data-bank]');
      if (!activeItem) return;
      const bankId = activeItem.dataset.bank;
      openBankModal('edit', bankId);
      const editModalEl = document.getElementById('addBankModal');
      if (editModalEl && window.bootstrap) {
        const modal = bootstrap.Modal.getOrCreateInstance(editModalEl);
        modal.show();
      }
    });
  }

  // Prepare Add/Edit modal
  function openBankModal(mode, bankId = null) {
    if (!bankForm) return;

    // Restore modal defaults
    bankForm.reset();
    bankFormMethod.value = 'POST';
    bankIdField.value = '';
    bankForm.action = '/dashboard/bank-accounts';

    const submitButton = bankForm.querySelector('#bankFormSubmit');

    // Ensure the modal is in a predictable state
    const inputs = bankForm.querySelectorAll('input, select, textarea');
    inputs.forEach((input) => {
      input.disabled = false;
    });
    submitButton.style.display = '';

    if (mode === 'view' && bankId) {
      modalTitle.textContent = 'View Bank Account';
      submitButton.style.display = 'none';
      bankFormMethod.value = 'GET';
      bankForm.action = `/dashboard/bank-accounts/${bankId}`;

      // Load bank data via AJAX
      loadBankDetails(bankId);
      return;
    }

    if (mode === 'edit' && bankId) {
      modalTitle.textContent = 'Edit Bank Account';
      submitButton.textContent = 'Update';
      bankFormMethod.value = 'PUT';
      bankIdField.value = bankId;
      bankForm.action = `/dashboard/bank-accounts/${bankId}`;

      // Load bank data via AJAX
      loadBankDetails(bankId);
      return;
    }

    // Default: add new bank
    modalTitle.textContent = 'Add Bank Account';
    submitButton.textContent = 'Save Details';
  }

  function loadBankDetails(bankId) {
    if (!bankForm) return;

    fetch(`/dashboard/bank-accounts/${bankId}`, { headers: { 'Accept': 'application/json' } })
      .then(async (res) => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
          const message = data?.message || 'Could not load bank account details.';
          throw new Error(message);
        }
        return data;
      })
      .then((data) => {
        bankForm.querySelector('[name="display_name"]').value = data.display_name || '';
        bankForm.querySelector('[name="opening_balance"]').value = data.opening_balance ?? '';
        bankForm.querySelector('[name="as_of_date"]').value = data.as_of_date ?? '';
        bankForm.querySelector('[name="account_number"]').value = data.account_number ?? '';
        bankForm.querySelector('[name="swift_code"]').value = data.swift_code ?? '';
        bankForm.querySelector('[name="iban"]').value = data.iban ?? '';
        bankForm.querySelector('[name="bank_name"]').value = data.bank_name ?? '';
        bankForm.querySelector('[name="account_holder_name"]').value = data.account_holder_name ?? '';
        bankForm.querySelector('[name="print_on_invoice"]').checked = !!data.print_on_invoice;
      })
      .catch((error) => {
        showToast(error?.message || 'Could not load bank account details.', 'danger');
      });
  }

  function normalizeDate(str) {
    // Support both dd/mm/yyyy and yyyy-mm-dd
    if (!str) return '';
    const trimmed = str.trim();
    if (/^\d{4}-\d{2}-\d{2}$/.test(trimmed)) {
      return trimmed;
    }
    const parts = trimmed.split('/');
    if (parts.length === 3) {
      const [d, m, y] = parts;
      const fullYear = y.length === 2 ? `20${y}` : y;
      return `${fullYear}-${m.padStart(2, '0')}-${d.padStart(2, '0')}`;
    }
    return trimmed.toLowerCase();
  }

  function applySearchFilter() {
    const q = search.value.trim().toLowerCase();
    const isDateSearch = /^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}$/.test(q) || /^\d{4}-\d{2}-\d{2}$/.test(q);
    const normalizedDate = normalizeDate(q);

    list.querySelectorAll('li').forEach(li => {
      const name = li.querySelector('.entity-name')?.textContent?.toLowerCase() || '';
      const bankName = li.dataset.bankName?.toLowerCase() || '';
      const accountNumber = li.dataset.accountNumber?.toLowerCase() || '';
      const asOfDate = normalizeDate(li.dataset.asOfDate || '');

      if (q === '') {
        li.style.display = '';
        return;
      }

      if (isDateSearch) {
        li.style.display = asOfDate.includes(normalizedDate) ? '' : 'none';
        return;
      }

      const matches = [name, bankName, accountNumber].some(val => val.includes(q));
      li.style.display = matches ? '' : 'none';
    });
  }

  if (sidebarSearch) {
    sidebarSearch.addEventListener('input', applySearchFilter);
  }

  function applyTableFilter() {
    if (!bankTable || !tableSearch) return;
    const q = tableSearch.value.trim().toLowerCase();

    Array.from(bankTable.tBodies[0].rows).forEach(row => {
      const text = Array.from(row.cells)
        .slice(0, -1) // exclude action column
        .map(cell => cell.textContent.trim().toLowerCase())
        .join(' ');
      row.style.display = q === '' || text.includes(q) ? '' : 'none';
    });
  }

  if (tableSearch) {
    tableSearch.addEventListener('input', applyTableFilter);
  }

  const focusSearchBtn = document.getElementById('focusSearchBtn');
  if (focusSearchBtn) {
    focusSearchBtn.addEventListener('click', () => {
      if (tableSearch) {
        tableSearch.focus();
        return;
      }
      if (sidebarSearch) {
        sidebarSearch.focus();
      }
    });
  }

  // Clicking a date cell filters the table to only show rows for that date.
  if (bankTable) {
    bankTable.addEventListener('click', (event) => {
      const cell = event.target.closest('td');
      if (!cell) return;

      const row = cell.closest('tr');
      if (!row) return;

      const cells = Array.from(row.children);
      const dateColumnIndex = 4; // As of Date column
      const clickedIndex = cells.indexOf(cell);

      // If clicked outside the date column, clear the date filter
      if (clickedIndex !== dateColumnIndex) {
        if (activeFilterDate) {
          activeFilterDate = null;
          Array.from(bankTable.tBodies[0].rows).forEach(r => r.style.display = '');
        }
        return;
      }

      const clickedDate = cell.textContent.trim();
      if (!clickedDate) return;

      // Toggle the filter when clicking the same date again
      if (activeFilterDate === clickedDate) {
        activeFilterDate = null;
        Array.from(bankTable.tBodies[0].rows).forEach(r => r.style.display = '');
        return;
      }

      activeFilterDate = clickedDate;
      Array.from(bankTable.tBodies[0].rows).forEach(r => {
        const dateCell = r.children[dateColumnIndex];
        if (dateCell) {
          r.style.display = dateCell.textContent.trim() === clickedDate ? '' : 'none';
        }
      });
    });
  }

  // Action dropdown handling for each table row
  document.addEventListener('click', (event) => {
    const toggle = event.target.closest('.action-toggle');
    const dropdown = event.target.closest('.action-dropdown');

    // Close any open menus if click is outside
    document.querySelectorAll('.action-dropdown .action-menu').forEach(menu => {
      if (!menu.contains(event.target) && !menu.parentElement.querySelector('.action-toggle')?.contains(event.target)) {
        menu.style.display = 'none';
      }
    });

    if (!toggle) return;

    event.preventDefault();
    const menu = toggle.parentElement.querySelector('.action-menu');
    if (!menu) return;

    const isVisible = menu.style.display === 'block';
    menu.style.display = isVisible ? 'none' : 'block';
  });

  // Handle action item clicks (view/edit/delete)
  document.addEventListener('click', (event) => {
    const actionBtn = event.target.closest('.action-item');
    if (!actionBtn) return;

    const action = actionBtn.dataset.action;
    const bankId = actionBtn.dataset.bankId;

    // Close open menus
    document.querySelectorAll('.action-dropdown .action-menu').forEach(menu => menu.style.display = 'none');

    if (action === 'view') {
      const item = document.querySelector(`li[data-bank="${bankId}"]`);
      if (item) selectBankItem(item);
      openBankModal('view', bankId);
      const viewModalEl = document.getElementById('addBankModal');
      if (viewModalEl && window.bootstrap) {
        const modal = bootstrap.Modal.getOrCreateInstance(viewModalEl);
        modal.show();
      }
      return;
    }

    if (action === 'edit') {
      const item = document.querySelector(`li[data-bank="${bankId}"]`);
      if (item) selectBankItem(item);
      openBankModal('edit', bankId);
      const editModalEl = document.getElementById('addBankModal');
      if (editModalEl && window.bootstrap) {
        const modal = bootstrap.Modal.getOrCreateInstance(editModalEl);
        modal.show();
      }
      return;
    }

    if (action === 'delete') {
      if (!confirm('Are you sure you want to delete this bank account?')) {
        return;
      }

      fetch(`/dashboard/bank-accounts/${bankId}`, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
      })
        .then(async (res) => {
          const data = await res.json().catch(() => ({}));
          if (!res.ok) {
            const message = data?.message || 'Could not delete bank account.';
            throw new Error(message);
          }
          return data;
        })
        .then(() => {
          // Remove from sidebar list
          const listItem = document.querySelector(`li[data-bank="${bankId}"]`);
          if (listItem) listItem.remove();

          // Remove from table
          const tableRow = document.querySelector(`tr[data-bank-id="${bankId}"]`);
          if (tableRow) tableRow.remove();

          showToast('Bank account deleted successfully.');

          // If the deleted account was selected, pick the first remaining
          const remaining = document.querySelector('li[data-bank]');
          if (remaining) selectBankItem(remaining);
        })
        .catch((error) => {
          showToast(error?.message || 'Could not delete bank account.', 'danger');
        });

      return;
    }
  });

  // Handle form submission (create/update) via AJAX
  if (bankForm) {
    bankForm.addEventListener('submit', (event) => {
      event.preventDefault();

      const url = bankForm.action;
      const method = bankFormMethod.value || 'POST';
      const formData = new FormData(bankForm);

      fetch(url, {
        method: method,
        headers: {
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: formData,
      })
        .then(async (res) => {
          const data = await res.json().catch(() => ({}));
          if (!res.ok) {
            const message = data?.message || 'Could not save bank account.';
            throw new Error(message);
          }
          return data;
        })
        .then((data) => {
          showToast(data.message || 'Saved successfully.');

          // Reload the page to ensure table + sidebar stay in sync.
          // (This keeps behavior simple and avoids edge cases.)
          setTimeout(() => {
            window.location.reload();
          }, 500);
        })
        .catch((error) => {
          showToast(error?.message || 'Could not save bank account.', 'danger');
        });
    });
  }

  // Export table data to CSV (Excel-friendly)
  function exportTableToCsv(filename) {
    if (!bankTable) return;

    const rows = Array.from(bankTable.tBodies[0].rows).filter(r => r.style.display !== 'none');
    if (rows.length === 0) {
      showToast('No rows available to export.', 'warning');
      return;
    }

    const headerCells = Array.from(bankTable.tHead.rows[0].cells).map(th => th.textContent.trim());
    const keepIndexes = headerCells
      .map((header, idx) => (header.toLowerCase() === 'actions' ? -1 : idx))
      .filter(idx => idx !== -1);

    const csv = [keepIndexes.map(idx => headerCells[idx]).join(',')];

    rows.forEach(row => {
      const cols = keepIndexes.map(idx => {
        const td = row.cells[idx];
        let text = td ? td.textContent.trim() : '';
        // Remove any extra whitespace/newlines
        text = text.replace(/\s+/g, ' ');
        // Wrap values that contain comma/quote/newline
        if (/[",\n]/.test(text)) {
          text = `"${text.replace(/"/g, '""')}"`;
        }
        return text;
      });
      csv.push(cols.join(','));
    });

    const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', filename);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
  }

  const exportExcelBtn = document.getElementById('exportExcelBtn');
  if (exportExcelBtn) {
    exportExcelBtn.addEventListener('click', () => {
      const now = new Date();
      const filename = `bank-accounts-${now.getFullYear()}-${String(now.getMonth()+1).padStart(2,'0')}-${String(now.getDate()).padStart(2,'0')}.csv`;
      exportTableToCsv(filename);
    });
  }

  const printTableBtn = document.getElementById('printTableBtn');
  if (printTableBtn) {
    printTableBtn.addEventListener('click', () => {
      window.print();
    });
  }

  // Auto-hide flash messages after 4 seconds
  const flash = document.getElementById('bankFlash');
  if (flash) {
    setTimeout(() => {
      flash.style.transition = 'opacity 0.3s';
      flash.style.opacity = '0';
      setTimeout(() => flash.remove(), 300);
    }, 4000);
  }

  function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} mt-3`;
    toast.textContent = message;
    document.querySelector('.uper-panel').insertAdjacentElement('afterend', toast);

    setTimeout(() => {
      toast.style.transition = 'opacity 0.3s';
      toast.style.opacity = '0';
      setTimeout(() => toast.remove(), 300);
    }, 4000);
  }
});
