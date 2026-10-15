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
        if (!Schema::hasColumn('users', 'max_streak')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('max_streak')->default(0)->after('streak');
            });
        }

        if (!Schema::hasTable('user_checkins')) {
            Schema::create('user_checkins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->string('checkin_date', 10); // 'YYYY-MM-DD'
                $table->string('checkin_type', 30)->default('auto'); // 'auto_quiz', 'auto_canvas', 'auto_story', 'manual'
                $table->timestamps();

                $table->unique(['user_id', 'checkin_date']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_checkins');

        if (Schema::hasColumn('users', 'max_streak')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('max_streak');
            });
        }
    }
};
