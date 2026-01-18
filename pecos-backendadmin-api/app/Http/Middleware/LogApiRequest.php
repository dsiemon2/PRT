<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequest
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $responseTime = round(($endTime - $startTime) * 1000); // in milliseconds

        // Log the request
        try {
            DB::table('api_logs')->insert([
                'endpoint' => $request->path(),
                'method' => $request->method(),
                'status_code' => $response->getStatusCode(),
                'response_time' => $responseTime,
                'ip_address' => $request->ip(),
                'country' => $this->getCountryFromIp($request->ip()),
                'user_agent' => $request->userAgent(),
                'request_body' => $this->sanitizeBody($request->all()),
                'dropshipper_id' => $request->user() ? $request->user()->id : null,
                'created_at' => now(),
            ]);
        } catch (\Exception $e) {
            // Silently fail - don't break the API if logging fails
            \Log::error('API Log failed: ' . $e->getMessage());
        }

        return $response;
    }

    /**
     * Sanitize request body to remove sensitive data
     */
    private function sanitizeBody(array $body): ?string
    {
        if (empty($body)) {
            return null;
        }

        // Remove sensitive fields
        $sensitiveFields = ['password', 'password_confirmation', 'token', 'api_key', 'secret'];
        foreach ($sensitiveFields as $field) {
            if (isset($body[$field])) {
                $body[$field] = '***REDACTED***';
            }
        }

        $json = json_encode($body);

        // Limit size to prevent huge logs
        if (strlen($json) > 2000) {
            return substr($json, 0, 2000) . '...[truncated]';
        }

        return $json;
    }

    /**
     * Get country from IP address using free IP geolocation API
     */
    private function getCountryFromIp(?string $ip): ?string
    {
        if (!$ip || $ip === '127.0.0.1' || $ip === '::1') {
            return 'Local';
        }

        // Skip private/local IPs
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return 'Local';
        }

        try {
            // Use ip-api.com free service (no API key needed, 45 req/min limit)
            $response = @file_get_contents("http://ip-api.com/json/{$ip}?fields=country,countryCode", false, stream_context_create([
                'http' => ['timeout' => 2]
            ]));

            if ($response) {
                $data = json_decode($response, true);
                if (isset($data['countryCode'])) {
                    return $data['countryCode'];
                }
            }
        } catch (\Exception $e) {
            // Silently fail
        }

        return null;
    }
}
