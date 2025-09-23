<?php
/**
 * Template Name: Login
 */
get_header();

if ( is_user_logged_in() ) {
    wp_redirect( site_url( '/dashboard' ) );
    exit;
}
?>

<div class="d-flex justify-content-center align-items-center vh-100 bg-light">
    <div class="card shadow-sm border-0 p-4" style="max-width: 400px; width: 100%;">
        <h3 class="fw-bold mb-4 text-center">Sign In</h3>

        <form name="loginform" id="loginform" action="<?php echo esc_url( wp_login_url() ); ?>" method="post">
            <div class="mb-3">
                <label for="user_login" class="form-label">Username or Email</label>
                <input type="text" name="log" id="user_login" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="user_pass" class="form-label">Password</label>
                <input type="password" name="pwd" id="user_pass" class="form-control" required>
            </div>

            <div class="form-check mb-3">
                <input name="rememberme" type="checkbox" id="rememberme" value="forever" class="form-check-input">
                <label class="form-check-label" for="rememberme">Remember Me</label>
            </div>

            <input type="hidden" name="redirect_to" value="<?php echo esc_url( site_url( '/dashboard' ) ); ?>">

            <div class="d-grid">
                <button type="submit" name="wp-submit" id="wp-submit" class="btn btn-primary">
                    Login
                </button>
            </div>
        </form>

        <div class="text-center mt-3">
            <a href="<?php echo esc_url( wp_registration_url() ); ?>" class="text-decoration-none">
                Create an account
            </a>
        </div>
    </div>
</div>

<?php get_footer(); ?>
