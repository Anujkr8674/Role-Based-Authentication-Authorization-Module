<?php
require_once __DIR__ . '/includes/admin-common.php';
require_once __DIR__ . '/includes/layout.php';

$admin_user = getCurrentAdmin($conn, $_SESSION['admin_id']);
if (!$admin_user) { header('Location: admin-logout.php'); exit; }

$stats = [];
$result = $conn->query("SELECT COUNT(*) AS total_employees FROM employee");
$stats['employees'] = $result ? $result->fetch_assoc() : ['total_employees' => 0];
$result = $conn->query("SELECT COUNT(*) AS total_managers FROM `manager`");
$stats['managers'] = $result ? $result->fetch_assoc() : ['total_managers' => 0];
$result = $conn->query("SELECT COUNT(*) AS total_admins FROM admin");
$stats['admins'] = $result ? $result->fetch_assoc() : ['total_admins' => 0];

$page_title = 'Admin Dashboard';
$page_subtitle = 'Overview of the core account system';
$active_page = 'overview';
adminLayoutStart($page_title, $page_subtitle, $admin_user, $active_page);
?>
<section class="hero">
    <div>
        <h2>Overview</h2>
        <p>This dashboard highlights the core account counts across the platform and gives quick access to management sections.</p>
    </div>
    <div class="tag"><i class="fa-solid fa-shield-halved"></i> <?php echo htmlspecialchars($admin_user['admin_id']); ?></div>
</section>
<div class="grid">
    <section class="panel">
        <div class="panel-title"><div><h3>Account Summary</h3><p>Live counts pulled from the current tables.</p></div></div>
        <div class="stats">
            <article class="card"><i class="fa-solid fa-users"></i><h4>Total Employees</h4><p><?php echo (int)($stats['employees']['total_employees'] ?? 0); ?></p></article>
            <article class="card"><i class="fa-solid fa-user-tie"></i><h4>Total Managers</h4><p><?php echo (int)($stats['managers']['total_managers'] ?? 0); ?></p></article>
            <article class="card"><i class="fa-solid fa-user-shield"></i><h4>Total Admins</h4><p><?php echo (int)($stats['admins']['total_admins'] ?? 0); ?></p></article>
        </div>
    </section>
    <section class="panel">
        <div class="panel-title"><div><h3>Quick Actions</h3><p>Go directly to the main admin management areas.</p></div></div>
        <div class="actions">
            <a class="btn-primary" href="admin-manage-manager.php"><i class="fa-solid fa-user-tie"></i> Manage Manager</a>
            <a class="btn-secondary" href="admin-manage-employee.php"><i class="fa-solid fa-users-gear"></i> Manage Employee</a>
            <a class="btn-secondary" href="admin-change-password.php"><i class="fa-solid fa-key"></i> Change Password</a>
        </div>
    </section>
</div>
<?php adminLayoutEnd(); ?>
