<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('party_id')->constrained('parties')->cascadeOnDelete();
            $table->foreignId('broker_id')->nullable()->constrained('brokers')->nullOnDelete();
            $table->unsignedInteger('tadad')->default(0);
            $table->decimal('total_wazan', 15, 2)->default(0);
            $table->decimal('safi_wazan', 15, 2)->default(0);
            $table->decimal('rate', 15, 2)->default(0);
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('deo')->nullable();
            $table->string('broker_commission_type')->nullable();
            $table->decimal('broker_commission_value', 15, 2)->default(0);
            $table->decimal('broker_commission', 15, 2)->default(0);
            $table->decimal('final_amount', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
