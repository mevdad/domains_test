<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; margin: 0; padding: 32px 16px; }
        .wrapper { max-width: 520px; margin: 0 auto; background: #ffffff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }
        .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 28px 36px; }
        .header h1 { margin: 0; color: #fff; font-size: 1.25rem; font-weight: 700; }
        .header p { margin: 6px 0 0; color: rgba(255,255,255,0.8); font-size: 0.875rem; }
        .body { padding: 32px 36px; border: 1px solid #f0f0f0; }
        table { width: 100%; border-collapse: collapse; }
        tr { border-bottom: 1px solid #f0f0f0; }
        tr:last-child { border-bottom: none; }
        td { padding: 12px 0; font-size: 0.9rem; color: #374151; vertical-align: top; }
        td:first-child { font-weight: 600; color: #6b7280; width: 40%; }
        td:last-child { color: #111827; word-break: break-word; }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="header">
            <h1>Нова заявка з форми</h1>
            <p>Отримано нові дані з контактної форми</p>
        </div>
        <div class="body">
            <table>
                <tr>
                    <td>Ім'я</td>
                    <td>{{ $formData['name'] ?: '—' }}</td>
                </tr>
                <tr>
                    <td>Email</td>
                    <td>{{ $formData['email'] }}</td>
                </tr>
                <tr>
                    <td>Повідомлення</td>
                    <td>{{ $formData['message'] ?: '—' }}</td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>
