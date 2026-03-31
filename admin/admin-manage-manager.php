<?php
require_once __DIR__ . '/includes/admin-common.php';
require_once __DIR__ . '/includes/layout.php';

$admin_user = getCurrentAdmin($conn, $_SESSION['admin_id']);
if (!$admin_user) {
    header('Location: admin-logout.php');
    exit;
}

$success_message = $_SESSION['admin_manager_success'] ?? '';
$error_message = $_SESSION['admin_manager_error'] ?? '';
unset($_SESSION['admin_manager_success'], $_SESSION['admin_manager_error']);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['manager_action'], $_POST['manager_id'])) {
    $manager_id = trim($_POST['manager_id']);
    $manager_action = $_POST['manager_action'];

    if ($manager_action === 'delete') {
        $delete_stmt = $conn->prepare("DELETE FROM `manager` WHERE manager_id = ?");
        $delete_stmt->bind_param('s', $manager_id);
        if ($delete_stmt->execute()) {
            $_SESSION['admin_manager_success'] = 'Manager deleted successfully.';
        } else {
            $_SESSION['admin_manager_error'] = 'Unable to delete manager.';
        }
        $delete_stmt->close();
    } elseif ($manager_action === 'edit') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');

        if ($name === '' || $email === '') {
            $_SESSION['admin_manager_error'] = 'Name and email are required.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['admin_manager_error'] = 'Please enter a valid email address.';
        } elseif ($phone !== '' && !preg_match('/^\d{10}$/', $phone)) {
            $_SESSION['admin_manager_error'] = 'Phone number must be exactly 10 digits.';
        } else {
            $email_check = $conn->prepare("SELECT manager_id FROM `manager` WHERE email = ? AND manager_id != ? LIMIT 1");
            $email_check->bind_param('ss', $email, $manager_id);
            $email_check->execute();
            $email_exists = $email_check->get_result()->fetch_assoc();
            $email_check->close();

            if ($email_exists) {
                $_SESSION['admin_manager_error'] = 'This email is already assigned to another manager.';
            } else {
                $update_stmt = $conn->prepare("UPDATE `manager` SET name = ?, email = ?, phone = ? WHERE manager_id = ?");
                $update_stmt->bind_param('ssss', $name, $email, $phone, $manager_id);
                if ($update_stmt->execute()) {
                    $_SESSION['admin_manager_success'] = 'Manager details updated successfully.';
                } else {
                    $_SESSION['admin_manager_error'] = 'Unable to update manager details.';
                }
                $update_stmt->close();
            }
        }
    }

    header('Location: admin-manage-manager.php');
    exit;
}

$managers = [];
$result = $conn->query("SELECT id, manager_id, name, email, phone, last_login, created_at, updated_at FROM `manager` ORDER BY id DESC");
if ($result) {
    $managers = $result->fetch_all(MYSQLI_ASSOC);
}

$page_title = 'Manage Manager';
$page_subtitle = 'Review and control manager accounts from one place';
$active_page = 'manage-manager';

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
        <h2>Manage Manager</h2>
        <p>Review manager accounts, inspect essential details, and run quick admin actions from the same screen.</p>
    </div>
    <div class="tag"><i class="fa-solid fa-user-tie"></i> Manager</div>
</section>

<?php if ($success_message !== ''): ?><div class="flash success"><?php echo htmlspecialchars($success_message); ?></div><?php endif; ?>
<?php if ($error_message !== ''): ?><div class="flash error"><?php echo htmlspecialchars($error_message); ?></div><?php endif; ?>

