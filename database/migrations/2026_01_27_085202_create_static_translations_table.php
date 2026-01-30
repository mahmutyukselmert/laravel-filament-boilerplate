<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('static_translations', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique(); // 'footer_slogan' veya 'Teklif Al'
            $table->json('text'); // Her dil için çeviri: {"tr": "Teklif Al", "en": "Get Quote"}
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('static_translations');
    }
};
