<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;


class MenuItem extends Model
{
    protected $fillable = ['menu_id', 'parent_id', 'linkable_type', 'linkable_id', 'url', 'target', 'sort_order', 'active'];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(MenuItem::class, 'parent_id')->orderBy('sort_order');
    }

    public function translations()
    {
        return $this->hasMany(MenuItemTranslation::class);
    }
}
