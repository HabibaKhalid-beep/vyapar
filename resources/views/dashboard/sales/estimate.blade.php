@extends('layouts.app')

@section('title', 'Vyapar — Estimate / Quotation')
@section('description', 'Create professional estimates and quotations for your customers in Vyapar.')
@section('page', 'sale-estimate')

@section('content')
    <div class="container-fluid col-12">
      @if(session('error'))
        <div class="alert alert-danger mb-3">{{ session('error') }}</div>
      @endif

      <div class="d-flex justify-content-between align-items-center bg-light mb-2 p-4">
        <div>
         <div class="dropdown">
          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="h4"> Estimates / Quotations</span>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="invoice.html">Sale Invoice</a></li>
            <li><a class="dropdown-item" href="sale-estimate.html">Estimate / Quotation</a></li>
            <li><a class="dropdown-item" href="sale-return.html">Sale Return / Cr. Note</a></li>
            <li><a class="dropdown-item" href="payment-in.html">Payment In</a></li>
            <li><a class="dropdown-item" href="payment-out.html">Payment out</a></li>
            <li><a class="dropdown-item" href="purchase-bill.html">Purchase Bill</a></li>
            <li><a class="dropdown-item" href="purchase-return.html">Purchase Return / Dr. Note</a></li>
            <li><a class="dropdown-item" href="expenses.html">Expenses</a></li>

          </ul>
        </div>
        </div>
        <button class="btn rounded-pill" style="background-color: #D4112E;" onclick="window.location='{{ route('estimates.create') }}'"><span class="text-light">+ Add
            Estimate</span></button>
      </div>
      <div class="d-flex justify-content-between align-items-center bg-light mb-2 px-4 py-2 rounded">
        <div class="d-flex">
          <div class="d-flex justify-content-center align-items-center me-2">Filter By: </div>
          <div class="d-flex rounded-pill" style="background-color:#E4F2FF;">
            <div class="d-flex justify-content-center align-items-center text-center"
              style="width: 9rem; height:40px; border-right: 1px solid rgb(45, 44, 44); font-size:12px;"><select name=""
                id="" class="bg-transparent border-0" style="outline:none;">
                <option value="">All Estimates</option>
                <option value="" selected>This Month</option>
                <option value="">Last Month</option>
                <option value="">This Quarter</option>
                <option value="">This Year</option>
                <option value="">Custom</option>
              </select></div>
            <div class="d-flex justify-content-center align-items-center" style="width: 16rem; height: 40px;">01/03/2026
              To 31/03/2026</div>

          </div>
          <div class="d-flex justify-content-center align-items-center rounded-pill ms-4"
            style="background-color:#E4F2FF; width: 8rem; height: 40px;"><select name="" id=""
              class="bg-transparent border-0" style="outline:none;">
              <option value="" selected>All Firms</option>
              <option value=""><a href="">Firm 1</a></option>
              <option value=""><a href="">Firm 2</a></option>
              <option value=""><a href="">Firm 3</a></option>

            </select></div>

        </div>
      </div>
      <div class="bg-light mb-2 px-4 py-3 rounded">
        <div class="border rounded p-1" style="width: 25rem; height: 8rem; background-color: #FCF8FF;">
          <div class="w-100 d-flex">
            <div class="w-50 mt-2">
              <p class="ps-3 text-secondary m-0">Total Quotations</p>
              <p class="ps-3 h4">Rs {{ number_format(($allEstimates ?? $estimates)->sum('grand_total'), 2) }}</p>
            </div>
            <div class="w-50 mt-2 d-flex align-items-end justify-content-center flex-column">
              <div class="col-5 h-50 rounded-pill d-flex justify-content-center align-item-center me-4"
                style="background-color: #DEF7EE;">
                <p class="text-success pt-1">{{ ($allEstimates ?? $estimates)->count() > 0 ? round((($allEstimates ?? $estimates)->where('status', 'converted')->count() / ($allEstimates ?? $estimates)->count()) * 100) : 0 }}% <i class="bi bi-arrow-up-right "></i></p>
              </div>
              <span class="me-4 pe-1 mt-1 text-secondary" style="font-size: 10px;">conversion rate</span>
            </div>
          </div>
          <div class="w-100 d-flex mt-3">
            <p class="ps-3 pe-3 text-secondary" style="border-right:1px solid rgb(45, 44, 44);">Converted : <span
                class="fw-bold text-dark">Rs {{ number_format(($allEstimates ?? $estimates)->where('status', 'converted')->sum('grand_total'), 2) }}</span></p>
            <p class="ps-3 text-secondary">Open : <span class="fw-bold text-dark">Rs {{ number_format(($allEstimates ?? $estimates)->where('status', 'open')->sum('grand_total'), 2) }}</span></p>

          </div>
        </div>
      </div>

      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <p class="fw-bold mb-2">Transactions</p>
            </div>
            <div class="col-md-6">
              <form method="GET" action="{{ route('sale.estimate') }}" class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" placeholder="Search by Bill No. or Party Name..."
                       name="search" value="{{ $search ?? '' }}" style="border-radius: 20px;">
                <button type="submit" class="btn btn-sm btn-outline-primary" style="border-radius: 20px; white-space: nowrap;">
                  <i class="fas fa-search"></i> Search
                </button>
                @if($search)
                  <a href="{{ route('sale.estimate') }}" class="btn btn-sm btn-outline-secondary" style="border-radius: 20px; white-space: nowrap;">
                    Clear
                  </a>
                @endif
              </form>
            </div>
          </div>

          <div class="table-responsive">
            <table id="estimatesTable" class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Reference No.</th>
                  <th>Party Name</th>
                  <th>Amount</th>
                  <th>Balance</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($estimates ?? [] as $estimate)
                <tr data-estimate-id="{{ $estimate->id }}">
                  <td>{{ $estimate->invoice_date ? $estimate->invoice_date->format('d/m/Y') : '-' }}</td>
                  <td>{{ $estimate->bill_number ?? '-' }}</td>
                  <td>{{ $estimate->party_name ?? '-' }}</td>
                  <td>Rs {{ number_format($estimate->items->sum('amount'), 2) }}</td>
                  <td>Rs {{ number_format($estimate->balance ?? $estimate->grand_total ?? 0, 2) }}</td>
                  <td>
                    @php
                      $isConverted = $estimate->status === 'converted';
                      $convertedInvoiceNumber = $convertedInvoices[$estimate->id] ?? null;
                      $statusLabel = $isConverted
                          ? 'Converted' . ($convertedInvoiceNumber ? ' (Invoice #' . $convertedInvoiceNumber . ')' : '')
                          : ucfirst($estimate->status);
                    @endphp
                    <span class="badge {{ $isConverted ? 'text-primary bg-primary-subtle border border-primary-subtle' : ($estimate->status === 'open' ? 'bg-success' : 'bg-warning text-dark') }}">
                      {{ $statusLabel }}
                    </span>
                  </td>
                  <td>
                    <div class="dropdown d-inline me-2">
                      <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" style="white-space: nowrap;" {{ $isConverted ? 'disabled' : '' }}>
                        Convert
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <a class="dropdown-item {{ $isConverted ? 'disabled' : '' }}" href="{{ $isConverted ? '#' : route('estimates.convert-to-sale', $estimate->id) }}">
                            <i class="fas fa-file-invoice me-2"></i>Estimate to Sale
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item {{ $isConverted ? 'disabled' : '' }}" href="{{ $isConverted ? '#' : route('estimates.convert-to-sale-order', $estimate->id) }}">
                            <i class="fas fa-clipboard-list me-2"></i>Estimate to Sale Order
                          </a>
                        </li>
                      </ul>
                    </div>
                    <div class="dropdown d-inline">
                      <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('estimates.edit', $estimate->id) }}"><i class="fas fa-edit me-2"></i>View/Edit</a></li>
                        <li><a class="dropdown-item" href="#" onclick="printEstimate('{{ route('estimates.print', $estimate->id) }}'); return false;"><i class="fas fa-print me-2"></i>Print</a></li>
                        <li><a class="dropdown-item" href="#" onclick="previewEstimate('{{ route('estimates.preview', $estimate->id) }}'); return false;"><i class="fas fa-file-alt me-2"></i>Preview</a></li>
                        <li><a class="dropdown-item" href="#" onclick="openPdf('{{ route('estimates.pdf', $estimate->id) }}'); return false;"><i class="fas fa-file-pdf me-2"></i>Open PDF</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item text-danger" href="#" onclick="deleteEstimate('{{ route('estimates.destroy', $estimate->id) }}'); return false;"><i class="fas fa-trash me-2"></i>Delete</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    No estimates yet. Click "New Estimate" to create one.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>

        </div>
      </div>
    </div>
  </main>

