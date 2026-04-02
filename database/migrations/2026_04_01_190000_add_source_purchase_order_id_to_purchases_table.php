<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'source_purchase_order_id')) {
                $table->unsignedBigInteger('source_purchase_order_id')->nullable()->after('type');
                $table->index('source_purchase_order_id', 'purchases_source_purchase_order_id_index');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'source_purchase_order_id')) {
                $table->dropIndex('purchases_source_purchase_order_id_index');
                $table->dropColumn('source_purchase_order_id');
            }
        });
    }
};
