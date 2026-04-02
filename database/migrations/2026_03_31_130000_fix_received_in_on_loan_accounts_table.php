<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE loan_accounts
            MODIFY received_in BIGINT UNSIGNED NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE loan_accounts
            MODIFY received_in ENUM('1','2') NOT NULL DEFAULT '1'
        ");
    }
};
