@extends('layouts.app')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>

/* uper panel styling */
.uper-panel{
  box-shadow: 0 2px 6px rgba(0,0,0,0.15);
  height: 8vh;
  padding: 8px 16px; /* horizontal padding */
  background-color: white;
  display: flex;
  align-items: center; /* vertically center content */
}

.panel-main{
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center; /* vertically center all items */
  width: 100%;
}

.text{
  display: flex;
  align-items: center; /* vertically center h1 + arrow */
  gap: 6px;
}

.text h1{
  font-size: 20px;
  margin: 0; /* remove default margin */
}

.header-dropdown {
  position: relative;
  display: flex;
  align-items: center; /* center h1 + arrow */
  gap: 4px;
  cursor: pointer;
}

.arrow-icon {
  color: #0f6fcc;
  font-size: 16px;
  transition: transform 0.3s;
}
.dropdown-container {
  position: relative;
  width: 200px;
}

.dropdown-input {
  width: 100%;
  padding: 8px;
  border: 1px solid #ccc;
  border-radius: 5px;
  cursor: pointer;
  margin-top: 4px; /* spacing below your label */
}

.dropdown-options {
  position: absolute;
  top: 100%;
  left: 0;
  right: 0;
  background: white;
  border: 1px solid #ccc;
  border-radius: 5px;
  margin-top: 2px;
  display: none;
  z-index: 10;
}

.dropdown-option {
  padding: 8px;
  cursor: pointer;
  position: relative;
}

.dropdown-option:hover {
  background-color: #dbeafe; /* light blue */
  color: black;
}

.dropdown-option.selected::after {
  content: '✓';
  color: #3b82f6; /* light blue tick */
  position: absolute;
  right: 8px;
  top: 50%;
  transform: translateY(-50%);
}
.header-dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  margin-top: 4px;
  background: white;
  border: 1px solid #d1d5db;
  border-radius: 6px;
  padding: 8px 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.1);
  display: none;
  z-index: 999;
  min-width: 120px;
}
 input:focus {
  border-color: #3b82f6 !important; /* light blue border on focus */
  box-shadow: 0 0 4px rgba(96, 165, 250, 0.5); /* subtle blue glow */
  outline: none; /* remove default outline */
}

.header-dropdown-menu label {
  display: flex;
  align-items: center;
  gap: 6px;
  font-size: 14px;
  color: #1f2937;
}

.header-dropdown-menu input[type="checkbox"] {
  accent-color: #0f6fcc;
}

.action-buttons {
  display: flex;
  align-items: center;
  gap: 6px;
}

.btn-add-entity{
  background-color: #dc2626;
  color: white;
  border: none;
  min-width: 160px;
  padding: 0 14px;
  border-radius: 23px;
  height: 44px;
  font-size: 15px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  gap: 6px;
}

.dropdown-item {
  position: relative;
  display: block;
  padding: 6px 12px;
  font-size: 14px;
  color: #1f2937;
  cursor: default;
  width: 30px;
}

.tick-icon {
  position: absolute;
  top: 50%;
  right: 8px;           /* top-right corner */
  transform: translateY(-50%);
  color: #0f6fcc;       /* light blue */
  font-size: 14px;
}

.btn-settings,
.btn-ellipsis {
  background: transparent;
  border: none;
  cursor: pointer;
  color: #9ca3af;
  font-size: 16px;
  padding: 4px;
}

.btn-settings:hover,
.btn-ellipsis:hover {
  color: #374151;
}

.btn-settings {
  margin-left: 12px;
}

/* search box */
.search-box{
  position: relative;
  width: 420px; /* width increase */
}

.search-box i{
  position: absolute;
  left: 15px;
  top: 50%;
  transform: translateY(-50%);
  color: #9ca3af;
  font-size: 14px;
  z-index: 2;
}

.search-input{
  width: 100%;
  height: 42px;
  padding-left: 40px !important; /* IMPORTANT: icon ke liye space */
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  color: #9ca3af;
}

