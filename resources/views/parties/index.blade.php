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
  width: 120px;
  border-radius: 16px;
  height: 5vh;
  font-size: 15px;
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
  
@section('title', 'Vyapar — Parties')
@section('description', 'Manage your business parties, customers, and suppliers in Vyapar accounting software.')
@section('page', 'parties')

@section('content')

<!-- uper panel -->
<div class="uper-panel">
  <div class="panel-main">
    
    <!-- Left: Header + Arrow -->
    <div class="text">
      <div class="header-dropdown">
        <h1>Parties</h1>
        <i class="fa fa-chevron-down arrow-icon" onclick="toggleHeaderDropdown(this)"></i>

      <div class="header-dropdown-menu">
  <label class="dropdown-item">
    Parties
    <i class="fa fa-check tick-icon"></i>
  </label>
</div>
      </div>
    </div>

    <!-- Right: Buttons -->
    <div class="action-buttons">
      <button class="btn-add-entity" data-bs-toggle="modal" data-bs-target="#addPartyModal">
        <i class="fa-solid fa-plus me-1"></i> Add Party
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

  <div class="split-pane">


    <!-- Left: Party List -->
    <div class="split-left">
      <div class="list-panel-header">
      <div class="search-box">
  <i class="fa fa-search"></i>
  <input type="text" class="form-control search-input" placeholder="Search Party Name" id="partySearchInput">
</div>
       
      </div>
      <ul class="entity-list" id="partyList">
        <li class="active" data-party="abc">
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<div class="filter-wrapper">
<div class="parent-arrows" onclick="this.classList.toggle('active')">
  <span class="entity-balance positive" style="color: gray !important;">Party Name</span>
  <div class="counter-arrows">
    <i class="fa fa-chevron-up increment"></i>
    <i class="fa fa-chevron-down decrement"></i>
  </div>
</div>


<i class="fa fa-filter filter-icon" onclick="toggleFilter()"></i>

<div class="filter-dropdown" id="filterDropdown">

<label><input type="checkbox"> All</label>
<label><input type="checkbox"> Active</label>
<label><input type="checkbox"> Inactive</label>
<label><input type="checkbox"> To Receive</label>
<label><input type="checkbox"> To Pay</label>

<div class="filter-actions">
<button class="clear-btn">Clear</button>
<button class="apply-btn">Apply</button>
</div>

</div>

</div>
          <!-- Vertical separator -->
    <div class="separator"></div>
 <div class="parent-arrows" onclick="this.classList.toggle('active')">
  <span class="entity-balance positive" style="color: gray !important;">Amount</span>
  <div class="counter-arrows">
    <i class="fa fa-chevron-up increment"></i>
    <i class="fa fa-chevron-down decrement"></i>
  </div>
</div>
    
    
    
</li>
    <ul id="partiesList">
  @foreach($parties as $party)
    <li class="party-item" data-id="{{ $party->id }}" data-name="{{ $party->name }}" data-phone="{{ $party->phone }}" data-email="{{ $party->email }}" data-billing-address="{{ $party->billing_address }}"
 data-shipping-address="{{ $party->shipping_address }}"
  data-opening-balance="{{ $party->opening_balance }}"
  data-transaction-type="{{ $party->transaction_type }}">
      <span class="entity-name">{{ $party->name }}</span>
      <span class="entity-balance {{ $party->opening_balance < 0 ? 'negative' : 'positive' }}">
        ₹ {{ number_format($party->opening_balance, 2) }}
      </span>
    </li>
  @endforeach
</ul>
    </div>
    <!-- Right: Party Details -->
    <div class="split-right">
      <div class="detail-panel-header">
        <div>
          <div style="display: flex;">
          <div class="entity-detail-name" id="partyDetailName" style="font-weight: 400;">abc
          
          </div>
           <button class="btn-icon"  id="editPartyBtn" title="Edit">  <i class="fa-solid fa-pen"></i>
 
