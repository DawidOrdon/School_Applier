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
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->references('id')->on('schools');
            $table->string('name',50);
            $table->string('desc',255);
            $table->integer('slots');
            $table->foreignId('school_type')->references('id')->on('schools_types');
            $table->foreignId('subject1')->references('id')->on('subjects');
            $table->foreignId('subject2')->references('id')->on('subjects');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
