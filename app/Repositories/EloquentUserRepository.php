<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class EloquentUserRepository implements UserRepository
{
    public function getAllUsers(int $perPage = 10): LengthAwarePaginator
    {
        return User::with('role')->orderBy('updated_at', 'desc')->paginate($perPage);
    }

    public function getUserById(int $id): ?User
    {
        return User::with('role')->find($id);
    }

    public function getUserByEmail(string $email): ?User
    {
        return User::where('email', $email)->first();
    }

    public function createUser(array $data): User
    {
        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }

        return User::create($data);
    }

    public function updateUser(int $id, array $data): ?User
    {
        $user = $this->getUserById($id);

        if (!$user) {
            return null;
        }

        if (isset($data['password']) && !empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return $user->fresh();
    }

    public function deleteUser(int $id): bool
    {
        $user = $this->getUserById($id);

        if (!$user) {
            return false;
        }

        return $user->delete();
    }

    public function getUsersByRole(int $roleId, int $perPage): LengthAwarePaginator
    {
        return User::where('role_id', $roleId)
            ->with('role')
            ->paginate($perPage);
    }

    public function getCustomers(int $perPage): LengthAwarePaginator
    {
        return User::whereHas('role', function ($query) {
            $query->where('role_name', 'customer');
        })
        ->with('role')
        ->paginate($perPage);
    }

    public function getAdmins(): Collection
    {
        return User::whereHas('role', function ($query) {
            $query->where('role_name', 'admin');
        })
        ->with('role')
        ->get();
    }

    public function activateUser(int $id): ?User
    {
        $user = $this->getUserById($id);

        if (!$user) {
            return null;
        }

        $user->update(['is_activate' => true]);

        return $user->fresh();
    }

    public function deactivateUser(int $id): ?User
    {
        $user = $this->getUserById($id);

        if (!$user) {
            return null;
        }

        $user->update(['is_activate' => false]);

        return $user->fresh();
    }

    public function count(): int
    {
        return User::count();
    }

    public function countByRole(int $roleId): int
    {
        return User::where('role_id', $roleId)->count();
    }
}
