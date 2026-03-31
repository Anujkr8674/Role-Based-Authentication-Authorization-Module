<?php
session_start();
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_otp = trim($_POST['otp'] ?? '');
    if (isset($_SESSION['register_otp']) && $user_otp == $_SESSION['register_otp']) {
        $_SESSION['otp_verified'] = true;
        unset($_SESSION['register_otp']);
        unset($_SESSION['otp_sent_time']);
        unset($_SESSION['otp_expired']);
        echo json_encode(['success' => true, 'message' => 'OTP verified! Please set your employee password.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid OTP. Please try again.']);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']); 
