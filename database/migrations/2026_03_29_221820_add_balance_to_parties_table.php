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
        if (!Schema::hasColumn('parties', 'balance')) {
            $table->decimal('balance', 10, 2)->default(0)->after('id');
        }
    });
}

public function down()
{
    Schema::table('parties', function (Blueprint $table) {
        $table->dropColumn('balance');
    });
}
};
