<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->foreignId('publication_template_id')->nullable()->after('layout_id')
                ->constrained('publication_templates')->nullOnDelete();
            $table->foreignId('react_component_id')->nullable()->after('publication_template_id')
                ->constrained('react_components')->nullOnDelete();

            $table->index('publication_template_id');
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropConstrainedForeignId('publication_template_id');
            $table->dropConstrainedForeignId('react_component_id');
        });
    }
};
