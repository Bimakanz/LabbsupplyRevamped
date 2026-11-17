<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\File;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'path',
        'original_path',
        'alt',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->path, 'http')) {
            return $this->path;
        }

        return asset('storage/'.$this->existingPath());
    }

    /**
     * Use the WebP file when the stored path still points to an old
     * JPG/JPEG/PNG that no longer exists on disk.
     */
    private function existingPath(): string
    {
        $path = (string) $this->path;
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        if (! in_array($extension, ['jpg', 'jpeg', 'png'], true)) {
            return $path;
        }

        $webp = preg_replace('/\.(jpe?g|png)$/i', '.webp', $path);

        return File::exists(storage_path('app/public/'.$webp)) ? $webp : $path;
    }
}
