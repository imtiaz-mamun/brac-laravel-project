<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('repayments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('restrict');
            $table->date('payment_date');
            $table->decimal('amount_paid', 15, 2);
            $table->enum('payment_mode', ['CASH', 'BANK', 'MOBILE']);
            $table->string('reference_no')->nullable();
            $table->timestamps();

            $table->index(['loan_id', 'payment_date']);
            $table->index(['payment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repayments');
    }
};
