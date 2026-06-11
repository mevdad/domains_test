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
        Schema::table('domains', function (Blueprint $table) {
            $table->unsignedSmallInteger('check_interval')->default(5)->after('name');
            $table->unsignedTinyInteger('check_timeout')->default(10)->after('check_interval');
            $table->string('check_method', 4)->default('GET')->after('check_timeout');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('domains', function (Blueprint $table) {
            $table->dropColumn(['check_interval', 'check_timeout', 'check_method']);
        });
    }
};
