<?php

use App\Models\App;
use App\Services\PasswordService;
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
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('type');
            $table->timestamp('start_date')->unique();
            $table->timestamps();
        });

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
            $table->unsignedInteger('type');
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
            $table->foreignId('end_semester_id')->nullable()->constrained('semesters');
            $table->foreignId('slot_id')->nullable()->constrained();
            $table->foreignId('specialization_id')->nullable()->constrained();
            $table->foreignId('start_semester_id')->nullable()->constrained('semesters');
            $table->foreignId('venue_id')->nullable()->constrained();
            $table->unsignedInteger('semester_type');
            $table->string('name');
            $table->string('internal_name');
            $table->string('short_name')->nullable();
            $table->boolean('block');
            $table->text('content');
            $table->unsignedInteger('ects');
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
            $table->string('name')->unique();
            $table->string('url');
            $table->timestamps();
        });

        Schema::create('page_contents', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('app', function (Blueprint $table) {
            $table->id();
            $table->string('admin_password');
            $table->timestamps();
        });

        App::create([
            'id' => 1,
            'admin_password' => PasswordService::hash(env('ADMIN_PASSWORD'))
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app');
        Schema::dropIfExists('page_contents');
        Schema::dropIfExists('links');
        Schema::dropIfExists('course_course_group');
        Schema::dropIfExists('course_semester');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('course_group_specialization');
        Schema::dropIfExists('course_groups');
        Schema::dropIfExists('theses');
        Schema::dropIfExists('specializations');
        Schema::dropIfExists('clusters');
        Schema::dropIfExists('slots');
        Schema::dropIfExists('venues');
        Schema::dropIfExists('semesters');
    }
};
