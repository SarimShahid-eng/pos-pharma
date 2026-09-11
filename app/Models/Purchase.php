<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Purchase extends Model
{
    use HasFactory;
    protected $appends = ['has_returns', 'purchase_date'];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_number',
        'reference_number',
        'supplier_id',
        'total_amount', // total_amount is the amount without discount
        'discount_amount',
        'paid_amount',
        'total_bonus_qty',
        'remaining_amount', //toal-paid_amount
        'date',
        'notes',
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
            'supplier_id' => 'integer',
            'total_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_bonus_qty' => 'decimal:2',
            'paid_amount' => 'decimal:2',
            'remaining_amount' => 'decimal:2',
            'date' => 'date',
        ];
    }
    public function scopeTodayPurchase(Builder $query): Builder
    {
        return $query->whereDate('date', now()->today());
    }
    protected function formattedDate(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => isset($attributes['date'])
                ? Carbon::parse($attributes['date'])->format('M d, Y')
                : null,
        );
    }
    public function purchaseDate(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->date->format('Y-m-d')
        );
    }
    public function purchaseReturn(): HasOne
    {
        return $this->hasOne(PurchaseReturn::class, 'purchase_id')->latestOfMany();
    }
    public function purchaseTotalAmount(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->purchaseItems->sum(function ($item) {
                return $item->qty * $item->unit_cost;
            })
        );
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseItems(): HasMany
    {
        return $this->hasMany(PurchaseItem::class);
    }
    public function getHasReturnsAttribute(): bool
    {
        // Uses loaded relation count if available, or runs quick exists query
        return $this->purchaseReturns()->exists();
    }
    public function purchaseReturns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function supplierPayment(): HasOne
    {
        return $this->hasOne(SupplierPayment::class);
    }
    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }
}
