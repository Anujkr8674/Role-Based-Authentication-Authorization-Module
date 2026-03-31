<?php
require __DIR__ . '/includes/manager-common.php';
require __DIR__ . '/includes/layout.php';

$manager = getCurrentManager($conn, $_SESSION['manager_id']);
if (!$manager) {
    header('Location: manager-logout.php');
    exit;
}

$page_title = 'Comming soon1';
$page_subtitle = 'Upcoming manager module';
$active_page = 'Comming soon1';

managerLayoutStart($page_title, $page_subtitle, $manager, $active_page);
?>
<section class="hero">
    <div>
        <h2>Comming soon1</h2>
        <p>This manager section is reserved for the next feature rollout.</p>
    </div>
    <div class="tag"><i class="fa-solid fa-clock"></i> Upcoming</div>
</section>

<section class="panel" style="display:grid;place-items:center;min-height:420px;">
    <div style="text-align:center;">
        <div style="font-size:clamp(2.6rem,7vw,5.8rem);font-weight:800;letter-spacing:.08em;color:#1f6feb;text-transform:uppercase;">
            Coming Soon
        </div>
    </div>
</section>
<?php
managerLayoutEnd();
?>
