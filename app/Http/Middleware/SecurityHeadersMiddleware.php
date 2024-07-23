<?php

namespace App\Http\Middleware;

use Closure;
use Google\Rpc\Context\AttributeContext\Request;

class SecurityHeadersMiddleware
{
    private $unwantedHeaders = ['X-Powered-By', 'server', 'Server'];

    /**
     * @param $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if (!$request->hasHeader('X-Api-Version') || !$request->prefers(['application/json'])) {
            return response()->json(['error_message' => 'Bad Request'], 400);
        }

        // For Reference: 
        // https://cheatsheetseries.owasp.org/cheatsheets/REST_Security_Cheat_Sheet.html#security-headers
        $response = $next($request);

        $response->headers->set('Content-Security-Policy', "default-src 'none'; frame-ancestors 'none'");
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        $response->headers->set('X-Content-Type-Options', 'no-sniff');
        $response->headers->set('Content-Type', 'application/json');
        $response->headers->set('X-Frame-Options:', 'DENY');
        $response->headers->set('Referrer-Policy:', 'no-referrer');
        $response->headers->set('Permissions-Policy', 'aaccelerometer=(), ambient-light-sensor=(), autoplay=(), battery=(), camera=(), cross-origin-isolated=(), display-capture=(), document-domain=(), encrypted-media=(), execution-while-not-rendered=(), execution-while-out-of-viewport=(), fullscreen=(), geolocation=(), gyroscope=(), keyboard-map=(), magnetometer=(), microphone=(), midi=(), navigation-override=(), payment=(), picture-in-picture=(), publickey-credentials-get=(), screen-wake-lock=(), sync-xhr=(), usb=(), web-share=(), xr-spatial-tracking=()');

        $response->header('X-Api-Version', $request->header('X-Api-Version', 'v1'));
        $response->header('X-Api-Latest-Version', config('app.latestApiVersion'));

        // Found on the web if return html
        // $response->headers->set('X-XSS-Protection', '1; mode=block');
        // $response->headers->set('Expect-CT', 'enforce, max-age=30');

        $this->removeUnwantedHeaders($this->unwantedHeaders);

        return $response;
    }

    /**
     * @param $headers
     */
    private function removeUnwantedHeaders($headers): void
    {
        foreach ($headers as $header) {
            header_remove($header);
        }
    }

}
