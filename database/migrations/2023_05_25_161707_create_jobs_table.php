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
        Schema::create('jobs', function (Blueprint $table) {
            $table->integer("id")->autoIncrement();
            $table->integer("company_id");
            $table->integer("cat_id");
            $table->string("position",50);
            $table->string("salary",50);
            $table->string("type");
            $table->string("description");
            $table->string("status",30);
            $table->dateTime("close_day");  
            $table->foreign("company_id")->references("id")->on("companies")->onDelete("cascade");
            $table->foreign("cat_id")->references("id")->on("categories")->onDelete("cascade");
            $table->timestamps();
          
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jobs');
    }
};
