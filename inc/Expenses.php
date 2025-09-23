<?php
namespace Expensive;

class Expenses {
    private static $instance = null;

    private function __construct() {
        // Future: register custom post type or custom DB table
        add_action( 'init', [ $this, 'register_expenses' ] );

        // AJAX handlers for adding expense and income
        add_action( 'wp_ajax_exp_add_expense', [ $this, 'save_expense' ] );
        add_action( 'wp_ajax_exp_add_income', [ $this, 'save_income' ] );
        add_action('template_redirect', [$this, 'handle_post_submission']);

    }

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register_expenses() {
        // Placeholder: register CPT for expenses (optional)
        // Example for future:
        /*
        $labels = [
            'name' => 'Expenses',
            'singular_name' => 'Expense',
            'menu_name' => 'Expenses',
        ];
        $args = [
            'labels' => $labels,
            'public' => false,
            'show_ui' => true,
            'supports' => ['title', 'editor', 'custom-fields'],
        ];
        register_post_type('expense', $args);
        */
    }

    /**
     * Save Expense
     */
    public function save_expense() {
        $this->save_transaction('expense');
    }

    /**
     * Save Income
     */
    public function save_income() {
        $this->save_transaction('income');
    }

    /**
     * Common function to save transaction
     */
    private function save_transaction( $type ) {
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'exp_add_' . $type ) ) {
            wp_send_json_error( 'Invalid nonce' );
        }

        $user_id = get_current_user_id();
        if ( ! $user_id ) wp_send_json_error( 'User not logged in' );

        // Sanitize input
        $data = [
            'type'           => $type,
            'amount'         => floatval( $_POST['amount'] ?? 0 ),
            'category'       => sanitize_text_field( $_POST['category'] ?? '' ),
            'assigned_to'    => sanitize_text_field( $_POST['assigned_to'] ?? '' ),
            'payment_method' => sanitize_text_field( $_POST['payment_method'] ?? '' ),
            'comment'        => sanitize_textarea_field( $_POST['comment'] ?? '' ),
            'date'           => sanitize_text_field( $_POST['date'] ?? date('Y-m-d') ),
            'user_id'        => $user_id,
        ];

        // Save in user_meta (MVP)
        $transactions = get_user_meta( $user_id, 'transactions', true ) ?: [];
        $transactions[] = $data;
        update_user_meta( $user_id, 'transactions', $transactions );

        wp_send_json_success( $data );
    }

    /**
     * Get all transactions for current user
     */
    public static function get_user_transactions( $user_id = null ) {
        $user_id = $user_id ?: get_current_user_id();
        if ( ! $user_id ) return [];
        return get_user_meta( $user_id, 'transactions', true ) ?: [];
    }
    public function handle_post_submission() {
    if( isset($_POST['exp_add_expense_nonce']) && wp_verify_nonce($_POST['exp_add_expense_nonce'], 'exp_add_expense') ) {
        $_POST['nonce'] = $_POST['exp_add_expense_nonce']; // reuse existing save_transaction
        $this->save_expense();
        wp_redirect( $_SERVER['REQUEST_URI'] ); // reload page
        exit;
    }
    if( isset($_POST['exp_add_income_nonce']) && wp_verify_nonce($_POST['exp_add_income_nonce'], 'exp_add_income') ) {
        $_POST['nonce'] = $_POST['exp_add_income_nonce'];
        $this->save_income();
        wp_redirect( $_SERVER['REQUEST_URI'] );
        exit;
    }
}
}
