<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entries', function (Blueprint $table) {
            $table->id();
            $table->char('ulid', 26)->unique();
            $table->foreignId('content_type_id')->constrained()->cascadeOnDelete();
            $table->foreignId('layout_id')->constrained()->cascadeOnDelete();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->unsignedBigInteger('og_image_id')->nullable();
            $table->string('title', 500);
            $table->string('slug', 500)->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->unsignedTinyInteger('read_time')->default(1);
            $table->enum('status', ['draft', 'private', 'published', 'archived'])->default('draft');
            $table->enum('visibility', ['public', 'unlisted'])->default('public');
            $table->boolean('featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('og_image_id')->references('id')->on('images')->nullOnDelete();
            $table->index('status');
            $table->index('published_at');
            $table->index('content_type_id');
            $table->index('featured');

            if ($this->supportsFullText()) {
                $table->fullText(['title', 'excerpt', 'body']);
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entries');
    }

    protected function supportsFullText(): bool
    {
        return Schema::getConnection()->getDriverName() === 'mysql';
    }
};
