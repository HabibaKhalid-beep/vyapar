<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();

            // Header information
            $table->string('party_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('bill_number')->nullable();
            $table->date('invoice_date')->nullable();

            // Payment information
            $table->string('payment_type')->nullable();
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();

            // Totals
            $table->unsignedInteger('total_qty')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('discount_pct', 8, 2)->default(0);
            $table->decimal('discount_rs', 15, 2)->default(0);
            $table->decimal('tax_pct', 8, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('round_off', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sales');
    }
};
