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
        Schema::create('iso4217', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->integer('number');
            $table->integer('decimal');
            $table->string('currency');
            $table->text('currency_locations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('iso4217');
    }
};
