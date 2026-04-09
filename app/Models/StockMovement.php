<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'type', 'quantity', 'description'];

    protected static function booted(): void
    {
        static::created(function (StockMovement $movement) {
            $product = $movement->product;
            if ($movement->type === 'in') {
                $product->increment('stock_quantity', $movement->quantity);
            } elseif ($movement->type === 'out') {
                $product->decrement('stock_quantity', $movement->quantity);
            }
        });

        static::deleted(function (StockMovement $movement) {
            $product = $movement->product;
            if ($movement->type === 'in') {
                $product->decrement('stock_quantity', $movement->quantity);
            } elseif ($movement->type === 'out') {
                $product->increment('stock_quantity', $movement->quantity);
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}