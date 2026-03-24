@extends('layouts.app')

@section('title', 'Staff Management')

@push('styles')
<style>
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .modern-card {
        background: white;
        border-radius: 24px;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.04);
        overflow: hidden;
    }

    .table-modern thead th {
        background: #f8fafc;
        padding: 20px;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 1px;
        color: #64748b;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-modern tbody td {
        padding: 20px;
        border-bottom: 1px solid #f8fafc;
        vertical-align: middle;
    }

    .user-avatar {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        color: #334155;
        margin-right: 15px;
    }

    .badge-modern {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.75rem;
    }

    .btn-action {
        width: 35px;
        height: 35px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        color: #64748b;
        margin-left: 5px;
    }

    .btn-action:hover {
        background: var(--accent-color);
        border-color: var(--accent-color);
        color: #000;
        transform: translateY(-2px);
    }

    .btn-action.delete:hover {
        background: #ef4444;
        border-color: #ef4444;
        color: #fff;
    }

    .add-staff-btn {
        background: #0f172a;
        color: white;
        padding: 12px 25px;
        border-radius: 15px;
        font-weight: 700;
        transition: all 0.3s;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }

    .add-staff-btn:hover {
        background: #000;
        transform: translateY(-2px);
        color: white;
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="page-header">
        <div>
            <h2 class="fw-900 mb-1">Staff Members</h2>
            <p class="text-muted mb-0">Manage roles and access for your team</p>
        </div>
        <a href="{{ route('staff.create') }}" class="add-staff-btn">
            <i class="fas fa-plus"></i> Add New Staff
        </a>
    </div>

    <div class="modern-card animate__animated animate__fadeIn">
        <div class="table-responsive">
            <table class="table table-modern mb-0">
                <thead>
                    <tr>
                        <th>Staff Member</th>
                        <th>Role</th>
                        <th>Login Access</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="user-avatar text-uppercase">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-800 text-dark">{{ $user->name }}</div>
                                    <div class="small text-muted">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge-modern bg-{{ $user->role === 'owner' ? 'dark text-white' : ($user->role === 'manager' ? 'primary text-white' : 'light text-dark') }}">
                                {{ strtoupper($user->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge-modern {{ $user->is_active ? 'bg-success text-white' : 'bg-danger text-white' }}">
                                {{ $user->is_active ? 'ACTIVE' : 'INACTIVE' }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('staff.edit', $user) }}" class="btn-action" title="Edit Staff">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($user->id !== auth()->id())
                            <form action="{{ route('staff.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove this staff member?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action delete" title="Delete Staff">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5">
                            <img src="https://cdn-icons-png.flaticon.com/512/11329/11329061.png" style="width: 80px; opacity: 0.2;" class="mb-3">
                            <p class="text-muted">No staff members found.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection