<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            $table->char('ulid', 26)->unique();
            $table->foreignId('layout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('og_image_id')->nullable();
            $table->unsignedBigInteger('cover_image_id')->nullable();
            $table->enum('publication_type', ['ebook'])->default('ebook');
            $table->string('title', 500);
            $table->string('slug', 500)->unique();
            $table->string('tagline', 500)->nullable();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->enum('status', ['draft', 'private', 'published', 'archived'])->default('draft');
            $table->boolean('featured')->default(false);
            $table->boolean('bundle')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('og_image_id')->references('id')->on('images')->nullOnDelete();
            $table->foreign('cover_image_id')->references('id')->on('images')->nullOnDelete();
            $table->index('status');
            $table->index('publication_type');
            $table->index('featured');
            $table->index('bundle');

            if ($this->supportsFullText()) {
                $table->fullText(['title', 'tagline', 'excerpt']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publications');
    }

    protected function supportsFullText(): bool
    {
        return Schema::getConnection()->getDriverName() === 'mysql';
    }
};
