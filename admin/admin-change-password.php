<?php
require_once __DIR__ . '/includes/admin-common.php';
require_once __DIR__ . '/includes/layout.php';
$admin_user = getCurrentAdmin($conn, $_SESSION['admin_id']);
if (!$admin_user) { header('Location: admin-logout.php'); exit; }
$success_message = $_SESSION['admin_password_success'] ?? '';
$error_message = $_SESSION['admin_password_error'] ?? '';
unset($_SESSION['admin_password_success'], $_SESSION['admin_password_error']);
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    if ($current_password === '' || $new_password === '' || $confirm_password === '') {
        $_SESSION['admin_password_error'] = 'All password fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['admin_password_error'] = 'New password and confirm password do not match.';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\\d)(?=.*[^A-Za-z0-9]).{8,16}$/', $new_password)) {
        $_SESSION['admin_password_error'] = 'Password must be 8-16 chars, include upper, lower, digit, special char.';
    } else {
        $stmt = $conn->prepare("SELECT password FROM admin WHERE admin_id = ?");
        $stmt->bind_param('s', $_SESSION['admin_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        if (!$row || !password_verify($current_password, $row['password'])) {
            $_SESSION['admin_password_error'] = 'Current password is incorrect.';
        } else {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $conn->prepare("UPDATE admin SET password = ? WHERE admin_id = ?");
            $update->bind_param('ss', $hashed, $_SESSION['admin_id']);
            if ($update->execute()) { $_SESSION['admin_password_success'] = 'Password changed successfully.'; } else { $_SESSION['admin_password_error'] = 'Password change failed.'; }
            $update->close();
        }
    }
    header('Location: admin-change-password.php');
    exit;
}
$page_title = 'Change Password';
$page_subtitle = 'Update admin password securely';
$active_page = 'change-password';
adminLayoutStart($page_title, $page_subtitle, $admin_user, $active_page);
?>
<style>
    .password-field { position: relative; width: 100%; }
    .password-field input { width: 100%; padding-right: 50px; }
    .password-toggle {
        position: absolute;
        top: 50%;
        right: 14px;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #6b7280;
        cursor: pointer;
        width: 28px;
        height: 28px;
        display: grid;
        place-items: center;
        z-index: 2;
    }
</style>
<section class="hero"><div><h2>Change Password</h2><p>Verify the current password and set a new secure password for the admin account.</p></div><div class="tag"><i class="fa-solid fa-key"></i> Security</div></section>
<?php if ($success_message): ?><div class="flash success"><?php echo htmlspecialchars($success_message); ?></div><?php endif; ?>
<?php if ($error_message): ?><div class="flash error"><?php echo htmlspecialchars($error_message); ?></div><?php endif; ?>
<section class="panel">
    <div class="panel-title"><div><h3>Update Password</h3><p>Keep the admin account secure with a strong password.</p></div></div>
    <form method="post">
        <div class="form-grid">
            <div class="group full"><label for="current_password">Current Password</label><div class="password-field"><input type="password" id="current_password" name="current_password" required><button class="password-toggle" type="button" data-target="current_password" aria-label="Show current password"><i class="fa-regular fa-eye"></i></button></div></div>
            <div class="group"><label for="new_password">New Password</label><div class="password-field"><input type="password" id="new_password" name="new_password" required><button class="password-toggle" type="button" data-target="new_password" aria-label="Show new password"><i class="fa-regular fa-eye"></i></button></div></div>
            <div class="group"><label for="confirm_password">Confirm Password</label><div class="password-field"><input type="password" id="confirm_password" name="confirm_password" required><button class="password-toggle" type="button" data-target="confirm_password" aria-label="Show confirm password"><i class="fa-regular fa-eye"></i></button></div></div>
        </div>
        <div class="note">Password rule: 8-16 characters, one uppercase, one lowercase, one digit, and one special character.</div>
        <div class="actions"><button class="btn-primary" type="submit" name="change_password"><i class="fa-solid fa-key"></i> Update Password</button></div>
    </form>
</section>
<script>
    document.querySelectorAll('.password-toggle').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.getElementById(button.dataset.target);
            const icon = button.querySelector('i');
            const isPassword = input.type === 'password';
            input.type = isPassword ? 'text' : 'password';
            icon.className = isPassword ? 'fa-regular fa-eye-slash' : 'fa-regular fa-eye';
        });
    });
</script>
<?php adminLayoutEnd(); ?>
