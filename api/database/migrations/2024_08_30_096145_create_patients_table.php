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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('national_code', 10);
            $table->string('birthday', 10);
            $table->enum('gender', ['male', 'female']);
            $table->text('desc')->nullable();
            $table->foreignId('status')->constrained('patient_statuses');
            $table->foreignId('province')->constrained('provinces');
            $table->foreignId('city')->constrained('cities');
            $table->foreignId('lead_source')->constrained('lead_sources');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};