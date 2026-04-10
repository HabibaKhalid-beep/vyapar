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
        <form id="paymentInForm" action="{{ route('payments-in.store') }}" method="POST">
           @csrf
          <input type="hidden" id="paymentInId" value="">

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

                <input type="hidden" class="party-id" name="party_id">
                <input type="hidden" class="phone-input" name="phone-inpur">
                <input type="hidden" class="billing-address" name="billing_address">
                <div id="partyBalanceDisplay" class="mt-1"></div>
              </div>

              <!-- Payment Section -->
              <div id="paymentContainer">

                <div style="width: auto; height: auto; padding: 22px; border-radius: 1px; border: 1px solid #ced4da; box-shadow: 0 2px 6px rgba(0,0,0,0.12);">

          <div class="row align-items-end payment-row mb-2">

  <div class="col-md-4">
    <label class="form-label">Bank Account</label>
    <input type="hidden" class="payment-type" value="bank">
    <select class="form-select payment-bank" name="bank_account_id">
    <option value="">-- Select Bank --</option>
    @foreach($bankAccounts as $bank)
       @php
         $accountNumber = preg_replace('/\s+/', '', (string) ($bank->account_number ?? ''));
         $bankLabel = trim($bank->display_name . ($accountNumber !== '' ? ' - ' . $accountNumber : ''));
       @endphp
       <option value="{{ $bank->id }}">{{ $bankLabel }}</option>
    @endforeach
</select>
  </div>

  <div class="col-md-3">
    <label class="form-label">Amount</label>
    <input type="number" class="form-control payment-amount" placeholder="Enter amount">
  </div>

  <div class="col-md-3">
    <label class="form-label">Reference No</label>
    <input type="text" class="form-control payment-reference" placeholder="Enter reference number">
  </div>

  <div class="col-md-2 d-flex align-items-center">
    <button type="button" class="remove-row border-0 bg-transparent text-secondary" style="display:none; font-size:18px;">
      <i class="fa-solid fa-trash"></i>
    </button>
  </div>

</div>
<!-- Add Payment Button -->
<div class="mt-2">
  <button type="button" id="addPaymentRow" class="btn p-0 text-primary border-0 bg-transparent">
    + Add Payment
  </button>
