<aside class="sidebar" id="sidebar">
    <div class="brand">
        <div class="brand-badge"><i class="fa-solid fa-user-shield"></i></div>
        <div class="brand-text"><strong><?php echo htmlspecialchars($admin_user['name']); ?></strong><span>Admin control room</span></div>
    </div>
    <nav class="nav">
        <a class="nav-link <?php echo $active_page === 'overview' ? 'active' : ''; ?>" href="admin-dashboard.php"><i class="fa-solid fa-chart-pie"></i><span>Overview</span></a>
        <a class="nav-link <?php echo $active_page === 'manage-manager' ? 'active' : ''; ?>" href="admin-manage-manager.php"><i class="fa-solid fa-user-tie"></i><span>Manage Manager</span></a>
        <a class="nav-link <?php echo $active_page === 'manage-employee' ? 'active' : ''; ?>" href="admin-manage-employee.php"><i class="fa-solid fa-users-gear"></i><span>Manage Employee</span></a>
        <a class="nav-link <?php echo $active_page === 'change-password' ? 'active' : ''; ?>" href="admin-change-password.php"><i class="fa-solid fa-key"></i><span>Change Password</span></a>
        <a class="nav-link <?php echo $active_page === 'coming-soon1' ? 'active' : ''; ?>" href="admin-coming-soon1.php"><i class="fa-solid fa-clock"></i><span>Coming Soon 1</span></a>
        <a class="nav-link <?php echo $active_page === 'coming-soon2' ? 'active' : ''; ?>" href="admin-coming-soon2.php"><i class="fa-solid fa-compass-drafting"></i><span>Coming Soon 2</span></a>
        <a id="clr" class="nav-link" href="admin-logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </nav>
    <!-- <div class="side-foot"><i class="fa-solid fa-sparkles"></i><span>Central admin access enabled</span></div> -->
</aside>
<style>
    #clr    {
        color: red;
    }
    #clr:hover    {
        color: #fff;
        background-color:red;
    }
</style>
