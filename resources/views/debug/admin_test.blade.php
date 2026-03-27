@extends('layouts.app')

@section('title', 'Admin Debug Test')
@section('page', 'dashboard')

@section('content')
<div class="container py-4">
  <h1>Admin Debug Test (Dashboard)</h1>
  <p>Use this page to validate sidebar visibility as Admin.</p>
  <pre id="debugOutput"></pre>
</div>

<script>
  const out = {
    appUser: window.App?.user || null,
    isSuperAdmin: null,
  };
  if (window.App?.user) {
    const roles = Array.isArray(window.App.user.roles) ? window.App.user.roles : [];
    const role = typeof window.App.user.role === 'string' ? window.App.user.role : '';
    out.isSuperAdmin =
      window.App.user.id === 1 ||
      roles.some(r => r.toLowerCase().includes('admin')) ||
      role.toLowerCase().includes('admin') ||
      (Array.isArray(window.App.user.permissions) && (window.App.user.permissions.includes('admin') || window.App.user.permissions.includes('super-admin')));
  }
  document.getElementById('debugOutput').textContent = JSON.stringify(out, null, 2);
  console.log('Admin Debug:', out);
</script>
@endsection
