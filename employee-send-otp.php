<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (!$name || !$email) {
        echo json_encode(['success' => false, 'message' => 'Name and email required.']);
        exit;
    }

    require 'vendor/autoload.php';
    require 'config.php';

    $otp = rand(100000, 999999);
    $_SESSION['register_otp'] = $otp;
    $_SESSION['register_email'] = $email;
    $_SESSION['register_name'] = $name;
    $_SESSION['otp_sent_time'] = time();
    unset($_SESSION['otp_expired']);
    unset($_SESSION['otp_verified']);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_email;
        $mail->Password = $smtp_app_password; // NO SPACES
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($smtp_email, 'Anuj Kumar');
        $mail->addAddress($email);

        $mail->isHTML(true);
        // $mail->Subject = 'Your OTP for Registration';
        // $mail->Body    = "<h2>Your OTP is: <b>$otp</b></h2>";

        $mail->Subject = 'Your OTP for Employee Registration';
       $mail->Body = '<div style="font-size:16px; font-family: Arial, sans-serif;"><br><br>'
            . '🌟 <b>Welcome!</b> 🌟<br><br>'
            . 'You are just one step away from completing your registration.<br>'
            . 'Please verify your OTP to proceed and access your account.<br><br>'
            
            . '<b>Your OTP is: <span style="font-size:20px; color:#2c3e50;">' . $otp . '</span></b><br><br>'
            
            . 'This OTP is valid for a limited time. Please do not share it with anyone.<br><br>'
            
            . 'Once verified, you will be able to access your dashboard and continue further.<br><br>'
            
            . 'Best Regards,<br>'
            . '<b>Anuj Kumar</b><br>'
            . '</div>';

        $mail->send();
        echo json_encode(['success' => true, 'message' => "OTP sent successfully to $email", 'otp_sent_time' => $_SESSION['otp_sent_time']]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
    exit;
}
echo json_encode(['success' => false, 'message' => 'Invalid request.']); 
