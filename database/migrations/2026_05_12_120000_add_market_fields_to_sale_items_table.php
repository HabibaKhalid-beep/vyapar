<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            if (!Schema::hasColumn('sale_items', 'tafseel')) {
                $table->string('tafseel')->nullable()->after('item_description');
            }

            if (!Schema::hasColumn('sale_items', 'gross_w')) {
                $table->decimal('gross_w', 15, 2)->default(0)->after('quantity');
            }

            if (!Schema::hasColumn('sale_items', 'net_w')) {
                $table->decimal('net_w', 15, 2)->default(0)->after('gross_w');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sale_items', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('sale_items', 'tafseel') ? 'tafseel' : null,
                Schema::hasColumn('sale_items', 'gross_w') ? 'gross_w' : null,
                Schema::hasColumn('sale_items', 'net_w') ? 'net_w' : null,
            ]);

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
