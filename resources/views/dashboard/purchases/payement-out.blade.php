<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vyapar — Payment Out</title>
  <meta name="description" content="Record money paid to suppliers as Payment Out vouchers in Vyapar.">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <!-- Font Awesome 6 -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
  <!-- Custom Styles -->
  <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
</head>

<body data-page="payment-out">

  <!-- Navbar & Sidebar injected by components.js -->

  <main class="main-content" id="mainContent">
    <div class="d-flex justify-content-between align-items-center bg-light mb-2 p-4">
      <div>
        <!-- <h4 class="mb-0">Estimates / Quotations</h4> -->
        <div class="dropdown">
          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="h4">Payment Out</span>
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
      <button class="btn rounded-pill" style="background-color: #D4112E;"><span class="text-light">+ Add
          Payment Out</span></button>
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
            <p class="ps-3 text-secondary m-0">Total Amount</p>
            <p class="ps-3 h4">Rs 0</p>
          </div>
          <div class="w-50 mt-2 d-flex align-items-end justify-content-center flex-column">
            <div class="col-5 h-50 rounded-pill d-flex justify-content-center align-item-center me-4"
              style="background-color: #F2F2F2;">
              <p class="text-secondary fw-bold pt-1">0% <i class="bi bi-arrow-up-right "></i></>
              </p>
            </div>
            <span class="me-4 pe-1 mt-1 text-secondary" style="font-size: 10px;">vs last month</span>
          </div>
        </div>
        <div class="w-100 d-flex mt-3">
          <p class="ps-3 pe-3 text-secondary">Paid : <span
              class="fw-bold text-dark">Rs 0.00</span></p>


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
                  <p class="pt-1">Reference No.</p>
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
                        <p class="mb-0" style="font-size: 11px;">Reference No.</p>
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

  <!-- Scripts -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/components.js') }}"></script>
  <script src="{{ asset('js/common.js') }}"></script>
  <script src="{{ asset('js/payment-out.js') }}"></script>

</body>

</html>
