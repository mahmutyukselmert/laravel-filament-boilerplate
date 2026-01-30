<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->index()->unique();
            $table->string('slide_type')->default('image'); // image, video

            // Desktop Dosyaları
            $table->string('image')->nullable();
            $table->string('video_path')->nullable();
            $table->string('video_url')->nullable();

            // Mobil Dosyaları
            $table->string('mobile_image')->nullable();
            $table->string('mobile_video_path')->nullable();
            $table->string('mobile_video_url')->nullable();
            
            $table->json('extra_fields')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sliders');
    }
};
