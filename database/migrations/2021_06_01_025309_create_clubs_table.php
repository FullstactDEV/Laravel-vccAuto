<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClubsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clubs', function (Blueprint $table) {
            $table->id();
            $table->string('domain_type');
            $table->string('username');
            $table->string('password');
            $table->string('site_id');
            $table->string('site_name')->nullable();
            $table->boolean('is_internal');
            $table->boolean('is_eu_site');
            $table->boolean('is_chinese_site');
            $table->string('center_point_latitude')->nullable();
            $table->string('center_point_longitude')->nullable();
            $table->boolean('is_active')->default(true);
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
        Schema::dropIfExists('clubs');
    }
}
