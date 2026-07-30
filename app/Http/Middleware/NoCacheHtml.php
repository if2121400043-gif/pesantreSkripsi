<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Prevent browsers and PWA service workers from caching HTML responses.
 * 
 * This ensures that every page navigation fetches fresh data from the server,
 * which is critical for PWA mode where stale cached pages can show outdated information.
 */
class NoCacheHtml
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Only add no-cache headers to HTML responses (not API JSON, file downloads, etc.)
        $contentType = $response->headers->get('Content-Type', '');
        
        if (str_contains($contentType, 'text/html') || $request->acceptsHtml()) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate, private');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
