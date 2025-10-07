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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('restrict');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('restrict');
            $table->decimal('loan_amount', 15, 2);
            $table->decimal('interest_rate', 5, 2); // percentage
            $table->date('issue_date');
            $table->integer('tenure_months');
            $table->enum('status', ['ACTIVE', 'CLOSED', 'DEFAULTED'])->default('ACTIVE');
            $table->timestamps();

            $table->index(['client_id', 'status']);
            $table->index(['branch_id', 'issue_date']);
            $table->index(['status', 'issue_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
