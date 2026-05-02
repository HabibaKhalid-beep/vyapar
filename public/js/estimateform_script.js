function initializeForm(context) {
    const $ctx = $(context);
    const hasCustomPartyDropdown = $ctx.find('.party-id').length > 0;
    const $paidInput = $ctx.find('.received-amount, .advance-amount').first();

    const itemOptionsHtml = (window.items || []).map(item => {
        const plainLabel = item.name || ""; const richLabel = `${plainLabel} | Sale: ${item.sale_price ?? item.price ?? 0} | Stock: ${item.opening_qty ?? 0} | Location: ${item.location ?? ""}`; return `<option value="${item.id}" data-price="${item.price ?? ""}" data-sale-price="${item.sale_price ?? ""}" data-stock="${item.opening_qty ?? ""}" data-location="${item.location ?? ""}" data-label="${plainLabel}" data-rich-label="${richLabel}" data-unit="${item.unit || ''}">${richLabel}</option>`;
    }).join('');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Auto-fill invoice date and placeholder invoice no
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    $ctx.find('.invoice-date').val(`${yyyy}-${mm}-${dd}`);

    // If editing an existing sale, populate the form with saved values
    if (window.editSaleData) {
        populateFormFromSale(window.editSaleData);
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
                    <span>Full Receive</span>
                </label>`
            );
        }
    }

    function buildImageUrl(path) {
        if (!path) return '';
        const trimmed = path.toString().trim();
        // If it is already a full URL or absolute path, just normalize it
        if (/^https?:\/\//i.test(trimmed)) {
            return trimmed;
        }
        if (trimmed.startsWith('/')) {
            return encodeURI(trimmed);
        }
        // If it begins with storage/ (relative), use it as absolute
        if (trimmed.startsWith('storage/')) {
            return encodeURI('/' + trimmed);
        }
        // Otherwise assume it's just a filename stored under /storage/images/
        return encodeURI('/storage/images/' + trimmed);
    }

    function setImagePreviewUrl(url) {
        const $preview = $ctx.find('.image-preview');
        const $img = $preview.find('.image-preview-img');
        const $placeholder = $ctx.find('.image-placeholder');

        if (!url) {
            $preview.addClass('d-none');
            $placeholder.removeClass('d-none');
            return;
        }

        $img.attr('src', buildImageUrl(url));
        $preview.removeClass('d-none');
        $placeholder.addClass('d-none');
    }

    function populateFormFromSale(sale) {
        // Fill header fields
        if (hasCustomPartyDropdown) {
            const party = (window.parties || []).find(p => String(p.id) === String(sale.party_id || ''));
            $ctx.find('.party-id').val(sale.party_id || '');
            if (party) {
                $ctx.find('#partyDropdownBtn').text(party.name || 'Select Party');
                $ctx.find('.phone-input').val(party.phone || sale.phone || '');
                $ctx.find('.billing-address').val(party.billing_address || sale.billing_address || '');
            } else {
                $ctx.find('#partyDropdownBtn').text('Select Party');
            }
        } else {
            const partyOption = $ctx.find('.party-select option').filter(function () {
                return $(this).val() == (sale.party_id || '');
            }).first();

            if (partyOption.length) {
                partyOption.prop('selected', true);
                partyOption.trigger('change');
            } else {
                $ctx.find('.party-select').val('');
            }
        }

        $ctx.find('.phone-input').val(sale.phone || sale.party?.phone || '');
        $ctx.find('.billing-address').val(sale.billing_address || sale.party?.billing_address || '');
        $ctx.find('.bill-number').val(sale.bill_number || '');
        $ctx.find('.invoice-date').val(sale.invoice_date ? sale.invoice_date.split(' ')[0] : `${yyyy}-${mm}-${dd}`);

        // Items
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
            if (item.unit) {
                ensureUnitOption($row.find('.item-unit'), item.unit);
            }
            $row.find('.item-price').val(item.unit_price || 0);
            $row.find('.item-amount').val(item.amount || 0);
        });

        // Discount / Tax / Round off
        $ctx.find('.discount-pct').val(sale.discount_pct || 0);
        $ctx.find('.discount-rs').val(sale.discount_rs || 0);
        $ctx.find('.tax-select').val(sale.tax_pct || 0);
        $ctx.find('.round-off-val').val(sale.round_off || 0);
        $ctx.find('.grand-total').val(sale.grand_total || 0);

        // Description (show if already set)
        const desc = sale.description || '';
        $ctx.find('.description-input').val(desc);
        if (desc) {
            $ctx.find('.description-pane').removeClass('d-none');
        }

        // Image (show preview if there is an existing image)
        const imageUrl = sale.image_url || sale.image_path || '';
        setImagePreviewUrl(imageUrl);

        // Document (show file name if there is an existing document)
        const docName = sale.document_name || sale.document_path || '';
        if (docName) {
            $ctx.find('.selected-document-name').text('Document: ' + docName);
        } else {
            $ctx.find('.selected-document-name').text('');
        }

        // Payments: treat values as "already received" and allow adding new payments
        window.existingReceivedAmount = parseFloat(sale.received_amount || 0) || 0;
        window.existingBalance = parseFloat(sale.balance || 0) || 0;

        // Pre-select the same bank as the first payment (so user can quickly add more)
        $ctx.find('.default-payment-type').val('');
        $ctx.find('.default-payment-amount').val('0').addClass('d-none');
        $ctx.find('.default-payment-reference').val('').addClass('d-none');
        $ctx.find('.payment-entries').empty();

        (sale.payments || []).forEach((payment, index) => {
            if (index === 0 && payment.bank_account_id) {
                $ctx.find('.default-payment-type').val(`bank-${payment.bank_account_id}`);
            }
        });

        // Show the current received / balance values based on stored sale
        $ctx.find('.payment-total-amount').text((window.existingReceivedAmount || 0).toFixed(2));
        $ctx.find('.balance-amount').text((window.existingBalance || 0).toFixed(2));

        calculateTotals();
    }

    // Party select logic
    $ctx.on('change', '.party-select', function() {
        const selectedId = $(this).val();
        const party = (window.parties || []).find(p => String(p.id) === String(selectedId));
        if (party) {
            $ctx.find('.phone-input').val(party.phone || '');
            $ctx.find('.billing-address').val(party.billing_address || '');
        } else {
            $ctx.find('.phone-input').val('');
            $ctx.find('.billing-address').val('');
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

        // Close the dropdown after selection
        const dropdownElement = $ctx.find('#partyDropdownBtn').get(0);
        if (dropdownElement && bootstrap.Dropdown) {
            bootstrap.Dropdown.getInstance(dropdownElement)?.hide();
        }
    });

    // Party search/filter functionality
    // Party search/filter functionality
    $ctx.on('input', '.party-search-input', function(e) {
        e.stopPropagation();
        const searchValue = $(this).val().toLowerCase().trim();
        const $partyOptions = $ctx.find('.party-option');

        $partyOptions.each(function() {
            const $this = $(this);
            const partyName = $.trim($this.find('span').first().text()).toLowerCase();
            const partyPhone = $this.data('phone') ? String($this.data('phone')).toLowerCase() : '';

            if (searchValue === '' || partyName.includes(searchValue) || partyPhone.includes(searchValue)) {
                $this.closest('li').removeClass('d-none');
            } else {
                $this.closest('li').addClass('d-none');
            }
        });
    });

    // Prevent dropdown from closing when clicking on search input
    $ctx.on('click', '.dropdown-header-search', function(e) {
        e.stopPropagation();
    });

    $ctx.on('click', '.party-search-input', function(e) {
        e.stopPropagation();
    });

    // Prevent dropdown from closing when typing in search
    $ctx.on('keydown keyup', '.party-search-input', function(e) {
        e.stopPropagation();
    });

    // Clear search input when dropdown closes
    $ctx.on('hidden.bs.dropdown', '#partyDropdownMenu', function() {
        $ctx.find('.party-search-input').val('');
        $ctx.find('.party-option').closest('li').removeClass('d-none');
    });

    // Function to refresh party dropdown menu
    function refreshPartyDropdown() {
        const $dropdown = $ctx.find('#partyDropdownMenu');
        const $existingItems = $dropdown.find('.party-option').closest('li');

        // Clear existing party options but keep header and footer
        $existingItems.remove();

        // Rebuild party list
        const partiesHtml = (window.parties || []).map(party => `
            <li>
                <a class="dropdown-item d-flex justify-content-between party-option" href="#"
                   data-id="${party.id}"
                   data-phone="${party.phone || ''}"
                   data-billing="${(party.billing_address || '').replace(/"/g, '&quot;')}"
                   data-opening="${party.opening_balance || 0}"
                   data-type="${party.transaction_type || ''}">
                    <span>${party.name || ''}</span>
                    <span class="${party.transaction_type === 'pay' ? 'text-danger' : 'text-success'}">
                        ${party.transaction_type === 'pay' ? '<i class="fa-solid fa-arrow-up me-1"></i>' : '<i class="fa-solid fa-arrow-down me-1"></i>'}
                        ₹${parseFloat(party.opening_balance || 0).toFixed(2)}
                    </span>
                </a>
            </li>
        `).join('');

        // Insert new items before the divider
        const $divider = $dropdown.find('li:has(> hr.dropdown-divider)');
        if ($divider.length) {
            $divider.before(partiesHtml);
        }
    }

    // Listen for changes to window.parties and refresh dropdown
    const originalPartiesArray = window.parties;
    if (Array.isArray(originalPartiesArray)) {
        new Proxy(originalPartiesArray, {
            set(target, property, value) {
                target[property] = value;
                refreshPartyDropdown();
                return true;
            }
        });
    }

    // Also refresh dropdown after a short delay when parties change
    window.addEventListener('partiesUpdated', function() {
        refreshPartyDropdown();
    });

    // Add row functionality
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

    // Delete row functionality
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

    // Auto-fill price/unit and qty when item is selected
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

        const $qty = $row.find('.item-qty');
        // Always default selected item quantity to 1 when item is chosen
        $qty.val(1);

        $row.find('.item-price').val(price.toFixed(2));
        if (unit) {
            ensureUnitOption($row.find('.item-unit'), unit);
        }

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

    // Payment entry management
    $ctx.on('click', '.add-payment-entry', function(e) {
        e.preventDefault();

        const $defaultAmount = $ctx.find('.default-payment-amount');
        const $defaultReference = $ctx.find('.default-payment-reference');

        // If amount/reference are hidden, show them (this happens on first click)
        if ($defaultAmount.hasClass('d-none') || $defaultReference.hasClass('d-none')) {
            $defaultAmount.removeClass('d-none').focus();
            $defaultReference.removeClass('d-none');
            updatePaymentSummary();
            return;
        }

        // Otherwise, add a new payment row
        const template = document.getElementById('payment-entry-template');
        if (!template) return;

        const clone = template.content.cloneNode(true);
        $ctx.find('.payment-entries').append(clone);

        // Ensure the newly added row is visible and focused for value entry
        const $newEntry = $ctx.find('.payment-entries .payment-entry').last();
        $newEntry.find('.payment-amount').focus();
    });

    // Toast helper
    function showToast(message, isError = false) {
        const toastEl = document.getElementById('sale-toast');
        if (!toastEl) return;

        const toastBody = toastEl.querySelector('.toast-body');
        toastBody.textContent = message;

        toastEl.classList.toggle('text-bg-success', !isError);
        toastEl.classList.toggle('text-bg-danger', isError);

        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    }

    // Update payment summary when default payment type is changed
    $ctx.on('change', '.default-payment-type', function() {
        updatePaymentSummary();
    });

    // Ensure amount and reference inputs are kept visible for all payment rows
    $ctx.on('change', '.payment-type-entry', function() {
        updatePaymentSummary();
    });


    $ctx.on('click', '.remove-payment-entry', function() {
        $(this).closest('.payment-entry').remove();
    });

    // Helper: collect data from form
    function gatherSaleData() {
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

        const data = {
            type: 'estimate',
            party_id: $ctx.find('.party-id').val() || $ctx.find('.party-select').val() || '',
            party_name: $ctx.find('#partyDropdownBtn').text().trim() || $ctx.find('.party-select option:selected').text() || '',
            phone: $ctx.find('.phone-input').val() || '',
            billing_address: $ctx.find('.billing-address').val() || '',
            bill_number: $ctx.find('.bill-number').val() || '',
            invoice_date: $ctx.find('.invoice-date').val() || '',
            due_date: $ctx.find('.invoice-date').val() || '',
            total_qty: parseInt($ctx.find('.total-qty').text() || 0, 10) || 0,
            total_amount: parseFloat($ctx.find('.total-base-amount').text() || 0) || 0,
            discount_pct: parseFloat($ctx.find('.discount-pct').val() || 0) || 0,
            discount_rs: parseFloat($ctx.find('.discount-rs').val() || 0) || 0,
            tax_pct: parseFloat($ctx.find('.tax-select').val() || 0) || 0,
            tax_amount: parseFloat($ctx.find('.tax-amount-display').text() || 0) || 0,
            round_off: parseFloat($ctx.find('.round-off-val').val() || 0) || 0,
            grand_total: parseFloat($ctx.find('.grand-total').val() || 0) || 0,
            description: $ctx.find('.description-input').val() || null,
            image_path: (function() {
                const file = $ctx.find('.image-input')[0]?.files?.[0];
                if (file) return file.name;
                if (window.editSaleData && window.editSaleData.image_path) return window.editSaleData.image_path;
                return null;
            })(),
            items,
            payments: [],
        };

        return data;
    }

    function submitEstimate(btn, options = {}) {
        const saleData = gatherSaleData();
        const idleText = options.idleText || 'Save';
        const loadingText = options.loadingText || 'Saving...';
        const successMessage = options.successMessage || 'Estimate saved successfully! Redirecting...';
        const redirectToShare = Boolean(options.redirectToShare);

        if (!saleData.items.length) {
            alert('Please add at least one item before saving.');
            return;
        }

        btn.prop('disabled', true).text(loadingText);

        fetch(window.saleStoreUrl, {
            method: window.saleMethod || 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify(saleData),
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
                    const message = (data && data.message) ? data.message : 'Server error';
                    throw new Error(message);
                }

                return data;
            })
            .then(data => {
                if (data && data.success) {
                    if (data.bill_number) {
                        $ctx.find('.bill-number').val(data.bill_number);
                    }

                    showToast(successMessage, false);

                    const targetUrl = redirectToShare ? (data.share_url || data.redirect_url) : data.redirect_url;
                    if (targetUrl) {
                        setTimeout(() => {
                            window.location.href = targetUrl;
                        }, 2000);
                    }

                    return;
                }

                console.error(data);
                showToast('Unable to save estimate. See console for details.', true);
            })
            .catch(err => {
                console.error(err);
                showToast('Error saving estimate. ' + (err.message || ''), true);
            })
            .finally(() => {
                btn.prop('disabled', false).text(idleText);
            });
    }

    $ctx.on('click', '.btn-save', function() {
        submitEstimate($(this), {
            idleText: 'Save',
            loadingText: 'Saving...',
            successMessage: 'Estimate saved successfully! Redirecting...',
        });
    });

    $ctx.on('click', '.btn-share-main', function() {
        submitEstimate($(this), {
            redirectToShare: true,
            idleText: 'Share',
            loadingText: 'Saving...',
            successMessage: 'Estimate saved successfully! Opening invoice preview...',
        });
    });

    // Add description/image/document actions
    $ctx.on('click', '.add-description', function() {
        const $pane = $ctx.find('.description-pane');
        $pane.toggleClass('d-none');
        if (!$pane.hasClass('d-none')) {
            $pane.find('.description-input').focus();
        }
    });

    $ctx.on('click', '.add-image', function() {
        const $container = $(this).closest('.invoice-container');
        $container.find('.image-input').trigger('click');
    });

    $ctx.on('click', '.add-document', function() {
        const $container = $(this).closest('.invoice-container');
        $container.find('.document-input').trigger('click');
    });

    // Image preview UI
    function updateImagePreview(file) {
        const $preview = $ctx.find('.image-preview');
        const $img = $preview.find('.image-preview-img');

        if (!file) {
            $preview.addClass('d-none');
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            $img.attr('src', e.target.result);
            $preview.removeClass('d-none');
        };
        reader.readAsDataURL(file);
    }

    $ctx.on('change', '.image-input', function() {
        const file = this.files?.[0];
        updateImagePreview(file);
    });

    $ctx.on('click', '.image-placeholder', function() {
        $ctx.find('.image-input').trigger('click');
    });

    $ctx.on('click', '.replace-image', function() {
        $ctx.find('.image-input').trigger('click');
    });

    $ctx.on('click', '.remove-image', function() {
        $ctx.find('.image-input').val('');
        updateImagePreview(null);
    });

    $ctx.on('change', '.document-input', function() {
        const fileName = this.files?.[0]?.name;
        if (fileName) {
            $ctx.find('.selected-document-name').text('Document: ' + fileName);
        } else {
            $ctx.find('.selected-document-name').text('');
        }
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

        const roundOffEnabled = $ctx.find('.round-off-check').is(':checked');
        let roundOffVal = roundOffEnabled ? (parseFloat($ctx.find('.round-off-val').val()) || 0) : 0;
        let grandTotal = finalBase + roundOffVal;

        $ctx.find('.round-off-val').val(roundOffVal.toFixed(2));
        $ctx.find('.grand-total').val(grandTotal.toFixed(2));

        // Update payment summary (total payments / received / balance) whenever grand total changes
        updatePaymentSummary();
    }

    function updatePaymentSummary() {
        const grandTotal = parseFloat($ctx.find('.grand-total').val() || 0) || 0;

        // Received amount starts from existing sale payments when editing
        let received = 0;
        if (window.editSaleData) {
            received += parseFloat(window.editSaleData.received_amount || 0) || 0;
        }

        // Include the default payment row (first row) as additional payment when editing
        const defaultType = $ctx.find('.default-payment-type').val() || '';
        if (defaultType.startsWith('bank-')) {
            received += parseFloat($ctx.find('.default-payment-amount').val() || 0) || 0;
        }

        // Include additional payment entries
        received += Array.from($ctx.find('.payment-type-entry')).reduce((sum, el) => {
            const rawType = $(el).val() || '';
            const isBank = rawType.startsWith('bank-');
            if (!isBank) return sum;

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

    // Recalculate payment summary when payments change
    $ctx.on('keyup change', '.default-payment-amount, .payment-amount', updatePaymentSummary);

    // Update when payment rows are removed
    $ctx.on('click', '.remove-payment-entry', function() {
        $(this).closest('.payment-entry').remove();
        updatePaymentSummary();
    });

    $ctx.on('change', '.fill-balance-check, .round-off-check', function() {
        setupAdjustmentControls();
        calculateTotals();
    });
    $ctx.on('input change', '.round-off-val', calculateTotals);

    setupAdjustmentControls();
    calculateTotals();
}
