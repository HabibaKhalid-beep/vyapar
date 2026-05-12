<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
public function up()
{
    Schema::create('delivery_challan_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('delivery_challan_id')->constrained()->onDelete('cascade');
        $table->string('item_name');
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
        Schema::dropIfExists('delivery_challan_items');
    }
};
