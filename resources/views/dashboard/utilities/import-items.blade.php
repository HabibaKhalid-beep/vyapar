@extends('layouts.app')

@section('title', 'Utilities - Import Items')
@section('description', 'Import items into the system using CSV or Excel files.')
@section('page', 'import-items')

@php
  $itemFields = [
      'Item name*',
      'Item code',
      'Description',
      'Category',
      'HSN',
      'Default MRP',
      'Disc % on MRP for Sale Price',
      'Sale price',
      'Purchase price',
      'Wholesale price',
      'Minimum wholesale quantity',
      'Disc % on mrp for wholesale price',
      'Discount Type',
      'Sale Discount',
      'Opening stock quantity',
      'Minimum stock quantity',
      'Item Location',
      'Tax Rate',
      'Inclusive Of Tax',
      'Base Unit (x)',
      'Secondary Unit (y)',
      'Conversion Rate (n) (x = ny)',
  ];

  $batchFields = [
      'Item name*',
      'Batch No',
      'Model No',
      'Size',
      'Mfg date',
      'Exp date',
      'Opening stock quantity',
      'MRP',
  ];
@endphp

@push('styles')
  <style>
    .import-items-page {
      padding: 28px;
      background: #f4f4f7;
      min-height: calc(100vh - 20px);
    }

    .import-items-shell {
      background: #fff;
      border: 1px solid #ececf3;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 18px 40px rgba(37, 44, 97, 0.08);
    }

    .import-items-header {
      padding: 26px 30px 18px;
      border-bottom: 1px solid #ececf3;
      background: linear-gradient(180deg, #ffffff 0%, #fcfcff 100%);
    }

    .import-items-header h1 {
      margin: 0;
      font-size: 2rem;
      line-height: 1.2;
      color: #24304a;
      font-weight: 700;
    }

    .import-items-header p {
      margin: 10px 0 0;
      color: #6c748a;
      font-size: 1rem;
    }

    .import-stage {
      display: none;
    }

    .import-stage.is-active {
      display: block;
    }

    .import-entry {
      display: grid;
      grid-template-columns: minmax(320px, 1fr) minmax(360px, 1.15fr);
      min-height: 640px;
    }

    .import-panel {
      padding: 34px 30px;
    }

    .import-panel + .import-panel {
      border-left: 1px solid #ececf3;
    }

    .steps-title {
      text-align: center;
      color: #30364b;
      font-size: 1.9rem;
      font-weight: 700;
      margin-bottom: 28px;
    }

    .step-block + .step-block {
      margin-top: 28px;
    }

    .step-label {
      color: #ff3567;
      font-weight: 800;
      font-size: 1rem;
      letter-spacing: 0.18em;
      margin-bottom: 10px;
      text-align: center;
    }

    .step-text {
      margin: 0;
      color: #4b5368;
      font-size: 1.18rem;
      line-height: 1.5;
    }

    .sample-button {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 10px;
      margin-top: 18px;
      min-width: 182px;
      padding: 13px 22px;
      border-radius: 999px;
      border: 2px solid #4fa6ff;
      color: #2184ee;
      background: #fff;
      font-weight: 700;
      font-size: 1rem;
      text-decoration: none;
      transition: 0.2s ease;
    }

    .sample-button:hover {
      background: #eff7ff;
      color: #0d74dd;
    }

    .sample-preview {
      margin-top: 26px;
      border-radius: 16px;
      border: 1px solid #dfe6f5;
      background: linear-gradient(180deg, #f9fbff 0%, #f2f6fb 100%);
      box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.9);
      overflow: hidden;
    }

    .sample-preview table {
      width: 100%;
      border-collapse: collapse;
    }

    .sample-preview th,
    .sample-preview td {
      padding: 7px 8px;
      font-size: 0.76rem;
      border: 1px solid #dfe6f5;
      text-align: left;
      white-space: nowrap;
    }

    .sample-preview th {
      background: #3d94e8;
      color: #fff;
      font-weight: 700;
    }

    .sample-preview td {
      color: #4f5a70;
      background: rgba(255, 255, 255, 0.86);
    }

    .upload-panel {
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      text-align: center;
      background:
        radial-gradient(circle at top left, rgba(62, 170, 255, 0.08), transparent 35%),
        radial-gradient(circle at bottom right, rgba(255, 64, 129, 0.08), transparent 28%),
        #fff;
    }

    .upload-title {
      color: #40485e;
      font-size: 1.7rem;
      margin-bottom: 24px;
      font-weight: 500;
    }

    .upload-title strong {
      font-weight: 800;
    }

    .upload-dropzone {
      width: 100%;
      min-height: 348px;
      border: 2px dashed #5ab0ff;
      border-radius: 22px;
      background: #f6fbff;
      padding: 30px 28px;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: 0.2s ease;
      cursor: pointer;
    }

    .upload-dropzone.is-dragging {
      background: #edf7ff;
      border-color: #2184ee;
      transform: scale(0.995);
    }

    .upload-dropzone i {
      font-size: 4.4rem;
      color: #aad7ff;
      display: block;
      margin-bottom: 18px;
    }

    .upload-dropzone h3 {
      margin: 0;
      font-size: 2rem;
      color: #687289;
      font-weight: 500;
    }

    .upload-dropzone p {
      margin: 16px 0 0;
      font-size: 1.4rem;
      color: #7c859a;
    }

    .upload-button {
      margin-top: 24px;
      border: 0;
      border-radius: 999px;
      background: linear-gradient(180deg, #ff3a72 0%, #ff1654 100%);
      color: #fff;
      padding: 15px 30px;
      font-size: 1.15rem;
      font-weight: 700;
      box-shadow: 0 10px 18px rgba(255, 32, 89, 0.3);
    }

    .upload-button:hover {
      background: linear-gradient(180deg, #ff2c68 0%, #f0124f 100%);
      color: #fff;
    }

    .upload-file-meta {
      margin-top: 16px;
      color: #5e6880;
      font-size: 0.96rem;
      font-weight: 600;
    }

    .mapping-stage {
      padding: 28px 18px 24px;
      background: #fff;
    }

    .mapping-topbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 14px;
      padding: 0 14px 18px;
    }

    .mapping-topbar h2 {
      margin: 0;
      color: #24304a;
      font-size: 2rem;
      font-weight: 700;
    }

    .mapping-file-pill {
      display: inline-flex;
      align-items: center;
      gap: 10px;
      border-radius: 999px;
      background: #eef7ff;
      color: #287fdc;
      padding: 10px 16px;
      font-weight: 700;
    }

    .mapping-card {
      border: 1px solid #e7ebf4;
      border-radius: 20px;
      overflow: hidden;
      box-shadow: 0 18px 35px rgba(30, 50, 94, 0.08);
    }

    .mapping-card-header {
      padding: 24px 24px 18px;
      text-align: center;
      border-bottom: 1px solid #edf0f7;
      background: linear-gradient(180deg, #ffffff 0%, #fbfcff 100%);
    }

    .mapping-card-header h3 {
      margin: 0 0 6px;
      color: #32415f;
      font-size: 1.9rem;
      font-weight: 700;
    }

    .mapping-card-header p {
      margin: 0;
      color: #778199;
      font-size: 1rem;
    }

    .mapping-columns {
      display: grid;
      grid-template-columns: 1.2fr 0.8fr;
      gap: 0;
    }

    .mapping-section + .mapping-section {
      border-left: 1px solid #edf0f7;
    }

    .mapping-section-title {
      padding: 18px 18px 10px;
      font-size: 1.75rem;
      font-weight: 700;
      color: #1f2738;
      text-align: center;
    }

    .mapping-table-wrap {
      padding: 0 10px 18px;
      max-height: 560px;
      overflow: auto;
    }

    .mapping-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 0;
    }

    .mapping-table th,
    .mapping-table td {
      padding: 14px 18px;
      border-bottom: 1px solid #edf0f7;
      font-size: 1rem;
    }

    .mapping-table th {
      background: #eff3f9;
      color: #3d4a66;
      font-weight: 800;
      position: sticky;
      top: 0;
      z-index: 2;
    }

    .mapping-table tbody tr:nth-child(even) td {
      background: #f7f9fc;
    }

    .mapping-table tbody tr:nth-child(odd) td {
      background: #ffffff;
    }

    .mapping-table select {
      width: 100%;
      min-width: 180px;
      border: 0;
      background: transparent;
      color: #1f2738;
      font-size: 1rem;
      padding-right: 12px;
      outline: none;
      cursor: pointer;
    }

    .mapping-actions {
      display: flex;
      justify-content: flex-end;
      padding: 18px 22px 24px;
      background: #fff;
    }

    .proceed-button {
      min-width: 180px;
      padding: 14px 28px;
      border: 0;
      border-radius: 14px;
      background: linear-gradient(180deg, #2f97ff 0%, #1c7de0 100%);
      color: #fff;
      font-size: 1.2rem;
      font-weight: 700;
      box-shadow: 0 10px 18px rgba(34, 125, 226, 0.28);
    }

    .import-success {
      display: none;
      padding: 18px 24px 30px;
    }

    .import-success.is-visible {
      display: block;
    }

    .success-card {
      border-radius: 18px;
      border: 1px solid #dcefdc;
      background: linear-gradient(180deg, #f8fff7 0%, #effbed 100%);
      padding: 18px 20px;
      color: #2c6b2f;
      font-weight: 600;
    }

    .results-stage {
      padding: 26px 18px 10px;
      background: #fff;
    }

    .results-shell {
      border: 1px solid #e7ebf4;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 18px 35px rgba(30, 50, 94, 0.08);
      background: #fff;
    }

    .results-grid {
      overflow: auto;
      max-height: 620px;
    }

    .results-table {
      width: 100%;
      min-width: 1600px;
      border-collapse: separate;
      border-spacing: 0;
    }

    .results-table th,
    .results-table td {
      padding: 13px 12px;
      border-right: 1px solid #e7ebf4;
      border-bottom: 1px solid #e7ebf4;
      font-size: 0.98rem;
      white-space: nowrap;
    }

    .results-table th {
      background: #ffffff;
      color: #375669;
      font-weight: 500;
      text-transform: uppercase;
      position: sticky;
      top: 0;
      z-index: 2;
    }

    .results-table td {
      color: #1f2738;
      background: #fff;
    }

    .results-table tbody tr:nth-child(odd) td {
      background: #eef2f8;
    }

    .results-table .is-error-row td {
      background: #fff4f6;
    }

    .result-link {
      display: inline-flex;
      align-items: center;
      gap: 8px;
      color: #1976d2;
      font-weight: 500;
      text-decoration: none;
    }

    .result-link:hover {
      color: #125ca5;
    }

    .results-footer {
      display: flex;
      justify-content: flex-end;
      gap: 16px;
      padding: 18px 8px 4px;
    }

    .results-footer .btn {
      min-width: 230px;
      padding: 14px 24px;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 700;
    }

    .results-footer .btn-outline-danger {
      border-width: 2px;
      color: #cf5d3f;
      background: #fff;
    }

    .results-footer .btn-primary {
      background: linear-gradient(180deg, #1f7de0 0%, #0d63bd 100%);
      border: 0;
      box-shadow: 0 10px 18px rgba(13, 99, 189, 0.24);
    }

    .toast-warning {
      position: fixed;
      top: 20px;
      right: 20px;
      max-width: 420px;
      z-index: 1080;
      display: none;
      align-items: flex-start;
      gap: 12px;
      padding: 18px 20px;
      border-radius: 8px;
      background: #f6a834;
      color: #fff;
      box-shadow: 0 14px 26px rgba(36, 44, 74, 0.18);
    }

    .toast-warning.is-visible {
      display: flex;
    }

    .toast-warning i {
      font-size: 1.8rem;
      line-height: 1;
    }

    .toast-warning strong,
    .toast-warning span {
      display: block;
      line-height: 1.25;
    }

    .toast-warning button {
      margin-left: auto;
      background: transparent;
      border: 0;
      color: #fff;
      font-size: 1rem;
      padding: 0;
    }

    .error-modal .modal-dialog {
      max-width: 96vw;
      margin: 1rem auto;
    }

    .error-modal .modal-content {
      border: 0;
      border-radius: 18px;
      overflow: hidden;
      min-height: 78vh;
    }

    .error-modal .modal-header {
      padding: 22px 28px;
      border-bottom: 1px solid #e9edf5;
    }

    .error-modal .modal-title {
      font-size: 1.75rem;
      font-weight: 700;
      color: #23314c;
    }

    .error-modal .btn-close {
      font-size: 1.2rem;
    }

    .error-modal .modal-body {
      padding: 14px 18px 24px;
      overflow: auto;
    }

    .error-section-title {
      margin: 8px 0 14px;
      color: #1f2738;
      font-size: 1.15rem;
      font-weight: 700;
    }

    .error-table-wrap {
      overflow: auto;
      margin-bottom: 18px;
    }

    .error-table {
      width: 100%;
      min-width: 1500px;
      border-collapse: separate;
      border-spacing: 0;
    }

    .error-table th,
    .error-table td {
      padding: 12px 12px;
      border-right: 1px solid #e5eaf2;
      border-bottom: 1px solid #e5eaf2;
      white-space: nowrap;
    }

    .error-table th {
      color: #375669;
      font-weight: 500;
      background: #fff;
      text-transform: uppercase;
      position: sticky;
      top: 0;
      z-index: 1;
    }

    .error-table tbody tr:nth-child(odd) td {
      background: #eef2f8;
    }

    .error-table tbody tr:nth-child(even) td {
      background: #fff;
    }

    .error-cell {
      color: #ff5a77;
    }

    .error-editable {
      width: 100%;
      min-width: 90px;
      border: 1px solid transparent;
      border-radius: 8px;
      background: transparent;
      color: #1f2738;
      padding: 6px 8px;
      outline: none;
    }

    .error-editable:focus {
      border-color: #71b5ff;
      background: #fff;
    }

    .error-modal-footer {
      display: flex;
      justify-content: flex-end;
      gap: 12px;
      padding: 6px 0 0;
    }

    .visually-hidden-input {
      position: absolute;
      width: 1px;
      height: 1px;
      padding: 0;
      margin: -1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
      border: 0;
    }

    @media (max-width: 1199.98px) {
      .import-entry,
      .mapping-columns {
        grid-template-columns: 1fr;
      }

      .import-panel + .import-panel,
      .mapping-section + .mapping-section {
        border-left: 0;
        border-top: 1px solid #ececf3;
      }
    }

    @media (max-width: 767.98px) {
      .import-items-page {
        padding: 16px;
      }

      .import-items-header,
      .import-panel,
      .mapping-stage {
        padding-left: 16px;
        padding-right: 16px;
      }

      .import-items-header h1,
      .mapping-topbar h2 {
        font-size: 1.55rem;
      }

      .steps-title,
      .upload-title,
      .mapping-card-header h3,
      .mapping-section-title {
        font-size: 1.35rem;
      }

      .upload-dropzone h3 {
        font-size: 1.45rem;
      }

      .upload-dropzone p,
      .step-text {
        font-size: 1rem;
      }

      .mapping-topbar {
        align-items: flex-start;
        flex-direction: column;
      }

      .results-footer {
        flex-direction: column;
      }

      .results-footer .btn {
        width: 100%;
      }
    }
  </style>
@endpush

@section('content')
  <div class="import-items-page">
    <div class="import-items-shell">
      <div class="import-items-header">
        <h1>Import Items From Excel File</h1>
        <p>Download the sample sheet, upload your Excel file, map the columns, and continue the import in the same flow.</p>
      </div>

      <div class="import-stage is-active" id="importEntryStage">
        <div class="import-entry">
          <section class="import-panel">
            <div class="steps-title">Steps to Import</div>

            <div class="step-block">
              <div class="step-label">STEP 1</div>
              <p class="step-text">Create an Excel file with the following format.</p>
              <a
                class="sample-button"
                download="export-items-sample.csv"
                href="data:text/csv;charset=utf-8,Item%20Code,Item%20Name,HSN,Sale%20Price,Purchase%20Price,Opening%20Stock%20Qty,Minimum%20Stock%20Qty,Item%20Location,Tax%20Rate,Tax%20Inclusive%0Aa101,Item%201,H001,5,4,20,5,Store%201,IGST@0%25,N%0Aa102,Item%202,H002,10,8,40,10,Store%202,IGST@0%25,N%0Aa103,Item%203,H003,15,12,60,15,Store%203,IGST@0%25,N%0Aa104,Item%204,H004,20,16,80,20,Store%204,IGST@0%25,N">
                <i class="bi bi-download"></i>
                Download Sample
              </a>

              <div class="sample-preview">
                <table>
                  <thead>
                    <tr>
                      <th>Item Code</th>
                      <th>Item Name</th>
                      <th>HSN</th>
                      <th>Sale Price</th>
                      <th>Purchase Price</th>
                      <th>Opening Qty</th>
                      <th>Min Stock Qty</th>
                      <th>Item Location</th>
                      <th>Tax Rate</th>
                      <th>Tax Inclusive</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td>a101</td>
                      <td>Item 1</td>
                      <td>H001</td>
                      <td>5</td>
                      <td>4</td>
                      <td>20</td>
                      <td>5</td>
                      <td>Store 1</td>
                      <td>IGST@0%</td>
                      <td>N</td>
                    </tr>
                    <tr>
                      <td>a102</td>
                      <td>Item 2</td>
                      <td>H002</td>
                      <td>10</td>
                      <td>8</td>
                      <td>40</td>
                      <td>10</td>
                      <td>Store 2</td>
                      <td>IGST@0%</td>
                      <td>N</td>
                    </tr>
                    <tr>
                      <td>a103</td>
                      <td>Item 3</td>
                      <td>H003</td>
                      <td>15</td>
                      <td>12</td>
                      <td>60</td>
                      <td>15</td>
                      <td>Store 3</td>
                      <td>IGST@0%</td>
                      <td>N</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="step-block">
              <div class="step-label">STEP 2</div>
              <p class="step-text">Upload the file (<strong>xlsx</strong> or <strong>xls</strong>) by clicking on the Upload File button or dragging it into the right panel.</p>
            </div>

            <div class="step-block">
              <div class="step-label">STEP 3</div>
              <p class="step-text">Verify the items from the file, map fields, and complete the import.</p>
            </div>
          </section>

          <section class="import-panel upload-panel">
            <div class="upload-title">Upload your <strong>.xls/ .xlsx (excel sheet)</strong></div>

            <label class="upload-dropzone" id="uploadDropzone" for="itemsImportFile">
              <div>
                <i class="bi bi-cloud-arrow-up"></i>
                <h3>Drag &amp; Drop files here</h3>
                <p>or</p>
                <span class="btn upload-button">
                  <i class="bi bi-upload"></i>
                  Upload File
                </span>
                <div class="upload-file-meta" id="uploadFileMeta">Accepted formats: .xls, .xlsx</div>
              </div>
            </label>
            <input class="visually-hidden-input" id="itemsImportFile" type="file" accept=".xls,.xlsx,.csv">
          </section>
        </div>
      </div>

      <div class="import-stage mapping-stage" id="mappingStage">
        <div class="mapping-topbar">
          <h2>Import Items From Excel File</h2>
          <div class="mapping-file-pill" id="selectedFilePill">
            <i class="bi bi-file-earmark-spreadsheet"></i>
            <span>No file selected</span>
          </div>
        </div>

        <div class="mapping-card">
          <div class="mapping-card-header">
            <h3>Map your fields to Vyapar's fields</h3>
            <p>Review imported columns and confirm the field mapping before proceeding.</p>
          </div>

          <div class="mapping-columns">
            <section class="mapping-section">
              <div class="mapping-section-title">Item Details</div>
              <div class="mapping-table-wrap">
                <table class="mapping-table">
                  <thead>
                    <tr>
                      <th>Fields available in Vyapar</th>
                      <th>Select your field</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($itemFields as $field)
                      <tr>
                        <td>{{ $field }}</td>
                        <td>
                          <select>
                            @foreach ($itemFields as $option)
                              <option value="{{ $option }}" @selected($option === $field)>{{ $option }}</option>
                            @endforeach
                            @foreach ($batchFields as $option)
                              @if (!in_array($option, $itemFields, true))
                                <option value="{{ $option }}">{{ $option }}</option>
                              @endif
                            @endforeach
                          </select>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </section>

            <section class="mapping-section">
              <div class="mapping-section-title">Batch Details</div>
              <div class="mapping-table-wrap">
                <table class="mapping-table">
                  <thead>
                    <tr>
                      <th>Fields available in Vyapar</th>
                      <th>Select your field</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($batchFields as $field)
                      <tr>
                        <td>{{ $field }}</td>
                        <td>
                          <select>
                            @foreach ($batchFields as $option)
                              <option value="{{ $option }}" @selected($option === $field)>{{ $option }}@if ($option === 'Mfg date') (DD/MM/YYYY)@elseif ($option === 'Exp date') (MM/YYYY)@endif</option>
                            @endforeach
                            @foreach ($itemFields as $option)
                              @if (!in_array($option, $batchFields, true))
                                <option value="{{ $option }}">{{ $option }}</option>
                              @endif
                            @endforeach
                          </select>
                        </td>
                      </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </section>
          </div>

          <div class="mapping-actions">
            <button class="proceed-button" type="button" id="proceedImportButton">Proceed</button>
          </div>
        </div>

        <div class="import-success" id="importSuccessState">
          <div class="success-card">
            Import flow is ready. Your uploaded sheet has been added to the mapping step and you can continue with the item verification/import process from here.
          </div>
        </div>
      </div>

      <div class="import-stage results-stage" id="resultsStage">
        <div class="import-items-header" style="border-bottom: 0; padding-top: 0;">
          <h1>Import Items From Excel File</h1>
        </div>

        <div class="results-shell">
          <div class="results-grid">
            <table class="results-table">
              <thead>
                <tr>
                  <th>Item Name*</th>
                  <th>Item Code</th>
                  <th>Description</th>
                  <th>Category</th>
                  <th>HSN</th>
                  <th>Default MRP</th>
                  <th>Disc % On M...</th>
                  <th>Sale Price</th>
                  <th>Purchase P...</th>
                  <th>Wholesale ...</th>
                  <th>Minimum W...</th>
                  <th>Disc % On M...</th>
                  <th>Discount T...</th>
                  <th>Sale Discount</th>
                  <th>Opening Stoc...</th>
                  <th>Minimum Stoc...</th>
                  <th>Item Location</th>
                  <th>Tax Rate</th>
                  <th>Inclusive Of Tax</th>
                  <th>Base Unit (x)</th>
                  <th>Secondary Unit (y)</th>
                  <th>Conversion Rate</th>
                </tr>
              </thead>
              <tbody id="resultsTableBody"></tbody>
            </table>
          </div>
        </div>

        <div class="results-footer">
          <button class="btn btn-outline-danger" type="button" id="showErrorsButton">See 0 Items With Error</button>
          <button class="btn btn-primary" type="button" id="importValidItemsButton">Import 0 Valid Items</button>
        </div>
      </div>
    </div>
  </div>

  <div class="toast-warning" id="unmappedToast">
    <i class="bi bi-exclamation-triangle"></i>
    <div>
      <strong>Data from unmapped columns is not imported.</strong>
      <span>Data from unmapped columns is not imported.</span>
    </div>
    <button type="button" id="closeWarningToast">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <div class="modal fade error-modal" id="itemsErrorModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable modal-fullscreen-xl-down">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Items with Error</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="error-section-title">Primary Errors</div>
          <div class="error-table-wrap">
            <table class="error-table">
              <thead>
                <tr>
                  <th>Item Name*</th>
                  <th>Item Code</th>
                  <th>Description</th>
                  <th>Category</th>
                  <th>HSN</th>
                  <th>Default MRP</th>
                  <th>Disc % On M...</th>
                  <th>Sale Price</th>
                  <th>Purchase P...</th>
                  <th>Wholesale ...</th>
                  <th>Minimum W...</th>
                  <th>Disc % On M...</th>
                  <th>Discount T...</th>
                  <th>Sale Discount</th>
                  <th>Opening Stoc...</th>
                  <th>Minimum Stoc...</th>
                  <th>Item Location</th>
                  <th>Tax Rate</th>
                  <th>Inclusive Of Tax</th>
                  <th>Base Unit (x)</th>
                  <th>Secondary Unit (y)</th>
                  <th>Conversion Rate</th>
                  <th>Error</th>
                </tr>
              </thead>
              <tbody id="primaryErrorsBody"></tbody>
            </table>
          </div>

          <div class="error-section-title">Batch Errors</div>
          <div class="error-table-wrap">
            <table class="error-table">
              <thead>
                <tr>
                  <th>Item Name*</th>
                  <th>Batch No</th>
                  <th>Model No</th>
                  <th>Size</th>
                  <th>Mfg Date(DD/M...)</th>
                  <th>Exp Date(MM/Y...)</th>
                  <th>Opening Stock...</th>
                  <th>MRP</th>
                  <th>Error</th>
                </tr>
              </thead>
              <tbody id="batchErrorsBody"></tbody>
            </table>
          </div>

          <div class="error-modal-footer">
            <button class="btn btn-outline-secondary" type="button" data-bs-dismiss="modal">Close</button>
            <button class="btn btn-primary" type="button" id="saveErrorChangesButton">Save Changes</button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const fileInput = document.getElementById('itemsImportFile');
      const dropzone = document.getElementById('uploadDropzone');
      const entryStage = document.getElementById('importEntryStage');
      const mappingStage = document.getElementById('mappingStage');
      const resultsStage = document.getElementById('resultsStage');
      const uploadFileMeta = document.getElementById('uploadFileMeta');
      const selectedFilePill = document.getElementById('selectedFilePill');
      const proceedButton = document.getElementById('proceedImportButton');
      const successState = document.getElementById('importSuccessState');
      const resultsTableBody = document.getElementById('resultsTableBody');
      const primaryErrorsBody = document.getElementById('primaryErrorsBody');
      const batchErrorsBody = document.getElementById('batchErrorsBody');
      const showErrorsButton = document.getElementById('showErrorsButton');
      const importValidItemsButton = document.getElementById('importValidItemsButton');
      const unmappedToast = document.getElementById('unmappedToast');
      const closeWarningToast = document.getElementById('closeWarningToast');
      const saveErrorChangesButton = document.getElementById('saveErrorChangesButton');
      const errorModalElement = document.getElementById('itemsErrorModal');
      const errorModal = new bootstrap.Modal(errorModalElement);
      const resultColumns = [
        'itemName', 'itemCode', 'description', 'category', 'hsn', 'defaultMrp', 'saleDiscMrp', 'salePrice',
        'purchasePrice', 'wholesalePrice', 'minWholesaleQty', 'wholesaleDiscMrp', 'discountType', 'saleDiscount',
        'openingStock', 'minStock', 'itemLocation', 'taxRate', 'inclusiveTax', 'baseUnit', 'secondaryUnit', 'conversionRate'
      ];
      const resultHeaders = [
        'Item Name*', 'Item Code', 'Description', 'Category', 'HSN', 'Default MRP', 'Disc % On M...', 'Sale Price',
        'Purchase P...', 'Wholesale ...', 'Minimum W...', 'Disc % On M...', 'Discount T...', 'Sale Discount',
        'Opening Stoc...', 'Minimum Stoc...', 'Item Location', 'Tax Rate', 'Inclusive Of Tax', 'Base Unit (x)', 'Secondary Unit (y)', 'Conversion Rate'
      ];
      let importRows = [];
      let importBatchErrors = [];

      function setStage(activeStage) {
        [entryStage, mappingStage, resultsStage].forEach(function (stage) {
          stage.classList.toggle('is-active', stage === activeStage);
        });
      }

      function openMappingStage(file) {
        const fileName = file && file.name ? file.name : 'items-import.xlsx';

        uploadFileMeta.textContent = 'Selected file: ' + fileName;
        selectedFilePill.querySelector('span').textContent = fileName;

        setStage(mappingStage);
        successState.classList.remove('is-visible');

        mappingStage.scrollIntoView({ behavior: 'smooth', block: 'start' });
      }

      function getDemoRows() {
        return [
          {
            itemName: 'a101',
            itemCode: 'a101',
            description: '',
            category: 'a101',
            hsn: '',
            defaultMrp: '20',
            saleDiscMrp: '30',
            salePrice: '20',
            purchasePrice: '25',
            wholesalePrice: '120',
            minWholesaleQty: '3',
            wholesaleDiscMrp: '10',
            discountType: 'Discount Amount',
            saleDiscount: 'Inclusive',
            openingStock: '10',
            minStock: '2',
            itemLocation: 'Store 1',
            taxRate: '',
            inclusiveTax: 'Inclusive',
            baseUnit: '',
            secondaryUnit: '',
            conversionRate: '',
            error: ''
          },
          {
            itemName: 'a102',
            itemCode: 'a102',
            description: '',
            category: 'a102',
            hsn: '',
            defaultMrp: '30',
            saleDiscMrp: '90',
            salePrice: '30',
            purchasePrice: '35',
            wholesalePrice: '110',
            minWholesaleQty: '4',
            wholesaleDiscMrp: '20',
            discountType: 'Discount Amount',
            saleDiscount: 'Exclusive',
            openingStock: '5',
            minStock: '',
            itemLocation: 'Store 2',
            taxRate: '',
            inclusiveTax: 'Exclusive',
            baseUnit: '',
            secondaryUnit: '',
            conversionRate: '',
            error: ''
          },
          {
            itemName: 'a103',
            itemCode: 'a103',
            description: '',
            category: 'a103',
            hsn: '',
            defaultMrp: '35',
            saleDiscMrp: '23',
            salePrice: '35',
            purchasePrice: '40',
            wholesalePrice: '45',
            minWholesaleQty: '2',
            wholesaleDiscMrp: '10',
            discountType: 'Discount Amount',
            saleDiscount: 'Inclusive',
            openingStock: '15',
            minStock: '1',
            itemLocation: 'Store 1',
            taxRate: '',
            inclusiveTax: 'Inclusive',
            baseUnit: '',
            secondaryUnit: '',
            conversionRate: '',
            error: ''
          },
          {
            itemName: 'a101',
            itemCode: 'a104',
            description: '',
            category: 'a104',
            hsn: '',
            defaultMrp: '20',
            saleDiscMrp: '12',
            salePrice: '20',
            purchasePrice: '25',
            wholesalePrice: '56',
            minWholesaleQty: '6',
            wholesaleDiscMrp: '30',
            discountType: 'Discount Amount',
            saleDiscount: 'Exclusive',
            openingStock: '10',
            minStock: '2',
            itemLocation: 'Store 1',
            taxRate: '',
            inclusiveTax: 'Exclusive',
            baseUnit: '',
            secondaryUnit: '',
            conversionRate: '',
            error: ''
          },
          {
            itemName: 'a105',
            itemCode: 'a105',
            description: '',
            category: 'a105',
            hsn: '',
            defaultMrp: '30',
            saleDiscMrp: '34',
            salePrice: '30',
            purchasePrice: '35',
            wholesalePrice: '78',
            minWholesaleQty: '8',
            wholesaleDiscMrp: '10',
            discountType: 'Discount Amount',
            saleDiscount: 'Inclusive',
            openingStock: '5',
            minStock: '7',
            itemLocation: 'Store 2',
            taxRate: '',
            inclusiveTax: 'Inclusive',
            baseUnit: '',
            secondaryUnit: '',
            conversionRate: '',
            error: ''
          },
          {
            itemName: 'a106',
            itemCode: 'a106',
            description: '',
            category: 'a106',
            hsn: '',
            defaultMrp: '35',
            saleDiscMrp: '30',
            salePrice: '35',
            purchasePrice: '37',
            wholesalePrice: '89',
            minWholesaleQty: '3',
            wholesaleDiscMrp: '',
            discountType: 'Discount Amount',
            saleDiscount: 'Inclusive',
            openingStock: '15',
            minStock: '1',
            itemLocation: 'Store 1',
            taxRate: '',
            inclusiveTax: 'Inclusive',
            baseUnit: '',
            secondaryUnit: '',
            conversionRate: '',
            error: ''
          }
        ];
      }

      function getBatchErrors() {
        return [
          { itemName: 'N/A', batchNo: '', modelNo: '', size: '', mfgDate: '', expDate: '', openingStock: '5', mrp: '10', error: 'Batch item name is missing.' },
          { itemName: 'N/A', batchNo: '', modelNo: '', size: '', mfgDate: '', expDate: '', openingStock: '15', mrp: '23', error: 'Batch item name is missing.' },
          { itemName: 'N/A', batchNo: '', modelNo: '', size: '', mfgDate: '', expDate: '', openingStock: '10', mrp: '12', error: 'Batch item name is missing.' }
        ];
      }

      function normalizeErrors(rows) {
        const counts = rows.reduce(function (accumulator, row) {
          const key = (row.itemName || '').trim().toLowerCase();
          accumulator[key] = (accumulator[key] || 0) + 1;
          return accumulator;
        }, {});

        rows.forEach(function (row) {
          const key = (row.itemName || '').trim().toLowerCase();
          const errors = [];

          if (!row.itemName) {
            errors.push('Item name is required.');
          }

          if (key && counts[key] > 1) {
            errors.push('Duplicate item name found.');
          }

          row.error = errors.join(' ');
          row.isError = errors.length > 0;
        });
      }

      function getValidRows() {
        return importRows.filter(function (row) {
          return !row.isError;
        });
      }

      function getErrorRows() {
        return importRows.filter(function (row) {
          return row.isError;
        });
      }

      function renderResultsTable() {
        resultsTableBody.innerHTML = '';

        importRows.forEach(function (row, index) {
          const tr = document.createElement('tr');
          if (row.isError) {
            tr.classList.add('is-error-row');
          }

          resultColumns.forEach(function (column, columnIndex) {
            const td = document.createElement('td');

            if (columnIndex === 0) {
              const link = document.createElement('a');
              link.href = '#';
              link.className = 'result-link';
              link.dataset.errorIndex = String(index);
              link.innerHTML = row[column] + ' <i class="bi bi-chevron-right"></i>';
              td.appendChild(link);
            } else {
              td.textContent = row[column] || '';
            }

            tr.appendChild(td);
          });

          resultsTableBody.appendChild(tr);
        });

        resultsTableBody.querySelectorAll('.result-link').forEach(function (link) {
          link.addEventListener('click', function (event) {
            event.preventDefault();
            errorModal.show();
          });
        });
      }

      function createEditableCell(value, rowIndex, key, isErrorCell) {
        const td = document.createElement('td');
        const input = document.createElement('input');
        input.type = 'text';
        input.value = value || '';
        input.className = 'error-editable';
        input.dataset.rowIndex = String(rowIndex);
        input.dataset.key = key;

        if (isErrorCell) {
          td.classList.add('error-cell');
        }

        input.addEventListener('input', function () {
          importRows[rowIndex][key] = input.value;
        });

        td.appendChild(input);
        return td;
      }

      function renderErrorTables() {
        primaryErrorsBody.innerHTML = '';
        batchErrorsBody.innerHTML = '';

        getErrorRows().forEach(function (row) {
          const rowIndex = importRows.indexOf(row);
          const tr = document.createElement('tr');

          resultColumns.forEach(function (column) {
            tr.appendChild(createEditableCell(row[column], rowIndex, column, false));
          });

          tr.appendChild(createEditableCell(row.error, rowIndex, 'error', true));
          primaryErrorsBody.appendChild(tr);
        });

        importBatchErrors.forEach(function (row, index) {
          const tr = document.createElement('tr');
          ['itemName', 'batchNo', 'modelNo', 'size', 'mfgDate', 'expDate', 'openingStock', 'mrp'].forEach(function (column) {
            const td = document.createElement('td');
            const input = document.createElement('input');
            input.type = 'text';
            input.value = row[column] || '';
            input.className = 'error-editable';
            input.addEventListener('input', function () {
              importBatchErrors[index][column] = input.value;
            });
            if (column === 'itemName' && (!row[column] || row[column] === 'N/A')) {
              td.classList.add('error-cell');
            }
            td.appendChild(input);
            tr.appendChild(td);
          });

          const errorTd = document.createElement('td');
          const errorInput = document.createElement('input');
          errorInput.type = 'text';
          errorInput.value = row.error || '';
          errorInput.className = 'error-editable';
          errorInput.addEventListener('input', function () {
            importBatchErrors[index].error = errorInput.value;
          });
          errorTd.classList.add('error-cell');
          errorTd.appendChild(errorInput);
          tr.appendChild(errorTd);
          batchErrorsBody.appendChild(tr);
        });
      }

      function updateCounts() {
        const validCount = getValidRows().length;
        const errorCount = getErrorRows().length + importBatchErrors.length;

        showErrorsButton.textContent = 'See ' + errorCount + ' Items With Error';
        importValidItemsButton.textContent = 'Import ' + validCount + ' Valid Items';
      }

      function renderResultsStage() {
        normalizeErrors(importRows);
        renderResultsTable();
        renderErrorTables();
        updateCounts();
      }

      function applyServerErrors(errors) {
        errors.forEach(function (error) {
          const rowIndex = Number(error.row) - 1;
          if (!Number.isNaN(rowIndex) && importRows[rowIndex]) {
            importRows[rowIndex].error = error.error || 'Import error.';
            importRows[rowIndex].isError = true;
          }
        });
      }

      fileInput.addEventListener('change', function (event) {
        const [file] = event.target.files;

        if (!file) {
          return;
        }

        openMappingStage(file);
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
        openMappingStage(files[0]);
      });

      proceedButton.addEventListener('click', function () {
        importRows = getDemoRows();
        importBatchErrors = getBatchErrors();
        renderResultsStage();

        setStage(resultsStage);
        successState.classList.add('is-visible');
        unmappedToast.classList.add('is-visible');
        resultsStage.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });

      showErrorsButton.addEventListener('click', function () {
        errorModal.show();
      });

      saveErrorChangesButton.addEventListener('click', function () {
        normalizeErrors(importRows);
        renderResultsStage();
        errorModal.hide();
      });

      closeWarningToast.addEventListener('click', function () {
        unmappedToast.classList.remove('is-visible');
      });

      importValidItemsButton.addEventListener('click', async function () {
        const validRows = getValidRows();

        if (!validRows.length) {
          showErrorsButton.click();
          return;
        }

        const originalLabel = importValidItemsButton.textContent;
        importValidItemsButton.disabled = true;
        importValidItemsButton.textContent = 'Importing...';

        try {
          const response = await fetch("{{ route('utilities.import-items.valid-items') }}", {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'Accept': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content')
            },
            body: JSON.stringify({ items: validRows })
          });

          const result = await response.json();

          if (!response.ok) {
            if (Array.isArray(result.errors)) {
              applyServerErrors(result.errors);
              renderResultsStage();
              errorModal.show();
            }
            throw new Error(result.message || 'Unable to import valid items.');
          }

          unmappedToast.classList.add('is-visible');
          unmappedToast.querySelector('strong').textContent = result.message || 'Items imported successfully.';
          unmappedToast.querySelector('span').textContent = 'Imported items have been stored in the items table.';
          importValidItemsButton.textContent = 'Imported ' + (result.imported_count || validRows.length) + ' Valid Items';
        } catch (error) {
          unmappedToast.classList.add('is-visible');
          unmappedToast.querySelector('strong').textContent = 'Import failed.';
          unmappedToast.querySelector('span').textContent = error.message;
        } finally {
          importValidItemsButton.disabled = false;
          if (!importValidItemsButton.textContent.startsWith('Imported ')) {
            importValidItemsButton.textContent = originalLabel;
          }
        }
      });
    });
  </script>
@endpush
