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
  const bulkMenuButton = document.getElementById('bankBulkMenuBtn');
  const bulkMenu = document.getElementById('bankBulkMenu');
  const bulkOverlay = document.getElementById('bankBulkOverlay');
  const bulkModalTitle = document.getElementById('bankBulkModalTitle');
  const bulkModalInfo = document.getElementById('bankBulkModalInfo');
  const bulkSearch = document.getElementById('bankBulkSearch');
  const bulkTbody = document.getElementById('bankBulkTbody');
  const bulkCheckAll = document.getElementById('bankBulkCheckAll');
  const bulkApplyBtn = document.getElementById('bankBulkApplyBtn');
  const bulkCancelBtn = document.getElementById('bankBulkCancelBtn');
  const bulkFooterNote = document.getElementById('bankBulkFooterNote');
  const bulkPasswordBox = document.getElementById('bankBulkPasswordBox');
  const bulkPasswordInput = document.getElementById('bankBulkPasswordInput');
  const bulkPasswordError = document.getElementById('bankBulkPasswordError');

  // Table element used for filtering & actions
  const bankTable = document.getElementById('bankTable');

  // Keep track of whether a date filter is currently active (clicking the date column)
  let activeFilterDate = null;
  let bulkModalType = null;

  function isBankInactive(bankId) {
    const item = list?.querySelector(`li[data-bank="${bankId}"]`);
    return item ? item.dataset.isActive === '0' : false;
  }

  function setBankInactive(bankId, inactive) {
    const item = list?.querySelector(`li[data-bank="${bankId}"]`);
    if (!item) return;
    item.dataset.isActive = inactive ? '0' : '1';
  }

  function ensureStatusPill(item) {
    if (!item) return null;
    let pill = item.querySelector('.bank-status-pill');
    if (!pill) {
      pill = document.createElement('span');
      pill.className = 'bank-status-pill';
      item.querySelector('.entity-name')?.insertAdjacentElement('afterend', pill);
    }
    return pill;
  }

  function refreshBankStatusUI() {
    if (!list) return;

    list.querySelectorAll('li[data-bank]').forEach((item) => {
      const inactive = isBankInactive(item.dataset.bank);
      item.classList.toggle('bank-inactive', inactive);
      const pill = ensureStatusPill(item);
      if (!pill) return;
      pill.textContent = inactive ? 'Inactive' : 'Active';
      pill.classList.toggle('inactive', inactive);
      pill.classList.toggle('active', !inactive);
    });

    applySearchFilter();

    const activeVisibleItem = list.querySelector('li.active[data-bank]:not([style*="display: none"])');
    if (!activeVisibleItem) {
      const firstVisibleItem = Array.from(list.querySelectorAll('li[data-bank]')).find((item) => item.style.display !== 'none');
      if (firstVisibleItem) {
        selectBankItem(firstVisibleItem);
      }
    }
  }

  function getBulkModalRows() {
    if (!list) return [];

    return Array.from(list.querySelectorAll('li[data-bank]'))
      .map((item) => ({
        id: item.dataset.bank || '',
        name: item.querySelector('.entity-name')?.childNodes[0]?.textContent?.trim() || item.querySelector('.entity-name')?.textContent?.trim() || 'Bank Account',
        accountNumber: item.dataset.accountNumber || '-',
        inactive: isBankInactive(item.dataset.bank),
      }))
      .filter((row) => {
        if (bulkModalType === 'bulk-inactive') return !row.inactive;
        if (bulkModalType === 'bulk-active') return row.inactive;
        return true;
      });
  }

  function renderBulkRows() {
    if (!bulkTbody) return;

    const query = (bulkSearch?.value || '').trim().toLowerCase();
    const rows = getBulkModalRows().filter((row) => {
      return [row.name, row.accountNumber].some((value) => String(value).toLowerCase().includes(query));
    });

    if (!rows.length) {
      bulkTbody.innerHTML = '<tr><td colspan="4" class="bulk-empty">No bank accounts to show</td></tr>';
      if (bulkCheckAll) bulkCheckAll.checked = false;
      return;
    }

    bulkTbody.innerHTML = rows.map((row) => `
      <tr>
        <td>
          <input type="checkbox" class="bank-bulk-check" value="${row.id}" style="width:15px;height:15px;accent-color:#2563eb;">
        </td>
        <td>${escapeHtml(row.name)}</td>
        <td>${escapeHtml(row.accountNumber)}</td>
        <td><span class="bank-status-pill ${row.inactive ? 'inactive' : 'active'}">${row.inactive ? 'Inactive' : 'Active'}</span></td>
      </tr>
    `).join('');
  }

  function openBulkModal(type) {
    bulkModalType = type;
    if (bulkModalTitle) {
      bulkModalTitle.textContent = type === 'bulk-active' ? 'Bulk Active' : 'Bulk Inactive';
    }
    if (bulkModalInfo) {
      bulkModalInfo.textContent = type === 'bulk-active'
        ? 'Showing inactive bank accounts only.'
        : 'Showing active bank accounts only.';
    }
    if (bulkFooterNote) {
      bulkFooterNote.textContent = type === 'bulk-active'
        ? 'Selected bank accounts will become active.'
        : 'Selected bank accounts will become inactive.';
    }
    if (bulkApplyBtn) {
      bulkApplyBtn.textContent = type === 'bulk-active' ? 'Mark Active' : 'Mark Inactive';
    }
    if (bulkPasswordBox) {
      bulkPasswordBox.classList.toggle('open', type === 'bulk-active');
    }
    if (bulkPasswordInput) {
      bulkPasswordInput.value = '';
    }
    if (bulkPasswordError) {
      bulkPasswordError.classList.remove('show');
    }
    if (bulkSearch) bulkSearch.value = '';
    if (bulkCheckAll) bulkCheckAll.checked = false;
    renderBulkRows();
    bulkOverlay?.classList.add('open');
  }

  function closeBulkModal() {
    bulkOverlay?.classList.remove('open');
    bulkModalType = null;
    if (bulkPasswordInput) {
      bulkPasswordInput.value = '';
    }
    if (bulkPasswordError) {
      bulkPasswordError.classList.remove('show');
    }
  }

  function closeBulkMenu() {
    bulkMenu?.classList.remove('open');
  }

  function escapeHtml(value) {
    return String(value ?? '')
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#39;');
  }

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

  function getSelectedBankId() {
    return document.querySelector('li.active[data-bank]')?.dataset.bank || '';
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

  refreshBankStatusUI();

  if (bulkMenuButton) {
    bulkMenuButton.addEventListener('click', (event) => {
      event.stopPropagation();
      bulkMenu?.classList.toggle('open');
    });
  }

  document.addEventListener('click', (event) => {
    if (!event.target.closest('.bulk-menu-wrap')) {
      closeBulkMenu();
    }
  });

  document.querySelectorAll('[data-bulk-action]').forEach((button) => {
    button.addEventListener('click', () => {
      closeBulkMenu();
      openBulkModal(button.dataset.bulkAction || 'bulk-inactive');
    });
  });

  if (bulkCancelBtn) {
    bulkCancelBtn.addEventListener('click', closeBulkModal);
  }

  if (bulkOverlay) {
    bulkOverlay.addEventListener('click', (event) => {
      if (event.target === bulkOverlay) {
        closeBulkModal();
      }
    });
  }

  if (bulkSearch) {
    bulkSearch.addEventListener('input', renderBulkRows);
  }

  if (bulkCheckAll) {
    bulkCheckAll.addEventListener('change', () => {
      document.querySelectorAll('.bank-bulk-check').forEach((checkbox) => {
        checkbox.checked = bulkCheckAll.checked;
      });
    });
  }

  if (bulkApplyBtn) {
    bulkApplyBtn.addEventListener('click', () => {
      const selectedIds = Array.from(document.querySelectorAll('.bank-bulk-check:checked'))
        .map((checkbox) => checkbox.value)
        .filter(Boolean);

      if (!selectedIds.length) {
        showToast('Please select at least one bank account.', 'warning');
        return;
      }

      const makeInactive = bulkModalType === 'bulk-inactive';

      if (!makeInactive) {
        const enteredPassword = bulkPasswordInput?.value || '';
        if (!enteredPassword) {
          bulkPasswordError?.classList.add('show');
          bulkPasswordInput?.focus();
          showToast('Please enter bank account password.', 'danger');
          return;
        }
      }

      fetch('/dashboard/bank-accounts/bulk-status', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          bank_ids: selectedIds,
          is_active: !makeInactive,
          password: bulkPasswordInput?.value || '',
        }),
      })
        .then(async (res) => {
          const data = await res.json().catch(() => ({}));
          if (!res.ok) {
            throw new Error(data?.message || 'Failed to update bank status.');
          }
          return data;
        })
        .then((data) => {
          selectedIds.forEach((bankId) => setBankInactive(bankId, makeInactive));
          refreshBankStatusUI();
          renderBulkRows();
          showToast(data.message || (makeInactive ? 'Selected bank accounts marked inactive.' : 'Selected bank accounts marked active.'));
          closeBulkModal();
        })
        .catch((error) => {
          showToast(error?.message || 'Failed to update bank status.', 'danger');
        });
    });
  }

  if (addBankButton) {
    addBankButton.addEventListener('click', () => {
      openBankModal('add');
    });
  }

  function getBankOptions(selectedBankId = '') {
    if (!list) return '';
    return Array.from(list.querySelectorAll('li[data-bank]'))
      .map((item) => {
        const bankId = item.dataset.bank || '';
        const bankName = item.querySelector('.entity-name')?.textContent?.trim() || 'Bank';
        const selected = String(bankId) === String(selectedBankId) ? 'selected' : '';
        return `<option value="${bankId}" ${selected}>${bankName}</option>`;
      })
      .join('');
  }

  function injectTransferActions() {
    if (!addBankButton || document.getElementById('bankTransferActions')) return;

    const actionButtons = addBankButton.closest('.action-buttons');
    if (!actionButtons) return;

    const wrapper = document.createElement('div');
    wrapper.id = 'bankTransferActions';
    wrapper.className = 'dropdown';
    wrapper.innerHTML = `
      <button class="btn btn-outline-danger rounded-pill px-3 py-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
        Deposit / Withdraw <i class="fa-solid fa-chevron-down ms-2"></i>
      </button>
      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
        <li><button class="dropdown-item bank-transfer-option" type="button" data-mode="bank_to_cash">Bank to Cash Transfer</button></li>
        <li><button class="dropdown-item bank-transfer-option" type="button" data-mode="cash_to_bank">Cash to Bank Transfer</button></li>
        <li><button class="dropdown-item bank-transfer-option" type="button" data-mode="bank_to_bank">Bank to Bank Transfer</button></li>
        <li><button class="dropdown-item bank-transfer-option" type="button" data-mode="adjust_balance">Adjust Bank Balance</button></li>
      </ul>
    `;

    actionButtons.insertBefore(wrapper, actionButtons.querySelector('.btn-settings'));
  }

  function injectTransferModal() {
    if (document.getElementById('bankTransferModal')) return;

    const modal = document.createElement('div');
    modal.className = 'modal fade';
    modal.id = 'bankTransferModal';
    modal.tabIndex = -1;
    modal.setAttribute('aria-hidden', 'true');
    modal.innerHTML = `
      <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
          <form id="bankTransferForm">
            <div class="modal-header">
              <h5 class="modal-title" id="bankTransferModalTitle">Bank To Cash Transfer</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <input type="hidden" id="bankTransferMode" value="bank_to_cash">
              <div class="row g-3" id="bankTransferDualFields">
                <div class="col-md-6">
                  <label class="form-label" id="transferFromLabel">From</label>
                  <select class="form-select" id="transferFromBank"></select>
                  <input type="text" class="form-control d-none" id="transferFromCash" value="Cash" readonly>
                </div>
                <div class="col-md-6">
                  <label class="form-label" id="transferToLabel">To</label>
                  <select class="form-select" id="transferToBank"></select>
                  <input type="text" class="form-control d-none" id="transferToCash" value="Cash" readonly>
                </div>
              </div>
              <div class="row g-3 d-none" id="bankAdjustFields">
                <div class="col-md-6">
                  <label class="form-label">Account Name</label>
                  <select class="form-select" id="adjustAccountName"></select>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Type</label>
                  <select class="form-select" id="adjustType">
                    <option value="increase">Increase balance</option>
                    <option value="decrease">Decrease balance</option>
                  </select>
                </div>
              </div>
              <div class="row g-3 mt-1">
                <div class="col-md-6">
                  <label class="form-label">Amount</label>
                  <input type="number" step="0.01" class="form-control" id="transferAmount" value="0">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Adjustment Date</label>
                  <input type="date" class="form-control" id="transferDate">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Description</label>
                  <textarea class="form-control" id="transferDescription" rows="3" placeholder="Add description"></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Image</label>
                  <input type="file" class="form-control" id="transferImage" accept="image/*">
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Cancel</button>
              <button type="submit" class="btn btn-danger rounded-pill px-4">Save</button>
            </div>
          </form>
        </div>
      </div>
    `;

    document.body.appendChild(modal);
  }

  function setTransferModalMode(mode) {
    const modalTitleEl = document.getElementById('bankTransferModalTitle');
    const dualFields = document.getElementById('bankTransferDualFields');
    const adjustFields = document.getElementById('bankAdjustFields');
    const fromBank = document.getElementById('transferFromBank');
    const toBank = document.getElementById('transferToBank');
    const fromCash = document.getElementById('transferFromCash');
    const toCash = document.getElementById('transferToCash');
    const adjustAccountName = document.getElementById('adjustAccountName');
    const transferDate = document.getElementById('transferDate');
    const activeBankId = document.querySelector('li.active[data-bank]')?.dataset.bank || '';

    if (!modalTitleEl || !dualFields || !adjustFields || !fromBank || !toBank || !fromCash || !toCash || !adjustAccountName) {
      return;
    }

    document.getElementById('bankTransferMode').value = mode;
    transferDate.value = new Date().toISOString().slice(0, 10);
    fromBank.innerHTML = getBankOptions(activeBankId);
    toBank.innerHTML = getBankOptions(activeBankId);
    adjustAccountName.innerHTML = getBankOptions(activeBankId);

    dualFields.classList.remove('d-none');
    adjustFields.classList.add('d-none');
    fromBank.classList.remove('d-none');
    toBank.classList.remove('d-none');
    fromCash.classList.add('d-none');
    toCash.classList.add('d-none');

    if (mode === 'bank_to_cash') {
      modalTitleEl.textContent = 'Bank To Cash Transfer';
      toCash.classList.remove('d-none');
      toCash.readOnly = true;
      toBank.classList.add('d-none');
    } else if (mode === 'cash_to_bank') {
      modalTitleEl.textContent = 'Cash To Bank Transfer';
      fromCash.classList.remove('d-none');
      fromCash.readOnly = true;
      fromBank.classList.add('d-none');
    } else if (mode === 'bank_to_bank') {
      modalTitleEl.textContent = 'Bank To Bank Transfer';
    } else {
      modalTitleEl.textContent = 'Bank Adjustment Entry';
      dualFields.classList.add('d-none');
      adjustFields.classList.remove('d-none');
    }
  }

  injectTransferActions();
  injectTransferModal();

  document.addEventListener('click', (event) => {
    const transferOption = event.target.closest('.bank-transfer-option');
    if (!transferOption) return;

    const mode = transferOption.dataset.mode || 'bank_to_cash';
    setTransferModalMode(mode);

    const transferModalEl = document.getElementById('bankTransferModal');
    if (transferModalEl && window.bootstrap) {
      const modal = bootstrap.Modal.getOrCreateInstance(transferModalEl);
      modal.show();
    }
  });

  const bankTransferForm = document.getElementById('bankTransferForm');
  if (bankTransferForm) {
    bankTransferForm.addEventListener('submit', (event) => {
      event.preventDefault();

      const mode = document.getElementById('bankTransferMode')?.value || 'bank_to_bank';
      const amount = document.getElementById('transferAmount')?.value || 0;
      const fromBankId = document.getElementById('transferFromBank')?.value || '';
      const toBankId = document.getElementById('transferToBank')?.value || '';

      if (mode !== 'bank_to_bank') {
        showToast('Cash transfer flows ko next step me wire karenge. Bank to bank active hai.', 'warning');
        return;
      }

      fetch('/dashboard/bank-accounts/transfer', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken,
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          mode,
          from_bank_id: fromBankId,
          to_bank_id: toBankId,
          amount,
        }),
      })
        .then(async (res) => {
          const data = await res.json().catch(() => ({}));
          if (!res.ok) {
            throw new Error(data?.message || 'Transfer failed.');
          }
          return data;
        })
        .then((data) => {
          showToast(data.message || 'Transfer completed successfully.');

          const transferModalEl = document.getElementById('bankTransferModal');
          if (transferModalEl && window.bootstrap) {
            const modal = bootstrap.Modal.getOrCreateInstance(transferModalEl);
            modal.hide();
          }

          setTimeout(() => {
            window.location.reload();
          }, 400);
        })
        .catch((error) => {
          showToast(error?.message || 'Transfer failed.', 'danger');
        });
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
    const q = sidebarSearch.value.trim().toLowerCase();
    const isDateSearch = /^\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4}$/.test(q) || /^\d{4}-\d{2}-\d{2}$/.test(q);
    const normalizedDate = normalizeDate(q);

    list.querySelectorAll('li').forEach(li => {
      if (!li.dataset.bank) return;

      if (isBankInactive(li.dataset.bank)) {
        li.style.display = 'none';
        return;
      }

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
    const selectedBankId = getSelectedBankId();

    Array.from(bankTable.tBodies[0].rows).forEach(row => {
      const text = Array.from(row.cells)
        .slice(0, -1) // exclude action column
        .map(cell => cell.textContent.trim().toLowerCase())
        .join(' ');
      const matchesSearch = q === '' || text.includes(q);
      const matchesBank = !selectedBankId || row.dataset.bankId === selectedBankId;
      row.style.display = matchesSearch && matchesBank ? '' : 'none';
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
      const dateColumnIndex = 5;
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

    if (action === 'history') {
      const item = document.querySelector(`li[data-bank="${bankId}"]`);
      if (item) {
        selectBankItem(item);
      }
      tableSearch?.focus();
      bankTable?.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
