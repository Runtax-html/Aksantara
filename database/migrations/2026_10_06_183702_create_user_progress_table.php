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
        Schema::create('user_progress', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->foreignId('character_id')
                  ->constrained('characters')
                  ->cascadeOnDelete();

            // Status pembelajaran
            $table->boolean('is_learned')->default(false);
            $table->boolean('is_mastered')->default(false);

            // Statistik latihan
            $table->unsignedInteger('correct_count')->default(0);
            $table->unsignedInteger('incorrect_count')->default(0);

            // Tingkat penguasaan (0–100)
            $table->unsignedTinyInteger('mastery_percentage')->default(0);

            // Kapan terakhir berlatih karakter ini
            $table->timestamp('last_practiced_at')->nullable();

            $table->timestamps();

            // Satu user hanya punya satu progress per karakter
            $table->unique(['user_id', 'character_id'], 'unique_user_character');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
