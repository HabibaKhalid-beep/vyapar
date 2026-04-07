<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'broker_id')) {
                $table->foreignId('broker_id')->nullable()->after('party_id')->constrained('brokers')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            if (Schema::hasColumn('sales', 'broker_id')) {
                $table->dropForeign(['broker_id']);
                $table->dropColumn('broker_id');
            }
        });
    }
};
