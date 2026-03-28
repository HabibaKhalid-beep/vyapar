@extends('layouts.app')

@php
  $isEdit = isset($role) && $role;
@endphp

@section('title', $isEdit ? 'Vyapar - Edit Role' : 'Vyapar - Add Role')
@section('description', $isEdit ? 'Update an existing user role and its permissions.' : 'Create a new user role and assign permissions.')
@section('page', 'roles')

@section('content')
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h1 class="h3">{{ $isEdit ? 'Edit Role' : 'Add Role' }}</h1>
      <p class="text-muted mb-0">{{ $isEdit ? 'Update the role name and refine permission access for this role.' : 'Create a new role and assign the permissions it should have.' }}</p>
    </div>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">
      <i class="fa-solid fa-arrow-left me-1"></i> Back to Roles
    </a>
  </div>

  @if(session('success'))
    <div class="alert alert-success" role="alert">
      {{ session('success') }}
    </div>
  @endif

  <div class="card">
    <div class="card-body">
      <form action="{{ $isEdit ? route('roles.update', $role) : route('roles.store') }}" method="POST">
        @csrf
        @if($isEdit)
          @method('PUT')
        @endif
        @include('dashboard.roles.partials.role_form')
      </form>
    </div>
  </div>
@endsection

@push('scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.check_all').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
          const group = checkbox.closest('.check_group');
          if (!group) return;
          const checkboxes = group.querySelectorAll('input[type="checkbox"]');
          checkboxes.forEach(function (cb) {
            if (cb === checkbox) return;
            cb.checked = checkbox.checked;
          });
        });
      });

      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
      });
    });
  </script>
@endpush
