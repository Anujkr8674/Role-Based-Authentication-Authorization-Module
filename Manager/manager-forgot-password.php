<?php
session_start();

require '../config.php';
require '../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$step = 1;

if (isset($_POST['send_otp']) || isset($_POST['resend_otp'])) {
    $email = trim($_POST['email']);
    $stmt = $conn->prepare("SELECT id FROM `manager` WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        $message = "No account found with this email.";
    } else {
        $otp = rand(100000, 999999);
        $_SESSION['manager_fp_otp'] = $otp;
        $_SESSION['manager_fp_email'] = $email;
        $_SESSION['manager_fp_otp_time'] = time();

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
            $mail->Subject = 'Manager Account - OTP for Password Reset';
            $mail->Body = "
                <div style='font-family: Arial, sans-serif; font-size: 16px; color: #333;'>
                    <p>Dear Manager,</p>
                    <p>We received a request to reset your password for your <strong>Manager</strong> account.</p>
                    <p>Please use the following One-Time Password (OTP) to proceed:</p>
                    <h2 style='color: #2E86C1;'>Your OTP is: <strong>$otp</strong></h2>
                    <p><strong>Note:</strong> This OTP is valid for the next 5 minutes only. Do not share it with anyone.</p>
                    <br>
                    <p>If you did not request a password reset, please ignore this email.</p>
                    <br>
                    <p>Regards,<br><strong>Anuj Kumar</strong></p>
                </div>";
            $mail->send();
            $message = "OTP sent to your email.";
            $step = 2;
        } catch (Exception $e) {
            $message = "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }
    $stmt->close();
}

if (isset($_POST['verify_otp'])) {
    $otp = trim($_POST['otp']);
    $expired = !isset($_SESSION['manager_fp_otp_time']) || (time() - $_SESSION['manager_fp_otp_time'] >= 300);
    if (isset($_SESSION['manager_fp_otp']) && $otp == $_SESSION['manager_fp_otp'] && !$expired) {
        $_SESSION['manager_fp_verified'] = true;
        $message = "OTP verified! Please set your new password.";
        $step = 3;
    } elseif ($expired) {
        $message = "OTP expired. Please request a new OTP.";
        $step = 2;
    } else {
        $message = "Invalid OTP.";
        $step = 2;
    }
}

if (isset($_POST['reset_password'])) {
    if (!isset($_SESSION['manager_fp_verified']) || !$_SESSION['manager_fp_verified']) {
        $message = "OTP verification required.";
        $step = 1;
    } else {
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];
        if ($password !== $confirm) {
            $message = "Passwords do not match.";
            $step = 3;
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{8,16}$/', $password)) {
            $message = "Password must be 8-16 chars, include upper, lower, digit, special char.";
            $step = 3;
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $email = $_SESSION['manager_fp_email'];
            $stmt = $conn->prepare("UPDATE `manager` SET password = ? WHERE email = ?");
            $stmt->bind_param("ss", $hashed, $email);
            if ($stmt->execute()) {
                $message = "Password reset successful! You can now <a href='manager-signin.php'>login</a>.";
                unset($_SESSION['manager_fp_otp'], $_SESSION['manager_fp_email'], $_SESSION['manager_fp_otp_time'], $_SESSION['manager_fp_verified']);
                $step = 4;
                echo "<script>alert('Password reset successful! Please login.'); setTimeout(function(){ window.location.href = 'manager-signin.php'; }, 1000);</script>";
            } else {
                $message = "Error resetting password.";
                $step = 3;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta charset="UTF-8">
    <title>Manager Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            min-height: 100vh;
            align-items: center;
            justify-content: center;
            /* background: #f8f9fb; */
               background: url('./includes/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
        }

        .container1 {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            padding: 24px;
            border: 1.5px solid #222;
        }

        .main-title {
            text-align: center;
            font-size: 1.35rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }

        .message {
            text-align: center;
            margin-bottom: 10px;
            color: #219a3b;
            font-size: 1rem;
        }

        .error {
            color: #e74c3c;
        }

        .input-box {
            width: 100%;
            margin-bottom: 18px;
        }

        .input-box input {
            width: 95%;
            padding: 12px 10px;
            border: 1.5px solid #e3e6ee;
            border-radius: 8px;
            font-size: 1rem;
            background: #f5f7fa;
            outline: none;
            transition: border 0.2s;
        }

        .input-box input:focus {
            border: 1.5px solid #4f7cff;
            background: #fff;
        }

        .btn {
            width: 100%;
            padding: 13px 0;
            background: #4f7cff;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-bottom: 18px;
            margin-top: 8px;
            transition: background 0.2s;
        }

        .btn:hover {
            background: #3456b3;
        }

        #otp-timer {
            color: #4f7cff;
            text-align: center;
            margin-bottom: 10px;
        }

        .resend-btn {
            background: none;
            border: none;
            color: #219a3b;
            font-weight: 500;
            cursor: pointer;
            font-size: 0.95rem;
            text-decoration: underline;
            display: block;
            margin: 0 auto 10px auto;
        }

        .resend-btn:disabled {
            color: #aaa;
            cursor: not-allowed;
        }

        @media (max-width: 500px) {
            .container1 {
                margin: 20px;
                border: 1.5px solid black;
                padding: 24px;
                border-radius: 18px;
            }
        }
    </style>
    <script>
        function startOTPTimer(sentTime) {
            const timerDiv = document.getElementById('otp-timer');
            const resendBtn = document.getElementById('resend-otp-btn');
            let expiresIn = 300 - (Math.floor(Date.now() / 1000) - sentTime);
            function updateTimer() {
                if (expiresIn <= 0) {
                    timerDiv.innerText = 'OTP expired. Please request a new OTP.';
                    if (resendBtn) resendBtn.disabled = false;
                } else {
                    let min = Math.floor(expiresIn / 60);
                    let sec = expiresIn % 60;
                    timerDiv.innerText = `OTP valid for: ${min}m ${sec}s`;
                    if (resendBtn) resendBtn.disabled = true;
                    expiresIn--;
                    setTimeout(updateTimer, 1000);
                }
            }
            updateTimer();
        }

        document.addEventListener('DOMContentLoaded', function () {
            var otpInput = document.getElementById('otp');
            if (otpInput) {
                otpInput.addEventListener('input', function () {
                    let val = this.value.replace(/\D/g, '');
                    if (val.length > 6) val = val.slice(0, 6);
                    this.value = val;
                });
            }
            var otpForm = document.getElementById('otpForm');
            if (otpForm) {
                otpForm.addEventListener('submit', function (e) {
                    var otpVal = otpInput.value;
                    if (!/^\d{6}$/.test(otpVal)) {
                        alert('OTP must be exactly 6 digits.');
                        e.preventDefault();
                        return false;
                    }
                });
            }
        });
    </script>
</head>

<body>
    <br /><br />
    <div class="container1">
        <div class="main-title">Manager Forgot Password</div>
        <?php if ($message): ?>
            <div class="message<?php echo ($step == 2 && strpos($message, 'Invalid') !== false) ? ' error' : ''; ?>">
                <?php echo $message; ?></div>
        <?php endif; ?>

        <?php if ($step == 1): ?>
            <form method="post">
                <div class="input-box">
                    <label for="email">Enter your registered Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <button class="btn" type="submit" name="send_otp">Send OTP</button>
            </form>
        <?php elseif ($step == 2): ?>
            <form method="post" id="otpForm">
                <div class="input-box">
                    <label for="otp">Enter OTP sent to your email</label>
                    <input type="text" name="otp" id="otp" maxlength="6" pattern="\d{6}" required>
                </div>
                <div id="otp-timer"></div>
                <button class="btn" type="submit" name="verify_otp">Verify OTP</button>
            </form>
            <form method="post" style="margin-top:-10px;">
                <input type="hidden" name="email"
                    value="<?php echo htmlspecialchars($_SESSION['manager_fp_email'] ?? $_POST['email'] ?? ''); ?>">
                <button class="resend-btn" type="submit" name="resend_otp" id="resend-otp-btn" disabled>Resend OTP</button>
            </form>
            <script>
                <?php if (isset($_SESSION['manager_fp_otp_time'])): ?>
                    startOTPTimer(<?php echo $_SESSION['manager_fp_otp_time']; ?>);
                <?php endif; ?>
            </script>
        <?php elseif ($step == 3): ?>
            <form method="post">
                <div class="input-box">
                    <label for="password">New Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="input-box">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" required>
                </div>
                <button class="btn" type="submit" name="reset_password">Reset Password</button>
            </form>
        <?php elseif ($step == 4): ?>
            <div style="text-align:center; color:#27ae60; font-size:1.1rem;"><?php echo $message; ?></div>
        <?php endif; ?>
    </div>
</body>

</html>
<br /><br />
