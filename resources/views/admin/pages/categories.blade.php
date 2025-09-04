@extends('admin.layouts.app')

@section('content')
<div class="content-section active">
    <div class="table-container">
        <div class="table-header">
            <h2 class="table-title">{{ __('Category Management') }}</h2>
            <button class="btn btn-primary" data-modal="categoryModal" type="button">
                <i class="fas fa-plus"></i> {{ __('Add Category') }}
            </button>
        </div>
        <div style="padding: 20px;">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Image') }}</th>
                        <th>{{ __('Category Name') }}</th>
                        <th>{{ __('Products Count') }}</th>
                        <th>{{ __('Created Date') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr data-id="{{ $category->category_id }}">
                        <td>#{{ $category->category_id }}</td>
                        <td>
							@php
								$imgSrc = $category->image ? asset($category->image) : asset('images/default-category.svg');
							@endphp
							<img src="{{ $imgSrc }}" alt="{{ $category->name }}" style="width: 48px; height: 48px; object-fit: cover; border-radius: 6px; border: 1px solid #eee;">
						</td>
                        <td>{{ __($category->name) }}</td>
                        <td>{{ $category->products_count }} {{ __('products') }}</td>
                        <td>{{ $category->created_at ? $category->created_at->format('d/m/Y') : __('N/A') }}</td>
                        <td><span class="status-badge category-status-badge status-active">{{ __('Active') }}</span></td>
                        <td>
                            <button class="btn btn-warning btn-sm edit-btn"
                                    data-id="{{ $category->category_id }}"
                                    data-name="{{ $category->name }}"
                                    data-image="{{ $category->image }}"
                                    {{-- data-modal="editCategoryModal"  --}}
                                    >
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm delete-btn" data-id="{{ $category->category_id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" style="text-align: center;">{{ __('No categories found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            <div style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $categories->links('pagination.pagination') }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Mở modal edit
    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', () => {
            const id = button.dataset.id;
            const name = button.dataset.name;
            const image = button.dataset.image;

            document.getElementById('edit_category_id').value = id;
            document.getElementById('edit_category_name').value = name;
            document.getElementById('editCategoryForm').action = `/admin/categories/${id}`;
            
            // display current image
            const editImageEl = document.getElementById('edit_category_image_preview');
            const editNoImageEl = document.getElementById('edit_category_no_image');
            const fileInput = document.querySelector('#editCategoryModal input[name="image"]');
            
            // original image source (restore if no new image is selected)
            let originalImageSrc = '';
            
            if (image && image !== '' && image !== 'null') {
                originalImageSrc = '/' + image;
                editImageEl.src = originalImageSrc;
                editImageEl.style.display = 'block';
                editNoImageEl.style.display = 'none';
            } else {
                editImageEl.style.display = 'none';
                editNoImageEl.style.display = 'block';
            }

            // reset file input when opening modal
            fileInput.value = '';
            
            fileInput.onchange = function(event) {
                const file = event.target.files[0];
                if (file) {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            editImageEl.src = e.target.result;
                            editImageEl.style.display = 'block';
                            editNoImageEl.style.display = 'none';
                        };
                        reader.readAsDataURL(file);
                    } else {
                        event.target.value = '';
                    }
                } else {
                    if (originalImageSrc) {
                        editImageEl.src = originalImageSrc;
                        editImageEl.style.display = 'block';
                        editNoImageEl.style.display = 'none';
                    } else {
                        editImageEl.style.display = 'none';
                        editNoImageEl.style.display = 'block';
                    }
                }
            };
            
            adminPanel.openModal('editCategoryModal');
        });
    });

    // Xử lý xoá category
    let categoryIdToDelete = null;
    document.querySelectorAll('.delete-btn').forEach(button => {
        button.addEventListener('click', () => {
            categoryIdToDelete = button.dataset.id;
            adminPanel.openModal('deleteCategoryModal');
        });
    });
    // Handle delete confirmation modal actions
    document.getElementById('cancelDeleteCategoryBtn').addEventListener('click', () => {
        adminPanel.closeModal('deleteCategoryModal');
        categoryIdToDelete = null;
    });
    document.getElementById('confirmDeleteCategoryBtn').addEventListener('click', () => {
        if (!categoryIdToDelete) return;
        fetch(`/admin/categories/${categoryIdToDelete}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        }).then(async response => {
            if (response.ok) {
                window.location.reload();
            } else {
                let errorMsg = 'Failed to delete category.';
                try {
                    const data = await response.json();
                    if (data && data.message) {
                        errorMsg = data.message;
                    }
                } catch (e) {
                    // Ignore JSON parse errors, use default message
                }
                alert(errorMsg);
            }
        });
        adminPanel.closeModal('deleteCategoryModal');
        categoryIdToDelete = null;
    });
</script>

@endsection
