<?php

namespace App\Http\Controllers;

use App\Modules\Newsletter\Models\Subscriber;

class NewsletterController extends Controller
{
    public function unsubscribe(string $token)
    {
        // Find subscriber by token
        $subscriber = Subscriber::where('unsubscribe_token', $token)->first();

        // Token not found → invalid
        if (! $subscriber) {
            return view('newsletter.unsubscribe-invalid');
        }

        // Already unsubscribed
        if (! $subscriber->is_active) {
            return view('newsletter.unsubscribe-already', [
                'subscriber' => $subscriber,
            ]);
        }

        // Unsubscribe user
        $subscriber->is_active = false;
        $subscriber->save();

        return view('newsletter.unsubscribed', [
            'subscriber' => $subscriber,
        ]);
    }
}
