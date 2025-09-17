<?php
/**
 * Theme Footer
 */
?>
            </main> <!-- end main -->
        </div> <!-- end flex-grow -->
    </div> <!-- end d-flex -->

    <footer class="bg-dark text-white py-3 text-center">
        <small>&copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?> | Built with ❤️ WordPress + Bootstrap</small>
    </footer>

    <script>
        // Sidebar toggle for mobile
        document.addEventListener('DOMContentLoaded', function() {
            const toggle = document.getElementById('sidebarToggle');
            const sidebar = document.getElementById('sidebar');
            if(toggle) {
                toggle.addEventListener('click', function() {
                    sidebar.classList.toggle('d-none');
                });
            }
        });
    </script>

    <?php wp_footer(); ?>
</body>
</html>
