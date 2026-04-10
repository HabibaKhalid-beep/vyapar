function initializeForm(context) {
    const $ctx = $(context);
    const itemOptionsHtml = (window.items || []).map(item => {
        const plainLabel = item.name || '';
        const richLabel = `${plainLabel} | Sale: ${item.sale_price ?? item.price ?? 0} | Stock: ${item.opening_qty ?? 0} | Location: ${item.location ?? ''}`;
        return `<option value="${item.id}" data-price="${item.price ?? ''}" data-sale-price="${item.sale_price ?? ''}" data-label="${plainLabel}" data-unit="${item.unit || ''}">${richLabel}</option>`;
    }).join('');

    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
    const warehouseModalEl = document.getElementById('warehouseModal');
    const warehouseModal = warehouseModalEl ? new bootstrap.Modal(warehouseModalEl) : null;
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');
    const todayValue = `${yyyy}-${mm}-${dd}`;
    let pendingImages = [];
    let existingImagePaths = [];

    $ctx.find('.invoice-date').val(todayValue);
    $ctx.find('.due-date').val(todayValue);

    function showToast(message, isError = false) {
        const toastEl = document.getElementById('sale-toast');
        if (!toastEl) return alert(message);
        toastEl.querySelector('.toast-body').textContent = message;
        toastEl.classList.toggle('text-bg-success', !isError);
        toastEl.classList.toggle('text-bg-danger', isError);
        new bootstrap.Toast(toastEl, { delay: 5000 }).show();
    }

    function setPartyBalance(opening = 0, type = '') {
        const amount = Number(opening || 0).toFixed(2);
        const icon = type === 'pay' ? '<i class="fa-solid fa-arrow-up me-1"></i>' : type === 'receive' ? '<i class="fa-solid fa-arrow-down me-1"></i>' : '';
        const klass = type === 'pay' ? 'text-danger' : type === 'receive' ? 'text-success' : 'text-primary';
        $ctx.find('#partyBalanceDisplay').html(`<span class="${klass}">${icon}Rs ${amount}</span>`);
    }

    function setPartyDetails(party) {
        if (!party) return;
        $ctx.find('.party-id').val(party.id || '');
        $ctx.find('#partyDropdownBtn').text(party.name || 'Select Party');
        $ctx.find('.phone-input').val(party.phone || '');
        $ctx.find('.billing-address').val(party.billing_address || party.billing || '');
        setPartyBalance(party.opening_balance || party.opening || 0, party.transaction_type || party.type || '');
    }

    function setBrokerDetails({ id = '', name = '', phone = '' } = {}) {
        $ctx.find('.broker-id').val(id || '');
        $ctx.find('#brokerDropdownBtn').text(name || 'Select Broker or Party');
        $ctx.find('.broker-name-input').val(name || '');
        $ctx.find('.broker-phone-input').val(phone || '');
    }

    function setWarehouseDetails(warehouse = {}) {
        $ctx.find('.warehouse-id').val(warehouse.id || '');
        $ctx.find('#warehouseDropdownBtn').text(warehouse.name || 'Select Warehouse');
        $ctx.find('.warehouse-phone-input').val(warehouse.phone || '');
        $ctx.find('.warehouse-handler-input').val(warehouse.handler_name || '');
        $ctx.find('.warehouse-handler-phone-input').val(warehouse.handler_phone || '');
        if (warehouse.responsible_user_id || warehouse.user_id) {
            $ctx.find('.responsible-user-select').val(warehouse.responsible_user_id || warehouse.user_id || '');
        }
    }

    function normalizeExistingImagePaths(sale) {
        const paths = Array.isArray(sale.image_paths) ? sale.image_paths.filter(Boolean) : [];
        if (!paths.length && sale.image_path) paths.push(sale.image_path);
        return paths;
    }

    function buildImageUrl(path) {
        if (!path) return '';
        if (/^https?:\/\//i.test(path) || path.startsWith('/')) return path;
        return path.startsWith('storage/') ? '/' + path : '/storage/' + path;
    }

    function renderImages() {
        const gallery = $ctx.find('.image-gallery').empty();
        existingImagePaths.forEach((path, index) => {
            gallery.append(`<div class="image-card" data-existing-index="${index}"><img src="${buildImageUrl(path)}" alt="Attachment"><div class="image-card-body"><div class="image-card-name">${path.split('/').pop()}</div><button type="button" class="btn btn-sm btn-outline-danger mt-2 w-100 remove-existing-image">Remove</button></div></div>`);
        });
        pendingImages.forEach((file, index) => {
            gallery.append(`<div class="image-card" data-new-index="${index}"><img src="${URL.createObjectURL(file)}" alt="${file.name}"><div class="image-card-body"><div class="image-card-name">${file.name}</div><button type="button" class="btn btn-sm btn-outline-danger mt-2 w-100 remove-new-image">Remove</button></div></div>`);
        });
    }

    function ensureUnitOption($unitSelect, unit) {
        const normalizedUnit = (unit || '').toString().trim();
        if (!normalizedUnit) return;
        if (!$unitSelect.find('option').filter(function () { return ($(this).val() || '').trim() === normalizedUnit; }).length) {
            $unitSelect.append(`<option value="${normalizedUnit}">${normalizedUnit}</option>`);
        }
        $unitSelect.val(normalizedUnit);
    }

    function addWarehouseOption(warehouse) {
        const item = `<li><a class="dropdown-item warehouse-option" href="#" data-id="${warehouse.id || ''}" data-name="${warehouse.name || ''}" data-phone="${warehouse.phone || ''}" data-handler-name="${warehouse.handler_name || ''}" data-handler-phone="${warehouse.handler_phone || ''}" data-user-id="${warehouse.responsible_user_id || ''}">${warehouse.name || ''}${warehouse.handler_name ? ' | ' + warehouse.handler_name : ''}${warehouse.phone ? ' | ' + warehouse.phone : ''}</a></li>`;
        $ctx.find('#warehouseDropdownMenu').append(item);
        window.warehouses = (window.warehouses || []).concat([warehouse]);
    }

    function populateFormFromSale(sale) {
        const party = (window.parties || []).find(p => String(p.id) === String(sale.party_id || ''));
        if (party) setPartyDetails(party);
        $ctx.find('.bill-number').val(sale.bill_number || '');
        $ctx.find('.invoice-date').val(sale.invoice_date ? sale.invoice_date.split(' ')[0] : todayValue);
        $ctx.find('.due-date').val(sale.due_date ? sale.due_date.split(' ')[0] : todayValue);
        $ctx.find('.description-input').val(sale.description || '');
        $ctx.find('.discount-pct').val(sale.discount_pct || 0);
        $ctx.find('.discount-rs').val(sale.discount_rs || 0);
        $ctx.find('.tax-select').val(sale.tax_pct || 0);
        const challanDetail = sale.challan_detail || sale.challanDetail || {};
        setBrokerDetails({ id: sale.broker_id || '', name: challanDetail.broker_name || sale.broker?.name || '', phone: challanDetail.broker_phone || sale.broker?.phone || '' });
        setWarehouseDetails({ id: challanDetail.warehouse_id || '', name: challanDetail.warehouse_name || '', phone: challanDetail.warehouse_phone || '', handler_name: challanDetail.warehouse_handler_name || '', handler_phone: challanDetail.warehouse_handler_phone || '', responsible_user_id: challanDetail.responsible_user_id || '' });
        $ctx.find('.vehicle-number-input').val(challanDetail.vehicle_number || '');
        $ctx.find('.destination-input').val(challanDetail.destination || '');
        $ctx.find('.delivery-expenses-input').val(challanDetail.delivery_expenses || 0);
        existingImagePaths = normalizeExistingImagePaths(sale);
        renderImages();
        $ctx.find('.item-rows').empty();
        (sale.items || []).forEach(item => {
            addRow();
            const $row = $ctx.find('.item-rows tr').last();
            const matchOption = $row.find('.item-name option').filter(function () { return $(this).data('label') === (item.item_name || '').trim() || $(this).text().trim() === (item.item_name || '').trim(); }).first();
            if (matchOption.length) matchOption.prop('selected', true);
            $row.find('.item-category').val(item.item_category || '');
            $row.find('.item-code').val(item.item_code || '');
            $row.find('.item-desc').val(item.item_description || '');
            $row.find('.item-discount').val(item.discount || 0);
            $row.find('.item-qty').val(item.quantity || 0);
            ensureUnitOption($row.find('.item-unit'), item.unit || '');
            $row.find('.item-price').val(item.unit_price || 0);
            $row.find('.item-amount').val(item.amount || 0);
        });
        if (sale.description) $ctx.find('.description-pane').removeClass('d-none');
        calculateTotals();
    }

    function addRow() {
        const rowCount = $ctx.find('.item-rows tr').length + 1;
        const isCatVisible = $ctx.find('.check-category').is(':checked');
        const isCodeVisible = $ctx.find('.check-item-code').is(':checked');
        const isDescVisible = $ctx.find('.check-description').is(':checked');
        const isDiscVisible = $ctx.find('.check-discount').is(':checked');
        $ctx.find('.item-rows').append(`<tr class="item-row"><td class="row-num"><span class="row-index-text">${rowCount}</span><div class="delete-row-icon"><i class="fa-solid fa-trash-can"></i></div></td><td><select class="form-select item-name"><option value="" selected disabled>Select Item</option>${itemOptionsHtml}</select></td><td class="col-category ${isCatVisible ? '' : 'd-none'}"><input type="text" class="item-category" placeholder="Category"></td><td class="col-item-code ${isCodeVisible ? '' : 'd-none'}"><input type="text" class="item-code" placeholder="Item Code"></td><td class="col-description ${isDescVisible ? '' : 'd-none'}"><input type="text" class="item-desc" placeholder="Description"></td><td class="col-discount ${isDiscVisible ? '' : 'd-none'}"><input type="number" class="item-discount" value="0"></td><td><input type="number" class="item-qty" value="1"></td><td><select class="item-unit"><option value="">Select Unit</option><option value="PCS">PCS</option><option value="BOX">BOX</option><option value="PACK">PACK</option><option value="SET">SET</option><option value="KG">KG</option><option value="G">Gram</option><option value="M">Meter</option><option value="FT">Feet</option><option value="L">Liter</option><option value="ML">Milliliter</option></select></td><td><input type="number" class="item-price" value="0"></td><td class="col-amount"><input type="text" class="item-amount" value="0" readonly></td><td class="add-col"></td></tr>`);
    }

    function reindexRows() { $ctx.find('.item-rows tr').each((i, el) => $(el).find('.row-index-text').text(i + 1)); }

    function calculateTotals() {
        let totalQty = 0, totalBaseAmount = 0;
        $ctx.find('.item-qty').each((_, el) => totalQty += parseFloat($(el).val()) || 0);
        $ctx.find('.item-amount').each((_, el) => totalBaseAmount += parseFloat($(el).val()) || 0);
        $ctx.find('.total-qty').text(totalQty);
        $ctx.find('.total-base-amount').text(totalBaseAmount.toFixed(2));
        applyDiscountTax(totalBaseAmount);
    }

    function applyDiscountTax(base) {
        let finalBase = base;
        const discPct = parseFloat($ctx.find('.discount-pct').val()) || 0;
        const discRs = parseFloat($ctx.find('.discount-rs').val()) || 0;
        const taxPct = parseFloat($ctx.find('.tax-select').val()) || 0;
        if (discPct > 0) finalBase -= (finalBase * discPct / 100);
        if (discRs > 0) finalBase -= discRs;
        let taxAmount = 0;
        if (taxPct > 0) { taxAmount = (finalBase * taxPct / 100); finalBase += taxAmount; }
        $ctx.find('.tax-amount-display').text(taxAmount.toFixed(2));
        let grandTotal = finalBase, roundOffVal = 0;
        if ($ctx.find('.round-off-check').is(':checked')) { const rounded = Math.round(grandTotal); roundOffVal = rounded - grandTotal; grandTotal = rounded; }
        $ctx.find('.round-off-val').val(roundOffVal.toFixed(2));
        $ctx.find('.grand-total').val(grandTotal.toFixed(2));
    }

    async function compressImage(file) {
        if (!file.type.startsWith('image/')) return file;
        const dataUrl = await new Promise((resolve, reject) => { const reader = new FileReader(); reader.onload = e => resolve(e.target.result); reader.onerror = reject; reader.readAsDataURL(file); });
        const image = await new Promise((resolve, reject) => { const img = new Image(); img.onload = () => resolve(img); img.onerror = reject; img.src = dataUrl; });
        const maxDimension = 1600; let { width, height } = image;
        if (width > height && width > maxDimension) { height = Math.round(height * (maxDimension / width)); width = maxDimension; }
        else if (height > maxDimension) { width = Math.round(width * (maxDimension / height)); height = maxDimension; }
        const canvas = document.createElement('canvas'); canvas.width = width; canvas.height = height; canvas.getContext('2d').drawImage(image, 0, 0, width, height);
        const blob = await new Promise(resolve => canvas.toBlob(resolve, 'image/jpeg', 0.72));
        return blob ? new File([blob], file.name.replace(/\.[^.]+$/, '') + '.jpg', { type: 'image/jpeg', lastModified: Date.now() }) : file;
    }

    async function handleImageSelection(fileList) {
        const files = Array.from(fileList || []).filter(file => file && file.type.startsWith('image/')); if (!files.length) return;
        $ctx.find('.compression-status').text(`Compressing ${files.length} image(s)...`);
        const compressedFiles = [];
        for (const file of files) compressedFiles.push(await compressImage(file));
        pendingImages = pendingImages.concat(compressedFiles);
        $ctx.find('.compression-status').text(`${pendingImages.length} new image(s) ready for upload`);
        renderImages();
        $ctx.find('.image-input').val('');
    }

    function gatherChallanData() {
        const items = Array.from($ctx.find('.item-row')).map(row => {
            const $row = $(row); const selectedText = $row.find('.item-name option:selected').data('label') || $row.find('.item-name option:selected').text() || '';
            return { item_name: selectedText, item_category: $row.find('.item-category').val() || '', item_code: $row.find('.item-code').val() || '', item_description: $row.find('.item-desc').val() || '', quantity: parseInt($row.find('.item-qty').val() || 0, 10) || 0, unit: $row.find('.item-unit').val() || '', unit_price: parseFloat($row.find('.item-price').val() || 0) || 0, discount: parseFloat($row.find('.item-discount').val() || 0) || 0, amount: parseFloat($row.find('.item-amount').val() || 0) || 0 };
        }).filter(item => item.item_name || item.quantity || item.amount);
        return {
            party_id: $ctx.find('.party-id').val() || '', broker_id: $ctx.find('.broker-id').val() || '', broker_name: $ctx.find('.broker-name-input').val() || '', broker_phone: $ctx.find('.broker-phone-input').val() || '', phone: $ctx.find('.phone-input').val() || '', billing_address: $ctx.find('.billing-address').val() || '', bill_number: $ctx.find('.bill-number').val() || '', invoice_date: $ctx.find('.invoice-date').val() || todayValue, due_date: $ctx.find('.due-date').val() || todayValue,
            warehouse_id: $ctx.find('.warehouse-id').val() || '', warehouse_name: $ctx.find('#warehouseDropdownBtn').text().trim() === 'Select Warehouse' ? '' : $ctx.find('#warehouseDropdownBtn').text().trim(), warehouse_phone: $ctx.find('.warehouse-phone-input').val() || '', warehouse_handler_name: $ctx.find('.warehouse-handler-input').val() || '', warehouse_handler_phone: $ctx.find('.warehouse-handler-phone-input').val() || '', responsible_user_id: $ctx.find('.responsible-user-select').val() || '',
            vehicle_number: $ctx.find('.vehicle-number-input').val() || '', destination: $ctx.find('.destination-input').val() || '', delivery_expenses: $ctx.find('.delivery-expenses-input').val() || 0, total_qty: parseInt($ctx.find('.total-qty').text() || 0, 10) || 0, total_amount: parseFloat($ctx.find('.total-base-amount').text() || 0) || 0, discount_pct: parseFloat($ctx.find('.discount-pct').val() || 0) || 0, discount_rs: parseFloat($ctx.find('.discount-rs').val() || 0) || 0, tax_pct: parseFloat($ctx.find('.tax-select').val() || 0) || 0, tax_amount: parseFloat($ctx.find('.tax-amount-display').text() || 0) || 0, round_off: parseFloat($ctx.find('.round-off-val').val() || 0) || 0, grand_total: parseFloat($ctx.find('.grand-total').val() || 0) || 0, status: 'open', description: $ctx.find('.description-input').val() || '', items,
        };
    }

    $ctx.on('click', '.party-option', function (e) { e.preventDefault(); const party = (window.parties || []).find(p => String(p.id) === String($(this).data('id'))); setPartyDetails(party || { id: $(this).data('id') || '', name: $.trim($(this).find('span').first().text()), phone: $(this).data('phone') || '', billing_address: $(this).data('billing') || '', opening_balance: $(this).data('opening') || 0, transaction_type: $(this).data('type') || '' }); });
    $ctx.on('click', '.broker-option', function (e) { e.preventDefault(); setBrokerDetails({ id: $(this).data('source') === 'broker' ? ($(this).data('id') || '') : '', name: $(this).data('name') || '', phone: $(this).data('phone') || '' }); });
    $ctx.on('click', '.clear-broker-selection', function (e) { e.preventDefault(); setBrokerDetails(); });
    $ctx.on('click', '.warehouse-option', function (e) { e.preventDefault(); setWarehouseDetails({ id: $(this).data('id') || '', name: $(this).data('name') || '', phone: $(this).data('phone') || '', handler_name: $(this).data('handler-name') || '', handler_phone: $(this).data('handler-phone') || '', responsible_user_id: $(this).data('user-id') || '' }); });
    $ctx.on('click', '.add-warehouse-option', function (e) { e.preventDefault(); warehouseModal?.show(); });
    $(document).off('click.saveWarehouse').on('click.saveWarehouse', '.save-warehouse-btn', async function () {
        const form = document.getElementById('warehouseForm'); if (!form) return;
        const btn = $(this); btn.prop('disabled', true).text('Saving...');
        try {
            const response = await fetch(window.warehouseStoreUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: new FormData(form) });
            const payload = await response.json(); if (!response.ok || !payload.success) throw new Error(payload.message || 'Warehouse save failed');
            addWarehouseOption(payload.warehouse); setWarehouseDetails(payload.warehouse); form.reset(); warehouseModal?.hide(); showToast('Warehouse added successfully.');
        } catch (error) { showToast(error.message || 'Warehouse save failed', true); } finally { btn.prop('disabled', false).text('Save Warehouse'); }
    });

    $ctx.find('.add-row-btn').on('click', addRow);
    $ctx.on('click', '.delete-row-icon', function () { if ($ctx.find('.item-rows tr').length > 1) { $(this).closest('tr').remove(); reindexRows(); calculateTotals(); } });
    $ctx.on('change', '.item-name', function () { const $row = $(this).closest('tr'); const $selected = $(this).find('option:selected'); const price = parseFloat($selected.data('sale-price')) || parseFloat($selected.data('price')) || 0; ensureUnitOption($row.find('.item-unit'), $selected.data('unit') || ''); $row.find('.item-price').val(price.toFixed(2)); $row.find('.item-qty').val(1).trigger('change'); });
    $ctx.on('input change', '.item-qty, .item-price, .item-discount', function () { const $row = $(this).closest('tr'); const amount = ((parseFloat($row.find('.item-qty').val()) || 0) * (parseFloat($row.find('.item-price').val()) || 0)) - (parseFloat($row.find('.item-discount').val()) || 0); $row.find('.item-amount').val(amount.toFixed(2)); calculateTotals(); });
    $ctx.on('input change', '.discount-pct, .discount-rs, .tax-select, .round-off-check', function () { applyDiscountTax(parseFloat($ctx.find('.total-base-amount').text()) || 0); });
    $ctx.on('click', '.add-description', function () { $ctx.find('.description-pane').toggleClass('d-none'); });
    $ctx.on('click', '.add-image, .image-placeholder', function () { $ctx.find('.image-input').trigger('click'); });
    $ctx.on('change', '.image-input', async function () { await handleImageSelection(this.files); });
    $ctx.on('click', '.remove-new-image', function () { pendingImages.splice(Number($(this).closest('.image-card').data('new-index')), 1); renderImages(); });
    $ctx.on('click', '.remove-existing-image', function () { existingImagePaths.splice(Number($(this).closest('.image-card').data('existing-index')), 1); renderImages(); });

    $ctx.on('click', '.btn-save', async function () {
        const challanData = gatherChallanData(); if (!challanData.items.length) return showToast('Please add at least one item before saving.', true);
        const btn = $(this); btn.prop('disabled', true).text('Saving...');
        try {
            const formData = new FormData(); Object.entries(challanData).forEach(([key, value]) => formData.append(key, key === 'items' ? JSON.stringify(value) : (value ?? '')));
            existingImagePaths.forEach((path, index) => formData.append(`existing_image_paths[${index}]`, path)); pendingImages.forEach(image => formData.append('images[]', image));
            if (window.saleHttpMethod && window.saleHttpMethod !== 'POST') formData.append('_method', window.saleHttpMethod);
            const response = await fetch(window.saleStoreUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }, body: formData });
            const payload = await response.json(); if (!response.ok) throw new Error(payload.message || 'Server error');
            showToast('Delivery challan saved successfully.'); if (payload.redirect_url) setTimeout(() => { window.location.href = payload.redirect_url; }, 1200);
        } catch (error) { showToast('Error saving challan. ' + (error.message || ''), true); } finally { btn.prop('disabled', false).text('Save'); }
    });

    if (window.editSaleData) populateFormFromSale(window.editSaleData);
    calculateTotals();
}

