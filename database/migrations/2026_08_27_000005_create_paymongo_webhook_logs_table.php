<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paymongo_webhook_logs', function (Blueprint $table) {
            $table->id();
            $table->string('event_id')->nullable()->unique();
            $table->string('event_type')->nullable();
            $table->unsignedBigInteger('order_id')->nullable();
            $table->boolean('signature_valid')->default(false);
            $table->boolean('fulfilled')->default(false);
            $table->text('payload_summary')->nullable();
            $table->string('fulfill_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paymongo_webhook_logs');
    }
};
