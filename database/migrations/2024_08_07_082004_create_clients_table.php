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
        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('num')->nullable();
            $table->float('gredit')->nullable();
            $table->float('oldCredit')->nullable();
            $table->float('achat')->nullable();
            $table->float('accompte')->nullable();
            $table->float('resteApayer')->nullable();
            $table->string('designation')->nullable();
            $table->timestamp('date')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
