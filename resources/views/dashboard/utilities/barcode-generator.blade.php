@extends('layouts.app')

@section('title', 'Utilities — Barcode Generator')
@section('description', 'Generate barcodes for items and products.')
@section('page', 'barcode-generator')

@push('styles')
<style>
  .barcode-panel {
    min-height: 420px;
    border: 1px solid #e9ecef;
    border-radius: 16px;
    background: #f8f9fa;
  }
  .barcode-preview {
    background: #ffffff;
    border: 1px dashed #d8dbe0;
    border-radius: 18px;
    padding: 22px;
    min-height: 260px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    gap: 10px;
    box-shadow: inset 0 0 0 1px rgba(0,0,0,0.01);
  }
  .barcode-preview svg {
    width: 100%;
    height: 120px;
  }
  .barcode-preview h6 {
    font-size: 0.95rem;
    margin-bottom: 0.5rem;
    color: #343a40;
    text-transform: uppercase;
    letter-spacing: 0.03em;
  }
  .barcode-preview .preview-line {
    font-size: 0.93rem;
    color: #495057;
    line-height: 1.5;
  }
  .barcode-preview .badge-preview {
    display: inline-flex;
    justify-content: center;
    background: #f1f3f5;
    padding: 10px 12px;
    border-radius: 12px;
    margin-top: 12px;
    color: #495057;
  }
  .barcode-table {
    border-collapse: separate;
    border-spacing: 0 0.35rem;
  }
  .barcode-table thead th {
    background: #eef2f5;
    color: #2c3e50;
    font-weight: 700;
    font-size: 0.92rem;
    border: none;
    padding: 16px 14px;
  }
  .barcode-table tbody tr {
    background: #ffffff;
    border-radius: 16px;
    box-shadow: 0 1px 1px rgba(38, 57, 77, 0.04);
  }
  .barcode-table tbody tr td {
    border: none;
    padding: 14px 14px;
    vertical-align: middle;
  }
  .barcode-table tbody tr:hover {
    background: #f5f8fb;
  }
  .barcode-table .view-barcode-btn {
    min-width: 88px;
  }
  .barcode-table td:first-child {
    width: 42px;
  }
  .barcode-table td:last-child {
    width: 120px;
  }
  .form-label-required::after {
    content: '*';
    color: #e03131;
    margin-left: 0.2rem;
  }
  .barcode-table th, .barcode-table td {
    vertical-align: middle;
  }
</style>
@endpush

