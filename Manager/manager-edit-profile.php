<?php
require __DIR__ . '/includes/manager-common.php';
require __DIR__ . '/includes/layout.php';

$success_message = $_SESSION['manager_profile_success'] ?? '';
$error_message = $_SESSION['manager_profile_error'] ?? '';
unset($_SESSION['manager_profile_success'], $_SESSION['manager_profile_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name === '') {
        $_SESSION['manager_profile_error'] = 'Name is required.';
    } elseif ($phone !== '' && !preg_match('/^\d{10}$/', $phone)) {
        $_SESSION['manager_profile_error'] = 'Phone number must be exactly 10 digits.';
    } else {
        $update_stmt = $conn->prepare("UPDATE `manager` SET name = ?, phone = ? WHERE manager_id = ?");
        $update_stmt->bind_param('sss', $name, $phone, $_SESSION['manager_id']);
        if ($update_stmt->execute()) {
            $_SESSION['manager_name'] = $name;
            $_SESSION['manager_profile_success'] = 'Profile updated successfully.';
        } else {
            $_SESSION['manager_profile_error'] = 'Profile update failed.';
        }
        $update_stmt->close();
    }

    header('Location: manager-edit-profile.php');
    exit;
}

$manager = getCurrentManager($conn, $_SESSION['manager_id']);
if (!$manager) {
    header('Location: manager-logout.php');
    exit;
}

$page_title = 'Edit Manager Profile';
$page_subtitle = 'Update manager profile details cleanly';
$active_page = 'edit-profile';

managerLayoutStart($page_title, $page_subtitle, $manager, $active_page);
?>
<section class="hero">
    <div>
        <h2>Edit Profile</h2>
        <p>Update the editable manager profile details here. Manager ID and email are locked for security.</p>
    </div>
    <div class="tag"><i class="fa-solid fa-user-pen"></i> Profile</div>
</section>

<?php if ($success_message !== ''): ?>
    <div class="flash success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>
<?php if ($error_message !== ''): ?>
    <div class="flash error"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>

<section class="panel">
    <div class="panel-title">
        <div>
            <h3>Edit Manager Profile</h3>
            <p>Update form for editable manager profile fields.</p>
        </div>
    </div>
    <form method="post">
        <div class="form-grid">
            <div class="group">
                <label for="manager_id">Manager ID</label>
                <input class="readonly-input" type="text" id="manager_id" value="<?php echo htmlspecialchars($manager['manager_id']); ?>" readonly>
                <small>This field is locked and cannot be edited.</small>
            </div>
            <div class="group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($manager['name']); ?>" required>
            </div>
            <div class="group">
                <label for="email">Email</label>
                <input class="readonly-input" type="email" id="email" value="<?php echo htmlspecialchars($manager['email']); ?>" readonly>
                <small>This field is locked and cannot be edited.</small>
            </div>
            <div class="group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($manager['phone'] ?? ''); ?>" placeholder="Enter 10-digit phone number" inputmode="numeric" pattern="\d{10}" maxlength="10" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10);">
                <small>Add Put your Mobile No.</small>
            </div>
        </div>
        <div class="actions">
            <button class="btn-primary" type="submit" name="update_profile"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
            <a class="btn-secondary" href="manager-dashboard.php"><i class="fa-solid fa-arrow-left"></i> Back to Overview</a>
        </div>
    </form>
</section>
<?php
managerLayoutEnd();
?>
