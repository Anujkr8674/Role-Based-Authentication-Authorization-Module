<?php
require_once __DIR__ . '/includes/admin-common.php';
require_once __DIR__ . '/includes/layout.php';

$admin_user = getCurrentAdmin($conn, $_SESSION['admin_id']);
if (!$admin_user) {
    header('Location: admin-logout.php');
    exit;
}

function generateUniqueEmployeeId(mysqli $conn): string
{
    do {
        $employee_id = (string) random_int(100000, 999999);
        $check_stmt = $conn->prepare("SELECT id FROM `employee` WHERE employee_id = ? LIMIT 1");
        $check_stmt->bind_param('s', $employee_id);
        $check_stmt->execute();
        $exists = $check_stmt->get_result()->fetch_assoc();
        $check_stmt->close();
    } while ($exists);

    return $employee_id;
}

$error_message = '';
$form_name = '';
$form_email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_employee'])) {
    $form_name = trim($_POST['name'] ?? '');
    $form_email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if ($form_name === '' || $form_email === '' || $password === '' || $confirm_password === '') {
        $error_message = 'All fields are required.';
    } elseif (!filter_var($form_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,16}$/', $password)) {
        $error_message = 'Password must be 8-16 chars, include upper, lower, digit, special char.';
    } elseif ($password !== $confirm_password) {
        $error_message = 'Password and confirm password do not match.';
    } else {
        $email_check = $conn->prepare("SELECT id FROM `employee` WHERE email = ? LIMIT 1");
        $email_check->bind_param('s', $form_email);
        $email_check->execute();
        $email_exists = $email_check->get_result()->fetch_assoc();
        $email_check->close();

        if ($email_exists) {
            $error_message = 'This email is already registered for another employee.';
        } else {
            $employee_id = generateUniqueEmployeeId($conn);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $conn->prepare("INSERT INTO `employee` (employee_id, name, email, password) VALUES (?, ?, ?, ?)");
            $insert_stmt->bind_param('ssss', $employee_id, $form_name, $form_email, $hashed_password);

            if ($insert_stmt->execute()) {
                $_SESSION['admin_employee_success'] = 'Employee created successfully. Employee ID: ' . $employee_id;
                header('Location: admin-manage-employee.php');
                exit;
            }

            $error_message = 'Unable to create employee account.';
            $insert_stmt->close();
        }
    }
}

$page_title = 'Add Employee';
$page_subtitle = 'Create an employee account directly from the admin panel';
$active_page = 'manage-employee';

adminLayoutStart($page_title, $page_subtitle, $admin_user, $active_page);
?>
<style>
    /* .form-shell {
        max-width: 780px;
    } */
    .password-field {
        position: relative;
        width: 100%;
    }
    .password-field input {
        width: 100%;
        padding-right: 50px;
    }
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

<section class="hero">
    <div>
        <h2>Add Employee</h2>
        <p>Create an employee account directly from admin without OTP. Employee ID will be generated automatically and saved into the `employee` table.</p>
    </div>
    <div class="tag"><i class="fa-solid fa-user-plus"></i> Create</div>
</section>

<?php if ($error_message !== ''): ?><div class="flash error"><?php echo htmlspecialchars($error_message); ?></div><?php endif; ?>

<section class="panel form-shell">
    <div class="panel-title">
        <div>
            <h3>Create Employee Account</h3>
            <p>Use the same password rule as the employee registration flow.</p>
        </div>
    </div>
    <form method="post">
        <div class="form-grid">
            <div class="group">
                <label for="name">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($form_name); ?>" placeholder="Enter employee name" required>
            </div>
            <div class="group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($form_email); ?>" placeholder="Enter employee email" required>
            </div>
            <div class="group">
                <label for="password">Password</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" placeholder="Enter strong password" required>
                    <button class="password-toggle" type="button" data-target="password" aria-label="Show password">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>
            <div class="group">
                <label for="confirm_password">Confirm Password</label>
                <div class="password-field">
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm password" required>
                    <button class="password-toggle" type="button" data-target="confirm_password" aria-label="Show confirm password">
                        <i class="fa-regular fa-eye"></i>
                    </button>
                </div>
            </div>
        </div>
        <div class="note">Password rule: 8-16 characters, one uppercase, one lowercase, one digit, and one special character.</div>
        <div class="actions">
            <button class="btn-primary" type="submit" name="create_employee"><i class="fa-solid fa-user-plus"></i> Add Employee</button>
            <a class="btn-secondary" href="admin-manage-employee.php"><i class="fa-solid fa-arrow-left"></i> Back to Employee Directory</a>
        </div>
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
