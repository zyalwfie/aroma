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
      Schema::create('products', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('description')->nullable();
        $table->integer('price');
        $table->integer('stock')->default(0);
        $table->enum('status', ['draft', 'published'])->default('draft');
        $table->unsignedBigInteger('category_id');
        $table->string('image')->nullable();
        $table->integer('weight')->default(0); // dalam gram
        $table->timestamps();

        $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
