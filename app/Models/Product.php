<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'product_id';
    
    protected $fillable = ['name', 'description', 'price', 'category_id', 'image', 'stock'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
        /* $this->belongsTo(Model::class, 'foreign_key', 'owner_key') -> 
            'foreign_key' - 'category_id' in this case is the foreign key in the products table,
            in the other hand, 'owner_key' - 'category_id' is the primary key in the categories table.
        */
        }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    public function feedbacks(): HasMany
    {
        return $this->hasMany(Feedback::class, 'product_id');
    }

    public function manageProducts(): HasMany
    {
        return $this->hasMany(ManageProduct::class, 'product_id');
    }

    public function getRouteKeyName(): string
    {
        return 'product_id';
    }
}
