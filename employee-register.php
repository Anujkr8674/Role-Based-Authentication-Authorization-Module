<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'vendor/autoload.php'; // PHPMailer autoload
require 'config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Use $smtp_email and $smtp_app_password for PHPMailer SMTP credentials

function generateUserId($length = 6) {
    return str_pad(mt_rand(0, 999999), $length, '0', STR_PAD_LEFT);
}

if (isset($_POST['send_otp'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $otp = rand(100000, 999999);
    $_SESSION['register_otp'] = $otp;
    $_SESSION['otp_sent_time'] = time();
    $_SESSION['register_email'] = $email;
    $_SESSION['register_name'] = $name;

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_email;
        $mail->Password = $smtp_app_password;
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom($smtp_email, 'Your App Name');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Registration';
        $mail->Body    = "<h2>Your OTP is: <b>$otp</b></h2>";

        $mail->send();
        $otp_message = "OTP sent successfully to $email";
        echo '<script>window.onload = function(){ document.getElementById(\'otp-section\').style.display = \'block\'; document.getElementById(\'send-otp-btn\').disabled = true; };</script>';
    } catch (Exception $e) {
        $otp_message = "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

// On every page load, check OTP expiry
if (isset($_SESSION['otp_sent_time']) && (time() - $_SESSION['otp_sent_time'] > 300)) {
    unset($_SESSION['register_otp']);
    unset($_SESSION['otp_sent_time']);
    $_SESSION['otp_expired'] = true;
}

if (isset($_POST['verify_otp'])) {
    $user_otp = trim($_POST['otp']);
    if (isset($_SESSION['register_otp']) && $user_otp == $_SESSION['register_otp']) {
        $_SESSION['otp_verified'] = true;
        unset($_SESSION['register_otp']);
        unset($_SESSION['otp_sent_time']);
        unset($_SESSION['otp_expired']);
        $otp_verify_message = "OTP verified! Please set your password.";
    } else {
        $otp_verify_message = "Invalid OTP. Please try again.";
        $_SESSION['otp_verified'] = false;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Employee Register</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            min-height: 100vh;
            /* display: flex; */
            align-items: center;
            justify-content: center;
            /* background: #f8f9fb; */
              background: url('./includes/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            font-family: 'Segoe UI', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container1 {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 24px ;
            border: 1.5px solid black;
        }
        .profile-icon {
            display: flex;
            justify-content: center;
            margin: 32px 0 16px 0;
        }
        .profile-icon svg {
            width: 64px;
            height: 64px;
            color: #4f7cff;
            display: block;
        }
        .main-title {
            text-align: center;
            font-size: 1.35rem;
            font-weight: 600;
            margin-bottom: 0.3rem;
        }
        .subtitle {
            text-align: center;
            color: #8a8fa3;
            font-size: 1rem;
            margin-bottom: 1.5rem;
        }
        .form {
            padding: 0 24px;
        }
        .form-label {
            font-weight: 500;
            margin-bottom: 6px;
            display: block;
            color: #222;
        }
        .input-box {
            width: 100%;
            margin-bottom: 18px;
            position: relative;
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
        .password-box input {
            padding-right: 44px;
        }
        .password-toggle {
            position: absolute;
            right: 22px;
            top: 51px;
            transform: translateY(-50%);
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            padding: 0;
            cursor: pointer;
            color: #6c757d;
        }
        .password-toggle:hover {
            color: #222;
        }
        .password-toggle:focus {
            outline: 2px solid #4f7cff;
            outline-offset: 2px;
            border-radius: 6px;
        }
        .password-toggle svg {
            width: 20px;
            height: 20px;
            pointer-events: none;
        }
        .otp-btn, .verify-btn {
            position: absolute;
            right: 25px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #219a3b;
            font-weight: 500;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .otp-btn{
            top: 70% !important;
        }
        .otp-btn:disabled {
            color: #aaa;
            cursor: not-allowed;
        }
        .register-btn {
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
        .register-btn:hover {
            background: #3456b3;
        }
        .signin-link {
            text-align: center;
            margin-top: 10px;
            color: #7a7f9a;
            font-size: 1rem;
        }
        .signin-link a {
            color: #4f7cff;
            text-decoration: none;
            font-weight: 500;
        }
        .message {
            text-align: center;
            margin-bottom: 10px;
            color: #219a3b;
            font-size: 1rem;
        }
        .success-popup {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.45);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 20px;
        }
        .success-popup.show {
            display: flex;
        }
        .success-popup-card {
            width: 100%;
            max-width: 360px;
            background: #fff;
            border-radius: 16px;
            padding: 28px 24px;
            text-align: center;
            box-shadow: 0 20px 45px rgba(0, 0, 0, 0.18);
        }
        .success-popup-card h3 {
            margin: 0 0 10px;
            color: #219a3b;
            font-size: 1.35rem;
        }
        .success-popup-card p {
            margin: 0;
            color: #4a4a4a;
            line-height: 1.5;
        }
        .password-hint {
            color: #23238b;
            font-size: 0.95rem;
            /* margin-top: -10px; */
            margin-bottom: 2px;
            margin-left: 2px;
        }
        .password-error {
            color: #e74c3c;
            font-size: 0.97rem;
            margin-top: 2px;
            margin-bottom: 10px;
            margin-left: 2px;
            display: none;
        }
        @media (max-width: 500px) {
            .container1 {
                border-radius: 18px;
                box-shadow: none;
                padding: 24px ;
                margin: 24px;
            }
            .form {
                padding: 0 12px;
            }
        }
    </style>
    <script>
        function generateUserId() {
            return Math.floor(100000 + Math.random() * 900000).toString();
        }

        function validatePassword(pw) {
            return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,16}$/.test(pw);
        }

        function otpInputHandler(e) {
            let val = e.target.value.replace(/\D/g, '');
            if (val.length > 6) val = val.slice(0, 6);
            e.target.value = val;
        }

        function updatePasswordToggle(button, visible) {
            button.setAttribute('aria-label', visible ? 'Hide password' : 'Show password');
            button.setAttribute('aria-pressed', visible ? 'true' : 'false');
            button.innerHTML = visible
                ? '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M2 12s3.5-7 10-7c2.06 0 3.87.53 5.4 1.35"></path><path d="M22 12s-3.5 7-10 7c-2.06 0-3.87-.53-5.4-1.35"></path><path d="M2 2l20 20"></path></svg>'
                : '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7S1 12 1 12z"></path><circle cx="12" cy="12" r="3"></circle></svg>';
        }

        function setupPasswordToggle(buttonId, inputId) {
            const button = document.getElementById(buttonId);
            const input = document.getElementById(inputId);
            if (!button || !input) return;

            updatePasswordToggle(button, false);
            button.addEventListener('click', function() {
                const visible = input.type === 'text';
                input.type = visible ? 'password' : 'text';
                updatePasswordToggle(button, !visible);
            });
        }
    </script>
</head>
<body>

<!-- header -->
<br/><br/>


    <div class="container1">
        <div id="success-popup" class="success-popup">
            <div class="success-popup-card">
                <h3>Registration Successful</h3>
                <p id="success-popup-text">Your employee account has been created successfully. Redirecting to sign in...</p>
            </div>
        </div>
        <div class="profile-icon">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><circle cx="12" cy="8" r="4"></circle><path d="M4 20c0-4 4-7 8-7s8 3 8 7"></path></svg>
        </div>
        <div class="main-title">Create Your Employee Account</div>
        <div class="subtitle">Register to access your employee workspace</div>
        <!-- OTP Send Form -->
        <form id="otpForm" autocomplete="off">
            <div class="input-box">
                <label class="form-label" for="name">Name</label>
                <input type="text" id="name" name="name" placeholder="Enter your name" required>
            </div>
            <div class="input-box">
                <label class="form-label" for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                <button type="submit" id="send-otp-btn" class="otp-btn" disabled>Send OTP</button>
            </div>
        </form>
        <div class="input-box" id="otp-section" style="display:none; position:relative;">
            <input type="text" id="otp" name="otp" placeholder="Enter OTP" maxlength="6" inputmode="numeric" pattern="[0-9]*" autocomplete="one-time-code" oninput="this.value=this.value.replace(/\D/g,'').slice(0,6)">
            <button class="verify-btn" id="verify-otp-btn" style="position:absolute; right:25px; top:50%; transform:translateY(-50%);">Verify OTP</button>
        </div>
        <div id="otp-timer" style="color:#4f7cff; text-align:center; margin-bottom:10px;"></div>
        <div id="password-section" style="display:none;">
            <form id="registerForm" autocomplete="off">
                <div class="input-box password-box">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <button type="button" class="password-toggle" id="password-toggle" aria-label="Show password" aria-pressed="false"></button>
                    <div class="password-hint">Password must be 8-16 characters and include uppercase, lowercase, digit, and special character.</div>
                </div>
                <div class="input-box password-box">
                    <label class="form-label" for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required>
                    <button type="button" class="password-toggle" id="confirm-password-toggle" aria-label="Show password" aria-pressed="false"></button>
                </div>
                <button class="register-btn" type="submit">Register</button>
            </form>
            <div id="register-message" class="message"></div>
        </div>

           <div class="register-link">
            Already have an account? <a href="employee-signin.php">Sign In</a>
    </div> 
        <script>
        function checkNameEmail() {
            const name = document.getElementById('name').value.trim();
            const email = document.getElementById('email').value.trim();
            document.getElementById('send-otp-btn').disabled = !(name && email);
        }

        function startOTPTimer(sentTime) {
            const timerDiv = document.getElementById('otp-timer');
            let expiresIn = 300 - (Math.floor(Date.now() / 1000) - sentTime);

            function updateTimer() {
                if (expiresIn <= 0) {
                    timerDiv.innerText = 'OTP expired. Please request a new OTP.';
                    document.getElementById('send-otp-btn').disabled = false;
                } else {
                    let min = Math.floor(expiresIn / 60);
                    let sec = expiresIn % 60;
                    timerDiv.innerText = `OTP valid for: ${min}m ${sec}s`;
                    expiresIn--;
                    setTimeout(updateTimer, 1000);
                }
            }

            updateTimer();
        }

        function showRegistrationSuccessPopup(employeeId) {
            const popup = document.getElementById('success-popup');
            const text = document.getElementById('success-popup-text');
            text.innerText = `Your employee account has been created successfully. Employee ID: ${employeeId}. Redirecting to sign in...`;
            popup.classList.add('show');
            setTimeout(() => {
                window.location.href = 'employee-signin.php';
            }, 2200);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const nameInput = document.getElementById('name');
            const emailInput = document.getElementById('email');
            const otpInput = document.getElementById('otp');
            const passwordInput = document.getElementById('password');
            const registerForm = document.getElementById('registerForm');
            const otpForm = document.getElementById('otpForm');
            const verifyOtpBtn = document.getElementById('verify-otp-btn');

            window.generatedUserId = generateUserId();

            nameInput.addEventListener('input', checkNameEmail);
            emailInput.addEventListener('input', checkNameEmail);
            otpInput.addEventListener('input', otpInputHandler);
            checkNameEmail();
            setupPasswordToggle('password-toggle', 'password');
            setupPasswordToggle('confirm-password-toggle', 'confirm_password');

            otpForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const name = nameInput.value.trim();
                const email = emailInput.value.trim();
                const btn = document.getElementById('send-otp-btn');
                btn.disabled = true;
                btn.innerText = 'Checking...';

                const checkRes = await fetch('employee-check-email.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `email=${encodeURIComponent(email)}`
                });
                const checkData = await checkRes.json();
                if (checkData.exists) {
                    alert('This email is already registered. Please use another email or sign in.');
                    btn.disabled = false;
                    btn.innerText = 'Send OTP';
                    return;
                }

                btn.innerText = 'Sending...';
                const res = await fetch('employee-send-otp.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}`
                });
                const data = await res.json();

                if (data.success) {
                    alert(data.message);
                    otpInput.value = '';
                    document.getElementById('otp-section').style.display = 'block';
                    document.getElementById('otp-timer').style.display = 'block';
                    startOTPTimer(data.otp_sent_time);
                } else {
                    alert(data.message);
                    btn.disabled = false;
                }
                btn.innerText = 'Send OTP';
            });

            verifyOtpBtn.addEventListener('click', async function(e) {
                e.preventDefault();
                const otp = otpInput.value.trim();
                const btn = verifyOtpBtn;
                btn.disabled = true;
                btn.innerText = 'Verifying...';

                const res = await fetch('employee-verify-otp.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                    body: `otp=${encodeURIComponent(otp)}`
                });
                const data = await res.json();
                const msgDiv = document.getElementById('otp-message');

                btn.disabled = false;
                btn.innerText = 'Verify OTP';
                msgDiv.innerText = data.message;
                msgDiv.style.color = data.success ? '#219a3b' : '#e74c3c';

                if (data.success) {
                    document.getElementById('password-section').style.display = 'block';
                    document.getElementById('otp-section').style.display = 'none';
                    document.getElementById('otp-timer').style.display = 'none';
                } else {
                    otpInput.value = '';
                }
            });

            passwordInput.addEventListener('input', function() {
                if (validatePassword(this.value)) {
                    document.getElementById('register-message').innerText = '';
                }
            });

            registerForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                const password = passwordInput.value;
                const confirm = document.getElementById('confirm_password').value;
                const msgDiv = document.getElementById('register-message');
                const name = nameInput.value.trim();
                const email = emailInput.value.trim();
                const submitBtn = registerForm.querySelector('button[type="submit"]');
                const employeeId = window.generatedUserId || generateUserId();

                if (!validatePassword(password)) {
                    msgDiv.innerText = 'Password must be 8-16 chars, include upper, lower, digit, special char.';
                    msgDiv.style.color = '#e74c3c';
                    return;
                }

                if (password !== confirm) {
                    msgDiv.innerText = 'Passwords do not match.';
                    msgDiv.style.color = '#e74c3c';
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.innerText = 'Registering...';
                msgDiv.innerText = '';

                try {
                    const res = await fetch('employee-register-user.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                        body: `employee_id=${encodeURIComponent(employeeId)}&name=${encodeURIComponent(name)}&email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
                    });
                    const data = await res.json();

                    if (data.success) {
                        window.generatedUserId = data.employee_id;
                        showRegistrationSuccessPopup(data.employee_id);
                    } else {
                        msgDiv.innerText = data.message || 'Registration failed.';
                        msgDiv.style.color = '#e74c3c';
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Register';
                    }
                } catch (error) {
                    msgDiv.innerText = 'Registration failed. Please try again.';
                    msgDiv.style.color = '#e74c3c';
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Register';
                }
            });
        });
        </script>
        <div id="otp-message" class="message"></div>
    </div>
</body>
<br/><br/>
<!-- footer -->
</html>
