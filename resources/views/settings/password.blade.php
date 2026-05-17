@extends('master_page.layout')

@section('title', 'Change Password')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Change Password</h2>
        <a href="{{ route('settings.index') }}" class="btn btn-secondary">Back to Settings</a>
    </div>

    @if (session('status') === 'password-updated')
        <div class="alert alert-success" role="alert">
            Your password has been updated.
        </div>
    @endif

    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">Update Password</h5>
        </div>
        <div class="card-body">
            <form method="post" action="{{ route('settings.password.update') }}" class="mt-3">
                @csrf
                @method('put') {{-- Use put method for updates --}}

                <div class="mb-3">
                    <label for="update_password_current_password" class="form-label">Current Password</label>
                    <input type="password" class="form-control" id="update_password_current_password" name="current_password" required>
                    @error('current_password', 'updatePassword')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="update_password_password" class="form-label">New Password</label>
                    <input type="password" class="form-control" id="update_password_password" name="password" required>
                    @error('password', 'updatePassword')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="update_password_password_confirmation" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="update_password_password_confirmation" name="password_confirmation" required>
                    @error('password_confirmation', 'updatePassword')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex align-items-center gap-4">
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection 