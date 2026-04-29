            </div>
        </div>

    <footer style="
        background: linear-gradient(135deg, var(--color-bg-secondary) 0%, var(--color-bg-tertiary) 100%);
        border-top: 1px solid var(--color-border);
        padding: var(--spacing-2xl) 0 var(--spacing-xl);
        margin-top: var(--spacing-2xl);
        position: relative;
        overflow: hidden;
    ">
        <div style="
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="footer-pattern" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="rgba(26, 115, 232, 0.03)"/></pattern></defs><rect width="100" height="100" fill="url(%23footer-pattern)"/></svg>');
            opacity: 0.5;
        "></div>

        <div class="container" style="position: relative; z-index: 1;">
            <div style="
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: var(--spacing-xl);
                margin-bottom: var(--spacing-2xl);
            ">
                <div>
                    <div style="
                        display: flex;
                        align-items: center;
                        gap: var(--spacing-sm);
                        margin-bottom: var(--spacing-md);
                    ">
                        <div style="
                            width: 40px;
                            height: 40px;
                            background: linear-gradient(135deg, var(--color-primary), var(--color-accent));
                            border-radius: var(--radius-lg);
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                        ">
                            <i class="fas fa-paw"></i>
                        </div>
                        <h3 style="
                            font-size: 20px;
                            font-weight: 700;
                            color: var(--color-text);
                            margin: 0;
                        ">Pila Pet Registration</h3>
                    </div>
                    <p style="
                        color: var(--color-text-secondary);
                        line-height: 1.6;
                        margin: 0 0 var(--spacing-lg);
                        font-size: 15px;
                    ">
                        Modern pet management for the Pila community. Register, track, and protect your beloved companions with our digital platform.
                    </p>
                    <div style="display: flex; gap: var(--spacing-md);">
                        <a href="#" style="
                            width: 36px;
                            height: 36px;
                            background: var(--color-bg);
                            border: 1px solid var(--color-border);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: var(--color-text-secondary);
                            text-decoration: none;
                            transition: all 0.2s ease;
                        " onmouseover="this.style.background='var(--color-primary)'; this.style.color='white'; this.style.borderColor='var(--color-primary)';"
                           onmouseout="this.style.background='var(--color-bg)'; this.style.color='var(--color-text-secondary)'; this.style.borderColor='var(--color-border)';">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" style="
                            width: 36px;
                            height: 36px;
                            background: var(--color-bg);
                            border: 1px solid var(--color-border);
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: var(--color-text-secondary);
                            text-decoration: none;
                            transition: all 0.2s ease;
                        " onmouseover="this.style.background='var(--color-primary)'; this.style.color='white'; this.style.borderColor='var(--color-primary)';"
                           onmouseout="this.style.background='var(--color-bg)'; this.style.color='var(--color-text-secondary)'; this.style.borderColor='var(--color-border)';">
                            <i class="fab fa-twitter"></i>
                        </a>
            </div>
        <?php if (isset($_SESSION['user_id'])): ?>
            </div>
        <?php endif; ?>

                <div>
                    <h4 style="
                        font-size: 16px;
                        font-weight: 600;
                        color: var(--color-text);
                        margin-bottom: var(--spacing-lg);
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    ">Services</h4>
                    <ul style="
                        list-style: none;
                        padding: 0;
                        margin: 0;
                        display: flex;
                        flex-direction: column;
                        gap: var(--spacing-md);
                    ">
                        <li><a href="/index.php" style="color: var(--color-text-secondary); text-decoration: none; transition: color 0.2s ease; display: flex; align-items: center; gap: var(--spacing-sm);" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--color-text-secondary)'"><i class="fas fa-home" style="font-size: 14px;"></i>Home</a></li>
                        <li><a href="/lost_pets.php" style="color: var(--color-text-secondary); text-decoration: none; transition: color 0.2s ease; display: flex; align-items: center; gap: var(--spacing-sm);" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--color-text-secondary)'"><i class="fas fa-search" style="font-size: 14px;"></i>Lost & Found</a></li>
                        <li><a href="/adoption.php" style="color: var(--color-text-secondary); text-decoration: none; transition: color 0.2s ease; display: flex; align-items: center; gap: var(--spacing-sm);" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--color-text-secondary)'"><i class="fas fa-heart" style="font-size: 14px;"></i>Pet Adoption</a></li>
                        <li><a href="/user/dashboard.php" style="color: var(--color-text-secondary); text-decoration: none; transition: color 0.2s ease; display: flex; align-items: center; gap: var(--spacing-sm);" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--color-text-secondary)'"><i class="fas fa-tachometer-alt" style="font-size: 14px;"></i>My Dashboard</a></li>
                    </ul>
                </div>

                <div>
                    <h4 style="
                        font-size: 16px;
                        font-weight: 600;
                        color: var(--color-text);
                        margin-bottom: var(--spacing-lg);
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    ">Support</h4>
                    <ul style="
                        list-style: none;
                        padding: 0;
                        margin: 0;
                        display: flex;
                        flex-direction: column;
                        gap: var(--spacing-md);
                    ">
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <li><a href="/register.php" style="color: var(--color-text-secondary); text-decoration: none; transition: color 0.2s ease; display: flex; align-items: center; gap: var(--spacing-sm);" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--color-text-secondary)'"><i class="fas fa-user-plus" style="font-size: 14px;"></i>Register</a></li>
                            <li><a href="/login.php" style="color: var(--color-text-secondary); text-decoration: none; transition: color 0.2s ease; display: flex; align-items: center; gap: var(--spacing-sm);" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--color-text-secondary)'"><i class="fas fa-sign-in-alt" style="font-size: 14px;"></i>Login</a></li>
                        <?php endif; ?>
                        <li><a href="/view_emails.php" style="color: var(--color-text-secondary); text-decoration: none; transition: color 0.2s ease; display: flex; align-items: center; gap: var(--spacing-sm);" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--color-text-secondary)'"><i class="fas fa-envelope" style="font-size: 14px;"></i>Test Emails</a></li>
                        <li><a href="#" style="color: var(--color-text-secondary); text-decoration: none; transition: color 0.2s ease; display: flex; align-items: center; gap: var(--spacing-sm);" onmouseover="this.style.color='var(--color-primary)'" onmouseout="this.style.color='var(--color-text-secondary)'"><i class="fas fa-question-circle" style="font-size: 14px;"></i>Help & FAQ</a></li>
                    </ul>
                </div>

                <div>
                    <h4 style="
                        font-size: 16px;
                        font-weight: 600;
                        color: var(--color-text);
                        margin-bottom: var(--spacing-lg);
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    ">Contact Info</h4>
                    <ul style="
                        list-style: none;
                        padding: 0;
                        margin: 0;
                        display: flex;
                        flex-direction: column;
                        gap: var(--spacing-md);
                    ">
                        <li style="color: var(--color-text-secondary); display: flex; align-items: center; gap: var(--spacing-sm);">
                            <i class="fas fa-map-marker-alt" style="color: var(--color-primary); font-size: 16px;"></i>
                            <span>Pila, Laguna<br>Philippines 4010</span>
                        </li>
                        <li style="color: var(--color-text-secondary); display: flex; align-items: center; gap: var(--spacing-sm);">
                            <i class="fas fa-envelope" style="color: var(--color-primary); font-size: 16px;"></i>
                            <span>info@pila.pets</span>
                        </li>
                        <li style="color: var(--color-text-secondary); display: flex; align-items: center; gap: var(--spacing-sm);">
                            <i class="fas fa-phone" style="color: var(--color-primary); font-size: 16px;"></i>
                            <span>Emergency: Contact<br>Local Authorities</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div style="
                border-top: 1px solid var(--color-border);
                padding-top: var(--spacing-xl);
                text-align: center;
            ">
                <div style="
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    gap: var(--spacing-sm);
                    margin-bottom: var(--spacing-md);
                    flex-wrap: wrap;
                ">
                    <span style="color: var(--color-text-muted); font-size: 14px;">© 2024 Pila Pet Registration System</span>
                    <span style="color: var(--color-text-muted);">•</span>
                    <span style="color: var(--color-text-muted); font-size: 14px;">Built with ❤️ for pet lovers</span>
                    <span style="color: var(--color-text-muted);">•</span>
                    <span style="color: var(--color-text-muted); font-size: 14px;">Made in Pila, Laguna</span>
                </div>
                <p style="margin: 0; color: var(--color-text-muted); font-size: 13px;">
                    Protecting and connecting pets and their families since 2024
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Add hover effects for footer links
        document.querySelectorAll('footer a').forEach(link => {
            link.addEventListener('mouseenter', function() {
                this.style.color = 'var(--color-primary)';
            });
            link.addEventListener('mouseleave', function() {
                this.style.color = 'var(--color-text-secondary)';
            });
        });
    </script>
</body>
</html>