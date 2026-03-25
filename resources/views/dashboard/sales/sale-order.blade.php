<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vyapar — Sale Orders</title>
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

<body data-page="sale-orders">

  <!-- Navbar & Sidebar injected by components.js -->

  <!-- ═══════════════════════════════════════
     MAIN CONTENT — PURCHASE BILL
     ═══════════════════════════════════════ -->
  <main class="main-content" id="mainContent">


    <div class="d-flex justify-content-between align-items-center bg-light p-3 border-bottom mb-2">
      <div class="text-center col-12">
        <h4 class="text-secondary">Sales Orders</h4>

      </div>

    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="row g-2 mb-1">
          <p class="fw-bold">Transactions</p>
        </div>
        <div class="col-12 d-flex justify-content-between">
          <div class="topbar-search">
            <span class="search-icon"><i class="bi bi-search"></i></span>
            <input type="text" placeholder="Search...">
          </div>
          <div>
            <button class="btn border-0 bg-primary rounded text-white fw-bold"><span
                class="bg-light text-primary rounded-circle" style="padding: 0px 4px;">+</span> Add Delivery
              Challan</button>
          </div>
        </div>

        <div class="table-responsive small-table">
          <table class="table table-hover mb-0 align-middle table-clean">
            <thead>
              <tr class="d-flex gap-3">
                <th class="d-flex">
                  <p class="pt-1">Party</p>
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
                  <p class="pt-1">No.</p>
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
                        <p class="mb-0" style="font-size: 11px;">No.</p>
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
                  <p class="pt-1">Due Date</p>
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
                  <p class="pt-1">Total Amount</p>
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
                        <p class="mb-0" style="font-size: 11px;">Total Amount</p>
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
                  <p class="pt-1">Balance</p>
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
                        <p class="mb-0" style="font-size: 11px;">Balance</p>
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
                <th>
                   <p class="pt-1">Type</p>
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
                        <input type="checkbox"><span class="ms-1">Overdue</span>
                      </li>
                      <li class="dropdown-item">
                        <input type="checkbox"><span class="ms-1">Completed</span>
                      </li>
                      <li class="dropdown-item">
                        <input type="checkbox"><span class="ms-1">Partial Open</span>
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
  <script src="{{ asset('js/sale-orders.js') }}"></script>

</body>

</html>
