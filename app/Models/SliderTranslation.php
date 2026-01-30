<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SliderTranslation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'slider_id', 
        'language_id', 
        'title', 
        'subtitle', 
        'content', 
        'buttons', 
        'extra_fields'
    ];

    protected $casts = [
        'buttons' => 'array',
        'extra_fields' => 'array',
    ];

    public function slider()
    {
        return $this->belongsTo(Slider::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}