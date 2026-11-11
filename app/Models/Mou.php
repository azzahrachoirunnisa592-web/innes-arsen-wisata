<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mou extends Model
{
    protected $table = 'mous';

    protected $fillable = [
        'user_id', 'organization_name', 'contact_name', 'email', 'phone',
        'description', 'status', 'document_path', 'admin_document_path', 'admin_notes',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
