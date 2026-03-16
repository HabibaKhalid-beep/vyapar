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
     Schema::create('parties', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('phone')->nullable();
    $table->string('email')->nullable();
    $table->text('billing_address')->nullable();
    $table->text('shipping_address')->nullable();
    $table->decimal('opening_balance', 15, 2)->default(0);
    $table->date('as_of_date')->nullable();
    $table->boolean('credit_limit_enabled')->default(false);
    $table->json('custom_fields')->nullable(); // store custom field names/values
    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('parties');
    }
};
