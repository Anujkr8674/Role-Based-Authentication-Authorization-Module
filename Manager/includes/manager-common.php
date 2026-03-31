<?php
require __DIR__ . '/../manager-login-required.php';
require __DIR__ . '/../../config.php';

function managerHasLastLogin(mysqli $conn): bool
{
    $column_check = $conn->query("SHOW COLUMNS FROM `manager` LIKE 'last_login'");
    $has_last_login = $column_check && $column_check->num_rows > 0;
    if ($column_check instanceof mysqli_result) {
        $column_check->free();
    }
    return $has_last_login;
}

function getCurrentManager(mysqli $conn, string $manager_id): ?array
{
    $has_last_login = managerHasLastLogin($conn);
    $select_sql = $has_last_login
        ? "SELECT manager_id, name, email, phone, last_login, created_at FROM `manager` WHERE manager_id = ?"
        : "SELECT manager_id, name, email, phone, NULL AS last_login, created_at FROM `manager` WHERE manager_id = ?";
    $stmt = $conn->prepare($select_sql);
    $stmt->bind_param('s', $manager_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $manager = $result->fetch_assoc() ?: null;
    $stmt->close();
    return $manager;
}

function formatManagerDate(?string $value): string
{
    if (!$value) {
        return 'Not available';
    }
    return date('d M Y, h:i A', strtotime($value));
}
?>
