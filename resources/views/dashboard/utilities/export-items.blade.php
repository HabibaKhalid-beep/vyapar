@extends('layouts.app')

@section('title', 'Utilities — Export Items')
@section('description', 'Export item catalog and details.')
@section('page', 'export-items')

@section('content')
  <div class="container mt-4">
    <div class="card p-4 export-items-card">
      <div class="d-flex justify-content-between align-items-start flex-wrap">
        <div>
          <h1>Export Items</h1>
          <p class="text-muted mb-0">The item export modal will open automatically and prepare your inventory data for Excel download.</p>
        </div>
        <button type="button" class="btn btn-primary mt-3 mt-sm-0" id="manualExportOpenBtn">Open Export Preview</button>
      </div>
    </div>
  </div>
@endsection

@section('modals')
  <div class="modal fade" id="exportItemsModal" tabindex="-1" aria-labelledby="exportItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exportItemsModalLabel">Export Items to Excel</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <div id="exportItemsStatus" class="small text-secondary">Preparing export preview...</div>
            <div id="exportItemsLoader" class="spinner-border spinner-border-sm text-primary mt-2" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div class="table-responsive" style="max-height: 60vh; overflow:auto;">
            <table class="table table-bordered table-hover table-sm mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Item Name</th>
                  <th>Item Code</th>
                  <th>Description</th>
                  <th>Category</th>
                  <th>Unit</th>
                  <th>Sale Price</th>
                  <th>Purchase Price</th>
                  <th>Opening Qty</th>
                  <th>Stock Qty</th>
                  <th>Min Stock</th>
                  <th>Location</th>
                </tr>
              </thead>
              <tbody id="exportItemsTableBody">
                <tr>
                  <td colspan="12" class="text-center py-5">Loading item preview...</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer justify-content-between">
          <div>
            <button type="button" class="btn btn-outline-secondary" id="exportItemsRetryBtn" style="display:none;">Retry</button>
          </div>
          <div>
            <a href="#" id="exportItemsDownloadBtn" class="btn btn-success" style="display:none;" target="_blank">Export Excel</a>
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('styles')
  <style>
    .export-items-card {
      background: #ffffff;
      border: 1px solid #e8eaef;
      border-radius: 18px;
      box-shadow: 0 18px 40px rgba(37, 44, 97, 0.08);
    }

    @media (max-width: 575px) {
      .export-items-card h1 {
        font-size: 1.75rem;
      }
    }
  </style>
@endpush

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const exportModalEl = document.getElementById('exportItemsModal');
      const exportModal = exportModalEl ? new bootstrap.Modal(exportModalEl) : null;
      const openBtn = document.getElementById('manualExportOpenBtn');
      const statusText = document.getElementById('exportItemsStatus');
      const loader = document.getElementById('exportItemsLoader');
      const downloadBtn = document.getElementById('exportItemsDownloadBtn');
      const retryBtn = document.getElementById('exportItemsRetryBtn');
      const tableBody = document.getElementById('exportItemsTableBody');
      const dataUrl = '{{ route('utilities.export-items.data') }}';
      const downloadUrl = '{{ route('utilities.export-items.download') }}';

      function renderItems(items) {
        if (!tableBody) return;

        if (!items.length) {
          tableBody.innerHTML = '<tr><td colspan="12" class="text-center py-5">No items found for export.</td></tr>';
          return;
        }

        tableBody.innerHTML = items.map(function (item, index) {
          return `
            <tr>
              <td>${index + 1}</td>
              <td>${item.name || ''}</td>
              <td>${item.item_code || ''}</td>
              <td>${item.description || ''}</td>
              <td>${item.category || ''}</td>
              <td>${item.unit || ''}</td>
              <td>${item.sale_price ?? ''}</td>
              <td>${item.purchase_price ?? ''}</td>
              <td>${item.opening_qty ?? ''}</td>
              <td>${item.stock_qty ?? ''}</td>
              <td>${item.min_stock ?? ''}</td>
              <td>${item.location || ''}</td>
            </tr>
          `;
        }).join('');
      }

      async function loadExportItems() {
        if (!statusText || !loader) return;
        statusText.textContent = 'Loading export data...';
        loader.classList.remove('d-none');
        retryBtn.style.display = 'none';
        downloadBtn.style.display = 'none';

        try {
          const response = await fetch(dataUrl, { credentials: 'same-origin' });
          if (!response.ok) {
            throw new Error('Server returned ' + response.status);
          }

          const json = await response.json();
          if (!json.success) {
            throw new Error(json.message || 'Unable to load items.');
          }

          renderItems(json.items || []);
          statusText.textContent = `Loaded ${json.count || (json.items || []).length} items. Ready to export.`;
          loader.classList.add('d-none');
          downloadBtn.href = downloadUrl;
          downloadBtn.style.display = 'inline-flex';
        } catch (error) {
          statusText.textContent = 'Error loading export data: ' + error.message;
          loader.classList.add('d-none');
          retryBtn.style.display = 'inline-flex';
        }
      }

      if (openBtn) {
        openBtn.addEventListener('click', function () {
          exportModal?.show();
          loadExportItems();
        });
      }

      if (retryBtn) {
        retryBtn.addEventListener('click', loadExportItems);
      }

      if (exportModal) {
        exportModal.show();
        loadExportItems();
      }
    });
  </script>
@endpush
