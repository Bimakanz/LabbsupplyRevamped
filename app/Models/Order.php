<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'number',
        'user_id',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_url',
        'payment_payload',
        'subtotal',
        'shipping',
        'total',
        'notes',
        'paid_at',
        'billing_first_name',
        'billing_last_name',
        'billing_company',
        'billing_country',
        'billing_address_1',
        'billing_address_2',
        'billing_city',
        'billing_state',
        'billing_postcode',
        'billing_phone',
        'billing_email',
    ];

    protected function casts(): array
    {
        return [
            'payment_payload' => 'array',
            'subtotal' => 'decimal:2',
            'shipping' => 'decimal:2',
            'total' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getRouteKeyName(): string
    {
        return 'number';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedTotalAttribute(): string
    {
        return 'Rp'.number_format((float) $this->total, 0, ',', '.');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu Pembayaran',
            'processing' => 'Diproses',
            'completed' => 'Selesai',
            'cancelled' => 'Dibatalkan',
            default => ucfirst($this->status),
        };
    }

    public function getPaymentStatusLabelAttribute(): string
    {
        return match ($this->payment_status) {
            'paid' => 'Lunas',
            'unpaid' => 'Belum Dibayar',
            'failed' => 'Gagal',
            'expired' => 'Kedaluwarsa',
            default => ucfirst($this->payment_status),
        };
    }

    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }
}
