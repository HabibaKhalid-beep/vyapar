<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  // database/migrations/xxxx_create_estimate_invoice_items_table.php
public function up()
{
    Schema::create('estimate_invoice_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('estimate_invoice_id')->constrained()->onDelete('cascade');
        $table->string('item_name');         // always string, never null
        $table->string('tafseel')->nullable();
        $table->decimal('percent', 8, 2)->default(0);
        $table->decimal('amount', 10, 2)->default(0);
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('estimate_invoice_items');
    }
};
