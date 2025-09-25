<?php
namespace Expensive;

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class Cards {
    private static $instance = null;
    private $table;

    private function __construct() {
        global $wpdb;
        $this->table = $wpdb->prefix . 'exp_credit_cards';

        // Optional: AJAX handlers for frontend
        add_action( 'wp_ajax_exp_add_card', [ $this, 'ajax_add_card' ] );
        add_action( 'wp_ajax_exp_delete_card', [ $this, 'ajax_delete_card' ] );

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
        // Placeholder for future card registration
    }

    /**
     * Add a credit card
     */
    public function add_card( $data ) {
        global $wpdb;
        $inserted = $wpdb->insert( $this->table, [
            'user_id'      => get_current_user_id(),
            'card_name'    => sanitize_text_field( $data['card_name'] ?? '' ),
            'last_digits'  => sanitize_text_field( $data['last_digits'] ?? '' ),
            'card_limit'   => floatval( $data['card_limit'] ?? 0 ),
            'billing_date' => intval( $data['billing_date'] ?? 0 ),
            'due_date'     => intval( $data['due_date'] ?? 0 ),
            'created_at'   => current_time( 'mysql' ),
            'updated_at'   => current_time( 'mysql' ),
        ] );

        if ( $inserted ) {
            return (int) $wpdb->insert_id;
        }
        return false;
    }

    public function get_cards( $user_id = null ) {
        global $wpdb;
        $user_id = $user_id ?: get_current_user_id();
        if ( ! $user_id ) return [];

        $results = $wpdb->get_results( $wpdb->prepare(
            "SELECT * FROM {$this->table} WHERE user_id = %d ORDER BY id DESC",
            $user_id
        ), ARRAY_A );

        return $results ?: [];
    }

    public function update_card( $id, $data ) {
        global $wpdb;
        $id = intval( $id );
        return $wpdb->update( $this->table, [
            'card_name'    => sanitize_text_field( $data['card_name'] ?? '' ),
            'last_digits'  => sanitize_text_field( $data['last_digits'] ?? '' ),
            'card_limit'   => floatval( $data['card_limit'] ?? 0 ),
            'billing_date' => intval( $data['billing_date'] ?? 0 ),
            'due_date'     => intval( $data['due_date'] ?? 0 ),
            'updated_at'   => current_time( 'mysql' ),
        ], [
            'id'      => $id,
            'user_id' => get_current_user_id(),
        ] );
    }

    public function delete_card( $id ) {
        global $wpdb;
        return (bool) $wpdb->delete( $this->table, [
            'id'      => intval( $id ),
            'user_id' => get_current_user_id(),
        ] );
    }

    /**
     * AJAX add card
     */
    public function ajax_add_card() {
        check_ajax_referer( 'exp_card_nonce', 'nonce' );

        $user_id = get_current_user_id();
        if ( ! $user_id ) wp_send_json_error( 'Not logged in' );

        $id = $this->add_card([
            'card_name'    => $_POST['card_name'] ?? '',
            'last_digits'  => $_POST['last_digits'] ?? '',
            'card_limit'   => $_POST['card_limit'] ?? 0,
            'billing_date' => $_POST['billing_date'] ?? 0,
            'due_date'     => $_POST['due_date'] ?? 0,
        ]);

        if ( $id ) {
            wp_send_json_success([ 'id' => $id ]);
        }

        wp_send_json_error( 'Failed to add card' );
    }

    /**
     * AJAX delete card
     */
    public function ajax_delete_card() {
        check_ajax_referer( 'exp_card_nonce', 'nonce' );
        $id = intval( $_POST['id'] ?? 0 );
        if ( $id && $this->delete_card( $id ) ) {
            wp_send_json_success();
        }
        wp_send_json_error( 'Delete failed' );
    }

    /**
     * Placeholder for parsing PDF statements
     */
    public function parse_statement( $pdf_file ) {
        // Logic to extract transactions from PDF
    }
}
