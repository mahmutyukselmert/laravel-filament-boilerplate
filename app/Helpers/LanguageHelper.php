<?php

if (!function_exists('current_language_id')) {
    function current_language_id(): int
    {
        if(session()->has('language_id')) {
            return session('language_id');
        }

        $default = \App\Models\Language::where('active', 1)->where('is_default', 1)->first();
        return $default ? $default->id : 1; // fallback
    }
}
