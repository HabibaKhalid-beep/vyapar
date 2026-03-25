@extends('layouts.app')

@section('title', 'Vyapar - Roles')
@section('description', 'Manage user roles and permissions.')
@section('page', 'roles')

@push('styles')
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css">
  <style>
    .roles-page {
      color: #0f172a;
    }

    .roles-page .hero-card {
      position: relative;
      overflow: hidden;
      margin-bottom: 1.5rem;
      padding: 1.7rem;
      border: 1px solid rgba(148, 163, 184, 0.2);
      border-radius: 1.4rem;
      background:
        radial-gradient(circle at top right, rgba(14, 165, 233, 0.16), transparent 30%),
        linear-gradient(135deg, #f8fbff 0%, #ffffff 52%, #f4f8fc 100%);
      box-shadow: 0 28px 60px -42px rgba(15, 23, 42, 0.4);
    }

    .roles-page .hero-card::after {
      content: "";
      position: absolute;
      right: -3rem;
      bottom: -3rem;
      width: 10rem;
      height: 10rem;
      border-radius: 999px;
      background: rgba(37, 99, 235, 0.08);
    }

    .roles-page .eyebrow {
      display: inline-flex;
      align-items: center;
      padding: 0.42rem 0.82rem;
      border-radius: 999px;
      background: rgba(15, 23, 42, 0.06);
      color: #0369a1;
      font-size: 0.76rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .roles-page .hero-title {
      margin: 0.85rem 0 0;
      font-size: 2rem;
      font-weight: 800;
      letter-spacing: -0.04em;
      color: #0f172a;
    }

    .roles-page .hero-subtitle {
      margin: 0.7rem 0 0;
      max-width: 44rem;
      color: #475569;
      font-size: 0.98rem;
      line-height: 1.75;
    }

    .roles-page .hero-actions {
      display: flex;
      justify-content: flex-end;
      align-items: center;
      height: 100%;
    }

    .roles-page .create-btn {
      display: inline-flex;
      align-items: center;
      gap: 0.55rem;
      padding: 0.92rem 1.35rem;
      border: none;
      border-radius: 999px;
      background: linear-gradient(135deg, #0f172a 0%, #2563eb 100%);
      box-shadow: 0 24px 42px -24px rgba(37, 99, 235, 0.6);
      font-weight: 700;
    }

    .roles-page .create-btn:hover,
    .roles-page .create-btn:focus {
      opacity: 0.98;
      transform: translateY(-1px);
    }

    .roles-page .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 1rem;
      margin-bottom: 1.5rem;
    }

    .roles-page .stat-card {
      padding: 1.2rem 1.25rem;
      border: 1px solid rgba(148, 163, 184, 0.18);
      border-radius: 1.15rem;
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.98), rgba(248, 250, 252, 0.96));
      box-shadow: 0 22px 44px -38px rgba(15, 23, 42, 0.45);
    }

    .roles-page .stat-label {
      margin: 0;
      color: #64748b;
      font-size: 0.82rem;
      font-weight: 700;
      letter-spacing: 0.06em;
      text-transform: uppercase;
    }

    .roles-page .stat-value {
      margin: 0.4rem 0 0;
      font-size: 1.8rem;
      font-weight: 800;
      letter-spacing: -0.04em;
      color: #0f172a;
    }

    .roles-page .stat-note {
      margin: 0.3rem 0 0;
      color: #475569;
      font-size: 0.9rem;
    }

    .roles-page .table-shell {
      border: 1px solid rgba(148, 163, 184, 0.18);
      border-radius: 1.35rem;
      background: linear-gradient(180deg, rgba(255, 255, 255, 0.99), rgba(248, 250, 252, 0.98));
      box-shadow: 0 26px 60px -42px rgba(15, 23, 42, 0.45);
      overflow: hidden;
    }

    .roles-page .table-toolbar {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      padding: 1.2rem 1.3rem;
      border-bottom: 1px solid rgba(226, 232, 240, 0.9);
      background: rgba(248, 250, 252, 0.8);
    }

    .roles-page .table-title {
      margin: 0;
      font-size: 1.05rem;
      font-weight: 800;
      letter-spacing: -0.02em;
    }

    .roles-page .table-copy {
      margin: 0.25rem 0 0;
      color: #64748b;
      font-size: 0.9rem;
    }

    .roles-page .table-chip {
      display: inline-flex;
      align-items: center;
      padding: 0.42rem 0.8rem;
      border-radius: 999px;
      background: rgba(37, 99, 235, 0.08);
      color: #1d4ed8;
      font-size: 0.8rem;
      font-weight: 700;
    }

    .roles-page .table-wrap {
      padding: 1.15rem 1.25rem 1.25rem;
    }

    .roles-page #rolesTable {
      width: 100% !important;
      margin: 0 !important;
    }

    .roles-page #rolesTable thead th {
      border-bottom: none;
      padding: 0.95rem 1rem;
      background: #f8fafc;
      color: #475569;
      font-size: 0.8rem;
      font-weight: 800;
      letter-spacing: 0.08em;
      text-transform: uppercase;
    }

    .roles-page #rolesTable tbody td {
      padding: 1rem;
      vertical-align: middle;
      border-color: rgba(226, 232, 240, 0.75);
    }

    .roles-page .role-name {
      margin: 0;
      font-size: 1rem;
      font-weight: 700;
      color: #0f172a;
    }

    .roles-page .role-meta {
      margin-top: 0.18rem;
      color: #64748b;
      font-size: 0.85rem;
    }

    .roles-page .permission-pill {
      display: inline-flex;
      align-items: center;
      padding: 0.42rem 0.78rem;
      border-radius: 999px;
      background: rgba(15, 23, 42, 0.06);
      color: #0f172a;
      font-size: 0.82rem;
      font-weight: 700;
    }

    .roles-page .actions-cell {
      white-space: nowrap;
    }

    .roles-page .action-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 2.35rem;
      height: 2.35rem;
      border-radius: 0.9rem;
      border: 1px solid rgba(226, 232, 240, 1);
      background: #fff;
      color: #334155;
      transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease;
    }

    .roles-page .action-btn:hover,
    .roles-page .action-btn:focus {
      transform: translateY(-1px);
      box-shadow: 0 16px 24px -20px rgba(15, 23, 42, 0.35);
    }

    .roles-page .action-btn.edit-btn:hover,
    .roles-page .action-btn.edit-btn:focus {
      border-color: rgba(59, 130, 246, 0.3);
      color: #2563eb;
    }

    .roles-page .action-btn.delete-btn:hover,
    .roles-page .action-btn.delete-btn:focus {
      border-color: rgba(239, 68, 68, 0.28);
      color: #dc2626;
    }

    .roles-page .dataTables_wrapper .dataTables_filter label,
    .roles-page .dataTables_wrapper .dataTables_length label {
      color: #64748b;
      font-size: 0.9rem;
      font-weight: 600;
    }

    .roles-page .dataTables_wrapper .dataTables_filter input,
    .roles-page .dataTables_wrapper .dataTables_length select {
      margin-left: 0.45rem;
      border: 1px solid rgba(148, 163, 184, 0.35);
      border-radius: 0.8rem;
      padding: 0.45rem 0.75rem;
      background: #fff;
      box-shadow: none;
    }

    .roles-page .dataTables_wrapper .dataTables_info {
      padding-top: 1rem;
      color: #64748b;
      font-size: 0.88rem;
    }

    .roles-page .dataTables_wrapper .pagination {
      gap: 0.35rem;
      margin-top: 1rem;
    }

    .roles-page .dataTables_wrapper .page-link {
      border: 1px solid rgba(226, 232, 240, 1);
      border-radius: 0.75rem;
      color: #334155;
      box-shadow: none;
    }

    .roles-page .dataTables_wrapper .page-item.active .page-link {
      border-color: #2563eb;
      background: #2563eb;
      color: #fff;
    }

    .roles-page .empty-state {
      padding: 2.2rem 1rem;
      text-align: center;
      color: #64748b;
    }

    @media (max-width: 991.98px) {
      .roles-page .stats-grid {
        grid-template-columns: 1fr;
      }

      .roles-page .hero-actions {
        justify-content: flex-start;
        margin-top: 1rem;
      }
    }

    @media (max-width: 767.98px) {
      .roles-page .hero-card,
      .roles-page .table-toolbar,
      .roles-page .table-wrap {
        padding: 1rem;
      }

      .roles-page .hero-title {
        font-size: 1.55rem;
      }

      .roles-page .table-toolbar {
        flex-direction: column;
        align-items: flex-start;
      }
    }
  </style>