</button>
</div>
          
         <div class="entity-detail-meta-row">

  <div class="entity-detail-meta">
    <div class="meta-heading">Phone Number</div>
    <div class="meta-value"  id="partyPhone"> +91 98765 43210</div>
  </div>

  <div class="entity-detail-meta">
    <div class="meta-heading">Email</div>
    <div class="meta-value" id="partyEmail"> example@email.com</div>
  </div>

  <div class="entity-detail-meta">
    <div class="meta-heading">Billing Address</div>
    <div class="meta-value" id="partyAddress"> 123, Main Street, City</div>
  </div>

</div>
        </div>
        <div class="action-buttons">
         
        </div>
      </div>


    <div class="detail-panel-body">
  <div class="table-header">
    <h6 class="fw-600 mb-3" style="font-size: 14px !important;">Transactions</h6>
    <div class="header-icons">
      <i class="fa fa-search" title="Search"></i>
      <i class="fa fa-file-excel" title="Export to Excel"></i>
      <i class="fa fa-print" title="Print"></i>
    </div>
  </div>


        <table class="txn-table" id="partyTxnTable">
          <thead>
            <tr>
           <th>
  <div class="table-main" onclick="toggleSort(this)">
    <span>Type</span>

    <div class="sort-arrows">
      <i class="fa-solid fa-sort-up"></i>
      <i class="fa-solid fa-sort-down"></i>
    </div>

 <div class="filter-wrapper">
  <i class="fa-solid fa-filter table-filter-icon" onclick="toggleFilterDropdown(this)"></i>

  <div class="filter-dropdown">
    <div class="filter-options">
      <!-- 24 checkboxes -->
      <label><input type="checkbox"> Sale </label>
      <label><input type="checkbox"> Sale(e-invoice) </label>
      <label><input type="checkbox"> Purchase </label>
      <label><input type="checkbox"> Credit Note</label>
      <label><input type="checkbox"> Credit Note(e-invoice)</label>
      <label><input type="checkbox"> Debit Note </label>
      <label><input type="checkbox"> Sale order</label>
      <label><input type="checkbox"> Purchase Order</label>
      <label><input type="checkbox"> Payment-In </label>
      <label><input type="checkbox"> Payment-Out </label>
      <label><input type="checkbox"> Estimate </label>
      <label><input type="checkbox"> Performance Invoice </label>
      <label><input type="checkbox"> Delivery Challan </label>
      <label><input type="checkbox"> Receivable Opening Balance </label>
      <label><input type="checkbox"> Payable Opening Balance</label>
      <label><input type="checkbox"> Party to Party[Recieved] </label>
      <label><input type="checkbox"> Party to Party[Paid]</label>
      <label><input type="checkbox"> Sale FA</label>
      <label><input type="checkbox"> Purchase FA</label>
      <label><input type="checkbox"> Sale[Cancelled]</label>
      <label><input type="checkbox"> Job work out(Challan)</label>
      <label><input type="checkbox"> Purchase(Job work)</label>
      <label><input type="checkbox"> Journal Entry</label>
   
    </div>

    <div class="filter-actions">
      <button class="clear-btn">Clear</button>
      <button class="apply-btn">Apply</button>
    </div>
  </div>
</div>

  </div>
</th>

   <th>
  <div class="table-main" onclick="toggleSort(this)">
    <span>Number</span>

    <div class="sort-arrows">
      <i class="fa-solid fa-sort-up"></i>
      <i class="fa-solid fa-sort-down"></i>
    </div>

 <div class="filter-wrapper">
  <i class="fa-solid fa-filter table-filter-icon" onclick="toggleFilterDropdown(this)"></i>

  <div class="filter-dropdown"  style="width:242px; text-align:;">
    <div class="filter-options">
      <!-- 24 checkboxes -->
    <div class="dropdown-container">
  <label style="color: #9ca3af; width:176px;">Select Category</label>
  <input type="text" readonly class="dropdown-input" placeholder="Select..." >
  <div class="dropdown-options">
    <div class="dropdown-option">Contains</div>
    <div class="dropdown-option">Exact Match</div>
  </div>
