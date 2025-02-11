<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('domain_type');
            $table->bigInteger('club_id')->unsigned()->index();
            $table->foreign('club_id')->references('id')->on('clubs');
            $table->string('site_id');
            $table->string('site_name');
            $table->string('course_id');
            $table->string('course_name');
            $table->integer('course_number');
            $table->integer('first_hole');
            $table->integer('hole_count');
            $table->text('site_settings');
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
        Schema::dropIfExists('courses');
    }
}
