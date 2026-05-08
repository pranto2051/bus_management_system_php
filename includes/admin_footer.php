            </main>
        </div>
    </div>

    <script>
        // Sidebar toggle functionality
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const sidebar = document.getElementById('adminSidebar');
        const adminWrapper = document.querySelector('.admin-wrapper');

        function toggleSidebar() {
            adminWrapper.classList.toggle('sidebar-collapsed');
        }

        function toggleMobileMenu() {
            adminWrapper.classList.toggle('mobile-menu-open');
        }

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', toggleSidebar);
        }

        if (mobileMenuToggle) {
            mobileMenuToggle.addEventListener('click', toggleMobileMenu);
        }

        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            if (adminWrapper.classList.contains('mobile-menu-open')) {
                if (!sidebar.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
                    adminWrapper.classList.remove('mobile-menu-open');
                }
            }
        });
    </script>
</body>
</html>

