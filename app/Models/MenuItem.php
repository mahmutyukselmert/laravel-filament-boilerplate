<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class MenuItem extends Model
{
    use HasTranslations;
    
    public $translatable = ['title'];
    
    protected $fillable = [
        'menu_id',
        'parent_id',
        'linkable_type',
        'linkable_id',
        'url',
        'target',
        'sort_order',
        'active',
        'title',
    ];
    
    protected $casts = [
        'active' => 'boolean',
    ];
    
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
    
    public function parent()
    {
        return $this->belongsTo(MenuItem::class, 'parent_id');
    }
    public function linkable()
    {
        return $this->morphTo();
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('sort_order');
    }

    public function translations()
    {
        return $this->hasMany(MenuItemTranslation::class);
    }

    public function resolveUrl(): string
    {
        if ($this->linkable) {
            return $this->linkable->url ?? '#';
        }

        return $this->url ?? '#';
    }
}

