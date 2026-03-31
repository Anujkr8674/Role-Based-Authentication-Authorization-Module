<?php
require __DIR__ . '/../employee-login-required.php';
require __DIR__ . '/../config.php';

function employeeHasColumn(mysqli $conn, string $column): bool
{
    $column_check = $conn->query("SHOW COLUMNS FROM `employee` LIKE '" . $conn->real_escape_string($column) . "'");
    $has_column = $column_check && $column_check->num_rows > 0;
    if ($column_check instanceof mysqli_result) {
        $column_check->free();
    }
    return $has_column;
}

function getCurrentEmployee(mysqli $conn, string $employee_id): ?array
{
    $last_login_sql = employeeHasColumn($conn, 'last_login') ? 'last_login' : 'NULL AS last_login';
    $created_at_sql = employeeHasColumn($conn, 'created_at') ? 'created_at' : 'NULL AS created_at';
    $select_sql = "SELECT employee_id, name, email, phone, {$last_login_sql}, {$created_at_sql} FROM `employee` WHERE employee_id = ?";
    $stmt = $conn->prepare($select_sql);
    $stmt->bind_param('s', $employee_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $employee = $result->fetch_assoc() ?: null;
    $stmt->close();
    return $employee;
}

function formatEmployeeDate(?string $value): string
{
    if (!$value) {
        return 'Not available';
    }
    return date('d M Y, h:i A', strtotime($value));
}
?>
