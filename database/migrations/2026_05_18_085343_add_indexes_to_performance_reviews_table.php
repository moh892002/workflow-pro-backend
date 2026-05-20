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
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('reviewer_id');
            $table->index('review_period');
            $table->index('status');
            $table->index(['user_id', 'review_period']);
            $table->index(['reviewer_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['reviewer_id']);
            $table->dropIndex(['review_period']);
            $table->dropIndex(['status']);
            $table->dropIndex(['user_id', 'review_period']);
            $table->dropIndex(['reviewer_id', 'status']);
        });
    }
};
