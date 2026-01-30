<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    protected $fillable = ['file_path', 'title', 'model_type', 'model_id', 'sort_order'];

    protected $casts = [
        'title' => 'array', 
    ];

    public function getLocalizedTitleAttribute()
    {
        $langId = app()->getLocale() === 'tr' ? 1 : 2;
        return $this->title[$langId] ?? ($this->title[1] ?? '');
    }
}