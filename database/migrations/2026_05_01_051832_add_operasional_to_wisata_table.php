<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('Wisata', function (Blueprint $table) {
            $table->time('jam_buka')->nullable()->after('harga_camping');
            $table->time('jam_tutup')->nullable()->after('jam_buka');
            // JSON array hari buka: ["Senin","Selasa","Rabu","Kamis","Jumat","Sabtu","Minggu"]
            $table->json('hari_buka')->nullable()->after('jam_tutup');
            // JSON array tanggal tutup: ["2026-08-17","2026-01-01"]
            $table->json('tanggal_tutup')->nullable()->after('hari_buka');
        });
    }

    public function down(): void
    {
        Schema::table('Wisata', function (Blueprint $table) {
            $table->dropColumn(['jam_buka', 'jam_tutup', 'hari_buka', 'tanggal_tutup']);
        });
    }
};
