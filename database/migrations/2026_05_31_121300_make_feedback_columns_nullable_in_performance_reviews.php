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
            $table->text('ai_generated_feedback')->nullable()->change();
            $table->text('final_feedback')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->text('ai_generated_feedback')->nullable(false)->change();
            $table->text('final_feedback')->nullable(false)->change();
        });
    }
};
