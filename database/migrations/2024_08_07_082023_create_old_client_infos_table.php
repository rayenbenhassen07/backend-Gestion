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
        Schema::create('old_client_infos', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->float('credit')->nullable();
            $table->float('accompte')->nullable();
            $table->float('achat')->nullable();
            $table->float('resteAPayer')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('old_client_infos');
    }
};