.search-input::placeholder{
  color: #9ca3af !important;
}

.filter-wrapper {
  position: relative; /* zaroori */
  display: inline-flex;
  align-items: center;
  width: 100%;
  justify-content: space-between;
}

.entity-name {
  color: #9ca3af;
}

.filter-icon {
  color: red;
  cursor: pointer;
  font-size: 16px;
}


.btn-icon {
  background: transparent;        /* no full background */
  border: none;                   /* remove full border */
  border-bottom: 2px solid #0f6fcc; /* only bottom outline */
  padding: 6px 8px;
  cursor: pointer;
  color: #0f6fcc;                 /* icon color */
  border-radius: 4px 4px 0 0;     /* optional rounded top corners */
  font-size: 16px;
  transition: background 0.2s, color 0.2s;
}

.btn-icon:hover {
  background: rgba(15,111,204,0.1); /* slight blue background on hover */
  color: #074ea0;                     /* slightly darker icon on hover */
}
.btn-icon-right {
  background: transparent;          /* no full background */
  border: none;                     /* remove all borders first */
  border-right: 2px solid #0f6fcc; /* vertical outline on right side */
  padding: 6px 8px;
  cursor: pointer;
  color: #0f6fcc;                   /* icon color */
  font-size: 16px;
  display: inline-flex;
  align-items: center;              /* vertically center icon */
  justify-content: center;          /* horizontally center icon */
  border-radius: 4px 0 0 4px;       /* optional rounded left corners */
  transition: background 0.2s, color 0.2s;
}

.btn-icon-right:hover {
  background: rgba(15,111,204,0.1); /* subtle hover background */
  color: #074ea0;                   /* darker icon on hover */
}
.filter-dropdown label {
  display: block;
  color: black;
  margin-bottom: 6px;
  font-size: 14px;
}

.show {
  display: block;
}

.filter-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}

.clear-btn {
  background: transparent;
  border: none;
  color: #9ca3af;
  cursor: pointer;
}

.apply-btn {
   background-color: #dc2626;
  color: white;
  border: none;
  padding: 5px 12px;
  border-radius: 6px;
  cursor: pointer;
}

/* Parent row flex */
.entity-row {
  display: flex;
  align-items: center;
  gap: 12px; /* space between left and right */
}
/* left section */
/* SPLIT LAYOUT */
.split-pane{
display:flex;
width:100%;
}

/* LEFT PANEL */
.split-left{
width:240px !important;
flex:0 0 240px;
padding:0;
margin:0;
box-sizing:border-box;
}

/* RIGHT PANEL */
.split-right{
flex:1;
padding:0;
}

/* REMOVE UL DEFAULT SPACE */
.entity-list{
list-style:none;
padding:0;
margin:0;
}

/* LIST ITEMS */
.entity-list li{
display:flex;
align-items:center;
justify-content:space-between;
padding:6px 8px;
}

/* HEADER AREA */
.list-panel-header{
padding:8px;
}

/* FILTER WRAPPER */
.filter-wrapper{
display:flex;
align-items:center;
gap:6px;
padding:0;
margin:0;
}

/* PARTY NAME + ARROWS */
.parent-arrows{
display:flex;
align-items:center;
gap:4px;
}

/* SEPARATOR LINE */
.separator{
width:1px;
height:18px;
background:#e5e7eb;
margin:0 6px;
}

/* AMOUNT SECTION */
.entity-balance{
margin-left:auto;
}

/* Left side contains Party Name + Filter */
.left-side {
  display: flex;
  align-items: center;
  position: relative;
  padding-right: 12px;
  border-right: 1px solid #d1d5db; /* vertical line */
}

/* filter dropdown */
.filter-wrapper{
  display:flex;
  align-items:center;
  gap:6px;
  position:relative;
}

.filter-wrapper span{
  flex-shrink:0;
}

.table-filter-icon{
  font-size:16px;
  color:#6b7280;
  cursor:pointer;
  flex-shrink:0;
  margin-left:4px;
}

