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
        Schema::table('recycle_bins', function (Blueprint $table) {
            $table->index('deleted_model');
            $table->index('deleted_table_name');
            $table->index('deleted_at');
            $table->index(['deleted_model', 'deleted_table_name']);
            $table->index(['deleted_model', 'deleted_item_id']);
            $table->index(['deleted_at', 'deleted_by']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recycle_bins', function (Blueprint $table) {
            $table->dropIndex(['deleted_model']);
            $table->dropIndex(['deleted_table_name']);
            $table->dropIndex(['deleted_at']);
            $table->dropIndex(['deleted_model', 'deleted_table_name']);
            $table->dropIndex(['deleted_model', 'deleted_item_id']);
            $table->dropIndex(['deleted_at', 'deleted_by']);
        });
    }
};
