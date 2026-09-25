<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'shipping' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }

    public function getFormattedAddressAttribute(): string
    {
        $lines = array_filter([
            $this->address,
            $this->address_2,
            trim("{$this->postcode} {$this->city}"),
            $this->country,
        ]);

        return implode(', ', $lines);
    }

    /** Betrag im Schweizer Format, z. B. "CHF 1'234.56". */
    public function money($value): string
    {
        return swiss_money((float) $value, $this->currency ?: 'CHF');
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'pending' => 'In Bearbeitung',
            'processing' => 'Wird bearbeitet',
            'shipped' => 'Versendet',
            'completed' => 'Abgeschlossen',
            'cancelled' => 'Storniert',
            default => ucfirst((string) $this->status),
        };
    }
}
