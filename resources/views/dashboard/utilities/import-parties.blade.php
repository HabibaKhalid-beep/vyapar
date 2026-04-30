@extends('layouts.app')

@section('title', 'Utilities - Import Parties')
@section('description', 'Import party and customer data in bulk.')
@section('page', 'import-parties')

@push('styles')
  <style>
    .import-parties-page {
      padding: 28px;
      background: #f4f4f7;
      min-height: calc(100vh - 20px);
    }

    .import-parties-shell {
      background: #fff;
      border: 1px solid #ececf3;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 18px 40px rgba(37, 44, 97, 0.08);
    }

    .import-parties-header {
      padding: 26px 30px 18px;
      border-bottom: 1px solid #ececf3;
      background: linear-gradient(180deg, #ffffff 0%, #fcfcff 100%);
    }

    .import-parties-header h1 {
      margin: 0;
      font-size: 2rem;
      color: #24304a;
      font-weight: 700;
    }

    .import-parties-header p {
      margin: 8px 0 0;
      color: #6c748a;
      font-size: 1rem;
    }

    .import-parties-grid {
      display: grid;
      grid-template-columns: minmax(320px, 0.92fr) minmax(420px, 1.28fr);
      min-height: 620px;
    }

    .import-side {
      padding: 34px 30px;
    }

    .import-side + .import-side {
      border-left: 1px solid #ececf3;
    }

    .import-caption {
      text-align: center;
      color: #323a52;
      font-size: 1.5rem;
      font-weight: 700;
      margin-bottom: 22px;
    }

    .download-copy {
      text-align: center;
      color: #465168;
      font-size: 1.2rem;
      line-height: 1.45;
      margin: 0 auto 28px;
      max-width: 320px;
    }

    .file-illustration {
      width: 124px;
      height: 148px;
      margin: 0 auto 26px;
      border-radius: 24px;
      background: linear-gradient(180deg, #2488ed 0%, #1671d9 100%);
      position: relative;
      box-shadow: 0 16px 24px rgba(25, 110, 205, 0.28);
    }

    .file-illustration::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 36px;
      height: 36px;
      background: #a8d2ff;
      clip-path: polygon(0 0, 100% 0, 100% 100%);
      border-top-right-radius: 24px;
    }

    .file-illustration::after {
      content: 'xls';
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 2.2rem;
      font-weight: 700;
      text-transform: lowercase;
    }

    .download-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      min-width: 160px;
      padding: 13px 22px;
      border-radius: 12px;
      background: linear-gradient(180deg, #2d92f7 0%, #156ed8 100%);
      color: #fff;
      font-weight: 700;
      text-decoration: none;
      box-shadow: 0 10px 18px rgba(28, 120, 225, 0.24);
    }

    .download-button:hover {
      color: #fff;
      background: linear-gradient(180deg, #2289ef 0%, #0f66cf 100%);
    }

    .upload-title {
      text-align: center;
      color: #40485e;
      font-size: 1.55rem;
      font-weight: 500;
      margin-bottom: 24px;
    }

    .upload-title strong {
      font-weight: 800;
    }

    .party-dropzone {
      width: 100%;
      min-height: 330px;
      border: 2px dashed #d2d7e4;
      border-radius: 20px;
      background: #fff;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      cursor: pointer;
      transition: 0.2s ease;
      padding: 24px;
    }

    .party-dropzone.is-dragging {
      background: #eff6ff;
      border-color: #5aa8f7;
      transform: translateY(-2px);
    }

    .upload-file-illustration {
      width: 140px;
      height: 168px;
      margin: 0 auto 24px;
      border-radius: 24px;
      background: linear-gradient(180deg, #2b8aec 0%, #1a76dc 100%);
      position: relative;
      box-shadow: 0 18px 28px rgba(29, 114, 215, 0.25);
    }

    .upload-file-illustration::before {
      content: '';
      position: absolute;
      top: 0;
      right: 0;
      width: 42px;
      height: 42px;
      background: #b8dbff;
      clip-path: polygon(0 0, 100% 0, 100% 100%);
      border-top-right-radius: 24px;
    }

    .upload-file-illustration::after {
      content: '\2191';
      position: absolute;
      inset: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #fff;
      font-size: 4.3rem;
      font-weight: 700;
    }

    .party-dropzone p {
      margin: 0;
      color: #434c63;
      font-size: 1.25rem;
      line-height: 1.45;
    }

    .party-dropzone .browse-text {
      color: #2585eb;
      font-weight: 700;
    }

    .selected-file-text {
      margin-top: 18px;
      text-align: center;
      color: #657089;
      font-weight: 600;
    }

    .import-parties-modal .modal-dialog {
      max-width: 98vw;
      margin: 0.75rem auto;
    }

    .import-parties-modal .modal-content {
      border: 0;
      border-radius: 18px;
      overflow: hidden;
      min-height: 92vh;
    }

    .import-parties-modal .modal-header {
      border-bottom: 1px solid #e7ebf4;
      padding: 18px 24px 10px;
      align-items: flex-start;
    }

    .import-parties-modal .modal-title {
      color: #28344f;
      font-size: 2rem;
      font-weight: 700;
    }

    .party-tabs {
      display: flex;
      gap: 24px;
      padding: 0 24px;
      border-bottom: 1px solid #e7ebf4;
    }

    .party-tab {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      padding: 14px 0 12px;
      background: transparent;
      border: 0;
      border-bottom: 3px solid transparent;
      color: #364154;
      font-size: 1.1rem;
      font-weight: 700;
    }

    .party-tab.is-active {
      color: #2d7ed8;
      border-bottom-color: #2d7ed8;
    }

    .party-tab.valid i {
      color: #21b65a;
      font-size: 1.35rem;
    }

    .party-tab.error i {
      color: #ef4d4d;
      font-size: 1.35rem;
    }

    .party-modal-section {
      display: none;
      padding: 8px 0 0;
    }

    .party-modal-section.is-active {
      display: block;
    }

    .party-section-title {
      padding: 0 24px 14px;
      color: #212a3f;
      font-size: 1.35rem;
      font-weight: 700;
    }

    .party-table-wrap {
      overflow: auto;
      padding: 0 24px 18px;
      min-height: 560px;
    }

    .party-preview-table {
      width: 100%;
      min-width: 1300px;
      border-collapse: separate;
      border-spacing: 0;
    }

    .party-preview-table th,
    .party-preview-table td {
      padding: 13px 14px;
      border-right: 1px solid #e7ebf4;
      border-bottom: 1px solid #e7ebf4;
      white-space: nowrap;
      font-size: 1rem;
    }

    .party-preview-table th {
      background: #fff;
      color: #456276;
      font-weight: 500;
      position: sticky;
      top: 0;
      z-index: 1;
    }

    .party-preview-table tbody tr:nth-child(odd) td {
      background: #eef2f8;
    }

    .party-preview-table tbody tr:nth-child(even) td {
      background: #fff;
    }

    .party-preview-table .error-indicator {
      width: 52px;
      text-align: center;
      background: #fff !important;
    }

    .party-preview-table .error-indicator i {
      color: #ef4d4d;
      font-size: 1.25rem;
    }

    .party-preview-table .error-name {
      background: #f7b1b6 !important;
    }

    .party-input {
      width: 100%;
      min-width: 120px;
      border: 1px solid transparent;
      border-radius: 8px;
      background: transparent;
      color: #1f2738;
      padding: 6px 8px;
      outline: none;
    }

    .party-input:focus,
    .party-select:focus {
      border-color: #71b5ff;
      background: #fff;
    }

    .party-select {
      width: 100%;
      min-width: 120px;
      border: 1px solid transparent;
      border-radius: 8px;
      background: transparent;
      color: #1f2738;
      padding: 6px 8px;
      outline: none;
    }

    .no-rows {
      min-height: 360px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: #59657f;
      font-size: 1.2rem;
    }

    .modal-import-footer {
      display: flex;
      justify-content: flex-end;
      padding: 0 24px 22px;
    }

    .import-valid-button {
      min-width: 210px;
      border: 0;
      border-radius: 12px;
      background: #b8b8b8;
      color: #fff;
      padding: 14px 20px;
      font-size: 1rem;
      font-weight: 700;
    }

    .import-valid-button.is-ready {
      background: linear-gradient(180deg, #1f7de0 0%, #0d63bd 100%);
      box-shadow: 0 10px 18px rgba(13, 99, 189, 0.24);
    }

    .import-toast {
      position: fixed;
      top: 22px;
      right: 22px;
      z-index: 1080;
      max-width: 420px;
      border-radius: 10px;
      padding: 16px 18px;
      color: #fff;
      background: #27a857;
      box-shadow: 0 14px 24px rgba(34, 52, 92, 0.18);
      display: none;
    }

    .import-toast.is-visible {
      display: block;
    }

    .import-toast.error {
      background: #e85a5a;
    }

    @media (max-width: 1199.98px) {
      .import-parties-grid {
        grid-template-columns: 1fr;
      }

      .import-side + .import-side {
        border-left: 0;
        border-top: 1px solid #ececf3;
      }
    }

    @media (max-width: 767.98px) {
      .import-parties-page {
        padding: 16px;
      }

      .party-tabs {
        gap: 14px;
        flex-wrap: wrap;
      }

      .party-tab {
        font-size: 0.98rem;
      }

      .modal-import-footer {
        padding-left: 16px;
        padding-right: 16px;
      }

      .import-valid-button {
        width: 100%;
      }
    }
  </style>
@endpush

@section('content')
  <div class="import-parties-page">
    <div class="import-parties-shell">
      <div class="import-parties-header">
        <h1>Import Parties</h1>
        <p>Download the sample file, upload your formatted Excel sheet, review valid/error parties, then import only valid data.</p>
      </div>

      <div class="import-parties-grid">
        <section class="import-side text-center">
          <div class="import-caption">Import Excel</div>
          <p class="download-copy">Download <strong>.xls/ .xlsx (excel sheet)</strong> template file to enter party data.</p>

          <div class="file-illustration"></div>

          <a href="{{ route('utilities.import-parties.sample') }}" class="download-button">
            Download
          </a>
        </section>

        <section class="import-side d-flex flex-column justify-content-center">
          <div class="upload-title">Upload your <strong>.xls/ .xlsx (excel sheet)</strong></div>

          <label class="party-dropzone" id="partyDropzone" for="partyImportFile">
            <div>
              <div class="upload-file-illustration"></div>
              <p>Drag and drop or <span class="browse-text">Click here to Browse</span><br>formatted excel file to continue</p>
            </div>
          </label>

          <input id="partyImportFile" type="file" accept=".xls,.xlsx,.csv" hidden>
          <div class="selected-file-text" id="selectedPartyFileText">No file selected yet.</div>
        </section>
      </div>
    </div>
  </div>

  <div class="import-toast" id="partyImportToast"></div>
@endsection

@section('modals')
  <div class="modal fade import-parties-modal" id="partyImportPreviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Import Parties</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="party-tabs">
          <button class="party-tab valid is-active" type="button" data-target="validPartiesSection" id="validPartiesTab">
            <i class="bi bi-check-circle-fill"></i>
            <span id="validPartiesTabLabel">Valid Parties : 0</span>
          </button>
          <button class="party-tab error" type="button" data-target="errorPartiesSection" id="errorPartiesTab">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <span id="errorPartiesTabLabel">Parties with Errors : 0</span>
          </button>
        </div>

        <div class="modal-body p-0">
          <section class="party-modal-section is-active" id="validPartiesSection">
            <div class="party-section-title">Valid Parties</div>
            <div class="party-table-wrap">
              <table class="party-preview-table" id="validPartiesTable">
                <thead>
                  <tr>
                    <th>Name*</th>
                    <th>Contact No.</th>
                    <th>Email ID</th>
                    <th>Address</th>
                    <th>Opening Balance</th>
                    <th>Opening Date (dd/MM/yyyy)</th>
                    <th>Shipping Address</th>
                    <th>Party Type</th>
                    <th>Transaction Type</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
              <div class="no-rows" id="validNoRows">No Rows To Show</div>
            </div>
          </section>

          <section class="party-modal-section" id="errorPartiesSection">
            <div class="party-section-title">Parties with Errors</div>
            <div class="party-table-wrap">
              <table class="party-preview-table" id="errorPartiesTable">
                <thead>
                  <tr>
                    <th></th>
                    <th>Name*</th>
                    <th>Contact No.</th>
                    <th>Email ID</th>
                    <th>Address</th>
                    <th>Opening Balance</th>
                    <th>Opening Date (dd/MM/yyyy)</th>
                    <th>Shipping Address</th>
                    <th>Party Type</th>
                    <th>Transaction Type</th>
                    <th>Error</th>
                  </tr>
                </thead>
                <tbody></tbody>
              </table>
              <div class="no-rows" id="errorNoRows">No Rows To Show</div>
            </div>
          </section>
        </div>

        <div class="modal-import-footer">
          <button class="import-valid-button" type="button" id="importValidPartiesButton">Import 0 Valid Parties</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const fileInput = document.getElementById('partyImportFile');
      const dropzone = document.getElementById('partyDropzone');
      const selectedFileText = document.getElementById('selectedPartyFileText');
      const toast = document.getElementById('partyImportToast');
      const modalElement = document.getElementById('partyImportPreviewModal');
      const previewModal = new bootstrap.Modal(modalElement);
      const validPartiesTableBody = document.querySelector('#validPartiesTable tbody');
      const errorPartiesTableBody = document.querySelector('#errorPartiesTable tbody');
      const validPartiesTabLabel = document.getElementById('validPartiesTabLabel');
      const errorPartiesTabLabel = document.getElementById('errorPartiesTabLabel');
      const validNoRows = document.getElementById('validNoRows');
      const errorNoRows = document.getElementById('errorNoRows');
      const importValidPartiesButton = document.getElementById('importValidPartiesButton');
      const previewUrl = "{{ route('utilities.import-parties.preview') }}";
      const importUrl = "{{ route('utilities.import-parties.valid-parties') }}";
      const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
      let importedRows = [];

      function normalizeText(value) {
        return String(value ?? '').trim();
      }

      function normalizeBoolean(value) {
        if (value === true || value === false) {
          return value;
        }

        const text = normalizeText(value).toLowerCase();
        if (['1', 'true', 'yes', 'on'].includes(text)) {
          return true;
        }
        if (['0', 'false', 'no', 'off', ''].includes(text)) {
          return false;
        }
        return false;
      }

      function isValidEmail(value) {
        if (!normalizeText(value)) {
          return true;
        }

        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(normalizeText(value));
      }

      function isValidDate(value) {
        if (!normalizeText(value)) {
          return true;
        }

        const text = normalizeText(value);
        if (/^\d{4}-\d{2}-\d{2}$/.test(text)) {
          return !Number.isNaN(Date.parse(text));
        }

        if (/^\d{2}\/\d{2}\/\d{4}$/.test(text)) {
          const [day, month, year] = text.split('/');
          const iso = `${year}-${month}-${day}`;
          return !Number.isNaN(Date.parse(iso));
        }

        return false;
      }

      function getValidRows() {
        return importedRows.filter(function (row) {
          return !row.errors.length;
        });
      }

      function getInvalidRows() {
        return importedRows.filter(function (row) {
          return row.errors.length;
        });
      }

      function validateRows() {
        const nameCounts = {};

        importedRows.forEach(function (row) {
          const key = normalizeText(row.data.name).toLowerCase();
          if (key) {
            nameCounts[key] = (nameCounts[key] || 0) + 1;
          }
        });

        importedRows.forEach(function (row) {
          const errors = [];
          const data = row.data;
          const nameKey = normalizeText(data.name).toLowerCase();
          const openingBalance = normalizeText(data.opening_balance);
          const creditLimitAmount = normalizeText(data.credit_limit_amount);
          const partyType = normalizeText(data.party_type).toLowerCase();
          const transactionType = normalizeText(data.transaction_type).toLowerCase();

          if (!normalizeText(data.name)) {
            errors.push('Name is required.');
          }

          if (nameKey && nameCounts[nameKey] > 1) {
            errors.push('Duplicate party name in the uploaded file.');
          }

          if (!isValidEmail(data.email)) {
            errors.push('Email must be valid.');
          }

          if (openingBalance && isNaN(Number(openingBalance))) {
            errors.push('Opening balance must be numeric.');
          }

          if (!isValidDate(data.as_of_date)) {
            errors.push('Opening date format is invalid.');
          }

          if (partyType && !['customer', 'supplier', 'both'].includes(partyType)) {
            errors.push('Party type must be customer, supplier, or both.');
          }

          if (transactionType && !['receive', 'pay'].includes(transactionType)) {
            errors.push('Transaction type must be receive or pay.');
          }

          if (normalizeBoolean(data.credit_limit_enabled) && !creditLimitAmount) {
            errors.push('Credit limit amount is required when credit limit is enabled.');
          }

          if (creditLimitAmount && isNaN(Number(creditLimitAmount))) {
            errors.push('Credit limit amount must be numeric.');
          }

          row.errors = Array.from(new Set(errors));
        });
      }

      function toInput(value, rowIndex, field, extraClass) {
        return `<input class="party-input ${extraClass || ''}" data-row-index="${rowIndex}" data-field="${field}" value="${escapeHtml(value)}">`;
      }

      function toSelect(value, rowIndex, field, options) {
        const current = normalizeText(value).toLowerCase();
        const renderedOptions = options.map(function (option) {
          const selected = current === option.value ? 'selected' : '';
          return `<option value="${option.value}" ${selected}>${option.label}</option>`;
        }).join('');
        return `<select class="party-select" data-row-index="${rowIndex}" data-field="${field}">${renderedOptions}</select>`;
      }

      function showToast(message, isError) {
        toast.textContent = message;
        toast.classList.toggle('error', !!isError);
        toast.classList.add('is-visible');
        clearTimeout(showToast.timeoutId);
        showToast.timeoutId = setTimeout(function () {
          toast.classList.remove('is-visible');
        }, 3500);
      }

      function setActiveTab(targetId) {
        document.querySelectorAll('.party-tab').forEach(function (tab) {
          tab.classList.toggle('is-active', tab.dataset.target === targetId);
        });

        document.querySelectorAll('.party-modal-section').forEach(function (section) {
          section.classList.toggle('is-active', section.id === targetId);
        });
      }

      function escapeHtml(value) {
        return String(value ?? '')
          .replace(/&/g, '&amp;')
          .replace(/</g, '&lt;')
          .replace(/>/g, '&gt;')
          .replace(/"/g, '&quot;')
          .replace(/'/g, '&#039;');
      }

      function renderTables() {
        validPartiesTableBody.innerHTML = '';
        errorPartiesTableBody.innerHTML = '';

        const validParties = getValidRows();
        const invalidParties = getInvalidRows();

        validPartiesTabLabel.textContent = 'Valid Parties : ' + validParties.length;
        errorPartiesTabLabel.textContent = 'Parties with Errors : ' + invalidParties.length;
        importValidPartiesButton.textContent = 'Import ' + validParties.length + ' Valid Parties';
        importValidPartiesButton.classList.toggle('is-ready', validParties.length > 0);
        importValidPartiesButton.disabled = validParties.length === 0;

        validNoRows.style.display = validParties.length ? 'none' : 'flex';
        errorNoRows.style.display = invalidParties.length ? 'none' : 'flex';

        validParties.forEach(function (row) {
          const rowIndex = importedRows.indexOf(row);
          validPartiesTableBody.insertAdjacentHTML('beforeend', `
            <tr>
              <td>${toInput(row.data.name, rowIndex, 'name')}</td>
              <td>${toInput(row.data.phone, rowIndex, 'phone')}</td>
              <td>${toInput(row.data.email, rowIndex, 'email')}</td>
              <td>${toInput(row.data.address, rowIndex, 'address')}</td>
              <td>${toInput(row.data.opening_balance, rowIndex, 'opening_balance')}</td>
              <td>${toInput(row.data.as_of_date, rowIndex, 'as_of_date')}</td>
              <td>${toInput(row.data.shipping_address, rowIndex, 'shipping_address')}</td>
              <td>${toSelect(row.data.party_type, rowIndex, 'party_type', [
                { value: '', label: 'Select' },
                { value: 'customer', label: 'customer' },
                { value: 'supplier', label: 'supplier' },
                { value: 'both', label: 'both' }
              ])}</td>
              <td>${toSelect(row.data.transaction_type, rowIndex, 'transaction_type', [
                { value: '', label: 'Select' },
                { value: 'receive', label: 'receive' },
                { value: 'pay', label: 'pay' }
              ])}</td>
            </tr>
          `);
        });

        invalidParties.forEach(function (row) {
          const rowIndex = importedRows.indexOf(row);
          errorPartiesTableBody.insertAdjacentHTML('beforeend', `
            <tr>
              <td class="error-indicator"><i class="bi bi-exclamation-triangle-fill"></i></td>
              <td class="error-name">${toInput(row.data.name, rowIndex, 'name')}</td>
              <td>${toInput(row.data.phone, rowIndex, 'phone')}</td>
              <td>${toInput(row.data.email, rowIndex, 'email')}</td>
              <td>${toInput(row.data.address, rowIndex, 'address')}</td>
              <td>${toInput(row.data.opening_balance, rowIndex, 'opening_balance')}</td>
              <td>${toInput(row.data.as_of_date, rowIndex, 'as_of_date')}</td>
              <td>${toInput(row.data.shipping_address, rowIndex, 'shipping_address')}</td>
              <td>${toSelect(row.data.party_type, rowIndex, 'party_type', [
                { value: '', label: 'Select' },
                { value: 'customer', label: 'customer' },
                { value: 'supplier', label: 'supplier' },
                { value: 'both', label: 'both' }
              ])}</td>
              <td>${toSelect(row.data.transaction_type, rowIndex, 'transaction_type', [
                { value: '', label: 'Select' },
                { value: 'receive', label: 'receive' },
                { value: 'pay', label: 'pay' }
              ])}</td>
              <td>${escapeHtml((row.errors || []).join(', '))}</td>
            </tr>
          `);
        });

        document.querySelectorAll('.party-input, .party-select').forEach(function (input) {
          input.addEventListener('input', handleFieldChange);
          input.addEventListener('change', handleFieldChange);
        });
      }

      function handleFieldChange(event) {
        const rowIndex = Number(event.target.dataset.rowIndex);
        const field = event.target.dataset.field;

        if (Number.isNaN(rowIndex) || !importedRows[rowIndex]) {
          return;
        }

        importedRows[rowIndex].data[field] = event.target.value;
        validateRows();
        renderTables();
      }

      async function previewFile(file) {
        const formData = new FormData();
        formData.append('import_file', file);

        try {
          const response = await fetch(previewUrl, {
            method: 'POST',
            headers: {
              'X-CSRF-TOKEN': csrfToken,
              'Accept': 'application/json'
            },
            body: formData
          });

          const result = await response.json();

          if (!response.ok || !result.success) {
            throw new Error(result.message || 'Unable to preview import file.');
          }

          importedRows = []
            .concat(result.valid_parties || [], result.invalid_parties || [])
            .sort(function (left, right) {
              return Number(left.row || 0) - Number(right.row || 0);
            })
            .map(function (row) {
              return {
                row: row.row,
                data: Object.assign({
                  name: '',
                  phone: '',
                  phone_number_2: '',
                  ptcl_number: '',
                  email: '',
                  city: '',
                  address: '',
                  billing_address: '',
                  shipping_address: '',
                  opening_balance: '',
                  as_of_date: '',
                  credit_limit_enabled: 0,
                  credit_limit_amount: '',
                  due_days: '',
                  custom_fields: '',
                  transaction_type: '',
                  party_type: '',
                  party_group: ''
                }, row.data || {}),
                errors: row.errors || []
              };
            });

          validateRows();
          renderTables();
          setActiveTab(getValidRows().length ? 'validPartiesSection' : 'errorPartiesSection');
          previewModal.show();
        } catch (error) {
          showToast(error.message, true);
        }
      }

      fileInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        if (!file) {
          return;
        }

        selectedFileText.textContent = 'Selected file: ' + file.name;
        previewFile(file);
      });

      ['dragenter', 'dragover'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
          event.preventDefault();
          dropzone.classList.add('is-dragging');
        });
      });

      ['dragleave', 'dragend', 'drop'].forEach(function (eventName) {
        dropzone.addEventListener(eventName, function (event) {
          event.preventDefault();
          dropzone.classList.remove('is-dragging');
        });
      });

      dropzone.addEventListener('drop', function (event) {
        const files = event.dataTransfer.files;
        if (!files || !files.length) {
          return;
        }

        fileInput.files = files;
        selectedFileText.textContent = 'Selected file: ' + files[0].name;
        previewFile(files[0]);
      });

      document.querySelectorAll('.party-tab').forEach(function (tab) {
        tab.addEventListener('click', function () {
          setActiveTab(tab.dataset.target);
        });
      });

      importValidPartiesButton.addEventListener('click', async function () {
        const validParties = getValidRows();

        if (!validParties.length) {
          setActiveTab('errorPartiesSection');
          return;
        }

        const originalText = importValidPartiesButton.textContent;
        importValidPartiesButton.disabled = true;
        importValidPartiesButton.textContent = 'Importing...';

        try {
          const payload = {
            parties: validParties.map(function (row) {
              return {
                name: normalizeText(row.data.name),
                phone: normalizeText(row.data.phone),
                phone_number_2: normalizeText(row.data.phone_number_2),
                ptcl_number: normalizeText(row.data.ptcl_number),
                email: normalizeText(row.data.email),
                city: normalizeText(row.data.city),
                address: normalizeText(row.data.address),
                billing_address: normalizeText(row.data.billing_address),
                shipping_address: normalizeText(row.data.shipping_address),
                opening_balance: normalizeText(row.data.opening_balance),
                as_of_date: normalizeText(row.data.as_of_date),
                credit_limit_enabled: normalizeBoolean(row.data.credit_limit_enabled),
                credit_limit_amount: normalizeText(row.data.credit_limit_amount),
                due_days: normalizeText(row.data.due_days),
                custom_fields: normalizeText(row.data.custom_fields),
                transaction_type: normalizeText(row.data.transaction_type).toLowerCase(),
                party_type: normalizeText(row.data.party_type).toLowerCase(),
                party_group: normalizeText(row.data.party_group)
              };
            })
          };

          const response = await fetch(importUrl, {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(payload)
          });

          const result = await response.json();

          if (!response.ok || !result.success) {
            throw new Error(result.message || 'Unable to import valid parties.');
          }

          showToast((result.created || validParties.length) + ' parties imported successfully.', false);
          importedRows = importedRows.filter(function (row) {
            return row.errors.length;
          });
          renderTables();
          previewModal.hide();
        } catch (error) {
          showToast(error.message, true);
        } finally {
          importValidPartiesButton.disabled = false;
          importValidPartiesButton.textContent = originalText;
        }
      });
    });
  </script>
@endpush
