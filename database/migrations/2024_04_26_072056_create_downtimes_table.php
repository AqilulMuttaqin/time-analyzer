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
        Schema::create('downtime', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->integer('week');
            $table->enum('shift', ['A', 'B']);
            $table->foreignId('id_subgolongan')->constrained('subgolongan');
            $table->foreignId('id_downtimecode')->constrained('downtimecode');
            $table->string('detail')->nullable();
            $table->integer('minute');
            $table->float('man_hours');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('downtime');
    }
};