</div>

        <label style="color: #9ca3af; margin-top:6px; width:176px;"> Number</label>
      <input type="text" style="border:1px solid #d9dfe5; border-radius:6px;height:5vh"
      >
     
    </div>

    <div class="filter-actions">
      <button class="clear-btn">Clear</button>
      <button class="apply-btn">Apply</button>
    </div>
  </div>
</div>

  </div>
</th>
<!-- date -->
  <th>
  <div class="table-main" onclick="toggleSort(this)">
    <span>Date</span>

    <div class="sort-arrows">
      <i class="fa-solid fa-sort-up"></i>
      <i class="fa-solid fa-sort-down"></i>
    </div>

 <div class="filter-wrapper" style=" position: relative !important;
  overflow: visible;">
  <i class="fa-solid fa-filter table-filter-icon" onclick="toggleFilterDropdown(this)"></i>

  <div class="filter-dropdown"  style="width:242px;">
    <div class="filter-options">
      <!-- 24 checkboxes -->
    <div class="dropdown-container">
  <input type="text" readonly class="dropdown-input" placeholder="Select..." >
  <div class="dropdown-options" style="position:absolute; z-index:100!important;">
    <div class="dropdown-option"> Equal To</div>
    <div class="dropdown-option">Less Than</div>
     <div class="dropdown-option">Greater Than</div>
      <div class="dropdown-option">Range</div>
  </div>
</div>

        <label style="color: #9ca3af; margin-top:6px; width:176px;">Select Date</label>
      <input type="date" style="border:1px solid #d9dfe5; border-radius:6px;height:5vh;color:#9ca3af;padding:6px" >
     
    </div>

    <div class="filter-actions" style="position: relative;">
      <button class="clear-btn">Clear</button>
      <button class="apply-btn">Apply</button>
    </div>
  </div>
</div>

  </div>
</th>
 
<!-- total -->
    <th>
  <div class="table-main" onclick="toggleSort(this)">
    <span>Total</span>

    <div class="sort-arrows">
      <i class="fa-solid fa-sort-up"></i>
      <i class="fa-solid fa-sort-down"></i>
    </div>

 <div class="filter-wrapper">
  <i class="fa-solid fa-filter table-filter-icon" onclick="toggleFilterDropdown(this)"></i>

  <div class="filter-dropdown"  style="width:242px; text-align:;">
    <div class="filter-options">
      <!-- 24 checkboxes -->
    <div class="dropdown-container">
  <label style="color: #9ca3af; width:176px;">Select Category</label>
  <input type="text" readonly class="dropdown-input" placeholder="Select..." >
  <div class="dropdown-options" style="position:absolute; z-index:100!important;">
    <div class="dropdown-option"> Equal To</div>
    <div class="dropdown-option">Less Than</div>
     <div class="dropdown-option">Greater Than</div>
  </div>
</div>

        <label style="color: #9ca3af; margin-top:6px; width:176px;">Total</label>
      <input type="text" style="border:1px solid #d9dfe5; border-radius:6px;height:5vh"
      >
     
    </div>

    <div class="filter-actions">
      <button class="clear-btn">Clear</button>
      <button class="apply-btn">Apply</button>
    </div>
  </div>
</div>

  </div>
</th>

