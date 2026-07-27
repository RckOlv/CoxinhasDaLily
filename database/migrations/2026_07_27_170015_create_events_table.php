<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('client_name');
            $table->string('client_whatsapp');
            $table->date('event_date');
            $table->integer('quantity');
            $table->string('pickup_time');
            $table->string('event_type')->default('cumpleanos');
            $table->string('color');
            $table->text('notes')->nullable();
            $table->string('status')->default('pendiente');
            $table->decimal('total', 10, 2)->nullable();
            $table->boolean('deposit_paid')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
