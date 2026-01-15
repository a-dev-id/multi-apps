<?php

namespace App\Http\Controllers;

use App\Modules\Newsletter\Models\Newsletter;
use App\Modules\Newsletter\Models\Subscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterPreviewController extends Controller
{
    public function show(Request $request, Newsletter $newsletter)
    {
        $subscriber = new Subscriber([
            'name' => $request->get('name', 'John Doe'),
            'email' => $request->get('email', 'preview@example.com'),
            'is_active' => true,
            'unsubscribe_token' => Str::random(32),
        ]);

        return view('emails.templates.default', [
            'newsletter' => $newsletter,
            'subscriber' => $subscriber,
            'newsletterSend' => null,
            'unsubscribeToken' => $subscriber->unsubscribe_token,
        ]);
    }
}
