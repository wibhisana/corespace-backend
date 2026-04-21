<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

final class BlockVpnAccess
{
    private const LOCAL_IPS = ['127.0.0.1', '::1'];
    private const CACHE_TTL_SECONDS = 3600;
    private const API_TIMEOUT_SECONDS = 3;

    public function handle(Request $request, Closure $next): Response
    {
        $ip = $request->ip();

        if (in_array($ip, self::LOCAL_IPS, strict: true)) {
            return $next($request);
        }

        $isProxyOrVpn = cache()->remember(
            key: "vpn_check:{$ip}",
            ttl: self::CACHE_TTL_SECONDS,
            callback: fn (): bool => $this->detectProxy($ip),
        );

        if ($isProxyOrVpn) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Akses ditolak. Harap matikan koneksi VPN/Proxy Anda saat melakukan absensi.',
            ], Response::HTTP_FORBIDDEN);
        }

        return $next($request);
    }

    private function detectProxy(string $ip): bool
    {
        try {
            $query = ['vpn' => 1];

            if ($key = config('services.proxycheck.key')) {
                $query['key'] = $key;
            }

            $response = Http::timeout(self::API_TIMEOUT_SECONDS)
                ->acceptJson()
                ->get("https://proxycheck.io/v2/{$ip}", $query);

            if ($response->failed()) {
                return false;
            }

            return ($response->json($ip . '.proxy') === 'yes');
        } catch (\Throwable $e) {
            Log::warning('ProxyCheck lookup failed', ['ip' => $ip, 'error' => $e->getMessage()]);
            return false;
        }
    }
}
