@extends('master_page.layout')

@section('title', 'Settings')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Settings</h2>
    </div>

    <div class="row">
        {{-- Account Settings Card --}}
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Account Settings</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Manage your profile information and password.</p>
                    <a href="{{ route('settings.profile') }}" class="btn btn-outline-primary me-2">Edit Profile</a>
                    <a href="{{ route('settings.password') }}" class="btn btn-outline-secondary">Change Password</a>
                </div>
            </div>
        </div>

        {{-- User Management Card --}}
        <div class="col-md-6">
            <div class="card mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">User Management</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">Manage user accounts who can access the system.</p>
                    <a href="{{ route('users.index') }}" class="btn btn-primary">Manage Users</a>
                </div>
            </div>
        </div>

        {{-- Category Management Card --}}
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Category Management</h5>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoryModal">
                        <i class="bx bx-plus"></i> Add Category
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="categoriesTable">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Description</th>
                                    <th>Products Count</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($categories as $category)
                                <tr>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->description ?? 'N/A' }}</td>
                                    <td>{{ $category->products_count ?? 0 }}</td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-warning edit-category" 
                                                data-id="{{ $category->id }}"
                                                data-name="{{ $category->name }}"
                                                data-description="{{ $category->description }}">
                                            <i class="bx bx-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger delete-category" 
                                                data-id="{{ $category->id }}"
                                                data-name="{{ $category->name }}">
                                            <i class="bx bx-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Category Modal --}}
<div class="modal fade" id="categoryModal" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="categoryModalLabel">Add Category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="categoryForm" action="{{ route('categories.store') }}" method="POST">
                @csrf
                <input type="hidden" name="_method" id="method" value="POST">
                <input type="hidden" name="category_id" id="category_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Category Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Category</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete the category "<span id="deleteCategoryName"></span>"?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {

    console.log('Settings page script loaded.');

    // Check if Bootstrap is available
    if (typeof bootstrap === 'undefined') {
        console.error('Bootstrap JavaScript not loaded.');
        // Potentially alert the user or provide a fallback
        return; // Stop execution if Bootstrap is not available
    }

    const categoryModalElement = document.getElementById('categoryModal');
    const deleteModalElement = document.getElementById('deleteModal');

    // Ensure modal elements exist before initializing
    if (!categoryModalElement || !deleteModalElement) {
        console.error('Modal elements not found.');
        return; // Stop execution if modal elements are missing
    }

    const categoryModal = new bootstrap.Modal(categoryModalElement);
    const deleteModal = new bootstrap.Modal(deleteModalElement);

    const categoryForm = document.getElementById('categoryForm');
    const deleteForm = document.getElementById('deleteForm');

    // Ensure forms exist before adding listeners
     if (!categoryForm || !deleteForm) {
        console.error('Form elements not found.');
        return; // Stop execution if form elements are missing
    }

    // Edit category
    document.querySelectorAll('.edit-category').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Edit button clicked');
            const id = this.dataset.id;
            const name = this.dataset.name;
            const description = this.dataset.description;

            document.getElementById('categoryModalLabel').textContent = 'Edit Category';
            document.getElementById('category_id').value = id;
            document.getElementById('name').value = name;
            document.getElementById('description').value = description;
            document.getElementById('method').value = 'PUT';
            categoryForm.action = '{{ route('categories.update', ':id') }}'.replace(':id', id);

            categoryModal.show();
        });
    });

    // Delete category
    document.querySelectorAll('.delete-category').forEach(button => {
        button.addEventListener('click', function() {
            console.log('Delete button clicked');
            const id = this.dataset.id;
            const name = this.dataset.name;

            document.getElementById('deleteCategoryName').textContent = name;
            deleteForm.action = '{{ route('categories.destroy', ':id') }}'.replace(':id', id);

            deleteModal.show();
        });
    });

    // Reset form when modal is closed
    document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function() {
        console.log('Category modal hidden');
        document.getElementById('categoryModalLabel').textContent = 'Add Category';
        document.getElementById('category_id').value = '';
        document.getElementById('name').value = '';
        document.getElementById('description').value = '';
        document.getElementById('method').value = 'POST';
        categoryForm.action = '{{ route('categories.store') }}';
    });

    // Form submission
    categoryForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Category form submitted');
        
        fetch(this.action, {
            method: document.getElementById('method').value,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                name: document.getElementById('name').value,
                description: document.getElementById('description').value
            })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Category form response:', data);
            if (data.success) {
                categoryModal.hide();
                window.location.reload();
            } else {
                alert(data.message || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error saving category:', error);
            alert('An error occurred while saving the category');
        });
    });

    // Delete form submission
    deleteForm.addEventListener('submit', function(e) {
        e.preventDefault();
        console.log('Delete form submitted');
        
        fetch(this.action, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
             console.log('Delete form response:', data);
            if (data.success) {
                deleteModal.hide();
                window.location.reload();
            } else {
                alert(data.message || 'An error occurred');
            }
        })
        .catch(error => {
            console.error('Error deleting category:', error);
            alert('An error occurred while deleting the category');
        });
    });
});
</script>
@endpush 