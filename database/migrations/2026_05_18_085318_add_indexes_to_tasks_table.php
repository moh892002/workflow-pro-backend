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
        Schema::table('tasks', function (Blueprint $table) {
            $table->index('status');
            $table->index('priority');
            $table->index('deadline_date');
            $table->index(['status', 'priority']);
            $table->index(['assigned_to', 'status']);
            $table->index(['deadline_date', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropIndex(['status']);
            $table->dropIndex(['priority']);
            $table->dropIndex(['deadline_date']);
            $table->dropIndex(['status', 'priority']);
            $table->dropIndex(['assigned_to', 'status']);
            $table->dropIndex(['deadline_date', 'status']);
        });
    }
};
