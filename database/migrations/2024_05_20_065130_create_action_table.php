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
        Schema::create('action', function (Blueprint $table) {
            $table->id();
            $table->string('action');
            $table->string('pic');
            $table->date('due_date');
            $table->foreignId('id_concern')->constrained('concern');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action');
    }
};
