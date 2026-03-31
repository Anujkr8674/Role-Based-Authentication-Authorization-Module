<?php
session_start();
if (isset($_SESSION['admin_id']) || isset($_COOKIE['admin_auth_token'])) {
    header('Location: admin-dashboard.php');
    exit;
}
$error = $_SESSION['admin_login_error'] ?? '';
unset($_SESSION['admin_login_error']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
               background: url('./includes/bg.jpg') no-repeat center center fixed;
            background-size: cover;
            /* background: radial-gradient(circle at top left, rgba(80, 109, 99, .18), transparent 28%), radial-gradient(circle at bottom right, rgba(217, 145, 82, .16), transparent 26%), linear-gradient(180deg, #f7f7f3, #fcfbf7); */
            font-family: 'Segoe UI', Arial, sans-serif
        }

        .signin-card {
            width: 100%;
            max-width: 430px;
            background: #fff;
            border: 1px solid #dddccf;
            border-radius: 26px;
            padding: 28px;
            box-shadow: 0 14px 36px rgba(80, 109, 99, .12)
        }

        .badge-icon {
            width: 74px;
            height: 74px;
            margin: 0 auto 18px;
            border-radius: 24px;
            background: linear-gradient(135deg, #91b4a8, #f2bc84);
            display: grid;
            place-items: center;
            color: #fff;
            font-size: 1.7rem
        }

        .title {
            text-align: center;
            font-size: 1.55rem;
            font-weight: 800;
            color: #27322f
        }

        .subtitle {
            text-align: center;
            color: #6c7674;
            margin: 8px 0 20px
        }

        .form-label {
            font-weight: 700;
            color: #3f4b48
        }

        .form-control {
            padding: 13px 14px;
            border-radius: 16px;
            border: 1px solid #d8d8ce;
            background: #fff
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #8ba79e
        }

        .password-wrap {
            position: relative
        }

        .password-wrap .form-control {
            padding-right: 50px
        }

        .toggle-password {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #6c7674;
            width: 32px;
            height: 32px;
            display: grid;
            place-items: center;
            cursor: pointer
        }

        .btn-login {
            width: 100%;
            padding: 13px 0;
            border: none;
            border-radius: 16px;
            background: linear-gradient(135deg, #506d63, #31463f);
            color: #fff;
            font-weight: 700
        }

        .error-box {
            background: #fff1ec;
            border: 1px solid #f1cfbf;
            color: #c55c3f;
            padding: 12px 14px;
            border-radius: 14px;
            margin-bottom: 16px
        }
    </style>
</head>

<body>
    <div class="signin-card">
        <div class="badge-icon"><i class="fa-solid fa-lock"></i></div>
        <div class="title">Admin Sign In</div>
        <div class="subtitle">Secure access to the admin control room</div>
        <?php if ($error): ?>
            <div class="error-box"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
        <form method="POST" action="admin-login.php">
            <div class="mb-3">
                <label class="form-label" for="email">Email or Admin ID</label>
                <input class="form-control" id="email" name="email" type="text" value="ADM000001"
                    placeholder="Enter your email or admin ID" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <div class="password-wrap">
                    <input class="form-control" id="password" name="password" type="password" value="Anuj#0000"
                        placeholder="Enter your password" required>
                    <button class="toggle-password" type="button" id="togglePassword" aria-label="Show password">
                        <i class="fa-regular fa-eye" id="togglePasswordIcon"></i>
                    </button>
                </div>
            </div>
            <button class="btn-login" type="submit">Login</button>
        </form>
    </div>
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