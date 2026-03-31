<aside class="sidebar" id="sidebar">
    <div class="brand">
        <div class="brand-badge"><i class="fa-solid fa-id-badge"></i></div>
        <div class="brand-text"><span>Employee workspace</span><strong><?php echo htmlspecialchars($employee['name']); ?></strong></div>
    </div>
    <nav class="nav">
        <a class="nav-link <?php echo $active_page === 'overview' ? 'active' : ''; ?>" href="employee-dashboard.php"><i class="fa-solid fa-chart-line"></i><span>Overview</span></a>
        <a class="nav-link <?php echo $active_page === 'edit-profile' ? 'active' : ''; ?>" href="employee-edit-profile.php"><i class="fa-solid fa-user-pen"></i><span>Edit Profile</span></a>
        <a class="nav-link <?php echo $active_page === 'change-password' ? 'active' : ''; ?>" href="employee-change-password.php"><i class="fa-solid fa-key"></i><span>Change Password</span></a>
        <a class="nav-link <?php echo $active_page === 'coming-soon1' ? 'active' : ''; ?>" href="Comming soon1.php"><i class="fa-solid fa-clock"></i><span>Coming Soon 1</span></a>
        <a class="nav-link <?php echo $active_page === 'coming-soon2' ? 'active' : ''; ?>" href="Comming soon2.php"><i class="fa-solid fa-screwdriver-wrench"></i><span>Coming Soon 2</span></a>
        <a id="clr" class="nav-link" href="employee-logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </nav>
    <!-- <div class="side-foot"><i class="fa-solid fa-shield-halved"></i><span>Employee access enabled</span></div> -->
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