@endpush

@section('content')
  <div class="roles-page">
    <div class="hero-card">
      <div class="row align-items-center">
        <div class="col-lg-8">
          <span class="eyebrow">Access Control</span>
          <h1 class="hero-title">Professional role management</h1>
          <p class="hero-subtitle">Create, update, and organize user roles with a cleaner admin experience. Review permissions quickly, use table search instantly, and keep access control easy to manage.</p>
        </div>
        <div class="col-lg-4">
          <div class="hero-actions">
            <a href="{{ route('roles.create') }}" class="btn btn-primary create-btn">
              <i class="fa-solid fa-plus"></i>
              <span>Add Role</span>
            </a>
          </div>
        </div>
      </div>
    </div>

    <div class="stats-grid">
      <div class="stat-card">
        <p class="stat-label">Total Roles</p>
        <p class="stat-value">{{ $roles->count() }}</p>
        <p class="stat-note">All roles currently available in the system.</p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Permission Coverage</p>
        <p class="stat-value">{{ $roles->sum('permissions_count') }}</p>
        <p class="stat-note">Combined assigned permissions across all roles.</p>
      </div>
      <div class="stat-card">
        <p class="stat-label">Ready To Manage</p>
        <p class="stat-value">{{ $roles->where('permissions_count', '>', 0)->count() }}</p>
        <p class="stat-note">Roles already configured with at least one permission.</p>
      </div>
    </div>