<!-- blance -->

    <th>
  <div class="table-main" onclick="toggleSort(this)">
    <span>Balance</span>

    <div class="sort-arrows">
      <i class="fa-solid fa-sort-up"></i>
      <i class="fa-solid fa-sort-down"></i>
    </div>

 <div class="filter-wrapper">
  <i class="fa-solid fa-filter table-filter-icon" onclick="toggleFilterDropdown(this)"></i>

  <div class="filter-dropdown"  style="width:242px; right: 12px !important;">
    <div class="filter-options">
      <!-- 24 checkboxes -->
    <div class="dropdown-container">
  <label style="color: #9ca3af; width:176px;">Select Category</label>
  <input type="text" readonly class="dropdown-input" placeholder="Select..." >
  <div class="dropdown-options" style="position:absolute; z-index:100!important;">
    <div class="dropdown-option"> Equal To</div>
    <div class="dropdown-option">Less Than</div>
     <div class="dropdown-option">Greater Than</div>
  </div>
</div>

        <label style="color: #9ca3af; margin-top:6px; width:176px;">Balance</label>
      <input type="text" style="border:1px solid #d9dfe5; border-radius:6px;height:5vh"
      >
     
    </div>

    <div class="filter-actions">
      <button class="clear-btn">Clear</button>
      <button class="apply-btn">Apply</button>
    </div>
  </div>
</div>

  </div>
</th>


            </tr>
          </thead>
          <tbody>
            <tr>
              <td><span class="badge bg-primary bg-opacity-10 text-primary">Sale</span></td>
              <td>#001</td>
              <td>10/03/2026</td>
              <td>₹ 500.00</td>
              <td class="text-success-green fw-600">₹ 500.00</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

@endsection
@section('modals')
<!-- MODAL: ADD PARTY -->
<div class="modal fade" id="addPartyModal" tabindex="-1" aria-labelledby="addPartyModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPartyModalLabel"><i class="fa-solid fa-user-plus me-2"></i>Add Party</h5>
        <div class="d-flex align-items-center gap-2">
          <button class="btn btn-sm btn-outline-secondary" title="Settings"><i class="fa-solid fa-gear"></i></button>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
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

          <button class="btn btn-primary" id="btnUpdateParty">Update</button>
<button class="btn btn-danger" id="btnDeleteParty">Delete</button> 
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

const saveBtn = document.getElementById("btnSaveParty");
const saveNewBtn = document.getElementById("btnSaveNewParty");
const updateBtn = document.getElementById("btnUpdateParty");
const deleteBtn = document.getElementById("btnDeleteParty");

const partyList = document.getElementById("partiesList");

const addModalEl = document.getElementById('addPartyModal');
const addModal = new bootstrap.Modal(addModalEl);

let currentPartyId = null;


// RESET MODAL
function resetModal(){

document.getElementById("addPartyForm").reset();

saveBtn.style.display = "inline-block";
saveNewBtn.style.display = "inline-block";

updateBtn.style.display = "none";
deleteBtn.style.display = "none";

currentPartyId = null;

}


// GET FORM DATA
function getPartyData(){

return {

name: document.getElementById("partyNameInput").value,

phone: document.getElementById("partyPhoneInput").value,

email: document.querySelector('#partyAddressPane input[type="email"]').value,

billing_address: document.querySelectorAll('#partyAddressPane textarea')[0].value,

shipping_address: document.querySelectorAll('#partyAddressPane textarea')[1].value,

opening_balance: document.querySelector('#partyCreditPane input[type="number"]').value,

as_of_date: document.querySelector('#partyCreditPane input[type="date"]').value,

credit_limit_enabled: document.getElementById("creditLimitSwitch").checked ? 1 : 0,
transaction_type: document.getElementById("toReceive").checked
      ? 'receive'
      : document.getElementById("toPay").checked
      ? 'pay'
      : null,
      

      


};

}


// ADD PARTY
function addParty(closeModal=true){

const partyData = getPartyData();

fetch("{{ route('parties.store') }}",{

method:"POST",

headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":"{{ csrf_token() }}"
},

body: JSON.stringify(partyData)

})

.then(res=>res.json())

