<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Menu extends Model
{
    protected $fillable = ['name', 'key', 'location', 'sort_order','active'];

    public function items()
    {
        return $this->hasMany(MenuItem::class);
    }

    public function translations()
    {
        return $this->hasMany(MenuTranslation::class);
    }

    public function activeTranslation(): HasOne
    {
        return $this->hasOne(MenuTranslation::class)
            ->where('language_id', app()->getLocale() === 'tr' ? 1 : 2);
    }
}