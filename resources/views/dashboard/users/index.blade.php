@extends('layouts.app')

@section('title', 'Vyapar - Users')
@section('description', 'Manage user roles and assignments.')
@section('page', 'users')

@section('content')
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h1 class="h3">Users</h1>
      <a href="{{ route('users.create') }}" class="btn btn-primary">+ Create User</a>
    </div>

    @if(session('success'))
      <div class="alert alert-success" role="alert">{{ session('success') }}</div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
    @endif

    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Roles</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @forelse($users as $user)
            <tr>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>
                @if($user->roles->count() > 0)
                  <span class="badge bg-info">{{ $user->roles->pluck('name')->join(', ') }}</span>
                @else
                  <span class="text-muted">No roles</span>
                @endif
              </td>
              <td>
                <div class="btn-group btn-group-sm" role="group">
                  <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary" title="Edit Roles">
                    <i class="fa-solid fa-user-gear"></i> Roles
                  </a>
                  @if(auth()->user()?->hasPermission('user.delete') && auth()->id() !== $user->id)
                    <form action="{{ route('users.destroy', $user) }}" method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this user?');">
                      @csrf
                      @method('DELETE')
                      <button type="submit" class="btn btn-outline-danger" title="Delete User">
                        <i class="fa-solid fa-trash"></i> Delete
                      </button>
                    </form>
                  @endif
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="4" class="text-center">No users found. <a href="{{ route('users.create') }}">Create one now</a></td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{ $users->links() }}
  </div>
@endsection
