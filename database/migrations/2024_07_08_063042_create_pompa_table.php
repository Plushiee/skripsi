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
        Schema::create('pompa', function (Blueprint $table) {
            $table->id()->autoIncrement()->primary();
            $table->unsignedBigInteger('id_area');
            $table->string('status');
            $table->integer('suhu')->nullable();
            $table->boolean('otomatis');
            $table->timestamps();

            $table->foreign('id_area')->references('id_area')->on('area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pompa');
    }
};
