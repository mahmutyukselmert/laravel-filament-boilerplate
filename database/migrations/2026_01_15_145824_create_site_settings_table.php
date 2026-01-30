<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();

            $table->string('site_name')->nullable();
            $table->string('email')->nullable(); 
            $table->string('phone')->nullable(); 
            $table->string('phone_gsm')->nullable(); 
            $table->string('whatsapp')->nullable();
            $table->string('fax')->nullable(); 
            $table->string('address')->nullable();
            $table->string('map')->nullable();

            $table->string('logo')->nullable();
            $table->string('scrolled_logo')->nullable();
            $table->string('footer_logo')->nullable();

            // JSON alan: Çoklu iletişim (tel, mobile, whatsapp, fax, email)
            $table->json('contacts')->nullable();

            $table->string('facebook')->nullable();
            $table->string('instagram')->nullable();
            $table->string('linkedin')->nullable();
            $table->string('x_twitter')->nullable();
            $table->string('youtube')->nullable();

            // Dinamik eklemeler için JSON alan
            $table->json('social_extra')->nullable();
            $table->boolean('maintenance_mode')->default(false);
            $table->json('extra_fields')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