</div>
                  <input type="hidden" id="referenceNo" value="">



                </div>

              </div>

              <!-- Extra Buttons -->
              <div class="d-flex flex-column gap-2 mt-3 align-items-start">
                <button type="button" id="toggleDescriptionBtn" class="btn d-flex align-items-center gap-2 border w-auto"
                        style="border-color:#ced4da; background-color:#fff; padding: 6px 12px;">
                  <i class="fa-solid fa-file text-secondary" style="font-size:20px;"></i>
                  <span class="text-secondary">Add Description</span>
                </button>

                <div id="descriptionBox" class="d-none mt-2 w-100">
                  <label class="form-label text-secondary">Description</label>
                  <textarea class="form-control" id="paymentDescription" name="description" rows="4" placeholder="Enter description"></textarea>
                </div>

                <button type="button" id="toggleImageBtn" class="btn d-flex align-items-center gap-2 border w-auto"
                        style="border-color:#ced4da; background-color:#fff; padding: 6px 12px;">
                  <i class="fa-solid fa-camera text-secondary" style="font-size:20px;"></i>
                  <span class="text-secondary">Upload Image</span>
                </button>

                <input type="file" id="paymentImageInput" class="d-none" accept="image/*">
                <div id="imageUploadBox" class="d-none mt-2 w-100">
                  <div class="border rounded p-3 text-center text-secondary" id="imagePlaceholder" style="cursor:pointer;">
                    Click to select an image
                  </div>
                  <div id="imageSelectedName" class="small text-muted mt-2 d-none"></div>
                </div>
              </div>

            </div> <!-- End Left Side -->

            <!-- Right Side: Receipt No + Date + Received -->
            <div class="col-lg-5">

              <div class="mb-3">
                <label class="form-label text-secondary">Receipt No</label>
              <input type="text" class="form-control" id="receiptNo" name="receipt_no" placeholder="Receipt No">
              </div>

              <div class="mb-3">
                <label class="form-label text-secondary">Date</label>
                <input type="date" class="form-control" name="date">
              </div>

              <div class="mb-3">
                <label class="form-label text-secondary">Received</label>
                <input type="text" class="form-control" id="receivedAmount" placeholder="Received" readonly>
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
            <p class="ps-3 h4">Rs {{ number_format($paymentIns->sum('amount'), 2) }}</p>
          </div>
          <div class="w-50 mt-2 d-flex align-items-end justify-content-center flex-column">
            <div class="col-5 h-50 rounded-pill d-flex justify-content-center align-item-center me-4"
              style="background-color: #DEF7EE;">
              <p class="text-success pt-1">{{ $paymentIns->count() > 0 ? $paymentIns->count() : 0 }} <i class="bi bi-arrow-up-right "></i></>
              </p>
            </div>
            <span class="me-4 pe-1 mt-1 text-secondary" style="font-size: 10px;">vs last month</span>
          </div>
        </div>
        <div class="w-100 d-flex mt-3">
          <p class="ps-3 pe-3 text-secondary">Received : <span class="fw-bold text-dark">Rs {{ number_format($paymentIns->sum('amount'), 2) }}</span></p>


        </div>
      </div>
    </div>

    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="col-12 g-2 mb-3 d-flex flex-wrap justify-content-between">
          <p class="fw-bold">Transactions</p>

          <div class="d-flex align-items-center">
            <div class="search-container">
              <input type="text" class="search-input" id="paymentInSearch" placeholder="Search...">
              <span class="search-btn">
                <i class="fa fa-search"></i>
              </span>
            </div>
            <div class="mt-1 pt-1 ms-3 d-flex align-items-center">
              <button type="button" id="exportPaymentInExcel" class="btn p-0 mx-3 fs-4 text-secondary border-0 bg-transparent" title="Export Excel">
                <i class="fas fa-file-excel"></i>
              </button>
              <button type="button" id="printPaymentInTable" class="btn p-0 mx-3 fs-4 text-secondary border-0 bg-transparent" title="Print Table">
                <i class="fas fa-print"></i>
              </button>
            </div>

          </div>

        </div>

        <div class="table-responsive" id="paymentInTableWrap">
          <table class="table table-hover mb-0 align-middle" id="paymentInTable">
            <thead class="table-light">
              <tr>
                <th style="width: 12%;">
                  <div class="d-flex align-items-center justify-content-between">
                    <span>Date</span>
                    <div class="dropdown">
                      <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-filter"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="dropdown-item">
                          <input type="date" class="form-control form-control-sm" style="outline:none;">
                        </li>
                      </ul>
                    </div>
                  </div>
                </th>
                <th style="width: 14%;">
                  <div class="d-flex align-items-center justify-content-between">
                    <span>Reference No.</span>
                    <div class="dropdown">
                      <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-filter"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="dropdown-item">
                          <input type="text" class="form-control form-control-sm" placeholder="Search...">
                        </li>
                      </ul>
                    </div>
                  </div>
                </th>
                <th style="width: 18%;">
                  <div class="d-flex align-items-center justify-content-between">
                    <span>Party Name</span>
                    <div class="dropdown">
                      <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-filter"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li class="dropdown-item">
                          <input type="text" class="form-control form-control-sm" placeholder="Search...">
                        </li>
                      </ul>
                    </div>
                  </div>
                </th>
                <th style="width: 14%;">
                  <div class="d-flex align-items-center justify-content-between">
                    <span>Total Amount</span>
                    <div class="dropdown">
                      <button class="btn btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-filter"></i>
                      </button>
                    </div>
                  </div>
                </th>
                <th style="width: 14%;">
                  <div class="d-flex align-items-center justify-content-between">
                    <span>Bank Account</span>
                  </div>
                </th>
                <th style="width: 14%;">
                  <div class="d-flex align-items-center justify-content-between">
                    <span>Payment Type</span>
                  </div>
                </th>
                <th style="width: 14%; text-align: center;">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($paymentIns as $paymentIn)
                <tr class="payment-in-row">
                  <td>{{ $paymentIn->date ? \Carbon\Carbon::parse($paymentIn->date)->format('d-m-Y') : '-' }}</td>
                  <td><span class="badge bg-light text-dark">{{ $paymentIn->reference_no ?: '-' }}</span></td>
                  <td><strong>{{ $paymentIn->party?->name ?: '-' }}</strong></td>
                  <td><span class="text-success fw-bold">Rs {{ number_format((float) $paymentIn->amount, 2) }}</span></td>
                  <td><small>{{ $paymentIn->bankAccount?->display_name ?: '-' }}</small></td>
                  <td><span class="badge bg-info text-white">{{ ucfirst($paymentIn->payment_type) }}</span></td>
                  <td style="text-align: center;">
                    <div class="dropdown">
                      <button class="btn btn-sm btn-light px-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="More Actions">
                        <i class="fa-solid fa-ellipsis-vertical"></i>
                      </button>
                      <ul class="dropdown-menu dropdown-menu-end">
                        <!-- <li><a class="dropdown-item" href="{{ route('invoice', ['payment_in' => $paymentIn->id]) }}"><i class="fa-solid fa-eye me-2"></i>Open</a></li> -->
                        <li><a class="dropdown-item" href="{{ route('payments-in.edit', $paymentIn) }}"><i class="fa-solid fa-pen-to-square me-2"></i>Edit</a></li>
                        <li><a class="dropdown-item" href="{{ route('invoice.payment-in', ['payment_in' => $paymentIn->id]) }}" target="_blank"><i class="fa-solid fa-file-pdf me-2"></i>Open PDF</a></li>
                        <li><a class="dropdown-item" href="{{ route('invoice.payment-in', ['payment_in' => $paymentIn->id]) }}" target="_blank"><i class="fa-solid fa-print me-2"></i>Print</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="#" onclick="viewPaymentHistory({{ $paymentIn->id }})"><i class="fa-solid fa-history me-2"></i>View History</a></li>
                        <li>
                          <form action="{{ route('payments-in.destroy', $paymentIn) }}" method="POST" onsubmit="return confirm('Delete this payment in record?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger"><i class="fa-solid fa-trash me-2"></i>Delete</button>
                          </form>
                        </li>
                      </ul>
                    </div>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="7" class="text-center text-muted py-4">
                    <i class="fa-solid fa-inbox fa-2x mb-3 d-block opacity-50"></i>
                    <strong>No payment in records yet.</strong> Click "Add Payment-in" to create one.
                  </td>
                </tr>
              @endforelse
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

      $("#paymentInSearch").on("input", function () {
        const query = $(this).val().toLowerCase().trim();

        $("#paymentInTable tbody tr.payment-in-row").each(function () {
          const rowText = $(this).text().toLowerCase().replace(/\s+/g, " ").trim();
          $(this).toggle(rowText.includes(query));
        });
      });

      $("#exportPaymentInExcel").on("click", function () {
        let excelHtml = `
          <table border="1">
            <tr>
              <th>Date</th>
              <th>Reference No.</th>
              <th>Party Name</th>
              <th>Total Amount</th>
              <th>Bank Account</th>
              <th>Payment Type</th>
            </tr>
        `;

        $("#paymentInTable tbody tr.payment-in-row:visible").each(function () {
          const cells = $(this).find("td");
          excelHtml += "<tr>";
          for (let i = 0; i < 6; i++) {
            const cellText = $(cells[i]).text().replace(/\s+/g, " ").trim();
            excelHtml += `<td>${cellText}</td>`;
          }
          excelHtml += "</tr>";
        });

        excelHtml += "</table>";

        const blob = new Blob([`\ufeff${excelHtml}`], { type: "application/vnd.ms-excel;charset=utf-8;" });
        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);
        const now = new Date();
        const filename = `payment-in-${now.getFullYear()}-${String(now.getMonth() + 1).padStart(2, "0")}-${String(now.getDate()).padStart(2, "0")}.xls`;

        link.setAttribute("href", url);
        link.setAttribute("download", filename);
        link.style.visibility = "hidden";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
      });

      $("#printPaymentInTable").on("click", function () {
        const tableHtml = document.getElementById("paymentInTableWrap")?.innerHTML || "";
        const printWindow = window.open("", "_blank", "width=1200,height=800");
        if (!printWindow) {
          alert("Please allow popups to print the table.");
          return;
        }

        printWindow.document.write(`
          <html>
            <head>
              <title>Payment In Transactions</title>
              <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
              <style>
                body { font-family: Arial, sans-serif; padding: 24px; }
                h2 { margin-bottom: 16px; }
                table { width: 100%; border-collapse: collapse; }
                th, td { border: 1px solid #dee2e6; padding: 10px; text-align: left; }
                th:last-child, td:last-child { display: none; }
                .dropdown, .btn, form { display: none !important; }
              </style>
            </head>
            <body>
              <h2>Payment In Transactions</h2>
              ${tableHtml}
            </body>
          </html>
        `);

        printWindow.document.close();
        printWindow.focus();
        setTimeout(() => {
          printWindow.print();
        }, 400);
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
        <div class="col-md-4">
            <label class="form-label">Bank Account</label>
            <input type="hidden" class="payment-type" value="bank">
            <select class="form-select payment-bank" name="bank_account_id">
                <option value="">-- Select Bank --</option>
                @foreach($bankAccounts as $bank)
                    @php
                        $accountNumber = preg_replace('/\s+/', '', (string) ($bank->account_number ?? ''));
                        $bankLabel = trim($bank->display_name . ($accountNumber !== '' ? ' - ' . $accountNumber : ''));
                    @endphp
                    <option value="{{ $bank->id }}">{{ $bankLabel }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Amount</label>
            <input type="number" class="form-control payment-amount" placeholder="Enter amount">
        </div>

        <div class="col-md-3">
            <label class="form-label">Reference No</label>
            <input type="text" class="form-control payment-reference" placeholder="Enter reference number">
        </div>

        <div class="col-md-2 d-flex align-items-center">
            <button type="button" class="remove-row border-0 bg-transparent text-secondary" style="font-size:18px;">
                <i class="fa-solid fa-trash"></i>
            </button>
        </div>
    `;
  $('#paymentContainer > div:first').append(newRow);
  updateReceivedTotal();
});

// Remove row
document.addEventListener("click", function (e) {
    if (e.target.closest(".remove-row")) {
        e.target.closest(".payment-row").remove();
        updateReceivedTotal();
    }
});

function updateReceivedTotal() {
    let total = 0;
    document.querySelectorAll('.payment-amount').forEach(input => {
        total += parseFloat(input.value || 0) || 0;
    });

    const receivedInput = document.getElementById('receivedAmount');
    if (receivedInput) {
        receivedInput.value = total.toFixed(2).replace(/\.00$/, '');
    }
}

document.addEventListener('input', function(e) {
    if (e.target.classList.contains('payment-amount')) {
        updateReceivedTotal();
    }
});

document.getElementById('toggleDescriptionBtn')?.addEventListener('click', function() {
    document.getElementById('descriptionBox')?.classList.toggle('d-none');
});

document.getElementById('toggleImageBtn')?.addEventListener('click', function() {
    const box = document.getElementById('imageUploadBox');
    box?.classList.remove('d-none');
    document.getElementById('paymentImageInput')?.click();
});

document.getElementById('imagePlaceholder')?.addEventListener('click', function() {
    document.getElementById('paymentImageInput')?.click();
});

document.getElementById('paymentImageInput')?.addEventListener('change', function(e) {
    const file = e.target.files?.[0];
    if (!file) return;
    const selectedName = document.getElementById('imageSelectedName');
    if (selectedName) {
        selectedName.textContent = `Selected: ${file.name}`;
        selectedName.classList.remove('d-none');
    }
});

const editPaymentIn = @json($editPaymentIn ?? null);

function populateEditPaymentIn(paymentIn) {
    if (!paymentIn) return;

    const modalElement = document.getElementById('addPaymentInModal');
    const modal = modalElement ? new bootstrap.Modal(modalElement) : null;
    const firstRow = $('#paymentContainer .payment-row').first();

    $('#paymentInId').val(paymentIn.id || '');
    $('.party-id').val(paymentIn.party_id || '');
    $('#partyDropdownBtnText').text(paymentIn.party?.name || 'Select Party');
    $('#receiptNo').val(paymentIn.receipt_no || '');
    $('input[name="date"]').val(paymentIn.date || '');
    $('#paymentDescription').val(paymentIn.description || '');
    $('#referenceNo').val(paymentIn.reference_no || '');

    $('#paymentContainer .payment-row').not(':first').remove();
    firstRow.find('.payment-bank').val(paymentIn.bank_account_id || '');
    firstRow.find('.payment-amount').val(paymentIn.amount || '');
    firstRow.find('.payment-reference').val(paymentIn.reference_no || '');
    firstRow.find('.payment-type').val(paymentIn.payment_type || 'bank');

    updateReceivedTotal();
    modal?.show();
}

if (editPaymentIn) {
    populateEditPaymentIn(editPaymentIn);
}


$('#paymentInForm').on('submit', function(e) {
    e.preventDefault();

    const payments = [];
    $('#paymentContainer .payment-row').each(function() {
        const type = $(this).find('.payment-type').val();
        const amount = $(this).find('.payment-amount').val();
        const bank_account_id = $(this).find('.payment-bank').val();
        const reference = $(this).find('.payment-reference').val();

        if(type && amount) {
            payments.push({ type, amount, bank_account_id, reference });
        }
    });

    $('#referenceNo').val($('.payment-reference').first().val() || '');
    updateReceivedTotal();

    const paymentInId = $('#paymentInId').val();
    const requestUrl = paymentInId ? `/dashboard/payments-in/${paymentInId}` : '/dashboard/payments-in';
    const spoofMethod = paymentInId ? 'PUT' : 'POST';

    $.ajax({
        url: requestUrl,
        method: 'POST',
        contentType: 'application/json',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
            'Accept': 'application/json'
        },
        data: JSON.stringify({
            party_id: $('.party-id').val(),
            reference_no: $('#referenceNo').val(),
            payments: payments,
            receipt_no: $('#receiptNo').val(),
            date: $('input[name="date"]').val(),
            received: $('#receivedAmount').val(),
            description: $('#paymentDescription').val(),
            _method: spoofMethod,
            _token: $('meta[name="csrf-token"]').attr('content')
        }),
        success: function(res) {
            if (res.redirect_url) {
                window.location.href = res.redirect_url;
                return;
            }

            window.location.reload();
        },
        error: function(xhr) {
            console.log(xhr.responseJSON);
            if(xhr.status === 422) {
                const errors = xhr.responseJSON.errors;
                let msg = '';
                for(const field in errors) {
                    msg += field + ': ' + errors[field].join(', ') + '\n';
                }
                alert('Validation Error:\n' + msg);
            } else {
                const serverMessage = xhr.responseJSON?.message || xhr.responseText || 'Something went wrong. Please try again.';
                alert(serverMessage);
            }
        }
    });
});

// View Payment History Function - Display in Table Format
function viewPaymentHistory(paymentInId) {
    const historyUrl = `/dashboard/payments-in/${paymentInId}/history`;

    $.ajax({
        url: historyUrl,
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
        },
        success: function(res) {
            if(res.success && res.history && res.history.length > 0) {
                // Remove old modal if exists
                $('#paymentHistoryModal').remove();

                let historyHtml = `
                    <div class="modal fade" id="paymentHistoryModal" tabindex="-1">
                        <div class="modal-dialog modal-xl">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">
                                        <i class="fa-solid fa-history me-2"></i>Payment History (${res.total_records} Records)
                                    </h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                `;

                // Add payment details summary
                if(res.payment_details) {
                    historyHtml += `
                        <div class="alert alert-info mb-3">
                            <h6 class="mb-2"><strong>📋 Payment Details Summary:</strong></h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <small><strong>Receipt No:</strong> ${res.payment_details.receipt_no || '-'}</small><br>
                                    <small><strong>Reference No:</strong> ${res.payment_details.reference_no || '-'}</small><br>
                                    <small><strong>Amount:</strong> <span class="text-success fw-bold">₹${res.payment_details.amount}</span></small>
                                </div>
                                <div class="col-md-6">
                                    <small><strong>Payment Type:</strong> <span class="badge bg-info">${res.payment_details.payment_type || '-'}</span></small><br>
                                    <small><strong>Date:</strong> ${res.payment_details.date || '-'}</small>
                                </div>
                            </div>
                        </div>
                    `;
                }

                // Add table format history
                historyHtml += `
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 12%;">Date & Time</th>
                                    <th style="width: 18%;">Action</th>
                                    <th style="width: 12%;">Amount</th>
                                    <th style="width: 14%;">Reference</th>
                                    <th style="width: 14%;">Receipt</th>
                                    <th style="width: 12%;">Type</th>
                                    <th style="width: 18%;">User</th>
                                </tr>
                            </thead>
                            <tbody>
                `;

                res.history.forEach((entry, index) => {
                    const timestamp = entry.created_at || entry.updated_at || '-';
                    const user = entry.user_name || 'System';
                    const action = entry.action || 'Action Recorded';
                    const amount = entry.amount ? `₹${parseFloat(entry.amount).toFixed(2)}` : '-';
                    const reference = entry.reference || '-';
                    const receipt = entry.receipt || '-';
                    const paymentType = entry.payment_type ? `<span class="badge bg-info text-white text-uppercase" style="font-size: 0.7rem;">${entry.payment_type.substring(0, 3)}</span>` : '-';

                    historyHtml += `
                        <tr>
                            <td>
                                <small class="text-muted">${timestamp}</small>
                            </td>
                            <td>
                                <strong>${action}</strong>
                                ${entry.description ? `<br><small class="text-muted">${entry.description}</small>` : ''}
                            </td>
                            <td>
                                <span class="text-success fw-bold">${amount}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">${reference}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark">${receipt}</span>
                            </td>
                            <td>${paymentType}</td>
                            <td>
                                <small><i class="fa-solid fa-user me-1 text-secondary"></i>${user}</small>
                            </td>
                        </tr>
                    `;
                });

                historyHtml += `
                            </tbody>
                        </table>
                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                        <i class="fa-solid fa-xmark me-1"></i>Close
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                $('body').append(historyHtml);

                const modal = new bootstrap.Modal(document.getElementById('paymentHistoryModal'));
                modal.show();
            } else {
                alert('No history found for this payment.');
            }
        },
        error: function(xhr) {
            alert('Could not load history. Please try again.');
            console.error(xhr);
        }
    });
}

</script>


</body>

</html>
