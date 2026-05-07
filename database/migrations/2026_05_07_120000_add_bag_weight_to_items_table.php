<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('items') || Schema::hasColumn('items', 'bag_weight')) {
            return;
        }

        Schema::table('items', function (Blueprint $table) {
            $table->decimal('bag_weight', 10, 2)
                ->nullable()
                ->after('unit_conversion_rate');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('items') || !Schema::hasColumn('items', 'bag_weight')) {
            return;
        }

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn('bag_weight');
        });
    }
};
