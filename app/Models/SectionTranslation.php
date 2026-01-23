<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'title',
        'subtitle',
        'content',
        'image',
        'language_id',
        'section_id',
        'extra_fields',
    ];

    protected $casts = [
        'content' => 'array',
        'extra_fields' => 'array',
    ];
}
