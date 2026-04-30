<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->integer('tadad')->nullable()->after('deal_days');
            $table->decimal('total_wazan', 10, 2)->nullable()->after('tadad');
            $table->decimal('safi_wazan', 10, 2)->nullable()->after('total_wazan');
            $table->decimal('rate', 10, 2)->nullable()->after('safi_wazan');
            $table->decimal('deo', 10, 2)->nullable()->after('rate');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn([
                'tadad',
                'total_wazan',
                'safi_wazan',
                'rate',
                'deo',
            ]);
        });
    }
};
