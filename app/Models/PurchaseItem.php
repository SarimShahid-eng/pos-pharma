<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
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
        'purchase_id',
        'product_id',
        'qty',
        'unit_cost',
        'bonus_qty',
        'discount_amount',
        'subtotal_amount' //amount after discount
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
            'purchase_id' => 'integer',
            'product_id' => 'integer',
            'qty' => 'decimal:2',
            'bonus_qty' => 'decimal:2',
            'discount_amount',
            'subtotal_amount'
        ];
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
    protected function alreadyReturnedQty(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Sum return_quantity from linked purchaseReturnItems
                return (float) $this->purchaseReturnItems()->sum('qty');
            }
        );
    }
    protected function maxReturnableQty(): Attribute
    {
        return Attribute::make(
            get: fn() => max(0, (float) $this->qty - (float) $this->already_returned_qty)

        );
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function purchaseReturnItems()
    {
        return $this->hasMany(PurchaseReturnItem::class, 'purchase_item_id');
    }
}
