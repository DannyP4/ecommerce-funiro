<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class EloquentProductRepository implements ProductRepository
{
    private const PRODUCT_IMAGE_DIR = 'images/products';
    private const ALLOWED_IMAGE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
    private const MAX_IMAGE_SIZE = 2048 * 1024;

    public function getAllProducts(int $perPage = 10): LengthAwarePaginator
    {
        return Product::with('category')->orderBy('updated_at', 'desc')->paginate($perPage);
    }

    public function getProductById(int $id): ?Product
    {
        return Product::with('category')->find($id);
    }

    public function getProductsByCategoryId(int $categoryId, int $perPage): LengthAwarePaginator
    {
        return Product::where('category_id', $categoryId)
            ->with('category')
            ->paginate($perPage);
    }

    public function createProduct(array $data): Product
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        return Product::create($data);
    }

    public function updateProduct(int $id, array $data): ?Product
    {
        $product = $this->getProductById($id);

        if (!$product) {
            return null;
        }

        if (isset($data['image']) && $data['image']->isValid()) {
            $this->deleteImage($product);
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        $product->update($data);

        return $product->fresh();
    }

    public function deleteProduct(int $id): bool
    {
        $product = $this->getProductById($id);

        if (!$product) {
            return false;
        }

        $this->deleteImage($product);

        return $product->delete();
    }

    public function getProductsWithCategory(int $perPage): LengthAwarePaginator
    {
        return Product::with('category')->paginate($perPage);
    }

    public function searchProducts(?string $query = null, $categoryId = null, int $perPage = 10): LengthAwarePaginator
    {
        $productsQuery = Product::with('category');
        
        // search filter
        if (!empty($query)) {
            $productsQuery->where('name', 'like', "%{$query}%");
        }
        
        // category filter
        if (!empty($categoryId) && $categoryId !== 'all') {
            $productsQuery->where('category_id', $categoryId);
        }
        
        return $productsQuery->paginate($perPage);
    }

    public function decrementStock(int $productId, int $quantity): bool
    {
        $product = $this->getProductById($productId);

        if (!$product || $product->stock < $quantity) {
            return false;
        }

        $product->decrement('stock', $quantity);

        return true;
    }

    public function incrementStock(int $productId, int $quantity): bool
    {
        $product = $this->getProductById($productId);

        if (!$product) {
            return false;
        }

        $product->increment('stock', $quantity);

        return true;
    }

    public function count(): int
    {
        return Product::count();
    }

    public function getLowStockProducts(int $threshold = 10): Collection
    {
        return Product::where('stock', '<=', $threshold)
            ->where('stock', '>', 0)
            ->with('category')
            ->get();
    }

    private function handleImageUpload($image): string
    {
        $imageDirectory = public_path(self::PRODUCT_IMAGE_DIR);

        if (!File::exists($imageDirectory)) {
            File::makeDirectory($imageDirectory, 0755, true);
        }

        $mimeType = $image->getMimeType();
        if (!in_array($mimeType, self::ALLOWED_IMAGE_MIME_TYPES)) {
            throw new \InvalidArgumentException('Invalid image type.');
        }

        if ($image->getSize() > self::MAX_IMAGE_SIZE) {
            throw new \InvalidArgumentException('Image size exceeds maximum allowed size.');
        }

        $extension = $image->getClientOriginalExtension();
        $filename = Str::uuid() . '.' . $extension;
        $image->move($imageDirectory, $filename);

        return '/' . self::PRODUCT_IMAGE_DIR . '/' . $filename;
    }

    private function deleteImage(Product $product): void
    {
        if ($product->image && File::exists(public_path($product->image))) {
            File::delete(public_path($product->image));
        }
    }
}
