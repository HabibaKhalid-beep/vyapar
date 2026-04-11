function initializeForm(context) {
    const $ctx = $(context);

    const itemOptionsHtml = (window.items || []).map(item => {
        return `<option value="${item.id}" data-price="${item.price}" data-unit="${item.unit || ''}">${item.name}</option>`;
    }).join('');

    // Party Combobox Logic
    $ctx.find('.party-select').on('change', function() {
        const selectedId = $(this).val();
        const party = (window.parties || []).find(p => String(p.id) === String(selectedId));
        const $phone = $ctx.find('.phone-input');
        const $billing = $ctx.find('.billing-address');

        if (party) {
            $phone.val(party.phone || '');
            $billing.val(party.billing_address || '');
        } else {
            $phone.val('');
            $billing.val('');
        }
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest($ctx.find('.party-input-wrapper')).length) {
            $ctx.find('.party-dropdown').fadeOut(100);
        }
    });

    $ctx.find('.dropdown-item').on('click', function() {
        const value = $(this).text();
        if (value.startsWith('+')) {
            console.log("Add New Party clicked");
        } else {
            $ctx.find('.party-input').val(value).trigger('change');
        }
        $ctx.find('.party-dropdown').fadeOut(100);
    });

    // Column Selection Box Logic
    $ctx.find('.table-settings-btn').on('click', function(e) {
        e.stopPropagation();
        $ctx.find('.settings-box').fadeToggle(150);
    });

    $(document).on('click', function(e) {
        if (!$(e.target).closest($ctx.find('.table-settings-btn')).length && !$(e.target).closest($ctx.find('.settings-box')).length) {
            $ctx.find('.settings-box').fadeOut(150);
        }
    });

    // Column Visibility Logic
    $ctx.find('.check-category').on('change', function() {
        toggleColumn('col-category', this.checked);
    });
    $ctx.find('.check-item-code').on('change', function() {
        toggleColumn('col-item-code', this.checked);
    });
    $ctx.find('.check-description').on('change', function() {
        toggleColumn('col-description', this.checked);
    });
    $ctx.find('.check-discount').on('change', function() {
        toggleColumn('col-discount', this.checked);
    });

    function toggleColumn(className, show) {
        if (show) {
            $ctx.find(`.${className}`).removeClass('d-none');
        } else {
            $ctx.find(`.${className}`).addClass('d-none');
        }
    }

    // Add Row functionality
    $ctx.find('.add-row-btn').on('click', function() {
        addRow();
    });

    function addRow() {
        const rowCount = $ctx.find('.item-rows tr').length + 1;
        const isCatVisible = $ctx.find('.check-category').is(':checked');
        const isCodeVisible = $ctx.find('.check-item-code').is(':checked');
        const isDescVisible = $ctx.find('.check-description').is(':checked');
        const isDiscVisible = $ctx.find('.check-discount').is(':checked');

        const newRow = `
            <tr class="item-row">
                <td class="row-num">
                    <span class="row-index-text">${rowCount}</span>
                    <div class="delete-row-icon"><i class="fa-solid fa-trash-can"></i></div>
                </td>
                <td>
                    <select class="form-select item-name">
                        <option value="" selected disabled>Select Item</option>
                        ${itemOptionsHtml}
                    </select>
                </td>
                <td class="col-category ${isCatVisible ? '' : 'd-none'}"><input type="text" class="item-category" placeholder="Category"></td>
                <td class="col-item-code ${isCodeVisible ? '' : 'd-none'}"><input type="text" class="item-code" placeholder="Item Code"></td>
                <td class="col-description ${isDescVisible ? '' : 'd-none'}"><input type="text" class="item-desc" placeholder="Description"></td>
                <td class="col-discount ${isDiscVisible ? '' : 'd-none'}"><input type="number" class="item-discount" value="0"></td>
                <td><input type="number" class="item-qty" value="0"></td>
                <td>
                    <select class="item-unit">
                        <option>NONE</option>
                        <option>PCS</option>
                        <option>BOX</option>
                    </select>
                </td>
                <td><input type="number" class="item-price" value="0"></td>
                <td class="col-amount"><input type="text" class="item-amount" value="0" readonly></td>
                <td class="add-col"></td>
            </tr>
        `;
        $ctx.find('.item-rows').append(newRow);
    }

    // Delete Row functionality
    $ctx.on('click', '.delete-row-icon', function() {
        if ($ctx.find('.item-rows tr').length > 1) {
            $(this).closest('tr').remove();
            reindexRows();
            calculateTotals();
        } else {
            const $row = $(this).closest('tr');
            $row.find('input').val('');
            $row.find('.item-qty, .item-price, .item-amount').val('0');
            calculateTotals();
        }
    });

    function reindexRows() {
        $ctx.find('.item-rows tr').each(function(index) {
            $(this).find('.row-index-text').text(index + 1);
        });
    }

