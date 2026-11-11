<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Wisata extends Model
{
    protected $fillable = ['name', 'slug', 'province', 'description', 'image_path'];

    public function getImageUrlAttribute(): string
    {
        if (filled($this->image_path)) {
            if (Str::startsWith($this->image_path, 'destinations/')) {
                return Storage::disk('public')->url($this->image_path);
            }

            return Str::startsWith($this->image_path, ['http://', 'https://'])
                ? $this->image_path
                : asset(ltrim($this->image_path, '/'));
        }

        return match ($this->slug) {
            'bali' => 'https://images.unsplash.com/photo-1537996194471-e657df975ab4?auto=format&fit=crop&w=1000&q=85',
            'yogyakarta' => 'https://images.unsplash.com/photo-1596402184320-417e7178b2cd?auto=format&fit=crop&w=1000&q=85',
            'magelang-borobudur' => 'https://images.unsplash.com/photo-1604999333679-b86d54738315?auto=format&fit=crop&w=1000&q=85',
            'dieng-golden-sunrise' => 'https://images.unsplash.com/photo-1470770841072-f978cf4d019e?auto=format&fit=crop&w=1000&q=85',
            'sidoarjo-sekitarnya' => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1000&q=85',
            'lombok-gili-trawangan' => 'https://images.unsplash.com/photo-1518548419970-58e3b4079ab2?auto=format&fit=crop&w=1000&q=85',
            default => 'https://images.unsplash.com/photo-1501785888041-af3ef285b470?auto=format&fit=crop&w=1000&q=85',
        };
    }

    public function packages(): HasMany
    {
        return $this->hasMany(Paket::class);
    }
}
