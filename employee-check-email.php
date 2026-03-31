<?php
require 'config.php';
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
if (!$email) {
    echo json_encode(['exists' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM employee WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo json_encode(['exists' => true]);
} else {
    echo json_encode(['exists' => false]);
}
$stmt->close();
$conn->close();
?> 
