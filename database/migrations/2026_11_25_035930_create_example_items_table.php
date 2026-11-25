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
        Schema::create('example_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('example_id');
            $table->string('item_name');
            $table->decimal('quantity',15,2);
            $table->decimal('rate',15,2);
            $table->decimal('amount',15,2);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('example_items');
    }
};
