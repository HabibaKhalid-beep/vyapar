<div class="modal fade" id="itemColumnModal" tabindex="-1" aria-labelledby="itemColumnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h6 class="modal-title" id="itemColumnModalLabel">Add fields to items</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-check mb-2">
                    <input class="form-check-input check-category" type="checkbox" id="sharedColCategoryCheck">
                    <label class="form-check-label" for="sharedColCategoryCheck">Item Category</label>
                </div>
                <div class="mb-3">
                    <select class="form-select form-select-sm item-filter-category" disabled>
                        <option value="">Select Category</option>
                    </select>
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input check-item-code" type="checkbox" id="sharedColItemCodeCheck">
                    <label class="form-check-label" for="sharedColItemCodeCheck">Item Code</label>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control form-control-sm item-filter-code" placeholder="Filter by code" disabled>
                </div>

                <div class="form-check mb-2">
                    <input class="form-check-input check-description" type="checkbox" id="sharedColDescriptionCheck">
                    <label class="form-check-label" for="sharedColDescriptionCheck">Description</label>
                </div>
                <div class="mb-3">
                    <input type="text" class="form-control form-control-sm item-filter-description" placeholder="Filter by description" disabled>
                </div>

                <div class="form-check">
                    <input class="form-check-input check-discount" type="checkbox" id="sharedColDiscountCheck">
                    <label class="form-check-label" for="sharedColDiscountCheck">Discount</label>
                </div>
                <div class="mb-2">
                    <select class="form-select form-select-sm item-filter-discount" disabled>
                        <option value="">Any Discount</option>
                        <option value="has">Has Discount</option>
                        <option value="none">No Discount</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary w-100 item-filter-apply" data-bs-dismiss="modal">Apply</button>
            </div>
        </div>
    </div>
