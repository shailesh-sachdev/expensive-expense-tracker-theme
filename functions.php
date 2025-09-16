<?php
function expense_tracker_theme_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo');

    // Register menu
    register_nav_menus(array(
        'primary' => __('Primary Menu', 'expense-tracker'),
    ));
}
add_action('after_setup_theme', 'expense_tracker_theme_setup');

// Enqueue Bootstrap & theme CSS
function expense_tracker_enqueue_scripts() {
    // Bootstrap CSS
    wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
    // Theme CSS
    wp_enqueue_style('theme-style', get_stylesheet_uri());

    // Bootstrap JS (needs Popper + Bootstrap bundle)
    wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);

    wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), null, true);

}
add_action('wp_enqueue_scripts', 'expense_tracker_enqueue_scripts');


// Redirect users based on login status
function expense_tracker_redirects() {
    // Prevent this from running inside admin area
    if ( is_admin() ) {
        return;
    }

    $current_url = esc_url( home_url( add_query_arg( null, null ) ) );
    $login_page  = home_url('/login');
    $dashboard   = home_url('/dashboard');

    // // If not logged in and not already on login page -> redirect to login
    // if ( !is_user_logged_in() && $current_url !== $login_page ) {
    //     wp_redirect( $login_page );
    //     exit;
    // }

    // After login, redirect user to dashboard
    add_filter('login_redirect', function($redirect_to, $request, $user) use ($dashboard) {
        // Only redirect if login is successful and user is valid
        if ( isset($user->ID) ) {
            return $dashboard;
        }
        return $redirect_to;
    }, 10, 3);

    // If logged in and trying to access login page -> redirect to dashboard
    if ( is_user_logged_in() && $current_url === $login_page ) {
        wp_redirect( $dashboard );
        exit;
    }
}
add_action( 'template_redirect', 'expense_tracker_redirects' );

