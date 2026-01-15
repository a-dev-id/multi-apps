<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Mail;

Route::get('/', fn() => view('app-select'))->name('app.select');
// Route::get('/', fn() => view('choose-property'))
//     ->name('choose.property');

// adjust these to your real routes
// Route::get('/newsletter')->name('app.newsletter');
// Route::get('/guestletter')->name('app.guestletter');

Route::get('/newsletter/unsubscribe/{token}', [\App\Http\Controllers\NewsletterController::class, 'unsubscribe'])->name('newsletter.unsubscribe');
Route::get('/cron/newsletters/send-scheduled/{token}', [\App\Http\Controllers\CronNewsletterController::class, 'sendScheduled']);
Route::middleware(['web', 'auth'])->get('/admin/newsletters/{newsletter}/preview', [\App\Http\Controllers\NewsletterPreviewController::class, 'show'])->name('newsletters.preview');

Route::get('/cron/guestletter/{token}', [\App\Http\Controllers\CronGuestLetterController::class, 'dispatch']);
Route::get('/cron/guestletter/post-stay/{token}', [\App\Http\Controllers\CronGuestLetterController::class, 'dispatchPostStay']);

Route::get('/cron/queue-work/{token}', function ($token) {
    abort_unless($token === config('app.cron_token'), 403);

    Artisan::call('queue:work', [
        '--stop-when-empty' => true,
        '--max-jobs' => 20,
        '--max-time' => 50,
    ]);

    return Artisan::output();
});


Route::get('/clear-cache', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('view:clear');
    Artisan::call('route:clear');
    return 'Cache, Config, View & Route cleared!';
});

Route::get('/t/nl/open/{sendId}.png', function ($sendId) {
    $send = \App\Modules\Newsletter\Models\NewsletterSend::findOrFail($sendId);

    // First open timestamp
    if (is_null($send->opened_at)) {
        $send->opened_at = now();
    }

    $send->open_count++;
    $send->last_open_ip = request()->ip();
    $send->last_open_user_agent = substr(request()->userAgent(), 0, 1000);

    // If country already known (recommended)
    // $send->last_open_country ??= $send->subscriber->country_code;

    $send->save();

    // 1x1 transparent PNG
    return response(base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVQIW2NgYGD4DwABBAEAHq3GkQAAAABJRU5ErkJggg=='
    ))->header('Content-Type', 'image/png');
});


Route::get('/cron/queue-work/{token}', function (string $token) {
    abort_unless($token === config('app.cron_token'), 403);

    Artisan::call('queue:work', [
        '--stop-when-empty' => true,
        '--sleep' => 1,
        '--tries' => 3,
    ]);

    return response()->json([
        'status' => 'ok',
        'output' => Artisan::output(),
    ]);
});


Route::get('/tools/bounce-purge/{token}', \App\Http\Controllers\BouncePurgeController::class);
Route::get('/cron/birthday/{token}', [\App\Http\Controllers\CronBirthdayController::class, 'sendToday']);

Route::get('/mail-test/{token}', function ($token) {
    abort_unless($token === config('app.cron_token'), 403);

    Mail::raw('Mail test OK', function ($m) {
        $m->to('itm@nandinibali.com')->subject('Mail test');
    });

    return 'sent';
});
