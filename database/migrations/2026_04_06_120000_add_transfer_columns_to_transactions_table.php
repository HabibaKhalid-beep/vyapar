<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'counter_party_id')) {
                $table->foreignId('counter_party_id')->nullable()->after('party_id')->constrained('parties')->nullOnDelete();
            }

            if (!Schema::hasColumn('transactions', 'transfer_group')) {
                $table->string('transfer_group')->nullable()->after('number');
            }

            if (!Schema::hasColumn('transactions', 'description')) {
                $table->text('description')->nullable()->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'counter_party_id')) {
                $table->dropForeign(['counter_party_id']);
                $table->dropColumn('counter_party_id');
            }

            if (Schema::hasColumn('transactions', 'transfer_group')) {
                $table->dropColumn('transfer_group');
            }

            if (Schema::hasColumn('transactions', 'description')) {
                $table->dropColumn('description');
            }
        });
    }
};
