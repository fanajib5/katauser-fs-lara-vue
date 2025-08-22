<?php

namespace App\Http\Middleware;

use App\Models\Organization;
use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\PermissionRegistrar;
use Symfony\Component\HttpFoundation\Response;

class ResolveOrganizationContext
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
        $sub = null;
        $slug = null;

        // 1. Cek custom domain (Enterprise)
        $organization = Organization::where('domain', $host)->first();

        // 2. Cek subdomain (Pro)
        if (!$organization && $host !== $mainDomain) {
            $sub = explode('.', $host)[0] ?? null;

            if ($sub) {
                $organization = Organization::where('subdomain', $sub)->first();
            }
        }

        // 3. Cek path (Pro / Free)
        if (!$organization && $host !== $mainDomain) {
            $slug = $request->segment(1) ?? null;

            if ($slug) {
                $organization = Organization::where('slug', $slug)->first();
            }
        }

        if (!$organization) {
            abort(404, 'Organization not found');
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($organization->id);
        // Simpan ke request agar mudah diakses downstream
        $request->attributes->set('organization', $organization);

        return $next($request);
    }
}
