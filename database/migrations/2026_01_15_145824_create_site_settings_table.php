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
            $table->string('email')->nullable(); // Opsiyonel: genel e-mail
            $table->string('phone')->nullable(); // Opsiyonel: tek telefon
            $table->string('logo')->nullable();
            $table->string('footer_logo')->nullable();

            // JSON alan: Çoklu iletişim (tel, mobile, whatsapp, fax, email)
            $table->json('contacts')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
