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
        Schema::create('product', function (Blueprint $table) {
            $table->id()->comment('Gather UNIQUE_KEY from the CSV.');
            $table->string('title');
            $table->string('description');
            $table->string('style');
            $table->string('sanmar_mainframe_color')->nullable();
            $table->string('size');
            $table->string('color_name');
            $table->decimal('piece_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product');
    }
};
