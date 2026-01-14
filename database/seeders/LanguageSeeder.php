<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $languages = [
            [
                'name' => 'Türkçe',
                'code' => 'tr',
                'is_default' => true,
                'active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'English',
                'code' => 'en',
                'is_default' => false,
                'active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Deutsch',
                'code' => 'de',
                'is_default' => false,
                'active' => false,
                'sort_order' => 3,
            ],
            [
                'name' => 'Русский',
                'code' => 'ru',
                'is_default' => false,
                'active' => false,
                'sort_order' => 4,
            ],
            [
                'name' => 'Français',
                'code' => 'fr',
                'is_default' => false,
                'active' => false,
                'sort_order' => 5,
            ],
            [
                'name' => 'العربية',
                'code' => 'ar',
                'is_default' => false,
                'active' => false,
                'sort_order' => 6,
            ],
        ];

        foreach ($languages as $lang) {
            Language::updateOrCreate(['code' => $lang['code']], $lang);
        }
    }
}
