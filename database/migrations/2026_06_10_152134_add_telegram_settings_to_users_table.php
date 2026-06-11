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
            $table->string('telegram_bot_token')->nullable()->after('email');
            $table->string('telegram_chat_id')->nullable()->after('telegram_bot_token');
            $table->boolean('notify_mail')->default(true)->after('telegram_chat_id');
            $table->boolean('notify_telegram')->default(false)->after('notify_mail');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['telegram_bot_token', 'telegram_chat_id', 'notify_mail', 'notify_telegram']);
        });
    }
};
