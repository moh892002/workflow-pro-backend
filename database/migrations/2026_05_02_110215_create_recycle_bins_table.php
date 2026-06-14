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
        Schema::create('recycle_bins', function (Blueprint $table) {
            $table->id();
            $table->string('deleted_table_name');
            $table->string('deleted_model')->nullable();
            $table->unsignedBigInteger('deleted_item_id');
            $table->jsonb('deleted_data'); // Storing the full object state
            $table->timestamp('deleted_at')->nullable();
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->index('deleted_model');
            $table->index('deleted_table_name');
            $table->index('deleted_at');
            $table->index(['deleted_model', 'deleted_table_name']);
            $table->index(['deleted_model', 'deleted_item_id']);
            $table->index(['deleted_at', 'deleted_by']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('recycle_bins');
    }
};
