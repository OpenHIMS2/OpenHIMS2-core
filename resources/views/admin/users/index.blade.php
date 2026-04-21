@extends('layouts.admin')
@section('title', 'User Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0"><i class="bi bi-people-fill me-2 text-primary"></i>User Management</h4>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
        <i class="bi bi-person-plus-fill me-1"></i>New User
    </a>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        @if($users->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="bi bi-people" style="font-size:3rem; opacity:.3;"></i>
                <p class="mt-3 mb-0">No users yet.</p>
            </div>
        @else
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Institution</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="ps-4 fw-medium">
                            <i class="bi bi-person-circle text-muted me-1"></i>{{ $user->name }}
                        </td>
                        <td class="text-muted small">{{ $user->email }}</td>
                        <td>
                            @if($user->isAdmin())
                                <span class="badge bg-danger">Admin</span>
                            @else
                                <span class="badge bg-success">Clinical</span>
                            @endif
                        </td>
                        <td class="small text-muted">{{ $user->institution?->name ?? '—' }}</td>
                        <td class="text-end pe-4">
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-outline-secondary btn-sm py-0 me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="button"
                                        class="btn btn-outline-danger btn-sm py-0"
                                        data-confirm-body="Delete user &quot;{{ $user->name }}&quot;?"
                                        onclick="confirmDialog({title:'Delete User', body:this.dataset.confirmBody, confirmText:'Delete', confirmClass:'btn-danger', icon:'bi-trash3-fill text-danger'}, () => this.closest('form').submit())">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</div>
@endsection
