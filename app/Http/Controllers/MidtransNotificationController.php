<?php

namespace App\Http\Controllers;

use App\Models\Tiket;
use App\Services\MidtransService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MidtransNotificationController extends Controller
{
    public function __invoke(Request $request)
    {
        $payload = $request->all();
        $orderId = $payload['order_id'] ?? null;

        if (! $orderId) {
            return response()->json(['message' => 'Invalid payload'], 400);
        }

        $midtrans = app(MidtransService::class);
        if (! $midtrans->verifyNotification($payload)) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $transactionStatus = $payload['transaction_status'] ?? '';
        $fraudStatus = $payload['fraud_status'] ?? '';

        $tiket = Tiket::where('midtrans_order_id', $orderId)->first();
        if (! $tiket) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        if (in_array($transactionStatus, ['capture', 'settlement']) && $fraudStatus === 'accept') {
            if ($tiket->status !== 'paid') {
                $tiket->update([
                    'status' => 'paid',
                    'midtrans_transaction_id' => $payload['transaction_id'] ?? null,
                ]);

                // ── Increment counter notifikasi untuk admin wisata & pengelola ──
                cache()->increment('notif_admin_' . $tiket->id_wisata);
                cache()->increment('notif_pengelola');

                // Kirim notifikasi WhatsApp
                $wa = new \App\Services\WhatsAppService();
                $tiket->load('user', 'wisata');
                if ($tiket->user && $tiket->user->no_hp) {
                    $tanggalBerkunjung = Carbon::parse($tiket->tanggal_berkunjung);
                    $pesan = "Halo {$tiket->user->name},\n\nPembayaran via Midtrans sebesar *Rp " . number_format((float) $tiket->total_harga, 0, ',', '.') . "* untuk wisata *{$tiket->wisata->nama}* berhasil dikonfirmasi.\n\nKode Transaksi: {$tiket->midtrans_order_id}\nKode Tiket: *{$tiket->kode_tiket}*\nTanggal Kunjungan: {$tanggalBerkunjung->format('d/m/Y')}\nJumlah Pengunjung: *{$tiket->jumlah}* orang\n\nSilahkan tunjukkan QR Code Tiket kepada petugas saat masuk.";
                    
                    $encodedContent = urlencode($tiket->qr_content ?? $tiket->kode_tiket);
                    $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=240x240&data={$encodedContent}";
                    
                    $wa->sendMedia($tiket->user->no_hp, $pesan, $qrUrl);
                }
            }
        } elseif (in_array($transactionStatus, ['pending'])) {
            // Tetap pending
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel']) || $fraudStatus === 'deny') {
            $tiket->update(['status' => 'cancelled']);
        }

        return response()->json(['message' => 'OK']);
    }
}
