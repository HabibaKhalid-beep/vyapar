<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'credit')) {
                $table->decimal('credit', 15, 2)->default(0)->after('total');
            }

            if (!Schema::hasColumn('transactions', 'debit')) {
                $table->decimal('debit', 15, 2)->default(0)->after('credit');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('transactions', 'credit') ? 'credit' : null,
                Schema::hasColumn('transactions', 'debit') ? 'debit' : null,
            ]);

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
