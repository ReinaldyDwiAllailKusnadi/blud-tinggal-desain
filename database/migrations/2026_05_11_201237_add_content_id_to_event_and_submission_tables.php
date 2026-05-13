<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->unsignedBigInteger('content_id')->nullable()->after('location');
            $table->foreign('content_id')->references('id')->on('content')->onDelete('set null');
        });

        Schema::table('submission', function (Blueprint $table) {
            $table->unsignedBigInteger('content_id')->nullable()->after('location');
            $table->foreign('content_id')->references('id')->on('content')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->dropForeign(['content_id']);
            $table->dropColumn('content_id');
        });

        Schema::table('submission', function (Blueprint $table) {
            $table->dropForeign(['content_id']);
            $table->dropColumn('content_id');
        });
    }
};
