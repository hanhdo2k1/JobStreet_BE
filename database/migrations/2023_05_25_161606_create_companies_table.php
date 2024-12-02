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
            $table->integer("id")->autoIncrement();
            $table->string("company_name",150)->unique();
            $table->string("logo");
            $table->string("scale",20);
            $table->string("description");
            $table->string("website");
            $table->string("email")->unique();
            $table->string("password");
            $table->string("token");
            $table->string("address",250);
            $table->string("number_phone",20);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
