<?php
namespace Expensive;

class Loans {
    private static $instance = null;

    private function __construct() {
        // Future: register loan CPT or custom DB
        add_action( 'init', [ $this, 'register_loans' ] );
    }

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register_loans() {
        // Placeholder: loan registration logic
    }
}
