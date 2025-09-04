<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Role extends Model
{
    use HasFactory;

    protected $primaryKey = 'role_id';

    protected $fillable = ['name'];

    public const ADMIN = 1;
    public const CUSTOMER = 2;
    public const SUPER_ADMIN = 'admin1@gmail.com';

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
