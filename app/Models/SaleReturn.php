<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SaleReturn extends Model
{
    protected $appends = [
        'return_date'
    ];
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'sale_id',
        'total_amount',
        'refunded_amount',
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
            'sale_id' => 'integer',
            'total_amount' => 'decimal:2',
            'refunded_amount' => 'decimal:2',
            'date' => 'date',
        ];
    }
    protected function formattedDate(): Attribute
    {
        return Attribute::make(
            get: fn(mixed $value, array $attributes) => isset($attributes['date'])
                ? Carbon::parse($attributes['date'])->format('M d, Y')
                : null,
        );
    }

    public function returnDate(): Attribute
    {
        return Attribute::make(
            get: function () {
                return $this->date->format('Y-m-d');
            }
        );
    }
    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function saleReturnItems(): HasMany
    {
        return $this->hasMany(SaleReturnItem::class);
    }
}
