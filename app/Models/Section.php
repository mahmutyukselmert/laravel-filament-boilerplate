<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Relations\HasMany; // Bunu ekle

class Section extends Model implements TranslatableContract
{
    use Translatable;

    public $translatedAttributes = ['title', 'content'];

    protected $fillable = [
        'admin_title',
        'key',
        'type',
    ];

    // HATALI KISIM BURASIYDI, ŞU ŞEKİLDE DÜZELT:
    public function translations(): HasMany
    {
        return $this->hasMany(SectionTranslation::class);
    }
}