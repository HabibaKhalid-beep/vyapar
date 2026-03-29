<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Vyapar — Estimate / Quotation</title>
  <meta name="description" content="Create professional estimates and quotations for your customers in Vyapar.">

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
      background: #6C757D;
      color: white;
      border-radius: 50%;
      display: flex;
      justify-content: center;
      align-items: center;
      cursor: pointer;
    }
  </style>
</head>

<body data-page="payment-in">

  <!-- Navbar & Sidebar injected by components.js -->

  <!-- ═══════════════════════════════════════
     MAIN CONTENT — ESTIMATE / QUOTATION
     ═══════════════════════════════════════ -->
  <main class="main-content" id="mainContent">

    <div class="d-flex justify-content-between align-items-center bg-light mb-2 p-4">
      <div>
        <!-- <h4 class="mb-0">Estimates / Quotations</h4> -->
        <div class="dropdown">
          <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
            <span class="h4">Payment In</span>
          </button>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="invoice.html">Sale Invoice</a></li>
            <li><a class="dropdown-item" href="sale-estimate.html">Estimate / Quotation</a></li>
            <li><a class="dropdown-item" href="sale-return.html">Sale Return / Cr. Note</a></li>
            <li><a class="dropdown-item" href="payment_in.html">Payment In</a></li>
            <li><a class="dropdown-item" href="payment-out.html">Payment out</a></li>
            <li><a class="dropdown-item" href="purchase-bill.html">Purchase Bill</a></li>
            <li><a class="dropdown-item" href="purchase-return.html">Purchase Return / Dr. Note</a></li>
            <li><a class="dropdown-item" href="expenses.html">Expenses</a></li>

          </ul>
        </div>
      </div>
      <div>
        <button class="btn rounded-pill" style="background-color: #D4112E;" data-bs-toggle="modal" data-bs-target="#addPaymentInModal">
    <span class="text-light">+ Add Payment-in</span>
</button>
   <div class="modal fade" id="addPaymentInModal" tabindex="-1" aria-labelledby="addPaymentInModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl"> <!-- Expanded modal width -->
    <div class="modal-content">

      <!-- Modal Header -->
      <div class="modal-header d-flex justify-content-between align-items-center">
        <h5 class="modal-title" id="addPaymentInModalLabel">Payment-in</h5>
        <div class="d-flex gap-2">
          <button type="button" class="btn btn-light">
            <i class="fa-solid fa-calculator"></i>
          </button>
          <button type="button" class="btn btn-light">
            <i class="fa-solid fa-gear"></i>
          </button>
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">
            <i class="fa-solid fa-xmark"></i>
          </button>
        </div>
      </div>

      <!-- Modal Body -->
      <div class="modal-body">
        <form id="paymentInForm">

          <div class="row">

            <!-- Left Side: Party + Payment Section -->
            <div class="col-lg-7">

              <!-- Party Selection -->
              <div class="mb-3">
                <div class="dropdown">
                  <button class="btn btn-light dropdown-toggle w-100 text-start" type="button" id="partyDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <span id="partyDropdownBtnText">Select Party</span>
                  </button>
                  <ul class="dropdown-menu w-100" id="partyDropdownMenu" style="max-height: 300px; overflow-y: auto;">
                    @foreach($parties as $party)
                    <li class="party-option dropdown-item d-flex justify-content-between align-items-center" style="cursor: pointer;"
                        data-id="{{ $party->id }}"
                        data-opening="{{ $party->opening_balance ?? 0 }}"
                        data-type="{{ $party->transaction_type ?? '' }}"
                        data-phone="{{ $party->phone ?? '' }}"
                        data-billing="{{ addslashes($party->billing_address ?? '') }}">
                      <span class="party-name cursor-pointer">{{ $party->name }}</span>
                      <span class="party-balance small text-muted">
                        @if($party->transaction_type == 'pay')
                          <i class="fa-solid fa-arrow-up text-danger me-1"></i> ₹{{ number_format($party->opening_balance, 2) }}
                        @elseif($party->transaction_type == 'receive')
                          <i class="fa-solid fa-arrow-down text-success me-1"></i> ₹{{ number_format($party->opening_balance, 2) }}
                        @else
                          ₹{{ number_format($party->opening_balance, 2) }}
                        @endif
                      </span>
                    </li>
                    @endforeach
                    <li class="dropdown-item text-primary" id="addNewPartyBtn">+ Add New Party</li>
                  </ul>
                </div>

                <input type="hidden" class="party-id">
                <input type="hidden" class="phone-input">
                <input type="hidden" class="billing-address">
                <div id="partyBalanceDisplay" class="mt-1"></div>
              </div>

              <!-- Payment Section -->
              <div id="paymentContainer">

                <div style="width: auto; height: auto; padding: 22px; border-radius: 1px; border: 1px solid #ced4da; box-shadow: 0 2px 6px rgba(0,0,0,0.12);">

                <div class="row align-items-end payment-row mb-2">

  <!-- Payment Type -->
  <div class="col-md-5">
    <label class="form-label">Payment Type</label>
    <select class="form-select payment-type">
      <option value="">-- Select Type --</option>
      <option value="cash">Cash</option>
      <option value="remote">Remote</option>
    </select>
  </div>

  <!-- Amount -->
  <div class="col-md-5">
    <label class="form-label">Amount</label>
    <input type="number" class="form-control payment-amount" placeholder="Enter amount">
  </div>

  <!-- Delete -->
  <div class="col-md-2 d-flex align-items-center">
    <button type="button" class="remove-row border-0 bg-transparent text-secondary" style="display:none; font-size:18px;">
      <i class="fa-solid fa-trash"></i>
    </button>
  </div>

