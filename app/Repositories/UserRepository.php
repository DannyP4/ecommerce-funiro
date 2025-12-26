<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepository
{
    public function getAllUsers(int $perPage = 10): LengthAwarePaginator;
    
    public function getUserById(int $id): ?User;
    
    public function getUserByEmail(string $email): ?User;
    
    public function createUser(array $data): User;
    
    public function updateUser(int $id, array $data): ?User;
    
    public function deleteUser(int $id): bool;
    
    public function getUsersByRole(int $roleId, int $perPage): LengthAwarePaginator;
    
    public function getCustomers(int $perPage): LengthAwarePaginator;
    
    public function getAdmins(): Collection;
    
    public function activateUser(int $id): ?User;
    
    public function deactivateUser(int $id): ?User;
    
    public function count(): int;
    
    public function countByRole(int $roleId): int;
}
