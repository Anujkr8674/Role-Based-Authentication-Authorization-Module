<?php
require_once __DIR__ . '/includes/admin-common.php';
require_once __DIR__ . '/includes/layout.php';

$admin_user = getCurrentAdmin($conn, $_SESSION['admin_id']);
if (!$admin_user) {
    header('Location: admin-logout.php');
    exit;
}

$success_message = $_SESSION['admin_employee_success'] ?? '';
$error_message = $_SESSION['admin_employee_error'] ?? '';
unset($_SESSION['admin_employee_success'], $_SESSION['admin_employee_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employee_action'], $_POST['employee_id'])) {
    $employee_id = trim($_POST['employee_id']);
    $employee_action = $_POST['employee_action'];

    if ($employee_action === 'delete') {
        $delete_stmt = $conn->prepare("DELETE FROM `employee` WHERE employee_id = ?");
        $delete_stmt->bind_param('s', $employee_id);
        if ($delete_stmt->execute()) {
            $_SESSION['admin_employee_success'] = 'Employee deleted successfully.';
        } else {
            $_SESSION['admin_employee_error'] = 'Unable to delete employee.';
        }
        $delete_stmt->close();
    } elseif ($employee_action === 'edit') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($name === '' || $email === '') {
            $_SESSION['admin_employee_error'] = 'Name and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['admin_employee_error'] = 'Please enter a valid email address.';
        } elseif ($phone !== '' && !preg_match('/^\d{10}$/', $phone)) {
            $_SESSION['admin_employee_error'] = 'Phone number must be exactly 10 digits.';
        } else {
            $email_check = $conn->prepare("SELECT employee_id FROM `employee` WHERE email = ? AND employee_id != ? LIMIT 1");
            $email_check->bind_param('ss', $email, $employee_id);
            $email_check->execute();
            $email_exists = $email_check->get_result()->fetch_assoc();
            $email_check->close();

            if ($email_exists) {
                $_SESSION['admin_employee_error'] = 'This email is already assigned to another employee.';
            } else {
                $update_stmt = $conn->prepare("UPDATE `employee` SET name = ?, email = ?, phone = ? WHERE employee_id = ?");
                $update_stmt->bind_param('ssss', $name, $email, $phone, $employee_id);
                if ($update_stmt->execute()) {
                    $_SESSION['admin_employee_success'] = 'Employee details updated successfully.';
                } else {
                    $_SESSION['admin_employee_error'] = 'Unable to update employee details.';
                }
                $update_stmt->close();
            }
        }
    }

    header('Location: admin-manage-employee.php');
    exit;
}

$employees = [];
$has_updated_at = false;
$column_result = $conn->query("SHOW COLUMNS FROM `employee` LIKE 'updated_at'");
if ($column_result && $column_result->num_rows > 0) {
    $has_updated_at = true;
}

$employee_query = $has_updated_at
    ? "SELECT id, employee_id, name, email, phone, last_login, created_at, updated_at FROM `employee` ORDER BY id DESC"
    : "SELECT id, employee_id, name, email, phone, last_login, created_at, NULL AS updated_at FROM `employee` ORDER BY id DESC";

$result = $conn->query($employee_query);
if ($result) {
    $employees = $result->fetch_all(MYSQLI_ASSOC);
}

$page_title = 'Manage Employee';
$page_subtitle = 'Review and control employee accounts from one place';
$active_page = 'manage-employee';

