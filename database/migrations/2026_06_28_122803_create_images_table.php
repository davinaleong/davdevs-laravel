<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            $table->unsignedSmallInteger('width')->nullable();
            $table->unsignedSmallInteger('height')->nullable();
            $table->string('format', 20)->nullable();
            $table->unsignedInteger('bytes')->nullable();
            $table->boolean('qr_code')->default(false);
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('images');
    }
};
