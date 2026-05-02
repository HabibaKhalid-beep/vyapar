function initializeForm(context) {
    const $ctx = $(context);
    const hasCustomPartyDropdown = $ctx.find('.party-id').length > 0;
    const $paidInput = $ctx.find('.received-amount, .advance-amount').first();
    const defaultPaymentDirection = 'payment_in';

    const baseItems = Array.isArray(window.items) ? window.items : [];
    const defaultSaleUnits = [
        { id: 'pcs', name: 'PIECES', short_name: 'PCS' },
        { id: 'box', name: 'BOX', short_name: 'BOX' },
        { id: 'pack', name: 'PACK', short_name: 'PACK' },
        { id: 'set', name: 'SET', short_name: 'SET' },
        { id: 'kg', name: 'KILOGRAMS', short_name: 'KG' },
        { id: 'g', name: 'GRAM', short_name: 'G' },
        { id: 'm', name: 'METER', short_name: 'M' },
        { id: 'ft', name: 'FEET', short_name: 'FT' },
        { id: 'l', name: 'LITER', short_name: 'L' },
        { id: 'ml', name: 'MILLILITER', short_name: 'ML' }
    ];
    window.saleUnits = Array.isArray(window.saleUnits) && window.saleUnits.length ? window.saleUnits : defaultSaleUnits.slice();
    const getItemMeta = (item = {}) => {
        const plainLabel = item.name || "";
        const richLabel = `${plainLabel} | Sale: ${item.sale_price ?? item.price ?? 0} | Stock: ${item.opening_qty ?? 0} | Location: ${item.location ?? ""}`;
        const categoryLabel = item.category_name || (item.category && item.category.name) || item.category || item.category_id || '';
        const itemCode = item.item_code || item.code || '';
        const description = item.description || item.item_description || '';
        const discount = item.discount ?? item.sale_discount ?? 0;
        return { plainLabel, richLabel, categoryLabel, itemCode, description, discount };
    };

    const buildItemOptionsHtml = (items = []) => {
        return items.map(item => {
            const { plainLabel, richLabel, categoryLabel, itemCode, description, discount } = getItemMeta(item);
            return `<option value="${item.id}" data-price="${item.price ?? ""}" data-sale-price="${item.sale_price ?? ""}" data-stock="${item.opening_qty ?? ""}" data-location="${item.location ?? ""}" data-label="${plainLabel}" data-rich-label="${richLabel}" data-unit="${item.unit || ''}" data-category="${categoryLabel}" data-item-code="${itemCode}" data-description="${description}" data-discount="${discount}">${richLabel}</option>`;
        }).join('');
    };

    const getDomItems = () => {
        const domItems = [];
        $ctx.find('.item-name option').each(function () {
            const value = $(this).attr('value');
            if (!value) return;
            domItems.push({
                id: value,
                name: $(this).attr('data-label') || $(this).text().trim(),
                item_code: $(this).attr('data-item-code') || '',
                description: $(this).attr('data-description') || '',
                sale_price: parseFloat($(this).attr('data-sale-price') || $(this).attr('data-price') || 0) || 0,
                purchase_price: parseFloat($(this).attr('data-purchase-price') || 0) || 0,
                opening_qty: parseFloat($(this).attr('data-stock') || 0) || 0,
                location: $(this).attr('data-location') || '',
                unit: $(this).attr('data-unit') || '',
                category_name: $(this).attr('data-category') || ''
            });
        });
        return domItems;
    };

    const dedupeItemsById = (items = []) => {
        const seen = new Set();
        return (items || []).reduce((acc, item) => {
            const id = String(item?.id ?? '');
            if (!id || seen.has(id)) {
                return acc;
            }
            seen.add(id);
            acc.push(item);
            return acc;
        }, []);
    };

    const getSourceItems = () => {
        const latestItems = Array.isArray(window.items) ? window.items : baseItems;
        if (latestItems !== baseItems) {
            baseItems.splice(0, baseItems.length, ...latestItems);
        }

        if (latestItems.length) {
            return latestItems;
        }

        const domItems = getDomItems();
        if (domItems.length) {
            baseItems.splice(0, baseItems.length, ...domItems);
            window.items = domItems;
            return domItems;
        }

        return [];
    };

    const buildItemPickerRowsHtml = (items = []) => {
        if (!items.length) {
            return '<div class="item-picker-empty">No items found</div>';
        }

        return items.map(item => {
            const plainLabel = item.name || '';
            const itemCode = item.item_code || '';
            const salePrice = parseFloat(item.sale_price ?? item.price ?? 0) || 0;
            const purchasePrice = parseFloat(item.purchase_price ?? 0) || 0;
            const stock = parseFloat(item.opening_qty ?? 0) || 0;
            return `
                <div class="item-picker-row item-picker-option" data-id="${item.id}">
                    <div class="item-picker-name">${plainLabel}${itemCode ? `<small>(${itemCode})</small>` : ''}</div>
                    <div>${salePrice.toFixed(2)}</div>
                    <div>${purchasePrice.toFixed(2)}</div>
                    <div class="item-picker-stock ${stock < 0 ? 'neg' : ''}">${stock}</div>
                </div>
            `;
        }).join('');
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const $closeIcon = $('.close-app-icon');
    const selectedImages = [];
    const selectedDocuments = [];
    const $imageFilesList = $ctx.find('.image-files-list');
    const $documentFilesList = $ctx.find('.document-files-list');

    const itemRoutes = Object.assign({
        index: '/dashboard/items',
        store: '/dashboard/items',
        categoryStore: '/dashboard/items/category',
        unitsIndex: '/dashboard/items/units',
        unitsStore: '/dashboard/items/units'
    }, window.itemRoutes || {});
    const $categoryModal = $('#addCategoryModal');
    const $unitModal = $('#addUnitModal');

    function parseJsonSafely(text) {
        try {
            return JSON.parse(text);
        } catch (_) {
            return null;
        }
    }

    function fetchJson(url, options = {}) {
        const headers = Object.assign({
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }, options.headers || {});

        return fetch(url, Object.assign({}, options, { headers }))
            .then(async response => {
                const text = await response.text();
                const data = parseJsonSafely(text);

                if (!response.ok) {
                    const message = data?.message
                        || data?.error
                        || (text && text.trim().startsWith('<!DOCTYPE') ? `Request failed with status ${response.status}. Server returned HTML instead of JSON.` : `Request failed with status ${response.status}.`);
                    throw new Error(message);
                }

                if (data === null) {
                    throw new Error('Server response was not valid JSON.');
                }

                return data;
            });
    }

    const getNormalizedSaleUnits = () => {
        const sourceUnits = Array.isArray(window.saleUnits) && window.saleUnits.length ? window.saleUnits : defaultSaleUnits;
        return sourceUnits.map(unit => {
            const shortName = String(unit.short_name || unit.short || unit.name || '').trim().toUpperCase();
            const name = String(unit.name || shortName || '').trim().toUpperCase();
            return {
                id: unit.id || shortName.toLowerCase(),
                name,
                short_name: shortName || name
            };
        }).filter(unit => unit.short_name);
    };

    const buildUnitOptionsHtml = (selectedUnit = '') => {
        const normalizedSelected = String(selectedUnit || '').trim().toUpperCase();
        const seen = new Set();
        const options = ['<option value="">Select Unit</option>'];

        getNormalizedSaleUnits().forEach(unit => {
            const shortName = unit.short_name;
            if (!shortName || seen.has(shortName)) {
                return;
            }
            seen.add(shortName);
            options.push(`<option value="${shortName}" ${normalizedSelected === shortName ? 'selected' : ''}>${shortName}</option>`);
        });

        if (normalizedSelected && !seen.has(normalizedSelected)) {
            options.push(`<option value="${normalizedSelected}" selected>${normalizedSelected}</option>`);
        }

        return options.join('');
    };

    function syncItemUnitSelects() {
        $ctx.find('.item-unit').each(function() {
            const $select = $(this);
            const currentValue = String($select.val() || '').trim().toUpperCase();
            $select.html(buildUnitOptionsHtml(currentValue));
            if (currentValue) {
                $select.val(currentValue);
            }
        });
    }

    function renderNewItemUnitMenu(selectedUnit = '') {
        const normalizedSelected = String(selectedUnit || '').trim().toUpperCase();
        const units = getNormalizedSaleUnits();
        const itemsHtml = units.map(unit => `
            <li><button class="dropdown-item unit-option ${normalizedSelected === unit.short_name ? 'active' : ''}" type="button" data-unit="${unit.short_name}">${unit.short_name}</button></li>
        `).join('');

        $('#newItemUnitMenu').html(`
            ${itemsHtml}
            <li><hr class="dropdown-divider"></li>
            <li><button class="dropdown-item text-primary fw-semibold" type="button" id="openAddUnitModalBtn">+ Add Unit</button></li>
        `);
    }

    function positionItemPickerPanel($row) {
        const $input = $row.find('.item-picker-input');
        const $panel = $row.find('.item-picker-panel');

        if (!$input.length || !$panel.length || !$panel.hasClass('open')) {
            return;
        }

        const inputRect = $input[0].getBoundingClientRect();
        const viewportPadding = 12;
        const width = Math.max(inputRect.width, 320);
        let left = inputRect.left;

        if (left + width > window.innerWidth - viewportPadding) {
            left = Math.max(viewportPadding, window.innerWidth - width - viewportPadding);
        }

        const top = inputRect.bottom + 4;
        const availableHeight = Math.max(180, window.innerHeight - top - viewportPadding);
        const listMaxHeight = Math.max(120, Math.min(280, availableHeight - 88));

        $panel.css({
            position: 'fixed',
            top: `${top}px`,
            left: `${left}px`,
            right: '',
            width: `${width}px`,
            minWidth: `${width}px`,
            zIndex: 1055,
            display: 'block'
        });

        $panel.find('.item-picker-list').css('max-height', `${listMaxHeight}px`);
    }

    function hideItemPickerPanels() {
        $ctx.find('.item-picker-panel').removeClass('open').css({
            display: 'none',
            top: '',
            left: '',
            right: '',
            width: '',
            minWidth: ''
        });
    }

    function renderItemPicker($row, query = '') {
        const $panel = $row.find('.item-picker-panel');
        const $list = $row.find('.item-picker-list');
        const normalizedQuery = String(query || '').trim().toLowerCase();
        let itemsToUse = getSourceItems();

        if (!itemsToUse.length) {
            const fallbackItems = [];
            $row.find('.item-name option').each(function () {
                const value = $(this).attr('value');
                if (!value) return;
                fallbackItems.push({
                    id: value,
                    name: $(this).data('label') || $(this).text().trim(),
                    item_code: $(this).data('item-code') || '',
                    description: $(this).data('description') || '',
                    sale_price: $(this).data('sale-price') || $(this).data('price') || 0,
                    purchase_price: $(this).data('purchase-price') || 0,
                    opening_qty: $(this).data('stock') || 0,
                    location: $(this).data('location') || '',
                });
            });
            itemsToUse = fallbackItems;
        }

        const filtered = itemsToUse.filter(item => {
            const label = String(item.name || '').toLowerCase();
            const code = String(item.item_code || '').toLowerCase();
            const description = String(item.description || item.item_description || '').toLowerCase();
            return !normalizedQuery ||
                   label.includes(normalizedQuery) ||
                   code.includes(normalizedQuery) ||
                   description.includes(normalizedQuery);
        });

        $list.html(buildItemPickerRowsHtml(filtered));
        $panel.addClass('open').css('display', 'block');
        positionItemPickerPanel($row);
    }

    function syncItemPickerInput($row) {
        const $selected = $row.find('.item-name option:selected');
        const selectedValue = String($selected.val() || '').trim();
        const plainLabel = $selected.data('label') || $selected.text() || '';
        $row.find('.item-picker-input').val(selectedValue ? plainLabel.trim() : '');
    }

    function setupPartyDropdownSearch() {
        const $partySearchInput = $ctx.find('.party-search-input').first();
        const $partyDropdown = $ctx.find('.party-dropdown-wrapper').first();

        if (!$partySearchInput.length) {
            return;
        }

        $partySearchInput.on('click focus', function(e) {
            e.stopPropagation();
        });

        $partySearchInput.on('input', function() {
            const searchTerm = String($(this).val() || '').trim().toLowerCase();
            $ctx.find('.party-option').each(function() {
                const $option = $(this);
                const partyName = String($.trim($option.find('span').first().text() || '')).toLowerCase();
                const partyPhone = String($option.data('phone') || '').toLowerCase();
                const isVisible = !searchTerm || partyName.includes(searchTerm) || partyPhone.includes(searchTerm);
                $option.closest('li').toggleClass('d-none', !isVisible);
            });
        });

        if ($partyDropdown.length) {
            $partyDropdown.on('show.bs.dropdown', function() {
                setTimeout(() => {
                    $partySearchInput.trigger('focus').trigger('select');
                }, 100);
            });
            $partyDropdown.on('hide.bs.dropdown', function() {
                $partySearchInput.val('');
                $ctx.find('.party-option').closest('li').removeClass('d-none');
            });
        }

        $ctx.on('click', '.dropdown-header-search', function(e) {
            e.stopPropagation();
        });

        $ctx.on('click', '.party-search-input', function(e) {
            e.stopPropagation();
        });

        $ctx.on('keydown keyup', '.party-search-input', function(e) {
            e.stopPropagation();
        });
    }

    function refreshItemsList(selectedItem = null) {
        fetchJson(`${itemRoutes.index}?json=1&include_inactive=1`)
        .then(items => {
            const uniqueItems = dedupeItemsById(items);
            baseItems.splice(0, baseItems.length, ...uniqueItems);
            window.items = uniqueItems;
            updateItemSelectOptions();

            if (selectedItem && selectedItem.id) {
                const activeRowIndex = Number(window.activeSaleItemRowIndex || 0);
                const $targetRow = $ctx.find('.item-row').eq(activeRowIndex >= 0 ? activeRowIndex : 0);
                if ($targetRow.length) {
                    $targetRow.find('.item-name').val(String(selectedItem.id)).trigger('change');
                }
            }
        })
        .catch(() => {});
    }

    function refreshUnitsList(selectedUnit = '') {
        return fetchJson(`${itemRoutes.unitsIndex}?json=1`)
        .then(data => {
            const units = Array.isArray(data.units) ? data.units : defaultSaleUnits;
            window.saleUnits = units;
            renderNewItemUnitMenu(selectedUnit);
            syncItemUnitSelects();
            if (selectedUnit) {
                $('#newItemUnit').val(selectedUnit);
                $('#newItemUnitBtn').text(selectedUnit);
            }
            return units;
        })
        .catch(() => {
            renderNewItemUnitMenu(selectedUnit);
            syncItemUnitSelects();
            return getNormalizedSaleUnits();
        });
    }

    if (!window.__saleItemModalMessageBound) {
        window.__saleItemModalMessageBound = true;
        window.addEventListener('message', function(event) {
            if (event.data && event.data.type === 'item-saved') {
                $(document).trigger('sale:item-created', [event.data.item || null]);
            }
        });
    }

    $(document).off('sale:item-created.saleform').on('sale:item-created.saleform', function(_, item) {
        refreshItemsList(item);
        const modalEl = document.getElementById('addItemModal');
        if (modalEl) {
            bootstrap.Modal.getOrCreateInstance(modalEl).hide();
        }
    });

    // IMPORTANT: Set the doc-type field from window.docType
    // This ensures the correct type is captured when form is saved
    $ctx.find('.doc-type').val(window.docType || 'invoice');

    // Auto-fill invoice/order dates
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const todayValue = `${yyyy}-${mm}-${dd}`;
    $ctx.find('.invoice-date').val(todayValue);
    $ctx.find('.order-date').val(todayValue);
    $ctx.find('.due-date').val(todayValue);
    $ctx.find('.due-days-select').val('0');

    // If editing an existing sale, populate the form with saved values
    if (window.editSaleData) {
        populateFormFromSale(window.editSaleData);
    }

    // ========== TOGGLE FIELDS BASED ON DOCUMENT TYPE ==========
    const docType = window.docType || 'invoice';
    const typeLabels = {
        'invoice': 'Sales Invoice',
        'estimate': 'Estimate / Quotation',
        'sale_order': 'Sale Order',
        'proforma': 'Proforma Invoice',
        'delivery_challan': 'Delivery Challan',
        'sale_return': 'Sale Return',
        'pos': 'POS'
    };

    // Set the form title
    const formTitle = $ctx.find('.form-title');
    if (formTitle.length) {
        formTitle.text(typeLabels[docType] || 'Sale');
    }

    const shippingGroup = $ctx.find('.shipping-address-group');
    const orderDateGroup = $ctx.find('.order-date-group');
    const dueDateGroup = $ctx.find('.due-date-group');
    const invoiceDateGroup = $ctx.find('.invoice-date-group');
    const docNumberLabel = $ctx.find('.doc-number-label');
    const docDateLabel = $ctx.find('.doc-date-label');
    const paymentSection = $ctx.find('.payment-section');
    const receivedInput = $paidInput;
    const receivedLabelDiv = $ctx.find('.received-label-text');
    const receivedRow = $ctx.find('.received-row');
    const balanceRow = $ctx.find('.balance-row');

    $ctx.find('.default-payment-direction').val(defaultPaymentDirection);

    function updateDueDateFromSelection() {
        const $dueSelect = $ctx.find('.due-days-select');
        const $customInput = $ctx.find('.due-days-custom');
        const $orderDate = $ctx.find('.order-date');
        const $dueDate = $ctx.find('.due-date');

        if (!$dueSelect.length || !$orderDate.length || !$dueDate.length) return;

        const selectedValue = $dueSelect.val();
        const orderDateValue = $orderDate.val();

        if (selectedValue === 'custom') {
            $customInput.removeClass('d-none').focus();
        } else {
            $customInput.addClass('d-none');
        }

        const days = selectedValue === 'custom'
            ? parseInt($customInput.val() || 0, 10)
            : parseInt(selectedValue || 0, 10);

        if (!orderDateValue) return;

        const baseDate = new Date(orderDateValue);
        if (Number.isNaN(baseDate.getTime())) return;

        const dueDate = new Date(baseDate);
        if (days > 0) {
            dueDate.setDate(dueDate.getDate() + days);
        }

        const yyyy = dueDate.getFullYear();
        const mm = String(dueDate.getMonth() + 1).padStart(2, '0');
        const dd = String(dueDate.getDate()).padStart(2, '0');
        $dueDate.val(`${yyyy}-${mm}-${dd}`);
    }

    function syncDealDaysFromSale(sale) {
        const $dueSelect = $ctx.find('.due-days-select');
        const $customInput = $ctx.find('.due-days-custom');
        if (!$dueSelect.length) return;

        const dealDays = parseInt(sale.deal_days || 0, 10) || 0;
        const predefined = ['0', '5', '10', '15', '30', '45'];

        if (predefined.includes(String(dealDays))) {
            $dueSelect.val(String(dealDays));
            $customInput.addClass('d-none').val('');
        } else {
            $dueSelect.val('custom');
            $customInput.removeClass('d-none').val(dealDays > 0 ? dealDays : '');
        }
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
            const checkboxText = $paidInput.hasClass('advance-amount') ? 'Full Advance' : 'Full Receive';
            $paidInput.closest('.calc-inputs').prepend(
                `<label class="d-flex align-items-center gap-1 me-2 mb-0 text-nowrap" style="font-size:12px;">
                    <input type="checkbox" class="fill-balance-check">
                    <span>${checkboxText}</span>
                </label>`
            );
        }
    }

    function syncDefaultPaymentFields() {
        const hasDefaultPaymentType = Boolean($ctx.find('.default-payment-type').val());
        const $defaultAmount = $ctx.find('.default-payment-amount');
        const $defaultReference = $ctx.find('.default-payment-reference');

        if (!$defaultAmount.length || !$defaultReference.length) {
            return;
        }

        $defaultAmount.toggleClass('d-none', !hasDefaultPaymentType);
        $defaultReference.toggleClass('d-none', !hasDefaultPaymentType);

        if (!hasDefaultPaymentType) {
            $defaultAmount.val('0');
            $defaultReference.val('');
        }
    }

    function updateBrokerageFields() {
        const brokerageType = ($ctx.find('.brokerage-type').val() || '').toString();
        const totalQty = parseFloat($ctx.find('.total-qty').text() || 0) || 0;
        const rawRate = parseFloat($ctx.find('.brokerage-rate').val() || 0) || 0;
        const $brokerageAmount = $ctx.find('.brokerage-amount');
        const $brokerageRate = $ctx.find('.brokerage-rate');
        const $brokerageBaseAmount = $ctx.find('.brokerage-base-amount');

        let amount = 0;
        let placeholder = 'Enter value';

        if (brokerageType === 'per_kg') {
            amount = totalQty * rawRate;
            placeholder = 'Rate per kilo';
        } else if (brokerageType === 'half') {
            amount = rawRate / 2;
            placeholder = 'Full brokerage amount';
        } else if (brokerageType === 'full') {
            amount = rawRate;
            placeholder = 'Full brokerage amount';
        }

        $brokerageRate.attr('placeholder', placeholder);
        $brokerageBaseAmount.val(rawRate.toFixed(2));
        $brokerageAmount.val(amount.toFixed(2));
    }

    function getMarketExpenseTotal() {
        return (parseFloat($ctx.find('.brokerage-amount').val() || 0) || 0)
            + (parseFloat($ctx.find('.bardana-input').val() || 0) || 0)
            + (parseFloat($ctx.find('.labour-input').val() || 0) || 0)
            + (parseFloat($ctx.find('.rehra-mazdori-input').val() || 0) || 0)
            + (parseFloat($ctx.find('.post-expense-input').val() || 0) || 0)
            + (parseFloat($ctx.find('.extra-expense-input').val() || 0) || 0);
    }

    function updateMarketRowAmount($row) {
        if (!$row || !$row.length) {
            return;
        }

        const tadad = parseFloat($row.find('.tadad-input').val() || 0) || 0;
        const qty = parseFloat($row.find('.item-qty').val() || 0) || 0;
        const price = parseFloat($row.find('.item-price').val() || 0) || 0;
        const itemDiscount = parseFloat($row.find('.item-discount').val() || 0) || 0;
        const safiWazan = parseFloat($row.find('.safi-wazan-input').val() || 0) || 0;
        const rate = parseFloat($row.find('.rate-input').val() || 0) || 0;
        const hasMarketValues = safiWazan > 0 && rate > 0;
        const effectiveQty = tadad > 0 ? tadad : qty;

        if (tadad > 0) {
            $row.find('.item-qty').val(tadad);
        }

        let amount = (effectiveQty * price) - itemDiscount;

        if (hasMarketValues) {
            amount = safiWazan * rate;
        }

        $row.find('.item-amount').val(Math.max(amount, 0).toFixed(2));
    }

    if (docType === 'sale_order' || docType === 'delivery_challan') {
        // Show shipping address and dates
        shippingGroup.removeClass('d-none');
        orderDateGroup.removeClass('d-none');
        dueDateGroup.removeClass('d-none');

        // Update labels
        if (docNumberLabel.length) docNumberLabel.text('Order No.');
        if (docDateLabel.length) docDateLabel.text('Order Date');

        // For sale_order: change "Received" to "Advance"
        if (docType === 'sale_order') {
            if (receivedLabelDiv.length) {
                receivedLabelDiv.text('Advance Payment');
            }
            if (receivedInput.length) {
                receivedInput.prop('readonly', true).attr('placeholder', 'Advance amount');
            }
        }
    }
    else if (docType === 'estimate') {
        // Show due date for estimates
        dueDateGroup.removeClass('d-none');

        // Update labels
        if (docNumberLabel.length) docNumberLabel.text('Estimate No.');
        if (docDateLabel.length) docDateLabel.text('Estimate Date');

        // Hide payment section for estimates
        paymentSection.addClass('d-none');
        receivedRow.addClass('d-none');
        balanceRow.addClass('d-none');
    }
    else if (docType === 'proforma') {
        // Update labels for proforma
        if (docNumberLabel.length) docNumberLabel.text('Proforma No.');
    }
    // ========== END TOGGLE FIELDS ==========

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
                $ctx.find('.city-input').val(party.city || '');
                $ctx.find('.ptcl-input').val(party.ptcl_number || party.ptcl || '');
                $ctx.find('.address-input').val(party.address || '');
                $ctx.find('.billing-address').val(party.billing_address || sale.billing_address || '');
                $ctx.find('.shipping-address').val(party.shipping_address || sale.shipping_address || '');
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

        $ctx.find('.phone-input').val(sale.phone || '');
        $ctx.find('.billing-address').val(sale.billing_address || '');
        $ctx.find('.bill-number').val(sale.bill_number || '');
        $ctx.find('.invoice-date').val(sale.invoice_date ? sale.invoice_date.split(' ')[0] : `${yyyy}-${mm}-${dd}`);
        $ctx.find('.order-date').val(sale.order_date ? sale.order_date.split(' ')[0] : `${yyyy}-${mm}-${dd}`);
        syncDealDaysFromSale(sale);
        $ctx.find('.due-date').val(sale.due_date ? sale.due_date.split(' ')[0] : '');

        // Items
        $ctx.find('.item-rows').empty();
        (sale.items || []).forEach(item => {
            addRow();
            const $row = $ctx.find('.item-rows tr').last();
            const matchOption = $row.find('.item-name option').filter(function () {
                return ($(this).data('label') || $(this).text().trim()) === (item.item_name || '');
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
        const $marketRow = $ctx.find('.item-rows tr').first();
        $marketRow.find('.tadad-input').val(sale.tadad || sale.total_qty || '');
        $marketRow.find('.total-wazan-input').val(sale.total_wazan || '');
        $marketRow.find('.safi-wazan-input').val(sale.safi_wazan || '');
        $marketRow.find('.rate-input').val(sale.rate || '');
        $marketRow.find('.deo-input').val(sale.deo || '');
        $marketRow.find('.bardana-input').val(sale.bardana || '');
        $marketRow.find('.labour-input').val(sale.labour || '');
        $marketRow.find('.rehra-mazdori-input').val(sale.rehra_mazdori || '');
        $marketRow.find('.post-expense-input').val(sale.post_expense || '');
        $marketRow.find('.extra-expense-input').val(sale.extra_expense || '');
        updateMarketRowAmount($marketRow);

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
        $ctx.find('.default-payment-direction').val(defaultPaymentDirection);
        $ctx.find('.default-payment-amount').val('0').addClass('d-none');
        $ctx.find('.default-payment-reference').val('').addClass('d-none');
        $ctx.find('.payment-entries').empty();

        (sale.payments || []).forEach((payment, index) => {
            if (index !== 0) return;
            const paymentType = (payment.payment_type || '').toString().toLowerCase();
            $ctx.find('.default-payment-direction').val(defaultPaymentDirection);
            if (paymentType === 'cash') {
                $ctx.find('.default-payment-type').val('cash');
            } else if (payment.bank_account_id) {
                $ctx.find('.default-payment-type').val(`bank-${payment.bank_account_id}`);
            }
        });

        const broker = (window.brokers || []).find(b => String(b.id) === String(sale.broker_id || ''));
        $ctx.find('.broker-id').val(sale.broker_id || '');
        if (broker) {
            $ctx.find('#brokerDropdownBtn').text(broker.name || 'Select Broker');
            $ctx.find('.broker-phone-input').val(broker.phone || '');
        } else {
            $ctx.find('#brokerDropdownBtn').text('Select Broker');
            $ctx.find('.broker-phone-input').val('');
        }

        $ctx.find('.brokerage-type').val(sale.brokerage_type || '');
        const saleBrokerageRate = parseFloat(sale.brokerage_rate ?? sale.broker_amount ?? 0) || 0;
        $ctx.find('.brokerage-rate').val(saleBrokerageRate ? saleBrokerageRate.toFixed(2) : '');
        $ctx.find('.brokerage-base-amount').val(saleBrokerageRate.toFixed(2));
        $ctx.find('.brokerage-amount').val((parseFloat(sale.broker_amount || 0) || 0).toFixed(2));

        syncDefaultPaymentFields();
        updateBrokerageFields();
        updateDueDateFromSelection();

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
            $ctx.find('.party-id').val(party.id || '');
            $ctx.find('.phone-input').val(party.phone || '');
            $ctx.find('.billing-address').val(party.billing_address || '');
        } else {
            $ctx.find('.party-id').val('');
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
        const city = $option.data('city') || '';
        const ptcl = $option.data('ptcl') || '';
        const address = $option.data('address') || '';
        const billing = $option.data('billing') || '';
        const shipping = $option.data('shipping') || '';

        $ctx.find('.party-id').val(partyId);
        $ctx.find('#partyDropdownBtn').text(partyName || 'Select Party');
        $ctx.find('.phone-input').val(phone);
        $ctx.find('.city-input').val(city);
        $ctx.find('.ptcl-input').val(ptcl);
        $ctx.find('.address-input').val(address);
        $ctx.find('.billing-address').val(billing);
        $ctx.find('.shipping-address').val(shipping);

        const dropdownBtn = document.getElementById('partyDropdownBtn');
        if (dropdownBtn) {
            const dropdown = bootstrap.Dropdown.getOrCreateInstance(dropdownBtn);
            if (dropdown) {
                dropdown.hide();
            }
        }
    });

    // Add row functionality
    $ctx.off('click', '.add-row-btn').on('click', '.add-row-btn', function(e) {
        e.preventDefault();
        addRow();
    });

    function addRow() {
        const rowCount = $ctx.find('.item-rows tr').length + 1;
        const isCatVisible = $ctx.find('.check-category').is(':checked');
        const isCodeVisible = $ctx.find('.check-item-code').is(':checked');
        const isDescVisible = $ctx.find('.check-description').is(':checked');
        const isDiscVisible = $ctx.find('.check-discount').is(':checked');
        const optionsHtml = buildItemOptionsHtml(getFilteredItems());
        const unitOptionsHtml = buildUnitOptionsHtml();

        const newRow = `
            <tr class="item-row">
                <td class="row-num">
                    <span class="row-index-text">${rowCount}</span>
                    <div class="delete-row-icon"><i class="fa-solid fa-trash-can"></i></div>
                </td>
                <td>
                    <div class="item-picker">
                        <input type="text" class="item-picker-input" placeholder="Search Item">
                        <div class="item-picker-panel">
                            <div class="item-picker-add"><i class="fa-regular fa-circle-plus"></i> Add Item</div>
                            <div class="item-picker-head">
                                <span>Item</span>
                                <span>Sale Price</span>
                                <span>Purchase Price</span>
                                <span>Stock</span>
                            </div>
                            <div class="item-picker-list"></div>
                        </div>
                        <select class="form-select item-name d-none">
                            <option value="" selected disabled>Select Item</option>
                            ${optionsHtml}
                        </select>
                    </div>
                </td>
                <td class="col-category ${isCatVisible ? '' : 'd-none'}"><input type="text" class="item-category" placeholder="Category"></td>
                <td class="col-item-code ${isCodeVisible ? '' : 'd-none'}"><input type="text" class="item-code" placeholder="Item Code"></td>
                <td class="col-description ${isDescVisible ? '' : 'd-none'}"><input type="text" class="item-desc" placeholder="Description"></td>
                <td class="col-discount ${isDiscVisible ? '' : 'd-none'}"><input type="number" class="item-discount" value="0"></td>
                <td><input type="number" class="item-qty" value="1"></td>
                <td>
                    <select class="item-unit">${unitOptionsHtml}</select>
                </td>
                <td><input type="number" class="item-price" value="0"></td>
                <td class="col-amount"><input type="text" class="item-amount" value="0" readonly></td>
                <td><input type="number" class="item-inline-input tadad-input" value="0" min="0" step="1" placeholder="Tadad"></td>
                <td><input type="number" class="item-inline-input total-wazan-input" value="0" min="0" step="0.01" placeholder="Total Wazan"></td>
                <td><input type="number" class="item-inline-input safi-wazan-input" value="0" min="0" step="0.01" placeholder="Safi Wazan"></td>
                <td><input type="number" class="item-inline-input rate-input" value="0" min="0" step="0.01" placeholder="Rate"></td>
                <td><input type="number" class="item-inline-input deo-input" value="0" min="0" step="0.01" placeholder="Deo"></td>
                <td><input type="number" class="item-inline-input bardana-input" value="0" min="0" step="0.01" placeholder="Bardana"></td>
                <td><input type="number" class="item-inline-input labour-input" value="0" min="0" step="0.01" placeholder="Mazdori"></td>
                <td><input type="number" class="item-inline-input rehra-mazdori-input" value="0" min="0" step="0.01" placeholder="Rehra Mazdori"></td>
                <td><input type="number" class="item-inline-input post-expense-input" value="0" min="0" step="0.01" placeholder="Dak Karaya"></td>
                <td><input type="number" class="item-inline-input extra-expense-input" value="0" min="0" step="0.01" placeholder="Local"></td>
                <td class="add-col"></td>
            </tr>
        `;
        $ctx.find('.item-rows').append(newRow);
    }

    function applyColumnVisibility() {
        const isCatVisible = $('.check-category').is(':checked');
        const isCodeVisible = $('.check-item-code').is(':checked');
        const isDescVisible = $('.check-description').is(':checked');
        const isDiscVisible = $('.check-discount').is(':checked');

        $ctx.find('.col-category').toggleClass('d-none', !isCatVisible);
        $ctx.find('.col-item-code').toggleClass('d-none', !isCodeVisible);
        $ctx.find('.col-description').toggleClass('d-none', !isDescVisible);
        $ctx.find('.col-discount').toggleClass('d-none', !isDiscVisible);
    }

    function getFilteredItems() {
        const $modal = $('#itemColumnModal');
        const category = ($modal.find('.item-filter-category').val() || '').toString().trim().toLowerCase();
        const code = ($modal.find('.item-filter-code').val() || '').toString().trim().toLowerCase();
        const description = ($modal.find('.item-filter-description').val() || '').toString().trim().toLowerCase();
        const discountFilter = ($modal.find('.item-filter-discount').val() || '').toString().trim();

        return getSourceItems().filter(item => {
            const meta = getItemMeta(item);
            const categoryValue = String(meta.categoryLabel || '').toLowerCase();
            const codeValue = String(meta.itemCode || '').toLowerCase();
            const descValue = String(meta.description || '').toLowerCase();
            const discountValue = parseFloat(meta.discount || 0) || 0;

            if (category && categoryValue !== category) return false;
            if (code && !codeValue.includes(code)) return false;
            if (description && !descValue.includes(description)) return false;
            if (discountFilter === 'has' && discountValue <= 0) return false;
            if (discountFilter === 'none' && discountValue > 0) return false;
            return true;
        });
    }

    function updateItemSelectOptions() {
        const filteredItems = getFilteredItems();
        const optionsHtml = buildItemOptionsHtml(filteredItems);
        const sourceItems = getSourceItems();

        $ctx.find('.item-name').each(function () {
            const $select = $(this);
            const currentValue = $select.val();

            if (sourceItems.length) {
                $select.empty();
                $select.append('<option value="" selected disabled>Select Item</option>');
                $select.append(optionsHtml);
                if (currentValue) {
                    $select.val(currentValue);
                }
            }

            syncItemPickerInput($select.closest('tr'));
        });
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

        let $option = $unitSelect.find('option').filter(function() {
            return ($(this).val() || $(this).text()).toString().trim() === normalizedUnit;
        }).first();

        if (!$option.length) {
            $option = $('<option></option>').val(normalizedUnit).text(normalizedUnit);
            $unitSelect.append($option);
        }

        $unitSelect.find('option').prop('selected', false);
        $option.prop('selected', true);
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
        const category = $selected.data('category') || '';
        const itemCode = $selected.data('item-code') || '';
        const description = $selected.data('description') || '';
        const discount = $selected.data('discount');

        const $qty = $row.find('.item-qty');
        // Always default selected item quantity to 1 when item is chosen
        $qty.val(1);

        $row.find('.item-price').val(price.toFixed(2));
        $row.find('.item-category').val(category);
        $row.find('.item-code').val(itemCode);
        $row.find('.item-desc').val(description);
        if (discount !== undefined && discount !== null && discount !== '') {
            const currentDiscount = parseFloat($row.find('.item-discount').val() || 0) || 0;
            if (currentDiscount === 0) {
                $row.find('.item-discount').val(discount);
            }
        }
        if (unit) {
            ensureUnitOption($row.find('.item-unit'), unit);
        } else {
            $row.find('.item-unit').val('');
        }

        syncItemPickerInput($row);
        $row.find('.item-picker-panel').removeClass('open');
        $row.find('.item-amount').val(price.toFixed(2));
        updateMarketRowAmount($row);
        calculateTotals();
    });

    const loadItemsIfNeeded = ($row, query) => {
        if (getSourceItems().length) {
            return false;
        }

        refreshItemsList().then(() => {
            renderItemPicker($row, query);
        }).catch(() => {
            renderItemPicker($row, query);
        });

        return true;
    };

    $ctx.on('focus click', '.item-picker-input', function() {
        const $row = $(this).closest('tr');
        const rawQuery = String($(this).val() || '').trim();
        const query = rawQuery.toLowerCase() === 'select item' ? '' : rawQuery;
        if (loadItemsIfNeeded($row, query)) {
            return;
        }
        renderItemPicker($row, query);
    });

    $ctx.on('input', '.item-picker-input', function() {
        const $row = $(this).closest('tr');
        const rawQuery = String($(this).val() || '').trim();
        const query = rawQuery.toLowerCase() === 'select item' ? '' : rawQuery;
        if (loadItemsIfNeeded($row, query)) {
            return;
        }
        renderItemPicker($row, query);
    });

    $ctx.on('click', '.item-picker-option', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $row = $(this).closest('tr');
        const itemId = String($(this).data('id') || '');
        $row.find('.item-name').val(itemId).trigger('change');
    });

    $(document).off('click', '.unit-option').on('click', '.unit-option', function(e) {
        e.preventDefault();
        const unit = $(this).data('unit') || $(this).text().trim();
        $('#newItemUnitBtn').text(unit);
        $('#newItemUnit').val(unit);
    });

    $(document).on('click', '#assignItemCodeBtn', function(e) {
        e.preventDefault();
        const itemName = $('#newItemName').val().trim();
        if (!itemName) {
            return;
        }
        const code = itemName.toUpperCase().replace(/\s+/g, '-').replace(/[^A-Z0-9-_]/g, '').substring(0, 50);
        $('#newItemCode').val(code);
    });

    $ctx.on('click', '.item-picker-add', function() {
        const $row = $(this).closest('tr');
        window.activeSaleItemRowIndex = $ctx.find('.item-row').index($row);

        // Close all item picker dropdowns when opening modal
        hideItemPickerPanels();

        // Clear form
        document.getElementById('addItemForm').reset();
        $('#newItemUnitBtn').text('Select Unit');
        $('#newItemUnit').val('');
        renderNewItemUnitMenu();

        // Show modal
        const modalEl = document.getElementById('addItemModal');
        if (modalEl) {
            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }
    });

    $(document).on('click.saleItemPicker', function(e) {
        // Don't close dropdowns if modal is open
        if ($('#addItemModal').hasClass('show')) {
            return;
        }
        if (!$(e.target).closest('.item-picker').length) {
            hideItemPickerPanels();
        }
    });

    // Prevent dropdown from closing when clicking inside panel or input
    $ctx.on('click', '.item-picker-input, .item-picker-panel', function(e) {
        e.stopPropagation();
    });

    // Handle modal events to control item dropdown behavior
    $('#addItemModal').on('show.bs.modal', function() {
        // Close all item picker dropdowns when modal opens
        hideItemPickerPanels();
        refreshUnitsList($('#newItemUnit').val() || '');
        const pricingTabEl = document.getElementById('pricing-tab');
        const stockTabEl = document.getElementById('stock-tab');
        const pricingTabPane = document.getElementById('pricing-tab-pane');
        const stockTabPaneEl = document.getElementById('stock-tab-pane');

        if (pricingTabEl && pricingTabPane) {
            const pricingTab = bootstrap.Tab.getOrCreateInstance(pricingTabEl);
            pricingTab.show();
            $(pricingTabEl).attr('aria-selected', 'true');
            $(pricingTabPane).addClass('active show').show();
        }
        if (stockTabEl && stockTabPaneEl) {
            $(stockTabEl).removeClass('active').attr('aria-selected', 'false');
            $(stockTabPaneEl).removeClass('active show').show();
        }
    });

    $('#addItemModal').on('hidden.bs.modal', function() {
        // Clear form when modal is closed
        document.getElementById('addItemForm').reset();
        $('#newItemUnitBtn').text('Select Unit');
        $('#newItemUnit').val('');
        $('#newItemCategory').val('');
        $('#newItemType').val('product');
        $('#newItemTypeToggle').prop('checked', false);
        $('#newItemProductLabel').text('Product');
        $('#newItemNameLabel').text('Item Name *');
        $('#stock-tab').show().removeClass('active').attr('aria-selected', 'false');
        $('#pricing-tab').addClass('active').attr('aria-selected', 'true');
        $('#stock-tab-pane').removeClass('active show').show();
        $('#pricing-tab-pane').addClass('active show').show();
        $('#purchase-sec').show();
        const thumb = document.getElementById('newItemImageThumb');
        const label = document.getElementById('newItemImageLabel');
        if (thumb) {
            thumb.innerHTML = '<i class="fa-regular fa-image fa-2x text-secondary"></i>';
            thumb.style.border = '1.5px solid #93c5fd';
        }
        if (label) {
            label.textContent = 'Click to choose image';
        }
    });

    $('#newItemCategory').on('change', function() {
        if ($(this).val() !== '__add_new__') {
            return;
        }

        $(this).val('');
        $('#quickCategoryName').val('');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addCategoryModal')).show();
        setTimeout(() => $('#quickCategoryName').trigger('focus'), 150);
    });

    $(document).on('click', '#openAddUnitModalBtn', function(e) {
        e.preventDefault();
        const dropdownEl = document.getElementById('newItemUnitBtn');
        const dropdown = dropdownEl ? bootstrap.Dropdown.getOrCreateInstance(dropdownEl) : null;
        dropdown?.hide();
        $('#quickUnitName').val('');
        $('#quickUnitShortName').val('');
        bootstrap.Modal.getOrCreateInstance(document.getElementById('addUnitModal')).show();
        setTimeout(() => $('#quickUnitName').trigger('focus'), 150);
    });

    $(document).off('click', '#saveQuickCategoryBtn').on('click', '#saveQuickCategoryBtn', function() {
        const name = $('#quickCategoryName').val().trim();
        if (!name) {
            alert('Please enter a category name');
            return;
        }

        fetchJson(itemRoutes.categoryStore, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ name })
        })
        .then(data => {
            if (!data.category) {
                throw new Error('Category not returned');
            }

            const category = data.category;
            const $categorySelect = $('#newItemCategory');
            const $existing = $categorySelect.find(`option[value="${category.id}"]`);
            if (!$existing.length) {
                $categorySelect.find('option[value="__add_new__"]').before(
                    `<option value="${category.id}">${category.name}</option>`
                );
            }
            $categorySelect.val(String(category.id));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('addCategoryModal')).hide();
        })
        .catch(error => {
            console.error(error);
            alert(error.message || 'Error saving category');
        });
    });

    $(document).off('click', '#saveQuickUnitBtn').on('click', '#saveQuickUnitBtn', function() {
        const name = $('#quickUnitName').val().trim();
        const shortName = $('#quickUnitShortName').val().trim().toUpperCase();

        if (!name || !shortName) {
            alert('Please enter both unit name and short name');
            return;
        }

        fetchJson(itemRoutes.unitsStore, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify({ name, short_name: shortName })
        })
        .then(data => {
            const unitCode = String(data.unit?.short_name || shortName).toUpperCase();
            window.saleUnits = Array.isArray(data.units) ? data.units : getNormalizedSaleUnits();
            renderNewItemUnitMenu(unitCode);
            syncItemUnitSelects();
            $('#newItemUnit').val(unitCode);
            $('#newItemUnitBtn').text(unitCode);
            bootstrap.Modal.getOrCreateInstance(document.getElementById('addUnitModal')).hide();
        })
        .catch(error => {
            console.error(error);
            alert(error.message || 'Error saving unit');
        });
    });

    // Handle saving new item
    $(document).off('click', '#saveNewItemBtn').on('click', '#saveNewItemBtn', function() {
        const itemName = document.getElementById('newItemName').value.trim();
        if (!itemName) {
            alert('Please enter an item name');
            return;
        }

        const formData = new FormData();
        formData.append('name', itemName);
        formData.append('category_id', document.getElementById('newItemCategory').value);
        formData.append('unit', document.getElementById('newItemUnit').value);
        const newItemType = document.getElementById('newItemType')?.value || 'product';
        formData.append('item_type', newItemType);
        formData.append('type', newItemType);
        formData.append('sale_price', document.getElementById('newItemSalePrice').value || 0);
        formData.append('purchase_price', document.getElementById('newItemPurchasePrice').value || 0);
        formData.append('wholesale_price', document.getElementById('newItemWholesalePrice').value || 0);
        formData.append('wholesale_min_qty', document.getElementById('newItemWholesaleMinQty').value || 0);
        formData.append('item_code', document.getElementById('newItemCode').value);
        formData.append('opening_qty', document.getElementById('newItemStock').value || 0);
        formData.append('at_price', document.getElementById('newItemAtPrice').value || 0);
        formData.append('as_of_date', document.getElementById('newItemAsOfDate').value);
        formData.append('min_stock', document.getElementById('newItemMinStock').value || 0);
        formData.append('location', document.getElementById('newItemLocation').value);
        formData.append('description', document.getElementById('newItemDescription').value);

        // Add image if selected
        const imageInput = document.getElementById('newItemImage');
        if (imageInput && imageInput.files.length > 0) {
            formData.append('item_image', imageInput.files[0]);
        }

        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        fetchJson(itemRoutes.store, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            body: formData
        })
        .then(data => {
            if (data.item) {
                // Refresh the item picker from the server so the list stays consistent.
                refreshItemsList(data.item);

                // Close modal
                const modalEl = document.getElementById('addItemModal');
                bootstrap.Modal.getOrCreateInstance(modalEl).hide();

                // Add the new item to the current row if applicable
                if (window.activeSaleItemRowIndex !== undefined) {
                    const $targetRow = $ctx.find('.item-row').eq(window.activeSaleItemRowIndex);
                    $targetRow.find('.item-name').val(data.item.id).trigger('change');
                }

                alert('Item saved successfully!');
            } else {
                alert('Error saving item');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(error.message || 'Error saving item');
        });
    });

    $(window).on('resize scroll', function() {
        $ctx.find('.item-row').each(function() {
            positionItemPickerPanel($(this));
        });
    });

    syncItemUnitSelects();
    renderNewItemUnitMenu();
    refreshUnitsList();
    refreshItemsList();

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

    $ctx.on('input change', '.tadad-input, .total-wazan-input, .safi-wazan-input, .rate-input, .deo-input, .bardana-input, .labour-input, .rehra-mazdori-input, .post-expense-input, .extra-expense-input', function() {
        const $row = $(this).closest('tr');
        updateMarketRowAmount($row);
        calculateTotals();
    });

    // Payment entry management
    $ctx.off('click', '.add-payment-entry').on('click', '.add-payment-entry', function(e) {
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
        $newEntry.find('.payment-direction-entry').val(defaultPaymentDirection);
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

    function validateSaleBeforeSubmit(saleData) {
        if (!saleData.party_id) {
            showToast('Please select party before saving.', true);
            return false;
        }

        const invalidItemRow = Array.from($ctx.find('.item-row')).find(row => {
            const $row = $(row);
            const hasContent = Boolean(
                $row.find('.item-name').val() ||
                parseFloat($row.find('.item-qty').val() || 0) > 0 ||
                parseFloat($row.find('.item-price').val() || 0) > 0 ||
                parseFloat($row.find('.item-amount').val() || 0) > 0
            );

            return hasContent && !$row.find('.item-name').val();
        });

        if (invalidItemRow || !saleData.items.length) {
            showToast('Please select at least one item before saving.', true);
            return false;
        }

        if (saleData.type !== 'estimate') {
            const defaultPaymentType = $ctx.find('.default-payment-type').val() || '';
            const additionalPaymentTypes = Array.from($ctx.find('.payment-type-entry')).filter(entry => $(entry).val()).length;

            if (!defaultPaymentType && additionalPaymentTypes === 0) {
                showToast('Please select payment type before saving.', true);
                return false;
            }

            if (defaultPaymentType) {
                const defaultAmount = parseFloat($ctx.find('.default-payment-amount').val() || 0) || 0;
                if (defaultAmount <= 0) {
                    showToast('Please enter amount for selected payment type.', true);
                    return false;
                }
            }

            const invalidPaymentEntry = Array.from($ctx.find('.payment-entry')).find(entry => {
                const $entry = $(entry);
                const type = $entry.find('.payment-type-entry').val() || '';
                const amount = parseFloat($entry.find('.payment-amount').val() || 0) || 0;
                return type && amount <= 0;
            });

            if (invalidPaymentEntry) {
                showToast('Please enter amount for each selected payment type.', true);
                return false;
            }
        }

        return true;
    }

    // Update payment summary when default payment type is changed
    $ctx.on('change', '.default-payment-type', function() {
        syncDefaultPaymentFields();
        updatePaymentSummary();
    });

    // Ensure amount and reference inputs are kept visible for all payment rows
    $ctx.off('change', '.payment-type-entry').on('change', '.payment-type-entry', function() {
        updatePaymentSummary();
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

        const payments = [];
        const $marketRow = $ctx.find('.item-row').first();

        // Default payment type (amount + reference shown when selected)
        const defaultTypeVal = $ctx.find('.default-payment-type').val();
        if (defaultTypeVal) {
            const isCash = defaultTypeVal === 'cash';
            const bankId = isCash ? null : parseInt(defaultTypeVal.replace('bank-', ''), 10);
            const bank = !isCash ? (window.bankAccounts || []).find(b => b.id === bankId) : null;
            const defaultAmount = parseFloat($ctx.find('.default-payment-amount').val() || 0) || 0;
            const defaultReference = $ctx.find('.default-payment-reference').val() || null;
            const defaultDirection = $ctx.find('.default-payment-direction').val() || 'payment_in';

            if (defaultAmount > 0) {
                payments.push({
                    direction: defaultDirection,
                    payment_type: isCash ? 'cash' : (bank?.display_with_account || bank?.display_name || 'Bank'),
                    bank_account_id: bankId || null,
                    amount: defaultAmount,
                    reference: defaultReference,
                });
            }
        }

        // Additional payment rows (with amount)
        Array.from($ctx.find('.payment-entry')).forEach(entry => {
            const $entry = $(entry);
            const rawType = $entry.find('.payment-type-entry').val() || '';
            const isBank = rawType.startsWith('bank-');
            const isCash = rawType === 'cash';
            const bankId = isBank ? rawType.replace('bank-', '') : null;
            const bank = isBank ? (window.bankAccounts || []).find(b => String(b.id) === String(bankId)) : null;

            const amount = parseFloat($entry.find('.payment-amount').val() || 0) || 0;
            const reference = $entry.find('.payment-reference').val() || null;
            const direction = $entry.find('.payment-direction-entry').val() || 'payment_in';
            if (!rawType || amount <= 0) return;

            payments.push({
                direction,
                payment_type: isCash ? 'cash' : (isBank ? (bank?.display_with_account || bank?.display_name || 'Bank') : rawType),
                bank_account_id: bankId,
                amount: amount,
                reference: reference,
            });
        });

        return {
            type: $ctx.find('.doc-type').val() || 'invoice',
            source_estimate_id: window.sourceEstimateId || window.editSaleData?.source_estimate_id || null,
            source_sale_order_id: window.sourceSaleOrderId || window.editSaleData?.source_sale_order_id || null,
            source_challan_id: window.sourceChallanId || window.editSaleData?.source_challan_id || null,
            source_proforma_id: window.sourceProformaId || window.editSaleData?.source_proforma_id || null,
            party_id: $ctx.find('.party-id').val() || $ctx.find('.party-select').val() || null,
            broker_id: $ctx.find('.broker-id').val() || null,
            brokerage_type: $ctx.find('.brokerage-type').val() || null,
            brokerage_rate: parseFloat($ctx.find('.brokerage-base-amount').val() || $ctx.find('.brokerage-rate').val() || 0) || 0,
            broker_amount: parseFloat($ctx.find('.brokerage-amount').val() || 0) || 0,
            party_name: $ctx.find('#partyDropdownBtn').text().trim() || $ctx.find('.party-select option:selected').text() || '',
            phone: $ctx.find('.phone-input').val() || '',
            billing_address: $ctx.find('.billing-address').val() || '',
            shipping_address: $ctx.find('.shipping-address').val() || '',
            bill_number: $ctx.find('.bill-number').val() || '',
            invoice_date: $ctx.find('.invoice-date').val() || '',
            order_date: $ctx.find('.order-date').val() || '',
            deal_days: (function() {
                const selectedValue = $ctx.find('.due-days-select').val();
                if (selectedValue === 'custom') {
                    return parseInt($ctx.find('.due-days-custom').val() || 0, 10) || 0;
                }
                return parseInt(selectedValue || 0, 10) || 0;
            })(),
            due_date: $ctx.find('.due-date').val() || '',
            tadad: parseInt($marketRow.find('.tadad-input').val() || 0, 10) || 0,
            total_wazan: parseFloat($marketRow.find('.total-wazan-input').val() || 0) || 0,
            safi_wazan: parseFloat($marketRow.find('.safi-wazan-input').val() || 0) || 0,
            rate: parseFloat($marketRow.find('.rate-input').val() || 0) || 0,
            deo: parseFloat($marketRow.find('.deo-input').val() || 0) || 0,
            total_qty: parseInt($marketRow.find('.tadad-input').val() || $ctx.find('.total-qty').text() || 0, 10) || 0,
            total_amount: parseFloat($ctx.find('.total-base-amount').text() || 0) || 0,
            labour: parseFloat($marketRow.find('.labour-input').val() || 0) || 0,
            bardana: parseFloat($marketRow.find('.bardana-input').val() || 0) || 0,
            rehra_mazdori: parseFloat($marketRow.find('.rehra-mazdori-input').val() || 0) || 0,
            post_expense: parseFloat($marketRow.find('.post-expense-input').val() || 0) || 0,
            extra_expense: parseFloat($marketRow.find('.extra-expense-input').val() || 0) || 0,
            discount_pct: parseFloat($ctx.find('.discount-pct').val() || 0) || 0,
            discount_rs: parseFloat($ctx.find('.discount-rs').val() || 0) || 0,
            tax_pct: parseFloat($ctx.find('.tax-select').val() || 0) || 0,
            tax_amount: parseFloat($ctx.find('.tax-amount-display').text() || 0) || 0,
            round_off: parseFloat($ctx.find('.round-off-val').val() || 0) || 0,
            grand_total: parseFloat($ctx.find('.grand-total').val() || 0) || 0,
            description: $ctx.find('.description-input').val() || null,
            image_path: selectedImages.length ? selectedImages[0].name : (window.editSaleData?.image_path || null),
            image_paths: selectedImages.map(file => file.name),
            document_path: selectedDocuments.length ? selectedDocuments[0].name : (window.editSaleData?.document_path || null),
            document_paths: selectedDocuments.map(file => file.name),
            items,
            payments,
        };
    }

    function submitSale(btn, options = {}) {
        const saleData = gatherSaleData();
        const redirectToShare = Boolean(options.redirectToShare);
        const idleText = options.idleText || 'Save';
        const loadingText = options.loadingText || 'Saving...';
        const successMessage = options.successMessage || 'Sale saved successfully! Redirecting...';

        if (!saleData.items.length) {
            alert('Please add at least one item before saving.');
            return;
        }

        if (!validateSaleBeforeSubmit(saleData)) {
            return;
        }

        btn.prop('disabled', true).text(loadingText);

        const hasUploadFiles = selectedImages.length > 0 || selectedDocuments.length > 0;
        let requestBody;
        const requestHeaders = {
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
        };

        if (hasUploadFiles) {
            const formData = new FormData();
            Object.entries(saleData).forEach(([key, value]) => {
                if (value === undefined || value === null) {
                    return;
                }
                if (typeof value === 'object') {
                    formData.append(key, JSON.stringify(value));
                    return;
                }
                formData.append(key, value);
            });
            selectedImages.forEach(imageFile => formData.append('images[]', imageFile));
            selectedDocuments.forEach(docFile => formData.append('documents[]', docFile));
            requestBody = formData;
        } else {
            requestHeaders['Content-Type'] = 'application/json';
            requestBody = JSON.stringify(saleData);
        }

        fetch(window.saleStoreUrl, {
            method: window.saleMethod || 'POST',
            headers: requestHeaders,
            body: requestBody,
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
                showToast('Unable to save sale. See console for details.', true);
            })
            .catch(err => {
                console.error(err);
                showToast('Error saving sale. ' + (err.message || ''), true);
            })
            .finally(() => {
                btn.prop('disabled', false).text(idleText);
            });
    }

    // Save button
    $ctx.on('click', '.btn-save', function() {
        submitSale($(this), {
            redirectToShare: true,
            idleText: 'Save',
            loadingText: 'Saving...',
            successMessage: 'Sale saved successfully! Opening invoice preview...',
        });
    });

    // Save & Share button
    $ctx.on('click', '.btn-share-main', function() {
        submitSale($(this), {
            redirectToShare: true,
            idleText: 'Save & Share',
            loadingText: 'Saving & Sharing...',
            successMessage: 'Sale saved successfully! Opening invoice preview...',
        });
    });

    $closeIcon.off('click.saleClose').on('click.saleClose', function () {
        if (window.history.length > 1) {
            window.history.back();
        } else {
            window.location.href = '/dashboard/sales';
        }
    });

    // Add description/image/document actions
    $ctx.off('click', '.add-description').on('click', '.add-description', function(e) {
        e.preventDefault();
        e.stopPropagation();
        const $button = $(this);
        const $pane = $button.closest('.bottom-left, .invoice-container, body').find('.description-pane').first();
        if (!$pane.length) {
            return;
        }
        $pane.toggleClass('d-none');
        if (!$pane.hasClass('d-none')) {
            $pane.find('.description-input').focus();
        }
    });

    $ctx.off('click', '.add-image').on('click', '.add-image', function() {
        const $container = $(this).closest('.invoice-container');
        $container.find('.image-input').trigger('click');
    });

    $ctx.off('click', '.add-document').on('click', '.add-document', function() {
        const $container = $(this).closest('.invoice-container');
        $container.find('.document-input').trigger('click');
    });

    function renderSelectedImages() {
        if (!selectedImages.length) {
            $imageFilesList.empty();
            return;
        }

        const html = selectedImages.map((file, index) => {
            const url = URL.createObjectURL(file);
            return `
                <div class="image-file-card position-relative border rounded overflow-hidden" data-index="${index}">
                    <button type="button" class="btn-close position-absolute end-0 top-0 m-1 remove-selected-image" aria-label="Remove" data-index="${index}"></button>
                    <img src="${url}" alt="${file.name}" class="img-fluid" style="width:120px;height:120px;object-fit:cover;" />
                    <div class="small text-truncate p-1 text-center" style="max-width:120px;">${file.name}</div>
                </div>
            `;
        }).join('');

        $imageFilesList.html(html);
    }

    function renderSelectedDocuments() {
        if (!selectedDocuments.length) {
            $documentFilesList.empty();
            return;
        }

        const html = selectedDocuments.map((file, index) => {
            return `
                <div class="list-group-item d-flex justify-content-between align-items-center" data-index="${index}">
                    <span class="text-truncate" style="max-width: calc(100% - 32px);">${file.name}</span>
                    <button type="button" class="btn-close remove-selected-document" aria-label="Remove" data-index="${index}"></button>
                </div>
            `;
        }).join('');

        $documentFilesList.html(html);
    }

    function addSelectedImages(files) {
        Array.from(files || []).forEach(file => {
            const duplicate = selectedImages.some(existing => existing.name === file.name && existing.size === file.size && existing.type === file.type);
            if (!duplicate) {
                selectedImages.push(file);
            }
        });
        renderSelectedImages();
    }

    function addSelectedDocuments(files) {
        Array.from(files || []).forEach(file => {
            const duplicate = selectedDocuments.some(existing => existing.name === file.name && existing.size === file.size && existing.type === file.type);
            if (!duplicate) {
                selectedDocuments.push(file);
            }
        });
        renderSelectedDocuments();
    }

    $ctx.on('change', '.image-input', function() {
        addSelectedImages(this.files);
        this.value = '';
    });

    $ctx.on('change', '.document-input', function() {
        addSelectedDocuments(this.files);
        this.value = '';
    });

    $ctx.on('click', '.image-placeholder', function() {
        $ctx.find('.image-input').trigger('click');
    });

    $ctx.on('click', '.replace-image', function() {
        $ctx.find('.image-input').trigger('click');
    });

    $ctx.on('click', '.remove-selected-image', function() {
        const index = Number($(this).data('index'));
        selectedImages.splice(index, 1);
        renderSelectedImages();
    });

    $ctx.on('click', '.remove-selected-document', function() {
        const index = Number($(this).data('index'));
        selectedDocuments.splice(index, 1);
        renderSelectedDocuments();
    });

    function calculateTotals() {
        let totalQty = 0;
        let totalBaseAmount = 0;

        $ctx.find('.item-row').each(function() {
            const $row = $(this);
            const tadad = parseFloat($row.find('.tadad-input').val() || 0) || 0;
            const safiWazan = parseFloat($row.find('.safi-wazan-input').val() || 0) || 0;
            const rate = parseFloat($row.find('.rate-input').val() || 0) || 0;
            const qty = parseFloat($row.find('.item-qty').val() || 0) || 0;
            const price = parseFloat($row.find('.item-price').val() || 0) || 0;
            const itemDiscount = parseFloat($row.find('.item-discount').val() || 0) || 0;
            const effectiveQty = tadad > 0 ? tadad : qty;

            let rowAmount = (effectiveQty * price) - itemDiscount;

            if (tadad > 0) {
                $row.find('.item-qty').val(tadad);
                totalQty += tadad;
            } else {
                totalQty += qty;
            }

            if (safiWazan > 0 && rate > 0) {
                rowAmount = safiWazan * rate;
            }

            $row.find('.item-amount').val(rowAmount.toFixed(2));
            totalBaseAmount += rowAmount;
        });

        $ctx.find('.total-qty').text(totalQty);
        $ctx.find('.total-base-amount').text(totalBaseAmount.toFixed(2));

        updateBrokerageFields();
        applyDiscountTax(totalBaseAmount);
    }

    // Discount and Tax logic
    $ctx.on('keyup change', '.discount-pct, .discount-rs, .tax-select, .round-off-check', function() {
        calculateTotals();
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
        if (defaultType.startsWith('bank-') || defaultType === 'cash') {
            received += parseFloat($ctx.find('.default-payment-amount').val() || 0) || 0;
        }

        // Include additional payment entries
        received += Array.from($ctx.find('.payment-type-entry')).reduce((sum, el) => {
            const rawType = $(el).val() || '';
            const isBank = rawType.startsWith('bank-');
            const isCash = rawType === 'cash';
            if (!isBank && !isCash) return sum;

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
    $ctx.on('change', '.default-payment-direction, .payment-direction-entry', updatePaymentSummary);
    $ctx.on('change input', '.brokerage-type, .brokerage-rate', function() {
        updateBrokerageFields();
        calculateTotals();
    });
    $ctx.on('change', '.fill-balance-check, .round-off-check', function() {
        setupAdjustmentControls();
        calculateTotals();
    });
    $ctx.on('input change', '.round-off-val', calculateTotals);

    // Update when payment rows are removed
    $ctx.off('click', '.remove-payment-entry').on('click', '.remove-payment-entry', function() {
        $(this).closest('.payment-entry').remove();
        updatePaymentSummary();
    });

    setupAdjustmentControls();
    syncDefaultPaymentFields();
    setupPartyDropdownSearch();
    applyColumnVisibility();
    updateItemSelectOptions();
    if (!getSourceItems().length) {
        refreshItemsList();
    }
    calculateTotals();
    updateBrokerageFields();

    $(document).on('change', '.check-category, .check-item-code, .check-description, .check-discount', function() {
        applyColumnVisibility();
    });

    $ctx.on('change', '.due-days-select', updateDueDateFromSelection);
    $ctx.on('input', '.due-days-custom', updateDueDateFromSelection);
    $ctx.on('change', '.invoice-date', function() {
        const invoiceDateValue = $(this).val();
        if (!$ctx.find('.order-date').val()) {
            $ctx.find('.order-date').val(invoiceDateValue);
        }
        updateDueDateFromSelection();
    });
    $ctx.on('change', '.order-date', updateDueDateFromSelection);
    updateDueDateFromSelection();

    $('#itemColumnModal').on('show.bs.modal', function () {
        const $modal = $(this);
        const categories = Array.from(new Set(baseItems.map(item => getItemMeta(item).categoryLabel).filter(Boolean)));
        const $categorySelect = $modal.find('.item-filter-category');
        if ($categorySelect.length) {
            $categorySelect.empty().append('<option value="">Select Category</option>');
            categories.forEach(cat => {
                $categorySelect.append(`<option value="${cat.toString().toLowerCase()}">${cat}</option>`);
            });
        }
    });

    $('#itemColumnModal').on('change', '.check-category, .check-item-code, .check-description, .check-discount', function () {
        const $modal = $('#itemColumnModal');
        $modal.find('.item-filter-category').prop('disabled', !$('.check-category').is(':checked'));
        $modal.find('.item-filter-code').prop('disabled', !$('.check-item-code').is(':checked'));
        $modal.find('.item-filter-description').prop('disabled', !$('.check-description').is(':checked'));
        $modal.find('.item-filter-discount').prop('disabled', !$('.check-discount').is(':checked'));
    });

    $('#itemColumnModal').on('click', '.item-filter-apply', function () {
        applyColumnVisibility();
        updateItemSelectOptions();
    });

    function setAdditionalChargesEditable(isEnabled) {
        const $modal = $('#additionalChargesModal');
        const disabled = !isEnabled;
        $modal.find('.additional-charge-input, .additional-charge-tax, .additional-charge-tax-check, .additional-charge-check').prop('disabled', disabled);
    }

    $('#additionalChargesModal').on('shown.bs.modal', function () {
        const isEnabled = $('#additionalChargesToggle').is(':checked');
        setAdditionalChargesEditable(isEnabled);
    });

    $(document).on('change', '#additionalChargesToggle', function () {
        setAdditionalChargesEditable($(this).is(':checked'));
    });
}
