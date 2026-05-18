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
        Schema::table('salary_records', function (Blueprint $table) {
            $table->index('transaction_type');
            $table->index('transaction_date');
            $table->index(['user_id', 'transaction_date']);
            $table->index(['transaction_type', 'transaction_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_records', function (Blueprint $table) {
            $table->dropIndex(['transaction_type']);
            $table->dropIndex(['transaction_date']);
            $table->dropIndex(['user_id', 'transaction_date']);
            $table->dropIndex(['transaction_type', 'transaction_date']);
        });
    }
};
