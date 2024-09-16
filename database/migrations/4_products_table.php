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
            $table->string('name_product');
            $table->string('picture_product');
            $table->decimal('price', 9, 2);
            $table->string('description_product');
            $table->bigInteger('id_category');
            $table->bigInteger('id_company');
            $table->foreign('id_category')
                ->references('id')
                ->on('categories');
            $table->foreign('id_company')
                ->references('id')
                ->on('companies');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};