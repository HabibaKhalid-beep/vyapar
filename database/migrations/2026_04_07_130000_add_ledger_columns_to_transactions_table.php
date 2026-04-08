<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('transactions', 'paid_amount')) {
                $table->decimal('paid_amount', 15, 2)->default(0)->after('total');
            }
            if (!Schema::hasColumn('transactions', 'due_date')) {
                $table->date('due_date')->nullable()->after('balance');
            }
            if (!Schema::hasColumn('transactions', 'broker_id')) {
                $table->foreignId('broker_id')->nullable()->after('description')->constrained('brokers')->nullOnDelete();
            }
            if (!Schema::hasColumn('transactions', 'broker_amount')) {
                $table->decimal('broker_amount', 15, 2)->default(0)->after('broker_id');
            }
            if (!Schema::hasColumn('transactions', 'labour')) {
                $table->decimal('labour', 15, 2)->default(0)->after('broker_amount');
            }
            if (!Schema::hasColumn('transactions', 'bardana')) {
                $table->decimal('bardana', 15, 2)->default(0)->after('labour');
            }
            if (!Schema::hasColumn('transactions', 'parcel_expense')) {
                $table->decimal('parcel_expense', 15, 2)->default(0)->after('bardana');
            }
            if (!Schema::hasColumn('transactions', 'post_expense')) {
                $table->decimal('post_expense', 15, 2)->default(0)->after('parcel_expense');
            }
            if (!Schema::hasColumn('transactions', 'extra_expense')) {
                $table->decimal('extra_expense', 15, 2)->default(0)->after('post_expense');
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'broker_id')) {
                $table->dropConstrainedForeignId('broker_id');
            }

            $columns = array_filter([
                Schema::hasColumn('transactions', 'paid_amount') ? 'paid_amount' : null,
                Schema::hasColumn('transactions', 'due_date') ? 'due_date' : null,
                Schema::hasColumn('transactions', 'broker_amount') ? 'broker_amount' : null,
                Schema::hasColumn('transactions', 'labour') ? 'labour' : null,
                Schema::hasColumn('transactions', 'bardana') ? 'bardana' : null,
                Schema::hasColumn('transactions', 'parcel_expense') ? 'parcel_expense' : null,
                Schema::hasColumn('transactions', 'post_expense') ? 'post_expense' : null,
                Schema::hasColumn('transactions', 'extra_expense') ? 'extra_expense' : null,
            ]);

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
