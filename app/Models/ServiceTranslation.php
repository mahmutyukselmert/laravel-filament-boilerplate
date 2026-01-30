<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceTranslation extends Model
{
    protected $table = 'service_translations';
    public $timestamps = false;

    protected $fillable = [
        'title', 'subtitle', 'slug', 'content', 'short_description',
        'meta_title', 'meta_description', 'meta_keywords', 'sections',
        'gallery', 'language_id', 'service_id'
    ];

    protected $casts = [
        'sections' => 'array',
        'gallery' => 'array'
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}