<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // database/migrations/xxxx_create_estimate_invoices_table.php
public function up()
{
    Schema::create('estimate_invoices', function (Blueprint $table) {
        $table->id();
        $table->string('party_broker')->nullable();
        $table->string('city')->nullable();
        $table->string('goodz_name')->nullable();
        $table->string('details_extra')->nullable();
        $table->string('bilti_no')->nullable();
        $table->decimal('discount_percent', 8, 2)->default(0);
        $table->decimal('discount_amount', 10, 2)->default(0);
        $table->string('tax_type')->default('NONE');
        $table->decimal('tax_amount', 10, 2)->default(0);
        $table->decimal('round_off', 10, 2)->default(0);
        $table->decimal('total', 10, 2)->default(0);
        $table->decimal('paid_amount', 10, 2)->default(0);
        $table->decimal('remaining_amount', 10, 2)->default(0);
        $table->boolean('full_receive')->default(false);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_invoices');
    }
};
