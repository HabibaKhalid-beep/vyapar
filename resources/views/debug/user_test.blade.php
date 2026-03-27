@extends('layouts.app')

@section('title', 'User Debug Test')
@section('page', 'dashboard')

@section('content')
<div class="container py-4">
  <h1>User Debug Test (Dashboard)</h1>
  <p>Use this page to validate sidebar visibility for a normal user.</p>
  <pre id="debugOutput"></pre>
</div>

<script>
  const out = {
    appUser: window.App?.user || null,
    visibleMenus: []
  };

  // If you need, you can inspect rendered sidebar items directly.
  const links = document.querySelectorAll('.sidebar-nav .nav-link');
  out.visibleMenus = Array.from(links).map(l => ({ text: l.textContent.trim(), href: l.getAttribute('href'), page: l.dataset.page }));

  document.getElementById('debugOutput').textContent = JSON.stringify(out, null, 2);
  console.log('User Debug:', out);
</script>
@endsection
