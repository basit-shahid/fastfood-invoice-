@extends('layouts.app')

@section('title', isset($user) ? 'Edit Staff Member' : 'Add Staff Member')

@push('styles')
<style>
    .form-modern-card {
        background: white;
        border-radius: 30px;
        border: none;
        box-shadow: 0 20px 50px rgba(0,0,0,0.05);
        padding: 40px;
        margin-top: 20px;
    }

    .form-label {
        font-weight: 700;
        color: #334155;
        margin-bottom: 10px;
        font-size: 0.9rem;
    }

    .form-control, .form-select {
        border-radius: 15px;
        padding: 12px 20px;
        border: 2px solid #f1f5f9;
        background: #f8fafc;
        transition: all 0.3s;
        font-weight: 500;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--accent-color);
        background: white;
        box-shadow: 0 0 0 4px rgba(255,193,7,0.1);
        outline: none;
    }

    .btn-save {
        background: #0f172a;
        color: white;
        padding: 15px 30px;
        border-radius: 18px;
        font-weight: 700;
        border: none;
        transition: all 0.3s;
        width: 100%;
        margin-top: 20px;
    }

    .btn-save:hover {
        background: #000;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .back-btn {
        color: #64748b;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 20px;
        transition: color 0.2s;
    }

    .back-btn:hover { color: #000; }

    .required-star { color: #ef4444; }

    .form-check-input:checked {
        background-color: var(--accent-color);
        border-color: var(--accent-color);
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <a href="{{ route('staff.index') }}" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back to Staff List
            </a>

            <div class="form-modern-card animate__animated animate__fadeIn">
                <div class="text-center mb-5">
                    <h2 class="fw-900 mb-2">{{ isset($user) ? 'Edit Staff' : 'Add New Staff' }}</h2>
                    <p class="text-muted">Fill in the details below to manage system access</p>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 mb-4">
                        <ul class="mb-0 fw-600 p-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ isset($user) ? route('staff.update', $user) : route('staff.store') }}" method="POST">
                    @csrf
                    @if(isset($user))
                        @method('PUT')
                    @endif

                    <div class="mb-4">
                        <label for="name" class="form-label">FULL NAME <span class="required-star">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name ?? '') }}" placeholder="Enter full name" required>
                    </div>

                    <div class="mb-4">
                        <label for="email" class="form-label">EMAIL ADDRESS <span class="required-star">*</span></label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email ?? '') }}" placeholder="example@domain.com" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label for="password" class="form-label">PASSWORD @if(!isset($user))<span class="required-star">*</span>@endif</label>
                            <input type="password" class="form-control" id="password" name="password" {{ !isset($user) ? 'required' : '' }} placeholder="••••••••">
                            @if(isset($user))
                                <div class="mt-2"><small class="text-muted fst-italic">Leave blank to keep current password.</small></div>
                            @endif
                        </div>
                        <div class="col-md-6 mb-4">
                            <label for="password_confirmation" class="form-label">CONFIRM PASSWORD @if(!isset($user))<span class="required-star">*</span>@endif</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" {{ !isset($user) ? 'required' : '' }} placeholder="••••••••">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="role" class="form-label">ASSIGN ROLE <span class="required-star">*</span></label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="" disabled {{ !isset($user) ? 'selected' : '' }}>-- Select System Role --</option>
                            <option value="cashier" {{ old('role', $user->role ?? '') == 'cashier' ? 'selected' : '' }}>Cashier (POS Only)</option>
                            <option value="manager" {{ old('role', $user->role ?? '') == 'manager' ? 'selected' : '' }}>Manager (Reports & Menu)</option>
                            <option value="owner" {{ old('role', $user->role ?? '') == 'owner' ? 'selected' : '' }}>Owner (Full Access)</option>
                        </select>
                    </div>

                    <div class="mb-4 p-3 rounded-4 border-1 border" style="background: var(--bg-color); border-color: var(--border-color) !important;">
                        <div class="form-check form-switch mb-0">
                            <input type="checkbox" class="form-check-input" id="is_active" name="is_active" value="1"
                                {{ old('is_active', isset($user) ? $user->is_active : true) ? 'checked' : '' }}>
                            <label class="form-check-label fw-700 ms-2" for="is_active" style="color: var(--text-color);">Enable Account Access</label>
                        </div>
                    </div>

                    <button type="submit" class="btn-save">
                        {{ isset($user) ? 'Update Account' : 'Create Staff Member' }} <i class="fas fa-check-circle ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