@section('content')
  <div class="container-fluid mt-4">
    <div class="row gy-4">
      <div class="col-xl-12">
        <div class="card shadow-sm p-4">
          <div class="mb-4">
            <h2 class="mb-1">Barcode Generator</h2>
            <p class="text-muted mb-0">Enter item details to add for barcode and preview instantly.</p>
          </div>

          @if(session('success'))
            <div class="alert alert-success" id="successMessage">{{ session('success') }}</div>
          @endif

          <form id="barcodeForm" action="{{ route('utilities.barcode-generator.store') }}" method="POST">
            @csrf
            <div class="row gx-4 gy-3">
              <div class="col-xl-7">
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label form-label-required" for="itemSelect">Item Name</label>
                    <select id="itemSelect" name="item_id" class="form-select" required>
                      <option value="">Select Item</option>
                    </select>
                    <input type="hidden" id="itemName" name="item_name" />
                  </div>
                  <div class="col-md-6">
                    <label class="form-label form-label-required" for="itemCode">Item Code</label>
                    <div class="input-group">
                      <input id="itemCode" name="item_code" class="form-control" placeholder="Enter Item Code" required>
                      <button type="button" class="btn btn-outline-secondary" id="assignCodeBtn">Assign Code</button>
                    </div>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label form-label-required" for="labels">No of Labels</label>
                    <input id="labels" name="labels" type="number" min="1" value="1" class="form-control" placeholder="Enter No of Labels" required>
                  </div>
                  <div class="col-md-8">
                    <label class="form-label" for="header">Header</label>
                    <select id="header" name="header_option" class="form-select">
                      <option value="company">Company Name</option>
                      <option value="item_name">Item Name</option>
                      <option value="sale_price">Sale Price</option>
                      <option value="discount">Discount</option>
                    </select>
                    <input type="hidden" id="headerValue" name="header">
                  </div>

                  <input type="hidden" id="salePrice" name="sale_price">
                  <input type="hidden" id="discount" name="discount" value="0">

                  <div class="col-md-6">
                    <label class="form-label" for="line1">Line 1</label>
                    <select id="line1" name="line1_option" class="form-select">
                      <option value="company">Company Name</option>
                      <option value="item_name">Item Name</option>
                      <option value="sale_price">Sale Price</option>
                      <option value="discount">Discount</option>
                    </select>
                    <input type="hidden" id="line1Value" name="line_1">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="line2">Line 2</label>
                    <select id="line2" name="line2_option" class="form-select">
                      <option value="company">Company Name</option>
                      <option value="item_name">Item Name</option>
                      <option value="sale_price">Sale Price</option>
                      <option value="discount">Discount</option>
                    </select>
                    <input type="hidden" id="line2Value" name="line_2">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="line3">Line 3</label>
                    <select id="line3" name="line3_option" class="form-select">
                      <option value="company">Company Name</option>
                      <option value="item_name">Item Name</option>
                      <option value="sale_price">Sale Price</option>
                      <option value="discount">Discount</option>
                    </select>
                    <input type="hidden" id="line3Value" name="line_3">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label" for="line4">Line 4</label>
                    <select id="line4" name="line4_option" class="form-select">
                      <option value="company">Company Name</option>
                      <option value="item_name">Item Name</option>
                      <option value="sale_price">Sale Price</option>
                      <option value="discount">Discount</option>
                    </select>
                    <input type="hidden" id="line4Value" name="line_4">
                  </div>

                  <input type="hidden" id="barcodeValue" name="barcode_value">
                </div>
              </div>

              <div class="col-xl-5">
                <div class="card border rounded-4 p-3 h-100">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                      <small class="text-uppercase text-muted fw-semibold">Preview</small>
                      <h6 class="mb-0">Live Barcode</h6>
                    </div>
                    <span class="badge bg-secondary">Label Printer</span>
                  </div>
                  <div class="barcode-preview" style="min-height: 310px;">
                    <h6 id="previewHeader">{{ Auth::user()?->name ?? 'My Company' }}</h6>
                    <svg id="barcodeSvg" jsbarcode-format="CODE128" jsbarcode-value="123456789012"></svg>
                    <div id="previewItemCode" class="preview-line text-muted">123456789012</div>
                    <div id="previewLine1" class="preview-line">Sample Item</div>
                    <div id="previewLine2" class="preview-line">Sale Price: 0</div>
                    <div id="previewLine3" class="preview-line">Discount: 0%</div>
                    <div id="previewLine4" class="preview-line">{{ Auth::user()?->name ?? 'My Company' }}</div>
                                 <button type="submit" form="barcodeForm" class="btn btn-danger btn-sm">Add Barcode</button>
                  </div>
                </div>
              </div>
            </div>
          </form>
        </div>

        <div class="card shadow-sm p-4 mt-4">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h4 class="mb-0">Item Details</h4>
            <small class="text-muted">Click any row to preview barcode</small>
          </div>
          <div class="table-responsive">
            <table class="table table-bordered barcode-table mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width: 32px;"><input type="checkbox" id="selectAllRows"></th>
                  <th>Item Name</th>
                  <th>No of Labels</th>
                  <th>Header</th>
                  <th>Line 1</th>
                  <th>Line 2</th>
                  <th>Line 3</th>
                  <th>Line 4</th>
                  <th style="width: 120px;">Action</th>
                </tr>
              </thead>
              <tbody>
                @forelse($barcodes as $barcode)
                  <tr class="barcode-preview-row" data-barcode='@json($barcode)'>
                    <td><input type="checkbox" class="row-select"></td>
                    <td>{{ $barcode->item_name }}</td>
                    <td>{{ $barcode->labels }}</td>
                    <td>{{ $barcode->header }}</td>
                    <td>{{ $barcode->line_1 }}</td>
                    <td>{{ $barcode->line_2 }}</td>
                    <td>{{ $barcode->line_3 }}</td>
                    <td>{{ $barcode->line_4 }}</td>
                    <td><button type="button" class="btn btn-sm btn-outline-secondary view-barcode-btn">Preview</button></td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="9" class="text-center text-muted">No barcode entries saved yet.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

    </div>
  </div>

  <div class="modal fade" id="barcodePreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Preview</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          <div class="barcode-preview mx-auto" style="max-width: 520px;">
            <h6 id="modalHeader" class="mb-2"></h6>
            <svg id="modalBarcodeSvg" jsbarcode-format="CODE128" width="100%" height="120"></svg>
            <div id="modalItemCode" class="preview-line text-muted mt-2"></div>
            <div id="modalLine1" class="preview-line"></div>
            <div id="modalLine2" class="preview-line"></div>
            <div id="modalLine3" class="preview-line"></div>
            <div id="modalLine4" class="preview-line"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          <button type="button" class="btn btn-outline-primary" id="downloadBtn">Download</button>
          <button type="button" class="btn btn-primary" id="printBtn">Print</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
