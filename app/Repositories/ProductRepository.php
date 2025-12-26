<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface ProductRepository
{
    public function getAllProducts(int $perPage = 10): LengthAwarePaginator;
    
    public function getProductById(int $id): ?Product;
    
    public function getProductsByCategoryId(int $categoryId, int $perPage): LengthAwarePaginator;
    
    public function createProduct(array $data): Product;
    
    public function updateProduct(int $id, array $data): ?Product;
    
    public function deleteProduct(int $id): bool;
    
    public function getProductsWithCategory(int $perPage): LengthAwarePaginator;
    
    public function searchProducts(?string $query = null, $categoryId = null, int $perPage = 10): LengthAwarePaginator;
    
    public function decrementStock(int $productId, int $quantity): bool;
    
    public function incrementStock(int $productId, int $quantity): bool;
    
    public function count(): int;
    
    public function getLowStockProducts(int $threshold = 10): Collection;
}
