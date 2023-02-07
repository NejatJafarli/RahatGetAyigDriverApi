<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();
            $table->string('fullname');
            $table->string('phone')->unique();
            $table->integer('age')->nullable();
            $table->string('password');
            $table->string('photo')->nullable();
            $table->string('email')->unique()->nullable();
            $table->integer('status')->default(1); // if status = 1 then driver is active
            //fin code
            $table->string('fin_code')->nullable()->unique();
            //want reservation works 
            $table->integer('want_reservation')->default(0);
            $table->float('balance')->default(0);
            //Driver license number
            $table->string('license_number')->nullable()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('drivers');
    }
}