.filter-dropdown{
  position:absolute;
  top:28px;
  margin-left: 12px;
  width:200px;
  background:#f3f4f6;
  border:1px solid #d1d5db;
  border-radius:8px;
  box-shadow:0 2px 8px rgba(0,0,0,0.25);
  display:none;
  flex-direction:column;
  z-index:1000;
  padding: 10px;

}

.filter-options{
  overflow-y:auto;
  max-height:160px;
  padding:8px;
  display:flex;
  flex-direction:column;
  gap:4px;


}

.filter-options label{
  display:flex;
  align-items:center;
  gap:6px;
  font-size:10px;
  color:#374151;
  cursor:pointer;
  font-weight: 400;
}

.filter-options::-webkit-scrollbar{
  width:6px;
}
.filter-options::-webkit-scrollbar-thumb{
  background:#9ca3af;
  border-radius:3px;
}
.filter-options::-webkit-scrollbar-track{
  background:#f3f4f6;
}

.filter-actions{
  display:flex;
  justify-content:space-between;
  padding:8px;
  border-top:1px solid #d1d5db;
  background:#f9fafb;
  border-radius:0 0 8px 8px;
}

.clear-btn{
  background:whitesmoke;
  color:#6b7280;
  border:none;
  padding:6px 12px;
  border-radius:16px;
  transition:0.2s;
  font-size: 14px !important;
  font-weight: 600;
}
.clear-btn:hover{
  background:#e5e7eb;
}


.apply-btn:hover{
   background: #8B0000 !important;
}
/* Right side (Amount) */
.right-side {
  padding-left: 32px; /* space after line */
}
.entity-row {
  list-style: none;
  margin-bottom: 12px;
}

.row-content {
  display: flex;
  align-items: center;
}


/* transection table*/
.table-main{
  display:flex;
  justify-content:space-between;
  align-items:center;
  gap:6px;
}
.left-side {
  display: flex;
  align-items: center;
  gap: 8px; /* Party Name + icon spacing */
  position: relative; /* dropdown ke liye relative parent */
}
.txn-table th{
  border-right:1px solid #d1d5db;;
  padding:12px 16px;
  background:#f9fafb;
}
.txn-table th{
  font-size:12px !important;
  text-transform: capitalize !important;
}
.filter-icon {
  color: red;
  cursor: pointer;
  font-size: 16px;
}

.table-main{
display:flex;
align-items:center;
gap:6px;
cursor:pointer;
position:relative;
}
/* table layout equal columns */
.txn-table{
width:100%;
border-collapse:collapse;
table-layout:fixed;
}

.txn-table tbody tr.active-row {
  background-color: rgba(255, 255, 255, 0.08);
}

/* table heads */
.txn-table th{
padding:10px 12px;
text-align:left;
position:relative;
}
.table-header{
display:flex;
justify-content:space-between;
align-items:center !important;
margin-bottom:8px;
}

.table-header h6{
margin:0;
font-weight:600;
font-size:16px;
}

.header-icons{
display:flex;
align-items:center;
gap:12px;
}

.header-icons i{
font-size:16px;
color: #9ca3af;
cursor:pointer;
transition:0.2s;
line-height:1;
}


/* header content */
.table-main{
display:flex;
align-items:center;
gap:6px;
cursor:pointer;
position:relative;
}

/* sort arrows */
.sort-arrows{
display:flex;
flex-direction:column;
position:absolute;
right:28px;   /* filter icon se pehle fixed position */
font-size:9px;
line-height:7px;
opacity:0;
transition:0.2s;
}

/* arrows spacing */
.sort-arrows i{
margin:0;
padding:0;
}

/* show arrows on click */
.table-main.active .sort-arrows{
opacity:1;
}

/* filter icon */
.table-filter-icon{
margin-left:auto;
font-size:12px;
color:#9ca3af;
}

