<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'email',
        'phone',
        'logo',
        'footer_logo',
        'contacts',
    ];

    // JSON cast
    protected $casts = [
        'contacts' => 'array',
    ];

    // Accessor: Sadece WhatsApp numarasını döndürmek için
    public function getWhatsappAttribute(): ?string
    {
        return collect($this->contacts)
            ->firstWhere('type', 'mobile')['value'] ?? null;
    }

    // Accessor: Display hali
    public function getWhatsappDisplayAttribute(): ?string
    {
        return collect($this->contacts)
            ->firstWhere('type', 'mobile')['display'] ?? null;
    }

    // Link üretmek için helper kullanabilirsin
    public function getContactLink(string $type): ?string
    {
        $contact = collect($this->contacts)->firstWhere('type', $type);

        return $contact ? \App\Helpers\ContactHelper::link($contact['value'], $type) : null;
    }

    public function getContactDisplay(string $type): ?string
    {
        $contact = collect($this->contacts)->firstWhere('type', $type);

        return $contact['display'] ?? null;
    }
}
