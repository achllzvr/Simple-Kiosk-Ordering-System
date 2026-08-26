<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        Schema::dropIfExists('orders');

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_name')->nullable();
            $table->string('guest_phone')->nullable();
            $table->string('guest_email')->nullable();
            $table->string('tracking_token', 64)->unique();
            $table->foreignId('restaurant_id')->nullable()->constrained('restaurants')->nullOnDelete();
            $table->string('status')->default('placed');
            $table->string('payment_status')->default('unpaid');
            $table->string('payment_method')->nullable();
            $table->decimal('total_price', 10, 2);
            $table->string('order_mode')->default('dine-in');
            $table->decimal('customer_lat', 10, 7)->nullable();
            $table->decimal('customer_lng', 10, 7)->nullable();
            $table->text('notes')->nullable();
            $table->string('paymongo_checkout_session_id', 64)->nullable();
            $table->string('paymongo_payment_id', 64)->nullable();
            $table->string('external_payment_ref', 128)->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('status');
            $table->index('payment_status');
            $table->index('created_at');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['order_id']);
        });

        Schema::dropIfExists('orders');

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('restrict');
            $table->string('status')->default('placed');
            $table->decimal('total_price', 10, 2);
            $table->string('order_mode')->default('dine-in');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->foreign('order_id')->references('id')->on('orders')->cascadeOnDelete();
        });
    }
};
