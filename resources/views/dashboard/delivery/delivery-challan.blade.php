<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vyapar — Delivery Challan</title>
  <meta name="description" content="Record supplier purchase bills with live preview in Vyapar.">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <!-- Custom Styles -->
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">


   <script>
    // Ensure window.App is always initialized, even if Auth is null
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
    console.log('App initialized:', window.App);
  </script>


</head>

<body data-page="delivery-challan">

  <!-- Navbar & Sidebar injected by components.js -->

  <!-- ═══════════════════════════════════════
     MAIN CONTENT — PURCHASE BILL
     ═══════════════════════════════════════ -->
  <main class="main-content" id="mainContent">


    <div class="d-flex justify-content-between align-items-center bg-light p-4 border-bottom mb-2">
      <div class="col-12 text-center">
        <h4 class="mb-0 text-secondary">Delivery Challan</h4>
      </div>

    </div>
    <div class="d-flex align-items-center bg-light px-4 py-2 rounded mb-2">
      <div class="d-flex">
        <!-- <div class="d-flex justify-content-center align-items-center me-2">Filter By: </div> -->
        <select name="" id="" class="bg-transparent border-0 fs-5 fw-bold" style="outline:none;">
          <option value="">All Purchase Invoices</option>
          <option value="" selected>This Month</option>
          <option value="">Last Month</option>
          <option value="">This Quarter</option>
          <option value="">This Year</option>
          <option value="">Custom</option>
        </select>
      </div>
      <div class="d-flex pt-3 ps-2">
        <p class="text-center pt-2 text-white fw-bold"
          style="width: 6rem; height: 40px; background-color: #AAAAAA; border-radius: 5px 0px 0px 5px">Between</p>
        <div class="d-flex justify-content-center pt-2 gap-3"
          style="width: 20rem; height: 40px; border-radius: 0px 5px 5px 0px; border: 1px solid #AAAAAA">
          <p>01/03/2026</p>
          <p>To</p>
          <p>31/03/2026</p>
        </div>

      </div>
      <div class="d-flex justify-content-center align-items-center ms-4"
        style="border: 1px solid #AAAAAA;border-radius: 5px; width: 8rem; height: 40px;"><select name="" id=""
          class="bg-transparent border-0" style="outline:none;">
          <option value="" selected>All Firms</option>
          <option value=""><a href="">Firm 1</a></option>
          <option value=""><a href="">Firm 2</a></option>
          <option value=""><a href="">Firm 3</a></option>

        </select></div>
      <div class="px-5 mx-5"></div>
      <div class="d-flex gap-5 text-secondary">
        <i class="fa-solid fa-file-excel fs-5"></i>
        <i class="fa-solid fa-print fs-5"></i>
      </div>

    </div>



    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="row g-2 mb-3">
          <p class="fw-bold">Transactions</p>
        </div>
        <div class="col-12 d-flex justify-content-between">
          <div class="topbar-search ms-3">
            <span class="search-icon"><i class="bi bi-search"></i></span>
            <input type="text" placeholder="Search...">
          </div>

          <button onclick="window.location.href='{{ route('create-challan') }}'"
        class="btn btn-primary rounded">
    <span class="text-primary bg-light rounded-circle" style="padding: 0px 4px;">+</span>
    Add Delivery Challan
</button>
        </div>

       <div class="table-responsive">
          <table class="table align-middle mb-0" id="challanTable">
            <thead>
              <tr class="text-uppercase small text-secondary">
                <th class="py-3">Date</th>
                <th class="py-3">Party</th>
                <th class="py-3">Challan No.</th>
                <th class="py-3">Due Date</th>
                <th class="py-3 text-end">Total Amount</th>
                <th class="py-3">Status</th>
                <th class="py-3">Action</th>
                <th class="py-3 text-center" style="width:56px;"></th>
              </tr>
            </thead>
            <tbody>
              @forelse($challans as $challan)
                @php
                  $isClosed = $challan->status === 'closed';
                  $isOverdue = !$isClosed && $challan->due_date && $challan->due_date->isPast();
                  $convertedInvoice = $convertedInvoices[$challan->id] ?? null;
                  $convertedInvoiceNumber = $convertedInvoice->bill_number ?? null;
                  $overdueDays = $isOverdue ? max(1, $challan->due_date->copy()->startOfDay()->diffInDays(now()->copy()->startOfDay())) : 0;
                @endphp
                <tr>
                  <td>{{ optional($challan->invoice_date)->format('d/m/Y') ?? '-' }}</td>
                  <td>{{ $challan->display_party_name }}</td>
                  <td>{{ $challan->bill_number ?? '-' }}</td>
                  <td>
                    <div>{{ optional($challan->due_date)->format('d/m/Y') ?? '-' }}</div>
                    @if($isOverdue)
                      <span class="badge text-bg-light text-secondary mt-1">Overdue: {{ $overdueDays }} {{ $overdueDays === 1 ? 'day' : 'days' }}</span>
                    @endif
                  </td>
                  <td class="text-end fw-semibold">Rs {{ number_format($challan->grand_total ?? 0, 2) }}</td>
                  <td>
                    @if($isClosed)
                      <span class="text-primary fw-semibold">Closed</span>
                      <span class="badge text-bg-light text-secondary">{{ optional($challan->updated_at)->format('d/m/Y') }}</span>
                    @else
                      <span class="text-primary fw-semibold">Open</span>
                    @endif
                  </td>
                  <td>
                    @if($isClosed)
                      <a href="{{ $convertedInvoice ? route('sale.edit', $convertedInvoice->id) : '#' }}" class="text-decoration-underline text-primary">
                        Converted To Invoice No.{{ $convertedInvoiceNumber ?? '-' }}
                      </a>
                    @else
                      <a href="{{ route('delivery-challans.convert-to-sale', $challan->id) }}" class="btn btn-sm btn-light border text-uppercase text-primary px-3">
                        Convert To Sale
                      </a>
                    @endif
                  </td>
                  <td class="text-center">
                    <div class="dropdown">
                      <button class="btn btn-sm border-0 text-secondary" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-ellipsis-vertical"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><a class="dropdown-item" href="{{ route('delivery-challan.edit', $challan->id) }}">View/Edit</a></li>
                        <li><a class="dropdown-item" href="#" onclick="deleteChallan('{{ route('delivery-challan.destroy', $challan->id) }}'); return false;">Delete</a></li>
                        <li><a class="dropdown-item" href="{{ route('delivery-challan.pdf', $challan->id) }}" target="_blank">Open PDF</a></li>
                        <li><a class="dropdown-item" href="{{ route('delivery-challan.preview', $challan->id) }}" target="_blank">Preview</a></li>
                        <li><a class="dropdown-item" href="{{ route('delivery-challan.print', $challan->id) }}" target="_blank">Print</a></li>
                        <li><a class="dropdown-item" href="{{ route('delivery-challan.duplicate', $challan->id) }}">Duplicate</a></li>
                      </ul>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="8" class="text-center text-muted py-5">No delivery challans found.</td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>

      </div>
    </div>

  </main>

  <!-- ═══════════════════════════════════════════
     SCRIPTS
     ═══════════════════════════════════════════ -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/components.js') }}"></script>
  <script src="{{ asset('js/common.js') }}"></script>
  <script src="{{ asset('js/delivery_challan.js') }}"></script>

</body>

</html>


