<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyMidtransIp
{
    /**
     * Daftar IP resmi server Midtrans.
     * Sumber: https://docs.midtrans.com/docs/ip-address-whitelist
     */
    protected array $allowedIps = [
        '34.101.76.30',
        '34.101.75.105',
        '34.101.185.62',
        '34.101.237.235',
        '34.101.73.236',
        '34.101.244.170',
        '34.101.72.188',
        '34.101.230.114',
        // IP sandbox Midtrans
        '202.152.144.0',
        '210.16.117.0',
        '103.14.197.0',
    ];

    /**
     * Hanya izinkan request dari IP resmi Midtrans ke endpoint webhook.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Di environment lokal/testing, bypass pengecekan IP
        if (! app()->isProduction()) {
            return $next($request);
        }

        $requestIp = $request->ip();

        if (! in_array($requestIp, $this->allowedIps, true)) {
            \Illuminate\Support\Facades\Log::warning('Midtrans webhook: IP tidak diizinkan — ' . $requestIp);
            return response()->json(['message' => 'Forbidden'], 403);
        }

        return $next($request);
    }
}
