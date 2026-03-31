<?php
session_start();
require 'config.php';

header('Content-Type: application/json');

$employee_id = $_POST['employee_id'] ?? '';
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$employee_id || !$name || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$check = $conn->prepare("SELECT id FROM employee WHERE email = ? OR employee_id = ?");
$check->bind_param("ss", $email, $employee_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email or Employee ID already exists!']);
} else {
    $stmt = $conn->prepare("INSERT INTO employee (employee_id, name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $employee_id, $name, $email, $hashed_password);

    if ($stmt->execute()) {
        unset($_SESSION['otp_verified']);
        unset($_SESSION['register_email']);
        unset($_SESSION['register_name']);
        unset($_SESSION['register_otp']);
        unset($_SESSION['otp_sent_time']);
        unset($_SESSION['otp_expired']);
        echo json_encode(['success' => true, 'employee_id' => $employee_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed.']);
    }
    $stmt->close();
}
$check->close();
$conn->close();
?> 
