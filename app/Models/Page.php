<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model implements TranslatableContract
{
    use Translatable;
    use SoftDeletes;

    // Dile göre değişen alanlar (page_translations tablosundaki kolonlar)
    public $translatedAttributes = [
        'title', 'slug', 'subtitle', 'short_description', 
        'content', 'sections', 'meta_title', 'meta_description', 'meta_keywords'
    ];

    protected $fillable = ['image', 'is_active', 'sort_order'];

    protected $with = ['translations'];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        try {
            $locales = Language::where('active', true)->pluck('code')->toArray();
            $this->mergeFillable($locales);
        } catch (\Exception $e) {
            // Migration sırasında hata almamak için boş bırakıyoruz
            // Bu hata genellikle migration sırasında oluşur, uygulama çalışırken ignore edilir
        }
    }
}