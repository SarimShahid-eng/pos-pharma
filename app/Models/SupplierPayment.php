<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPayment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'supplier_id',
        'payment_method',
        'amount',
        'purchase_id',
        'type',
        'reference_no',
        'date',
        'notes',
    ];
    protected $appends = ['voucher_no'];
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
            'amount' => 'decimal:2',
            'purchase_id' => 'integer',
            'date' => 'date',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }
    protected function voucherNo(): Attribute
    {
        return Attribute::make(
            get: fn() => 'PV-' . str_pad($this->id, 5, '0', STR_PAD_LEFT)
        );
    }
}
