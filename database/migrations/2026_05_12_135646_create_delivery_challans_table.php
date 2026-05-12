<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    

public function up()
{
    Schema::create('delivery_challans', function (Blueprint $table) {
        $table->id();
        $table->string('party_broker')->nullable();
        $table->string('city')->nullable();
        $table->string('goodz_name')->nullable();
        $table->string('details_extra')->nullable();
        $table->string('bilti_no')->nullable();
        $table->decimal('total', 10, 2)->default(0);
        $table->text('notes')->nullable();    // delivery specific
        $table->timestamps();
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('delivery_challans');
    }
};
