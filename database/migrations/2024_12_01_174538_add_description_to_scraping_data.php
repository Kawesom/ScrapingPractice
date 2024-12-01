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
        Schema::table('scraping_data', function (Blueprint $table) {
            $table->text('description')->nullable();
            $table->string('upc')->nullable();
            $table->string('availability')->nullable();
            $table->string('link');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('scraping_data', function (Blueprint $table) {
            //
        });
    }
};
