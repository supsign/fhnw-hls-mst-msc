<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('slots', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->timestamps();
        });

        Schema::create('clusters', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('core_competences')->nullable();
            $table->timestamps();
        });

        Schema::create('specializations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cluster_id')->constrained();
            $table->string('name');
            $table->string('short_name');
            $table->timestamps();
        });

        Schema::create('theses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('specialization_id')->constrained();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('course_groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('internal_name');
            $table->unsignedInteger('required_courses_count');
            $table->timestamps();
        });

        Schema::create('course_group_specialization', function (Blueprint $table) {
            $table->foreignId('course_group_id')->constrained();
            $table->foreignId('specialization_id')->constrained();
            $table->timestamps();
            $table->unique(['course_group_id', 'specialization_id'], 'cgs_course_group_id_specialization_id_unique');
        });        

        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cluster_id')->nullable()->constrained();
            $table->foreignId('as_slot_id')->nullable()->constrained('slots');
            $table->foreignId('ss_slot_id')->nullable()->constrained('slots');
            $table->foreignId('specialization_id')->nullable()->constrained();
            $table->foreignId('venue_id')->constrained();
            $table->string('name');
            $table->string('internal_name');
            $table->string('short_name');
            $table->string('content');
            $table->unsignedInteger('ects');
            $table->boolean('block');
            $table->timestamps();
        });

        Schema::create('course_course_group', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->constrained();
            $table->foreignId('course_group_id')->nullable()->constrained();
            $table->timestamps();
            $table->unique(['course_id', 'course_group_id']);
        });

        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('url');
            $table->timestamps();
        });

        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('content');
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
        Schema::dropIfExists('page_contents');
        Schema::dropIfExists('links');
        Schema::dropIfExists('course_course_group');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_group_specialization');
        Schema::dropIfExists('course_groups');
        Schema::dropIfExists('theses');
        Schema::dropIfExists('specializations');
        Schema::dropIfExists('clusters');
        Schema::dropIfExists('slots');
        Schema::dropIfExists('venues');
    }
};
