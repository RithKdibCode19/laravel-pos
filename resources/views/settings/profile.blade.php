@extends('master_page.layout')

@section('title', 'Profile Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Profile Settings</h2>
    </div>

    @if (session('status') === 'profile-updated')
        <div class="alert alert-success" role="alert">
            Your profile information has been updated.
        </div>
    @endif

    @if (session('status') === 'password-updated')
        <div class="alert alert-success" role="alert">
            Your password has been updated.
        </div>
    @endif

    <div class="row">
        {{-- Display Profile Information --}}
        <div class="col-md-6" id="displayProfileSection">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Profile Information</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <p class="form-control-static">{{ Auth::user()->name }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <p class="form-control-static">{{ Auth::user()->email }}</p>
                    </div>
                    {{-- Add email verification status display if applicable --}}

                    <button type="button" class="btn btn-primary" id="editProfileButton">Edit Profile</button>
                </div>
            </div>
        </div>

        {{-- Update Profile Information Form (Initially hidden) --}}
        <div class="col-md-6" id="editProfileSection" style="display: none;">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Update Profile Information</h5>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('settings.profile.update') }}" class="mt-3">
                        @csrf
                        @method('patch') {{-- Use patch method for updates --}}

                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required autofocus>
                            @error('name')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email')
                                <div class="text-danger mt-2">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Add email verification status check if applicable --}}

                        <div class="d-flex align-items-center gap-4">
                            <button type="submit" class="btn btn-primary">Save</button>
                            <button type="button" class="btn btn-secondary" id="cancelEditButton">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    
    </div>
</div>
@push('scripts')
<script>
    document.getElementById('editProfileButton').addEventListener('click', function() {
        document.getElementById('displayProfileSection').style.display = 'none';
        document.getElementById('editProfileSection').style.display = 'block';
    });

    document.getElementById('cancelEditButton').addEventListener('click', function() {
        document.getElementById('editProfileSection').style.display = 'none';
        document.getElementById('displayProfileSection').style.display = 'block';
    });
</script>
@endpush
@endsection 