<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->string('name')->after('category_id');
            $table->string('unit')->nullable()->after('name');
            $table->decimal('sale_price', 10, 2)->default(0)->after('unit');
            $table->decimal('wholesale_price', 10, 2)->default(0)->after('sale_price');
            $table->decimal('purchase_price', 10, 2)->default(0)->after('wholesale_price');
            $table->decimal('opening_qty', 10, 2)->default(0)->after('purchase_price');
            $table->string('item_code')->nullable()->after('opening_qty');
            $table->string('location')->nullable()->after('item_code');
            $table->decimal('min_stock', 10, 2)->default(0)->after('location');
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['name','unit','sale_price','wholesale_price','purchase_price','opening_qty','item_code','location','min_stock']);
        });
    }
};