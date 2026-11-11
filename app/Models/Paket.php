<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Paket extends Model
{
    protected $fillable = [
        'wisata_id', 'title', 'slug', 'category', 'duration_days',
        'duration_nights', 'price', 'quota', 'description', 'includes',
        'excludes', 'published',
    ];

    protected function casts(): array
    {
        return ['price' => 'decimal:2', 'published' => 'boolean'];
    }

    public function wisata(): BelongsTo
    {
        return $this->belongsTo(Wisata::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
