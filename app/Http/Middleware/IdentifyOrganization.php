<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyOrganization
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();    // Contoh: klien-1.katauser.com
        $mainDomain = config('app.main_domain', 'katauser.com');

        $organization = null;

        // 1. Cek custom domain (Enterprise)
        $organization = Organization::where('domain', $host)->first();

        // 2. Cek subdomain (Pro / Free)
        if (!$organization && $host !== $mainDomain) {
            $sub = explode('.', $host)[0];
            $organization = Organization::where('subdomain', $sub)
                ->orWhere('random_subdomain', $sub)
                ->first();
        }

        // 3. Cek subfolder (Basic)
        if (!$organization && $host === $mainDomain) {
            $slug = $request->segment(1);
            $organization = Tenant::where('slug', $slug)->first();
        }

        if (!$organization) {
            abort(404, 'Tenant not found');
        }

        // Simpan tenant di service container
        app()->instance('tenant', $organization);

        // Redirect ke URL utama tier sekarang jika request masuk dari URL lama
        if (!in_array($host . ($path ? '/' . $path : ''), $organization->urls ?? [])) {
            // Misal tier sekarang 'pro' → pakai subdomain
            $targetUrl = match ($organization->tier) {
                'free' => "https://{$organization->random_subdomain}.{$mainDomain}/{$path}",
                'basic' => "https://{$mainDomain}/{$organization->slug}/{$path}",
                'pro' => "https://{$organization->subdomain}.{$mainDomain}/{$path}",
                'enterprise' => "https://{$organization->domain}/{$path}",
            };
            return redirect()->to($targetUrl);
        }

        // Set tenant ID di session
        session(['tenant_id' => $organization->id]);

        return $next($request);
    }
}
