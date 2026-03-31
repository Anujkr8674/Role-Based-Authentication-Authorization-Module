<?php
session_start();
require_once '../config.php';

function buildCookieOptions(int $expires, bool $is_secure): array
{
    $options = [
        'expires' => $expires,
        'path' => '/',
        'secure' => $is_secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ];

    $host = $_SERVER['HTTP_HOST'] ?? '';
    if ($host && $host !== 'localhost' && $host !== '127.0.0.1') {
        $options['domain'] = preg_replace('/:\d+$/', '', $host);
    }

    return $options;
}

if (isset($_SESSION['admin_id'])) {
    $stmt = $conn->prepare("UPDATE admin SET auth_token = NULL, token_expiry = NULL WHERE admin_id = ?");
    $stmt->bind_param("s", $_SESSION['admin_id']);
    $stmt->execute();
    $stmt->close();
}

$is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
setcookie('admin_auth_token', '', buildCookieOptions(time() - 3600, $is_secure));
session_unset();
session_destroy();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f7f7f3; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
        .modal-logout { background: #fffefa; border-radius: 16px; box-shadow: 0 8px 30px rgba(80,109,99,0.08); padding: 36px 32px; max-width: 400px; width: 100%; border: 1px solid #dddccf; text-align: center; }
        .modal-title { font-size: 1.3rem; font-weight: 700; color: #31463f; margin-bottom: 12px; }
    </style>
    <script>
        history.pushState(null, null, location.href);
        window.onpopstate = function () { history.go(1); };
        setTimeout(function() { window.location.href = 'admin-signin.php'; }, 1800);
    </script>
</head>
<body>
    <div class="modal-logout">
        <div class="modal-title">You have been logged out</div>
        <div>Please login again to continue.</div>
        <div class="mt-3"><a href="admin-signin.php" class="btn btn-primary">Go to Login</a></div>
    </div>
</body>
</html>
