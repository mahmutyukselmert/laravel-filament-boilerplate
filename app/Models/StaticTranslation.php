<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaticTranslation extends Model
{
    // Veritabanına toplu olarak yazılabilecek kolonları tanımlıyoruz
    protected $fillable = [
        'key',
        'text',
    ];

    // Önceki konuşmamızda bahsettiğim JSON cast işlemini de buraya ekleyelim
    protected $casts = [
        'text' => 'array',
    ];
}
