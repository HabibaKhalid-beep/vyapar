<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'secondary_unit')) {
                $table->string('secondary_unit')->nullable()->after('unit');
            }
            if (!Schema::hasColumn('items', 'unit_conversion_rate')) {
                $table->decimal('unit_conversion_rate', 15, 4)->nullable()->after('secondary_unit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (Schema::hasColumn('items', 'unit_conversion_rate')) {
                $table->dropColumn('unit_conversion_rate');
            }
            if (Schema::hasColumn('items', 'secondary_unit')) {
                $table->dropColumn('secondary_unit');
            }
        });
    }
};
