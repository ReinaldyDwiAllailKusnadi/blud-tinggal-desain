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
        Schema::table('content', function (Blueprint $table) {
            // capacity digunakan untuk kapasitas maksimal peserta.
            $table->unsignedInteger('capacity')->nullable()->after('close_time');
            
            // venue_type digunakan untuk tipe lokasi seperti aula, taman, lapangan, pendopo, indoor, outdoor.
            $table->string('venue_type')->nullable()->after('capacity');
            
            // is_indoor digunakan untuk menandai lokasi indoor.
            $table->boolean('is_indoor')->default(false)->after('venue_type');
            
            // is_outdoor digunakan untuk menandai lokasi outdoor.
            $table->boolean('is_outdoor')->default(false)->after('is_indoor');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('content', function (Blueprint $table) {
            $table->dropColumn(['capacity', 'venue_type', 'is_indoor', 'is_outdoor']);
        });
    }
};
