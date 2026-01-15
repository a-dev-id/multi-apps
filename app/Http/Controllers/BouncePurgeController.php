<?php

namespace App\Http\Controllers;

use App\Modules\Newsletter\Models\Subscriber;
use Illuminate\Http\Request;

class BouncePurgeController extends Controller
{
    public function __invoke(Request $request, string $token)
    {
        if ($token !== config('app.cron_token')) {
            abort(403);
        }

        if (!function_exists('imap_open')) {
            return response()->json([
                'ok' => false,
                'error' => 'PHP IMAP extension not enabled (imap_open missing)',
            ], 500);
        }

        $days = max(1, min((int) $request->query('days', 30), 365));
        $limit = max(50, min((int) $request->query('limit', 2000), 10000)); // scan last N messages

        $action = $request->query('action', 'list'); // list | delete | debug
        if (!in_array($action, ['list', 'delete', 'debug'], true)) {
            return response()->json(['ok' => false, 'error' => 'Invalid action'], 422);
        }

        $user = config('mail.mailers.smtp.username');
        $pass = config('mail.mailers.smtp.password');

        $smtpHost = config('mail.mailers.smtp.host', 'smtp.gmail.com');
        $imapHost = str_replace('smtp.', 'imap.', $smtpHost);

        if (!$user || !$pass) {
            return response()->json(['ok' => false, 'error' => 'MAIL_USERNAME / MAIL_PASSWORD not set'], 500);
        }

        // Your bounce emails are in this folder (from debug output)
        $mailbox = (string) $request->query('mailbox', 'Inbox Done');
        $imapMailbox = '{' . $imapHost . ':993/imap/ssl}' . $mailbox;

        $mbox = @imap_open($imapMailbox, $user, $pass);
        if (!$mbox) {
            return response()->json([
                'ok' => false,
                'error' => 'IMAP open failed',
                'detail' => imap_last_error(),
                'imap_mailbox' => $imapMailbox,
            ], 500);
        }

        if ($action === 'debug') {
            $folders = imap_list($mbox, '{' . $imapHost . ':993/imap/ssl}', '*') ?: [];
            imap_close($mbox);

            return response()->json([
                'ok' => true,
                'action' => 'debug',
                'opened_mailbox' => $imapMailbox,
                'folders' => $folders,
            ]);
        }

        $cutoffTs = time() - ($days * 86400);

        $total = imap_num_msg($mbox);
        if ($total <= 0) {
            imap_close($mbox);
            return response()->json([
                'ok' => true,
                'action' => $action,
                'mailbox' => $mailbox,
                'since_days' => $days,
                'scanned_messages' => 0,
                'unique_bounced_emails' => 0,
                'emails' => [],
                'note' => 'Mailbox is empty or unreadable.',
            ]);
        }

        // Scan last N messages only
        $start = max(1, $total - $limit + 1);

        $found = [];
        $scanned = 0;
        $matchedBounce = 0;

        // Patterns to extract recipient email
        $extractPatterns = [
            '/wasn\'?t delivered to\s+([a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,})/i',
            '/final-recipient:\s*rfc822;\s*([a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,})/i',
            '/original-recipient:\s*rfc822;\s*([a-z0-9._%+\-]+@[a-z0-9.\-]+\.[a-z]{2,})/i',
        ];

        // Bounce detection keywords (body/snippet)
        $bounceKeywords = [
            'address not found',
            "your message wasn't delivered",
            'delivery status notification',
            'undelivered mail returned to sender',
            '550 5.1.1',
        ];

        for ($msgno = $start; $msgno <= $total; $msgno++) {
            $scanned++;

            $overviewArr = imap_fetch_overview($mbox, (string) $msgno, 0);
            $overview = $overviewArr[0] ?? null;

            $dateStr = $overview->date ?? null;
            $ts = $dateStr ? strtotime($dateStr) : null;

            // Date filter (skip older messages)
            if ($ts && $ts < $cutoffTs) {
                continue;
            }

            $from = strtolower((string) ($overview->from ?? ''));
            $subject = strtolower((string) ($overview->subject ?? ''));

            // Quick bounce sender check
            $looksBounceSender =
                str_contains($from, 'mailer-daemon') ||
                str_contains($from, 'postmaster') ||
                str_contains($from, 'mail delivery subsystem');

            // Quick subject/body hint (subject sometimes contains "Address not found")
            $looksBounceSubject = str_contains($subject, 'address not found') || str_contains($subject, 'delivery status');

            // If neither looks likely, we still might have Gmail threads with original subject,
            // so we must check body for keywords, but only then fetch body (costly).
            $shouldFetchBody = $looksBounceSender || $looksBounceSubject;

            $body = '';
            if (!$shouldFetchBody) {
                // Fetch small body sample to avoid heavy reads
                $body = @imap_fetchbody($mbox, $msgno, 1);
                if (!$body) $body = '';
                $sample = strtolower(substr(strip_tags($body), 0, 2000));

                foreach ($bounceKeywords as $kw) {
                    if (str_contains($sample, $kw)) {
                        $shouldFetchBody = true;
                        break;
                    }
                }
            }

            if (!$shouldFetchBody) {
                continue;
            }

            $matchedBounce++;

            // Fetch full-ish content for extraction
            $header = imap_fetchheader($mbox, $msgno) ?: '';
            $body1  = @imap_fetchbody($mbox, $msgno, 1) ?: '';
            $body2  = @imap_fetchbody($mbox, $msgno, 2) ?: '';
            $full   = $header . "\n" . $body1 . "\n" . $body2;

            // Extract recipient emails
            foreach ($extractPatterns as $re) {
                if (preg_match_all($re, $full, $m)) {
                    foreach ($m[1] as $email) {
                        $email = strtolower(trim($email, " \t\n\r\0\x0B<>\"'.,;:"));
                        if ($email && $email !== strtolower($user)) {
                            $found[$email] = true;
                        }
                    }
                }
            }
        }

        imap_close($mbox);

        $emails = array_keys($found);
        sort($emails);

        if ($action === 'list') {
            return response()->json([
                'ok' => true,
                'action' => 'list',
                'mailbox' => $mailbox,
                'since_days' => $days,
                'scanned_messages' => $scanned,
                'matched_bounce_messages' => $matchedBounce,
                'unique_bounced_emails' => count($emails),
                'emails' => $emails,
                'note' => 'Search-free scan (works even when IMAP SEARCH / X-GM-RAW returns 0).',
            ]);
        }

        $deleted = 0;
        if (count($emails)) {
            $deleted = Subscriber::whereIn('email', $emails)->delete();
        }

        return response()->json([
            'ok' => true,
            'action' => 'delete',
            'mailbox' => $mailbox,
            'since_days' => $days,
            'scanned_messages' => $scanned,
            'matched_bounce_messages' => $matchedBounce,
            'unique_bounced_emails' => count($emails),
            'subscribers_deleted' => $deleted,
            'emails' => $emails,
        ]);
    }
}
