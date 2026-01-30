<?php

namespace App\Helpers;

class ContactHelper
{
    /**
     * DB için TEK FORMAT
     */
    public static function normalize(?string $number): ?string
    {
        if (! $number) {
            return null;
        }

        $n = preg_replace('/\D+/', '', $number);

        // 444'lü numaralar
        if (preg_match('/^444\d{4}$/', $n)) {
            return $n;
        }

        // 0 ile başlayan TR numara
        if (preg_match('/^0\d{10}$/', $n)) {
            return '9' . $n;
        }

        // 90 ile başlayan TR numara
        if (preg_match('/^90\d{10}$/', $n)) {
            return $n;
        }

        return $n;
    }

    /**
     * İnsan okunur FORMAT
     */
    public static function format(?string $number): ?string
    {
        if (! $number) {
            return null;
        }

        $n = self::normalize($number);

        // 444 4 444
        if (preg_match('/^444(\d)(\d{3})$/', $n, $m)) {
            return "444 {$m[1]} {$m[2]}";
        }

        // GSM (5xx)
        if (preg_match('/^90(5\d{2})(\d{3})(\d{2})(\d{2})$/', $n, $m)) {
            return "0 ({$m[1]}) {$m[2]} {$m[3]} {$m[4]}";
        }

        // SABİT + 0850 (2xx / 3xx / 8xx)
        if (preg_match('/^90([238]\d{2})(\d{3})(\d{2})(\d{2})$/', $n, $m)) {
            return "0 ({$m[1]}) {$m[2]} {$m[3]} {$m[4]}";
        }

        return $number;
    }

    /**
     * Link üretimi
     */
    public static function link(?string $number, string $type = 'phone'): ?string
    {
        if (! $number) {
            return null;
        }

        $n = self::normalize($number);

        return match ($type) {
            'whatsapp', 'mobile' => "https://wa.me/{$n}",
            'phone', 'fax' => "tel:{$n}",
            default => $n,
        };
    }

    public function getSocialLink(string $type): ?string
    {
        $link = collect($this->social)->firstWhere('type', $type);
        return $link['label'] ?? null;
    }

}
