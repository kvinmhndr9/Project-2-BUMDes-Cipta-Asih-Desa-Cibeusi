<?php

namespace App\Http\Controllers;

use App\Models\Wisata;
use App\Models\ProdukKhas;
use App\Models\User;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function wisataIndex()
    {
        $wisata = Wisata::orderBy('nama')->get();
        return view('publik.wisata-index', compact('wisata'));
    }

    public function wisataShow(Wisata $wisata)
    {
        $wisata->load(['reviews.user']);

        // Cek apakah pengunjung yang login punya tiket used untuk wisata ini
        // dan belum memberikan ulasan (untuk form ulasan langsung di halaman wisata)
        $tiketBisaUlasan = null;
        /** @var User|null $currentUser */
        $currentUser = auth()->user();
        if (auth()->check() && $currentUser && $currentUser->isPengunjung()) {
            $tiketBisaUlasan = \App\Models\Tiket::where('id_wisata', $wisata->id_wisata)
                ->where('id_user', auth()->id())
                ->where('status', 'used')
                ->whereDoesntHave('review')
                ->latest('used_at')
                ->first();
        }

        return view('publik.wisata-show', compact('wisata', 'tiketBisaUlasan'));
    }

    public function produkKhasIndex()
    {
        $produk = ProdukKhas::orderBy('urutan')->orderBy('nama')->get();
        return view('publik.produk-khas-index', compact('produk'));
    }

    public function produkKhasShow(ProdukKhas $produk_khas)
    {
        // Load gallery nanti ditambahkan di tahap selanjutnya
        return view('publik.produk-khas-show', compact('produk_khas'));
    }

    public function getWeather()
    {
        // Cache data cuaca selama 10 menit agar lebih sering diperbarui
        return cache()->remember('bmkg_weather_cibeusi', 600, function () {
            try {
                // API BMKG resmi - Desa Cibeusi, Kec. Ciater, Kab. Subang
                $url = 'https://api.bmkg.go.id/publik/prakiraan-cuaca?adm4=32.13.29.2004';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 10);
                curl_setopt($ch, CURLOPT_USERAGENT, 'SIASIH-Weather/1.0');
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($httpCode != 200 || empty($response)) {
                    throw new \Exception('Gagal mengambil data cuaca dari BMKG');
                }

                $data = json_decode($response, true);
                if (!$data || !isset($data['data'][0]['cuaca'])) {
                    throw new \Exception('Format data BMKG tidak valid');
                }

                // Ambil semua slot cuaca (array of arrays per hari), flatten jadi 1 array
                $allSlots = array_merge(...$data['data'][0]['cuaca']);

                // Cari slot cuaca paling dekat dengan waktu lokal sekarang
                $nowTs = time() + (7 * 3600); // WIB = UTC+7
                $closest = null;
                $minDiff = PHP_INT_MAX;

                foreach ($allSlots as $slot) {
                    $slotTs = strtotime($slot['local_datetime']);
                    $diff = abs($nowTs - $slotTs);
                    if ($diff < $minDiff) {
                        $minDiff = $diff;
                        $closest = $slot;
                    }
                }

                if (!$closest) {
                    throw new \Exception('Tidak ada data cuaca tersedia');
                }

                $weatherCode = $closest['weather'];
                $info = $this->parseBmkgWeatherCode($weatherCode, $closest['weather_desc']);

                $cuaca = [
                    'suhu'       => $closest['t'] . '°C',
                    'kelembaban' => $closest['hu'] . '%',
                    'angin'      => round($closest['ws']) . ' km/jam',
                    'kondisi'    => $closest['weather_desc'],
                    'icon'       => $info['icon'],
                    'arah_angin' => $closest['wd'] ?? '-',
                    'jarak_pandang' => $closest['vs_text'] ?? '-',
                    'sumber'     => 'BMKG',
                ];

                return response()->json([
                    'success' => true,
                    'data'    => $cuaca,
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage()
                ], 500);
            }
        });
    }


    private function parseBmkgWeatherCode($kode, $desc = '')
    {
        // Kode cuaca BMKG resmi (https://www.bmkg.go.id/cuaca/prakiraan-cuaca.bmkg)
        $kode = (int) $kode;
        $map = [
            0  => ['icon' => 'bi-sun-fill'],           // Cerah
            1  => ['icon' => 'bi-sun-fill'],           // Cerah
            2  => ['icon' => 'bi-cloud-sun-fill'],     // Cerah Berawan
            3  => ['icon' => 'bi-cloud-sun-fill'],     // Cerah Berawan
            4  => ['icon' => 'bi-cloud-fill'],         // Berawan
            5  => ['icon' => 'bi-cloud-fill'],         // Berawan
            10 => ['icon' => 'bi-cloud-fog-fill'],     // Asap
            45 => ['icon' => 'bi-cloud-fog-fill'],     // Berkabut
            60 => ['icon' => 'bi-cloud-drizzle-fill'], // Hujan Lokal
            61 => ['icon' => 'bi-cloud-rain-fill'],    // Hujan Ringan
            63 => ['icon' => 'bi-cloud-rain-fill'],    // Hujan Sedang
            65 => ['icon' => 'bi-cloud-rain-heavy-fill'], // Hujan Lebat
            80 => ['icon' => 'bi-cloud-rain-fill'],    // Hujan Ringan
            81 => ['icon' => 'bi-cloud-rain-heavy-fill'], // Hujan Sedang
            82 => ['icon' => 'bi-cloud-rain-heavy-fill'], // Hujan Lebat
            95 => ['icon' => 'bi-cloud-lightning-rain-fill'], // Hujan Petir
            97 => ['icon' => 'bi-cloud-lightning-rain-fill'], // Hujan Petir Lebat
        ];

        // Mapping default berdasarkan deskripsi bila kode tidak dikenal
        if (!isset($map[$kode])) {
            $descLower = strtolower($desc);
            if (str_contains($descLower, 'petir'))  return ['icon' => 'bi-cloud-lightning-rain-fill'];
            if (str_contains($descLower, 'lebat'))  return ['icon' => 'bi-cloud-rain-heavy-fill'];
            if (str_contains($descLower, 'hujan'))  return ['icon' => 'bi-cloud-rain-fill'];
            if (str_contains($descLower, 'gerimis')) return ['icon' => 'bi-cloud-drizzle-fill'];
            if (str_contains($descLower, 'kabut'))  return ['icon' => 'bi-cloud-fog-fill'];
            if (str_contains($descLower, 'berawan')) return ['icon' => 'bi-cloud-sun-fill'];
            if (str_contains($descLower, 'cerah'))  return ['icon' => 'bi-sun-fill'];
            return ['icon' => 'bi-cloud-fill'];
        }

        return $map[$kode];
    }
}
