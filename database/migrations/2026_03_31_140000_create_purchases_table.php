<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->nullable()->constrained('parties')->nullOnDelete();
            $table->string('party_name')->nullable();
            $table->string('phone')->nullable();
            $table->text('billing_address')->nullable();
            $table->string('bill_number')->nullable();
            $table->date('bill_date')->nullable();
            $table->integer('total_qty')->default(0);
            $table->decimal('total_amount', 15, 2)->default(0);
            $table->decimal('discount_pct', 10, 2)->default(0);
            $table->decimal('discount_rs', 10, 2)->default(0);
            $table->decimal('tax_pct', 10, 2)->default(0);
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('shipping_charge', 15, 2)->default(0);
            $table->decimal('round_off', 15, 2)->default(0);
            $table->decimal('grand_total', 15, 2)->default(0);
            $table->decimal('paid_amount', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->string('document_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
