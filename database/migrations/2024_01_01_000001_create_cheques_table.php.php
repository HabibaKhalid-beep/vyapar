<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cheques', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default('sale');          // sale | purchase | payment_in | payment_out | other
            $table->string('name');                           // party / description
            $table->string('ref_no')->nullable();             // cheque number
            $table->date('transaction_date');
            $table->date('cheque_date')->nullable();
            $table->decimal('amount', 15, 2)->default(0);
            $table->string('status')->default('open');        // open | deposited | bounced | cancelled
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->string('reference_type')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('deposited_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'cheque_date']);
            $table->index(['reference_type', 'reference_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cheques');
    }
};