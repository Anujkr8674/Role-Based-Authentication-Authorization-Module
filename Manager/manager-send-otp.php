<?php
session_start();
require '../vendor/autoload.php';
require '../config.php';

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

    $otp = rand(100000, 999999);
    $_SESSION['manager_register_otp'] = $otp;
    $_SESSION['manager_register_email'] = $email;
    $_SESSION['manager_register_name'] = $name;
    $_SESSION['manager_otp_sent_time'] = time();
    unset($_SESSION['manager_otp_expired'], $_SESSION['manager_otp_verified']);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_email;
        $mail->Password = $smtp_app_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($smtp_email, 'Anuj Kumar');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Manager Registration';
        $mail->Body = '<div style="font-size:16px;">'
            . '<p>Hello <b>' . htmlspecialchars($name) . '</b>,</p>'
            . '<p>Your OTP for manager registration is below:</p>'
            . '<b>Your OTP is: <span style="font-size:20px;">' . $otp . '</span></b><br><br>'
            . '<p>This OTP is valid for 5 minutes.</p>'
            . '<p>Regards,<br><b>Anuj Kumar</b></p>'
            . '</div>';

        $mail->send();
        echo json_encode([
            'success' => true,
            'message' => "OTP sent successfully to $email",
            'otp_sent_time' => $_SESSION['manager_otp_sent_time']
        ]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request.']);
?>