adminLayoutStart($page_title, $page_subtitle, $admin_user, $active_page);
?>
<style>
    .directory-table .view-btn,
    .directory-table .edit-btn,
    .directory-table .delete-btn,
    .mobile-card .view-btn,
    .mobile-card .edit-btn,
    .mobile-card .delete-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        border-radius: 12px;
        padding: 10px 14px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
    }
    .view-btn {
        background: #edf7f1;
        color: var(--brand);
    }
    .edit-btn {
        background: #f5efd9;
        color: #8d6b18;
    }
    .delete-btn {
        background: #fff0ec;
        color: #c4543e;
    }
    .action-stack {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }
    .inline-form {
        margin: 0;
    }
    .detail-row {
        display: none;
        background: #fbfefc;
    }
    .detail-row.open {
        display: table-row;
    }
    .detail-cell {
        padding: 0;
    }
    .detail-box {
        padding: 22px;
        background: linear-gradient(180deg, #fbfefc, #f2faf5);
    }
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
        margin-bottom: 18px;
    }
    .detail-item {
        padding: 14px 16px;
        border: 1px solid #dcebe0;
        border-radius: 16px;
        background: #ffffff;
    }
    .detail-item span {
        display: block;
        font-size: .78rem;
        font-weight: 700;
        color: var(--muted);
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 6px;
    }
    .detail-item strong {
        display: block;
        word-break: break-word;
    }
    .detail-form {
        border-top: 1px solid #dcebe0;
        padding-top: 18px;
    }
    .detail-form h4 {
        margin: 0 0 14px;
    }
    .mobile-list {
        display: none;
        gap: 16px;
    }
    .mobile-card {
        border: 1px solid #dcebe0;
        border-radius: 20px;
        background: linear-gradient(180deg, #ffffff, #f4faf6);
        padding: 18px;
    }
    .mobile-head {
        display: flex;
        justify-content: space-between;
        gap: 14px;
        align-items: flex-start;
        margin-bottom: 14px;
    }
    .mobile-head h4,
    .mobile-head p {
        margin: 0;
    }
    .mobile-head p {
        color: var(--muted);
        margin-top: 6px;
    }
    .mobile-meta {
        display: grid;
        gap: 10px;
        margin-bottom: 14px;
    }
    .mobile-meta div {
        padding: 12px 14px;
        border-radius: 14px;
        background: #ffffff;
        border: 1px solid #e3eee6;
    }
    .mobile-meta span {
        display: block;
        color: var(--muted);
        font-size: .78rem;
        margin-bottom: 4px;
        text-transform: uppercase;
        letter-spacing: .04em;
        font-weight: 700;
    }
    .mobile-details {
        display: none;
        margin-top: 16px;
        padding-top: 16px;
        border-top: 1px solid #dcebe0;
    }
    .mobile-details.open {
        display: block;
    }
    .mobile-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 12px;
    }
    .header-actions {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 18px;
    }
    @media (max-width:860px) {
        .desktop-directory {
            display: none;
        }
        .mobile-list {
            display: grid;
        }
        .detail-grid {
            grid-template-columns: 1fr;
        }
        .header-actions {
            justify-content: stretch;
        }
        .header-actions a {
            width: 100%;
            justify-content: center;
        }
    }
</style>

<section class="hero">
    <div>
        <h2>Manage Employee</h2>
        <p>Review employee accounts, inspect essential details, and run quick admin actions from the same screen.</p>
    </div>
    <div class="tag"><i class="fa-solid fa-users-gear"></i> Employee</div>
</section>

<?php if ($success_message !== ''): ?><div class="flash success"><?php echo htmlspecialchars($success_message); ?></div><?php endif; ?>
<?php if ($error_message !== ''): ?><div class="flash error"><?php echo htmlspecialchars($error_message); ?></div><?php endif; ?>