.then(data=>{

if(data.success){

const party=data.party;

const li=document.createElement("li");

li.className="party-item";

li.dataset.id=party.id;
li.dataset.name=party.name;
li.dataset.phone=party.phone;
li.dataset.email=party.email;
li.dataset.billingAddress = party.billing_address;
li.dataset.shippingAddress = party.shipping_address;
li.dataset.opening_balance=party.opening_balance;
li.dataset.as_of_date=party.as_of_date;
li.dataset.transactionType = party.transaction_type;


li.innerHTML=`

<span class="entity-name">${party.name}</span>

<span class="entity-balance">₹ ${parseFloat(party.opening_balance).toFixed(2)}</span>

`;

partyList.prepend(li);

if(closeModal){

addModal.hide();
resetModal();

}else{

document.getElementById("addPartyForm").reset();

}

}

});

}


saveBtn.addEventListener("click",()=>addParty(true));

saveNewBtn.addEventListener("click",()=>addParty(false));



// PARTY CLICK → RIGHT PANEL
partyList.addEventListener("click",function(e){

const li=e.target.closest(".party-item");

if(!li) return;

currentPartyId=li.dataset.id;

document.getElementById("partyDetailName").textContent=li.dataset.name;
document.getElementById("partyPhone").textContent=li.dataset.phone;
document.getElementById("partyEmail").textContent=li.dataset.email;

document.getElementById("partyAddress").textContent =
li.dataset.billingAddress;

});


// OPEN ADD MODAL
document.querySelector(".btn-add-entity").addEventListener("click",function(){

resetModal();
addModal.show();

});


// EDIT PARTY
document.getElementById("editPartyBtn").addEventListener("click",function(){

if(!currentPartyId) return alert("Select party first");

const li=document.querySelector(`.party-item[data-id="${currentPartyId}"]`);

document.getElementById("partyNameInput").value=li.dataset.name;
document.getElementById("partyPhoneInput").value=li.dataset.phone;

document.querySelectorAll('#partyAddressPane textarea')[0].value = li.dataset.billingAddress;
document.querySelectorAll('#partyAddressPane textarea')[1].value = li.dataset.shippingAddress;

document.querySelector('#partyCreditPane input[type="number"]').value=li.dataset.opening_balance;
document.querySelector('#partyCreditPane input[type="date"]').value = party.as_of_date || new Date().toISOString().split('T')[0];

li.dataset.transactionType = party.transaction_type;

saveBtn.style.display="none";
saveNewBtn.style.display="none";

updateBtn.style.display="inline-block";
deleteBtn.style.display="inline-block";

addModal.show();

});


// Populate modal with party data for editing
function populatePartyModal(party) {
    currentPartyId = party.id;

    // Fill inputs
    document.getElementById("partyNameInput").value = party.name;
    document.getElementById("partyPhoneInput").value = party.phone;
    document.querySelector('#partyAddressPane input[type="email"]').value = party.email;
    document.querySelectorAll('#partyAddressPane textarea')[0].value = party.billing_address;
    document.querySelectorAll('#partyAddressPane textarea')[1].value = party.shipping_address;
    document.querySelector('#partyCreditPane input[type="number"]').value = party.opening_balance || 0;
    document.querySelector('#partyCreditPane input[type="date"]').value = party.as_of_date || new Date().toISOString().split('T')[0];
    document.getElementById("creditLimitSwitch").checked = party.credit_limit_enabled == 1;

    // Transaction type checkboxes
    if (party.transaction_type === 'receive') {
        document.getElementById("toReceive").checked = true;
        document.getElementById("toPay").checked = false;
    } else if (party.transaction_type === 'pay') {
        document.getElementById("toReceive").checked = false;
        document.getElementById("toPay").checked = true;
    } else {
        document.getElementById("toReceive").checked = false;
        document.getElementById("toPay").checked = false;
    }

    // Show update/delete buttons
    saveBtn.style.display = "none";
    saveNewBtn.style.display = "none";
    updateBtn.style.display = "inline-block";
    deleteBtn.style.display = "inline-block";

    addModal.show();
}

