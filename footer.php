<?php
/**
 * Theme Footer
 */
?>
    </main> <!-- end main -->

    <footer class="bg-dark text-white py-4 mt-auto">
        <div class="container text-center">
            <p class="mb-1">
                &copy; <?php echo date( 'Y' ); ?> <?php bloginfo( 'name' ); ?>. All rights reserved.
            </p>
            <small class="text-muted">
                Built with ❤️ using WordPress + Bootstrap
            </small>
        </div>
    </footer>

    <?php wp_footer(); ?>
</body>
</html>
