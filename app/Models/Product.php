<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'barcode',
        'label_title',
        'name',
        'unit',
        'cost_price',
        'sale_price',
        'discount',
        'profit',
        'stock_qty',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'id' => 'integer',
            'cost_price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'discount' => 'decimal:2',
            'profit' => 'decimal:2',
            'stock_qty' => 'decimal:2',
        ];
    }

    public function productHistories(): HasMany
    {
        return $this->hasMany(ProductHistory::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseReturnItems(): HasMany
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }

    public function saleReturnItems(): HasMany
    {
        return $this->hasMany(SaleReturnItem::class);
    }

    protected function stockQuantity(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes) {
                $openingStock = (float) ($attributes['stock_qty'] ?? 0);

                // Check if eager-loaded via withSum()
                if (array_key_exists('purchased_qty', $attributes)) {
                    $purchased    = (float) ($attributes['purchased_qty'] ?? 0)
                        + (float) ($attributes['bonus_qty'] ?? 0);
                    $pReturned    = (float) ($attributes['purchase_returned_qty'] ?? 0);
                    $sold         = (float) ($attributes['sold_qty'] ?? 0);
                    $sReturned    = (float) ($attributes['sale_returned_qty'] ?? 0);
                } else {
                    // Fallback to direct queries for single model instances
                    $purchased    = (float) $this->purchaseItems()->sum('qty')
                        + (float) $this->purchaseItems()->sum('bonus_qty');
                    $pReturned    = (float) $this->purchaseReturnItems()->sum('qty');
                    $sold         = (float) $this->saleItems()->sum('qty');
                    $sReturned    = (float) $this->saleReturnItems()->sum('qty');
                }

                return $openingStock + $purchased - $pReturned - ($sold - $sReturned);
            }
        );
    }
    public function isLowStock(float $threshold = 10): bool
    {
        return $this->stock_quantity <= $threshold;
    }
}
