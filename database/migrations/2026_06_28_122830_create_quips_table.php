<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quips', function (Blueprint $table) {
            $table->id();
            $table->enum('variant', ['qa', 'statement']);
            $table->text('question')->nullable();
            $table->text('punchline');
            $table->boolean('active')->default(true);
            $table->softDeletes();

            $table->index('variant');
            $table->index('active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quips');
    }
};
