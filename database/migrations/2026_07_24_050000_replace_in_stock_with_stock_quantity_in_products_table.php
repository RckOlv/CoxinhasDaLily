<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->integer('stock_quantity')->default(0)->after('is_active');
        });

        DB::table('products')->where('in_stock', true)->update(['stock_quantity' => 10]);
        DB::table('products')->where('in_stock', false)->update(['stock_quantity' => 0]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('in_stock');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('in_stock')->default(true)->after('is_active');
        });

        DB::table('products')->where('stock_quantity', '>', 0)->update(['in_stock' => true]);
        DB::table('products')->where('stock_quantity', 0)->update(['in_stock' => false]);

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('stock_quantity');
        });
    }
};
