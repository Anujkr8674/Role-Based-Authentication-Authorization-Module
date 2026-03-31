<?php
require '../config.php';

header('Content-Type: application/json');

$email = $_POST['email'] ?? '';
if (!$email) {
    echo json_encode(['exists' => false]);
    exit;
}

$stmt = $conn->prepare("SELECT id FROM `manager` WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

echo json_encode(['exists' => $stmt->num_rows > 0]);

$stmt->close();
$conn->close();
?>
