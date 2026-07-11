<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            // Drop the existing NOT NULL foreign key constraint, then re-add as nullable.
            // This allows entries to exist without a layout (layouts belong to publications).
            $table->dropForeign(['layout_id']);
            $table->unsignedBigInteger('layout_id')->nullable()->change();
            $table->foreign('layout_id')->references('id')->on('layouts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('entries', function (Blueprint $table) {
            $table->dropForeign(['layout_id']);
            $table->unsignedBigInteger('layout_id')->nullable(false)->change();
            $table->foreignId('layout_id')->constrained()->cascadeOnDelete();
        });
    }
};
