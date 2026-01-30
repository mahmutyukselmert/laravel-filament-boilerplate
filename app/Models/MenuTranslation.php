<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MenuTranslation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'menu_id', 
        'language_id', 
        'title',
        'extra_fields'
    ];

    protected $casts = [
        'extra_fields' => 'array',
    ];

    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}