.table-main{
display:flex;
align-items:center;
justify-content:space-between; /* text left, arrows+filter right */
gap:4px;
position:relative;
width:100%;
}
.table-main span{
flex-shrink:0;
}

/* arrows container */
.sort-arrows{
position:absolute;
display:flex;
flex-direction:column;
line-height:1px;
font-size:9px;
opacity:0;       /* hidden but space reserved */
transition:0.2s;
margin-right:4px; /* gap from filter icon */
}
.sort-arrows i{
margin:0;
padding:0;
line-height:3px;
}


/* arrows style */
.sort-arrows i{
color:#6b7280;
}

/* click par show */
.table-main.active .sort-arrows{
opacity:1;
}

/* Dropdown */

.split-pane{
  margin-top: 10px;

}

.filter-dropdown label {
  display: block;
  color: black;
  margin-bottom: 6px;
  font-size: 14px;
}

.show {
  display: block;
}

/* Actions buttons */
.filter-actions {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
}





/* Vertical line */
.separator {
  width: 1px;
  background-color: #d1d5db;
  height: 24px;
  margin: 0 16px;
}
.positive {
  color: #16a34a !important; /* green */
}

.negative {
  color: #dc2626 !important; /* red */
}

.entity-balance.positive {
  color: #9ca3af !important; /* override green */
}
/* Right side */
.right-side {
  display: flex;
  align-items: center;
}
.left-side {
  display: flex;
  align-items: center;
  position: relative; /* dropdown ke liye */
}

.entity-name {
  color: #9ca3af;
  font-size: 14px;

}
/* parent wrapper */
.parent-arrows{
display:flex;
align-items:center;
gap:4px;           /* text aur arrows ke darmiyan gap */
position:relative;
cursor:pointer;
}

/* counter arrows (increment/decrement) */
.counter-arrows{
display:flex;
flex-direction:column;
line-height:6px;      /* arrows close ho */
font-size:9px;
opacity:0;             /* default hidden */
transition:0.2s;
}

/* show arrows only when parent has 'active' class */
.parent-arrows.active .counter-arrows{
opacity:1;
}

/* arrows styling */
.counter-arrows i{
margin:0;
padding:0;
line-height:6px;
color:#6b7280;
}

.counter-arrows {
  display: flex;
  flex-direction: column; /* vertical stacked arrows */
}

.counter-arrows i {
  font-size: 8px;
  color: #9ca3af;
  cursor: pointer;
  margin: 0;
  padding: 0;
}
.table-wrapper{
  position: relative;
}


/* Parent row */
.entity-detail-meta-row {
  display: flex;
  gap: 40px;             /* space between the 3 items */
  align-items: flex-start;
  margin-top: 16px;      /* space from any content above */
}

/* Each meta block */
.entity-detail-meta {
  display: flex;
  flex-direction: column;
}

/* Heading */
.meta-heading {
  color: #9ca3af;        /* gray color */
  font-weight: 400;
  font-size: 12px;
  margin-bottom: 4px;    /* spacing between heading and value */
}

/* Value */
.meta-value {
  font-size: 14px;
  color: #111827;        /* darker text for value */
  display: flex;
  align-items: center;
  gap: 6px;              /* space between icon and text */
}
/* Optional: responsive slight gap on smaller screens */
@media (max-width: 768px) {
  .counter-arrows {
    margin-left: 4px; /* small space on mobile */
  }

}

@media (max-width:768px){

.txn-table th{
padding:10px 6px;
font-size:13px;
}

.table-main{
gap:4px;
}

.sort-arrows{
right:16px;
font-size:8px;
}

.table-filter-icon{
font-size:12px;
}

}

/* Actions dropdown in table */
.action-dropdown {
  position: relative;
  display: inline-block;
}

.action-toggle {
  background: transparent;
  border: none;
  cursor: pointer;
  padding: 4px;
  color: #6b7280;
  font-size: 16px;
}

.action-toggle:hover {
  color: #111827;
}

