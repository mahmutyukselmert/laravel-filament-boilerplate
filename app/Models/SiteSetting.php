<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'email',
        'phone',
        'phone_gsm',
        'whatsapp',
        'fax',
        'address',
        'map',
        'logo',
        'scrolled_logo',
        'footer_logo',
        'contacts',
        'social_extra',
        'facebook',
        'instagram',
        'linkedin',
        'x_twitter',
        'youtube',
        'maintenance_mode',
        'extra_fields',
    ];

    // JSON cast
    protected $casts = [
        'contacts' => 'array',
        'social_extra' => 'array',
        'extra_fields' => 'array',
    ];
    
    public function getPhone(): ?string
    {
        return $this->phone
            ? \App\Helpers\ContactHelper::format($this->phone)
            : null;
    }

}
