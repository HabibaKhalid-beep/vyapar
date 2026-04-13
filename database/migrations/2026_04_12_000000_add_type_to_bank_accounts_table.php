<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->string('type')->default('bank')->after('display_name');
        });

        DB::table('bank_accounts')
            ->whereNull('type')
            ->update(['type' => 'bank']);

        $cashAccount = DB::table('bank_accounts')
            ->where('type', 'cash')
            ->first();

        if (!$cashAccount) {
            DB::table('bank_accounts')->insert([
                'display_name' => 'Cash in Hand',
                'type' => 'cash',
                'opening_balance' => 0,
                'bank_name' => 'Cash',
                'account_holder_name' => 'Cash',
                'print_on_invoice' => false,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down()
    {
        Schema::table('bank_accounts', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
