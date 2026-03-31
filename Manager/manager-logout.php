<?php
ob_start();
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
    $stmt = $conn->prepare("UPDATE `manager` SET auth_token = NULL, token_expiry = NULL WHERE manager_id = ?");
    $stmt->bind_param("s", $_SESSION['manager_id']);
    $stmt->execute();
    $stmt->close();
}

$is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

$host = $_SERVER['HTTP_HOST'] ?? '';
setcookie('manager_auth_token', '', buildCookieOptions(time() - 3600, $is_secure));

session_unset();
session_destroy();

function buildAppUrl(string $targetPath, bool $is_secure, string $host): string
{
    $scheme = $is_secure ? 'https://' : 'http://';
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
    $currentDir = str_replace('\\', '/', dirname($scriptName));
    $currentDir = $currentDir === '/' ? '' : rtrim($currentDir, '/');
    $basePath = $currentDir !== '' ? $currentDir : '';
    $targetPath = '/' . ltrim($targetPath, '/');

    return $scheme . ($host ?: '') . $basePath . $targetPath;
}

$redirectUrl = buildAppUrl('manager-signin.php', $is_secure, $host);
if (!headers_sent()) {
    header("Location: $redirectUrl");
    exit;
}

echo '<script>window.location.href=' . json_encode($redirectUrl) . ';</script>';
echo '<meta http-equiv="refresh" content="0;url=' . $redirectUrl . '">';
exit;
?>
