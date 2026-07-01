<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publication_store', function (Blueprint $table) {
            $table->id();
            $table->foreignId('publication_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('ls_product_id', 100)->nullable();
            $table->string('ls_variant_id', 100)->nullable();
            $table->text('ls_store_url')->nullable();
            $table->string('price_display', 50)->nullable();
            $table->char('currency', 3)->default('SGD');
            $table->text('free_sample_url')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_store');
    }
};
