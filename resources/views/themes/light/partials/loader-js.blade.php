

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Retrieve default theme and changeable mode from server-side variables
            let defaultTheme = "{{basicControl()->default_mode}}"; // e.g., 'dark' or 'light'
            let changeable = "{{basicControl()->changeable_mode}}"; // e.g., 1 for changeable, 0 for not

            // If the theme is not changeable, set the default theme in localStorage
            if (changeable != 1) {
                localStorage.setItem('dark-theme', defaultTheme);
            }

            // Apply the theme on page load
            setTheme();

        });
    </script>
