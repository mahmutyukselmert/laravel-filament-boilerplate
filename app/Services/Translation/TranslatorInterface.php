<?php

namespace App\Services\Translation;

interface TranslatorInterface
{
    /**
     * Metni bir dilden diğerine çevirir
     *
     * @param string $text
     * @param string $from Kaynak dil (örn: 'tr')
     * @param string $to Hedef dil (örn: 'en')
     * @return string
     */
    public function translate(string $text, string $from, string $to): string;
}
