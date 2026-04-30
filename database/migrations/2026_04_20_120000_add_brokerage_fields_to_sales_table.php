<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'brokerage_type')) {
                $table->string('brokerage_type')->nullable()->after('broker_id');
            }

            if (!Schema::hasColumn('sales', 'brokerage_rate')) {
                $table->decimal('brokerage_rate', 15, 2)->default(0)->after('brokerage_type');
            }

            if (!Schema::hasColumn('sales', 'broker_amount')) {
                $table->decimal('broker_amount', 15, 2)->default(0)->after('brokerage_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('sales', 'broker_amount') ? 'broker_amount' : null,
                Schema::hasColumn('sales', 'brokerage_rate') ? 'brokerage_rate' : null,
                Schema::hasColumn('sales', 'brokerage_type') ? 'brokerage_type' : null,
            ]);

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
