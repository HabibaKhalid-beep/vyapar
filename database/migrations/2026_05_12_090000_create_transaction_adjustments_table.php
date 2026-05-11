<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->cascadeOnDelete();
            $table->foreignId('account_party_id')->nullable()->constrained('parties')->nullOnDelete();
            $table->foreignId('broker_id')->nullable()->constrained('brokers')->nullOnDelete();
            $table->foreignId('item_id')->nullable()->constrained('items')->nullOnDelete();
            $table->enum('mode', ['+', '-', 'S'])->default('+');
            $table->string('title')->nullable();
            $table->text('details')->nullable();
            $table->decimal('percentage', 10, 4)->nullable();
            $table->decimal('rate', 10, 2)->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->boolean('affects_invoice')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction_adjustments');
    }
};
