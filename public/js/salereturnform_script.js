function initializeForm(context) {
    const $ctx = $(context);
    const hasCustomPartyDropdown = $ctx.find('.party-id').length > 0;
    const $paidInput = $ctx.find('.received-amount, .advance-amount').first();
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const itemOptionsHtml = (window.items || []).map(item => {
        const plainLabel = item.name || ""; const richLabel = `${plainLabel} | Sale: ${item.sale_price ?? item.price ?? 0} | Stock: ${item.opening_qty ?? 0} | Location: ${item.location ?? ""}`; return `<option value="${item.id}" data-price="${item.price ?? ""}" data-sale-price="${item.sale_price ?? ""}" data-stock="${item.opening_qty ?? ""}" data-location="${item.location ?? ""}" data-label="${plainLabel}" data-rich-label="${richLabel}" data-unit="${item.unit || ''}">${richLabel}</option>`;
    }).join('');

    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const todayValue = `${yyyy}-${mm}-${dd}`;

    $ctx.find('.order-date').val(todayValue);
    $ctx.find('.due-date').val(todayValue);

    if (window.editSaleReturnData) {
        populateFormFromSaleReturn(window.editSaleReturnData);
    }

    function setupAdjustmentControls() {
        const $roundOffInput = $ctx.find('.round-off-val');
        const $roundOffCheck = $ctx.find('.round-off-check');
        if ($roundOffInput.length && $roundOffCheck.length) {
            $roundOffInput.prop('readonly', !$roundOffCheck.is(':checked'));
            if (!$roundOffCheck.is(':checked')) {
                $roundOffInput.val('0');
            }
        }

        if ($paidInput.length && !$ctx.find('.fill-balance-check').length) {
            $paidInput.closest('.calc-inputs').prepend(
                `<label class="d-flex align-items-center gap-1 me-2 mb-0 text-nowrap" style="font-size:12px;">
                    <input type="checkbox" class="fill-balance-check">
                    <span>Full Advance</span>
                </label>`
            );
        }
    }

    function showToast(message, isError = false) {
        const toastEl = document.getElementById('sale-toast');
        if (!toastEl) return;

        const toastBody = toastEl.querySelector('.toast-body');
        toastBody.textContent = message;
        toastEl.classList.toggle('text-bg-success', !isError);
        toastEl.classList.toggle('text-bg-danger', isError);
        bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 5000 }).show();
    }

    function reindexRows() {
        $ctx.find('.item-rows tr').each(function(index) {
            $(this).find('.row-index-text').text(index + 1);
        });
    }

    function populateFormFromSaleReturn(saleReturn) {
        if (hasCustomPartyDropdown) {
            const party = (window.parties || []).find(p => String(p.id) === String(saleReturn.party_id || ''));
            $ctx.find('.party-id').val(saleReturn.party_id || '');
            if (party) {
                $ctx.find('#partyDropdownBtn').text(party.name || 'Select Party');
                $ctx.find('.phone-input').val(party.phone || saleReturn.phone || '');
                $ctx.find('.billing-address').val(party.billing_address || saleReturn.billing_address || '');
            } else {
                $ctx.find('#partyDropdownBtn').text('Select Party');
            }
        } else {
            const partyOption = $ctx.find('.party-select option').filter(function () {
                return $(this).val() == (saleReturn.party_id || '');
            }).first();

            if (partyOption.length) {
                partyOption.prop('selected', true);
                partyOption.trigger('change');
            }
        }

        $ctx.find('.phone-input').val(saleReturn.phone || '');
        $ctx.find('.billing-address').val(saleReturn.billing_address || '');
        $ctx.find('.shipping-address').val(saleReturn.shipping_address || '');
        $ctx.find('.bill-number').val(saleReturn.bill_number || '');
        $ctx.find('.reference-bill-number').val(saleReturn.reference_bill_number || '');
        $ctx.find('.order-date').val(saleReturn.order_date || saleReturn.invoice_date || todayValue);
        $ctx.find('.due-date').val(saleReturn.due_date || todayValue);

        $ctx.find('.item-rows').empty();
        (saleReturn.items || []).forEach(item => {
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
            $row.find('.item-qty').val(item.quantity || 1);
            ensureUnitOption($row.find('.item-unit'), item.unit || 'NONE');
            $row.find('.item-price').val(item.unit_price || 0);
            $row.find('.item-amount').val(item.amount || 0);
        });

        $ctx.find('.discount-pct').val(saleReturn.discount_pct || 0);
        $ctx.find('.discount-rs').val(saleReturn.discount_rs || 0);
        $ctx.find('.tax-select').val(saleReturn.tax_pct || 0);
        $ctx.find('.tax-amount-display').text(parseFloat(saleReturn.tax_amount || 0).toFixed(2));
        $ctx.find('.round-off-val').val(parseFloat(saleReturn.round_off || 0).toFixed(2));
        $ctx.find('.grand-total').val(parseFloat(saleReturn.grand_total || 0).toFixed(2));
        $ctx.find('.balance-amount').text(parseFloat(saleReturn.balance || saleReturn.grand_total || 0).toFixed(2));
        $paidInput.val(parseFloat(saleReturn.received_amount || 0).toFixed(2));
        $ctx.find('.description-input').val(saleReturn.description || '');

        calculateTotals();
    }

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
                <td>
                    <select class="item-unit"><option value="">Select Unit</option><option value="PCS">PCS (Pieces)</option><option value="BOX">BOX</option><option value="PACK">PACK</option><option value="SET">SET</option><option value="KG">KG (Kilogram)</option><option value="G">Gram</option><option value="M">Meter</option><option value="FT">Feet</option><option value="L">Liter</option><option value="ML">Milliliter</option></select>
                </td>
                <td><input type="number" class="item-price" value="0"></td>
                <td class="col-amount"><input type="text" class="item-amount" value="0" readonly></td>
                <td class="add-col"></td>
            </tr>
        `;

        $ctx.find('.item-rows').append(newRow);
    }

    function updatePaymentSummary() {
        const grandTotal = parseFloat($ctx.find('.grand-total').val() || 0) || 0;
        let received = 0;

        const defaultType = $ctx.find('.default-payment-type').val() || '';
        if (defaultType.startsWith('bank-')) {
            received += parseFloat($ctx.find('.default-payment-amount').val() || 0) || 0;
        }

        received += Array.from($ctx.find('.payment-type-entry')).reduce((sum, el) => {
            const rawType = $(el).val() || '';
            if (!rawType.startsWith('bank-')) {
                return sum;
            }

            const amountInput = $(el).closest('.payment-entry').find('.payment-amount');
            return sum + (parseFloat(amountInput.val() || 0) || 0);
        }, 0);

        if ($ctx.find('.fill-balance-check').is(':checked')) {
            received = grandTotal;
        }

        const balance = Math.max(0, grandTotal - received);
        $ctx.find('.payment-total-amount').text(received.toFixed(2));
        $paidInput.val(received.toFixed(2));
        $ctx.find('.balance-amount').text(balance.toFixed(2));
    }

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

        const roundOffEnabled = $ctx.find('.round-off-check').is(':checked');
        let roundOffVal = roundOffEnabled ? (parseFloat($ctx.find('.round-off-val').val()) || 0) : 0;
        let grandTotal = finalBase + roundOffVal;

        $ctx.find('.round-off-val').val(roundOffVal.toFixed(2));
        $ctx.find('.grand-total').val(grandTotal.toFixed(2));
        updatePaymentSummary();
    }

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

    function gatherSaleReturnData() {
        const items = Array.from($ctx.find('.item-row')).map(row => {
            const $row = $(row);
            const itemName = $row.find('.item-name option:selected').data('label') || $row.find('.item-name option:selected').text() || '';

            return {
                item_name: itemName,
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

        const payments = [];
        const defaultTypeVal = $ctx.find('.default-payment-type').val();

        if (defaultTypeVal) {
            const bankId = parseInt(defaultTypeVal.replace('bank-', ''), 10);
            const bank = (window.bankAccounts || []).find(b => b.id === bankId);
            const defaultAmount = parseFloat($ctx.find('.default-payment-amount').val() || 0) || 0;
            const defaultReference = $ctx.find('.default-payment-reference').val() || null;

            if (defaultAmount > 0) {
                payments.push({
                    payment_type: bank?.display_name || 'Bank',
                    bank_account_id: bankId || null,
                    amount: defaultAmount,
                    reference: defaultReference,
                });
            }
        }

        Array.from($ctx.find('.payment-entries .payment-entry')).forEach(entry => {
            const $entry = $(entry);
            const rawType = $entry.find('.payment-type-entry').val() || '';
            const isBank = rawType.startsWith('bank-');
            const bankId = isBank ? rawType.replace('bank-', '') : null;
            const bank = isBank ? (window.bankAccounts || []).find(b => String(b.id) === String(bankId)) : null;
            const amount = parseFloat($entry.find('.payment-amount').val() || 0) || 0;
            const reference = $entry.find('.payment-reference').val() || null;

            if (!rawType || amount <= 0) {
                return;
            }

            payments.push({
                payment_type: isBank ? (bank?.display_name || 'Bank') : rawType,
                bank_account_id: bankId,
                amount,
                reference,
            });
        });

        return {
            type: 'sale_return',
            party_id: $ctx.find('.party-id').val() || $ctx.find('.party-select').val() || null,
            phone: $ctx.find('.phone-input').val() || '',
            billing_address: $ctx.find('.billing-address').val() || '',
            shipping_address: $ctx.find('.shipping-address').val() || '',
            bill_number: $ctx.find('.bill-number').val() || '',
            reference_bill_number: $ctx.find('.reference-bill-number').val() || '',
            order_date: $ctx.find('.order-date').val() || '',
            due_date: $ctx.find('.due-date').val() || '',
            invoice_date: $ctx.find('.order-date').val() || '',
            total_qty: parseInt($ctx.find('.total-qty').text() || 0, 10) || 0,
            total_amount: parseFloat($ctx.find('.total-base-amount').text() || 0) || 0,
            discount_pct: parseFloat($ctx.find('.discount-pct').val() || 0) || 0,
            discount_rs: parseFloat($ctx.find('.discount-rs').val() || 0) || 0,
            tax_pct: parseFloat($ctx.find('.tax-select').val() || 0) || 0,
            tax_amount: parseFloat($ctx.find('.tax-amount-display').text() || 0) || 0,
            round_off: parseFloat($ctx.find('.round-off-val').val() || 0) || 0,
            grand_total: parseFloat($ctx.find('.grand-total').val() || 0) || 0,
            balance: parseFloat($ctx.find('.balance-amount').text() || 0) || 0,
            description: $ctx.find('.description-input').val() || null,
            image_path: (() => {
                const file = $ctx.find('.image-input')[0]?.files?.[0];
                return file ? file.name : null;
            })(),
            document_path: (() => {
                const file = $ctx.find('.document-input')[0]?.files?.[0];
                return file ? file.name : null;
            })(),
            items,
            payments,
        };
    }

    $ctx.on('change', '.party-select', function() {
        const selectedId = $(this).val();
        const party = (window.parties || []).find(p => String(p.id) === String(selectedId));

        if (party) {
            $ctx.find('.phone-input').val(party.phone || '');
            $ctx.find('.billing-address').val(party.billing_address || '');
            $ctx.find('.shipping-address').val(party.shipping_address || '');
        } else {
            $ctx.find('.phone-input').val('');
            $ctx.find('.billing-address').val('');
            $ctx.find('.shipping-address').val('');
        }
    });

    $ctx.on('click', '.party-option', function(e) {
        e.preventDefault();
        const $option = $(this);
        const partyId = $option.data('id') || '';
        const partyName = $.trim($option.find('span').first().text());
        const phone = $option.data('phone') || '';
        const billing = $option.data('billing') || '';

        $ctx.find('.party-id').val(partyId);
        $ctx.find('#partyDropdownBtn').text(partyName || 'Select Party');
        $ctx.find('.phone-input').val(phone);
        $ctx.find('.billing-address').val(billing);
    });

    $ctx.find('.add-row-btn').on('click', function() {
        addRow();
    });

    $ctx.on('click', '.delete-row-icon', function() {
        if ($ctx.find('.item-rows tr').length > 1) {
            $(this).closest('tr').remove();
            reindexRows();
            calculateTotals();
            return;
        }

        const $row = $(this).closest('tr');
        $row.find('input').val('');
        $row.find('.item-qty').val('1');
        $row.find('.item-price, .item-amount, .item-discount').val('0');
        calculateTotals();
    });

    function restoreRichItemDropdownLabels() {
        $ctx.find('.item-name option').each(function() {
            const richLabel = $(this).data('rich-label');
            if (richLabel) {
                $(this).text(richLabel);
            }
        });
    }

    function collapseSelectedItemLabel($select) {
        restoreRichItemDropdownLabels();
        const $selected = $select.find('option:selected');
        const plainLabel = $selected.data('label');
        if (plainLabel) {
            $selected.text(plainLabel);
        }
    }

    function ensureUnitOption($unitSelect, unit) {
        const normalizedUnit = (unit || '').toString().trim();
        if (!normalizedUnit) return;

        const existingOption = $unitSelect.find('option').filter(function() {
            return ($(this).val() || $(this).text()).toString().trim() === normalizedUnit;
        }).first();

        if (!existingOption.length) {
            $unitSelect.append(`<option value="${normalizedUnit}">${normalizedUnit}</option>`);
        }

        $unitSelect.val(normalizedUnit);
    }

    $ctx.on('focus mousedown', '.item-name', function() {
        restoreRichItemDropdownLabels();
    });

    $ctx.on('blur', '.item-name', function() {
        collapseSelectedItemLabel($(this));
    });

    $ctx.on('change', '.item-name', function() {
        const $row = $(this).closest('tr');
        const $selected = $(this).find('option:selected');
        const price = parseFloat($selected.data('price')) || parseFloat($selected.data('sale-price')) || 0;
        const unit = $selected.data('unit') || '';

        $row.find('.item-qty').val(1);
        $row.find('.item-price').val(price.toFixed(2));
        if (unit) {
            ensureUnitOption($row.find('.item-unit'), unit);
        }

        $row.find('.item-qty').trigger('change');
    });

    $ctx.on('keyup change', '.item-qty, .item-price, .item-discount', function() {
        const $row = $(this).closest('tr');
        const qty = parseFloat($row.find('.item-qty').val()) || 0;
        const price = parseFloat($row.find('.item-price').val()) || 0;
        const discount = parseFloat($row.find('.item-discount').val()) || 0;
        const amount = (qty * price) - discount;

        $row.find('.item-amount').val(amount.toFixed(2));
        calculateTotals();
    });

    $ctx.on('click', '.add-payment-entry', function(e) {
        e.preventDefault();

        const $defaultAmount = $ctx.find('.default-payment-amount');
        const $defaultReference = $ctx.find('.default-payment-reference');

        if ($defaultAmount.hasClass('d-none') || $defaultReference.hasClass('d-none')) {
            $defaultAmount.removeClass('d-none').focus();
            $defaultReference.removeClass('d-none');
            updatePaymentSummary();
            return;
        }

        const template = document.getElementById('payment-entry-template');
        if (!template) {
            return;
        }

        const clone = template.content.cloneNode(true);
        $ctx.find('.payment-entries').append(clone);
        $ctx.find('.payment-entries .payment-entry').last().find('.payment-amount').focus();
    });

    $ctx.on('change', '.default-payment-type, .payment-type-entry', updatePaymentSummary);
    $ctx.on('keyup change', '.default-payment-amount, .payment-amount', updatePaymentSummary);

    $ctx.on('click', '.remove-payment-entry', function() {
        $(this).closest('.payment-entry').remove();
        updatePaymentSummary();
    });

    $ctx.on('keyup change', '.discount-pct, .discount-rs, .tax-select, .round-off-check', function() {
        const totalBaseAmount = parseFloat($ctx.find('.total-base-amount').text()) || 0;
        applyDiscountTax(totalBaseAmount);
    });
    $ctx.on('change', '.fill-balance-check, .round-off-check', function() {
        setupAdjustmentControls();
        calculateTotals();
    });
    $ctx.on('input change', '.round-off-val', calculateTotals);

    $ctx.on('click', '.add-description', function() {
        const $pane = $ctx.find('.description-pane');
        $pane.toggleClass('d-none');
        if (!$pane.hasClass('d-none')) {
            $pane.find('.description-input').focus();
        }
    });

    $ctx.on('click', '.add-image', function() {
        $ctx.find('.image-input').trigger('click');
    });

    $ctx.on('click', '.add-document', function() {
        $ctx.find('.document-input').trigger('click');
    });

    $ctx.on('change', '.image-input', function() {
        const file = this.files?.[0];
        const $preview = $ctx.find('.image-preview');
        const $img = $preview.find('.image-preview-img');

        if (!file) {
            $preview.addClass('d-none');
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            $img.attr('src', e.target.result);
            $preview.removeClass('d-none');
        };
        reader.readAsDataURL(file);
    });

    $ctx.on('click', '.image-placeholder, .replace-image', function() {
        $ctx.find('.image-input').trigger('click');
    });

    $ctx.on('click', '.remove-image', function() {
        $ctx.find('.image-input').val('');
        $ctx.find('.image-preview').addClass('d-none');
    });

    $ctx.on('change', '.document-input', function() {
        const fileName = this.files?.[0]?.name || '';
        $ctx.find('.selected-document-name').text(fileName ? 'Document: ' + fileName : '');
    });

    $ctx.on('click', '.btn-save', function() {
        const saleReturnData = gatherSaleReturnData();

        if (!saleReturnData.items.length) {
            showToast('Please add at least one item before saving.', true);
            return;
        }

        const btn = $(this);
        btn.prop('disabled', true).text('Saving...');

        fetch(window.saleReturnStoreUrl, {
            method: window.saleReturnMethod || 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(saleReturnData),
        })
            .then(async res => {
                const text = await res.text();
                let data = null;

                try {
                    data = JSON.parse(text);
                } catch (e) {
                    throw new Error('Invalid JSON response: ' + text);
                }

                if (!res.ok) {
                    throw new Error(data?.message || 'Server error');
                }

                return data;
            })
            .then(data => {
                if (data.success) {
                    if (data.bill_number) {
                        $ctx.find('.bill-number').val(data.bill_number);
                    }

                    showToast('Sale return saved successfully! Redirecting...');
                    if (data.redirect_url) {
                        setTimeout(() => {
                            window.location.href = data.redirect_url;
                        }, 1500);
                    }
                    return;
                }

                showToast('Unable to save sale return.', true);
            })
            .catch(err => {
                console.error(err);
                showToast('Error saving sale return. ' + (err.message || ''), true);
            })
            .finally(() => {
                btn.prop('disabled', false).text('Save');
            });
    });

    setupAdjustmentControls();
    calculateTotals();
}



