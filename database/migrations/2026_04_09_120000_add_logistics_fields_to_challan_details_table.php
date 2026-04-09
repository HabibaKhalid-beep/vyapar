<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('challan_details', function (Blueprint $table) {
            if (!Schema::hasColumn('challan_details', 'broker_name')) {
                $table->string('broker_name')->nullable()->after('due_date');
            }

            if (!Schema::hasColumn('challan_details', 'broker_phone')) {
                $table->string('broker_phone')->nullable()->after('broker_name');
            }

            if (!Schema::hasColumn('challan_details', 'warehouse_name')) {
                $table->string('warehouse_name')->nullable()->after('broker_phone');
            }

            if (!Schema::hasColumn('challan_details', 'warehouse_handler_name')) {
                $table->string('warehouse_handler_name')->nullable()->after('warehouse_name');
            }

            if (!Schema::hasColumn('challan_details', 'responsible_user_id')) {
                $table->foreignId('responsible_user_id')->nullable()->after('warehouse_handler_name')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('challan_details', 'vehicle_number')) {
                $table->string('vehicle_number')->nullable()->after('responsible_user_id');
            }

            if (!Schema::hasColumn('challan_details', 'destination')) {
                $table->string('destination')->nullable()->after('vehicle_number');
            }

            if (!Schema::hasColumn('challan_details', 'delivery_expenses')) {
                $table->decimal('delivery_expenses', 15, 2)->default(0)->after('destination');
            }

            if (!Schema::hasColumn('challan_details', 'notification_sent_at')) {
                $table->timestamp('notification_sent_at')->nullable()->after('delivery_expenses');
            }
        });
    }

    public function down(): void
    {
        Schema::table('challan_details', function (Blueprint $table) {
            if (Schema::hasColumn('challan_details', 'responsible_user_id')) {
                $table->dropConstrainedForeignId('responsible_user_id');
            }

            $columns = [
                'broker_name',
                'broker_phone',
                'warehouse_name',
                'warehouse_handler_name',
                'vehicle_number',
                'destination',
                'delivery_expenses',
                'notification_sent_at',
            ];

            $existingColumns = array_filter($columns, fn ($column) => Schema::hasColumn('challan_details', $column));

            if (!empty($existingColumns)) {
                $table->dropColumn($existingColumns);
            }
        });
    }
};
