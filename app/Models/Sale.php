<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    protected $appends = [
        'sale_date',
        'has_returns'
    ];
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'invoice_number',
        'total_amount',
        'discount_amount',
        'net_amount',
        'payment_method',
        'date',
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
            'total_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'net_amount' => 'decimal:2',
            'received_amount' => 'decimal:2',
            'date' => 'date',
        ];
    }
    public function scopeTodaySale(Builder $query): Builder
    {
        return $query->whereDate('date', now()->today());
    }

    public function saleReturn(): HasOne
    {
        return $this->hasOne(SaleReturn::class, 'sale_id')->latestOfMany();
    }
    public function saleDate(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->date->format('Y-m-d');
            }
        );
    }
    protected function formattedDate(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => isset($attributes['date'])
                ? Carbon::parse($attributes['date'])->format('M d, Y')
                : null,
        );
    }
    public function hasReturns(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->saleReturns()->exists()
        );
    }
    public function saleItems(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }

    public function saleReturns(): HasMany
    {
        return $this->hasMany(SaleReturn::class);
    }
}
