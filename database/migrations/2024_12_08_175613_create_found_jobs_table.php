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
        Schema::create('job_sources', function (Blueprint $table) {
            $table->id('job_source_id');
            $table->string('source_name');
            $table->string('channel_or_group')->nullable();
            $table->boolean('requires_third_party_login')->default(false);
            $table->unsignedBigInteger('no_of_applications')->default(0);
            $table->unsignedBigInteger('no_of_jobs')->default(0);
            $table->string('scrape_url')->nullable();
            $table->string('last_crawled')->nullable();
            $table->timestamps();
        });

        Schema::create('found_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('job_name');
            $table->foreignId('source_id')->constrained('job_sources', 'job_source_id');
            $table->string('company_name')->nullable();//separate table later
            $table->string('industry')->nullable();
            $table->string('job_url', 1024)->nullable();
            $table->string('employer_image')->nullable();
            $table->string('salary')->nullable();//split into 2 columns later
            $table->longText('description');
            $table->text('requirements')->nullable();//might change later
            $table->enum('gender_preference',['male','female','none'])->default('none');
            $table->bigInteger('people_applied')->default(0);//might change to int later
            $table->string('years_of_experience');
            //add currency later
            $table->enum('worker_looking_for',['regular','contract','internship','corper','apprenticeship'])->default('regular');
            $table->integer('min_age')->nullable();
            $table->integer('max_age')->nullable();
            $table->string('city_or_province')->nullable();
            $table->string('qualification')->nullable();
            $table->string('materials_to_apply')->nullable();//array
            $table->string('state');
            $table->string('country');
            $table->string('field_hiring_for');
            $table->json('tags');//maybe array
            $table->string('job_type');
            $table->json('relevant_fields');//might change type later
            $table->string('source');//separate table later
            $table->string('email_to_apply')->nullable();
            $table->string('link_to_apply')->nullable();
            $table->timestamp('date_posted');
            $table->timestamp('deadline')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_sources');
        Schema::dropIfExists('found_jobs');
    }
};
