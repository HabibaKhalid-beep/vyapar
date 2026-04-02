<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (!Schema::hasColumn('purchases', 'type')) {
                $table->string('type')->default('purchase_bill')->after('party_id');
            }

            if (!Schema::hasColumn('purchases', 'due_date')) {
                $table->date('due_date')->nullable()->after('bill_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('purchases', function (Blueprint $table) {
            if (Schema::hasColumn('purchases', 'due_date')) {
                $table->dropColumn('due_date');
            }

            if (Schema::hasColumn('purchases', 'type')) {
                $table->dropColumn('type');
            }
        });
    }
};
