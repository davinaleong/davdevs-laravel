<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('social_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->morphs('broadcastable');             // entry/publication polymorphic
            $table->enum('platform', ['linkedin', 'facebook', 'instagram', 'threads']);
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->text('post_url')->nullable();        // URL of the created post (if returned)
            $table->text('error')->nullable();
            $table->timestamps();

            $table->index(['platform', 'status']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_broadcasts');
    }
};
