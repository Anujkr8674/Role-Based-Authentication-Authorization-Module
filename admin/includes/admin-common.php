<?php
require __DIR__ . '/../admin-login-required.php';
require __DIR__ . '/../../config.php';

function getCurrentAdmin(mysqli $conn, string $admin_id): ?array
{
    $stmt = $conn->prepare("SELECT admin_id, name, email, created_at FROM admin WHERE admin_id = ?");
    $stmt->bind_param('s', $admin_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc() ?: null;
    $stmt->close();
    return $admin;
}

function formatAdminDate(?string $value): string
{
    if (!$value) {
        return 'Not available';
    }
    return date('d M Y, h:i A', strtotime($value));
}
?>
