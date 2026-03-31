<?php
function managerLayoutStart(string $page_title, string $page_subtitle, array $manager, string $active_page): void
{
    ?>
    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo htmlspecialchars($page_title); ?></title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
        <style>
            :root {
                --bg: #f4f7fb;
                --panel: #fff;
                --line: #d9e4f2;
                --text: #203048;
                --muted: #6f7f97;
                --brand: #1f6feb;
                --sidebar: 260px;
                --mini: 88px
            }

            * {
                box-sizing: border-box
            }

            body {
                margin: 0;
                font-family: 'Segoe UI', Arial, sans-serif;
                background: linear-gradient(180deg, #eef4ff, #f8fbff);
                color: var(--text)
            }

            .shell {
                display: flex;
                min-height: 100vh
            }

            .sidebar {
                position: fixed;
                inset: 0 auto 0 0;
                width: var(--sidebar);
                background: linear-gradient(180deg, #183153, #0e2138);
                color: #fff;
                padding: 20px 14px;
                transition: .25s;
                z-index: 1001
            }

            .sidebar.collapsed {
                width: var(--mini)
            }

            .brand,
            .nav-link,
            .side-foot {
                display: flex;
                align-items: center;
                gap: 14px
            }

            .brand {
                padding: 10px 12px 18px;
                border-bottom: 1px solid rgba(255, 255, 255, .12);
                margin-bottom: 16px
            }

            .brand-badge {
                width: 44px;
                height: 44px;
                border-radius: 14px;
                background: linear-gradient(135deg, #4f9cff, #18b6a4);
                display: grid;
                place-items: center;
                flex-shrink: 0
            }

            .brand-text strong,
            .brand-text span,
            .nav-link span,
            .side-foot span {
                white-space: nowrap
            }

            .brand-text span {
                display: block;
                color: rgba(255, 255, 255, .68);
                font-size: .84rem;
                margin-top: 3px
            }

            .sidebar.collapsed .brand-text,
            .sidebar.collapsed .nav-link span,
            .sidebar.collapsed .side-foot span {
                opacity: 0;
                width: 0;
                overflow: hidden
            }

            .nav {
                display: flex;
                flex-direction: column;
                gap: 8px
            }

            .nav-link {
                padding: 14px;
                border-radius: 16px;
                color: rgba(255, 255, 255, .9) !important;
                text-decoration: none
            }

            .nav-link:hover,
            .nav-link.active {
                background: rgba(255, 255, 255, .12)
            }

            .sidebar.collapsed .nav-link {
                justify-content: center;
                padding: 14px 0
            }

            .side-foot {
                margin-top: auto;
                background: rgba(255, 255, 255, .08);
                padding: 14px;
                border-radius: 16px
            }

            .main {
                flex: 1;
                margin-left: var(--sidebar);
                transition: .25s
            }

            .main.expanded {
                margin-left: var(--mini)
            }

            .topbar {
                position: sticky;
                top: 0;
                background: rgba(244, 247, 251, .92);
                backdrop-filter: blur(10px);
                border-bottom: 1px solid var(--line);
                padding: 16px 24px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                z-index: 999
            }

            .top-left,
            .profile-btn {
                display: flex;
                align-items: center;
                gap: 12px
            }

            .icon-btn {
                width: 44px;
                height: 44px;
                border-radius: 14px;
                border: 1px solid var(--line);
                background: #fff;
                display: grid;
                place-items: center;
                cursor: pointer
            }

            #menuBtn {
                display: none
            }

            .top-title h1 {
                margin: 0;
                font-size: 1.08rem
            }

            .top-title p {
                margin: 4px 0 0;
                color: var(--muted);
                font-size: .9rem
            }

            .profile-menu {
                position: relative
            }

            .profile-btn {
                background: #fff;
                border: 1px solid var(--line);
                padding: 8px 12px;
                border-radius: 18px
            }

            .profile-btn img {
                width: 42px;
                height: 42px;
                border-radius: 14px;
                object-fit: cover
            }

            .profile-meta strong {
                display: block;
                font-size: .94rem
            }

            .profile-meta span {
                display: block;
                font-size: .82rem;
                color: var(--muted);
                margin-top: 2px
            }

            .dropdown {
                position: absolute;
                top: calc(100% + 10px);
                right: 0;
                background: #fff;
                border: 1px solid var(--line);
                border-radius: 18px;
                min-width: 210px;
                padding: 10px;
                box-shadow: 0 20px 40px rgba(26, 54, 93, .12);
                opacity: 0;
                pointer-events: none;
                transform: translateY(8px);
                transition: .2s
            }

            .profile-menu:hover .dropdown,
            .profile-menu:focus-within .dropdown {
                opacity: 1;
                pointer-events: auto;
                transform: translateY(0)
            }

            .dropdown a {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 12px 14px;
                border-radius: 14px;
                text-decoration: none;
                color: var(--text)
            }

            .dropdown a:hover {
                background: #f3f7ff
            }

            .dropdown .logout {
                color: #d64545
            }

            .content {
                padding: 24px
            }

            .hero,
            .panel {
                background: #fff;
                border: 1px solid var(--line);
                border-radius: 24px;
                box-shadow: 0 12px 28px rgba(26, 54, 93, .08)
            }

            .hero {
                padding: 24px;
                background: linear-gradient(135deg, #193c74, #1f6feb 60%, #17b0a1);
                color: #fff;
                display: flex;
                justify-content: space-between;
                gap: 18px;
                align-items: flex-start;
                margin-bottom: 20px
            }

            .hero h2 {
                margin: 0 0 10px;
                font-size: 1.55rem
            }

            .hero p {
                margin: 0;
                max-width: 700px;
                line-height: 1.6;
                color: rgba(255, 255, 255, .86)
            }

            .tag {
                padding: 12px 16px;
                background: rgba(255, 255, 255, .14);
                border-radius: 16px;
                font-weight: 700
            }

            .flash {
                padding: 14px 16px;
                border-radius: 16px;
                margin-bottom: 16px
            }

            .flash.success {
                background: #38eb80;
                color: #000;
                border: 1px solid #000
            }

            .flash.error {
                background: #ef8e8e;
                color: #000;
                border: 1px solid #000;
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(12, minmax(0, 1fr));
                gap: 20px
            }

            .panel {
                grid-column: span 12;
                padding: 22px
            }

            .panel-title {
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 12px;
                margin-bottom: 18px
            }

            .panel-title h3 {
                margin: 0;
                font-size: 1.06rem
            }

            .panel-title p {
                margin: 6px 0 0;
                color: var(--muted);
                font-size: .9rem
            }

            .stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 16px
            }

            .card {
                padding: 18px;
                border-radius: 18px;
                background: #f8fbff;
                border: 1px solid #dfe8f5
            }

            .card i {
                width: 46px;
                height: 46px;
                border-radius: 15px;
                background: #e8f0ff;
                color: var(--brand);
                display: grid;
                place-items: center;
                margin-bottom: 12px
            }

            .card h4 {
                margin: 0 0 6px;
                font-size: .94rem;
                color: var(--muted)
            }

            .card p {
                margin: 0;
                font-size: 1rem;
                font-weight: 700;
                word-break: break-word
            }

            .summary {
                display: grid;
                grid-template-columns: 260px 1fr;
                gap: 20px
            }

            .profile-card {
                padding: 22px;
                border-radius: 22px;
                background: linear-gradient(180deg, #193c74, #132843);
                color: #fff;
                text-align: center
            }

            .profile-card img {
                width: 104px;
                height: 104px;
                border-radius: 26px;
                object-fit: cover;
                border: 4px solid rgba(255, 255, 255, .18);
                margin-bottom: 14px
            }

            .profile-card h4 {
                margin: 0 0 6px
            }

            .profile-card p {
                margin: 0;
                color: rgba(255, 255, 255, .72)
            }

            .rows {
                display: grid;
                gap: 12px
            }

            .row {
                display: flex;
                justify-content: space-between;
                gap: 18px;
                padding: 14px 16px;
                border-radius: 16px;
                background: #f7faff;
                border: 1px solid #e0e9f5
            }

            .row span {
                color: var(--muted)
            }

            .form-grid {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
                gap: 18px
            }

            .full {
                grid-column: 1/-1
            }

            .group {
                display: flex;
                flex-direction: column;
                gap: 8px
            }

            .group label {
                font-weight: 700
            }

            .group input {
                padding: 14px 15px;
                border: 1px solid var(--line);
                border-radius: 16px;
                background: #fbfdff;
                outline: none
            }

            .group input.readonly-input {
                background: linear-gradient(180deg, #eef4ff, #e6eefb);
                border-color: #b9ccee;
                color: #284167;
                font-weight: 700;
                box-shadow: inset 0 0 0 1px rgba(31, 111, 235, .08)
            }

            .group small {
                color: var(--muted)
            }

            .actions {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                margin-top: 12px
            }

            .btn-primary,
            .btn-secondary {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                padding: 13px 18px;
                border-radius: 16px;
                text-decoration: none;
                font-weight: 700;
                border: none;
                cursor: pointer
            }

            .btn-primary {
                background: linear-gradient(135deg, #1f6feb, #174ea6);
                color: #fff
            }

            .btn-secondary {
                background: #edf4ff;
                color: #174ea6
            }

            .note {
                margin-top: 12px;
                padding: 14px 16px;
                border-radius: 16px;
                background: #f4f8ff;
                border: 1px solid #deebff;
                color: var(--muted);
                line-height: 1.6
            }

            .overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 33, 57, .36);
                opacity: 0;
                pointer-events: none;
                transition: .2s;
                z-index: 1000
            }

            .overlay.show {
                opacity: 1;
                pointer-events: auto
            }

            @media (max-width:1000px) {
                .summary {
                    grid-template-columns: 1fr
                }

                .form-grid {
                    grid-template-columns: 1fr
                }
            }

            @media (max-width:860px) {
                #menuBtn {
                    display: grid
                }

                .sidebar {
                    transform: translateX(-100%);
                    width: min(290px, 86vw)
                }

                .sidebar.mobile-open {
                    transform: translateX(0)
                }

                .sidebar.collapsed {
                    width: min(290px, 86vw)
                }

                .sidebar.collapsed .brand-text,
                .sidebar.collapsed .nav-link span,
                .sidebar.collapsed .side-foot span {
                    opacity: 1;
                    width: auto
                }

                .sidebar.collapsed .nav-link {
                    justify-content: flex-start;
                    padding: 14px
                }

                .main,
                .main.expanded {
                    margin-left: 0
                }

                .topbar {
                    padding: 14px 16px
                }

                .content {
                    padding: 16px
                }

                .hero {
                    flex-direction: column
                }

                .profile-meta,
                .top-title p {
                    display: none
                }
            }

            @media (max-width:640px) {
                .row {
                    flex-direction: column
                }

                .row strong {
                    text-align: left
                }
            }
        </style>
    </head>

    <body>
        <div class="overlay" id="overlay"></div>
        <div class="shell">
            <?php include __DIR__ . '/sidebar.php'; ?>
            <div class="main" id="main">
                <?php include __DIR__ . '/navbar.php'; ?>
                <main class="content">
                    <?php
}

function managerLayoutEnd(): void
{
    ?>
                </main>
            </div>
        </div>
        <script>
            const sidebar = document.getElementById('sidebar');
            const main = document.getElementById('main');
            const collapseBtn = document.getElementById('collapseBtn');
            const collapseIcon = document.getElementById('collapseIcon');
            const menuBtn = document.getElementById('menuBtn');
            const overlay = document.getElementById('overlay');
            function syncLayout() {
                const mobile = window.innerWidth <= 860;
                collapseBtn.style.display = mobile ? 'none' : 'grid';
                if (mobile) {
                    sidebar.classList.remove('collapsed');
                    main.classList.remove('expanded');
                    collapseIcon.className = 'fa-solid fa-angles-left';
                } else {
                    sidebar.classList.remove('mobile-open');
                    overlay.classList.remove('show');
                }
            }
            collapseBtn.addEventListener('click', function () {
                const collapsed = sidebar.classList.toggle('collapsed');
                main.classList.toggle('expanded', collapsed);
                collapseIcon.className = collapsed ? 'fa-solid fa-angles-right' : 'fa-solid fa-angles-left';
            });
            menuBtn.addEventListener('click', function () {
                if (window.innerWidth <= 860) {
                    sidebar.classList.add('mobile-open');
                    overlay.classList.add('show');
                }
            });
            overlay.addEventListener('click', function () {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('show');
            });
            window.addEventListener('resize', syncLayout);
            syncLayout();
        </script>
    </body>

    </html>
    <?php
}
?>