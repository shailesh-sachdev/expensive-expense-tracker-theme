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

namespace Expensive;

class Theme {
    public function __construct() {
        add_action( 'after_setup_theme', [ $this, 'setup' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_assets' ] );
        add_action( 'after_setup_theme', [ $this, 'init_classes' ] );
        add_filter( 'template_include', [ $this, 'use_dashboard_header' ] );
    }

    /**
     * Theme Setup
     */
    public function setup() {
        add_theme_support( 'title-tag' );
        add_theme_support( 'post-thumbnails' );

        register_nav_menus( [
            'primary' => __( 'Primary Menu', 'expensive' ),
        ] );
    }

    /**
     * Enqueue Scripts & Styles
     */
    public function enqueue_assets() {
        wp_enqueue_style(
            'expensive-google-fonts',
            'https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap',
            [],
            null
        );

        wp_enqueue_style(
            'expensive-bootstrap',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css',
            [],
            '5.3.3'
        );

        wp_enqueue_style(
            'expensive-style',
            get_stylesheet_uri(),
            [ 'expensive-bootstrap' ],
            wp_get_theme()->get( 'Version' )
        );

        wp_enqueue_script(
            'expensive-bootstrap',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js',
            [ 'jquery' ],
            '5.3.3',
            true
        );
    }

    /**
     * Initialize Core Classes
     */
    public function init_classes() {
        Auth::get_instance();
        Expenses::get_instance();
        Cards::get_instance();
        Loans::get_instance();
        Reminders::get_instance();
    }

    /**
     * Swap headers depending on context
     */
    public function use_dashboard_header( $template ) {
        if ( is_user_logged_in() && is_page( [ 'dashboard', 'expenses', 'loans', 'credit-cards', 'profile' ] ) ) {
            add_filter( 'get_header', fn( $name ) => 'dashboard' );
        }

        return $template;
    }

    public static function get_instance() {
        static $instance = null;
        if ( $instance === null ) {
            $instance = new self();
        }
        return $instance;
    }
}

// Initialize theme
Theme::get_instance();
