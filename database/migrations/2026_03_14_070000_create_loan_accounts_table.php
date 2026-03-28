


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
        Schema::create('loan_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('display_name');
            $table->foreignId('lender_bank_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->string('account_number');
            $table->text('description')->nullable();
            $table->decimal('current_balance', 15, 2);
            $table->date('balance_as_of');
            $table->enum('received_in', ['1', '2'])->default('1'); // Assuming 1 or 2 for some enum
            $table->decimal('interest_rate', 5, 2);
            $table->integer('term_months');
            $table->decimal('processing_fee', 10, 2);
            $table->foreignId('processing_fee_paid_from_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_accounts');
    }
};
