<?php
/**
 * Expensive Expense Tracker Theme Functions
 *
 * @package ExpensiveExpenseTracker
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Theme Setup
 */
function expensive_theme_setup() {
    // Let WordPress handle <title>
    add_theme_support( 'title-tag' );

    // Enable featured images
    add_theme_support( 'post-thumbnails' );

    // Register navigation menu(s)
    register_nav_menus( [
        'primary' => __( 'Primary Menu', 'expensive' ),
    ] );
}
add_action( 'after_setup_theme', 'expensive_theme_setup' );

/**
 * Enqueue Scripts & Styles
 */
function expensive_enqueue_assets() {
    // Google Fonts
    wp_enqueue_style(
        'expensive-google-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap',
        [],
        null
    );

    // Bootstrap CSS
    wp_enqueue_style(
        'expensive-bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
        [],
        '5.3.3'
    );

    // Theme stylesheet
    wp_enqueue_style(
        'expensive-style',
        get_stylesheet_uri(),
        [ 'expensive-bootstrap' ],
        wp_get_theme()->get( 'Version' )
    );

    // Bootstrap JS
    wp_enqueue_script(
        'expensive-bootstrap',
        'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
        [ 'jquery' ],
        '5.3.3',
        true
    );
}
add_action( 'wp_enqueue_scripts', 'expensive_enqueue_assets' );

/**
 * Autoload Classes from /inc
 */
spl_autoload_register( function ( $class ) {
    $prefix   = 'Expensive\\';
    $base_dir = get_template_directory() . '/inc/';
    $len      = strlen( $prefix );

    if ( strncmp( $prefix, $class, $len ) !== 0 ) {
        return;
    }

    $relative_class = substr( $class, $len );
    $file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

    if ( file_exists( $file ) ) {
        require $file;
    }
} );

/**
 * Initialize Core Classes
 */
function expensive_init_classes() {
    // Authentication (Login/Register)
    Expensive\Auth::get_instance();

    // Expenses Manager
    Expensive\Expenses::get_instance();

    // Cards Manager
    Expensive\Cards::get_instance();

    // Loans Manager
    Expensive\Loans::get_instance();

    // Reminders Manager
    Expensive\Reminders::get_instance();
}
add_action( 'after_setup_theme', 'expensive_init_classes' );
