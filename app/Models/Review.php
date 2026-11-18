<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'name',
        'email',
        'rating',
        'comment',
        'is_approved',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'is_approved' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        $recalc = function (Review $review): void {
            if ($review->product_id) {
                $product = $review->product;
                if ($product) {
                    $approved = $product->reviews()->where('is_approved', true);
                    $count = $approved->count();
                    $avg = $count > 0 ? (float) $approved->avg('rating') : 0;
                    $product->update([
                        'rating_count' => $count,
                        'rating_avg' => round($avg, 2),
                    ]);
                }
            }
        };

        static::saved($recalc);
        static::deleted($recalc);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
