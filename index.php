<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Role-Based Authentication & Authorization Module</title>
    <style>
        :root {
            --bg: #f4efe7;
            --paper: rgba(255, 255, 255, 0.82);
            --paper-strong: rgba(255, 252, 247, 0.96);
            --ink: #1d2a26;
            --muted: #66756e;
            --line: rgba(58, 78, 71, 0.12);
            --accent: #2e6b5b;
            --accent-deep: #1d4c41;
            --warm: #d2863c;
            --admin: #9c5a2c;
            --manager: #2f7a66;
            --employee: #366c9a;
            --soft-green: #dbe9df;
            --soft-sand: #efe2cf;
            --soft-blue: #dce8f2;
            --shadow: 0 28px 80px rgba(30, 42, 38, 0.14);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: "Segoe UI", Arial, sans-serif;
            color: var(--ink);
            /* background:
                radial-gradient(circle at top left, rgba(210, 134, 60, 0.18), transparent 24%),
                radial-gradient(circle at top right, rgba(47, 122, 102, 0.16), transparent 26%),
                linear-gradient(135deg, #f7f2ea 0%, #efe5d8 48%, #e6efe9 100%); */
            background: url('./includes/webbg.jpg') no-repeat center center fixed;
            background-size: cover;
            position: relative;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.22) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.22) 1px, transparent 1px);
            background-size: 32px 32px;
            opacity: 0.35;
            pointer-events: none;
        }

        .page-shell {
            max-width: 1220px;
            margin: 0 auto;
            padding: 40px 20px 56px;
            position: relative;
            z-index: 1;
        }

        .hero {
            background:
                linear-gradient(135deg, rgba(255, 252, 246, 0.96), rgba(246, 239, 228, 0.94)),
                linear-gradient(180deg, rgba(255, 255, 255, 0.4), transparent);
            border: 1px solid rgba(58, 78, 71, 0.1);
            border-radius: 36px;
            padding: 40px;
            box-shadow: var(--shadow);
            overflow: hidden;
            position: relative;
        }

        .hero::after {
            content: "";
            position: absolute;
            right: -40px;
            top: -30px;
            width: 220px;
            height: 220px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(210, 134, 60, 0.22), transparent 68%);
        }

        .hero::before {
            content: "";
            position: absolute;
            left: -40px;
            bottom: -60px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(47, 122, 102, 0.16), transparent 70%);
        }

        .eyebrow {
            display: inline-block;
            padding: 9px 16px;
            border-radius: 999px;
            background: linear-gradient(135deg, rgba(47, 122, 102, 0.15), rgba(210, 134, 60, 0.14));
            color: var(--accent-deep);
            font-size: 0.8rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        h1 {
            margin: 16px 0 14px;
            font-size: clamp(2rem, 4vw, 3.4rem);
            line-height: 1.08;
            max-width: 760px;
        }

        .hero p {
            max-width: 760px;
            font-size: 1.05rem;
            line-height: 1.8;
            color: var(--muted);
            margin: 0 0 24px;
        }

        .hero-points {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
        }

        .hero-points span {
            padding: 11px 15px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid rgba(58, 78, 71, 0.08);
            font-size: 0.93rem;
            font-weight: 600;
            box-shadow: 0 10px 24px rgba(30, 42, 38, 0.05);
        }

        .section-title {
            margin: 38px 0 18px;
            font-size: 1.6rem;
            padding-top: 20px;
            text-align: center;
            padding-bottom: 10px;
            text-decoration: underline;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .card {
            background: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(252, 248, 241, 0.82));
            border: 1px solid var(--line);
            border-radius: 28px;
            padding: 26px;
            box-shadow: var(--shadow);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .card.admin {
            border-top: 6px solid var(--admin);
        }

          .card.admin:hover{
            background-color:#000;
            
          }

        .card.manager {
            border-top: 6px solid var(--manager);
        }
          .card.manager:hover{
            background-color: #000;

          }

        .card.employee {
            border-top: 6px solid var(--employee);
        }

         .card.employee:hover   {
            background-color: #000;
         }

        .card::after {
            content: "";
            position: absolute;
            right: -35px;
            top: -35px;
            width: 120px;
            height: 120px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.28);
        }

        .role-tag {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 999px;
            font-size: 0.82rem;
            font-weight: 700;
            letter-spacing: 0.03em;
            text-transform: uppercase;
            margin-bottom: 14px;
        }

        .admin .role-tag {
            background: rgba(156, 90, 44, 0.14);
            color: var(--admin);
        }

        .manager .role-tag {
            background: rgba(47, 122, 102, 0.14);
            color: var(--manager);
        }

        .employee .role-tag {
            background: rgba(54, 108, 154, 0.14);
            color: var(--employee);
        }

        .card h2 {
            margin: 0 0 10px;
            font-size: 1.45rem;
            position: relative;
            z-index: 1;
        }

        .card p {
            margin: 0 0 18px;
            color: var(--muted);
            line-height: 1.65;
            position: relative;
            z-index: 1;
        }

        .link-group {
            display: grid;
            gap: 12px;
            margin-bottom: 18px;
            position: relative;
            z-index: 1;
        }

        .shortcut {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            text-decoration: none;
            color: var(--ink);
            background: var(--paper-strong);
            border: 1px solid rgba(58, 78, 71, 0.08);
            border-radius: 20px;
            padding: 15px 16px;
            transition: transform 0.18s ease, border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .shortcut:hover {
            transform: translateY(-3px);
            border-color: rgba(46, 107, 91, 0.24);
            box-shadow: 0 14px 28px rgba(30, 42, 38, 0.11);
        }

        .shortcut strong {
            display: block;
            font-size: 1rem;
            margin-bottom: 4px;
        }

        .shortcut span {
            color: var(--muted);
            font-size: 0.9rem;
            word-break: break-all;
        }

        .arrow {
            font-size: 1.2rem;
            color: var(--accent);
            flex-shrink: 0;
        }

        .card-note {
            margin: 0;
            padding: 15px 16px;
            border-radius: 20px;
            background: rgba(219, 233, 223, 0.7);
            color: var(--muted);
            line-height: 1.6;
            font-size: 0.94rem;
            position: relative;
            z-index: 1;
        }

        .notes {
            margin-top: 22px;
            background:
                linear-gradient(180deg, rgba(255, 252, 247, 0.97), rgba(246, 240, 230, 0.95));
            border: 1px solid var(--line);
            border-radius: 32px;
            padding: 32px;
            box-shadow: var(--shadow);
            position: relative;
            overflow: hidden;
        }

        .notes::after {
            content: "";
            position: absolute;
            right: -50px;
            bottom: -50px;
            width: 180px;
            height: 180px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(54, 108, 154, 0.12), transparent 70%);
        }

        .notes-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
            margin-top: 18px;
        }

        .note-card {
            background: rgba(255, 255, 255, 0.72);
            border: 1px solid rgba(58, 78, 71, 0.08);
            border-radius: 24px;
            padding: 20px;
            box-shadow: 0 12px 28px rgba(30, 42, 38, 0.05);
            position: relative;
            z-index: 1;
        }


        .note-card h3 {
            margin: 0 0 10px;
            font-size: 1.08rem;
        }

        .note-card p {
            margin: 0;
            /* color: var(--muted); */
            line-height: 1.7;
        }

        .note-card:hover {
            background: #000;
            color: #fff;
        }

        .demo-note {
            margin-top: 20px;
            padding: 20px 22px;
            border-left: 4px solid var(--warm);
            border-radius: 20px;
            background: linear-gradient(135deg, rgba(239, 226, 207, 0.85), rgba(255, 247, 237, 0.9));
            line-height: 1.7;
            color: #654d32;
            position: relative;
            z-index: 1;
        }

        .role {
            background-color: #fff;
            margin-top: 20px;

            background-color: #fff;

            padding-bottom: 40px;
            border-radius: 32px;
        }

        @media (max-width: 980px) {
            .cards {
                grid-template-columns: 1fr;
            }

            .notes-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .page-shell {
                padding: 20px 14px 36px;
            }

            .role {
                padding: 0 14px 24px;
                border-radius: 24px;
            }

            .section-title {
                font-size: 1.35rem;
                margin: 24px 0 14px;
            }

            .hero,
            .notes,
            .card {
                padding: 22px 18px;
                border-radius: 24px;
            }

            .cards,
            .notes-grid {
                gap: 14px;
            }

            .shortcut {
                align-items: flex-start;
            }
        }
    </style>