<section class="panel">
    <div class="panel-title">
        <div>
            <h3>Employee Directory</h3>
            <!-- <p>View, edit, or remove employee records from the `employee` table.</p> -->
        </div>
         <div class="header-actions">
        <a class="btn-primary" href="admin-add-employee.php"><i class="fa-solid fa-user-plus"></i> Add Employee</a>
    </div>
    </div>
   

    <div class="table-wrap desktop-directory">
        <table class="data-table directory-table">
            <thead>
            <tr>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>View</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$employees): ?>
                <tr><td colspan="6">No employee records found.</td></tr>
            <?php endif; ?>
            <?php foreach ($employees as $employee): ?>
                <?php $row_id = 'employee-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', $employee['employee_id']); ?>
                <tr>
                    <td><?php echo htmlspecialchars($employee['employee_id']); ?></td>
                    <td><?php echo htmlspecialchars($employee['name']); ?></td>
                    <td><?php echo htmlspecialchars($employee['email']); ?></td>
                    <td><?php echo htmlspecialchars($employee['phone'] ?: 'Not added'); ?></td>
                    <td>
                        <button type="button" class="view-btn js-toggle-detail" data-target="<?php echo htmlspecialchars($row_id); ?>">
                            <i class="fa-solid fa-eye"></i> View
                        </button>
                    </td>
                    <td>
                        <div class="action-stack">
                            <button type="button" class="edit-btn js-toggle-detail" data-target="<?php echo htmlspecialchars($row_id); ?>">
                                <i class="fa-solid fa-pen"></i> Edit
                            </button>
                            <form method="post" class="inline-form" onsubmit="return confirm('Delete this employee account?');">
                                <input type="hidden" name="employee_action" value="delete">
                                <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee['employee_id']); ?>">
                                <button type="submit" class="delete-btn"><i class="fa-solid fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <tr class="detail-row" id="<?php echo htmlspecialchars($row_id); ?>">
                    <td colspan="6" class="detail-cell">
                        <div class="detail-box">
                            <div class="detail-grid">
                                <div class="detail-item"><span>Employee ID</span><strong><?php echo htmlspecialchars($employee['employee_id']); ?></strong></div>
                                <div class="detail-item"><span>Email</span><strong><?php echo htmlspecialchars($employee['email']); ?></strong></div>
                                <div class="detail-item"><span>Phone</span><strong><?php echo htmlspecialchars($employee['phone'] ?: 'Not added'); ?></strong></div>
                                <div class="detail-item"><span>Created At</span><strong><?php echo htmlspecialchars($employee['created_at'] ?: 'Not available'); ?></strong></div>
                                <div class="detail-item"><span>Last Login</span><strong><?php echo htmlspecialchars($employee['last_login'] ?: 'Not available'); ?></strong></div>
                                <div class="detail-item"><span>Updated At</span><strong><?php echo htmlspecialchars($employee['updated_at'] ?: 'Not available'); ?></strong></div>
                            </div>
                            <form method="post" class="detail-form">
                                <h4>Edit Employee</h4>
                                <input type="hidden" name="employee_action" value="edit">
                                <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee['employee_id']); ?>">
                                <div class="form-grid">
                                    <div class="group">
                                        <label for="name-<?php echo htmlspecialchars($row_id); ?>">Full Name</label>
                                        <input type="text" id="name-<?php echo htmlspecialchars($row_id); ?>" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
                                    </div>
                                    <div class="group">
                                        <label for="email-<?php echo htmlspecialchars($row_id); ?>">Email</label>
                                        <input type="email" id="email-<?php echo htmlspecialchars($row_id); ?>" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
                                    </div>
                                    <div class="group">
                                        <label for="phone-<?php echo htmlspecialchars($row_id); ?>">Phone</label>
                                        <input type="text" id="phone-<?php echo htmlspecialchars($row_id); ?>" name="phone" value="<?php echo htmlspecialchars($employee['phone'] ?? ''); ?>" placeholder="Enter 10-digit phone number" inputmode="numeric" pattern="\d{10}" maxlength="10" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10);">
                                    </div>
                                </div>
                                <div class="actions">
                                    <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Employee</button>
                                </div>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="mobile-list">
        <?php if (!$employees): ?>
            <div class="mobile-card">No employee records found.</div>
        <?php endif; ?>
        <?php foreach ($employees as $employee): ?>
            <?php $card_safe_id = preg_replace('/[^a-zA-Z0-9_-]/', '-', $employee['employee_id']); ?>
            <?php $card_id = 'employee-card-' . $card_safe_id; ?>
            <article class="mobile-card">
                <div class="mobile-head">
                    <div>
                        <h4><?php echo htmlspecialchars($employee['name']); ?></h4>
                        <p><?php echo htmlspecialchars($employee['employee_id']); ?></p>
                    </div>
                    <button type="button" class="view-btn js-toggle-detail" data-target="<?php echo htmlspecialchars($card_id); ?>">
                        <i class="fa-solid fa-eye"></i> View
                    </button>
                </div>
                <div class="mobile-meta">
                    <div><span>Email</span><?php echo htmlspecialchars($employee['email']); ?></div>
                    <div><span>Phone</span><?php echo htmlspecialchars($employee['phone'] ?: 'Not added'); ?></div>
                </div>
                <div class="mobile-actions">
                    <button type="button" class="edit-btn js-toggle-detail" data-target="<?php echo htmlspecialchars($card_id); ?>">
                        <i class="fa-solid fa-pen"></i> Edit
                    </button>
                    <form method="post" class="inline-form" onsubmit="return confirm('Delete this employee account?');">
                        <input type="hidden" name="employee_action" value="delete">
                        <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee['employee_id']); ?>">
                        <button type="submit" class="delete-btn"><i class="fa-solid fa-trash"></i> Delete</button>
                    </form>
                </div>
                <div class="mobile-details" id="<?php echo htmlspecialchars($card_id); ?>">
                    <div class="detail-grid">
                        <div class="detail-item"><span>Employee ID</span><strong><?php echo htmlspecialchars($employee['employee_id']); ?></strong></div>
                        <div class="detail-item"><span>Created At</span><strong><?php echo htmlspecialchars($employee['created_at'] ?: 'Not available'); ?></strong></div>
                        <div class="detail-item"><span>Last Login</span><strong><?php echo htmlspecialchars($employee['last_login'] ?: 'Not available'); ?></strong></div>
                        <div class="detail-item"><span>Updated At</span><strong><?php echo htmlspecialchars($employee['updated_at'] ?: 'Not available'); ?></strong></div>
                    </div>
                    <form method="post" class="detail-form">
                        <h4>Edit Employee</h4>
                        <input type="hidden" name="employee_action" value="edit">
                        <input type="hidden" name="employee_id" value="<?php echo htmlspecialchars($employee['employee_id']); ?>">
                        <div class="form-grid">
                            <div class="group">
                                <label for="mobile-name-<?php echo htmlspecialchars($card_safe_id); ?>">Full Name</label>
                                <input type="text" id="mobile-name-<?php echo htmlspecialchars($card_safe_id); ?>" name="name" value="<?php echo htmlspecialchars($employee['name']); ?>" required>
                            </div>
                            <div class="group">
                                <label for="mobile-email-<?php echo htmlspecialchars($card_safe_id); ?>">Email</label>
                                <input type="email" id="mobile-email-<?php echo htmlspecialchars($card_safe_id); ?>" name="email" value="<?php echo htmlspecialchars($employee['email']); ?>" required>
                            </div>
                            <div class="group">
                                <label for="mobile-phone-<?php echo htmlspecialchars($card_safe_id); ?>">Phone</label>
                                <input type="text" id="mobile-phone-<?php echo htmlspecialchars($card_safe_id); ?>" name="phone" value="<?php echo htmlspecialchars($employee['phone'] ?? ''); ?>" placeholder="Enter 10-digit phone number" inputmode="numeric" pattern="\d{10}" maxlength="10" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10);">
                            </div>
                        </div>
                        <div class="actions">
                            <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Employee</button>
                        </div>
                    </form>
                </div>
            </article>
        <?php endforeach; ?>
    </div>
</section>

<script>
    document.querySelectorAll('.js-toggle-detail').forEach(function (button) {
        button.addEventListener('click', function () {
            const targetId = button.dataset.target;
            const target = document.getElementById(targetId);
            if (!target) {
                return;
            }

            const isOpen = target.classList.contains('open');

            document.querySelectorAll('.detail-row.open, .mobile-details.open').forEach(function (row) {
                row.classList.remove('open');
            });

            if (!isOpen) {
                target.classList.add('open');
                target.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        });
    });
</script>
<?php adminLayoutEnd(); ?>
