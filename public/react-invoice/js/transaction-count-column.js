(function (window, document) {
    'use strict';

    if (window.__transactionCountColumnLoaded) {
        return;
    }

    window.__transactionCountColumnLoaded = true;

    function isEnabled() {
        return Boolean(window.transactionSettings && window.transactionSettings.countEnabled);
    }

    function getLabel() {
        return (window.transactionSettings && window.transactionSettings.countLabel) || 'Count';
    }

    function ensureHeader(table) {
        const headerRow = table.querySelector('thead tr');
        if (!headerRow) {
            return;
        }

        const existing = headerRow.querySelector('.col-count');
        if (existing) {
            existing.textContent = getLabel().toUpperCase();
            return;
        }

        const qtyHeader = Array.from(headerRow.children).find(function (cell) {
            return cell.textContent.trim().toUpperCase() === 'QTY';
        });

        if (!qtyHeader) {
            return;
        }

        const countHeader = document.createElement('th');
        countHeader.className = 'col-count';
        countHeader.textContent = getLabel().toUpperCase();
        qtyHeader.insertAdjacentElement('afterend', countHeader);
    }

    function ensureRowCell(row) {
        if (row.querySelector('.col-count')) {
            return;
        }

        const qtyInputCell = row.querySelector('.item-qty')?.closest('td');
        if (!qtyInputCell) {
            return;
        }

        const cell = document.createElement('td');
        cell.className = 'col-count';
        cell.innerHTML = '<input type="number" class="item-inline-input count-input" value="0" min="0" step="1" placeholder="Count">';
        qtyInputCell.insertAdjacentElement('afterend', cell);
    }

    function syncTable(table) {
        if (!isEnabled()) {
            table.querySelectorAll('.col-count').forEach(function (cell) {
                cell.remove();
            });
            return;
        }

        ensureHeader(table);
        table.querySelectorAll('tbody .item-row').forEach(ensureRowCell);
    }

    function watchTable(table) {
        if (table.dataset.countObserverAttached === '1') {
            return;
        }

        const tbody = table.querySelector('tbody.item-rows');
        if (!tbody) {
            return;
        }

        const observer = new MutationObserver(function () {
            syncTable(table);
        });

        observer.observe(tbody, { childList: true });
        table.dataset.countObserverAttached = '1';
    }

    function init() {
        document.querySelectorAll('.item-table').forEach(function (table) {
            syncTable(table);
            watchTable(table);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})(window, document);