</div>

<!-- Add Payment Type Button -->
<div class="mt-2">
  <button type="button" id="addPaymentRow" class="btn p-0 text-primary border-0 bg-transparent">
    + Add Payment Type
  </button>
</div>

                  <!-- Reference No -->
                  <div class="mb-3">
                    <label for="referenceNo" class="form-label">Reference No</label>
                    <input type="text" class="form-control" id="referenceNo" placeholder="Enter reference number">
                  </div>

               

                </div>

              </div>

              <!-- Extra Buttons -->
              <div class="d-flex flex-column gap-2 mt-3 align-items-start">
                <button type="button" class="btn d-flex align-items-center gap-2 border w-auto"
                        style="border-color:#ced4da; background-color:#fff; padding: 6px 12px;">
                  <i class="fa-solid fa-file text-secondary" style="font-size:20px;"></i>
                  <span class="text-secondary">Add Description</span>
                </button>

                <button type="button" class="btn d-flex align-items-center gap-2 border w-auto"
                        style="border-color:#ced4da; background-color:#fff; padding: 6px 12px;">
                  <i class="fa-solid fa-camera text-secondary" style="font-size:20px;"></i>
                  <span class="text-secondary">Upload Image</span>
                </button>
              </div>

            </div> <!-- End Left Side -->

            <!-- Right Side: Receipt No + Date + Received -->
            <div class="col-lg-5">

              <div class="mb-3">
                <label class="form-label text-secondary">Receipt No</label>
                <input type="text" class="form-control" placeholder="Receipt No">
              </div>

              <div class="mb-3">
                <label class="form-label text-secondary">Date</label>
                <input type="date" class="form-control">
              </div>

              <div class="mb-3">
                <label class="form-label text-secondary">Received</label>
                <input type="text" class="form-control" placeholder="Received">
              </div>

            </div> <!-- End Right Side -->

          </div> <!-- End Main Row -->

        </form>
      </div>

      <!-- Modal Footer -->
<!-- Modal Footer -->
  <!-- Modal Footer -->
<div class="modal-footer d-flex justify-content-between align-items-center">

  <!-- Top-left green button -->
  <button type="button" class="btn text-white" style="background-color: #28a745;">
    Link Payment
  </button>

  <!-- Right side buttons -->
  <div class="d-flex gap-2">

    <!-- Share Dropdown -->
    <!-- Share Dropdown -->
<div class="dropdown">
  <button class="btn btn-secondary dropdown-toggle" type="button" id="shareDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
    Share
  </button>
  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="shareDropdownBtn" data-bs-display="static" style="margin-top:0;">
    <li><a class="dropdown-item" href="#">Share via Email</a></li>
    <li><a class="dropdown-item" href="#">Share via WhatsApp</a></li>
    <li><a class="dropdown-item" href="#">Share via Link</a></li>
  </ul>
</div>

    <!-- Save Payment-in button -->
    <button type="submit" class="btn btn-primary" form="paymentInForm">Save Payment-in</button>

  </div>

</div>
    </div>
  </div>
