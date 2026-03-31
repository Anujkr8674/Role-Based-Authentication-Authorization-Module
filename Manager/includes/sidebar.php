<aside class="sidebar" id="sidebar">
    <div class="brand">
        <div class="brand-badge"><i class="fa-solid fa-briefcase"></i></div>
        <div class="brand-text"><span>Welcome Manager</span><strong><?php echo htmlspecialchars($manager['name']); ?></strong></div>
    </div>
    <nav class="nav">
        <a class="nav-link <?php echo $active_page === 'overview' ? 'active' : ''; ?>" href="manager-dashboard.php"><i class="fa-solid fa-chart-line"></i><span>Overview</span></a>
        <a class="nav-link <?php echo $active_page === 'edit-profile' ? 'active' : ''; ?>" href="manager-edit-profile.php"><i class="fa-solid fa-user-pen"></i><span>Edit Profile</span></a>
        <a class="nav-link <?php echo $active_page === 'change-password' ? 'active' : ''; ?>" href="manager-change-password.php"><i class="fa-solid fa-key"></i><span>Change Password</span></a>
        <a class="nav-link <?php echo $active_page === 'Comming soon1' ? 'active' : ''; ?>" href="Comming soon1.php"><i class="fa-solid fa-clock"></i><span>Comming soon1</span></a>
        <a class="nav-link <?php echo $active_page === 'Comming soon2' ? 'active' : ''; ?>" href="Comming soon2.php"><i class="fa-solid fa-screwdriver-wrench"></i><span>Comming soon2</span></a>
        <a id="clr"class="nav-link" href="manager-logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
    </nav>
    <!-- <div class="side-foot"><i class="fa-solid fa-shield-halved"></i><span>Manager access enabled</span></div> -->
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
