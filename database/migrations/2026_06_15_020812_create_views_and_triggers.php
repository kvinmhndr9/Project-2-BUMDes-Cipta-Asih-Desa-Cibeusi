<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Buat VIEW untuk rekapitulasi data tiket
        DB::statement("
            CREATE OR REPLACE VIEW view_laporan_transaksi AS
            SELECT 
                t.id_tiket,
                t.kode_tiket,
                u.name AS nama_pengunjung,
                w.nama AS nama_wisata,
                t.jumlah,
                t.total_harga,
                t.status,
                t.tanggal_berkunjung,
                t.created_at
            FROM Tiket t
            JOIN User u ON t.id_user = u.id_user
            JOIN Wisata w ON t.id_wisata = w.id_wisata
        ");

        // 2. Buat TRIGGER pengurangan stok otomatis saat tiket dibuat
        DB::unprepared("
            CREATE TRIGGER trigger_kurangi_stok_wisata
            AFTER INSERT ON Tiket
            FOR EACH ROW
            BEGIN
                UPDATE Wisata
                SET stok = stok - NEW.jumlah
                WHERE id_wisata = NEW.id_wisata;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP TRIGGER IF EXISTS trigger_kurangi_stok_wisata");
        DB::statement("DROP VIEW IF EXISTS view_laporan_transaksi");
    }
};
