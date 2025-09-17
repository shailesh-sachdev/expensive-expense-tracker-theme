<?php
namespace Expensive;

class Cards {
    private static $instance = null;

    private function __construct() {
        // Future: register card CPT or custom DB
        add_action( 'init', [ $this, 'register_cards' ] );
    }

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register_cards() {
        // Placeholder: card registration logic
    }

    public function parse_statement( $pdf_file ) {
        // Placeholder: logic to extract transactions from PDF
    }
}
