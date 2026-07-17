<!-- 🌐 GLOBAL THEME MEMORY FORCE -->
<?php if (isset($_GET['page']) && $_GET['page'] === 'dashboard' && isset($_GET['msg']) && $_GET['msg'] === 'success'): ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Fetch current system dark/light configuration state rules
            const systemTheme = localStorage.getItem('pos_theme') || 'dark';
            const isDark = (systemTheme === 'dark');

            Swal.fire({
                title: 'Access Authorized!',
                text: 'Welcome back, <?= htmlspecialchars($_SESSION['fullname'] ?? "Operator"); ?>! Terminal session initialized.',
                icon: 'success',
                timer: 5000,
                timerProgressBar: true,
                showConfirmButton: false,
                // 🎨 Matches SweetAlert background layers to your exact dark/light themes
                background: isDark ? '#1e293b' : '#ffffff',
                color: isDark ? '#f8fafc' : '#1e293b',
                iconColor: '#38bdf8' // Uses your sharp sky-blue primary layout color accents
            }).then(() => {
                // Clean up the URL string so refreshing the page doesn't trigger the alert again!
                const cleanUrl = window.location.protocol + "//" + window.location.host + window.location.pathname + "?page=dashboard";
                window.history.replaceState({
                    path: cleanUrl
                }, '', cleanUrl);
            });
        });
    </script>
<?php endif; ?>
<script>
    // Run this instantly to prevent screen flashing/blinking on load
    (function() {
        const savedTheme = localStorage.getItem('pos_theme') || 'dark'; // Defaulting to dark mode system-wide
        document.documentElement.setAttribute('data-bs-theme', savedTheme);
    })();
    // Global Theme Toggle Function with Icon Updates
    function toggleLayoutTheme() {
        // 1. Get current theme state status
        const currentTheme = document.documentElement.getAttribute('data-bs-theme') || 'dark';

        // 2. Invert selection value
        const targetTheme = (currentTheme === 'dark') ? 'light' : 'dark';

        // 3. Inject back onto the master app node framework
        document.documentElement.setAttribute('data-bs-theme', targetTheme);
        localStorage.setItem('pos_theme', targetTheme);

        // 4. Update the visual display icon state instantly
        updateThemeToggleIcon(targetTheme);
    }

    function updateThemeToggleIcon(theme) {
        const iconEl = document.getElementById('themeIcon');
        if (!iconEl) return;

        if (theme === 'dark') {
            iconEl.className = 'bi bi-moon-stars-fill text-primary fs-5';
        } else {
            iconEl.className = 'bi bi-sun-fill text-warning fs-5';
        }
    }

    // Automatically match the core system state icon configuration on DOM initialization ready state
    document.addEventListener('DOMContentLoaded', () => {
        const activeTheme = localStorage.getItem('pos_theme') || 'dark';
        updateThemeToggleIcon(activeTheme);
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>