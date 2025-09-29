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
        // New: upload & save handlers
        add_action('wp_ajax_exp_upload_statement', [$this, 'upload_statement']);
        add_action('wp_ajax_exp_save_transactions', [$this, 'save_transactions']);
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
function exp_extract_transactions_from_text($text) {
    $lines = preg_split("/\r\n|\n|\r/", $text);
    $transactions = [];

    foreach ($lines as $line) {
        $line = trim(preg_replace('/\s+/', ' ', $line)); // normalize spaces

        if (preg_match('/^(\d{2}\/\d{2}\/\d{4})([A-Za-z0-9\/\-\.\@\s]+)\s+([\d,]+\.\d{2})\s+(Dr|Cr)$/i', $line, $m)) {
            $transactions[] = [
                'date'   => $m[1],
                'to'     => trim($m[2]),
                'amount' => (float) str_replace(',', '', $m[3]),
                'type'   => strtoupper($m[4]) === 'CR' ? 'income' : 'expense',
            ];
        }
    }

    return $transactions;
}



/**
 * Mock Statement Upload & Parse
     */
    public function upload_statement() {
        error_log('upload_statement called');
        error_log('POST data: ' . print_r($_POST, true));
        // if (!isset($_FILES['pdf_statement']) || $_FILES['pdf_statement']['error'] !== UPLOAD_ERR_OK) {
        //     wp_send_json_error('File upload error');
        // }
        // // In real parser: check file & unlock with password
        // if (empty($_POST['pdf_password'])) {
        //     wp_send_json_error('Password is required.');
        // }

        // // MOCK transactions (later replace with parsed PDF)
        // $transactions = [
        //     ['date' => '2025-09-01', 'amount' => 1200.50, 'to' => 'Amazon'],
        //     ['date' => '2025-09-05', 'amount' => 350.00, 'to' => 'Swiggy'],
        //     ['date' => '2025-09-08', 'amount' => 5000.00, 'to' => 'Flight Booking'],
        // ];

        // wp_send_json_success($transactions);
         global $wpdb;

        if ( ! isset($_FILES['pdf_file']) ) {
            wp_send_json_error(['message' => 'No file uploaded.']);
        }

        $password = sanitize_text_field($_POST['password'] ?? '');

        $file_tmp  = $_FILES['pdf_file']['tmp_name'];
        error_log('File tmp name: ' . $file_tmp);
        $file_name = sanitize_file_name($_FILES['pdf_file']['name']);
        $upload_dir = wp_upload_dir();
        $dest_path  = $upload_dir['basedir'] . '/pdf_file/' . $file_name;

        // Ensure dir exists
        if ( ! file_exists(dirname($dest_path)) ) {
            wp_mkdir_p(dirname($dest_path));
        }

        // Move file
        if ( ! move_uploaded_file($file_tmp, $dest_path) ) {
            wp_send_json_error(['message' => 'File upload failed.']);
        }

        // --- Parse PDF ---
        // We’ll use smalot/pdfparser for PHP (can be installed via composer in plugin folder)
        require_once __DIR__ . '/../vendor/autoload.php';
        $parser = new \Smalot\PdfParser\Parser();

        // Check if PDF is secured (password protected)
        if (!empty($password)) {
            wp_send_json_error(['message' => 'Secured PDF files are not supported. Please upload an unsecured PDF.']);
        }

        try {
            $pdf    = $parser->parseFile($dest_path); // do not pass password
            $text   = $pdf->getText();
            error_log('Extracted text: ' . substr($text, 0, 500000)); // log first 500 chars

            // Convert raw text → transactions
            $transactions = $this->exp_extract_transactions_from_text($text);
            if (empty($transactions)) {
                error_log("⚠️ No matches found in PDF text.");
                wp_send_json_success([
                    'transactions' => [],
                    'message' => 'No transactions could be parsed'
                ]);
            } else {
                wp_send_json_success([
                    'transactions' => $transactions,
                    'message' => 'Statement parsed successfully'
                ]);
            }

            // Save statement reference + password
            $stmt_table = $wpdb->prefix . 'exp_statements';
            $wpdb->insert($stmt_table, [
                'file_name' => $file_name,
                'file_path' => $dest_path,
                'password'  => $password,
                'uploaded_at' => current_time('mysql'),
            ]);

            wp_send_json_success([
                'transactions' => $transactions,
                'message'      => 'Statement parsed successfully!',
            ]);

        } catch (Exception $e) {
            wp_send_json_error(['message' => 'PDF parsing failed: ' . $e->getMessage()]);
        }
    }

    /**
     * Save selected transactions to expense table
     */
    public function save_transactions() {
        error_log('save_transactions called');
        error_log('POST data: ' . print_r($_POST, true));
        $assign = isset($_POST['assign']) ? $_POST['assign'] : [];
        global $wpdb;
        $table = $wpdb->prefix . 'exp_transactions';
        $user_id = get_current_user_id();

        if (empty($_POST['txn'])) {
            wp_send_json_error('No transactions received');
        }
        // error_log('Transactions data: ' . print_r($_POST['txn'], true));
        if (is_string($_POST['txn'])) {
            $transactions = json_decode(stripslashes($_POST['txn']), true);
        } else {
            $transactions = $_POST['txn'];
        }
        // error_log('Decoded transactions: ' . print_r($transactions, true));

        foreach ($transactions as $t) {
            $wpdb->insert($table, [
                'user_id'    => $user_id,
                'date'       => sanitize_text_field($t['date']),
                'amount'     => floatval($t['amount']),
                'category'   => 'Credit Card', // later improve mapping
                'type'       => 'expense',
                'payment_method' => 'credit_card',
                'family_member_id' => isset($t['family_member_id']) ? intval($t['family_member_id']) : 0,
                'comment'    => sanitize_text_field($t['to']),
                'created_at' => current_time('mysql')
            ]);
        }

        wp_send_json_success('Transactions saved!');
    }
}
