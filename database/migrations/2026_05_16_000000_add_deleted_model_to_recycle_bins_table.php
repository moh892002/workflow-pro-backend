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
            $table->string('deleted_model')->after('deleted_table_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recycle_bins', function (Blueprint $table) {
            $table->dropColumn('deleted_model');
        });
    }
};