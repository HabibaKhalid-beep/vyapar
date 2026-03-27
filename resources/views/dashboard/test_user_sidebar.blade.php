@extends('layouts.app')

@section('title', 'User Sidebar Test')
@section('page', 'dashboard')

@section('content')
<div class="container mt-5">
  <div class="card">
    <div class="card-header bg-info text-white">
      <h5 class="mb-0">✅ User Sidebar Test</h5>
    </div>
    <div class="card-body">
      <p><strong>Assigned User:</strong> Should see ONLY assigned permission menu items</p>
      <hr />
      <h6>Example: Sales-Only User Should See:</h6>
      <ul style="line-height: 2;">
        <li>✓ Home (always visible)</li>
        <li>✓ Sale (with assigned sub-items only)</li>
        <li>✗ User Management (hidden)</li>
        <li>✗ Parties (hidden)</li>
        <li>✗ Items (hidden)</li>
        <li>✗ Purchase & Expense (hidden)</li>
        <li>✗ Grow Your Business (hidden)</li>
        <li>✗ Cash & Bank (hidden)</li>
        <li>✗ Reports (hidden)</li>
        <li>✗ Others (hidden)</li>
      </ul>
      <hr />
      <h6>Current User: <strong>{{ Auth::user()->name ?? 'Not Logged In' }}</strong></h6>
      <h6>Assigned Role: <strong>{{ Auth::user()?->roles()->pluck('name')->join(', ') ?? 'No role' }}</strong></h6>
      <h6>Assigned Permissions:</h6>
      <div style="background: #f9f9f9; padding: 10px; border-radius: 5px; max-height: 200px; overflow-y: auto;">
        @forelse(Auth::user()?->getAllPermissions() ?? [] as $perm)
          <span class="badge bg-secondary" style="margin: 3px;">{{ $perm }}</span>
        @empty
          <span class="text-muted">No permissions assigned</span>
        @endforelse
      </div>
      <hr />
      <h6>Debug Info:</h6>
      <pre id="userDebugOutput" style="background: #f5f5f5; padding: 10px; border-radius: 5px;"></pre>
    </div>
  </div>
</div>

<script>
  const debugInfo = {
    userInfo: window.App?.user,
    isAuthenticated: window.App?.isAuthenticated,
    userRoles: window.App?.user?.roles,
    userPermissions: window.App?.user?.permissions?.slice(0, 10), // First 10 perms for brevity
    totalPermissions: window.App?.user?.permissions?.length,
    visibleMenuItems: [],
  };

  // Count visible sidebar items
  const sidebarItems = document.querySelectorAll('.sidebar-nav .nav-item, .sidebar-nav > li');
  debugInfo.totalMenuItems = sidebarItems.length;
  debugInfo.visibleMenuItems = Array.from(sidebarItems).map(item => ({
    text: item.textContent.trim().split('\n')[0],
    hasSubmenu: item.querySelector('.sidebar-submenu') !== null,
  }));

  document.getElementById('userDebugOutput').textContent = JSON.stringify(debugInfo, null, 2);
  console.log('User Sidebar Test:', debugInfo);
</script>
@endsection
