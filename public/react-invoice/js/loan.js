/**
 * VYAPAR — Loan Accounts Page Logic
 *
 * This script mirrors the bank account page's behavior (bank.js) but operates
 * on loan accounts. It handles selecting loans from the sidebar, updating the
 * detail panel, filtering and exporting the table, and powering the Add/Edit
 * modal.
 */

(function () {
  const apiBase = '/dashboard/loan-accounts';

  function qs(selector) {
    return document.querySelector(selector);
  }

  function qsa(selector) {
    return Array.from(document.querySelectorAll(selector));
  }

  function formatCurrency(value) {
    const num = Number(value ?? 0);
    if (Number.isNaN(num)) return '-';
    return `₹ ${num.toFixed(2)}`;
  }

  function formatPercent(value) {
    if (value === null || value === undefined || value === '') return '-';
    const num = Number(value);
    if (Number.isNaN(num)) return '-';
    return `${num.toFixed(2)}%`;
  }

  function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || window.App?.csrfToken || '';
  }

  function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `alert alert-${type} mt-3`;
    toast.textContent = message;
    const panel = qs('.uper-panel');
    if (panel) {
      panel.insertAdjacentElement('afterend', toast);
      setTimeout(() => {
        toast.style.transition = 'opacity 0.3s';
        toast.style.opacity = '0';
        setTimeout(() => toast.remove(), 300);
      }, 4000);
    }
  }

  function setActiveLoanListItem(loanId) {
    qsa('#loanList li').forEach(li => {
      const id = li.dataset.loan;
      li.classList.toggle('active', id === String(loanId));
    });
  }

  function setActiveTableRow(loanId) {
    qsa('#loanTable tbody tr').forEach(row => {
      const match = row.dataset.loanId === String(loanId);
      row.style.display = match ? '' : 'none';
      row.classList.toggle('active-row', match);
    });
  }

  function updateDetailPanel(data) {
    const nameEl = qs('#loanDetailName');
    const accountEl = qs('#loanDetailAccountNumber');
    const lenderBankEl = qs('#loanDetailLenderBank');
    const balanceEl = qs('#loanDetailCurrentBalance');
    const interestEl = qs('#loanDetailInterestRate');

    if (nameEl) nameEl.textContent = data.display_name || 'Select a loan';
    if (accountEl) accountEl.textContent = data.account_number || '-';
    if (lenderBankEl) lenderBankEl.textContent = data.lender_bank?.display_name || '-';
    if (balanceEl) balanceEl.textContent = formatCurrency(data.current_balance);
    if (interestEl) interestEl.textContent = formatPercent(data.interest_rate);
  }

  function loadLoanDetails(loanId) {
    return fetch(`${apiBase}/${loanId}`, { headers: { Accept: 'application/json' } })
      .then(res => {
        if (!res.ok) throw new Error('Unable to load loan details.');
        return res.json();
      });
  }

  function openLoanModal(mode, loanId = null) {
    const loanForm = qs('#loanForm');
    const loanFormMethod = qs('#loanFormMethod');
    const loanIdField = qs('#loanIdField');
    const loanModalLabel = qs('#loanModalLabel');
    if (!loanForm || !loanFormMethod || !loanIdField || !loanModalLabel) return;

    loanForm.reset();
    loanFormMethod.value = 'POST';
    loanIdField.value = '';
    loanForm.action = apiBase;
    loanModalLabel.textContent = 'Add Loan Account';

    if (mode === 'edit' && loanId) {
      loanFormMethod.value = 'PUT';
      loanIdField.value = loanId;
      loanForm.action = `${apiBase}/${loanId}`;
      loanModalLabel.textContent = 'Edit Loan Account';

      loadLoanDetails(loanId)
        .then(data => {
          loanForm.querySelector('[name="display_name"]').value = data.display_name || '';
          loanForm.querySelector('[name="lender_bank_id"]').value = data.lender_bank_id || '';
          loanForm.querySelector('[name="account_number"]').value = data.account_number || '';
          loanForm.querySelector('[name="description"]').value = data.description || '';
          loanForm.querySelector('[name="current_balance"]').value = data.current_balance ?? '';
          loanForm.querySelector('[name="balance_as_of"]').value = data.balance_as_of ?? '';
          loanForm.querySelector('[name="received_in"]').value = data.received_in || '';
          loanForm.querySelector('[name="processing_fee_paid_from_id"]').value = data.processing_fee_paid_from_id || '';
          loanForm.querySelector('[name="processing_fee"]').value = data.processing_fee ?? '';
          loanForm.querySelector('[name="interest_rate"]').value = data.interest_rate ?? '';
          loanForm.querySelector('[name="term_months"]').value = data.term_months ?? '';
        })
        .catch(err => showToast(err.message || 'Failed to load loan.', 'danger'));
    }

    const modalEl = document.getElementById('loanModal');
    if (modalEl && window.bootstrap) {
      const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
      modal.show();
    }
  }

  function deleteLoan(loanId) {
    if (!confirm('Are you sure you want to delete this loan account?')) return;

    fetch(`${apiBase}/${loanId}`, {
      method: 'DELETE',
      headers: {
        'X-CSRF-TOKEN': getCsrfToken(),
        'Accept': 'application/json',
      },
    })
      .then(async res => {
        const data = await res.json().catch(() => ({}));
        if (!res.ok) {
          throw new Error(data.message || 'Could not delete loan account.');
        }
        return data;
      })
      .then(() => {
        const listItem = qs(`#loanList li[data-loan="${loanId}"]`);
        const tableRow = qs(`#loanTable tbody tr[data-loan-id="${loanId}"]`);
        if (listItem) listItem.remove();
        if (tableRow) tableRow.remove();
        showToast('Loan account deleted successfully.');

        // Select next available loan
        const next = qs('#loanList li[data-loan]');
        if (next) {
          selectLoan(next.dataset.loan);
        } else {
          updateDetailPanel({});
        }
      })
      .catch(err => showToast(err.message || 'Could not delete loan account.', 'danger'));
  }

  function exportTableToCsv(tableEl, filename) {
    if (!tableEl) return;

    const rows = Array.from(tableEl.tBodies[0].rows).filter(r => r.style.display !== 'none');
    if (!rows.length) {
      showToast('No rows available to export.', 'warning');
      return;
    }

    const headerCells = Array.from(tableEl.tHead.rows[0].cells).map(th => th.textContent.trim());
    const keepIndexes = headerCells
      .map((header, idx) => (header.toLowerCase() === 'actions' ? -1 : idx))
      .filter(idx => idx !== -1);

    const csv = [keepIndexes.map(idx => headerCells[idx]).join(',')];

    rows.forEach(row => {
      const cols = keepIndexes.map(idx => {
        const td = row.cells[idx];
        let text = td ? td.textContent.trim() : '';
        text = text.replace(/\s+/g, ' ');
        if (/[,"\n]/.test(text)) {
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

  function applyTableSearch(tableEl, query) {
    if (!tableEl) return;
    const q = query.trim().toLowerCase();
    Array.from(tableEl.tBodies[0].rows).forEach(row => {
      const text = Array.from(row.cells)
        .slice(0, -1)
        .map(cell => cell.textContent.trim().toLowerCase())
        .join(' ');
      row.style.display = q === '' || text.includes(q) ? '' : 'none';
    });
  }

  function applySidebarSearch(query) {
    const q = query.trim().toLowerCase();
    qsa('#loanList li').forEach(li => {
      const name = li.querySelector('.entity-name')?.textContent?.toLowerCase() || '';
      const matching = name.includes(q);
      li.style.display = q === '' || matching ? '' : 'none';
    });
  }

  function selectLoan(loanId) {
    const listItem = qs(`#loanList li[data-loan="${loanId}"]`);
    if (!listItem) return;

    setActiveLoanListItem(loanId);
    setActiveTableRow(loanId);

    loadLoanDetails(loanId)
      .then(data => updateDetailPanel(data))
      .catch(err => showToast(err.message || 'Could not load loan.', 'danger'));
  }

  document.addEventListener('DOMContentLoaded', () => {
    const loanTable = qs('#loanTable');
    const tableSearch = qs('#tableSearchInput');
    const focusSearchBtn = qs('#focusSearchBtn');
    const exportExcelBtn = qs('#exportExcelBtn');
    const printTableBtn = qs('#printTableBtn');
    const addLoanBtn = qs('#addLoanBtn');
    const loanDetailEditBtn = qs('#loanDetailEditBtn');

    // Initialize selection
    const firstLoan = qs('#loanList li[data-loan]');
    if (firstLoan) {
      selectLoan(firstLoan.dataset.loan);
    }

    // Sidebar search
    const loanSearch = qs('#loanSearchInput');
    if (loanSearch) {
      loanSearch.addEventListener('input', () => applySidebarSearch(loanSearch.value));
    }

    // Table search
    if (tableSearch && loanTable) {
      tableSearch.addEventListener('input', () => applyTableSearch(loanTable, tableSearch.value));
    }

    if (focusSearchBtn && tableSearch) {
      focusSearchBtn.addEventListener('click', () => tableSearch.focus());
    }

    if (exportExcelBtn && loanTable) {
      exportExcelBtn.addEventListener('click', () => {
        const now = new Date();
        const filename = `loan-accounts-${now.getFullYear()}-${String(now.getMonth()+1).padStart(2, '0')}-${String(now.getDate()).padStart(2, '0')}.csv`;
        exportTableToCsv(loanTable, filename);
      });
    }

    if (printTableBtn) {
      printTableBtn.addEventListener('click', () => window.print());
    }

    // Sidebar list click
    qsa('#loanList li[data-loan]').forEach(li => {
      li.addEventListener('click', () => {
        selectLoan(li.dataset.loan);
      });
    });

    // Detail edit button
    if (loanDetailEditBtn) {
      loanDetailEditBtn.addEventListener('click', () => {
        const active = qs('#loanList li.active');
        if (active) {
          openLoanModal('edit', active.dataset.loan);
        }
      });
    }


    // Action dropdowns (edit/delete)
    document.addEventListener('click', (event) => {
      const toggle = event.target.closest('.action-toggle');
      if (toggle) {
        const menu = toggle.parentElement.querySelector('.action-menu');
        if (!menu) return;
        const isVisible = menu.style.display === 'block';
        document.querySelectorAll('.action-menu').forEach(m => (m.style.display = 'none'));
        menu.style.display = isVisible ? 'none' : 'block';
        return;
      }

      const actionBtn = event.target.closest('.action-item');
      if (!actionBtn) return;

      const action = actionBtn.dataset.action;
      const loanId = actionBtn.dataset.loanId;

      document.querySelectorAll('.action-menu').forEach(m => (m.style.display = 'none'));

      if (action === 'edit' && loanId) {
        setActiveLoanListItem(loanId);
        setActiveTableRow(loanId);
        openLoanModal('edit', loanId);
      }

      if (action === 'delete' && loanId) {
        deleteLoan(loanId);
      }
    });

    // Add Loan button
    if (addLoanBtn) {
      addLoanBtn.addEventListener('click', () => openLoanModal('add'));
    }
  });
})();
