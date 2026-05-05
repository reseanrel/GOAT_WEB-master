<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pila Pet Registration</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Modern CSS Reset */
        *, *::before, *::after {
            box-sizing: border-box;
        }

        /* Modern Typography System */
        :root {
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            --color-bg: #ffffff;
            --color-bg-secondary: #f8f9fa;
            --color-bg-tertiary: #f1f3f4;
            --color-text: #202124;
            --color-text-secondary: #5f6368;
            --color-text-muted: #80868b;
            --color-border: #e8eaed;
            --color-border-hover: #dadce0;
            --color-primary: #1a73e8;
            --color-primary-hover: #1557b0;
            --color-success: #34a853;
            --color-warning: #fbbc04;
            --color-error: #ea4335;
            --color-accent: #8ab4f8;
            --shadow-sm: 0 1px 2px 0 rgba(60, 64, 67, 0.3), 0 1px 3px 1px rgba(60, 64, 67, 0.15);
            --shadow-md: 0 1px 3px 0 rgba(60, 64, 67, 0.3), 0 4px 8px 3px rgba(60, 64, 67, 0.15);
            --shadow-lg: 0 4px 6px -1px rgba(60, 64, 67, 0.1), 0 10px 15px -3px rgba(60, 64, 67, 0.1);
            --radius-sm: 4px;
            --radius-md: 8px;
            --radius-lg: 12px;
            --radius-xl: 16px;
            --spacing-xs: 4px;
            --spacing-sm: 8px;
            --spacing-md: 16px;
            --spacing-lg: 24px;
            --spacing-xl: 32px;
            --spacing-2xl: 48px;
        }

        body {
            font-family: var(--font-family);
            background-color: var(--color-bg);
            color: var(--color-text);
            line-height: 1.5;
            margin: 0;
            padding: 0;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        /* Modern Navigation */
        .navbar {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--color-border);
            padding: var(--spacing-md) 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s ease;
        }

        .navbar-brand {
            font-weight: 600;
            font-size: 18px;
            color: var(--color-text) !important;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            transition: color 0.2s ease;
        }

        .navbar-brand:hover {
            color: var(--color-primary) !important;
        }

        .navbar-brand i {
            color: var(--color-primary);
            font-size: 20px;
        }

        .nav-link {
            font-weight: 500;
            color: var(--color-text-secondary) !important;
            text-decoration: none;
            padding: var(--spacing-sm) var(--spacing-md);
            border-radius: var(--radius-md);
            transition: all 0.2s ease;
            position: relative;
        }

        .nav-link:hover {
            color: var(--color-primary) !important;
            background-color: var(--color-bg-tertiary);
        }

        .nav-link.active {
            color: var(--color-primary) !important;
            background-color: rgba(26, 115, 232, 0.1);
        }

        /* Mobile Menu Button */
        .navbar-toggler {
            border: none;
            background: none;
            padding: var(--spacing-sm);
            border-radius: var(--radius-md);
            transition: background-color 0.2s ease;
        }

        .navbar-toggler:hover {
            background-color: var(--color-bg-tertiary);
        }

        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(32, 33, 36, 0.6)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 var(--spacing-lg);
        }

        /* Modern Alert System */
        .alert {
            padding: var(--spacing-md);
            border-radius: var(--radius-lg);
            border: none;
            margin-bottom: var(--spacing-lg);
            display: flex;
            align-items: flex-start;
            gap: var(--spacing-sm);
            font-weight: 500;
            box-shadow: var(--shadow-sm);
            position: relative;
            overflow: hidden;
        }

        .alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
        }

        .alert-success {
            background-color: #e8f5e8;
            color: #2e7d32;
        }

        .alert-success::before {
            background-color: var(--color-success);
        }

        .alert-error {
            background-color: #fce8e6;
            color: #c62828;
        }

        .alert-error::before {
            background-color: var(--color-error);
        }

        .alert i {
            font-size: 16px;
            margin-top: 2px;
        }

        .btn-close {
            background: none;
            border: none;
            font-size: 18px;
            color: inherit;
            opacity: 0.6;
            cursor: pointer;
            padding: var(--spacing-xs);
            border-radius: var(--radius-sm);
            transition: opacity 0.2s ease;
        }

        .btn-close:hover {
            opacity: 1;
        }

        /* Sidebar Navigation for Dashboard */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 280px;
            height: 100vh;
            background: var(--color-bg);
            border-right: 1px solid var(--color-border);
            padding: var(--spacing-xl) var(--spacing-lg);
            overflow-y: auto;
            z-index: 1000;
            box-shadow: var(--shadow-lg);
            transition: transform 0.3s ease;
        }

        .sidebar.collapsed {
            transform: translateX(-100%);
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-xl);
            padding-bottom: var(--spacing-lg);
            border-bottom: 1px solid var(--color-border);
        }

        .sidebar-brand {
            font-weight: 700;
            font-size: 20px;
            color: var(--color-primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        .sidebar-user {
            background: var(--color-bg-secondary);
            border-radius: var(--radius-lg);
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-xl);
            border: 1px solid var(--color-border);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            margin-right: var(--spacing-md);
        }

        .user-info h6 {
            margin: 0;
            font-size: 14px;
            font-weight: 600;
            color: var(--color-text);
        }

        .user-info p {
            margin: var(--spacing-xs) 0 0 0;
            font-size: 12px;
            color: var(--color-text-secondary);
        }

        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .sidebar-nav li {
            margin-bottom: var(--spacing-xs);
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: var(--spacing-md);
            padding: var(--spacing-md);
            border-radius: var(--radius-lg);
            text-decoration: none;
            color: var(--color-text-secondary);
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .sidebar-link:hover {
            background: var(--color-bg-secondary);
            color: var(--color-primary);
        }

        .sidebar-link.active {
            background: rgba(26, 115, 232, 0.1);
            color: var(--color-primary);
            font-weight: 600;
        }

        .sidebar-link i {
            font-size: 18px;
            width: 20px;
            text-align: center;
        }

        /* Main Content Area */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            background: var(--color-bg-secondary);
            transition: margin-left 0.3s ease;
        }

        .main-content.expanded {
            margin-left: 0;
        }

        .top-bar {
            background: var(--color-bg);
            border-bottom: 1px solid var(--color-border);
            padding: var(--spacing-md) var(--spacing-xl);
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .menu-toggle {
            background: none;
            border: none;
            padding: var(--spacing-sm);
            border-radius: var(--radius-md);
            color: var(--color-text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .menu-toggle:hover {
            background: var(--color-bg-secondary);
            color: var(--color-text);
        }

        .page-title {
            font-size: 24px;
            font-weight: 600;
            color: var(--color-text);
            margin: 0;
        }

        .page-content {
            padding: var(--spacing-xl);
        }

        /* Navigation Menu (for public pages) */
        .nav-menu {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        /* Mobile Menu */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }

            .nav-menu {
                position: absolute;
                top: 100%;
                left: 0;
                right: 0;
                background: var(--color-bg);
                border-bottom: 1px solid var(--color-border);
                flex-direction: column;
                padding: var(--spacing-lg);
                gap: var(--spacing-md);
                transform: translateY(-100%);
                opacity: 0;
                visibility: hidden;
                transition: all 0.3s ease;
                box-shadow: var(--shadow-lg);
            }

            .nav-menu.show {
                transform: translateY(0);
                opacity: 1;
                visibility: visible;
            }

            .container {
                padding: 0 var(--spacing-md);
            }

            .navbar-brand {
                font-size: 16px;
            }

            .nav-link {
                padding: var(--spacing-sm) var(--spacing-md);
                width: 100%;
                text-align: center;
                border-radius: var(--radius-lg);
            }

            .page-content {
                padding: var(--spacing-lg);
            }
        }

        /* Loading Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<body>
    <?php if (isset($_SESSION['user_id'])): ?>
        <!-- Sidebar for logged-in users -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a class="sidebar-brand" href="/index.php">
                    <i class="fas fa-paw"></i>
                    Pila Pets
                </a>
            </div>

            <div class="sidebar-user">
                <div style="display: flex; align-items: center;">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['full_name'] ?? 'U', 0, 1)); ?>
                    </div>
                    <div class="user-info">
                        <h6><?php echo htmlspecialchars($_SESSION['full_name'] ?? 'User'); ?></h6>
                        <p><?php echo $_SESSION['is_admin'] ? 'Administrator' : 'Pet Owner'; ?></p>
                    </div>
                </div>
            </div>

            <ul class="sidebar-nav">
                <?php if ($_SESSION['is_admin']): ?>
                    <li><a class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false ? 'active' : ''; ?>" href="/admin/dashboard.php"><i class="fas fa-tachometer-alt"></i>Dashboard</a></li>
                    <li><a class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'manage_pets.php' ? 'active' : ''; ?>" href="/admin/manage_pets.php"><i class="fas fa-paw"></i>Manage Pets</a></li>
                    <li><a class="sidebar-link" href="/lost_pets.php"><i class="fas fa-search"></i>Lost Pets</a></li>
                    <li><a class="sidebar-link" href="/adoption.php"><i class="fas fa-heart"></i>Adoption</a></li>
                <?php else: ?>
                    <li><a class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' && strpos($_SERVER['REQUEST_URI'], '/user/') !== false ? 'active' : ''; ?>" href="/user/dashboard.php"><i class="fas fa-home"></i>Dashboard</a></li>
                    <li><a class="sidebar-link <?php echo basename($_SERVER['PHP_SELF']) == 'register_pet.php' ? 'active' : ''; ?>" href="/user/register_pet.php"><i class="fas fa-plus-circle"></i>Register Pet</a></li>
                    <li><a class="sidebar-link" href="/lost_pets.php"><i class="fas fa-search"></i>Lost Pets</a></li>
                    <li><a class="sidebar-link" href="/adoption.php"><i class="fas fa-heart"></i>Adoption</a></li>
                <?php endif; ?>
                <li><a class="sidebar-link" href="#" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i>Logout</a></li>
            </ul>
        </aside>

        <!-- Main content wrapper for dashboard pages -->
        <div class="main-content" id="mainContent">
            <div class="top-bar">
                <button class="menu-toggle" onclick="toggleSidebar()">
                    <i class="fas fa-bars"></i>
                </button>
                <h1 class="page-title" id="pageTitle">Dashboard</h1>
                <div></div> <!-- Spacer for flex layout -->
            </div>
            <div class="page-content fade-in">
    <?php else: ?>
        <!-- Top navbar for public pages -->
        <nav class="navbar">
            <div class="container">
                <a class="navbar-brand" href="/index.php">
                    <i class="fas fa-paw"></i>
                    Pila Pet Registration
                </a>

                <button class="navbar-toggler" type="button" onclick="toggleMobileMenu()">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="nav-menu" id="navMenu">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>" href="/login.php">
                        Login
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>" href="/register.php">
                        Register
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'lost_pets.php' ? 'active' : ''; ?>" href="/lost_pets.php">
                        Lost Pets
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'adoption.php' ? 'active' : ''; ?>" href="/adoption.php">
                        Adoption
                    </a>
                </div>
            </div>
        </nav>

        <div class="container fade-in">
    <?php endif; ?>
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <div><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'">×</button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
                <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'">×</button>
            </div>
        <?php endif; ?>

        <script>
            function toggleMobileMenu() {
                const menu = document.getElementById('navMenu');
                if (menu) menu.classList.toggle('show');
            }

            function toggleSidebar() {
                const sidebar = document.getElementById('sidebar');
                const mainContent = document.getElementById('mainContent');
                if (sidebar) sidebar.classList.toggle('collapsed');
                if (mainContent) mainContent.classList.toggle('expanded');
            }

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                const menu = document.getElementById('navMenu');
                const toggler = document.querySelector('.navbar-toggler');

                if (menu && toggler && !menu.contains(event.target) && !toggler.contains(event.target)) {
                    menu.classList.remove('show');
                }
            });

            // Logout confirmation modal
            function confirmLogout(event) {
                event.preventDefault();
                showLogoutModal();
            }

            function showLogoutModal() {
                // Create modal overlay
                const modal = document.createElement('div');
                modal.id = 'logoutModal';
                modal.style.cssText = `
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.6);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 10000;
                    backdrop-filter: blur(4px);
                    animation: fadeIn 0.3s ease-out;
                `;

                // Create modal content
                modal.innerHTML = `
                    <div style="
                        background: white;
                        border-radius: var(--radius-2xl);
                        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
                        max-width: 400px;
                        width: 90%;
                        animation: slideUp 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                        position: relative;
                        overflow: hidden;
                    ">
                        <div style="
                            padding: var(--spacing-2xl) var(--spacing-2xl) var(--spacing-lg);
                            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                            color: white;
                            text-align: center;
                        ">
                            <div style="
                                width: 60px;
                                height: 60px;
                                background: rgba(255, 255, 255, 0.2);
                                border-radius: 50%;
                                display: flex;
                                align-items: center;
                                justify-content: center;
                                margin: 0 auto var(--spacing-lg);
                            ">
                                <i class="fas fa-sign-out-alt" style="font-size: 24px;"></i>
                            </div>
                            <h3 style="
                                margin: 0;
                                font-size: 24px;
                                font-weight: 700;
                                text-shadow: 0 2px 4px rgba(0,0,0,0.2);
                            ">Logout Confirmation</h3>
                        </div>

                        <div style="
                            padding: var(--spacing-2xl);
                            text-align: center;
                        ">
                            <p style="
                                margin: 0 0 var(--spacing-xl);
                                color: var(--color-text-secondary);
                                font-size: 16px;
                                line-height: 1.5;
                            ">
                                Are you sure you want to logout?<br>
                                <small style="color: var(--color-text-muted);">You'll need to login again to access your account.</small>
                            </p>

                            <div style="
                                display: flex;
                                gap: var(--spacing-lg);
                                justify-content: center;
                            ">
                                <button onclick="closeLogoutModal()" style="
                                    background: #e2e8f0;
                                    color: var(--color-text);
                                    border: 1px solid var(--color-border);
                                    padding: var(--spacing-md) var(--spacing-xl);
                                    border-radius: var(--radius-lg);
                                    font-weight: 600;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                    flex: 1;
                                " onmouseover="this.style.background='#cbd5e0'" onmouseout="this.style.background='#e2e8f0'">
                                    Cancel
                                </button>
                                <button onclick="proceedLogout()" style="
                                    background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
                                    color: white;
                                    border: none;
                                    padding: var(--spacing-md) var(--spacing-xl);
                                    border-radius: var(--radius-lg);
                                    font-weight: 600;
                                    cursor: pointer;
                                    transition: all 0.3s ease;
                                    flex: 1;
                                    box-shadow: 0 4px 16px rgba(229, 62, 62, 0.3);
                                " onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 8px 32px rgba(229, 62, 62, 0.4)'" onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 16px rgba(229, 62, 62, 0.3)'">
                                    <i class="fas fa-sign-out-alt" style="margin-right: var(--spacing-sm);"></i>
                                    Logout
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                document.body.appendChild(modal);

                // Add CSS animations
                const style = document.createElement('style');
                style.textContent = `
                    @keyframes fadeIn {
                        from { opacity: 0; }
                        to { opacity: 1; }
                    }

                    @keyframes fadeOut {
                        from { opacity: 1; }
                        to { opacity: 0; }
                    }

                    @keyframes slideUp {
                        from {
                            opacity: 0;
                            transform: translateY(30px) scale(0.95);
                        }
                        to {
                            opacity: 1;
                            transform: translateY(0) scale(1);
                        }
                    }

                    @media (max-width: 480px) {
                        #logoutModal .modal-content {
                            margin: var(--spacing-lg);
                            width: calc(100% - var(--spacing-2xl));
                        }

                        #logoutModal button {
                            font-size: 14px !important;
                            padding: var(--spacing-sm) var(--spacing-lg) !important;
                        }
                    }
                `;
                document.head.appendChild(style);

                // Focus management
                modal.setAttribute('tabindex', '-1');
                modal.focus();

                // Close on escape key
                document.addEventListener('keydown', handleEscape);
                function handleEscape(e) {
                    if (e.key === 'Escape') {
                        closeLogoutModal();
                        document.removeEventListener('keydown', handleEscape);
                    }
                }

                // Close on outside click
                modal.addEventListener('click', function(e) {
                    if (e.target === modal) {
                        closeLogoutModal();
                    }
                });
            }

            function closeLogoutModal() {
                const modal = document.getElementById('logoutModal');
                if (modal) {
                    modal.style.animation = 'fadeOut 0.3s ease-out';
                    setTimeout(() => {
                        modal.remove();
                    }, 300);
                }
            }

            function proceedLogout() {
                closeLogoutModal();
                window.location.href = '/user/logout.php';
            }

            // Set page title dynamically
            document.addEventListener('DOMContentLoaded', function() {
                const pageTitle = document.getElementById('pageTitle');
                if (pageTitle) {
                    const currentPage = window.location.pathname.split('/').pop().replace('.php', '');
                    const titles = {
                        'dashboard': 'Dashboard',
                        'register_pet': 'Register New Pet',
                        'manage_pets': 'Manage Pets',
                        'lost_pets': 'Lost Pets',
                        'adoption': 'Pet Adoption'
                    };
                    pageTitle.textContent = titles[currentPage] || 'Dashboard';
                }
            });
        </script>