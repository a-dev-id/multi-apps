<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Already unsubscribed</title>
</head>

<body style="font-family: Arial, sans-serif; padding: 40px; background:#f4f4f4;">
    <div style="max-width:600px; margin:0 auto; background:#fff; padding:24px; border-radius:8px;">
        <h1 style="margin-top:0;">You’re already unsubscribed</h1>
        <p>
            The email <strong>{{ $subscriber->email }}</strong> is already removed from our mailing list.
        </p>
    </div>
</body>

</html>