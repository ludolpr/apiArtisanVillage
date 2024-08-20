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
        Schema::create('products_tags', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('id_product');
            $table->bigInteger('id_tag');
            $table->foreign('id_product')
                ->references('id')
                ->on('products');
            $table->foreign('id_tag')
                ->references('id')
                ->on('tags');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_chats');
    }
};
