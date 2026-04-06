<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->string('city')->nullable()->after('email');
            $table->text('address')->nullable()->after('city');
            $table->string('ptcl_number', 30)->nullable()->after('phone');
            $table->string('party_group')->nullable()->after('party_type');
            $table->decimal('credit_limit_amount', 15, 2)->nullable()->after('credit_limit_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('parties', function (Blueprint $table) {
            $table->dropColumn([
                'city',
                'address',
                'ptcl_number',
                'party_group',
                'credit_limit_amount',
            ]);
        });
    }
};
