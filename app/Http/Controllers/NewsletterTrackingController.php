<?php

namespace App\Http\Controllers;

use App\Modules\Newsletter\Models\NewsletterSend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class NewsletterTrackingController extends Controller
{
    public function open(Request $request, NewsletterSend $send)
    {
        $ip = $request->ip();
        $ua = (string) $request->userAgent();

        $country = $this->countryFromIp($ip); // e.g. "ID", "US", null

        // Update stats
        $send->open_count = (int) ($send->open_count ?? 0) + 1;

        if (!$send->opened_at) {
            $send->opened_at = now(); // first open time (unique open)
        }

        $send->last_open_ip = $ip;
        $send->last_open_country = $country;
        $send->last_open_user_agent = $ua;
        $send->save();

        // Return a 1x1 transparent gif (email-safe)
        $gif = base64_decode('R0lGODlhAQABAIABAP///wAAACwAAAAAAQABAAACAkQBADs=');

        return Response::make($gif, 200, [
            'Content-Type'  => 'image/gif',
            'Cache-Control' => 'no-cache, no-store, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
            'Expires'       => '0',
        ]);
    }

    private function countryFromIp(?string $ip): ?string
    {
        if (!$ip) return null;

        // Skip local/private IPs (IPv4)
        if (
            str_starts_with($ip, '127.') ||
            str_starts_with($ip, '10.') ||
            str_starts_with($ip, '192.168.') ||
            preg_match('/^172\.(1[6-9]|2\d|3[0-1])\./', $ip)
        ) {
            return null;
        }

        // ipapi returns plain text country code on /country/
        $url = "https://ipapi.co/{$ip}/country/";

        $context = stream_context_create([
            'http' => [
                'method'  => 'GET',
                'timeout' => 2, // seconds
                'header'  => "User-Agent: NewsletterTracker/1.0\r\n",
            ],
        ]);

        $resp = @file_get_contents($url, false, $context);
        if (!$resp) return null;

        $code = strtoupper(trim($resp));

        // basic validation (2-letter ISO code)
        if (!preg_match('/^[A-Z]{2}$/', $code)) {
            return null;
        }

        return $code; // e.g. "ID"
    }
}
