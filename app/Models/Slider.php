<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Slider extends Model implements HasMedia
{
    use InteractsWithMedia;
    use SoftDeletes;

    protected $fillable = ['category', 'slide_type', 'image', 'video_path', 'video_url', 'mobile_image', 'mobile_video_path', 'mobile_video_url', 'extra_fields', 'sort_order', 'active'];

    protected $casts = [
        'active' => 'boolean',
        'extra_fields' => 'array',
    ];

    public function translations()
    {
        return $this->hasMany(SliderTranslation::class);
    }

    public function active_translation()
    {
        return $this->hasOne(SliderTranslation::class)
                    ->where('language_id', session('language_id', 1));
    }

    public function getTAttribute()
    {
        return $this->active_translation ?? $this->translations->first();
    }

    protected static function booted()
    {
        static::deleted(function ($slider) {
            // Slider soft-delete edildiğinde çevirilerini de soft-delete et
            $slider->translations()->delete();
        });

        static::restoring(function ($slider) {
            // Slider geri getirildiğinde (restore) çevirilerini de geri getir
            $slider->translations()->restore();
        });
    }
}