</div>
<script>
    window.getItemColumnSettings = window.getItemColumnSettings || function () {
        const modal = document.getElementById('itemColumnModal');
        if (!modal || !window.jQuery) {
            return { category: false, code: false, description: false, discount: false };
        }
        const $modal = window.jQuery(modal);
        return {
            category: $modal.find('.check-category').is(':checked'),
            code: $modal.find('.check-item-code').is(':checked'),
            description: $modal.find('.check-description').is(':checked'),
            discount: $modal.find('.check-discount').is(':checked')
        };
    };

    window.applyItemColumnVisibility = window.applyItemColumnVisibility || function (scope) {
        if (!window.jQuery) return;
        const settings = window.getItemColumnSettings();
        const $scope = scope ? window.jQuery(scope) : window.jQuery(document);
        $scope.find('.col-category').toggleClass('d-none', !settings.category);
        $scope.find('.col-item-code').toggleClass('d-none', !settings.code);
        $scope.find('.col-description').toggleClass('d-none', !settings.description);
        $scope.find('.col-discount').toggleClass('d-none', !settings.discount);
    };

    window.getSharedItemCategoryOptionsHtml = window.getSharedItemCategoryOptionsHtml || function (selectedValue) {
        const normalizedSelected = String(selectedValue || '').trim();
        const items = Array.isArray(window.items) ? window.items : [];
        const categories = Array.from(new Set(items
            .map(item => item?.category_name || item?.category?.name || item?.category || item?.category_id || '')
            .map(value => String(value || '').trim())
            .filter(Boolean)))
            .sort((a, b) => a.localeCompare(b));

        let html = '<option value="">Select Category</option>';
        categories.forEach(category => {
            const isSelected = category === normalizedSelected ? ' selected' : '';
            html += `<option value="${category.replace(/"/g, '&quot;')}"${isSelected}>${category}</option>`;
        });

        if (normalizedSelected && !categories.includes(normalizedSelected)) {
            html += `<option value="${normalizedSelected.replace(/"/g, '&quot;')}" selected>${normalizedSelected}</option>`;
        }

        return html;
    };

    window.transformItemExtraFields = window.transformItemExtraFields || function (scope) {
        if (!window.jQuery) return;
        const $scope = scope ? window.jQuery(scope) : window.jQuery(document);

        $scope.find('.col-category').each(function () {
            const $cell = window.jQuery(this);
            const $input = $cell.find('input.item-category');
            if ($input.length) {
                const selectedValue = $input.val() || '';
                $input.replaceWith(`<select class="item-category">${window.getSharedItemCategoryOptionsHtml(selectedValue)}</select>`);
            } else {
                const $select = $cell.find('select.item-category');
                if ($select.length) {
                    const selectedValue = $select.val() || '';
                    $select.html(window.getSharedItemCategoryOptionsHtml(selectedValue));
                    $select.val(selectedValue);
                }
            }
        });

        $scope.find('.item-code').prop('readonly', true);
        $scope.find('.item-desc').prop('readonly', true);

        $scope.find('.col-discount').each(function () {
            const $cell = window.jQuery(this);
            if ($cell.find('.item-discount-fields').length) {
                return;
            }
            const $amountInput = $cell.find('.item-discount');
            if (!$amountInput.length) {
                return;
            }
            const currentValue = $amountInput.val() || '0';
            $amountInput.replaceWith(`
                <div class="item-discount-fields">
                    <input type="number" class="item-discount-pct" value="" min="0" step="0.01" placeholder="%">
                    <input type="number" class="item-discount" value="${currentValue}" min="0" step="0.01" placeholder="Amount">
                </div>
            `);
        });
    };

    window.ensureSharedItemSelectOptions = window.ensureSharedItemSelectOptions || function ($select) {
        if (!window.jQuery || !$select || !$select.length) return;
        if ($select.data('all-options-html')) return;
        $select.data('all-options-html', $select.html() || '');
    };

    window.filterSharedRowItemsByCategory = window.filterSharedRowItemsByCategory || function ($row) {
        if (!window.jQuery || !$row || !$row.length) return;

        const $select = $row.find('.item-name').first();
        if (!$select.length) return;

        window.ensureSharedItemSelectOptions($select);

        const selectedCategory = String($row.find('.item-category').val() || '').trim().toLowerCase();
        const currentValue = String($select.val() || '').trim();
        const allOptionsHtml = $select.data('all-options-html') || '';

        if (!allOptionsHtml) return;

        const $temp = window.jQuery('<select>' + allOptionsHtml + '</select>');
        const $options = $temp.find('option').filter(function () {
            const value = String(window.jQuery(this).attr('value') || '').trim();
            if (!value) return true;
            if (!selectedCategory) return true;
            const optionCategory = String(window.jQuery(this).data('category') || '').trim().toLowerCase();
            return optionCategory === selectedCategory;
        });

        $select.html($options);

        if (currentValue && $select.find(`option[value="${currentValue.replace(/"/g, '\\"')}"]`).length) {
            $select.val(currentValue);
        } else {
            $select.val('');
            $row.find('.item-code').val('');
            $row.find('.item-desc').val('');
            $row.find('.item-price').val('0');
            $row.find('.item-amount').val('0');
            $row.find('.item-discount').val('0');
            $row.find('.item-discount-pct').val('');
        }

        $select.trigger('change.sharedPicker');
    };

    window.syncSharedDiscountRow = window.syncSharedDiscountRow || function ($row, source) {
        if (!window.jQuery || !$row || !$row.length) return;
        const qty = parseFloat($row.find('.item-qty').val() || 0) || 0;
        const price = parseFloat($row.find('.item-price').val() || 0) || 0;
        const baseAmount = qty * price;
        const $pct = $row.find('.item-discount-pct');
        const $amount = $row.find('.item-discount');
        if (!$amount.length) return;

        if (source === 'pct') {
            const pctValue = parseFloat($pct.val() || 0) || 0;
            const amountValue = baseAmount > 0 ? (baseAmount * pctValue) / 100 : 0;
            $amount.val(amountValue.toFixed(2)).trigger('change');
            return;
        }

        if (source === 'amount') {
            const amountValue = parseFloat($amount.val() || 0) || 0;
            const pctValue = baseAmount > 0 ? (amountValue / baseAmount) * 100 : 0;
            $pct.val(amountValue > 0 ? pctValue.toFixed(2) : '');
            return;
        }

        if (($pct.val() || '').toString().trim() !== '') {
            const pctValue = parseFloat($pct.val() || 0) || 0;
            const amountValue = baseAmount > 0 ? (baseAmount * pctValue) / 100 : 0;
            $amount.val(amountValue.toFixed(2)).trigger('change');
        } else {
            const amountValue = parseFloat($amount.val() || 0) || 0;
            const pctValue = baseAmount > 0 ? (amountValue / baseAmount) * 100 : 0;
            $pct.val(amountValue > 0 ? pctValue.toFixed(2) : '');
        }
    };

    if (window.jQuery && !window.__itemColumnModalBound) {
        window.__itemColumnModalBound = true;

        window.jQuery(document).on('change', '#itemColumnModal .check-category, #itemColumnModal .check-item-code, #itemColumnModal .check-description, #itemColumnModal .check-discount', function () {
            const $modal = window.jQuery('#itemColumnModal');
            $modal.find('.item-filter-category').prop('disabled', !$modal.find('.check-category').is(':checked'));
            $modal.find('.item-filter-code').prop('disabled', !$modal.find('.check-item-code').is(':checked'));
            $modal.find('.item-filter-description').prop('disabled', !$modal.find('.check-description').is(':checked'));
            $modal.find('.item-filter-discount').prop('disabled', !$modal.find('.check-discount').is(':checked'));
        });

        window.jQuery(document).on('click', '#itemColumnModal .item-filter-apply', function () {
            window.transformItemExtraFields(document);
            window.applyItemColumnVisibility(document);
            window.jQuery(document).trigger('item-column-settings:apply', [window.getItemColumnSettings()]);
        });

        window.jQuery(document).on('change', '.item-discount-pct', function () {
            window.syncSharedDiscountRow(window.jQuery(this).closest('tr'), 'pct');
        });

        window.jQuery(document).on('input change', '.item-discount', function () {
            window.syncSharedDiscountRow(window.jQuery(this).closest('tr'), 'amount');
        });

        window.jQuery(document).on('input change', '.item-qty, .item-price', function () {
            window.syncSharedDiscountRow(window.jQuery(this).closest('tr'));
        });

        window.jQuery(document).on('change', '.item-category', function () {
            window.filterSharedRowItemsByCategory(window.jQuery(this).closest('tr'));
        });

        window.jQuery(document).on('focus mousedown', '.item-name', function () {
            window.filterSharedRowItemsByCategory(window.jQuery(this).closest('tr'));
        });

        window.jQuery(function () {
            window.transformItemExtraFields(document);
            window.applyItemColumnVisibility(document);
            window.jQuery('.item-name').each(function () {
                window.ensureSharedItemSelectOptions(window.jQuery(this));
            });

            document.querySelectorAll('.item-rows').forEach(container => {
                const observer = new MutationObserver(() => {
                    window.transformItemExtraFields(container);
                    window.applyItemColumnVisibility(container);
                    window.jQuery(container).find('.item-name').each(function () {
                        window.ensureSharedItemSelectOptions(window.jQuery(this));
                    });
                });
                observer.observe(container, { childList: true });
            });
        });
    }
</script>
