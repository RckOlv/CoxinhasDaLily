<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_whatsapp');
            $table->enum('delivery_method', ['pickup', 'envio'])->default('pickup');
            $table->text('delivery_address')->nullable();
            $table->enum('payment_method', ['efectivo', 'transferencia', 'mercadopago'])->default('efectivo');
            $table->decimal('total', 10, 2)->default(0);
            $table->enum('status', ['pendiente', 'confirmado', 'entregado', 'cancelado'])->default('pendiente');
            $table->boolean('stock_decremented')->default(false);
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 8, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
