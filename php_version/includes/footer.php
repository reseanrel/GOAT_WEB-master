    </div>

    <footer style="
        background-color: var(--color-bg-secondary);
        border-top: 1px solid var(--color-border);
        padding: var(--spacing-2xl) 0 var(--spacing-xl);
        margin-top: var(--spacing-2xl);
    ">
        <div class="container">
            <div style="
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
                gap: var(--spacing-xl);
                margin-bottom: var(--spacing-xl);
            ">
                <div>
                    <h3 style="
                        font-size: 18px;
                        font-weight: 600;
                        color: var(--color-text);
                        margin-bottom: var(--spacing-md);
                        display: flex;
                        align-items: center;
                        gap: var(--spacing-sm);
                    ">
                        <i class="fas fa-paw" style="color: var(--color-primary);"></i>
                        Pila Pet Registration
                    </h3>
                    <p style="
                        color: var(--color-text-secondary);
                        line-height: 1.6;
                        margin: 0;
                    ">
                        Helping pet owners in Pila, Laguna manage their pets with modern technology.
                        Register, track, and protect your beloved companions.
                    </p>
                </div>

                <div>
                    <h4 style="
                        font-size: 14px;
                        font-weight: 600;
                        color: var(--color-text);
                        margin-bottom: var(--spacing-md);
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    ">Quick Links</h4>
                    <ul style="
                        list-style: none;
                        padding: 0;
                        margin: 0;
                        display: flex;
                        flex-direction: column;
                        gap: var(--spacing-sm);
                    ">
                        <li><a href="/index.php" style="color: var(--color-text-secondary); text-decoration: none; transition: color 0.2s ease;">Home</a></li>
                        <li><a href="/lost_pets.php" style="color: var(--color-text-secondary); text-decoration: none; transition: color 0.2s ease;">Lost Pets</a></li>
                        <li><a href="/adoption.php" style="color: var(--color-text-secondary); text-decoration: none; transition: color 0.2s ease;">Adoption</a></li>
                        <?php if (!isset($_SESSION['user_id'])): ?>
                            <li><a href="/register.php" style="color: var(--color-text-secondary); text-decoration: none; transition: color 0.2s ease;">Register</a></li>
                        <?php endif; ?>
                    </ul>
                </div>

                <div>
                    <h4 style="
                        font-size: 14px;
                        font-weight: 600;
                        color: var(--color-text);
                        margin-bottom: var(--spacing-md);
                        text-transform: uppercase;
                        letter-spacing: 0.5px;
                    ">Contact</h4>
                    <ul style="
                        list-style: none;
                        padding: 0;
                        margin: 0;
                        display: flex;
                        flex-direction: column;
                        gap: var(--spacing-sm);
                    ">
                        <li style="color: var(--color-text-secondary); display: flex; align-items: center; gap: var(--spacing-sm);">
                            <i class="fas fa-map-marker-alt"></i>
                            Pila, Laguna, Philippines
                        </li>
                        <li style="color: var(--color-text-secondary); display: flex; align-items: center; gap: var(--spacing-sm);">
                            <i class="fas fa-envelope"></i>
                            info@pila.pets
                        </li>
                        <li style="color: var(--color-text-secondary); display: flex; align-items: center; gap: var(--spacing-sm);">
                            <i class="fas fa-phone"></i>
                            Emergency: Contact Local Authorities
                        </li>
                    </ul>
                </div>
            </div>

            <div style="
                border-top: 1px solid var(--color-border);
                padding-top: var(--spacing-lg);
                text-align: center;
                color: var(--color-text-muted);
                font-size: 14px;
            ">
                <p style="margin: 0;">
                    © 2024 Pila Pet Registration System. Built with ❤️ for pet lovers.
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