.action-menu {
  position: absolute;
  top: 100%;
  right: 0;
  background: white;
  border: 1px solid #e5e7eb;
  border-radius: 6px;
  box-shadow: 0 8px 16px rgba(0,0,0,0.08);
  min-width: 140px;
  display: none;
  z-index: 2000;
  padding: 4px 0;
}

.action-item {
  width: 100%;
  text-align: left;
  background: transparent;
  border: none;
  padding: 8px 12px;
  cursor: pointer;
  font-size: 13px;
  color: #1f2937;
}

.action-item:hover {
  background: #f3f4f6;
}

</style>
  <script>
    function toggleFilter(){
let dropdown = document.getElementById("filterDropdown");

if(dropdown.style.display === "block"){
dropdown.style.display = "none";
}else{
dropdown.style.display = "block";
}
}

document.querySelector(".filter-icon").onclick = function(){
document.querySelector(".filter-dropdown").classList.toggle("show");
}

function toggleHeaderDropdown(element) {
  const dropdownMenu = element.nextElementSibling;
  const isVisible = dropdownMenu.style.display === 'block';
  dropdownMenu.style.display = isVisible ? 'none' : 'block';

  // Optional: rotate arrow
  element.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
}

function toggleSort(el){
el.classList.toggle("active");
}

function toggleParentArrows(el){
  el.classList.toggle('active');
}

// Toggle dropdown on icon click
function toggleFilterDropdown(icon){
  const dropdown = icon.nextElementSibling;
  dropdown.style.display = dropdown.style.display === 'flex' ? 'none' : 'flex';
}

// Close dropdown if clicked outside
document.addEventListener('click', function(e){
  document.querySelectorAll('.filter-dropdown').forEach(dd=>{
    if(!dd.contains(e.target) && !dd.previousElementSibling.contains(e.target)){
      dd.style.display = 'none';
    }
  });
});

// Clear button functionality
document.querySelectorAll('.clear-btn').forEach(btn=>{
  btn.addEventListener('click', function(){
    const checkboxes = this.closest('.filter-dropdown').querySelectorAll('input[type="checkbox"]');
    checkboxes.forEach(cb=>cb.checked=false);
  });
});


  </script>

@section('title', 'Vyapar — Bank Account')
@section('description', 'Manage your business parties, customers, and suppliers in Vyapar accounting software.')
@section('page', 'bank-accounts')

@section('content')

<!-- uper panel -->
<div class="uper-panel">
  <div class="panel-main">

    <!-- Left: Header + Arrow -->
    <div class="text">
      <div class="header-dropdown">
        <h1>Bank Account</h1>
        <i class="fa fa-chevron-down arrow-icon" onclick="toggleHeaderDropdown(this)"></i>

      <div class="header-dropdown-menu">
  <label class="dropdown-item">
    Bank Account
    <i class="fa fa-check tick-icon"></i>
  </label>
</div>
      </div>
    </div>

    <!-- Right: Buttons -->
    <div class="action-buttons">
      <button class="btn-add-entity" data-bs-toggle="modal" data-bs-target="#addBankModal">
        <i class="fa-solid fa-plus me-1"></i> Add Bank Account
      </button>

      <button class="btn-settings" title="Settings">
        <i class="fa-solid fa-gear"></i>
      </button>

      <button class="btn-ellipsis" title="More Options">
        <i class="fa-solid fa-ellipsis-vertical"></i>
      </button>
    </div>

  </div>
</div>

@if(session('success'))
  <div class="alert alert-success mt-3" role="alert" id="bankFlash">
    {{ session('success') }}
  </div>
@endif

@if($errors->any())
  <div class="alert alert-danger mt-3" role="alert">
    <ul class="mb-0">
      @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
@endif

  <div class="split-pane">


    <!-- Left: Bank Account List -->
    <div class="split-left">
      <div class="list-panel-header">
      <div class="search-box">
  <i class="fa fa-search"></i>
  <input type="text" class="form-control search-input" placeholder="Search Bank Account" id="bankSearchInput">
