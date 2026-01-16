<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SectionTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['title', 'content', 'language_id', 'section_id'];
    
    protected $casts = [
        'content' => 'array', 
    ];
}