<script>
  const itemsEndpoint = '/dashboard/items?json=1';
  const defaultCompany = @json(Auth::user()?->name ?? 'My Company');

  const itemSelectEl = document.getElementById('itemSelect');
  const itemNameEl = document.getElementById('itemName');
  const itemCodeEl = document.getElementById('itemCode');
  const salePriceEl = document.getElementById('salePrice');
  const discountEl = document.getElementById('discount');
  const headerEl = document.getElementById('header');
  const line1El = document.getElementById('line1');
  const line2El = document.getElementById('line2');
  const line3El = document.getElementById('line3');
  const line4El = document.getElementById('line4');
  const headerValue = document.getElementById('headerValue');
  const line1Value = document.getElementById('line1Value');
  const line2Value = document.getElementById('line2Value');
  const line3Value = document.getElementById('line3Value');
  const line4Value = document.getElementById('line4Value');
  const barcodeValueEl = document.getElementById('barcodeValue');
  const assignCodeBtn = document.getElementById('assignCodeBtn');
  const successMessage = document.getElementById('successMessage');
  const selectAllRows = document.getElementById('selectAllRows');
  const modal = new bootstrap.Modal(document.getElementById('barcodePreviewModal'));
  const modalHeader = document.getElementById('modalHeader');
  const modalItemCode = document.getElementById('modalItemCode');
  const modalLine1 = document.getElementById('modalLine1');
  const modalLine2 = document.getElementById('modalLine2');
  const modalLine3 = document.getElementById('modalLine3');
  const modalLine4 = document.getElementById('modalLine4');
  const modalBarcodeSvg = document.getElementById('modalBarcodeSvg');
  const downloadBtn = document.getElementById('downloadBtn');
  const printBtn = document.getElementById('printBtn');

  let barcodeItems = [];
  let currentBarcodeData = null;

  const formatText = (type, itemName, salePrice, discount) => {
    switch (type) {
      case 'company':
        return defaultCompany;
      case 'item_name':
        return itemName || 'Sample Item';
      case 'sale_price':
        return `Sale Price: ${salePrice}`;
      case 'discount':
        return `Discount: ${discount}%`;
      default:
        return '';
    }
  };

  const renderBarcode = (svgElement, value) => {
    if (!window.JsBarcode || !svgElement) return;
    JsBarcode(svgElement, value || '123456789012', {
      format: 'CODE128',
      lineColor: '#222',
      width: 3,
      height: 100,
      displayValue: false,
      margin: 6,
    });
  };

  const updatePreview = () => {
    const itemName = itemNameEl.value.trim() || 'Sample Item';
    const itemCode = itemCodeEl.value.trim() || '123456789012';
    const salePrice = parseFloat(salePriceEl.value) || 0;
    const discount = parseFloat(discountEl.value) || 0;

    const headerText = formatText(headerEl.value, itemName, salePrice, discount);
    const line1Text = formatText(line1El.value, itemName, salePrice, discount);
    const line2Text = formatText(line2El.value, itemName, salePrice, discount);
    const line3Text = formatText(line3El.value, itemName, salePrice, discount);
    const line4Text = formatText(line4El.value, itemName, salePrice, discount);

    document.getElementById('previewHeader').textContent = headerText;
    document.getElementById('previewItemCode').textContent = itemCode;
    document.getElementById('previewLine1').textContent = line1Text;
    document.getElementById('previewLine2').textContent = line2Text;
    document.getElementById('previewLine3').textContent = line3Text;
    document.getElementById('previewLine4').textContent = line4Text;

    headerValue.value = headerText;
    line1Value.value = line1Text;
    line2Value.value = line2Text;
    line3Value.value = line3Text;
    line4Value.value = line4Text;
    barcodeValueEl.value = itemCode;

    renderBarcode(barcodeSvg, itemCode);
  };

  const fillItemList = (items) => {
    itemSelectEl.innerHTML = '<option value="">Select Item</option>';
    items.forEach(item => {
      const option = document.createElement('option');
      option.value = item.id;
      option.textContent = item.name;
      option.dataset.name = item.name;
      option.dataset.code = item.item_code || String(item.id);
      option.dataset.salePrice = item.sale_price ?? 0;
      option.dataset.discount = item.discount ?? 0;
      itemSelectEl.appendChild(option);
    });
  };

  const selectMatchingItem = () => {
    const selected = itemSelectEl.selectedOptions[0];
    if (!selected || !selected.value) {
      itemNameEl.value = '';
      itemCodeEl.value = '';
      salePriceEl.value = '';
      discountEl.value = 0;
      updatePreview();
      return;
    }

    itemNameEl.value = selected.dataset.name || selected.textContent;
    itemCodeEl.value = selected.dataset.code || selected.value;
    salePriceEl.value = selected.dataset.salePrice || 0;
    discountEl.value = selected.dataset.discount || 0;
    updatePreview();
  };

  const assignCode = () => {
    const selected = itemSelectEl.selectedOptions[0];
    if (selected && selected.dataset.code) {
      itemCodeEl.value = selected.dataset.code;
    } else if (itemNameEl.value.trim()) {
      itemCodeEl.value = itemNameEl.value.trim().toUpperCase().replace(/[^A-Z0-9]/g, '').slice(0, 12) || 'CODE123456';
    } else {
      itemCodeEl.value = `BC${Math.random().toString(36).substr(2, 8).toUpperCase()}`;
    }
    updatePreview();
  };

  const buildModalData = (data) => {
    currentBarcodeData = data;
    modalHeader.textContent = data.header || defaultCompany;
    modalItemCode.textContent = data.barcode_value || data.item_code || '123456789012';
    modalLine1.textContent = data.line_1 || '';
    modalLine2.textContent = data.line_2 || '';
    modalLine3.textContent = data.line_3 || '';
    modalLine4.textContent = data.line_4 || '';
    renderBarcode(modalBarcodeSvg, data.barcode_value || data.item_code || '123456789012');
  };

  const openPreviewModal = () => {
    const itemName = itemNameEl.value.trim() || 'Sample Item';
    const itemCode = itemCodeEl.value.trim() || '123456789012';
    const salePrice = parseFloat(salePriceEl.value) || 0;
    const discount = parseFloat(discountEl.value) || 0;

    const data = {
      header: formatText(headerEl.value, itemName, salePrice, discount),
      item_code: itemCode,
      barcode_value: itemCode,
      line_1: formatText(line1El.value, itemName, salePrice, discount),
      line_2: formatText(line2El.value, itemName, salePrice, discount),
      line_3: formatText(line3El.value, itemName, salePrice, discount),
      line_4: formatText(line4El.value, itemName, salePrice, discount),
    };

    buildModalData(data);
    modal.show();
  };

  const openSavedRowModal = (barcodeJson) => {
    const data = JSON.parse(barcodeJson);
    buildModalData(data);
    modal.show();
  };

  const downloadSvgAsPng = () => {
    const svg = modalBarcodeSvg;
    const serializer = new XMLSerializer();
    const svgString = serializer.serializeToString(svg);
    const canvas = document.createElement('canvas');
    const img = new Image();
    const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
    const url = URL.createObjectURL(svgBlob);

    img.onload = () => {
      canvas.width = img.width * 2;
      canvas.height = img.height * 2;
      const ctx = canvas.getContext('2d');
      ctx.fillStyle = '#ffffff';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
      URL.revokeObjectURL(url);
      const pngUrl = canvas.toDataURL('image/png');
      const link = document.createElement('a');
      link.href = pngUrl;
      link.download = `barcode-${currentBarcodeData?.item_code || 'label'}.png`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
    };

    img.src = url;
  };

  const printModalContent = () => {
    const printWindow = window.open('', '_blank');
    if (!printWindow) return;
    printWindow.document.write('<html><head><title>Print Barcode</title>');
    printWindow.document.write('<style>body{margin:0;padding:24px;font-family:sans-serif;text-align:center;}svg{width:100%;height:auto;}</style>');
    printWindow.document.write('</head><body>');
    printWindow.document.write(`<h3>${modalHeader.textContent}</h3>`);
    printWindow.document.write(modalBarcodeSvg.outerHTML);
    printWindow.document.write(`<p>${modalItemCode.textContent}</p>`);
    printWindow.document.write(`<p>${modalLine1.textContent}</p>`);
    printWindow.document.write(`<p>${modalLine2.textContent}</p>`);
    printWindow.document.write(`<p>${modalLine3.textContent}</p>`);
    printWindow.document.write(`<p>${modalLine4.textContent}</p>`);
    printWindow.document.write('</body></html>');
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
  };

  const init = async () => {
    try {
      const response = await fetch(itemsEndpoint, { headers: { Accept: 'application/json' } });
      if (!response.ok) throw new Error('Unable to load items.');
      barcodeItems = await response.json();
      fillItemList(barcodeItems);
    } catch (error) {
      console.error(error);
    }

    updatePreview();

    if (successMessage) {
      setTimeout(() => {
        successMessage.style.transition = 'opacity 0.35s ease';
        successMessage.style.opacity = '0';
        setTimeout(() => successMessage.remove(), 350);
      }, 2000);
    }

    document.querySelectorAll('.view-barcode-btn').forEach(button => {
      button.addEventListener('click', (event) => {
        event.stopPropagation();
        const row = event.target.closest('tr');
        if (!row) return;
        openSavedRowModal(row.dataset.barcode);
      });
    });

    document.querySelectorAll('.row-select').forEach(cb => {
      cb.addEventListener('click', (event) => event.stopPropagation());
    });

    document.querySelectorAll('.barcode-preview-row').forEach(row => {
      row.addEventListener('click', () => {
        openSavedRowModal(row.dataset.barcode);
      });
    });

    if (selectAllRows) {
      selectAllRows.addEventListener('change', () => {
        document.querySelectorAll('.row-select').forEach(cb => cb.checked = selectAllRows.checked);
      });
    }
  };

  itemSelectEl.addEventListener('change', selectMatchingItem);
  assignCodeBtn.addEventListener('click', assignCode);
  [itemCodeEl, salePriceEl, discountEl, headerEl, line1El, line2El, line3El, line4El].forEach(el => {
    el.addEventListener('input', updatePreview);
  });
  downloadBtn.addEventListener('click', downloadSvgAsPng);
  printBtn.addEventListener('click', printModalContent);
  document.addEventListener('DOMContentLoaded', init);
</script>
@endpush
