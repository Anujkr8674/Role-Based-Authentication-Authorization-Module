<?php
session_start();
require '../config.php';

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

if (isset($_SESSION['manager_id'])) {
    return;
}

if (isset($_COOKIE['manager_auth_token'])) {
    $token = $_COOKIE['manager_auth_token'];

    $stmt = $conn->prepare("SELECT id, manager_id, name, email, token_expiry FROM `manager` WHERE auth_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        $_SESSION['manager_id'] = $row['manager_id'];
        $_SESSION['manager_name'] = $row['name'];
        $_SESSION['manager_email'] = $row['email'];
        $_SESSION['manager_auth_token'] = $token;
        $stmt->close();

        if ($update = $conn->prepare("UPDATE `manager` SET token_expiry = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE auth_token = ?")) {
            $update->bind_param('s', $token);
            $update->execute();
            $update->close();
        }

        $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        @setcookie('manager_auth_token', $token, buildCookieOptions(time() + (60 * 60 * 24 * 7), $is_secure));
        return;
    }

    $stmt->close();
    $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
        (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    @setcookie('manager_auth_token', '', buildCookieOptions(time() - 3600, $is_secure));
}

header("Location: manager-signin.php");
exit;
?>
