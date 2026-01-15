<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Unsubscribed</title>
</head>

<body style="font-family: Arial, sans-serif; padding: 40px; background:#f4f4f4;">
    <div style="max-width:600px; margin:0 auto; background:#fff; padding:24px; border-radius:8px;">
        <h1 style="margin-top:0;">You’ve been unsubscribed</h1>
        <p>
            Hi {{ $subscriber->name ?: $subscriber->email }},<br>
            You will no longer receive our newsletter at
            <strong>{{ $subscriber->email }}</strong>.
        </p>
        <p style="font-size:13px; color:#6b7280;">
            If this was a mistake, you can contact us through our website.
        </p>
    </div>
</body>

</html>