<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyGalleryImage extends Model
{
    protected $fillable = ['title', 'caption', 'image_path', 'sort_order', 'published'];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'published' => 'boolean',
        ];
    }
}