<section class="panel">
    <div class="panel-title">
        <div>
            <h3>Manager Directory</h3>
            <!-- <p>View, edit, or remove manager records from the `manager` table.</p> -->
        </div>
          <div class="header-actions">
        <a class="btn-primary" href="admin-add-manager.php"><i class="fa-solid fa-user-plus"></i> Add Manager</a>
    </div>
    </div>
    <!-- <div class="header-actions">
        <a class="btn-primary" href="admin-add-manager.php"><i class="fa-solid fa-user-plus"></i> Add Manager</a>
    </div> -->

    <div class="table-wrap desktop-directory">
        <table class="data-table directory-table">
            <thead>
            <tr>
                <th>Manager ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>View</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            <?php if (!$managers): ?>
                <tr><td colspan="6">No manager records found.</td></tr>
            <?php endif; ?>
            <?php foreach ($managers as $manager): ?>
                <?php $row_id = 'manager-' . preg_replace('/[^a-zA-Z0-9_-]/', '-', $manager['manager_id']); ?>
                <tr>
                    <td><?php echo htmlspecialchars($manager['manager_id']); ?></td>
                    <td><?php echo htmlspecialchars($manager['name']); ?></td>
                    <td><?php echo htmlspecialchars($manager['email']); ?></td>
                    <td><?php echo htmlspecialchars($manager['phone'] ?: 'Not added'); ?></td>
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
                            <form method="post" class="inline-form" onsubmit="return confirm('Delete this manager account?');">
                                <input type="hidden" name="manager_action" value="delete">
                                <input type="hidden" name="manager_id" value="<?php echo htmlspecialchars($manager['manager_id']); ?>">
                                <button type="submit" class="delete-btn"><i class="fa-solid fa-trash"></i> Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <tr class="detail-row" id="<?php echo htmlspecialchars($row_id); ?>">
                    <td colspan="6" class="detail-cell">
                        <div class="detail-box">
                            <div class="detail-grid">
                                <div class="detail-item"><span>Manager ID</span><strong><?php echo htmlspecialchars($manager['manager_id']); ?></strong></div>
                                <div class="detail-item"><span>Email</span><strong><?php echo htmlspecialchars($manager['email']); ?></strong></div>
                                <div class="detail-item"><span>Phone</span><strong><?php echo htmlspecialchars($manager['phone'] ?: 'Not added'); ?></strong></div>
                                <div class="detail-item"><span>Created At</span><strong><?php echo htmlspecialchars($manager['created_at'] ?: 'Not available'); ?></strong></div>
                                <div class="detail-item"><span>Last Login</span><strong><?php echo htmlspecialchars($manager['last_login'] ?: 'Not available'); ?></strong></div>
                                <div class="detail-item"><span>Updated At</span><strong><?php echo htmlspecialchars($manager['updated_at'] ?: 'Not available'); ?></strong></div>
                            </div>
                            <form method="post" class="detail-form">
                                <h4>Edit Manager</h4>
                                <input type="hidden" name="manager_action" value="edit">
                                <input type="hidden" name="manager_id" value="<?php echo htmlspecialchars($manager['manager_id']); ?>">
                                <div class="form-grid">
                                    <div class="group">
                                        <label for="name-<?php echo htmlspecialchars($row_id); ?>">Full Name</label>
                                        <input type="text" id="name-<?php echo htmlspecialchars($row_id); ?>" name="name" value="<?php echo htmlspecialchars($manager['name']); ?>" required>
                                    </div>
                                    <div class="group">
                                        <label for="email-<?php echo htmlspecialchars($row_id); ?>">Email</label>
                                        <input type="email" id="email-<?php echo htmlspecialchars($row_id); ?>" name="email" value="<?php echo htmlspecialchars($manager['email']); ?>" required>
                                    </div>
                                    <div class="group">
                                        <label for="phone-<?php echo htmlspecialchars($row_id); ?>">Phone</label>
                                        <input type="text" id="phone-<?php echo htmlspecialchars($row_id); ?>" name="phone" value="<?php echo htmlspecialchars($manager['phone'] ?? ''); ?>" placeholder="Enter 10-digit phone number" inputmode="numeric" pattern="\d{10}" maxlength="10" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10);">
                                    </div>
                                </div>
                                <div class="actions">
                                    <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Manager</button>
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
        <?php if (!$managers): ?>
            <div class="mobile-card">No manager records found.</div>
        <?php endif; ?>
        <?php foreach ($managers as $manager): ?>
            <?php $card_safe_id = preg_replace('/[^a-zA-Z0-9_-]/', '-', $manager['manager_id']); ?>
            <?php $card_id = 'manager-card-' . $card_safe_id; ?>
            <article class="mobile-card">
                <div class="mobile-head">
                    <div>
                        <h4><?php echo htmlspecialchars($manager['name']); ?></h4>
                        <p><?php echo htmlspecialchars($manager['manager_id']); ?></p>
                    </div>
                    <button type="button" class="view-btn js-toggle-detail" data-target="<?php echo htmlspecialchars($card_id); ?>">
                        <i class="fa-solid fa-eye"></i> View
                    </button>
                </div>
                <div class="mobile-meta">
                    <div><span>Email</span><?php echo htmlspecialchars($manager['email']); ?></div>
                    <div><span>Phone</span><?php echo htmlspecialchars($manager['phone'] ?: 'Not added'); ?></div>
                </div>
                <div class="mobile-actions">
                    <button type="button" class="edit-btn js-toggle-detail" data-target="<?php echo htmlspecialchars($card_id); ?>">
                        <i class="fa-solid fa-pen"></i> Edit
                    </button>
                    <form method="post" class="inline-form" onsubmit="return confirm('Delete this manager account?');">
                        <input type="hidden" name="manager_action" value="delete">
                        <input type="hidden" name="manager_id" value="<?php echo htmlspecialchars($manager['manager_id']); ?>">
                        <button type="submit" class="delete-btn"><i class="fa-solid fa-trash"></i> Delete</button>
                    </form>
                </div>
                <div class="mobile-details" id="<?php echo htmlspecialchars($card_id); ?>">
                    <div class="detail-grid">
                        <div class="detail-item"><span>Manager ID</span><strong><?php echo htmlspecialchars($manager['manager_id']); ?></strong></div>
                        <div class="detail-item"><span>Created At</span><strong><?php echo htmlspecialchars($manager['created_at'] ?: 'Not available'); ?></strong></div>
                        <div class="detail-item"><span>Last Login</span><strong><?php echo htmlspecialchars($manager['last_login'] ?: 'Not available'); ?></strong></div>
                        <div class="detail-item"><span>Updated At</span><strong><?php echo htmlspecialchars($manager['updated_at'] ?: 'Not available'); ?></strong></div>
                    </div>
                    <form method="post" class="detail-form">
                        <h4>Edit Manager</h4>
                        <input type="hidden" name="manager_action" value="edit">
                        <input type="hidden" name="manager_id" value="<?php echo htmlspecialchars($manager['manager_id']); ?>">
                        <div class="form-grid">
                            <div class="group">
                                <label for="mobile-name-<?php echo htmlspecialchars($card_safe_id); ?>">Full Name</label>
                                <input type="text" id="mobile-name-<?php echo htmlspecialchars($card_safe_id); ?>" name="name" value="<?php echo htmlspecialchars($manager['name']); ?>" required>
                            </div>
                            <div class="group">
                                <label for="mobile-email-<?php echo htmlspecialchars($card_safe_id); ?>">Email</label>
                                <input type="email" id="mobile-email-<?php echo htmlspecialchars($card_safe_id); ?>" name="email" value="<?php echo htmlspecialchars($manager['email']); ?>" required>
                            </div>
                            <div class="group">
                                <label for="mobile-phone-<?php echo htmlspecialchars($card_safe_id); ?>">Phone</label>
                                <input type="text" id="mobile-phone-<?php echo htmlspecialchars($card_safe_id); ?>" name="phone" value="<?php echo htmlspecialchars($manager['phone'] ?? ''); ?>" placeholder="Enter 10-digit phone number" inputmode="numeric" pattern="\d{10}" maxlength="10" oninput="this.value=this.value.replace(/\D/g,'').slice(0,10);">
                            </div>
                        </div>
                        <div class="actions">
                            <button type="submit" class="btn-primary"><i class="fa-solid fa-floppy-disk"></i> Save Manager</button>
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
