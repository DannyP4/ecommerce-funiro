<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $primaryKey = 'category_id';

    protected $fillable = ['name', 'image'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id', 'category_id');
    }

    public function manageCategories(): HasMany
    {
        return $this->hasMany(ManageCategory::class, 'category_id');
    }
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    // This method is used to define how the model should be referenced in routes
    // It allows you to use the 'category_id' instead of the default 'id'
    public function getRouteKeyName()
    {
        return 'category_id';
    }
}