// Item selection: auto-fill price and unit when an item is chosen
    $ctx.on('change', '.item-name', function() {
        const $row = $(this).closest('tr');
        const $selected = $(this).find('option:selected');
        const price = parseFloat($selected.data('price')) || 0;
        const unit = $selected.data('unit') || '';

        $row.find('.item-price').val(price.toFixed(2));
        if (unit) {
            $row.find('.item-unit').val(unit);
        }

        // Recalculate totals when the item changes
        $row.find('.item-qty').trigger('change');
    });

    // Line item calculation
    $ctx.on('keyup change', '.item-qty, .item-price, .item-discount', function() {
        const $row = $(this).closest('tr');
        const qty = parseFloat($row.find('.item-qty').val()) || 0;
        const price = parseFloat($row.find('.item-price').val()) || 0;
        const itemDiscount = parseFloat($row.find('.item-discount').val()) || 0;

        const amount = (qty * price) - itemDiscount;

        $row.find('.item-amount').val(amount.toFixed(2));
        calculateTotals();
    });

    // Calculations for totals
    function calculateTotals() {
        let totalQty = 0;
        let totalBaseAmount = 0;

        $ctx.find('.item-qty').each(function() {
            totalQty += parseFloat($(this).val()) || 0;
        });

        $ctx.find('.item-amount').each(function() {
            totalBaseAmount += parseFloat($(this).val()) || 0;
        });

        $ctx.find('.total-qty').text(totalQty);
        $ctx.find('.total-base-amount').text(totalBaseAmount.toFixed(2));

        applyDiscountTax(totalBaseAmount);
    }

    // Discount and Tax logic
    $ctx.on('keyup change', '.discount-pct, .discount-rs, .tax-select, .round-off-check', function() {
        const totalBaseAmount = parseFloat($ctx.find('.total-base-amount').text()) || 0;
        applyDiscountTax(totalBaseAmount);
    });

    function applyDiscountTax(base) {
        let finalBase = base;

        const discPct = parseFloat($ctx.find('.discount-pct').val()) || 0;
        if (discPct > 0) {
            finalBase -= (finalBase * discPct / 100);
        }

        const discRs = parseFloat($ctx.find('.discount-rs').val()) || 0;
        if (discRs > 0) {
            finalBase -= discRs;
        }

        const taxPct = parseFloat($ctx.find('.tax-select').val()) || 0;
        let taxAmount = 0;
        if (taxPct > 0) {
            taxAmount = (finalBase * taxPct / 100);
            finalBase += taxAmount;
        }
        $ctx.find('.tax-amount-display').text(taxAmount.toFixed(2));

        let grandTotal = finalBase;
        let roundOffVal = 0;

        if ($ctx.find('.round-off-check').is(':checked')) {
            const rounded = Math.round(grandTotal);
            roundOffVal = rounded - grandTotal;
            grandTotal = rounded;
        }

        $ctx.find('.round-off-val').val(roundOffVal.toFixed(2));
        $ctx.find('.grand-total').val(grandTotal.toFixed(2));
    }

    calculateTotals();
}
