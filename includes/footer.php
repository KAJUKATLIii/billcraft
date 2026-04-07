    <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->

    <!-- Core Scripts -->
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/metisMenu/metisMenu.min.js"></script>
    <script src="assets/js/sb-admin-2.js"></script>
    <script src="assets/js/jquery.validate.min.js"></script>

    <script>
        // ─── Theme Toggle ─────────────────────
        function toggleTheme() {
            const html = document.documentElement;
            if (html.classList.contains('dark')) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
            }
        }

        // ─── Sidebar Toggle (mobile) ──────────
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('sidebar-open');
        }

        // ─── DOM Ready ────────────────────────
        $(document).ready(function () {

            // Show mobile sidebar toggle on small screens
            function checkMobile() {
                const isMobile = window.innerWidth <= 768;
                const btn = document.getElementById('sidebar-toggle');
                if (btn) btn.style.display = isMobile ? 'flex' : 'none';
            }
            checkMobile();
            window.addEventListener('resize', checkMobile);

            // Close sidebar on overlay click (mobile)
            $(document).on('click', function(e) {
                if (window.innerWidth <= 768) {
                    const sidebar = $('#sidebar');
                    if (!sidebar.is(e.target) && sidebar.has(e.target).length === 0) {
                        sidebar.removeClass('sidebar-open');
                    }
                }
            });

            // Smooth fade-in for page content
            $('#page-wrapper').addClass('animate-fade-in');

            // Auto-hide flash messages
            setTimeout(function () {
                $('.alert').fadeOut('slow');
            }, 5000);

            // Add active class highlight based on current URL
            const currentPath = window.location.pathname.split('/').pop();
            $('.sidebar ul li a').each(function() {
                const href = $(this).attr('href');
                if (href === currentPath) {
                    $(this).addClass('active');
                }
            });
        });
    </script>
</body>
</html>