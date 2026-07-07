<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $err_title ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body { background: #f1f5f9; display: flex; align-items: center; justify-content: center; min-height: 100vh; font-family: 'Segoe UI', system-ui, sans-serif; margin: 0; }
        .err-box { background: white; border-radius: 24px; padding: 3rem 2.5rem; max-width: 480px; width: 92%; text-align: center; box-shadow: 0 16px 48px rgba(0,0,0,.1); }
        .err-icon { font-size: 4rem; margin-bottom: 1.25rem; }
        .err-title { font-size: 1.5rem; font-weight: 900; color: #1e293b; margin-bottom: .5rem; }
        .err-msg { color: #64748b; font-size: .92rem; line-height: 1.6; margin-bottom: 1.75rem; }
        .err-code { font-size: .75rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: .5rem 1rem; color: #64748b; display: inline-block; }
    </style>
</head>
<body>
<div class="err-box">
    <?php if ($err_type === 'expired'): ?>
        <div class="err-icon">⏰</div>
    <?php elseif ($err_type === 'revoked'): ?>
        <div class="err-icon">🚫</div>
    <?php else: ?>
        <div class="err-icon">🔒</div>
    <?php endif; ?>
    <div class="err-title"><?= $err_title ?></div>
    <div class="err-msg"><?= $err_message ?></div>
    <div class="err-code">TGM ERP — Owner Report System</div>
</div>
</body>
</html>
