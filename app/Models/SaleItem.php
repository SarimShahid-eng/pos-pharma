<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItem extends Model
{
    use HasFactory;
    protected $appends = [
        'already_returned_qty',
        'max_returnable_qty',
    ];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'product_id',
        'qty',
        'rate',
        'discount',
        'after_discount_price', //its how much a product costs you after discount per qty,
        'amount', //amount after discount in rs
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
            'sale_id' => 'integer',
            'product_id' => 'integer',
            'qty' => 'decimal:2',
            'rate' => 'decimal:2',
            'discount' => 'decimal:2',
            'after_discount_price' => 'decimal:2',
            'amount' => 'decimal:2',
        ];
    }
    protected function alreadyReturnedQty(): Attribute
    {
        return Attribute::make(
            get: function () {
                return (float) $this->saleReturnItems()->sum('qty');
            }
        );
    }
    protected function maxReturnableQty(): Attribute
    {
        return Attribute::make(
            get: fn() => max(0, (float) $this->qty - (float) $this->already_returned_qty)

        );
    }
    public function saleReturnItems()
    {
        return $this->hasMany(SaleReturnItem::class, 'sale_item_id');
    }
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
