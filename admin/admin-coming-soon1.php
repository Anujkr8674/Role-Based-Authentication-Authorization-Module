<?php
require_once __DIR__ . '/includes/admin-common.php';
require_once __DIR__ . '/includes/layout.php';
$admin_user = getCurrentAdmin($conn, $_SESSION['admin_id']);
if (!$admin_user) { header('Location: admin-logout.php'); exit; }
$page_title = 'Coming Soon 1';
$page_subtitle = 'Upcoming admin module';
$active_page = 'coming-soon1';
adminLayoutStart($page_title, $page_subtitle, $admin_user, $active_page);
?>
<section class="hero"><div><h2>Coming Soon 1</h2><p>This admin section is reserved for the next feature rollout.</p></div><div class="tag"><i class="fa-solid fa-clock"></i> Upcoming</div></section>
<section class="panel" style="display:grid;place-items:center;min-height:420px;"><div style="font-size:clamp(2.7rem,7vw,6rem);font-weight:800;letter-spacing:.08em;color:#506d63;text-transform:uppercase;">Coming Soon</div></section>
<?php adminLayoutEnd(); ?>
