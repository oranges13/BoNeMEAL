<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('password')->nullable()->change();
            $table->string('role')->default('moderator')->after('email');
            $table->boolean('active')->default(true)->after('role');
            $table->boolean('must_set_password')->default(false)->after('active');
            $table->string('minecraft_uuid', 36)->nullable()->unique()->after('must_set_password');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['minecraft_uuid']);
            $table->dropColumn(['role', 'active', 'must_set_password', 'minecraft_uuid']);
        });
    }
};
