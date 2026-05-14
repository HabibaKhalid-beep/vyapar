<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cheque_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('type');            // CHEQUE_IN | CHEQUE_OUT
            $table->string('name');            // party / description
            $table->string('cheque_number')->nullable();
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->enum('status', ['pending', 'cleared', 'bounced'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cheque_transactions');
    }
};