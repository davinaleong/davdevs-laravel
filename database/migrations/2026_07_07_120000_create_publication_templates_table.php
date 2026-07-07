<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publication_templates', function (Blueprint $table) {
            $table->id();
            $table->enum('publication_type', ['ebook'])->default('ebook');
            $table->string('name', 200);
            $table->string('slug', 200);
            $table->string('blade_path', 500);
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);

            $table->unique(['publication_type', 'slug']);
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_templates');
    }
};
