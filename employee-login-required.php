<?php
session_start();
require 'config.php';

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

// Check if user is logged in via session
if (isset($_SESSION['employee_id'])) {
    // User is logged in via session, continue
    return;
}

// Check if user has valid token in cookie
if (isset($_COOKIE['employee_auth_token'])) {
    $token = $_COOKIE['employee_auth_token'];
    
    // Check token in database
    $stmt = $conn->prepare("SELECT id, employee_id, name, email, token_expiry FROM employee WHERE auth_token = ? AND token_expiry > NOW()");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        // Valid token found, restore session
        $_SESSION['employee_id'] = $row['employee_id'];
        $_SESSION['employee_name'] = $row['name'];
        $_SESSION['employee_email'] = $row['email'];
        $_SESSION['employee_auth_token'] = $token;
        $stmt->close();

        // Sliding expiration: extend token expiry and refresh cookie for 7 days
        if ($update = $conn->prepare("UPDATE employee SET token_expiry = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE auth_token = ?")) {
            $update->bind_param('s', $token);
            $update->execute();
            $update->close();
        }
        $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                     (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        @setcookie('employee_auth_token', $token, buildCookieOptions(time() + (60 * 60 * 24 * 7), $is_secure));
        return;
    }
    $stmt->close();

    // If token is invalid/expired, clear cookie to prevent loops
    $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                 (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    @setcookie('employee_auth_token', '', buildCookieOptions(time() - 3600, $is_secure));
}

// No valid session or token, redirect to login
header("Location: employee-signin.php");
exit;
?> 