</div>
        <span class="mx-3 py-1" style="border-right: 1px solid rgb(45, 44, 44);"></span>
        <span class="text-secondary fs-5 pt-1"><i class="fas fa-gear"></i></span>
      </div>


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
            <p class="ps-3 h4">Rs 100</p>
          </div>
          <div class="w-50 mt-2 d-flex align-items-end justify-content-center flex-column">
            <div class="col-5 h-50 rounded-pill d-flex justify-content-center align-item-center me-4"
              style="background-color: #DEF7EE;">
              <p class="text-success pt-1">100% <i class="bi bi-arrow-up-right "></i></>
              </p>
            </div>
            <span class="me-4 pe-1 mt-1 text-secondary" style="font-size: 10px;">vs last month</span>
          </div>
        </div>
        <div class="w-100 d-flex mt-3">
          <p class="ps-3 pe-3 text-secondary">Received : <span class="fw-bold text-dark">Rs 100</span></p>


        </div>
      </div>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="col-12 g-2 mb-3 d-flex flex-wrap justify-content-between">
          <p class="fw-bold">Transactions</p>

          <div class="d-flex">
            <div class="search-container">
              <input type="text" class="search-input" placeholder="Search...">
              <span class="search-btn">
                <i class="fa fa-search"></i>
              </span>
            </div>
            <div class="mt-1 pt-1 ms-3">
              <span class="mx-3 fs-4 text-secondary"><i class="fas fa-file-excel"></i></span>
              <span class="mx-3 fs-4 text-secondary"><i class="fas fa-print"></i></span>
            </div>

          </div>

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
                  <p class="pt-1">Received</p>
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
                  <p class="pt-1">Payment Type</p>
                  <div class="dropdown ms-3">
                    <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="fa-solid fa-filter"></i>
                    </button>
                    <ul class="dropdown-menu">
                      <li class="dropdown-item">
                        <input type="checkbox"><span class="ms-1">Cash</span>
                      </li>
                      <li class="dropdown-item">
                        <input type="checkbox"><span class="ms-1">Check</span>
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

  <!-- ADD PARTY MODAL -->
<div class="modal fade" id="addPartyModal" tabindex="-1" >
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          <i class="fa-solid fa-user-plus me-2"></i> Add Party
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">
        <form id="addPartyForm">
          @csrf
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-600">Party Name <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" placeholder="Enter party name" id="partyNameInput" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Phone Number</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fa-solid fa-phone"></i></span>
                <input type="tel" name="phone" class="form-control" placeholder="Enter phone number" id="partyPhoneInput">
              </div>
            </div>
          </div>

          <!-- Tabs -->
          <ul class="nav nav-tabs" id="partyModalTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="party-address-tab" data-bs-toggle="tab" data-bs-target="#partyAddressPane" type="button" role="tab">
                <i class="fa-solid fa-location-dot me-1"></i> Address
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="party-credit-tab" data-bs-toggle="tab" data-bs-target="#partyCreditPane" type="button" role="tab">
                <i class="fa-solid fa-credit-card me-1"></i> Credit & Balance
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="party-additional-tab" data-bs-toggle="tab" data-bs-target="#partyAdditionalPane" type="button" role="tab">
                <i class="fa-solid fa-sliders me-1"></i> Additional Fields
              </button>
            </li>
          </ul>

          <div class="tab-content pt-3" id="partyModalTabContent">
            <!-- Address Tab -->
            <div class="tab-pane fade show active" id="partyAddressPane" role="tabpanel">
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label">Email ID</label>
                  <input type="email" name="email" class="form-control" placeholder="example@email.com">
                </div>
                <div class="col-md-6"></div>
                <div class="col-md-6">
                  <label class="form-label">Billing Address</label>
                  <textarea id="billingAddress" class="form-control" name="billing_address" rows="3" placeholder="Enter billing address"></textarea>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Shipping Address</label>
                  <textarea  id="shippingAddress" class="form-control" name="shipping_address" rows="3" placeholder="Enter shipping address"></textarea>
                </div>
              </div>
            </div>

            <!-- Credit & Balance Tab -->
          <div class="tab-pane fade" id="partyCreditPane" role="tabpanel">
  <div class="row g-3">
    <div class="col-md-4">
      <label class="form-label">Opening Balance</label>
      <div class="input-group">
        <span class="input-group-text">₹</span>
        <input type="number" name="opening_balance" class="form-control" placeholder="0.00">
      </div>
    </div>
    <div class="col-md-4">
      <label class="form-label">As Of Date</label>
      <input type="date" name="as_of_date" class="form-control" value="{{ date('Y-m-d') }}">
    </div>
    <div class="col-md-4">
      <label class="form-label d-block">Credit Limit</label>
      <div class="form-check form-switch mt-2">
        <input class="form-check-input" name="credit_limit_enabled" type="checkbox" id="creditLimitSwitch">
        <label class="form-check-label" for="creditLimitSwitch">Enable</label>
      </div>
    </div>
  </div>

  <!-- To Receive / To Pay Options at the bottom -->
  <div class="mt-4">
    <label class="form-label d-block">Transaction Type</label>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" id="toReceive" value="receive">
      <label class="form-check-label" for="toReceive">To Receive</label>
    </div>
    <div class="form-check form-check-inline">
      <input class="form-check-input" type="checkbox" id="toPay" value="pay">
      <label class="form-check-label" for="toPay">To Pay</label>
    </div>
  </div>
