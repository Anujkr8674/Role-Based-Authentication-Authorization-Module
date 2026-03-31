<?php
require __DIR__ . '/includes/employee-common.php';
require __DIR__ . '/includes/layout.php';
$employee = getCurrentEmployee($conn, $_SESSION['employee_id']);
if (!$employee) { header('Location: employee-logout.php'); exit; }
$page_title = 'Coming Soon 2';
$page_subtitle = 'Upcoming employee module';
$active_page = 'coming-soon2';
employeeLayoutStart($page_title, $page_subtitle, $employee, $active_page);
?>
<section class="hero">
    <div>
        <h2>Coming Soon 2</h2>
        <p>This employee section is reserved for the next feature rollout.</p>
    </div>
    <div class="tag"><i class="fa-solid fa-screwdriver-wrench"></i> Upcoming</div>
</section>
<section class="panel" style="display:grid;place-items:center;min-height:420px;"><div style="font-size:clamp(2.6rem,7vw,5.8rem);font-weight:800;letter-spacing:.08em;color:#1f6feb;text-transform:uppercase;">Coming Soon</div></section>
<?php employeeLayoutEnd(); ?>
