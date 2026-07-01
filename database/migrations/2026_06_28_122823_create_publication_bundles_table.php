<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('publication_bundles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bundle_id')->constrained('publications')->cascadeOnDelete();
            $table->foreignId('member_id')->constrained('publications')->cascadeOnDelete();
            $table->unsignedSmallInteger('sort_order')->default(0);

            $table->unique(['bundle_id', 'member_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_bundles');
    }
};
