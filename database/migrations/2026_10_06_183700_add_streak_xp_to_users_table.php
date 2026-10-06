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
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('streak')->default(0)->after('remember_token');
            $table->unsignedInteger('xp')->default(0)->after('streak');
            $table->unsignedSmallInteger('level')->default(1)->after('xp');
            $table->date('last_activity_at')->nullable()->after('level');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['streak', 'xp', 'level', 'last_activity_at']);
        });
    }
};
