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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('room_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('price_per_month', 12, 2);
            $table->string('room_size')->nullable();
            $table->integer('floor')->nullable();
            $table->integer('capacity')->default(1);
            $table->integer('status_id')->default(1); // 1: Available, 2: Reserved, 3: Occupied, 4: Maintenance
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
