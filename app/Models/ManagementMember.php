<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManagementMember extends Model
{
    protected $fillable = ['name', 'position', 'bio', 'sort_order'];
}
