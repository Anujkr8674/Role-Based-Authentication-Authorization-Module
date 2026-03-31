<?php
require __DIR__ . '/includes/employee-common.php';
require __DIR__ . '/includes/layout.php';

$success_message = $_SESSION['employee_profile_success'] ?? '';
$error_message = $_SESSION['employee_profile_error'] ?? '';
unset($_SESSION['employee_profile_success'], $_SESSION['employee_profile_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($name === '') {
        $_SESSION['employee_profile_error'] = 'Name is required.';
    } elseif ($phone !== '' && !preg_match('/^\d{10}$/', $phone)) {
        $_SESSION['employee_profile_error'] = 'Phone number must be exactly 10 digits.';
    } else {
        $update_stmt = $conn->prepare("UPDATE `employee` SET name = ?, phone = ? WHERE employee_id = ?");
        $update_stmt->bind_param('sss', $name, $phone, $_SESSION['employee_id']);
        if ($update_stmt->execute()) {
            $_SESSION['employee_name'] = $name;
            $_SESSION['employee_profile_success'] = 'Profile updated successfully.';
        } else {
            $_SESSION['employee_profile_error'] = 'Profile update failed.';
        }
        $update_stmt->close();
    }

    header('Location: employee-edit-profile.php');
    exit;
}

$employee = getCurrentEmployee($conn, $_SESSION['employee_id']);
if (!$employee) {
    header('Location: employee-logout.php');
    exit;
}

$page_title = 'Edit Employee Profile';
$page_subtitle = 'Update employee profile details cleanly';
$active_page = 'edit-profile';

employeeLayoutStart($page_title, $page_subtitle, $employee, $active_page);
?>
<section class="hero">
    <div>
        <h2>Edit Profile</h2>
        <p>Update the editable employee profile details here. Employee ID and email are locked for security.</p>
    </div>
    <div class="tag"><i class="fa-solid fa-user-pen"></i> Profile</div>
</section>

<?php if ($success_message !== ''): ?><div class="flash success"><?php echo htmlspecialchars($success_message); ?></div><?php endif; ?>
<?php if ($error_message !== ''): ?><div class="flash error"><?php echo htmlspecialchars($error_message); ?></div><?php endif; ?>

<section class="panel">
    <div class="panel-title">
        <div>
            <h3>Edit Employee Profile</h3>
            <p>Update form for editable employee profile fields.</p>
        </div>
    </div>
    <form method="post">
        <div class="form-grid">
            <div class="group">
                <label for="employee_id">Employee ID</label>
                <input class="readonly-input" type="text" id="employee_id" value="<?php echo htmlspecialchars($employee['employee_id']); ?>" readonly>
                <small>This field is locked and cannot be edited.</small>
            </div>
            <div class="group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
            </div>
            <div class="group">
                <label for="email">Email</label>
                <input class="readonly-input" type="email" id="email" value="<?php echo htmlspecialchars($employee['email']); ?>" readonly>
                <small>This field is locked and cannot be edited.</small>
            </div>
            <div class="group">
                <label for="phone">Phone</label>
                <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($employee['phone'] ?? ''); ?>" placeholder="Enter 10-digit phone number" inputmode="numeric" pattern="\d{10}" maxlength="10" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10);">
                <small>Add Put your Mobile No.</small>
            </div>
        </div>
        <div class="actions">
            <button class="btn-primary" type="submit" name="update_profile"><i class="fa-solid fa-floppy-disk"></i> Save Changes</button>
            <a class="btn-secondary" href="employee-dashboard.php"><i class="fa-solid fa-arrow-left"></i> Back to Overview</a>
        </div>
    </form>
</section>
<?php employeeLayoutEnd(); ?>
