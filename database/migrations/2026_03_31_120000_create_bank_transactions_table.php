<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('from_bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->foreignId('to_bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->string('type', 50);
            $table->decimal('amount', 15, 2);
            $table->date('transaction_date')->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->text('description')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_transactions');
    }
};