// Attach edit button click
document.getElementById("editPartyBtn").addEventListener("click", function () {
    if (!currentPartyId) return;

    const li = document.querySelector(`.party-item[data-id='${currentPartyId}']`);
    const party = {
        id: li.dataset.id,
        name: li.dataset.name,
        phone: li.dataset.phone,
        email: li.dataset.email,
        billing_address: li.dataset.billingAddress,
        shipping_address: li.dataset.shippingAddress,
        opening_balance: li.dataset.opening_balance,
        // optional: you can store transaction_type in data attribute too
        transaction_type: li.dataset.transactionType || ''
    };

    populatePartyModal(party);
});

// Optional: click on party in list to select
document.querySelectorAll(".party-item").forEach(li => {
    li.addEventListener("click", function () {
        currentPartyId = this.dataset.id;

        document.getElementById("partyDetailName").textContent = this.dataset.name;
        document.getElementById("partyPhone").textContent = this.dataset.phone;
        document.getElementById("partyEmail").textContent = this.dataset.email;
        document.getElementById("partyAddress").textContent = this.dataset.billingAddress;
    });
});


// UPDATE PARTY
updateBtn.addEventListener("click",function(){

if(!currentPartyId) return;

const partyData=getPartyData();

fetch(`/parties/${currentPartyId}`,{

method:"PUT",

headers:{
"Content-Type":"application/json",
"X-CSRF-TOKEN":"{{ csrf_token() }}"
},

body:JSON.stringify(partyData)

})

.then(res=>res.json())

.then(data=>{

if(data.success){

const li=document.querySelector(`.party-item[data-id="${currentPartyId}"]`);

li.dataset.name=partyData.name;
li.dataset.phone=partyData.phone;
li.dataset.email=partyData.email;
li.dataset.billingAddress = partyData.billing_address;
li.dataset.shippingAddress = partyData.shipping_address;
li.dataset.opening_balance=partyData.opening_balance;
li.dataset.transactionType = partyData.transaction_type;

li.querySelector(".entity-name").textContent=partyData.name;

li.querySelector(".entity-balance").textContent="₹ "+parseFloat(partyData.opening_balance).toFixed(2);

document.getElementById("partyDetailName").textContent=partyData.name;
document.getElementById("partyPhone").textContent=partyData.phone;
document.getElementById("partyEmail").textContent=partyData.email;

document.getElementById("partyAddress").textContent =
partyData.billing_address + " | " + partyData.shipping_address;

alert("Party updated successfully");

addModal.hide();
resetModal();

}

});

});


// DELETE PARTY
deleteBtn.addEventListener("click",function(){

if(!currentPartyId) return;

if(!confirm("Delete this party?")) return;

fetch(`/parties/${currentPartyId}`,{

method:"DELETE",

headers:{
"X-CSRF-TOKEN":"{{ csrf_token() }}"
}

})

.then(res=>res.json())

.then(data=>{

if(data.success){

const li=document.querySelector(`.party-item[data-id="${currentPartyId}"]`);

li.remove();

document.getElementById("partyDetailName").textContent="";
document.getElementById("partyPhone").textContent="";
document.getElementById("partyEmail").textContent="";
document.getElementById("partyAddress").textContent="";

currentPartyId=null;

addModal.hide();
resetModal();

}

});

});

});

const toReceive = document.getElementById('toReceive');
const toPay = document.getElementById('toPay');
const transactionTypeValue = document.getElementById('transactionTypeValue');

// Make checkboxes mutually exclusive
[toReceive, toPay].forEach(checkbox => {
  checkbox.addEventListener('change', function() {
    if (this.checked) {
      // Uncheck the other
      [toReceive, toPay].forEach(cb => {
        if (cb !== this) cb.checked = false;
      });
      // Update hidden input
      transactionTypeValue.value = this.value;
    } else {
      transactionTypeValue.value = '';
    }
  });
});
</script>

@endpush
