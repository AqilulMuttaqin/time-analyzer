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
        Schema::create('effective', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->integer('week');
            $table->enum('shift', ['A', 'B']);
            $table->float('standart');
            $table->float('indirect');
            $table->float('overtime');
            $table->float('reguler_eh');
            $table->foreignId('id_subgolongan')->constrained('subgolongan');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('effective');
    }
};
