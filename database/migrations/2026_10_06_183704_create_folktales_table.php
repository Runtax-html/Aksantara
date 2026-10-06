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
        Schema::create('folktales', function (Blueprint $table) {
            $table->id();

            // Judul cerita
            $table->string('title');

            // Jenis aksara: jawa, sunda, bali, dll.
            $table->string('script_type', 50)->index();

            // Isi cerita dalam aksara Nusantara
            $table->longText('content_aksara');

            // Transliterasi Latin
            $table->longText('content_latin');

            // Terjemahan Bahasa Indonesia
            $table->longText('content_translation');

            // Ringkasan cerita
            $table->text('synopsis')->nullable();

            // Tingkat kesulitan: pemula, menengah, mahir
            $table->enum('difficulty', ['pemula', 'menengah', 'mahir'])->default('pemula');

            // XP reward setelah selesai membaca
            $table->unsignedSmallInteger('xp_reward')->default(50);

            // Asal daerah cerita
            $table->string('region')->nullable();

            // Gambar sampul
            $table->string('cover_image_url')->nullable();

            // Estimasi waktu baca (menit)
            $table->unsignedSmallInteger('read_time_minutes')->default(5);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('folktales');
    }
};
