<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->string('email')->nullable()->after('phone');
            $table->string('city')->nullable()->after('email');
            $table->enum('type', ['main', 'branch', 'storage', 'distribution'])->default('storage')->after('city');
            $table->decimal('capacity', 10, 2)->nullable()->after('type'); // in tons or sq ft
            $table->text('notes')->nullable()->after('capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropColumn(['email', 'city', 'type', 'capacity', 'notes']);
        });
    }
};
