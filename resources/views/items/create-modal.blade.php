<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Add Item</title>
    <style>
        body {
            margin: 0;
            padding: 20px;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: #f8f9fa;
        }
        .form-container {
            background: white;
            border-radius: 8px;
            padding: 24px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 1px solid #e5e7eb;
        }
        .form-title {
            font-size: 20px;
            font-weight: 700;
            color: #1a1a1a;
            margin: 0;
        }
        .close-btn {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6b7280;
            padding: 0;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
        }
        .close-btn:hover {
            background: #f3f4f6;
            color: #374151;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-label {
            display: block;
            margin-bottom: 6px;
            font-weight: 600;
            color: #374151;
            font-size: 14px;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-primary {
            background: #2563eb;
            color: white;
        }
        .btn-primary:hover {
            background: #1d4ed8;
        }
        .btn-primary:disabled {
            background: #9ca3af;
            cursor: not-allowed;
        }
        .btn-secondary {
            background: white;
            color: #2563eb;
            border: 1px solid #2563eb;
        }
        .btn-secondary:hover {
            background: #f0f9ff;
        }
        .form-footer {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .type-toggle {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 20px;
        }
        .toggle-switch {
            position: relative;
            width: 48px;
            height: 24px;
        }
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 16px;
            width: 16px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }
        input:checked + .toggle-slider {
            background-color: #2563eb;
        }
        input:checked + .toggle-slider:before {
            transform: translateX(24px);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <div class="form-header">
            <h2 class="form-title">Add Item</h2>
            <button type="button" class="close-btn" onclick="closeModal()">&times;</button>
        </div>

        <form id="itemForm">
            <div class="type-toggle">
                <label>Product</label>
                <div class="toggle-switch">
                    <input type="checkbox" id="typeToggle" onchange="handleTypeToggle()">
                    <span class="toggle-slider"></span>
                </div>
                <label>Service</label>
            </div>

            <div class="form-group">
                <label class="form-label">Item Name *</label>
                <input type="text" id="itemName" class="form-control" placeholder="Enter item name" required oninput="updateSaveBtn()">
            </div>

            <div class="form-group">
                <label class="form-label">Category</label>
                <select id="category" class="form-control">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->name }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Unit</label>
                <select id="unit" class="form-control">
                    <option value="">Select Unit</option>
                    <option value="PCS">PCS (Pieces)</option>
                    <option value="BOX">BOX</option>
                    <option value="PACK">PACK</option>
                    <option value="SET">SET</option>
                    <option value="KG">KG (Kilogram)</option>
                    <option value="G">Gram</option>
                    <option value="M">Meter</option>
                    <option value="FT">Feet</option>
                    <option value="L">Liter</option>
                    <option value="ML">Milliliter</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label">Item Code</label>
                <input type="text" id="itemCode" class="form-control" placeholder="Enter item code">
            </div>

            <div class="form-group">
                <label class="form-label">Sale Price</label>
                <input type="number" id="salePrice" class="form-control" placeholder="0.00" min="0" step="0.01">
            </div>

            <div class="form-group" id="purchasePriceGroup">
                <label class="form-label">Purchase Price</label>
                <input type="number" id="purchasePrice" class="form-control" placeholder="0.00" min="0" step="0.01">
            </div>

            <div class="form-group">
                <label class="form-label">Opening Quantity</label>
                <input type="number" id="openingQty" class="form-control" placeholder="0" min="0">
            </div>

            <div class="form-group">
                <label class="form-label">Location</label>
                <input type="text" id="location" class="form-control" placeholder="Enter location">
            </div>

            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea id="description" class="form-control" rows="3" placeholder="Enter description"></textarea>
            </div>

            <div class="form-footer">
                <button type="button" class="btn btn-secondary" onclick="saveAndNew()">Save & New</button>
                <button type="button" class="btn btn-primary" id="saveBtn" onclick="saveItem()" disabled>Save</button>
            </div>
        </form>
    </div>

    <script>
        let currentType = 'product';

        function closeModal() {
            if (window.parent && window.parent.postMessage) {
                window.parent.postMessage({ type: 'close-modal' }, '*');
            }
        }

        function handleTypeToggle() {
            const isChecked = document.getElementById('typeToggle').checked;
            currentType = isChecked ? 'service' : 'product';
            
            // Toggle purchase price visibility for services
            document.getElementById('purchasePriceGroup').style.display = isChecked ? 'none' : 'block';
        }

        function updateSaveBtn() {
            const itemName = document.getElementById('itemName').value.trim();
            const saveBtn = document.getElementById('saveBtn');
            saveBtn.disabled = !itemName;
        }

        function saveItem() {
            const itemName = document.getElementById('itemName').value.trim();
            if (!itemName) {
                alert('Please enter an item name');
                return;
            }

            const formData = new FormData();
            formData.append('name', itemName);
            formData.append('type', currentType);
            formData.append('category', document.getElementById('category').value);
            formData.append('unit', document.getElementById('unit').value);
            formData.append('item_code', document.getElementById('itemCode').value);
            formData.append('sale_price', document.getElementById('salePrice').value || 0);
            formData.append('purchase_price', document.getElementById('purchasePrice').value || 0);
            formData.append('opening_qty', document.getElementById('openingQty').value || 0);
            formData.append('location', document.getElementById('location').value);
            formData.append('description', document.getElementById('description').value);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

            fetch('/items', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.item) {
                    // Notify parent window about the new item
                    if (window.parent && window.parent.postMessage) {
                        window.parent.postMessage({
                            type: 'item-saved',
                            item: data.item
                        }, '*');
                    }
                    closeModal();
                } else {
                    alert('Error saving item');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving item');
            });
        }

        function saveAndNew() {
            saveItem();
            // Reset form for new item
            document.getElementById('itemForm').reset();
            updateSaveBtn();
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateSaveBtn();
        });
    </script>
</body>
</html>
