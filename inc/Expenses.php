<?php
namespace Expensive;

class Expenses {
    private static $instance = null;

    private function __construct() {
        // Future: register custom post type or custom DB table
        add_action( 'init', [ $this, 'register_expenses' ] );
    }

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register_expenses() {
        // Placeholder: register CPT for expenses (optional)
    }
}
