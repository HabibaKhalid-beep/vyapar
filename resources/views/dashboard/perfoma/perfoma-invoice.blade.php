<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Vyapar - Proforma Invoice</title>
  <meta name="description" content="Create and manage proforma invoices in Vyapar.">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

  <script>
    const authUser = @json(Auth::user());
    window.App = window.App || {
      isAuthenticated: @json(Auth::check()),
      user: authUser ? {
        id: authUser.id,
        name: authUser.name,
        roles: @json(Auth::user()?->roles()->pluck('name')->toArray() ?? []),
        permissions: @json(Auth::user()?->getAllPermissions() ?? []),
      } : { id: null, name: null, roles: [], permissions: [] },
      logoutUrl: "{{ route('logout') }}",
      csrfToken: "{{ csrf_token() }}",
    };
  </script>
</head>

<body data-page="proforma-invoice">
  <main class="main-content" id="mainContent">
    <div class="container-fluid col-12">
      <div class="d-flex justify-content-between align-items-center bg-light mb-2 p-4">
        <div class="dropdown">
          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="h4">Proforma Invoice</span>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('sale.index') }}">Sale Invoice</a></li>
            <li><a class="dropdown-item" href="{{ route('sale.estimate') }}">Estimate / Quotation</a></li>
            <li><a class="dropdown-item" href="{{ route('sale-return') }}">Sale Return / Cr. Note</a></li>
          </ul>
        </div>
        <button class="btn rounded-pill" style="background-color: #D4112E;" onclick="window.location='{{ route('proforma-invoice.create') }}'">
          <span class="text-light">+ Add Proforma</span>
        </button>
      </div>

      <div class="d-flex justify-content-between align-items-center bg-light mb-2 px-4 py-2 rounded">
        <div class="d-flex">
          <div class="d-flex justify-content-center align-items-center me-2">Filter By:</div>
          <div class="d-flex rounded-pill" style="background-color:#E4F2FF;">
            <div class="d-flex justify-content-center align-items-center text-center" style="width: 9rem; height:40px; border-right: 1px solid rgb(45, 44, 44); font-size:12px;">
              <select class="bg-transparent border-0" style="outline:none;">
                <option>All Proformas</option>
                <option selected>This Month</option>
                <option>Last Month</option>
                <option>This Quarter</option>
                <option>This Year</option>
              </select>
            </div>
            <div class="d-flex justify-content-center align-items-center" style="width: 16rem; height: 40px;">01/03/2026 To 31/03/2026</div>
          </div>
        </div>
      </div>

      <div class="bg-light mb-2 px-4 py-3 rounded">
        <div class="border rounded p-1" style="width: 25rem; height: 8rem; background-color: #FCF8FF;">
          <div class="w-100 d-flex">
            <div class="w-50 mt-2">
              <p class="ps-3 text-secondary m-0">Total Quotations</p>
              <p class="ps-3 h4">Rs {{ number_format(($allProformas ?? $proformas)->sum('grand_total'), 2) }}</p>
            </div>
            <div class="w-50 mt-2 d-flex align-items-end justify-content-center flex-column">
              <div class="col-5 h-50 rounded-pill d-flex justify-content-center align-item-center me-4" style="background-color: #DEF7EE;">
                <p class="text-success pt-1">{{ ($allProformas ?? $proformas)->count() > 0 ? round((($allProformas ?? $proformas)->where('status', 'converted')->count() / ($allProformas ?? $proformas)->count()) * 100) : 0 }}% <i class="bi bi-arrow-up-right"></i></p>
              </div>
              <span class="me-4 pe-1 mt-1 text-secondary" style="font-size: 10px;">conversion rate</span>
            </div>
          </div>
          <div class="w-100 d-flex mt-3">
            <p class="ps-3 pe-3 text-secondary" style="border-right:1px solid rgb(45, 44, 44);">Converted : <span class="fw-bold text-dark">Rs {{ number_format(($allProformas ?? $proformas)->where('status', 'converted')->sum('grand_total'), 2) }}</span></p>
            <p class="ps-3 text-secondary">Open : <span class="fw-bold text-dark">Rs {{ number_format(($allProformas ?? $proformas)->where('status', 'open')->sum('grand_total'), 2) }}</span></p>
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
              <form method="GET" action="{{ route('proforma-invoice') }}" class="d-flex gap-2">
                <input type="text" class="form-control form-control-sm" placeholder="Search by Ref No. or Party Name..." name="search" value="{{ $search ?? '' }}" style="border-radius: 20px;">
                <button type="submit" class="btn btn-sm btn-outline-primary" style="border-radius: 20px; white-space: nowrap;">
                  <i class="fas fa-search"></i> Search
                </button>
              </form>
            </div>
          </div>

          <div class="table-responsive">
            <table class="table table-striped table-hover">
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Reference no</th>
                  <th>Party Name</th>
                  <th>Amount</th>
                  <th>Balance</th>
                  <th>Status</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($proformas as $proforma)
                  @php
                    $isConverted = $proforma->status === 'converted';
                    $convertedSaleNumber = $convertedSales[$proforma->id] ?? null;
                    $convertedSaleOrderNumber = $convertedSaleOrders[$proforma->id] ?? null;
                  @endphp
                  <tr>
                    <td>{{ optional($proforma->invoice_date)->format('d/m/Y') ?? '-' }}</td>
                    <td>{{ $proforma->bill_number ?? '-' }}</td>
                    <td>{{ $proforma->display_party_name }}</td>
                    <td>Rs {{ number_format($proforma->items->sum('amount'), 2) }}</td>
                    <td>Rs {{ number_format($proforma->balance ?? $proforma->grand_total ?? 0, 2) }}</td>
                    <td>
                      <span class="badge {{ $isConverted ? 'text-primary bg-primary-subtle border border-primary-subtle' : 'bg-warning text-dark' }}">
                        @if($isConverted)
                          Converted
                        @else
                          {{ ucfirst($proforma->status ?? 'open') }}
                        @endif
                      </span>
                    </td>
                    <td>
                      <div class="dropdown d-inline me-2">
                        <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" {{ $isConverted ? 'disabled' : '' }}>
                          Convert
                        </button>
                        <ul class="dropdown-menu">
                          <li>
                            <a class="dropdown-item {{ $isConverted ? 'disabled' : '' }}" href="{{ $isConverted ? '#' : route('proforma-invoice.convert-to-sale', $proforma->id) }}">
                              <i class="fas fa-file-invoice me-2"></i>Convert to Sale
                            </a>
                          </li>
                          <li>
                            <a class="dropdown-item {{ $isConverted ? 'disabled' : '' }}" href="{{ $isConverted ? '#' : route('proforma-invoice.convert-to-sale-order', $proforma->id) }}">
                              <i class="fas fa-clipboard-list me-2"></i>Convert to Sale Order
                            </a>
                          </li>
                        </ul>
                      </div>
                      <div class="dropdown d-inline">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                          <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <ul class="dropdown-menu">
                          <li><a class="dropdown-item" href="{{ route('proforma-invoice.edit', $proforma->id) }}"><i class="fas fa-edit me-2"></i>View/Edit</a></li>
                          <li><a class="dropdown-item" href="{{ route('proforma-invoice.preview', $proforma->id) }}" target="_blank"><i class="fas fa-file-alt me-2"></i>Preview</a></li>
                          <li><a class="dropdown-item" href="{{ route('proforma-invoice.print', $proforma->id) }}" target="_blank"><i class="fas fa-print me-2"></i>Print</a></li>
                          <li><a class="dropdown-item" href="{{ route('proforma-invoice.pdf', $proforma->id) }}" target="_blank"><i class="fas fa-file-pdf me-2"></i>Open PDF</a></li>
                          <li><a class="dropdown-item" href="{{ route('proforma-invoice.duplicate', $proforma->id) }}"><i class="fas fa-copy me-2"></i>Duplicate</a></li>
                          <li><hr class="dropdown-divider"></li>
                          <li><a class="dropdown-item text-danger" href="#" onclick="deleteProforma('{{ route('proforma-invoice.destroy', $proforma->id) }}'); return false;"><i class="fas fa-trash me-2"></i>Delete</a></li>
                        </ul>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted py-4">No proforma invoices yet.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/components.js') }}"></script>
  <script src="{{ asset('js/common.js') }}"></script>
  <script>
    function deleteProforma(url) {
      if (!confirm('Are you sure you want to delete this proforma invoice?')) {
        return;
      }

      fetch(url, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"]').getAttribute('content'),
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
          alert(error.message || 'Unable to delete proforma invoice.');
        });
    }
  </script>
</body>

</html>
