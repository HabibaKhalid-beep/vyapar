<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->decimal('bardana', 15, 2)->default(0);
            $table->decimal('mazdori', 15, 2)->default(0);
            $table->decimal('rehra_mazdori', 15, 2)->default(0);
            $table->decimal('dak_karaya', 15, 2)->default(0);
            $table->decimal('brokeri', 15, 2)->default(0);
            $table->decimal('local_izafi', 15, 2)->default(0);
            $table->decimal('total_expense', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_expenses');
    }
};
