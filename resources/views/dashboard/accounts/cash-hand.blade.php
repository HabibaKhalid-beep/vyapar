<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vyapar — Cash In Hand</title>
  <meta name="description" content="Record supplier purchase bills with live preview in Vyapar.">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <!-- Custom Styles -->
  <link href="{{ asset('../css/styles.css') }}" rel="stylesheet">

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

  <style>
    .search-container {
      position: relative;
      width: 50px;
      transition: all 0.3s ease;
    }

    .search-container.active {
      width: 250px;
    }

    .search-input {
      width: 100%;
      height: 40px;
      border: none;
      outline: none;
      padding: 0 40px 0 10px;
      border-radius: 20px;
      opacity: 0;
      transition: 0.3s;
    }

    .search-container.active .search-input {
      opacity: 1;
    }

    .search-btn {
      position: absolute;
      right: 5px;
      top: 5px;
      width: 30px;
      height: 30px;
      background: transparent;
      color: #6C757D;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
    }

    .cash-table thead th {
      font-size: 0.85rem;
      color: #0f172a;
      font-weight: 700;
      border-bottom: 1px solid #e5e7eb;
      vertical-align: middle;
      white-space: nowrap;
    }

    .cash-table tbody td {
      font-size: 0.9rem;
      color: #0f172a;
      vertical-align: middle;
      border-bottom: 1px solid #f1f5f9;
    }

    .cash-table .filter-btn {
      border: 0;
      background: transparent;
      color: #111827;
      padding: 0;
      margin-left: 6px;
    }

    .cash-table .amount-cell {
      text-align: right;
      font-weight: 600;
    }
  </style>
</head>

<body data-page="purchase-bill">

  <!-- Navbar & Sidebar injected by components.js -->

  <!-- ═══════════════════════════════════════
     MAIN CONTENT — PURCHASE BILL
     ═══════════════════════════════════════ -->
  <main class="main-content" id="mainContent">


    <div class="d-flex justify-content-between align-items-center bg-light mb-2 p-4">
      <div>
        <span class="mb-0 pe-3  border-end border-dark h4 fw-bold">Cash In Hand</span>
        <span class="ps-3 fs-5 text-success">Rs {{ number_format($cashAccount->opening_balance ?? 0, 2) }}</span>
        <!-- <div class="dropdown">
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
          </div> -->
      </div>

      <button type="button" class="btn rounded-pill" style="background-color: #D4112E;" data-bs-toggle="modal"
        data-bs-target="#exampleModal">
        <span class="text-light"><i class="fas fa-sliders me-2"></i>Adjust Cash</span>
      </button>

      <!-- Modal -->
      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Adjust Cash</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form action="">
                <div class="col-12">
                  <input type="radio" name="adjust" id="cash" class="me-2"> <label for="credit" class="fs-5">Add
                    Cash</label>

                  <input type="radio" name="adjust" id="cash" class="me-2 ms-3"> <label for="credit" class="fs-5">Reduce
                    Cash</label>
                </div>
                <div class="mt-3">
                  <label for="amount">Enter Amount <span class="text-danger ">*</span></label>
                  <input type="number" class="form-control" id="amount">
                  <span class="mt-1 text-secondary h6">Updated Cash: </span><span>Rs 75,874</span>
                </div>
                <div class="mt-3">
                  <label for="date">Adjustment Date</label>
                  <input type="date" class="form-control" id="date">
                </div>
                 <div class="mt-3">
                  <label for="description">Description</label>
                  <input type="text" class="form-control" id="description">
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
              <button type="button" class="btn btn-danger rounded-pill">Save</button>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="card shadow-sm border-0">
      <div class="card-body">
        <!-- <div class="row g-2 mb-3">
          <p class="fw-bold">Transactions</p>
        </div> -->
        <div class="col-12 g-2 mb-3 d-flex flex-wrap justify-content-between">
          <p class="fw-bold">Transactions</p>

          <div class="d-flex">
            <div class="search-container">
              <input type="text" class="search-input" placeholder="Search...">
              <span class="search-btn">
                <i class="fa fa-search"></i>
              </span>
            </div>

          </div>

        </div>

        <div class="table-responsive small-table">
          <table class="table table-hover mb-0 align-middle cash-table">
            <thead>
              <tr>
                <th style="width: 18%;">Type <button class="filter-btn" type="button"><i class="fa-solid fa-filter"></i></button></th>
                <th>Name <button class="filter-btn" type="button"><i class="fa-solid fa-filter"></i></button></th>
                <th style="width: 18%;">Date <button class="filter-btn" type="button"><i class="fa-solid fa-filter"></i></button></th>
                <th style="width: 18%;" class="text-end">Amount <button class="filter-btn" type="button"><i class="fa-solid fa-filter"></i></button></th>
              </tr>
            </thead>
            <tbody>
              @forelse($cashTransactions as $transaction)
                <tr>
                  <td>{{ strtoupper(str_replace('_', ' ', $transaction->type ?? 'cash')) }}</td>
                  <td>{{ $transaction->description ?? '-' }}</td>
                  <td>{{ \Illuminate\Support\Carbon::parse($transaction->transaction_date ?? $transaction->created_at)->format('d/m/Y') }}</td>
                  <td class="amount-cell">Rs {{ number_format($transaction->amount ?? 0, 2) }}</td>
                </tr>
              @empty
                <tr>
                  <td colspan="4" class="text-center text-muted py-4">
                    No cash transactions yet.
                  </td>
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
  <script src="{{ asset('../js/components.js') }}"></script>
  <script src="{{ asset('../js/common.js') }}"></script>
  <script src="{{ asset('../js/cash-in-hand.js') }}"></script>
  <script>
    $(document).ready(function () {
      $(".search-btn").click(function () {
        $(".search-container").toggleClass("active");
        $(".search-input").focus();
      });
    });
  </script>

</body>

</html>
