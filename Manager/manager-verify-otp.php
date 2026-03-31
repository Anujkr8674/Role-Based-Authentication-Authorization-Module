<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_otp = trim($_POST['otp'] ?? '');
    if (isset($_SESSION['manager_register_otp']) && $user_otp == $_SESSION['manager_register_otp']) {
        $_SESSION['manager_otp_verified'] = true;
        unset($_SESSION['manager_register_otp'], $_SESSION['manager_otp_sent_time'], $_SESSION['manager_otp_expired']);
        echo json_encode(['success' => true, 'message' => 'OTP verified! Please set your password.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?>
