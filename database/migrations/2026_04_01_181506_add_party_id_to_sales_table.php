<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
  public function up()
{
    Schema::table('sales', function (Blueprint $table) {
        $table->foreignId('party_id')->nullable()->constrained('parties')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('sales', function (Blueprint $table) {
        $table->dropForeign(['party_id']);
        $table->dropColumn('party_id');
    });
}
};
