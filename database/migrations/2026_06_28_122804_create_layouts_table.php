<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layouts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('slug', 100)->unique();
            $table->string('blade_component', 200);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('preview_image_id')->nullable();
            $table->boolean('active')->default(true);

            $table->foreign('preview_image_id')->references('id')->on('images')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layouts');
    }
};
