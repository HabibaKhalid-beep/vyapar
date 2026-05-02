(function () {
    const defaultUnits = [
        { short_name: 'PCS' }, { short_name: 'BOX' }, { short_name: 'PACK' }, { short_name: 'SET' },
        { short_name: 'KG' }, { short_name: 'G' }, { short_name: 'M' }, { short_name: 'FT' },
        { short_name: 'L' }, { short_name: 'ML' }
    ];

    const parseJsonSafely = (text) => {
        try { return JSON.parse(text); } catch (_) { return null; }
    };

    const fetchJson = (url, options = {}) => {
        const headers = Object.assign({
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }, options.headers || {});

        return fetch(url, Object.assign({}, options, { headers }))
            .then(async (response) => {
                const text = await response.text();
                const data = parseJsonSafely(text);
                if (!response.ok) {
                    throw new Error(data?.message || `Request failed with status ${response.status}.`);
                }
                if (data === null) {
                    throw new Error('Server response was not valid JSON.');
                }
                return data;
            });
    };

    const getItemMeta = (item = {}) => ({
        plainLabel: item.name || '',
        richLabel: `${item.name || ''} | Sale: ${item.sale_price ?? item.price ?? 0} | Stock: ${item.opening_qty ?? 0} | Location: ${item.location ?? ''}`,
        categoryLabel: item.category_name || item.category?.name || item.category || item.category_id || '',
        itemCode: item.item_code || '',
        description: item.description || item.item_description || '',
        discount: item.discount ?? item.sale_discount ?? 0,
        purchasePrice: item.purchase_price ?? 0
    });

    const buildPickerRowsHtml = (items = []) => {
        if (!items.length) return '<div class="item-picker-empty">No items found</div>';
        return items.map((item) => {
            const stock = parseFloat(item.opening_qty ?? 0) || 0;
            return `
                <div class="item-picker-row item-picker-option" data-id="${item.id}">
                    <div class="item-picker-name">${item.name || ''}${item.item_code ? `<small>(${item.item_code})</small>` : ''}</div>
                    <div>${(parseFloat(item.sale_price ?? item.price ?? 0) || 0).toFixed(2)}</div>
                    <div>${(parseFloat(item.purchase_price ?? 0) || 0).toFixed(2)}</div>
                    <div class="item-picker-stock ${stock < 0 ? 'neg' : ''}">${stock}</div>
                </div>
            `;
        }).join('');
    };

    const buildOptionsHtml = (items = []) => items.map((item) => {
        const meta = getItemMeta(item);
        return `<option value="${item.id}" data-price="${item.price ?? ''}" data-sale-price="${item.sale_price ?? ''}" data-purchase-price="${meta.purchasePrice}" data-stock="${item.opening_qty ?? ''}" data-location="${item.location ?? ''}" data-label="${meta.plainLabel}" data-rich-label="${meta.richLabel}" data-unit="${item.unit || ''}" data-category="${meta.categoryLabel}" data-item-code="${meta.itemCode}" data-description="${meta.description}" data-discount="${meta.discount}">${meta.richLabel}</option>`;
    }).join('');

    const buildUnitMenuHtml = (units = []) => {
        const normalized = (Array.isArray(units) && units.length ? units : defaultUnits)
            .map((unit) => String(unit.short_name || unit.short || unit.name || '').trim().toUpperCase())
            .filter(Boolean);

        return normalized.map((unit) => `<li><button class="dropdown-item unit-option" type="button" data-unit="${unit}">${unit}</button></li>`).join('');
    };

    function initPartySearch($ctx) {
        const $partySearchInput = $ctx.find('.party-search-input');
        const $partyDropdown = $ctx.find('.party-dropdown-wrapper');

        if (!$partySearchInput.length) return;

        $partySearchInput.off('.sharedPartySearch').on('click.sharedPartySearch focus.sharedPartySearch keydown.sharedPartySearch keyup.sharedPartySearch', function (e) {
            e.stopPropagation();
        }).on('input.sharedPartySearch', function () {
            const searchTerm = String($(this).val() || '').trim().toLowerCase();
            $ctx.find('.party-option').each(function () {
                const $option = $(this);
                const partyName = String($.trim($option.find('span').first().text() || '')).toLowerCase();
                const partyPhone = String($option.data('phone') || '').toLowerCase();
                const visible = !searchTerm || partyName.includes(searchTerm) || partyPhone.includes(searchTerm);
                $option.closest('li').toggleClass('d-none', !visible);
            });
        });

        $partyDropdown.off('show.bs.dropdown.sharedPartySearch hide.bs.dropdown.sharedPartySearch')
            .on('show.bs.dropdown.sharedPartySearch', function () {
                setTimeout(() => $partySearchInput.trigger('focus').trigger('select'), 100);
            })
            .on('hide.bs.dropdown.sharedPartySearch', function () {
                $partySearchInput.val('');
                $ctx.find('.party-option').closest('li').removeClass('d-none');
            });
    }

    function positionPanel($picker) {
        const $input = $picker.find('.item-picker-input');
        const $panel = $picker.find('.item-picker-panel');
        if (!$panel.hasClass('open')) return;

        const rect = $input.get(0).getBoundingClientRect();
        const width = Math.max(rect.width, 320);
        const left = Math.max(12, Math.min(rect.left, window.innerWidth - width - 12));

        $panel.css({
            position: 'fixed',
            top: `${rect.bottom + 4}px`,
            left: `${left}px`,
            width: `${width}px`,
            minWidth: `${width}px`,
            zIndex: 1055,
            display: 'block'
        });
    }

    function getItemsFromSelect($select) {
        return $select.find('option').map(function () {
            const value = $(this).attr('value');
            if (!value) return null;
            return {
                id: value,
                name: $(this).attr('data-label') || $(this).text().trim(),
                item_code: $(this).attr('data-item-code') || '',
                description: $(this).attr('data-description') || '',
                sale_price: parseFloat($(this).attr('data-sale-price') || $(this).attr('data-price') || 0) || 0,
                purchase_price: parseFloat($(this).attr('data-purchase-price') || 0) || 0,
                opening_qty: parseFloat($(this).attr('data-stock') || 0) || 0,
                location: $(this).attr('data-location') || '',
                unit: $(this).attr('data-unit') || '',
                category_name: $(this).attr('data-category') || '',
                discount: parseFloat($(this).attr('data-discount') || 0) || 0
            };
        }).get().filter(Boolean);
    }

    function enhanceItemPicker($ctx, $select) {
        if ($select.data('enhanced-picker')) return;
        $select.data('enhanced-picker', true).addClass('d-none');

        const pickerHtml = `
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
            </div>
        `;

        const $picker = $(pickerHtml);
        $select.before($picker);

        const syncInput = () => {
            const $selected = $select.find('option:selected');
            const value = String($selected.val() || '').trim();
            $picker.find('.item-picker-input').val(value ? (($selected.data('label') || $selected.text() || '').trim()) : '');
        };

        const renderPicker = (query = '') => {
            const normalized = String(query || '').trim().toLowerCase();
            const items = (window.items && window.items.length ? window.items : getItemsFromSelect($select)).filter((item) => {
                const label = String(item.name || '').toLowerCase();
                const code = String(item.item_code || '').toLowerCase();
                const desc = String(item.description || '').toLowerCase();
                return !normalized || label.includes(normalized) || code.includes(normalized) || desc.includes(normalized);
            });
            $picker.find('.item-picker-list').html(buildPickerRowsHtml(items));
            $picker.find('.item-picker-panel').addClass('open');
            positionPanel($picker);
        };

        syncInput();

        $picker.on('focus click', '.item-picker-input', function () {
            const raw = String($(this).val() || '').trim();
            renderPicker(raw.toLowerCase() === 'select item' ? '' : raw);
        });

        $picker.on('input', '.item-picker-input', function () {
            renderPicker($(this).val());
        });

        $picker.on('click', '.item-picker-option', function (e) {
            e.preventDefault();
            const id = String($(this).data('id') || '');
            $select.val(id).trigger('change');
            syncInput();
            $picker.find('.item-picker-panel').removeClass('open').hide();
        });

        $picker.on('click', '.item-picker-add', function () {
            window.activeCreateRow = $ctx.find('.item-row').index($select.closest('tr'));
            bootstrap.Modal.getOrCreateInstance(document.getElementById('addItemModal')).show();
        });

        $select.on('change.sharedPicker', function () {
            syncInput();
        });
    }

    function enhanceAllItemPickers($ctx) {
        $ctx.find('select.item-name').each(function () {
            enhanceItemPicker($ctx, $(this));
        });
    }

    function refreshAllItemOptions(item) {
        if (item) {
            window.items = Array.isArray(window.items) ? window.items : [];
            window.items.push(item);
        }
        const unique = [];
        const seen = new Set();
        (window.items || []).forEach((entry) => {
            const id = String(entry.id || '');
            if (!id || seen.has(id)) return;
            seen.add(id);
            unique.push(entry);
        });
        window.items = unique;
        const optionsHtml = buildOptionsHtml(unique);
        $('select.item-name').each(function () {
            const current = $(this).val();
            $(this).html('<option value="" selected disabled>Select Item</option>' + optionsHtml);
            if (current) $(this).val(current);
            $(this).trigger('change.sharedPicker');
        });
    }

    function savePartyFromModal() {
        const form = document.getElementById('addPartyForm');
        if (!form || !window.partyStoreUrl) return;
        const formData = new FormData(form);
        const creditSwitch = document.getElementById('creditLimitSwitch');
        if (creditSwitch) {
            formData.set('credit_limit_enabled', creditSwitch.checked ? 1 : 0);
        }

        fetchJson(window.partyStoreUrl, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
            body: formData
        }).then((data) => {
            const party = data.party || data.data || null;
            if (!party) throw new Error('Party was not returned from server.');
            window.parties = Array.isArray(window.parties) ? window.parties : [];
            window.parties.unshift(party);
            $('.party-dropdown-wrapper #partyDropdownMenu').each(function () {
                const $menu = $(this);
                const $divider = $menu.find('.dropdown-divider').first();
                const itemHtml = `<li><a class="dropdown-item d-flex justify-content-between party-option" href="#" data-id="${party.id}" data-phone="${party.phone || ''}" data-billing="${party.billing_address || ''}" data-shipping="${party.shipping_address || ''}" data-opening="${party.opening_balance || 0}" data-type="${party.transaction_type || ''}"><span>${party.name || ''}</span><span>Rs ${Number(party.opening_balance || 0).toFixed(2)}</span></a></li>`;
                if ($divider.length) $divider.parent().before(itemHtml); else $menu.append(itemHtml);
            });
            $('#partyDropdownBtn').text(party.name || 'Select Party');
            $('.party-id').val(party.id || '');
            $('.phone-input').val(party.phone || '');
            $('.billing-address').val(party.billing_address || '');
            bootstrap.Modal.getOrCreateInstance(document.getElementById('addPartyModal')).hide();
            form.reset();
        }).catch((error) => alert(error.message || 'Unable to save party.'));
    }

    function bindSharedCreateEnhancements() {
        const $ctx = $(document.body);
        initPartySearch($ctx);
        enhanceAllItemPickers($ctx);

        const observer = new MutationObserver(() => enhanceAllItemPickers($ctx));
        const rowsRoot = document.querySelector('.item-rows');
        if (rowsRoot) observer.observe(rowsRoot, { childList: true, subtree: true });

        $(document).on('click.sharedPickerClose', function (e) {
            if (!$(e.target).closest('.item-picker').length) {
                $('.item-picker-panel').removeClass('open').hide();
            }
        });

        $(window).on('resize.sharedPicker scroll.sharedPicker', function () {
            $('.item-picker').each(function () { positionPanel($(this)); });
        });

        $(document).on('click.sharedPartyOpen', '#addNewPartyBtn', function (e) {
            e.preventDefault();
            const modalEl = document.getElementById('addPartyModal');
            if (modalEl) bootstrap.Modal.getOrCreateInstance(modalEl).show();
        });

        if (window.useSharedPartySave) {
            $(document).on('click.sharedPartySave', '#btnSaveParty, #btnSaveNewParty', function (e) {
                e.preventDefault();
                savePartyFromModal();
            });
        }

        $(document).on('click.sharedUnitSelect', '.unit-option', function (e) {
            e.preventDefault();
            const unit = $(this).data('unit') || $(this).text().trim();
            $('#newItemUnitBtn').text(unit);
            $('#newItemUnit').val(unit);
        });

        $(document).on('click.sharedAssignCode', '#assignItemCodeBtn', function (e) {
            e.preventDefault();
            const itemName = $('#newItemName').val().trim();
            if (!itemName) return;
            $('#newItemCode').val(itemName.toUpperCase().replace(/\s+/g, '-').replace(/[^A-Z0-9-_]/g, '').substring(0, 50));
        });

        $(document).on('click.sharedItemSave', '#saveNewItemBtn', function () {
            const itemRoutes = window.itemRoutes || {};
            const storeUrl = itemRoutes.store;
            if (!storeUrl) return alert('Item store route is missing.');
            const itemName = $('#newItemName').val().trim();
            if (!itemName) return alert('Please enter an item name');

            const formData = new FormData();
            formData.append('name', itemName);
            formData.append('category_id', $('#newItemCategory').val() || '');
            formData.append('unit', $('#newItemUnit').val() || '');
            const type = $('#newItemType').val() || 'product';
            formData.append('item_type', type);
            formData.append('type', type);
            formData.append('sale_price', $('#newItemSalePrice').val() || 0);
            formData.append('purchase_price', $('#newItemPurchasePrice').val() || 0);
            formData.append('wholesale_price', $('#newItemWholesalePrice').val() || 0);
            formData.append('wholesale_min_qty', $('#newItemWholesaleMinQty').val() || 0);
            formData.append('item_code', $('#newItemCode').val() || '');
            formData.append('opening_qty', $('#newItemStock').val() || 0);
            formData.append('at_price', $('#newItemAtPrice').val() || 0);
            formData.append('as_of_date', $('#newItemAsOfDate').val() || '');
            formData.append('min_stock', $('#newItemMinStock').val() || 0);
            formData.append('location', $('#newItemLocation').val() || '');
            formData.append('description', $('#newItemDescription').val() || '');

            const imageInput = document.getElementById('newItemImage');
            if (imageInput && imageInput.files.length > 0) {
                formData.append('item_image', imageInput.files[0]);
            }

            fetchJson(storeUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '' },
                body: formData
            }).then((data) => {
                if (!data.item) throw new Error('Item was not returned from server.');
                refreshAllItemOptions(data.item);
                const rowIndex = Number(window.activeCreateRow || 0);
                const $targetSelect = $('.item-row').eq(rowIndex).find('select.item-name').first();
                if ($targetSelect.length) {
                    $targetSelect.val(String(data.item.id)).trigger('change');
                }
                bootstrap.Modal.getOrCreateInstance(document.getElementById('addItemModal')).hide();
                document.getElementById('addItemForm')?.reset();
                $('#newItemUnitBtn').text('Select Unit');
                $('#newItemUnit').val('');
            }).catch((error) => alert(error.message || 'Unable to save item.'));
        });
    }

    document.addEventListener('DOMContentLoaded', bindSharedCreateEnhancements);
})();
