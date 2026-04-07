<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('bank_accounts', 'is_active')) {
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->boolean('is_active')->default(true)->after('print_on_invoice');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('bank_accounts', 'is_active')) {
            Schema::table('bank_accounts', function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
        }
    }
};
