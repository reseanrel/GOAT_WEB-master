            </div>
        </div>



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