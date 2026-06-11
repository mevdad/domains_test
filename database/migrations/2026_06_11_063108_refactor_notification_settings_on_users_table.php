<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->json('notification_settings')->nullable()->after('email');
        });

        DB::table('users')->orderBy('id')->each(function (object $user) {
            $settings = [];

            if (isset($user->notify_mail) && $user->notify_mail) {
                $settings['mail'] = ['enabled' => true];
            }

            if (isset($user->telegram_bot_token) || isset($user->telegram_chat_id)) {
                $settings['telegram'] = [
                    'enabled' => isset($user->notify_telegram) && $user->notify_telegram,
                    'bot_token' => $user->telegram_bot_token ?? null,
                    'chat_id' => $user->telegram_chat_id ?? null,
                ];
            }

            DB::table('users')->where('id', $user->id)->update([
                'notification_settings' => json_encode($settings ?: (object) []),
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['notify_mail', 'notify_telegram', 'telegram_bot_token', 'telegram_chat_id']);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_bot_token')->nullable()->after('email');
            $table->string('telegram_chat_id')->nullable()->after('telegram_bot_token');
            $table->boolean('notify_mail')->default(true)->after('telegram_chat_id');
            $table->boolean('notify_telegram')->default(false)->after('notify_mail');
        });

        DB::table('users')->orderBy('id')->each(function (object $user) {
            $settings = json_decode($user->notification_settings ?? '{}', true) ?? [];

            DB::table('users')->where('id', $user->id)->update([
                'notify_mail' => (bool) ($settings['mail']['enabled'] ?? false),
                'notify_telegram' => (bool) ($settings['telegram']['enabled'] ?? false),
                'telegram_bot_token' => $settings['telegram']['bot_token'] ?? null,
                'telegram_chat_id' => $settings['telegram']['chat_id'] ?? null,
            ]);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('notification_settings');
        });
    }
};
