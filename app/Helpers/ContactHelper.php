<?php

namespace App\Helpers;

class ContactHelper
{
    public static function format(string $number, string $type = 'phone'): string
    {
        $n = preg_replace('/\D+/', '', $number);

        // 444 numaralar
        if (preg_match('/^444\d{4}$/', $n)) {
            return substr($n, 0, 3).' '.substr($n, 3, 1).' '.substr($n, 4, 3);
        }

        // Türkiye GSM
        if (preg_match('/^90(5\d{2})(\d{3})(\d{2})(\d{2})$/', $n, $m)) {
            return '0 ('.$m[1].') '.$m[2].' '.$m[3].' '.$m[4];
        }

        // Türkiye sabit
        if (preg_match('/^90(2\d{2})(\d{3})(\d{2})(\d{2})$/', $n, $m)) {
            return '0 ('.$m[1].') '.$m[2].' '.$m[3].' '.$m[4];
        }

        return $number;
    }

    public static function link(string $number, string $type): string
    {
        $n = preg_replace('/\D+/', '', $number);

        return match ($type) {
            'whatsapp', 'mobile' => "https://wa.me/{$n}",
            'phone', 'fax' => "tel:{$n}",
            default => $n,
        };
    }
}
