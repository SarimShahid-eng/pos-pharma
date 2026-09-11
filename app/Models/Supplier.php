<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'phone_number',
        'is_active',
        'opening_balance',
        'date'
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date'=>'date',
            'id' => 'integer',
            'opening_balance' => 'decimal:2',
            'current_balance' => 'decimal:2',
        ];
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function supplierPayments(): HasMany
    {
        return $this->hasMany(SupplierPayment::class);
    }

    public function purchaseReturns(): HasMany
    {
        return $this->hasMany(PurchaseReturn::class);
    }
    protected function currentBalance(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->calculateBalance()
        );
    }
    public function calculateBalance(): float
    {
        $openingBalance = (float) $this->opening_balance;
        $purchases = (float) $this->purchases()->sum('total_amount');
        $returns = (float) $this->purchaseReturns()->sum('total_amount');
        $debitPayments = (float) $this->supplierPayments()
            ->where('type', 'debit')
            ->sum('amount');
        $creditPayments = (float) $this->supplierPayments()
            ->where('type', 'credit')
            ->sum('amount');
        return (float) ($openingBalance + $purchases + $creditPayments) - ($returns + $debitPayments);
    }
}
