<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            $table->enum('reactionable_type', ['entry', 'publication']);
            $table->unsignedBigInteger('reactionable_id');
            $table->char('token_hash', 64);
            $table->char('ip_hash', 64);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['reactionable_type', 'reactionable_id', 'token_hash']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