</div>
<div class="col-md-6">
  <label class="form-label fw-600">Party Type</label>

  <div class="form-check">
    <input class="form-check-input" type="radio" name="party_type" id="customerParty" value="customer" checked>
    <label class="form-check-label" for="customerParty">Customer Party</label>
  </div>

  <div class="form-check">
    <input class="form-check-input" type="radio" name="party_type" id="supplierParty" value="supplier">
    <label class="form-check-label" for="supplierParty">Supplier Party</label>
  </div>
</div>

            <!-- Additional Fields Tab -->
            <div class="tab-pane fade" id="partyAdditionalPane" role="tabpanel">
              <p class="text-muted mb-3" style="font-size:13px;">Add custom fields to track additional information.</p>
              <div class="row g-3">
                @for($i=1; $i<=4; $i++)
                <div class="col-md-6">
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="checkbox" id="customField{{$i}}Check">
                    <label class="form-check-label" for="customField{{$i}}Check">Custom Field {{$i}}</label>
                  </div>
                  <input type="text" name="custom_fields[]" class="form-control form-control-sm" placeholder="Field name">
                </div>

                <input type="hidden" id="transactionTypeValue" name="transaction_type">
                @endfor

              </div>
            </div>
          </div>
          

          <div class="modal-footer">
            <button type="button" class="btn btn-outline-primary" id="btnSaveNewParty">
              <i class="fa-solid fa-plus me-1"></i> Save & New
            </button>
            <button type="button" class="btn btn-primary" id="btnSaveParty">
              <i class="fa-solid fa-check me-1"></i> Save
            </button>

          </div>
        </form>
      </div>
      

    </div>
  </div>
