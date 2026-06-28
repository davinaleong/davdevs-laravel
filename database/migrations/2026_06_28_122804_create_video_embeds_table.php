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
        Schema::create('video_embeds', function (Blueprint $table) {
            $table->id();
            $table->string('video_id', 20)->unique();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->string('channel_name', 255)->nullable();
            $table->text('thumbnail_url')->nullable();
            $table->smallInteger('duration_seconds')->unsigned()->nullable();
            $table->date('published_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_embeds');
    }
};
