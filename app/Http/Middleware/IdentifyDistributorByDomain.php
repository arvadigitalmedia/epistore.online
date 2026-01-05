<?php

namespace App\Http\Middleware;

use App\Models\Distributor;
use App\Models\DistributorDomain;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\View;

class IdentifyDistributorByDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());
        $appUrl = config('app.url');
        $mainDomain = parse_url($appUrl, PHP_URL_HOST);

        // Jika akses dari domain utama atau localhost IP, skip logic distributor
        if ($host === $mainDomain || $host === 'localhost' || $host === '127.0.0.1') {
            return $next($request);
        }

        $distributor = null;

        // 1. Cek apakah ini subdomain dari domain utama?
        // Asumsi domain utama: epi-oss.test (misal)
        // Jika host: toko1.epi-oss.test
        if (str_ends_with($host, '.' . $mainDomain)) {
            $subdomain = str_replace('.' . $mainDomain, '', $host);
            $distributor = Distributor::where('subdomain', $subdomain)->first();
            
            if (!$distributor) {
                 \Illuminate\Support\Facades\Log::info("Subdomain detected: {$subdomain}, but no distributor found.");
            }
        } 
        // 2. Jika bukan subdomain, cek custom domain
        else {
            $domainRecord = DistributorDomain::where('domain', $host)
                                ->where('status', 'verified')
                                ->first();
            
            if ($domainRecord) {
                $distributor = $domainRecord->distributor;
            } else {
                 \Illuminate\Support\Facades\Log::info("Custom domain accessed: {$host}, but not found in distributor_domains.");
            }
        }

        // Jika distributor ditemukan, inject ke request dan share ke view
        if ($distributor) {
            // Cek status aktif
            if ($distributor->status !== 'active') {
                \Illuminate\Support\Facades\Log::warning("Access attempt to inactive distributor: {$host}");
                abort(503, 'This store is currently unavailable.');
            }

            $request->merge(['current_distributor' => $distributor]);
            View::share('current_distributor', $distributor);
            
            // Opsional: Set config app name sesuai nama distributor
            config(['app.name' => $distributor->name]);
        } else {
            // Log error akses subdomain tidak dikenal
            \Illuminate\Support\Facades\Log::error("Unknown subdomain/domain access attempt: {$host}");
            
            // Jika domain tidak dikenali, mungkin 404 atau redirect ke main domain
            // Tapi hati-hati, jangan memblokir akses ke file static atau route global lain jika salah konfigurasi
            // Untuk amannya, jika tidak ketemu, kita bisa abort 404 custom "Store not found"
             abort(404, 'Store not found.');
        }

        return $next($request);
    }
}
