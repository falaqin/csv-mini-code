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
            $table->string('stored_filesize')->after('stored_filename');
            $table->string('stored_filehash')->after('stored_filesize');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('files_uploaded', function (Blueprint $table) {
            $table->removeColumn('stored_filesize');
            $table->removeColumn('stored_filehash');
        });
    }
};
