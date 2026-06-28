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
        Schema::create('links', function (Blueprint $table) {
            $table->id();
            $table->string('label', 255);
            $table->text('url');
            $table->enum('target', ['_self', '_blank'])->default('_self');
            $table->string('rel', 100)->default('noopener noreferrer');
            $table->text('description')->nullable();
            $table->enum('category', ['general', 'social', 'nav'])->default('general');
            $table->string('icon_class', 100)->nullable();
            $table->smallInteger('sort_order')->unsigned()->default(0);
            $table->boolean('active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('links');
    }
};
