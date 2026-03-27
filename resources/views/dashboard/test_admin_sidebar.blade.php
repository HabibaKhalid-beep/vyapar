@extends('layouts.app')

@section('title', 'Admin Sidebar Test')
@section('page', 'dashboard')

@section('content')
<div class="container mt-5">
  <div class="card">
    <div class="card-header bg-success text-white">
      <h5 class="mb-0">✅ Admin Sidebar Test</h5>
    </div>
    <div class="card-body">
      <p><strong>Admin User:</strong> Should see FULL sidebar with all menu items</p>
      <hr />
      <h6>Expected Sidebar Items for Admin:</h6>
      <ul style="line-height: 2;">
        <li>✓ Home</li>
        <li>✓ User Management (with Roles, Users)</li>
        <li>✓ Parties</li>
        <li>✓ Items</li>
        <li>✓ Sale (with all sub-items: Invoice, Estimate, Payment In, Proforma, Order, Delivery, Return, POS)</li>
        <li>✓ Purchase & Expense (with all sub-items: Bill, Payment Out, Return, Expense, Order)</li>
        <li>✓ Grow Your Business</li>
        <li>✓ Cash & Bank (with Loan Accounts, Bank Accounts)</li>
        <li>✓ Reports</li>
        <li>✓ Sync / Share / Backup</li>
        <li>✓ Utilities</li>
        <li>✓ Settings</li>
      </ul>
      <hr />
      <h6>Debug Info:</h6>
      <pre id="adminDebugOutput" style="background: #f5f5f5; padding: 10px; border-radius: 5px;"></pre>
    </div>
  </div>
</div>

<script>
  const debugInfo = {
    userInfo: window.App?.user,
    isAuthenticated: window.App?.isAuthenticated,
    userRoles: window.App?.user?.roles,
    userPermissions: window.App?.user?.permissions,
    visibleMenuItems: [],
  };

  // Count visible sidebar items
  const sidebarItems = document.querySelectorAll('.sidebar-nav .nav-item, .sidebar-nav > li');
  debugInfo.totalMenuItems = sidebarItems.length;
  debugInfo.visibleMenuItems = Array.from(sidebarItems).map(item => ({
    text: item.textContent.trim().split('\n')[0],
    hasSubmenu: item.querySelector('.sidebar-submenu') !== null,
  }));

  document.getElementById('adminDebugOutput').textContent = JSON.stringify(debugInfo, null, 2);
  console.log('Admin Sidebar Test:', debugInfo);
</script>
@endsection
