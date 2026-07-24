<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publication_store', function (Blueprint $table) {
            $table->dropColumn(['ls_product_id', 'ls_variant_id']);
        });

        Schema::table('publication_variants', function (Blueprint $table) {
            $table->dropColumn('ls_product_id');
        });
    }

    public function down(): void
    {
        Schema::table('publication_store', function (Blueprint $table) {
            $table->string('ls_product_id', 100)->nullable()->after('publication_id');
            $table->string('ls_variant_id', 100)->nullable()->after('ls_product_id');
        });

        Schema::table('publication_variants', function (Blueprint $table) {
            $table->string('ls_product_id', 100)->nullable()->after('price_display');
        });
    }
};
