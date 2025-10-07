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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('email')->unique()->nullable()->after('name');
            $table->timestamp('email_verified_at')->nullable()->after('email');
            $table->string('password')->nullable()->after('email_verified_at');
            $table->string('phone')->nullable()->after('password');
            $table->rememberToken()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['email', 'email_verified_at', 'password', 'phone', 'remember_token']);
        });
    }
};
