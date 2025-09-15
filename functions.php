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
