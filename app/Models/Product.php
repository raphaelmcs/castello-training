<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'cost_price', 'price', 'stock_quantity'];

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}