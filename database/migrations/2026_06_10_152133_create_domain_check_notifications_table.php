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
        Schema::create('domain_check_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_check_id')->constrained()->cascadeOnDelete();
            $table->string('channel', 20);
            $table->string('status', 20)->default('pending');
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
            $table->index(['domain_check_id', 'channel']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_check_notifications');
    }
};
