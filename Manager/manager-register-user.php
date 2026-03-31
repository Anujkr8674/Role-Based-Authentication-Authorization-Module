<?php
session_start();
require '../config.php';

header('Content-Type: application/json');

$manager_id = $_POST['manager_id'] ?? '';
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$manager_id || !$name || !$email || !$password) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$check = $conn->prepare("SELECT id FROM `manager` WHERE email = ? OR manager_id = ?");
$check->bind_param("ss", $email, $manager_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email or Manager ID already exists!']);
} else {
    $stmt = $conn->prepare("INSERT INTO `manager` (manager_id, name, email, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $manager_id, $name, $email, $hashed_password);

    if ($stmt->execute()) {
        unset(
            $_SESSION['manager_otp_verified'],
            $_SESSION['manager_register_email'],
            $_SESSION['manager_register_name'],
            $_SESSION['manager_register_otp'],
            $_SESSION['manager_otp_sent_time'],
            $_SESSION['manager_otp_expired']
        );
        echo json_encode(['success' => true, 'manager_id' => $manager_id]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Registration failed.']);
    }
    $stmt->close();
}

$check->close();
$conn->close();
?>
