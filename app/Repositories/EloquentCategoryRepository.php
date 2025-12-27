<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EloquentCategoryRepository implements CategoryRepository
{
    private const CATEGORY_IMAGE_DIR = 'images/categories';
    private const ALLOWED_IMAGE_MIME_TYPES = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/svg+xml'];
    private const MAX_IMAGE_SIZE = 2048 * 1024; // 2MB in bytes

    public function getAllCategories(): Collection
    {
        return Category::all();
    }

    public function getCategoryById(int $id): ?Category
    {
        return Category::find($id);
    }

    public function createCategory(array $data): Category
    {
        if (isset($data['image']) && $data['image']->isValid()) {
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        return Category::create($data);
    }

    public function updateCategory(int $id, array $data): ?Category
    {
        $category = $this->getCategoryById($id);

        if (!$category) {
            return null;
        }

        if (isset($data['image']) && $data['image']->isValid()) {
            $this->deleteImage($category);
            $data['image'] = $this->handleImageUpload($data['image']);
        }

        $category->update($data);

        return $category;
    }

    public function deleteCategory(int $id): bool
    {
        $category = $this->getCategoryById($id);

        if (!$category) {
            return false;
        }

        if ($this->hasProducts($id)) {
            return false;
        }

        $this->deleteImage($category);

        return $category->delete();
    }

    public function getCategoriesWithProductCount(int $perPage): LengthAwarePaginator
    {
        return Category::withCount('products')->orderBy('updated_at', 'desc')->paginate($perPage);
    }

    public function hasProducts(int $categoryId): bool
    {
        $category = $this->getCategoryById($categoryId);
        return $category ? $category->products()->count() > 0 : false;
    }

    public function deleteImage(Category $category): void
    {
        if ($category->image) {
            // Extract path from S3 URL if it's a full URL
            $path = $category->image;
            if (filter_var($path, FILTER_VALIDATE_URL)) {
                // Extract path from URL (remove domain part)
                $parsedUrl = parse_url($path);
                $path = ltrim($parsedUrl['path'], '/');
            }
            
            // Delete from S3
            if (Storage::disk('s3')->exists($path)) {
                Storage::disk('s3')->delete($path);
            }
        }
    }

    public function count(): int
    {
        return Category::count();
    }

    private function handleImageUpload($image): ?string
    {
        $mimeType = $image->getMimeType();
        $size = $image->getSize();

        if (!in_array($mimeType, self::ALLOWED_IMAGE_MIME_TYPES) || $size > self::MAX_IMAGE_SIZE) {
            throw new \InvalidArgumentException('Invalid image file type or size.');
        }

        $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
        $path = self::CATEGORY_IMAGE_DIR . '/' . $imageName;
        
        // Upload to S3
        Storage::disk('s3')->put($path, file_get_contents($image->getRealPath()), 'public');
        
        // Build and return S3 URL
        $bucket = config('filesystems.disks.s3.bucket');
        $region = config('filesystems.disks.s3.region');
        return "https://{$bucket}.s3.{$region}.amazonaws.com/{$path}";
    }
} 
