<?php
namespace Expensive;

class Expenses {
    private static $instance = null;

    private function __construct() {
        // Register hooks
        add_action( 'init', [ $this, 'register_expenses' ] );

        // AJAX handlers
        add_action( 'wp_ajax_exp_add_expense', [ $this, 'save_expense' ] );
        add_action( 'wp_ajax_exp_add_income', [ $this, 'save_income' ] );

        // Handle form submissions (non-AJAX)
        add_action( 'template_redirect', [ $this, 'handle_post_submission' ] );
    }

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function register_expenses() {
        // Placeholder for CPT (if needed in future)
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

        global $wpdb;
        $table_name = $wpdb->prefix . 'exp_transactions';

        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            wp_send_json_error( 'User not logged in' );
        }

        $data = [
            'user_id'        => $user_id,
            'family_member_id' => intval($_POST['assigned_to'] ?? 0),
            'type'           => $type,
            'amount'         => floatval($_POST['amount'] ?? 0),
            'category'       => sanitize_text_field($_POST['category'] ?? ''),
            'payment_method' => sanitize_text_field($_POST['payment_method'] ?? ''),
            'comment'        => sanitize_textarea_field($_POST['comment'] ?? ''),
            'date'           => sanitize_text_field($_POST['date'] ?? date('Y-m-d')),
            'created_at'     => current_time('mysql'),
        ];

        $inserted = $wpdb->insert($table_name, $data);

        if ( $inserted ) {
            wp_send_json_success($data);
        } else {
            wp_send_json_error('Failed to save transaction');
        }
    }

    /**
     * Get all transactions for a user
     */
    public static function get_user_transactions( $user_id = null, $month = null, $year = null ) {
        global $wpdb;
        $transactions_table = $wpdb->prefix . 'exp_transactions';

        $user_id = $user_id ?: get_current_user_id();
        if ( ! $user_id ) return [];

        $query = "SELECT * FROM $transactions_table WHERE user_id = %d";
        $params = [ $user_id ];

        if ( $month && $year ) {
            $query .= " AND MONTH(date) = %d AND YEAR(date) = %d";
            $params[] = $month;
            $params[] = $year;
        }

        $query .= " ORDER BY date DESC";

        return $wpdb->get_results( $wpdb->prepare( $query, ...$params ), ARRAY_A );
    }

    /**
     * Get totals (income/expense) for dashboard
     */
    public static function get_user_totals( $user_id = null ) {
        global $wpdb;
        $table_name = $wpdb->prefix . 'exp_transactions';

        $user_id = $user_id ?: get_current_user_id();
        if ( ! $user_id ) return ['income' => 0, 'expense' => 0];

        $income = $wpdb->get_var(
            $wpdb->prepare("SELECT SUM(amount) FROM $table_name WHERE user_id = %d AND type = %s", $user_id, 'income')
        );

        $expense = $wpdb->get_var(
            $wpdb->prepare("SELECT SUM(amount) FROM $table_name WHERE user_id = %d AND type = %s", $user_id, 'expense')
        );

        return [
            'income'  => floatval($income),
            'expense' => floatval($expense),
        ];
    }

    /**
     * Handle non-AJAX form submissions
     */
    public function handle_post_submission() {
        if ( isset($_POST['exp_add_expense_nonce']) && wp_verify_nonce($_POST['exp_add_expense_nonce'], 'exp_add_expense') ) {
            $_POST['nonce'] = $_POST['exp_add_expense_nonce'];
            $this->save_expense();
            wp_redirect( $_SERVER['REQUEST_URI'] );
            exit;
        }
        if ( isset($_POST['exp_add_income_nonce']) && wp_verify_nonce($_POST['exp_add_income_nonce'], 'exp_add_income') ) {
            $_POST['nonce'] = $_POST['exp_add_income_nonce'];
            $this->save_income();
            wp_redirect( $_SERVER['REQUEST_URI'] );
            exit;
        }
    }
    public static function get_family_totals( $user_id = null, $month = null, $year = null ) {
        global $wpdb;
        $transactions_table = $wpdb->prefix . 'exp_transactions';
        $family_table = $wpdb->prefix . 'exp_family_members';

        $user_id = $user_id ?: get_current_user_id();
        if ( ! $user_id ) return [];

        $query = "
            SELECT fm.id, fm.name, fm.role,
                SUM(CASE WHEN t.type = 'income' THEN t.amount ELSE 0 END) as total_income,
                SUM(CASE WHEN t.type = 'expense' THEN t.amount ELSE 0 END) as total_expense
            FROM $family_table fm
            LEFT JOIN $transactions_table t 
                ON fm.id = t.family_member_id AND fm.user_id = t.user_id
            WHERE fm.user_id = %d
        ";
        $params = [ $user_id ];

        if ( $month && $year ) {
            $query .= " AND MONTH(t.date) = %d AND YEAR(t.date) = %d";
            $params[] = $month;
            $params[] = $year;
        }

        $query .= " GROUP BY fm.id, fm.name, fm.role";

        return $wpdb->get_results( $wpdb->prepare( $query, ...$params ), ARRAY_A );
    }
}