</div>

      </div>
      <ul class="entity-list" id="bankList">
        @forelse($bankAccounts as $bank)
        <li class="{{ $loop->first ? 'active' : '' }}" data-bank="{{ $bank->id }}"
            data-account-number="{{ $bank->account_number }}"
            data-bank-name="{{ $bank->bank_name }}"
            data-opening-balance="{{ $bank->opening_balance }}"
            data-as-of-date="{{ optional($bank->as_of_date)->format('d/m/Y') }}"
            data-swift="{{ $bank->swift_code }}"
            data-iban="{{ $bank->iban }}"
            data-account-holder="{{ $bank->account_holder_name }}"
            data-print-on-invoice="{{ $bank->print_on_invoice ? '1' : '0' }}"
        >
          <span class="entity-name">{{ $bank->display_name }}</span>
          <span class="entity-balance {{ (float) $bank->opening_balance < 0 ? 'negative' : 'positive' }}">₹ {{ number_format($bank->opening_balance, 2) }}</span>
        </li>
        @empty
        <li class="text-center text-muted py-5">
          No bank accounts found. Click <strong>Add Bank Account</strong> to get started.
        </li>
        @endforelse
      </ul>
    </div>
    <!-- Right: Bank Details -->
    <div class="split-right">
      <div class="detail-panel-header">
        <div>
          <div class="entity-detail-name" id="bankDetailName" style="font-weight: 400;">
            {{ optional($bankAccounts->first())->display_name ?? 'Select a bank account' }}
            <button class="btn-icon" title="Edit">
              <i class="fa-solid fa-pen"></i>
            </button>
          </div>
         <div class="entity-detail-meta-row">

  <div class="entity-detail-meta">
    <div class="meta-heading">Account Number</div>
    <div class="meta-value" id="bankDetailAccountNumber">{{ optional($bankAccounts->first())->account_number ?? '-' }}</div>
  </div>

  <div class="entity-detail-meta">
    <div class="meta-heading">Bank Name</div>
    <div class="meta-value" id="bankDetailBankName">{{ optional($bankAccounts->first())->bank_name ?? '-' }}</div>
  </div>

  <div class="entity-detail-meta">
    <div class="meta-heading"> Balance</div>
    <div class="meta-value" id="bankDetailOpeningBalance">₹ {{ number_format(optional($bankAccounts->first())->opening_balance ?? 0, 2) }}</div>
  </div>

