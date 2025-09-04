@extends('admin.layouts.app')

@section('content')
<div class="content-section active">
    <div class="table-container">
        <div class="table-header">
            <h2 class="table-title">{{ __('Products Management') }}</h2>
            <button class="btn btn-primary" data-modal="productModal" type="button">
                <i class="fas fa-plus"></i> {{ __('Add Product') }}
            </button>
        </div>
        <div style="padding: 20px;">
            <form method="GET" action="/admin/products/search" class="search-bar">
                <input type="text" name="query" id="searchInput" class="search-input" 
                       placeholder="{{ __('Search products...') }}" 
                       value="{{ request('query') }}">
                <select name="category_id" id="categoryFilter" class="form-control" style="width: auto;">
                    <option value="all">{{ __('All Categories') }}</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->category_id }}" 
                                {{ request('category_id') == $category->category_id ? 'selected' : '' }}>
                            {{ __($category->name) }}
                        </option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-search"></i> {{ __('Search') }}
                </button>
            </form>
            
            <table class="data-table">
                <thead>
                    <tr>
                        <th>{{ __('ID') }}</th>
                        <th>{{ __('Image') }}</th>
                        <th>{{ __('Product Name') }}</th>
                        <th>{{ __('Category') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Stock') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                    <tr>
                        <td>#{{ $product->product_id }}</td>
                        <td>
                            <div style="width: 50px; height: 50px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                @if($product->image)
                                    <img src="{{ asset($product->image) }}" alt="{{ $product->name }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                @else
                                    <i class="fas fa-image" style="color: #ccc;"></i>
                                @endif
                            </div>
                        </td>
                        <td>{{ $product->name }}</td>
                        <td>{{ __($product->category->name ?? 'N/A') }}</td>
                        <td>{{ number_format($product->price) }} VNĐ</td>
                        <td>{{ $product->stock ?? 0 }}</td>
                        <td>
                            @if(($product->stock ?? 0) > 0)
                                <button class="btn btn-success btn-sm" disabled>
                                    <i class="fas fa-check"></i> {{ __('Available') }}
                                </button>
                            @else
                                <button class="btn btn-danger btn-sm" disabled>
                                    <i class="fas fa-times"></i> {{ __('Out of Stock') }}
                                </button>
                            @endif
                        </td>
                        <td>
                            <button class="btn btn-secondary btn-sm" 
                                    data-product-id="{{ $product->product_id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-category-name="{{ $product->category->name ?? 'N/A' }}"
                                    data-product-price="{{ $product->price }}"
                                    data-product-stock="{{ $product->stock }}"
                                    data-product-description="{{ $product->description ?? '' }}"
                                    data-product-image="{{ $product->image ?? '' }}"
                                    onclick="viewProductData(this)">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn btn-warning btn-sm"
                                    data-product-id="{{ $product->product_id }}"
                                    data-product-name="{{ $product->name }}"
                                    data-category-id="{{ $product->category_id }}"
                                    data-product-price="{{ $product->price }}"
                                    data-product-stock="{{ $product->stock }}"
                                    data-product-description="{{ $product->description ?? '' }}"
                                    data-product-image="{{ $product->image ?? '' }}"
                                    onclick="editProductData(this)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" 
                                    data-product-id="{{ $product->product_id }}"
                                    onclick="deleteProductData(this)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" style="text-align: center;">{{ __('No products found') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            
            <!-- Pagination -->
            <div style="margin-top: 20px; display: flex; justify-content: center;">
                {{ $products->links('pagination.pagination') }}
            </div>
        </div>
    </div>
</div>
@endsection

<script>
// Auto-submit form when category changes
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('categoryFilter').addEventListener('change', function() {
        this.closest('form').submit();
    });
    
    // Enable search on Enter key
    document.getElementById('searchInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            this.closest('form').submit();
        }
    });
});

// Product management functions
function viewProductData(button) {
    const data = button.dataset;
    
    document.getElementById('view_product_id').value = '#' + data.productId;
    document.getElementById('view_product_name').value = data.productName;
    document.getElementById('view_product_category').value = data.categoryName;
    document.getElementById('view_product_price').value = new Intl.NumberFormat('vi-VN').format(data.productPrice) + ' VNĐ';
    document.getElementById('view_product_stock').value = data.productStock;
    document.getElementById('view_product_status').value = data.productStock > 0 ? 'Available' : 'Out of Stock';
    document.getElementById('view_product_description').value = data.productDescription;
    
    const imageEl = document.getElementById('view_product_image');
    const noImageEl = document.getElementById('view_no_image');
    
    if (data.productImage) {
        imageEl.src = '/' + data.productImage;
        imageEl.style.display = 'block';
        noImageEl.style.display = 'none';
    } else {
        imageEl.style.display = 'none';
        noImageEl.style.display = 'block';
    }
    
    adminPanel.openModal('viewProductModal');
}

function editProductData(button) {
    const data = button.dataset;
    
    document.getElementById('edit_product_id').value = data.productId;
    document.getElementById('edit_product_name').value = data.productName;
    document.getElementById('edit_product_category').value = data.categoryId;
    document.getElementById('edit_product_price').value = data.productPrice;
    document.getElementById('edit_product_stock').value = data.productStock;
    document.getElementById('edit_product_description').value = data.productDescription;
    document.getElementById('editProductForm').action = `/admin/products/${data.productId}`;
    
    const editImageEl = document.getElementById('edit_product_image');
    const editNoImageEl = document.getElementById('edit_no_image');
    const fileInput = document.querySelector('#editProductModal input[name="image"]');
    
    // original image source (restore if no new image is selected)
    let originalImageSrc = '';
    
    if (data.productImage && data.productImage !== '' && data.productImage !== 'null') {
        originalImageSrc = '/' + data.productImage;
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
    
    adminPanel.openModal('editProductModal');
}

function deleteProductData(button) {
    window.productIdToDelete = button.dataset.productId;
    adminPanel.openModal('deleteProductModal');
}

document.addEventListener('DOMContentLoaded', function() {
    const cancelBtn = document.getElementById('cancelDeleteProductBtn');
    const confirmBtn = document.getElementById('confirmDeleteProductBtn');
    
    cancelBtn?.addEventListener('click', () => {
        adminPanel.closeModal('deleteProductModal');
        window.productIdToDelete = null;
    });

    confirmBtn?.addEventListener('click', async () => {
        if (!window.productIdToDelete) return;
        
        try {
            const response = await fetch(`/admin/products/${window.productIdToDelete}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            });
            
            if (response.ok) {
                window.location.reload();
            } else {
                const data = await response.json().catch(() => ({}));
                alert(data.message || 'Failed to delete product.');
            }
        } catch (error) {
            console.error('Error:', error);
            alert(data.message || window.translations.failedToDeleteProduct);
        }
        
        adminPanel.closeModal('deleteProductModal');
        window.productIdToDelete = null;
    });
});
</script>
