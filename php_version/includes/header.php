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

        /* Navigation Menu */
        .nav-menu {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
        }

        /* Mobile Menu */
        @media (max-width: 768px) {
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
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['is_admin']): ?>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' && strpos($_SERVER['REQUEST_URI'], '/admin/') !== false ? 'active' : ''; ?>" href="/admin/dashboard.php">
                            Admin Dashboard
                        </a>
                    <?php else: ?>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' && strpos($_SERVER['REQUEST_URI'], '/user/') !== false ? 'active' : ''; ?>" href="/user/dashboard.php">
                            My Pets
                        </a>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'lost_pets.php' ? 'active' : ''; ?>" href="/lost_pets.php">
                            Lost Pets
                        </a>
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'adoption.php' ? 'active' : ''; ?>" href="/adoption.php">
                            Adoption
                        </a>
                    <?php endif; ?>
                    <a class="nav-link" href="/user/logout.php">
                        Logout
                    </a>
                <?php else: ?>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'login.php' ? 'active' : ''; ?>" href="/login.php">
                        Login
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'register.php' ? 'active' : ''; ?>" href="/register.php">
                        Register
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'lost_pets.php' ? 'active' : ''; ?>" href="/lost_pets.php">
                        Lost Pets
                    </a>
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'view_emails.php' ? 'active' : ''; ?>" href="/view_emails.php">
                        Test Emails
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <div class="container fade-in">
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
                menu.classList.toggle('show');
            }

            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                const menu = document.getElementById('navMenu');
                const toggler = document.querySelector('.navbar-toggler');

                if (!menu.contains(event.target) && !toggler.contains(event.target)) {
                    menu.classList.remove('show');
                }
            });
        </script>