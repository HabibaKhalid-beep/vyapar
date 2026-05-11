<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('sales_details')) {
            return;
        }

        Schema::create('sales_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            $table->foreignId('warehouse_id')->nullable()->constrained('warehouses')->nullOnDelete();
            $table->string('delivery_person')->nullable();
            $table->string('bilti_no')->nullable();
            $table->string('gate_no')->nullable();
            $table->string('po_no')->nullable();
            $table->date('po_date')->nullable();
            $table->string('city')->nullable();
            $table->string('party_no')->nullable();
            $table->string('goods_name')->nullable();
            $table->string('details_extra')->nullable();
            $table->string('bilti_gari_no')->nullable();
            $table->json('custom_expenses')->nullable();
            $table->timestamps();

            $table->unique('sale_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sales_details');
    }
};
