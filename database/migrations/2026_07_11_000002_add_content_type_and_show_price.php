<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add content_type_id to publications so publications can be categorised
        // using the same ContentType system as entries (table_target = 'publications').
        Schema::table('publications', function (Blueprint $table) {
            $table->unsignedBigInteger('content_type_id')->nullable()->after('layout_id');
            $table->foreign('content_type_id')->references('id')->on('content_types')->nullOnDelete();
        });

        // Add show_price toggle to content_types so each publication content type
        // can independently control whether prices are displayed on the frontend.
        Schema::table('content_types', function (Blueprint $table) {
            $table->boolean('show_price')->default(true)->after('listed');
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropForeign(['content_type_id']);
            $table->dropColumn('content_type_id');
        });

        Schema::table('content_types', function (Blueprint $table) {
            $table->dropColumn('show_price');
        });
    }
};
