<?php
ob_start();
session_start();
require 'config.php';

function buildCookieOptions(int $expires, bool $is_secure): array
{
    $options = [
        'expires' => $expires,
        'path' => '/',
        'secure' => $is_secure,
        'httponly' => true,
        'samesite' => 'Lax'
    ];

    $host = $_SERVER['HTTP_HOST'] ?? '';
    if ($host && $host !== 'localhost' && $host !== '127.0.0.1') {
        $options['domain'] = preg_replace('/:\d+$/', '', $host);
    }

    return $options;
}

if (isset($_SESSION['employee_id']) || isset($_COOKIE['employee_auth_token'])) {
    header("Location: employee-dashboard.php");
    exit;
}

$error_message = "";
$success_message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $login_id = trim($_POST['login_id']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, employee_id, name, email, password FROM employee WHERE email = ? OR employee_id = ?");
    $stmt->bind_param("ss", $login_id, $login_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['password'])) {
            try {
                $token = bin2hex(random_bytes(32));
            } catch (Throwable $e) {
                if (function_exists('openssl_random_pseudo_bytes')) {
                    $token = bin2hex(openssl_random_pseudo_bytes(32));
                } else {
                    $token = bin2hex(md5(uniqid(mt_rand(), true)));
                }
            }

            $token_expiry = date('Y-m-d H:i:s', strtotime('+7 days'));
            $last_login_exists = false;
            $last_login_check = $conn->query("SHOW COLUMNS FROM `employee` LIKE 'last_login'");
            if ($last_login_check && $last_login_check->num_rows > 0) {
                $last_login_exists = true;
            }
            if ($last_login_check instanceof mysqli_result) {
                $last_login_check->free();
            }

            if ($last_login_exists) {
                $update_stmt = $conn->prepare("UPDATE employee SET auth_token = ?, token_expiry = ?, last_login = NOW() WHERE id = ?");
                $update_stmt->bind_param("ssi", $token, $token_expiry, $row['id']);
            } else {
                $update_stmt = $conn->prepare("UPDATE employee SET auth_token = ?, token_expiry = ? WHERE id = ?");
                $update_stmt->bind_param("ssi", $token, $token_expiry, $row['id']);
            }
            $update_stmt->execute();
            $update_stmt->close();

            $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

            setcookie('employee_auth_token', $token, buildCookieOptions(time() + (7 * 24 * 60 * 60), $is_secure));

            session_regenerate_id(true);
            $_SESSION['employee_id'] = $row['employee_id'];
            $_SESSION['employee_name'] = $row['name'];
            $_SESSION['employee_email'] = $row['email'];
            $_SESSION['employee_auth_token'] = $token;

            $success_message = "Login successful! Redirecting...";
            $redirect = 'employee-dashboard.php';
            if (!headers_sent()) {
                header("Location: $redirect");
                exit;
            }

            echo '<script>window.location.href = ' . json_encode($redirect) . ';</script>';
            echo '<meta http-equiv="refresh" content="0;url=' . $redirect . '">';
            exit;
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No employee found with this Email/Employee ID.";
    }

    $stmt->close();
}
ob_end_flush();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Employee Sign In</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
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
            margin: 60px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
            padding: 0 0 24px 0;
            border: 1.5px solid #000;
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
        }

        .password-field {
            position: relative;
        }

        .input-box input {
            width: 100%;
            padding: 12px 10px;
            border: 1.5px solid #e3e6ee;
            border-radius: 8px;
            font-size: 1rem;
            background: #f5f7fa;
            transition: border 0.2s;
        }

        .input-box input:focus {
            border: 1.5px solid #4f7cff;
            background: #fff;
        }

        .password-field input {
            padding-right: 44px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #7a7f9a;
            cursor: pointer;
            width: 28px;
            height: 28px;
            display: grid;
            place-items: center;
        }

        .login-btn {
            width: 100%;
            padding: 13px 0;
            background: #4f7cff;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            margin-top: 8px;
            transition: background 0.2s;
        }

        .login-btn:hover {
            background: #3456b3;
        }

        .register-link {
            text-align: center;
            margin-top: 10px;
            color: #7a7f9a;
            font-size: 1rem;
        }

        .register-link a {
            color: #4f7cff;
            text-decoration: none;
            font-weight: 500;
        }

        @media (max-width: 500px) {
            .container1 {
                max-width: 360px;
            }
        }
    </style>
</head>

<body>
    <br /><br />
    <div class="container1">
        <div class="profile-icon">
            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <circle cx="12" cy="8" r="4"></circle>
                <path d="M4 20c0-4 4-7 8-7s8 3 8 7"></path>
            </svg>
        </div>
        <div class="main-title">Login to Your Employee Account</div>
        <div class="subtitle">Access your employee workspace</div>
        <?php if ($error_message): ?>
            <div class="message" style="color:#e74c3c; text-align:center;"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if ($success_message): ?>
            <div class="message" style="color:#27ae60; text-align:center;"><?php echo $success_message; ?></div>
        <?php endif; ?>
        <form class="form" method="post" action="">
            <div class="input-box">
                <label class="form-label" for="login_id">Email or Employee ID</label>
                <input type="text" id="login_id" name="login_id" placeholder="Enter your email or employee ID" required>
            </div>
            <div class="input-box">
                <label class="form-label" for="password">Password</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    <button class="password-toggle" type="button" id="togglePassword" aria-label="Show password">
                        <i class="fa-regular fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>
            <button class="login-btn" type="submit" name="login">Login</button>
        </form>
        <div style="text-align:center; margin-top:5px; margin-bottom:10px;">
            <a href="employee-forgot-password.php" style="color:#4f7cff; text-decoration:none; font-size:1rem;">Forgot
                password?</a>
        </div>
        <div class="register-link">
            Don't have an account? <a href="employee-register.php">Register</a>
        </div>
    </div>
    <br /><br />
    <script>
        const passwordInput = document.getElementById('password');
        const togglePassword = document.getElementById('togglePassword');
        const togglePasswordIcon = document.getElementById('togglePasswordIcon');

        togglePassword.addEventListener('click', function () {
            const showPassword = passwordInput.type === 'password';
            passwordInput.type = showPassword ? 'text' : 'password';
            togglePasswordIcon.className = showPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
            togglePassword.setAttribute('aria-label', showPassword ? 'Hide password' : 'Show password');
        });
    </script>
</body>

</html>
