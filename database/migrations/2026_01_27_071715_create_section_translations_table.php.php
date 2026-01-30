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
        // create_section_translations_table.php
        Schema::create('section_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained()->onDelete('cascade');
            $table->foreignId('language_id')->constrained(); // Senin sistemin ID bazlı
            
            // Ortak alanlar
            $table->string('title')->nullable();
            $table->string('subtitle')->nullable();
            $table->text('description')->nullable();
            
            // Esnek alanlar (Repeater verileri buraya gelecek)
            $table->json('content')->nullable(); 
            $table->json('buttons')->nullable();
            $table->json('images')->nullable();
            $table->json('extra_fields')->nullable();

            $table->unique(['section_id', 'language_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_translations');
    }
};
