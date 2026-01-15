<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>{{ $subject ?? 'Nandini Jungle by Hanging Gardens' }}</title>
</head>

<body style="margin:0; padding:0; font-family: Arial, Helvetica, sans-serif; background:#f6f6f6;">
    <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
            <td align="center" style="padding:30px 15px;">
                <table width="600" cellpadding="0" cellspacing="0" role="presentation" style="background:#ffffff;">
                    <tr>
                        <td style="padding:30px; text-align:center;">
                            <h1 style="margin:0 0 10px; font-size:24px; color:#111;">
                                Newsletter Open Tracking Test
                            </h1>
                            <p style="margin:0; color:#555;">
                                This email is used to test open tracking and country detection.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 30px; color:#333; font-size:14px; line-height:1.6;">
                            <p>
                                @if(!empty($subscriber->name))
                                Hello {{ $subscriber->name ?? '' }},
                                @endif
                            </p>

                            <p>
                                If you are reading this email, the tracking pixel below will
                                record the open event automatically.
                            </p>

                            <p>
                                You do not need to click anything. Just opening this email
                                should increment <strong>open_count</strong> and store your
                                country (if available).
                            </p>

                            <p>
                                Thank you for helping us test the newsletter system.
                            </p>

                            <p style="margin-top:30px; font-size:12px; color:#777;">
                                If you no longer wish to receive emails,
                                @php
                                $token = $subscriber->unsubscribe_token ?? null;
                                @endphp

                                <a href="{{ $token
                                    ? route('newsletter.unsubscribe', ['token' => $token])
                                    : url('/newsletter/unsubscribe/preview') }}">
                                    <u>Unsubscribe</u>
                                </a>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- OPEN TRACKING PIXEL --}}
    {{-- OPEN TRACKING PIXEL --}}
    @isset($newsletterSend)
    <img src="{{ url('/t/nl/open/' . $newsletterSend->id . '.png') }}" width="1" height="1" style="display:none!important" alt="" />
    @endisset
</body>

</html>