<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    protected $table = 'page_translations';
    public $timestamps = false;

    protected $fillable = [
        'title', 'subtitle', 'slug', 'content', 'short_description',
        'meta_title', 'meta_description', 'meta_keywords', 'sections',
        'language_id', 'page_id'
    ];

    protected $casts = [
        'sections' => 'array',
    ];

    public function page()
    {
        return $this->belongsTo(Page::class, 'page_id');
    }
}