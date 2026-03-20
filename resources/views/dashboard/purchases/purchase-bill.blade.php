<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vyapar — Purchase Bill</title>
  <meta name="description" content="Record supplier purchase bills with live preview in Vyapar.">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <!-- Custom Styles -->
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>

<body data-page="purchase-bill">

  <!-- Navbar & Sidebar injected by components.js -->

  <!-- ═══════════════════════════════════════
     MAIN CONTENT — PURCHASE BILL
     ═══════════════════════════════════════ -->
  <main class="main-content" id="mainContent">


    <div class="d-flex justify-content-between align-items-center bg-light mb-2 p-4">
      <div>
        <h4 class="mb-0">Purchase Bills</h4>
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
      <button class="btn rounded-pill" style="background-color: #D4112E;"><span class="text-light">+ Add
          Purchase</span></button>
    </div>
    <div class="d-flex align-items-center bg-light px-4 py-2 rounded">
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
    </div>
    <div class="bg-light mb-2 px-4 py-3 rounded d-flex">
      <div class="rounded d-flex flex-column ps-3 pt-2" style="width: 11rem; height:5rem; background-color: #B9F3E7;">
        <p class="mb-1 h5">Paid</p>
        <p class="fs-4 fw-bold">Rs 0.00</p>
      </div>
      <div class="d-flex align-items-center mx-3"><span class="h2">+</span></div>
      <div class="rounded" style="width: 11rem; height:5rem; background-color: #B9F3E7;">
         <div class="rounded d-flex flex-column ps-3 pt-2" style="width: 11rem; height:5rem; background-color: #CFE6FE;">
        <p class="mb-1 h5">Unpaid</p>
        <p class="fs-4 fw-bold">Rs 0.00</p>
      </div>
      </div>
      <div class="d-flex align-items-center mx-3"><span class="h2">=</span></div>
      <div class="rounded" style="width: 11rem; height:5rem; background-color: #B9F3E7;">
         <div class="rounded d-flex flex-column ps-3 pt-2" style="width: 11rem; height:5rem; background-color: #F8C889;">
        <p class="mb-1 h5">Total</p>
        <p class="fs-4 fw-bold">Rs 0.00</p>
      </div>
      </div>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="row g-2 mb-3">
          <p class="fw-bold">Transactions</p>
        </div>

        <div class="table-responsive small-table">
          <table class="table table-hover mb-0 align-middle table-clean">
            <thead>
              <tr class="d-flex gap-3">
                <th class="d-flex">
                  <p class="pt-1">Date</p>
                  <div class="dropdown ms-3">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa-solid fa-filter"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li class="dropdown-item">
                        <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                        <select name="" id="" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                          <option value="" selected>Equal to</option>
                          <option value=""><a href="">Less than</a></option>
                          <option value=""><a href="">Greater than</a></option>
                          <option value=""><a href="">Range</a></option>
                        </select>
                      </li>
                      <li class="dropdown-item">
                        <p class="mb-0" style="font-size: 11px;">Select Date:</p>
                        <input type="date" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                      </li>
                      <div class="mt-2 ms-4">
                        <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                            style="color: #71748E;">Clear</span></button>
                        <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                            class="text-light">Apply</span></button>
                      </div>

                    </ul>
                  </div>
                </th>
                <th class="d-flex">
                  <p class="pt-1">Invoice No.</p>
                  <div class="dropdown ms-3">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa-solid fa-filter"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li class="dropdown-item">
                        <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                        <select name="" id="" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                          <option value="" selected>Equal to</option>
                          <option value=""><a href="">Less than</a></option>
                          <option value=""><a href="">Greater than</a></option>
                          <option value=""><a href="">Range</a></option>
                        </select>
                      </li>
                      <li class="dropdown-item">
                        <p class="mb-0" style="font-size: 11px;">Party Name</p>
                        <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                      </li>
                      <div class="mt-2 ms-3">
                        <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                            style="color: #71748E;">Clear</span></button>
                        <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                            class="text-light">Apply</span></button>
                      </div>

                    </ul>
                  </div>
                </th>
                <th class="d-flex">
                  <p class="pt-1">Party Name</p>
                  <div class="dropdown ms-3">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa-solid fa-filter"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li class="dropdown-item">
                        <p class="mb-0" style="font-size: 11px;">Select Category:</p>
                        <select name="" id="" class="bg-transparent border py-2 rounded w-100" style="outline:none;">
                          <option value="" selected>Equal to</option>
                          <option value=""><a href="">Less than</a></option>
                          <option value=""><a href="">Greater than</a></option>
                          <option value=""><a href="">Range</a></option>
                        </select>
                      </li>
                      <li class="dropdown-item">
                        <p class="mb-0" style="font-size: 11px;">Party Name</p>
                        <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;">

                      </li>
                      <div class="mt-2 ms-3">
                        <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                            style="color: #71748E;">Clear</span></button>
                        <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                            class="text-light">Apply</span></button>
                      </div>

                    </ul>
                  </div>
                </th>
                <th class="d-flex">
                  <p class="pt-1">Price Range</p>
                  <div class="dropdown ms-3">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa-solid fa-filter"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li class="dropdown-item">
                        <p class="mb-0" style="font-size: 11px;">Price Range</p>
                        <input type="range" min="" max="" value="100">
                      </li>
                      <li class="dropdown-item">
                        <p class="mb-0" style="font-size: 11px;">Min</p>
                        <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;"
                          placeholder="0">
                        <p class="mb-0 mt-1" style="font-size: 11px;">Max</p>
                        <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;"
                          placeholder="+500000">

                      </li>
                      <div class="mt-2 ms-4">
                        <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                            style="color: #71748E;">Clear</span></button>
                        <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                            class="text-light">Apply</span></button>
                      </div>

                    </ul>
                  </div>
                </th>
                <th class="d-flex">
                  <p class="pt-1">Balance</p>
                  <div class="dropdown ms-3">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa-solid fa-filter"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li class="dropdown-item">
                        <p class="mb-0" style="font-size: 11px;">Price Range</p>
                        <input type="range" min="" max="" value="100">
                      </li>
                      <li class="dropdown-item">
                        <p class="mb-0" style="font-size: 11px;">Min</p>
                        <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;"
                          placeholder="0">
                        <p class="mb-0 mt-1" style="font-size: 11px;">Max</p>
                        <input type="text" class="bg-transparent border py-2 rounded w-100" style="outline:none;"
                          placeholder="+500000">

                      </li>
                      <div class="mt-2 ms-4">
                        <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                            style="color: #71748E;">Clear</span></button>
                        <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                            class="text-light">Apply</span></button>
                      </div>

                    </ul>
                  </div>
                </th>
                <th class="d-flex">
                  <p class="pt-1">Status</p>
                  <div class="dropdown ms-3">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa-solid fa-filter"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li class="dropdown-item">
                        <input type="checkbox"><span class="ms-1">Open</span>
                      </li>
                      <li class="dropdown-item">
                        <input type="checkbox"><span class="ms-1">Completed</span>
                      </li>
                      <div class="mt-2 ms-4">
                        <button class="btn rounded-pill" style="background-color: #EBEAEA;"><span
                            style="color: #71748E;">Clear</span></button>
                        <button class="btn rounded-pill" style="background-color: #D4112E;"><span
                            class="text-light">Apply</span></button>
                      </div>

                    </ul>
                  </div>
                </th>
                <th class="d-flex">
                  <p class="pt-1">Actions</p>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td colspan="7" class="text-center text-muted py-4">
                  No estimates yet. Click "New Estimate" to create one.
                </td>
              </tr>
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
  <script src="{{ asset('js/purchase-bill.js') }}"></script>

</body>

</html>
