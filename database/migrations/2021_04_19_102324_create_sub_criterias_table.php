<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubCriteriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sub_criterias', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->unsignedBigInteger("criteria_id");
            $table->integer("point")->default(0);
            $table->timestamps();

            $table->foreign("criteria_id")->references("id")->on("criterias");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('sub_criterias', function (Blueprint $table) {
            Schema::dropIfExists('sub_criterias');
        });
    }
}
