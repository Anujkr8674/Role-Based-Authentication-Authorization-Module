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

header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');

if (isset($_SESSION['admin_id'])) {
    $admin_id = $_SESSION['admin_id'] ?? '';
    $name = $_SESSION['admin_name'] ?? '';
    $admin_email = $_SESSION['admin_email'] ?? '';
    return;
}

if (isset($_COOKIE['admin_auth_token'])) {
    $token = $_COOKIE['admin_auth_token'];
    $stmt = $conn->prepare("SELECT admin_id, name, email, token_expiry FROM admin WHERE auth_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $_SESSION['admin_id'] = $row['admin_id'];
        $_SESSION['admin_name'] = $row['name'];
        $_SESSION['admin_email'] = $row['email'];
        $_SESSION['admin_auth_token'] = $token;
        $admin_id = $row['admin_id'];
        $name = $row['name'];
        $admin_email = $row['email'];
        $stmt->close();
        $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        @setcookie('admin_auth_token', $token, buildCookieOptions(time() + (60 * 60 * 24 * 7), $is_secure));
        return;
    }
    $stmt->close();
    $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    @setcookie('admin_auth_token', '', buildCookieOptions(time() - 3600, $is_secure));
}

header('Location: admin-signin.php');
exit;
?>