</div>
        </div>
        <div class="action-buttons">

        </div>
      </div>
    <div class="detail-panel-body">
  <div class="table-header">
    <h6 class="fw-600 mb-3" style="font-size: 14px !important;">Bank Transactions</h6>
    <div class="header-icons">
      <div class="table-search-box">
        <input type="text" class="form-control form-control-sm" id="tableSearchInput" placeholder="Search table">
      </div>
      <button type="button" class="btn btn-sm btn-light" id="focusSearchBtn" title="Search">
        <i class="fa fa-search"></i>
      </button>
      <button type="button" class="btn btn-sm btn-light" id="exportExcelBtn" title="Export to Excel">
        <i class="fa fa-file-excel"></i>
      </button>
      <button type="button" class="btn btn-sm btn-light" id="printTableBtn" title="Print">
        <i class="fa fa-print"></i>
      </button>
    </div>
  </div>


        <table class="txn-table" id="bankTable">
          <thead>
            <tr>
              <th>Type</th>
              <th>Invoice No</th>
              <th>Party</th>
              <th>Bank Name</th>
              <th>Payment Type</th>
              <th>Date</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($bankTransactions ?? collect()) as $transaction)
              @php
                $typeLabel = match($transaction->sale?->type) {
                    'invoice' => 'Sale Invoice',
                    'estimate' => 'Estimate',
                    'sale_order' => 'Sale Order',
                    'proforma' => 'Proforma',
                    'delivery_challan' => 'Delivery Challan',
                    'sale_return' => 'Sale Return',
                    'pos' => 'POS',
                    default => ucfirst(str_replace('_', ' ', $transaction->sale?->type ?? 'Unknown')),
                };
              @endphp
              <tr data-bank-id="{{ $transaction->bank_account_id }}">
                <td>{{ $typeLabel }}</td>
                <td>{{ $transaction->sale?->bill_number ?? '-' }}</td>
                <td>{{ $transaction->sale?->display_party_name ?? '-' }}</td>
                <td>{{ $transaction->bankAccount?->display_name ?? $transaction->bankAccount?->bank_name ?? '-' }}</td>
                <td>{{ $transaction->payment_type ?? '-' }}</td>
                <td>{{ optional($transaction->created_at)->format('d/m/Y h:i A') }}</td>
                <td class="positive">₹ {{ number_format($transaction->amount, 2) }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center py-5 text-muted">No payment transactions found yet.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

@endsection

@section('modals')
<!-- MODAL: ADD BANK ACCOUNT -->
<div class="modal fade" id="addBankModal" tabindex="-1" aria-labelledby="addBankModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <form method="POST" action="{{ route('bank-accounts.store') }}" id="bankForm">
        @csrf
        <input type="hidden" name="_method" value="POST" id="bankFormMethod">
        <input type="hidden" name="bank_id" id="bankIdField">
        <div class="modal-header">
          <h5 class="modal-title" id="addBankModalLabel"><i class="fa-solid fa-university me-2"></i>Add Bank Account</h5>
          <div class="d-flex align-items-center gap-2">
            <button class="btn btn-sm btn-outline-secondary" title="Settings"><i class="fa-solid fa-gear"></i></button>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
        </div>
        <div class="modal-body">
          <div class="row g-3 mb-4">
            <div class="col-md-6">
              <label class="form-label fw-600">Account Display Name <span class="text-danger">*</span></label>
              <input type="text" name="display_name" class="form-control" placeholder="Enter Account Display Name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Opening Balance</label>
              <div class="input-group">
                <span class="input-group-text">₹</span>
                <input type="number" step="0.01" name="opening_balance" class="form-control" placeholder="Enter Opening Balance">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">As of Date</label>
              <input type="date" name="as_of_date" class="form-control" value="{{ now()->format('Y-m-d') }}">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Account Number</label>
              <input type="text" name="account_number" class="form-control" placeholder="Enter Account Number">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">SWIFT Code</label>
              <input type="text" name="swift_code" class="form-control" placeholder="Enter SWIFT">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">IBAN</label>
              <input type="text" name="iban" class="form-control" placeholder="Enter IBAN">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Bank Name</label>
              <input type="text" name="bank_name" class="form-control" placeholder="Enter Bank Name">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-600">Account Holder Name</label>
              <input type="text" name="account_holder_name" class="form-control" placeholder="Enter Account Holder Name">
            </div>
          </div>

          <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" id="printOnInvoice" name="print_on_invoice" value="1">
            <label class="form-check-label" for="printOnInvoice">Print Bank Details on Invoices</label>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary" id="bankFormSubmit">Save Details</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/bank.js') }}"></script>
<script>
  document.querySelectorAll('.dropdown-container').forEach(container => {
    const input = container.querySelector('.dropdown-input');
    const optionsContainer = container.querySelector('.dropdown-options');
    const options = container.querySelectorAll('.dropdown-option');

    input.addEventListener('click', (e) => {
      e.stopPropagation();
      optionsContainer.style.display =
        optionsContainer.style.display === 'block' ? 'none' : 'block';
    });

    options.forEach(option => {
      option.addEventListener('click', () => {
        options.forEach(opt => opt.classList.remove('selected'));
        option.classList.add('selected');
        input.value = option.textContent;
        optionsContainer.style.display = 'none';
      });
    });
  });

  document.addEventListener('click', () => {
    document.querySelectorAll('.dropdown-options').forEach(drop => {
      drop.style.display = 'none';
    });
  });
</script>
@endpush
