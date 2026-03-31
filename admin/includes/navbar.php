<header class="topbar">
    <div class="top-left">
        <button class="icon-btn" id="menuBtn" type="button"><i class="fa-solid fa-bars"></i></button>
        <button class="icon-btn" id="collapseBtn" type="button"><i class="fa-solid fa-angles-left" id="collapseIcon"></i></button>
        <div class="top-title">
            <h1><?php echo htmlspecialchars($page_title); ?></h1>
            <p><?php echo htmlspecialchars($page_subtitle); ?></p>
        </div>
    </div>
    <div class="profile-menu">
        <div class="profile-btn" tabindex="0">
            <img src="./includes/user.webp" alt="Admin profile">
            <div class="profile-meta">
                <strong><?php echo htmlspecialchars($admin_user['name']); ?></strong>
                <span><?php echo htmlspecialchars($admin_user['email']); ?></span>
            </div>
            <i class="fa-solid fa-chevron-down"></i>
        </div>
        <div class="dropdown">
            <a href="admin-dashboard.php"><i class="fa-solid fa-shield"></i><span>Dashboard</span></a>
            <a class="logout" href="admin-logout.php"><i class="fa-solid fa-right-from-bracket"></i><span>Logout</span></a>
        </div>
    </div>
</header>
