function initializeForm(context) {
    const $ctx = $(context);

    const itemOptionsHtml = (window.items || []).map(item => {
        return `<option value="${item.id}" data-price="${item.price ?? ""}" data-sale-price="${item.sale_price ?? ""}" data-unit="${item.unit || ''}">${item.name}</option>`;
    }).join('');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const todayValue = `${yyyy}-${mm}-${dd}`;

    $ctx.find('.invoice-date').val(todayValue);
    $ctx.find('.due-date').val(todayValue);

    if (window.editSaleData) {
        populateFormFromSale(window.editSaleData);
    }

    function populateFormFromSale(sale) {
        const partyOption = $ctx.find('.party-select option').filter(function () {
            return $(this).val() == (sale.party_id || '');
        }).first();

        if (partyOption.length) {
            partyOption.prop('selected', true);
        }

        $ctx.find('.bill-number').val(sale.bill_number || '');
        $ctx.find('.invoice-date').val(sale.invoice_date ? sale.invoice_date.split(' ')[0] : todayValue);
        $ctx.find('.due-date').val(sale.due_date ? sale.due_date.split(' ')[0] : todayValue);
        $ctx.find('.description-input').val(sale.description || '');

        $ctx.find('.item-rows').empty();
        (sale.items || []).forEach(item => {
            addRow();
            const $row = $ctx.find('.item-rows tr').last();
            const matchOption = $row.find('.item-name option').filter(function () {
                return $(this).text().trim() === (item.item_name || '').trim();
            }).first();

            if (matchOption.length) {
                matchOption.prop('selected', true);
            }

            $row.find('.item-category').val(item.item_category || '');
            $row.find('.item-code').val(item.item_code || '');
            $row.find('.item-desc').val(item.item_description || '');
            $row.find('.item-discount').val(item.discount || 0);
            $row.find('.item-qty').val(item.quantity || 0);
            $row.find('.item-unit').val(item.unit || 'NONE');
            $row.find('.item-price').val(item.unit_price || 0);
            $row.find('.item-amount').val(item.amount || 0);
        });

        $ctx.find('.discount-pct').val(sale.discount_pct || 0);
        $ctx.find('.discount-rs').val(sale.discount_rs || 0);
        $ctx.find('.tax-select').val(sale.tax_pct || 0);
        $ctx.find('.round-off-val').val(sale.round_off || 0);
        $ctx.find('.grand-total').val(sale.grand_total || 0);

        if (sale.description) {
            $ctx.find('.description-pane').removeClass('d-none');
        }

        calculateTotals();
    }

    $ctx.on('change', '.item-name', function() {
        const $row = $(this).closest('tr');
        const $selected = $(this).find('option:selected');
        const price = parseFloat($selected.data('price')) || parseFloat($selected.data('sale-price')) || 0;
        const unit = $selected.data('unit') || 'NONE';

        $row.find('.item-price').val(price.toFixed(2));
        $row.find('.item-unit').val(unit);
        $row.find('.item-qty').val(1).trigger('change');
    });

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
                <td><input type="number" class="item-qty" value="1"></td>
                <td><select class="item-unit"><option>NONE</option><option>PCS</option><option>BOX</option></select></td>
                <td><input type="number" class="item-price" value="0"></td>
                <td class="col-amount"><input type="text" class="item-amount" value="0" readonly></td>
                <td class="add-col"></td>
            </tr>
        `;

        $ctx.find('.item-rows').append(newRow);
    }

    $ctx.on('click', '.delete-row-icon', function() {
        if ($ctx.find('.item-rows tr').length > 1) {
            $(this).closest('tr').remove();
            reindexRows();
            calculateTotals();
        }
    });

    function reindexRows() {
        $ctx.find('.item-rows tr').each(function(index) {
            $(this).find('.row-index-text').text(index + 1);
        });
    }

    $ctx.on('keyup change', '.item-qty, .item-price, .item-discount', function() {
        const $row = $(this).closest('tr');
        const qty = parseFloat($row.find('.item-qty').val()) || 0;
        const price = parseFloat($row.find('.item-price').val()) || 0;
        const itemDiscount = parseFloat($row.find('.item-discount').val()) || 0;
        const amount = (qty * price) - itemDiscount;

        $row.find('.item-amount').val(amount.toFixed(2));
        calculateTotals();
    });

    $ctx.on('click', '.add-description', function() {
        $ctx.find('.description-pane').toggleClass('d-none');
    });

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

    $ctx.on('keyup change', '.discount-pct, .discount-rs, .tax-select, .round-off-check', function() {
        const totalBaseAmount = parseFloat($ctx.find('.total-base-amount').text()) || 0;
        applyDiscountTax(totalBaseAmount);
    });

    function applyDiscountTax(base) {
        let finalBase = base;
        const discPct = parseFloat($ctx.find('.discount-pct').val()) || 0;
        const discRs = parseFloat($ctx.find('.discount-rs').val()) || 0;
        const taxPct = parseFloat($ctx.find('.tax-select').val()) || 0;

        if (discPct > 0) {
            finalBase -= (finalBase * discPct / 100);
        }

        if (discRs > 0) {
            finalBase -= discRs;
        }

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

    function gatherChallanData() {
        const items = Array.from($ctx.find('.item-row')).map(row => {
            const $row = $(row);
            return {
                item_name: $row.find('.item-name option:selected').text() || '',
                item_category: $row.find('.item-category').val() || '',
                item_code: $row.find('.item-code').val() || '',
                item_description: $row.find('.item-desc').val() || '',
                quantity: parseInt($row.find('.item-qty').val() || 0, 10) || 0,
                unit: $row.find('.item-unit').val() || '',
                unit_price: parseFloat($row.find('.item-price').val() || 0) || 0,
                discount: parseFloat($row.find('.item-discount').val() || 0) || 0,
                amount: parseFloat($row.find('.item-amount').val() || 0) || 0,
            };
        }).filter(item => item.item_name || item.quantity || item.amount);

        return {
            type: 'delivery_challan',
            party_id: $ctx.find('.party-select').val() || '',
            party_name: $ctx.find('.party-select option:selected').text() || '',
            bill_number: $ctx.find('.bill-number').val() || '',
            invoice_date: $ctx.find('.invoice-date').val() || todayValue,
            due_date: $ctx.find('.due-date').val() || todayValue,
            total_qty: parseInt($ctx.find('.total-qty').text() || 0, 10) || 0,
            total_amount: parseFloat($ctx.find('.total-base-amount').text() || 0) || 0,
            discount_pct: parseFloat($ctx.find('.discount-pct').val() || 0) || 0,
            discount_rs: parseFloat($ctx.find('.discount-rs').val() || 0) || 0,
            tax_pct: parseFloat($ctx.find('.tax-select').val() || 0) || 0,
            tax_amount: parseFloat($ctx.find('.tax-amount-display').text() || 0) || 0,
            round_off: parseFloat($ctx.find('.round-off-val').val() || 0) || 0,
            grand_total: parseFloat($ctx.find('.grand-total').val() || 0) || 0,
            status: 'open',
            description: $ctx.find('.description-input').val() || null,
            items,
            payments: [],
        };
    }

    function showToast(message, isError = false) {
        const toastEl = document.getElementById('sale-toast');
        if (!toastEl) {
            alert(message);
            return;
        }

        const toastBody = toastEl.querySelector('.toast-body');
        toastBody.textContent = message;
        toastEl.classList.toggle('text-bg-success', !isError);
        toastEl.classList.toggle('text-bg-danger', isError);

        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    }

    $ctx.on('click', '.btn-save', function() {
        const challanData = gatherChallanData();

        if (!challanData.items.length) {
            showToast('Please add at least one item before saving.', true);
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).text('Saving...');

        fetch(window.saleStoreUrl, {
            method: window.saleMethod || 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(challanData),
        })
            .then(async res => {
                const payload = await res.json();
                if (!res.ok) {
                    throw new Error(payload.message || 'Server error');
                }
                return payload;
            })
            .then(data => {
                showToast('Delivery challan saved successfully!');
                if (data.redirect_url) {
                    setTimeout(() => {
                        window.location.href = data.redirect_url;
                    }, 1500);
                }
            })
            .catch(error => {
                showToast('Error saving challan. ' + (error.message || ''), true);
            })
            .finally(() => {
                btn.prop('disabled', false).text('Save');
            });
    });

    calculateTotals();
}

