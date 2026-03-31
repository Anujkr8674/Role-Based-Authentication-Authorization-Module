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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$input || !$password) {
        $_SESSION['admin_login_error'] = 'Please enter your email/admin ID and password.';
        header('Location: admin-signin.php');
        exit;
    }

    if (filter_var($input, FILTER_VALIDATE_EMAIL)) {
        $stmt = $conn->prepare('SELECT admin_id, name, email, password FROM admin WHERE LOWER(email) = LOWER(?) LIMIT 1');
        $stmt->bind_param('s', $input);
    } else {
        $stmt = $conn->prepare('SELECT admin_id, name, email, password FROM admin WHERE admin_id = ? LIMIT 1');
        $stmt->bind_param('s', $input);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();
    $stmt->close();

    if ($admin && password_verify($password, $admin['password'])) {
        $token = bin2hex(random_bytes(32));
        $token_expiry = date('Y-m-d H:i:s', strtotime('+7 days'));
        $update_stmt = $conn->prepare("UPDATE admin SET auth_token = ?, token_expiry = ? WHERE admin_id = ?");
        $update_stmt->bind_param("sss", $token, $token_expiry, $admin['admin_id']);
        $update_stmt->execute();
        $update_stmt->close();

        $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
            (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
        setcookie('admin_auth_token', $token, buildCookieOptions(time() + (7 * 24 * 60 * 60), $is_secure));
        $_SESSION['admin_id'] = $admin['admin_id'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_auth_token'] = $token;
        header('Location: admin-dashboard.php');
        exit;
    }

    $_SESSION['admin_login_error'] = 'Invalid credentials. Please try again.';
    header('Location: admin-signin.php');
    exit;
}

header('Location: admin-signin.php');
exit;
?>
