<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registration Unavailable</title>
    <style>
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #f6f7f8; color: #0d141b; font-family: Arial, sans-serif; }
        main { width: min(560px, calc(100% - 48px)); padding: 40px; background: #fff; border: 1px solid #e7edf3; border-radius: 16px; text-align: center; box-shadow: 0 12px 30px rgba(13, 20, 27, .08); }
        h1 { margin: 0 0 16px; font-size: 28px; }
        p { margin: 0; color: #4c739a; line-height: 1.6; }
    </style>
</head>
<body>
<main>
    <h1><?= esc($title ?? 'Registration link unavailable') ?></h1>
    <p><?= esc($message ?? 'This invitation registration link is no longer available.') ?></p>
</main>
</body>
</html>
