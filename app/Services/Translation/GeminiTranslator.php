<?php

namespace App\Services\Translation;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiTranslator implements TranslatorInterface
{
    protected string $apiKey;
    protected string $apiUrl = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent';

    public function __construct()
    {
        $this->apiKey = 'AIzaSyBPWlOy0kbQZRW5B_KHpbYxXyi0gDskWAs'; 
    }

    public function translate(string $text, string $from, string $to): string
    {
        if (empty(trim($text)) || $text === '[]' || $text === 'null') return $text;

        $isJson = $this->isJson($text);
        $prompt = $isJson 
            ? "Task: Translate only the values in this JSON from {$from} to {$to}. Output ONLY raw JSON:\n\n{$text}"
            : "Translate this text from {$from} to {$to}. Output only translation:\n\n{$text}";

        // Maksimum 3 kere deneme yap (Kota hatası için)
        $attempts = 0;
        $maxAttempts = 3;

        while ($attempts < $maxAttempts) {
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'x-goog-api-key' => $this->apiKey,
                ])->post($this->apiUrl, [
                    'contents' => [['parts' => [['text' => $prompt]]]],
                    'generationConfig' => ['temperature' => 0.1]
                ]);

                if ($response->successful()) {
                    $result = $response->json();
                    $translated = $result['candidates'][0]['content']['parts'][0]['text'] ?? $text;
                    return $this->cleanMarkdown($translated);
                }

                // EĞER KOTA DOLDUYSA (429)
                if ($response->status() === 429) {
                    $attempts++;
                    Log::warning("Gemini Kotası Doldu. Deneme: {$attempts}. 5 saniye bekleniyor...");
                    sleep(5); // 5 saniye zorunlu bekleme
                    continue; // Döngü başına dön, tekrar dene
                }

                Log::error('Gemini API Error: ' . $response->status() . ' - ' . $response->body());
                break;

            } catch (\Exception $e) {
                Log::error('Gemini Exception: ' . $e->getMessage());
                break;
            }
        }

        return $text;
    }

    private function isJson($string): bool {
        if (!is_string($string)) return false;
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    private function cleanMarkdown($text): string {
        return preg_replace('/^```(?:json)?\n?|```$/', '', trim($text));
    }
}