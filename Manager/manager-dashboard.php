<?php
require __DIR__ . '/includes/manager-common.php';
require __DIR__ . '/includes/layout.php';

$manager = getCurrentManager($conn, $_SESSION['manager_id']);
if (!$manager) {
    header('Location: manager-logout.php');
    exit;
}

$display_last_login = formatManagerDate($manager['last_login'] ?? null);
$display_created_at = formatManagerDate($manager['created_at'] ?? null);
$page_title = 'Manager Workspace';
$page_subtitle = 'Overview, profile update, password change and logout';
$active_page = 'overview';

managerLayoutStart($page_title, $page_subtitle, $manager, $active_page);
?>
<section class="hero">
    <div>
        <h2>Overview</h2>
        <p>This page displays the logged-in manager profile data directly from the database. Password and auth token are intentionally hidden.</p>
    </div>
    <div class="tag"><i class="fa-solid fa-id-badge"></i> <?php echo htmlspecialchars($manager['manager_id']); ?></div>
</section>

<div class="grid">
    <section class="panel">
        <div class="panel-title">
            <div>
                <h3>Account Snapshot</h3>
                <p>The primary manager database fields are shown here in summary cards.</p>
            </div>
        </div>
        <div class="stats">
            <article class="card"><i class="fa-solid fa-id-card"></i><h4>Manager ID</h4><p><?php echo htmlspecialchars($manager['manager_id']); ?></p></article>
            <article class="card"><i class="fa-solid fa-user"></i><h4>Name</h4><p><?php echo htmlspecialchars($manager['name']); ?></p></article>
            <article class="card"><i class="fa-solid fa-envelope"></i><h4>Email</h4><p><?php echo htmlspecialchars($manager['email']); ?></p></article>
            <article class="card"><i class="fa-solid fa-phone"></i><h4>Phone</h4><p><?php echo htmlspecialchars($manager['phone'] ?: 'Not added'); ?></p></article>
            <article class="card"><i class="fa-solid fa-clock-rotate-left"></i><h4>Last Login</h4><p><?php echo htmlspecialchars($display_last_login); ?></p></article>
            <article class="card"><i class="fa-solid fa-calendar-check"></i><h4>Created At</h4><p><?php echo htmlspecialchars($display_created_at); ?></p></article>
        </div>
    </section>

    <section class="panel">
        <div class="panel-title">
            <div>
                <h3>Profile Details</h3>
                <p>A compact profile card with detailed information rows.</p>
            </div>
            <a class="btn-secondary" href="manager-edit-profile.php"><i class="fa-solid fa-pen-to-square"></i> Edit Profile</a>
        </div>
        <div class="summary">
            <div class="profile-card">
                <img src="./includes/user.webp" alt="Manager profile">
                <h4><?php echo htmlspecialchars($manager['name']); ?></h4>
                <p>Manager account</p>
            </div>
            <div class="rows">
                <div class="row"><span>Manager ID</span><strong><?php echo htmlspecialchars($manager['manager_id']); ?></strong></div>
                <div class="row"><span>Email</span><strong><?php echo htmlspecialchars($manager['email']); ?></strong></div>
                <div class="row"><span>Phone</span><strong><?php echo htmlspecialchars($manager['phone'] ?: 'Not added'); ?></strong></div>
                <div class="row"><span>Last Login</span><strong><?php echo htmlspecialchars($display_last_login); ?></strong></div>
            </div>
        </div>
    </section>
</div>
<?php
managerLayoutEnd();
?>
