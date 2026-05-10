<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            if (!Schema::hasColumn('parties', 'city')) {
                $table->string('city')->nullable()->after('email');
            }
            if (!Schema::hasColumn('parties', 'address')) {
                $table->text('address')->nullable()->after('city');
            }
            if (!Schema::hasColumn('parties', 'ptcl_number')) {
                $table->string('ptcl_number', 30)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('parties', 'party_group')) {
              $table->string('party_group')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('parties', 'credit_limit_amount')) {
                $table->decimal('credit_limit_amount', 15, 2)->nullable()->after('credit_limit_enabled');
            }
        });
    }

    public function down(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->dropColumn(array_filter([
                Schema::hasColumn('parties', 'city') ? 'city' : null,
                Schema::hasColumn('parties', 'address') ? 'address' : null,
                Schema::hasColumn('parties', 'ptcl_number') ? 'ptcl_number' : null,
                Schema::hasColumn('parties', 'party_group') ? 'party_group' : null,
                Schema::hasColumn('parties', 'credit_limit_amount') ? 'credit_limit_amount' : null,
            ]));
        });
    }
};