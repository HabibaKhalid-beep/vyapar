<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('payment_ins', function (Blueprint $table) {
    $table->id();
    $table->foreignId('party_id')->constrained('parties')->onDelete('cascade');
    $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->onDelete('set null');
    $table->decimal('amount', 10, 2);
    $table->string('payment_type')->default('cash');
    $table->string('reference_no')->nullable();
    $table->string('receipt_no')->nullable();
    $table->date('date')->nullable();
    $table->text('description')->nullable();
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_ins');
    }
};
