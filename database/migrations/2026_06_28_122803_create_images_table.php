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
        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('cloudinary_id', 500)->unique();
            $table->text('url');
            $table->string('title', 500)->nullable();
            $table->string('alt', 500)->nullable();
            $table->text('caption')->nullable();
            $table->string('credit', 300)->nullable();
            $table->smallInteger('width')->unsigned()->nullable();
            $table->smallInteger('height')->unsigned()->nullable();
            $table->string('format', 20)->nullable();
            $table->integer('bytes')->unsigned()->nullable();
            $table->boolean('qr_code')->default(false);
            $table->timestamp('deleted_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
