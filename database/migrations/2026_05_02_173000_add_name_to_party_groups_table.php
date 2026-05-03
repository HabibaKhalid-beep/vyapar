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
        Schema::table('party_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('party_groups', 'name')) {
                $table->string('name')->unique()->after('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('party_groups', function (Blueprint $table) {
            if (Schema::hasColumn('party_groups', 'name')) {
                $table->dropColumn('name');
            }
        });
    }
};
