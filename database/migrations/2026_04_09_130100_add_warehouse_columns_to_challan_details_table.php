<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('challan_details', function (Blueprint $table) {
            if (!Schema::hasColumn('challan_details', 'warehouse_id')) {
                $table->foreignId('warehouse_id')->nullable()->after('broker_phone')->constrained('warehouses')->nullOnDelete();
            }

            if (!Schema::hasColumn('challan_details', 'warehouse_phone')) {
                $table->string('warehouse_phone')->nullable()->after('warehouse_name');
            }

            if (!Schema::hasColumn('challan_details', 'warehouse_handler_phone')) {
                $table->string('warehouse_handler_phone')->nullable()->after('warehouse_handler_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('challan_details', function (Blueprint $table) {
            if (Schema::hasColumn('challan_details', 'warehouse_id')) {
                $table->dropConstrainedForeignId('warehouse_id');
            }

            $columns = array_filter([
                Schema::hasColumn('challan_details', 'warehouse_phone') ? 'warehouse_phone' : null,
                Schema::hasColumn('challan_details', 'warehouse_handler_phone') ? 'warehouse_handler_phone' : null,
            ]);

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