</head>

<body>
    <div class="page-shell">
        <section class="hero">
            <span class="eyebrow">Project Demo</span>
            <h1>Role-Based Authentication &amp; Authorization Module</h1>
            <p>
                This demo presents a multi-role authentication system designed for Admin, Manager, and Employee users.
                It combines secure sign-in, OTP-based registration and password recovery, role-specific access control,
                and persistent session handling so the project can be explained clearly to clients, reviewers, and
                interviewers.
            </p>
            <div class="hero-points">
                <span>Role-based access control</span>
                <span>OTP verification flow</span>
                <span>Session + cookie authentication</span>
                <span>Password reset and account recovery</span>
            </div>
        </section>

        <div class="role">
            <h2 class="section-title">Role Access Shortcuts</h2>
            <section class="cards">
                <article class="card admin">
                    <span class="role-tag">Admin</span>
                    <h2>Administrator Access</h2>
                    <p>
                        The Admin module is intended for privileged system control. It provides secure sign-in and
                        access to
                        protected admin operations such as managing role-based user records.
                    </p>
                    <div class="link-group">
                        <a class="shortcut" href="admin/admin-signin.php">
                            <div>
                                <strong>Admin Login</strong>
                                <span>/admin/admin-signin.php</span>
                            </div>
                            <span class="arrow">&rarr;</span>
                        </a>
                        <a class="shortcut" href="admin/admin-dashboard.php">
                            <div>
                                <strong>Admin Dashboard</strong>
                                <span>/admin/admin-dashboard.php</span>
                            </div>
                            <span class="arrow">&rarr;</span>
                        </a>
                    </div>

                </article>

                <article class="card manager">
                    <span class="role-tag">Manager</span>
                    <h2>Manager Portal</h2>
                    <p>
                        The Manager module supports self-registration with OTP verification, secure sign-in, password
                        recovery, and role-protected dashboard access.
                    </p>
                    <div class="link-group">
                        <a class="shortcut" href="Manager/manager-signin.php">
                            <div>
                                <strong>Manager Login</strong>
                                <span>/Manager/manager-signin.php</span>
                            </div>
                            <span class="arrow">&rarr;</span>
                        </a>
                        <a class="shortcut" href="Manager/manager-register.php">
                            <div>
                                <strong>Manager Register</strong>
                                <span>/Manager/manager-register.php</span>
                            </div>
                            <span class="arrow">&rarr;</span>
                        </a>
                        <a class="shortcut" href="Manager/manager-forgot-password.php">
                            <div>
                                <strong>Manager Forgot Password</strong>
                                <span>/Manager/manager-forgot-password.php</span>
                            </div>
                            <span class="arrow">&rarr;</span>
                        </a>
                    </div>

                </article>

                <article class="card employee">
                    <span class="role-tag">Employee</span>
                    <h2>Employee Workspace</h2>
                    <p>
                        The Employee module follows the same secure onboarding and authentication flow with OTP-based
                        registration, login, password reset, and role-specific dashboard protection.
                    </p>
                    <div class="link-group">
                        <a class="shortcut" href="employee-signin.php">
                            <div>
                                <strong>Employee Login</strong>
                                <span>/employee-signin.php</span>
                            </div>
                            <span class="arrow">&rarr;</span>
                        </a>
                        <a class="shortcut" href="employee-register.php">
                            <div>
                                <strong>Employee Register</strong>
                                <span>/employee-register.php</span>
                            </div>
                            <span class="arrow">&rarr;</span>
                        </a>
                        <a class="shortcut" href="employee-forgot-password.php">
                            <div>
                                <strong>Employee Forgot Password</strong>
                                <span>/employee-forgot-password.php</span>
                            </div>
                            <span class="arrow">&rarr;</span>
                        </a>
                    </div>

                </article>
            </section>
        </div>

        <section class="notes">
            <h2 class="section-title" style="margin-top:0;">Project Notes</h2>
            <p style="margin:0; color:var(--muted); line-height:1.75;">
                This module demonstrates a practical implementation of role-based authentication and authorization in
                PHP.

            </p>

            <div class="notes-grid">
                <article class="note-card">
                    <h3>1. Multi-Role Authentication Structure</h3>
                    <p>
                        The application separates Admin, Manager, and Employee flows into dedicated login, registration,
                        dashboard, and guard files. This makes each role independent, easier to maintain, and safer from
                        unauthorized cross-access.
                    </p>
                </article>

                <article class="note-card">
                    <h3>2. OTP-Based Registration and Recovery</h3>
                    <p>
                        Manager and Employee registration uses email OTP verification before password setup. Password
                        recovery also follows an OTP flow, which helps confirm user identity before credentials are
                        changed.
                    </p>
                </article>

                <article class="note-card">
                    <h3>3. Role-Based Authorization</h3>
                    <p>
                        Protected pages are guarded through role-specific login-check files. A valid Admin session
                        cannot
                        be used as a Manager session, and an Employee token cannot open Manager or Admin pages. Each
                        role
                        has its own access boundary.
                    </p>
                </article>

                <article class="note-card">
                    <h3>4. Session Handling and Persistent Login</h3>
                    <p>
                        After successful sign-in, the system stores role-specific session data and also issues secure
                        auth
                        cookies. If a session expires but the token is still valid, the application can restore the
                        session
                        and continue the user journey smoothly.
                    </p>
                </article>

                <article class="note-card">
                    <h3>5. Token Expiry and Login Continuity</h3>
                    <p>
                        The project supports token expiry timestamps and refreshes valid sessions for Manager and
                        Employee
                        users, creating a practical "remember me" style experience while still enforcing expiration
                        rules.
                    </p>
                </article>

                <article class="note-card">
                    <h3>6. Password Security Controls</h3>
                    <p>
                        Passwords are validated with strong rules requiring uppercase, lowercase, numeric, and special
                        characters. On submission, passwords are hashed before storage, which is essential for secure
                        credential management.
                    </p>
                </article>

                <article class="note-card">
                    <h3>7. Secure Data Access Patterns</h3>
                    <p>
                        Database operations in the authentication flow are implemented with prepared statements. This
                        helps
                        reduce SQL injection risk and improves overall reliability of login, registration, and account
                        verification features.
                    </p>
                </article>

                <article class="note-card">
                    <h3>8. Real-World Application Showcase</h3>
                    <p>
                        This project highlights a complete authentication workflow including secure login, role-based
                        access,
                        OTP verification, and session handling, reflecting real-world application standards.
                    </p>
                </article>
            </div>

            <div class="demo-note">
                <strong>Presentation Note:</strong> This project can be described as a complete authentication
                foundation
                for enterprise-style web applications where different user roles require different permissions, isolated
                dashboards, secure onboarding, and controlled access to protected resources.
            </div>
        </section>
    </div>
</body>

</html>
