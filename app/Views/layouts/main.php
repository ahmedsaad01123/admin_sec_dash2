<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'التطبيق', ENT_QUOTES, 'UTF-8') ?></title>
    <style>
        * { box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: #f4f6f8;
            margin: 0;
            padding: 0;
            color: #1f2937;
        }
        .container {
            max-width: 720px;
            margin: 80px auto;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            text-align: center;
        }
        h1 { color: #111827; margin-bottom: 8px; }
        p { color: #4b5563; font-size: 16px; }
        .badge {
            display: inline-block;
            background: #ecfdf5;
            color: #059669;
            padding: 4px 12px;
            border-radius: 999px;
            font-size: 13px;
            margin-top: 16px;
        }
    </style>
</head>
<body>
    <?= $content ?? '' ?>
</body>
</html>
