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
        Schema::table('files_uploaded', function (Blueprint $table) {
            $table->integer('completion_percentage')->after('status')->nullable()->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files_uploaded', function (Blueprint $table) {
            $table->removeColumn('completion_percentage');
        });
    }
};
