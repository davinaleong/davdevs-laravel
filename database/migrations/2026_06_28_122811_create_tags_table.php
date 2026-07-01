<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_type_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('scope', ['entries', 'publications', 'all'])->default('entries');
            $table->string('name', 100);
            $table->string('slug', 100);

            $table->unique(['content_type_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tags');
    }
};
