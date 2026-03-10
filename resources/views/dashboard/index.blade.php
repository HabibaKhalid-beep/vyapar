@extends('layouts.app')

@section('title', 'Vyapar — Dashboard')
@section('description', 'Vyapar billing and accounting dashboard — view total receivable, payable, sales chart, and reports.')
@section('page', 'dashboard')

@section('content')

  <!-- Business Name Bar -->
  <div class="business-name-bar" id="businessNameBar">
    <i class="fa-regular fa-building text-muted"></i>
    <input type="text" class="form-control" placeholder="Enter Business Name" id="businessNameInput">
    <button class="btn btn-primary btn-sm px-4" id="saveBusinessName">Save</button>
  </div>

  <!-- Summary Cards -->
  <div class="row g-3 mb-3">
    <div class="col-md-6">
      <div class="summary-card">
        <div class="card-icon receivable">
          <i class="fa-solid fa-arrow-trend-down"></i>
        </div>
        <div>
          <div class="card-label">Total Receivable</div>
          <div class="card-value">₹ 0</div>
        </div>
      </div>
    </div>
    <div class="col-md-6">
      <div class="summary-card">
        <div class="card-icon payable">
          <i class="fa-solid fa-arrow-trend-up"></i>
        </div>
        <div>
          <div class="card-label">Total Payable</div>
          <div class="card-value">₹ 0</div>
        </div>
      </div>
    </div>
  </div>

  <!-- Chart Area -->
  <div class="chart-card">
    <div class="chart-header">
      <div>
        <div class="chart-title">Total Sale</div>
      </div>
      <div class="chart-total">₹ 100 <small>This Month</small></div>
    </div>
    <div class="chart-area">
      <svg viewBox="0 0 1200 220" preserveAspectRatio="none" width="100%" height="100%">
        <defs>
          <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
            <stop offset="0%" stop-color="rgba(26,115,232,.25)"/>
            <stop offset="100%" stop-color="rgba(26,115,232,.02)"/>
          </linearGradient>
        </defs>
        <!-- Grid Lines -->
        <line x1="0" y1="40" x2="1200" y2="40" stroke="#f0f0f0" stroke-width="1"/>
        <line x1="0" y1="80" x2="1200" y2="80" stroke="#f0f0f0" stroke-width="1"/>
        <line x1="0" y1="120" x2="1200" y2="120" stroke="#f0f0f0" stroke-width="1"/>
        <line x1="0" y1="160" x2="1200" y2="160" stroke="#f0f0f0" stroke-width="1"/>
        <!-- Area Fill -->
        <path d="M20,188 C40,187 55,186 80,185 C105,184 120,186 160,187 C200,188 215,189 240,189 C265,189 280,188 320,186 C360,184 375,182 400,178 C425,174 440,168 480,158 C520,148 535,140 560,130 C585,120 600,108 640,90 C680,72 695,55 720,45 C745,35 755,32 760,30 C765,32 775,35 800,45 C825,55 840,72 880,90 C920,108 935,120 960,135 C985,150 1000,162 1040,172 C1080,182 1095,186 1120,188 C1145,189 1160,190 1180,190 L1180,210 L20,210 Z"
              fill="url(#chartGrad)"/>
        <!-- Line -->
        <path d="M20,188 C40,187 55,186 80,185 C105,184 120,186 160,187 C200,188 215,189 240,189 C265,189 280,188 320,186 C360,184 375,182 400,178 C425,174 440,168 480,158 C520,148 535,140 560,130 C585,120 600,108 640,90 C680,72 695,55 720,45 C745,35 755,32 760,30 C765,32 775,35 800,45 C825,55 840,72 880,90 C920,108 935,120 960,135 C985,150 1000,162 1040,172 C1080,182 1095,186 1120,188 C1145,189 1160,190 1180,190"
              fill="none" stroke="#1a73e8" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
        <!-- Peak Dot -->
        <circle cx="760" cy="30" r="5" fill="#1a73e8"/>
        <circle cx="760" cy="30" r="9" fill="rgba(26,115,232,.18)"/>
        <!-- X-axis labels -->
        <text x="20"   y="208" fill="#999" font-size="10" text-anchor="middle">Mar 1</text>
        <text x="100"  y="208" fill="#999" font-size="10" text-anchor="middle">Mar 3</text>
        <text x="180"  y="208" fill="#999" font-size="10" text-anchor="middle">Mar 5</text>
        <text x="260"  y="208" fill="#999" font-size="10" text-anchor="middle">Mar 7</text>
        <text x="340"  y="208" fill="#999" font-size="10" text-anchor="middle">Mar 9</text>
        <text x="420"  y="208" fill="#999" font-size="10" text-anchor="middle">Mar 11</text>
        <text x="500"  y="208" fill="#999" font-size="10" text-anchor="middle">Mar 13</text>
        <text x="580"  y="208" fill="#999" font-size="10" text-anchor="middle">Mar 15</text>
        <text x="660"  y="208" fill="#999" font-size="10" text-anchor="middle">Mar 17</text>
        <text x="740"  y="208" fill="#999" font-size="10" text-anchor="middle">Mar 19</text>
        <text x="820"  y="208" fill="#999" font-size="10" text-anchor="middle">Mar 21</text>
        <text x="900"  y="208" fill="#999" font-size="10" text-anchor="middle">Mar 23</text>
        <text x="980"  y="208" fill="#999" font-size="10" text-anchor="middle">Mar 25</text>
        <text x="1060" y="208" fill="#999" font-size="10" text-anchor="middle">Mar 27</text>
        <text x="1140" y="208" fill="#999" font-size="10" text-anchor="middle">Mar 29</text>
      </svg>
    </div>
  </div>

  <!-- Reports -->
  <div class="reports-section">
    <h6>Most Used Reports</h6>
    <div class="row g-3">
      <div class="col-md-4">
        <div class="report-card">
          <i class="fa-solid fa-file-lines d-block"></i>
          <h6>Sale Report</h6>
        </div>
      </div>
      <div class="col-md-4">
        <div class="report-card">
          <i class="fa-solid fa-list-check d-block"></i>
          <h6>All Transactions</h6>
        </div>
      </div>
      <div class="col-md-4">
        <div class="report-card">
          <i class="fa-solid fa-book-open d-block"></i>
          <h6>Daybook Report</h6>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
  <script src="{{ asset('js/dashboard.js') }}"></script>
@endpush
