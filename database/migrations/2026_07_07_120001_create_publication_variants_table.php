<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publication_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')->constrained()->cascadeOnDelete();
            $table->string('name', 200);
            $table->string('price_display', 50)->nullable();
            $table->string('ls_product_id', 100)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->index('publication_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_variants');
    }
};