</div>
 
  <!-- ═══════════════════════════════════════════
     SCRIPTS
     ═══════════════════════════════════════════ -->
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/components.js') }}"></script>
  <script src="{{ asset('js/common.js') }}"></script>
  <script src="{{ asset('js/payment_in.js') }}"></script>
  <script>
    $(document).ready(function () {
      $(".search-btn").click(function () {
        $(".search-container").toggleClass("active");
        $(".search-input").focus();
      });
    });
  </script>

  <script>

  document.addEventListener("DOMContentLoaded", function () {
    // Elements
    const dropdownBtn = document.getElementById("partyDropdownBtn");
    const dropdownBtnText = document.getElementById("partyDropdownBtnText");
    const dropdownMenu = document.getElementById("partyDropdownMenu");

    const partyIdInput = document.querySelector(".party-id");
    const phoneInput = document.querySelector(".phone-input");
    const billingInput = document.querySelector(".billing-address");
    const balanceDisplay = document.getElementById("partyBalanceDisplay");

    const addModalEl = document.getElementById('addPartyModal');
    const addModal = addModalEl ? new bootstrap.Modal(addModalEl) : null;

    const saveBtn = document.getElementById("btnSaveParty");
    const saveNewBtn = document.getElementById("btnSaveNewParty");

    // PARTY DROPDOWN CLICK
    if(dropdownMenu) {
        dropdownMenu.addEventListener("click", function(e) {
            const option = e.target.closest(".party-option");
            const addNew = e.target.closest("#addNewPartyBtn");

            if(option) {
                // Party select
                const partyName = option.dataset.name || option.querySelector(".party-name")?.innerText;
                const partyId = option.dataset.id || null;
                const phone = option.dataset.phone || "";
                const billing = option.dataset.billing || "";
                const opening = option.dataset.opening || 0;
                const type = option.dataset.type || "";

                dropdownBtnText.innerText = partyName;
                partyIdInput.value = partyId;
                phoneInput.value = phone;
                billingInput.value = billing;

                // Balance display
                balanceDisplay.innerHTML = type === 'pay' 
                    ? `<span class="text-danger">₹${opening}</span>` 
                    : `<span class="text-success">₹${opening}</span>`;
            }

            if(addNew && addModal) {
                addModal.show();
                document.getElementById("addPartyForm").reset();
            }
        });
    }

    // SAVE PARTY FUNCTION
    function saveParty(closeAfterSave = true) {
        const form = document.getElementById("addPartyForm");
        if(!form) return;

        const data = new FormData(form);

        // Transaction type
        const toReceive = document.getElementById("toReceive")?.checked;
        const toPay = document.getElementById("toPay")?.checked;
        if(toReceive) data.set("transaction_type", "receive");
        else if(toPay) data.set("transaction_type", "pay");

        // Credit limit
        const creditSwitch = document.getElementById("creditLimitSwitch");
        data.set("credit_limit_enabled", creditSwitch?.checked ? 1 : 0);

        fetch("{{ route('parties.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                "Accept": "application/json"
            },
            body: data
        })
        .then(res => res.json())
        .then(res => {
            if(res.success) {
                alert("Party saved successfully!");
                if(closeAfterSave) addModal?.hide();
                else form.reset();
            } else {
                alert(res.message || "Error saving party");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Something went wrong! Check console.");
        });
    }

    // BUTTON LISTENERS
    saveBtn?.addEventListener('click', () => saveParty(true));
    saveNewBtn?.addEventListener('click', () => saveParty(false));
});
</script>

  <script>
document.getElementById("addPaymentRow").addEventListener("click", function () {
    
    const container = document.getElementById("paymentContainer");

    const newRow = document.createElement("div");
    newRow.classList.add("row", "align-items-end", "payment-row", "mb-2");

    newRow.innerHTML = `
        <div class="col-md-5">
            <select class="form-select payment-type">
                <option value="">-- Select Type --</option>
                <option value="cash">Cash</option>
                <option value="remote">Remote</option>
            </select>
        </div>

        <div class="col-md-5">
            <input type="number" class="form-control payment-amount" placeholder="Enter amount">
        </div>

        <div class="col-md-2 d-flex align-items-center">
            <button type="button" class="remove-row border-0 bg-transparent text-secondary" style="font-size:18px;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    `;
  $('#paymentContainer > div:first').append(newRow); // ✅ append inside modal
});

// Remove row
document.addEventListener("click", function (e) {
    if (e.target.closest(".remove-row")) {
        e.target.closest(".payment-row").remove();
    }
});


$(document).ready(function() {

  $('#paymentInForm').on('submit', function(e) {
    e.preventDefault();

    // Collect all payment rows
    const payments = [];
    $('#paymentContainer .payment-row').each(function() {
      const type = $(this).find('.payment-type').val();
      const amount = $(this).find('.payment-amount').val();
      if(type && amount) {
        payments.push({ type, amount });
      }
    });

    // Prepare payload
    const data = {
      party_id: $('.party-id').val(),
      reference_no: $('#referenceNo').val(),
      payments: payments,
      receipt_no: $('input[placeholder="Receipt No"]').val(),
      date: $('input[type="date"]').val(),
      received: $('input[placeholder="Received"]').val(),
      _token: $('meta[name="csrf-token"]').attr('content')
    };

    // AJAX POST
    $.ajax({
      url: '/payments-in', // Laravel route
      method: 'POST',
      data: data,
      success: function(res) {
        alert('Payment saved successfully!');
        // Optionally reset the form
        $('#paymentInForm')[0].reset();
        $('#paymentContainer .payment-row').not(':first').remove();
      },
      error: function(xhr) {
        if(xhr.status === 422) {
          const errors = xhr.responseJSON.errors;
          let msg = '';
          for(const field in errors) {
            msg += errors[field].join(', ') + '\n';
          }
          alert('Validation Error:\n' + msg);
        } else {
          alert('Something went wrong. Please try again.');
        }
      }
    });

  });

});
</script>
 
</body>

</html>
