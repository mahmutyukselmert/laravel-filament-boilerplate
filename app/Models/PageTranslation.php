<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageTranslation extends Model
{
    // Laravel'in tabloyu doğru bulması için (varsayılan budur ama garantiye alalım)
    protected $table = 'page_translations';

    // Bu tabloda timestamps (created_at/updated_at) yoksa false yapın
    public $timestamps = false;

    protected $fillable = [
        'title', 
        'subtitle', 
        'slug', 
        'content', 
        'short_description', 
        'meta_title', 
        'meta_description', 
        'meta_keywords', 
        'locale',
        'page_id',
        'sections'
    ];

    protected $casts = [
        // JSON veriyi otomatik diziye (array) çevirir
        'sections' => 'array', 
    ];
}