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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->string('name_company');
            $table->string('description_company');
            $table->string('picture_company', 255);
            $table->decimal('zipcode', 5, 0);
            $table->string('phone');
            $table->string('address', 150);
            $table->decimal('siret', 14, 0);
            $table->string('town', 100);
            $table->string('lat');
            $table->string('long');
            $table->bigInteger('id_user');
            $table->foreign('id_user')
                ->references('id')
                ->on('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cards');
    }
};
