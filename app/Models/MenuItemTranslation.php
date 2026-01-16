<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MenuItemTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['menu_item_id', 'language_id', 'label'];

    public function menuItem()
    {
        return $this->belongsTo(MenuItem::class);
    }
}
