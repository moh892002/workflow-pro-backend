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
        Schema::create('salary_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->string('transaction_type');
            $table->decimal('amount', 10, 2);
            $table->date('transaction_date');
            $table->string('notes')->nullable();
            // $table->foreignId('processed_by')->constrained('users');
            $table->softDeletes();
            $table->index('transaction_type');
            $table->index('transaction_date');
            $table->index(['user_id', 'transaction_date']);
            $table->index(['transaction_type', 'transaction_date']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_records');
    }
};
