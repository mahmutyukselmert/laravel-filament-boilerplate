<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionTranslation extends Model
{
    public $timestamps = true;
    protected $fillable = [
        'section_id', 'language_id', 'title', 'subtitle', 
        'description', 'content', 'buttons', 'images', 'extra_fields'
    ];

    protected $casts = [
        'content' => 'array',
        'buttons' => 'array',
        'images' => 'array',
        'extra_fields' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}