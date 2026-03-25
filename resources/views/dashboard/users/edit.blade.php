@extends('layouts.app')

@section('title', 'Vyapar - Edit User Roles')
@section('description', 'Update roles for a user')
@section('page', 'users')

@section('content')
  @php
    $selectedRoles = collect(old('roles', $userRoles ?? $user->roles->pluck('id')->toArray()))
      ->map(fn ($roleId) => (int) $roleId)
      ->toArray();
  @endphp

  <style>
    .user-form-shell {
      max-width: 1080px;
      margin: 0 auto;
    }

    .user-form-hero {
      background: linear-gradient(135deg, #000000, #000000 58%, #000000);
      border-radius: 28px;
      color: #fff;
      padding: 28px 32px;
      box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
      position: relative;
      overflow: hidden;
    }

    .user-form-hero::after {
      content: '';
      position: absolute;
      inset: 22px -70px auto auto;
      width: 220px;
      height: 220px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.12);
    }

    .user-form-card {
      margin-top: -26px;
      background: #fff;
      border: 1px solid #e2e8f0;
      border-radius: 28px;
      box-shadow: 0 22px 60px rgba(15, 23, 42, 0.08);
      overflow: hidden;
    }

    .user-form-card .card-body {
      padding: 32px;
    }

    .user-section-title {
      font-size: 0.82rem;
      font-weight: 700;
      letter-spacing: 0.08em;
      text-transform: uppercase;
      color: #64748b;
      margin-bottom: 1rem;
    }

    .user-form-card .form-label {
      font-weight: 600;
      color: #0f172a;
      margin-bottom: 0.6rem;
    }

    .user-form-card .form-control {
      border-radius: 16px;
      border: 1px solid #cbd5e1;
      min-height: 52px;
      padding: 0.85rem 1rem;
      box-shadow: none;
    }

    .user-form-card .form-control:focus {
      border-color: #0f766e;
      box-shadow: 0 0 0 4px rgba(15, 118, 110, 0.12);
    }

    .role-picker {
      border: 1px solid #dbe4f0;
      border-radius: 22px;
      background: linear-gradient(180deg, #f8fffd, #f1f5f9);
      padding: 18px;
    }

    .role-picker-toggle {
      width: 100%;
      border: 0;
      background: #fff;
      border-radius: 18px;
      padding: 16px 18px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      box-shadow: inset 0 0 0 1px #d7e2f1;
      color: #0f172a;
    }

    .role-picker-toggle:focus {
      outline: none;
      box-shadow: inset 0 0 0 1px #0f766e, 0 0 0 4px rgba(15, 118, 110, 0.12);
    }

    .role-picker-summary {
      display: flex;
      flex-wrap: wrap;
      gap: 8px;
      align-items: center;
      min-height: 28px;
    }

    .role-picker-placeholder {
      color: #64748b;
      font-weight: 500;
    }

    .role-picker-badge {
      background: #ccfbf1;
      color: #0f766e;
      border-radius: 999px;
      padding: 6px 12px;
      font-size: 0.82rem;
      font-weight: 700;
    }

    .role-picker-arrow {
      color: #64748b;
      transition: transform 0.25s ease;
    }

    .role-picker.open .role-picker-arrow {
      transform: rotate(180deg);
    }

    .role-picker-menu {
      display: none;
      margin-top: 14px;
      background: #fff;
      border-radius: 20px;
      border: 1px solid #dbe4f0;
      padding: 14px;
      box-shadow: 0 18px 34px rgba(15, 23, 42, 0.08);
    }

    .role-picker.open .role-picker-menu {
      display: block;
    }

    .role-picker-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 12px;
    }

    .role-option {
      position: relative;
    }

    .role-option input {
      position: absolute;
      opacity: 0;
      pointer-events: none;
    }

    .role-option-card {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 14px 16px;
      border-radius: 16px;
      border: 1px solid #dbe4f0;
      background: #f8fafc;
      cursor: pointer;
      transition: 0.2s ease;
    }

    .role-option-card:hover {
      border-color: #5eead4;
      background: #ecfeff;
      transform: translateY(-1px);
    }

    .role-option-indicator {
      width: 20px;
      height: 20px;
      border-radius: 6px;
      border: 2px solid #94a3b8;
      display: inline-flex;
      align-items: center;
      justify-content: center;
      flex-shrink: 0;
      transition: 0.2s ease;
    }

    .role-option-indicator::after {
      content: '';
      width: 8px;
      height: 8px;
      border-radius: 2px;
      background: #fff;
      transform: scale(0);
      transition: transform 0.2s ease;
    }

    .role-option input:checked + .role-option-card {
      border-color: #0f766e;
      background: linear-gradient(135deg, #ecfeff, #ccfbf1);
      box-shadow: 0 10px 20px rgba(15, 118, 110, 0.12);
    }

    .role-option input:checked + .role-option-card .role-option-indicator {
      border-color: #0f766e;
      background: #0f766e;
    }

    .role-option input:checked + .role-option-card .role-option-indicator::after {
      transform: scale(1);
    }

    .user-form-actions {
      display: flex;
      flex-wrap: wrap;
      gap: 12px;
      margin-top: 28px;
    }

    .user-form-actions .btn {
      min-width: 140px;
      border-radius: 999px;
      padding: 0.8rem 1.4rem;
      font-weight: 700;
    }

    @media (max-width: 767.98px) {
      .user-form-hero,
      .user-form-card .card-body {
        padding: 22px;
      }
    }
  </style>

  <div class="container py-4">
    <div class="user-form-shell">
      <div class="user-form-hero mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-start gap-3 position-relative" style="z-index:1;">
          <div>
            <span class="badge rounded-pill text-bg-light text-success mb-3 px-3 py-2">User Roles</span>
            <h1 class="h3 mb-2 text-white">Edit roles for {{ $user->name }}</h1>
          </div>
          <a href="{{ route('users.index') }}" class="btn btn-light rounded-pill px-4">Back to Users</a>
        </div>
      </div>

      <div class="card user-form-card">
        <div class="card-body">
          <form action="{{ route('users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="user-section-title">Basic Details</div>
            <div class="row g-4">
              <div class="col-md-6">
                <label for="name" class="form-label">Name</label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>
            </div>

            <div class="user-section-title mt-4">Security</div>
            <div class="row g-4">
              <div class="col-md-6">
                <label for="password" class="form-label">Password <small class="text-muted">(leave blank to keep current)</small></label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" autocomplete="new-password">
                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
              </div>

              <div class="col-md-6">
                <label for="password_confirmation" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" autocomplete="new-password">
              </div>
            </div>

            <div class="user-section-title mt-4">Roles & Permissions</div>
            <div class="role-picker @error('roles') border border-danger @enderror" data-role-dropdown>
              <button type="button" class="role-picker-toggle" data-role-toggle aria-expanded="false">
                <div>
                  <div class="small text-uppercase fw-bold text-success mb-2">Assigned Roles</div>
                  <div class="role-picker-summary" data-role-summary>
                    @if (count($selectedRoles))
                      @foreach ($roles as $role)
                        @if (in_array($role->id, $selectedRoles))
                          <span class="role-picker-badge">{{ $role->name }}</span>
                        @endif
                      @endforeach
                    @else
                      <span class="role-picker-placeholder">Choose one or more roles from the dropdown</span>
                    @endif
                  </div>
                </div>
                <i class="fa-solid fa-chevron-down role-picker-arrow"></i>
              </button>

              <div class="role-picker-menu" data-role-menu>
                <div class="role-picker-grid">
                  @foreach($roles as $role)
                    <label class="role-option">
                      <input
                        class="role-option-input"
                        type="checkbox"
                        name="roles[]"
                        value="{{ $role->id }}"
                        data-role-name="{{ $role->name }}"
                        {{ in_array($role->id, $selectedRoles) ? 'checked' : '' }}
                      >
                      <span class="role-option-card">
                        <span class="role-option-indicator"></span>
                        <span class="fw-semibold">{{ $role->name }}</span>
                      </span>
                    </label>
                  @endforeach
                </div>
              </div>
            </div>
            <small class="text-muted d-block mt-2">Permissions automatically selected roles se inherit hongi.</small>
            @error('roles') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror

            <div class="user-form-actions">
              <button type="submit" class="btn btn-primary">Save</button>
              <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Cancel</a>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('[data-role-dropdown]').forEach(function (dropdown) {
        const toggle = dropdown.querySelector('[data-role-toggle]');
        const summary = dropdown.querySelector('[data-role-summary]');
        const inputs = dropdown.querySelectorAll('.role-option-input');

        const renderSummary = function () {
          const selected = Array.from(inputs).filter(function (input) {
            return input.checked;
          });

          if (!selected.length) {
            summary.innerHTML = '<span class="role-picker-placeholder">Choose one or more roles from the dropdown</span>';
            return;
          }

          summary.innerHTML = selected.map(function (input) {
            return '<span class="role-picker-badge">' + input.dataset.roleName + '</span>';
          }).join('');
        };

        toggle.addEventListener('click', function () {
          const isOpen = dropdown.classList.toggle('open');
          toggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        inputs.forEach(function (input) {
          input.addEventListener('change', renderSummary);
        });

        document.addEventListener('click', function (event) {
          if (dropdown.contains(event.target)) return;
          dropdown.classList.remove('open');
          toggle.setAttribute('aria-expanded', 'false');
        });

        renderSummary();
      });
    });
  </script>
@endsection
