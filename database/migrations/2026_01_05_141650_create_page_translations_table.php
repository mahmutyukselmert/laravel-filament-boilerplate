<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('page_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_id')->constrained()->cascadeOnDelete();
            $table->foreignId('language_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->string('slug')->index();
            $table->text('content')->nullable();
            $table->text('short_description')->nullable();
            $table->string('meta_title')->nullable();
            $table->string('meta_description')->nullable();
            $table->string('meta_keywords')->nullable();
            $table->json('sections')->nullable();
            $table->unique(['page_id', 'language_id']); // Bir sayfa için aynı dilde tek çeviri
            $table->unique(['language_id', 'slug']); // Aynı dilde slug benzersiz
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_translations');
    }
};
