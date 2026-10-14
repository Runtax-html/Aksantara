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
        if (!Schema::hasColumn('users', 'coins')) {
            Schema::table('users', function (Blueprint $table) {
                $table->unsignedInteger('coins')->default(50)->after('xp');
            });
        }

        if (!Schema::hasTable('user_quiz_progress')) {
            Schema::create('user_quiz_progress', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedTinyInteger('level_id');
                $table->string('status', 20)->default('locked'); // 'active', 'completed', 'locked'
                $table->unsignedTinyInteger('stars')->default(0);
                $table->unsignedSmallInteger('score')->default(0);
                $table->unsignedSmallInteger('total_questions')->default(0);
                $table->timestamp('completed_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'level_id']);
            });
        }

        if (!Schema::hasTable('user_unlocked_stories')) {
            Schema::create('user_unlocked_stories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->unsignedInteger('story_id');
                $table->boolean('is_read')->default(false);
                $table->timestamp('unlocked_at')->useCurrent();
                $table->timestamp('read_at')->nullable();
                $table->timestamps();

                $table->unique(['user_id', 'story_id']);
            });
        }

        if (!Schema::hasTable('user_daily_missions')) {
            Schema::create('user_daily_missions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
                $table->date('mission_date');
                $table->string('mission_key', 50); // 'complete_level', 'practice_writing', 'read_story', 'bonus_box'
                $table->boolean('is_completed')->default(false);
                $table->boolean('reward_claimed')->default(false);
                $table->timestamps();

                $table->unique(['user_id', 'mission_date', 'mission_key']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_daily_missions');
        Schema::dropIfExists('user_unlocked_stories');
        Schema::dropIfExists('user_quiz_progress');

        if (Schema::hasColumn('users', 'coins')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('coins');
            });
        }
    }
};
