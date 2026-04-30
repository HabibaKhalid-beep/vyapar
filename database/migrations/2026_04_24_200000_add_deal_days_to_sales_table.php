<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('sales', 'deal_days')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->integer('deal_days')->nullable()->after('due_date');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sales', 'deal_days')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('deal_days');
            });
        }
    }
};
