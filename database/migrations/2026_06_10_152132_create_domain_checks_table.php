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
        Schema::create('domain_checks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('domain_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_up');
            $table->unsignedSmallInteger('status_code')->nullable();
            $table->unsignedInteger('response_time_ms')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('checked_at');
            $table->index(['domain_id', 'checked_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_checks');
    }
};
