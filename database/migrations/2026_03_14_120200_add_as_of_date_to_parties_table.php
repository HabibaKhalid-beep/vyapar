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
    Schema::table('parties', function (Blueprint $table) {
        if (!Schema::hasColumn('parties', 'as_of_date')) {
            $table->date('as_of_date')->nullable();
        }
    });
}

public function down()
{
    Schema::table('parties', function (Blueprint $table) {
        $table->dropColumn('as_of_date');
    });
}
};