@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
$(document).ready(function() {
  var table = $('#estimatesTable').DataTable({
    responsive: true,
    pageLength: 10,
    lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
    order: [[0, 'desc']],
    columnDefs: [
      { orderable: false, targets: 6 }
    ],
    dom: 'lftip',
    language: {
      search: "Search:",
      lengthMenu: "Show _MENU_ entries",
      info: "Showing _START_ to _END_ of _TOTAL_ entries",
      paginate: {
        first: "First",
        last: "Last",
        next: "Next",
        previous: "Previous"
      }
    }
  });

  // Create individual filter rows for each column
  var headers = $('#estimatesTable thead tr:first th');
  headers.each(function(i) {
    if (i < 6) { // Don't add filter to Actions column
      var filterRow = $('<tr class="filter-row" data-column="' + i + '" style="display: none;"></tr>');

      // Add empty cells for all columns
      for (var j = 0; j < headers.length; j++) {
        if (j === i) {
          var title = $(headers[j]).text();
          filterRow.append('<th><input type="text" placeholder="Search ' + title + '..." class="form-control form-control-sm column-filter"></th>');
        } else {
          filterRow.append('<th></th>');
        }
      }

      $('#estimatesTable thead').append(filterRow);
    }
  });

  // Toggle filter for clicked column header
  $('#estimatesTable thead tr:first th').each(function(i) {
    if (i < 6) {
      $(this).css({
        'cursor': 'pointer',
        'position': 'relative'
      });

      $(this).on('click', function(e) {
        var currentRow = $('#estimatesTable thead .filter-row[data-column="' + i + '"]');
        var otherRows = $('#estimatesTable thead .filter-row[data-column!="' + i + '"]');

        // Hide all filter rows
        otherRows.slideUp(150);

        // Toggle current filter row
        if (currentRow.is(':visible')) {
          currentRow.slideUp(150);
        } else {
          currentRow.slideDown(150);
        }

        e.stopPropagation();
      });

      // Add filter icon hint
      $(this).append(' <i class="fas fa-filter ms-1" style="opacity: 0.5; font-size: 12px;"></i>');
    }
  });

  // Apply column search on input
  table.columns().every(function(i) {
    $('.filter-row[data-column="' + i + '"] .column-filter').on('keyup change', function() {
      if (table.column(i).search() !== this.value) {
        table.column(i).search(this.value).draw();
      }
    });
  });
});

function previewEstimate(url) {
  window.open(url, '_blank');
}

function printEstimate(url) {
  window.open(url, '_blank');
}

function openPdf(url) {
  window.open(url, '_blank');
}

function deleteEstimate(url) {
  if (!confirm('Are you sure you want to delete this estimate?')) {
    return;
  }

  fetch(url, {
    method: 'DELETE',
    headers: {
      'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
      'Accept': 'application/json',
    },
  })
    .then(async (response) => {
      const data = await response.json();

      if (!response.ok) {
        throw new Error(data.message || 'Delete failed');
      }

      window.location.reload();
    })
    .catch((error) => {
      alert(error.message || 'Unable to delete estimate.');
    });
}
</script>

<script src="{{ asset('js/sale-estimate.js') }}"></script>
@endpush