@if(session('success'))
  <div id="successMessage" class="alert alert-success border-0 shadow-sm" role="alert">
    {{ session('success') }}
  </div>
@endif

    <div class="table-shell">
      <div class="table-toolbar">
        <div>
          <h2 class="table-title">Roles Directory</h2>
          <p class="table-copy">Search, sort, and manage role access from one organized table.</p>
        </div>
        <span class="table-chip">{{ $roles->count() }} records</span>
      </div>

      <div class="table-wrap">
        <div class="table-responsive">
          <table class="table align-middle" id="rolesTable">
            <thead>
              <tr>
                <th scope="col">Role</th>
                <th scope="col">Permissions</th>
                <th scope="col" class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              @forelse($roles as $role)
                <tr>
                  <td>
                    <p class="role-name">{{ $role->name }}</p>
                    <div class="role-meta">Role ID: {{ $role->id }}</div>
                  </td>
                  <td>
                    <span class="permission-pill">{{ $role->permissions_count }} permissions</span>
                  </td>
                  <td class="text-end actions-cell">
                    <a href="{{ route('roles.edit', $role) }}" class="action-btn edit-btn me-2" title="Edit role">
                      <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <form action="{{ route('roles.destroy', $role) }}" method="POST" class="d-inline">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="action-btn delete-btn" title="Delete role" onclick="return confirm('Are you sure you want to delete this role?');">
                        <i class="fa-solid fa-trash"></i>
                      </button>
                    </form>
                  </td>
                </tr>
              @empty
                <tr>
                  <td colspan="3">
                    <div class="empty-state">
                      No roles found yet. Create your first role to start managing permissions.
                    </div>
                  </td>
                </tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      $('#rolesTable').DataTable({
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, 'All']],
        order: [[0, 'asc']],
        columnDefs: [
          { orderable: false, searchable: false, targets: 2 }
        ],
        language: {
          search: '',
          searchPlaceholder: 'Search roles...',
          lengthMenu: 'Show _MENU_ roles',
          info: 'Showing _START_ to _END_ of _TOTAL_ roles',
          emptyTable: 'No roles available right now'
        }
      });
    });
  </script>

  <script>
  setTimeout(function() {
    let msg = document.getElementById('successMessage');
    if (msg) {
      msg.style.transition = "opacity 0.5s ease";
      msg.style.opacity = "0";

      setTimeout(() => {
        msg.remove();
      }, 500); // fade out complete hone ke baad remove
    }
  }, 1500); // 1.5 seconds
</script>

@endpush
