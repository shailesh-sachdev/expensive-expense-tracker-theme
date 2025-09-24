<?php
namespace Expensive;

class Family_Members {
    private static $instance = null;

    private function __construct() {
        // AJAX handlers
        add_action('wp_ajax_exp_add_family_member', [$this, 'add_family_member']);
        add_action('wp_ajax_exp_delete_family_member', [$this, 'delete_family_member']);
        add_action('wp_ajax_exp_get_family_members', [$this, 'get_family_members']);
    }

    public static function get_instance() {
        if ( self::$instance === null ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Add family member
     */
    public function add_family_member() {
        if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'exp_family_nonce') ) {
            wp_send_json_error('Invalid request');
        }


        global $wpdb;
        $table = $wpdb->prefix . 'exp_family_members';
        $user_id = get_current_user_id();
        if ( ! $user_id ) wp_send_json_error('Not logged in');

        $data = [
            'user_id' => $user_id,
            'name'    => sanitize_text_field($_POST['name'] ?? ''),
            'email'   => sanitize_email($_POST['email'] ?? ''),
            'role'    => sanitize_text_field($_POST['role'] ?? ''),
        ];

        $inserted = $wpdb->insert($table, $data);
        if ( $inserted ) {
            $data['id'] = $wpdb->insert_id;
            wp_send_json_success($data);
        } else {
            wp_send_json_error('Failed to add member');
        }
    }

    /**
     * Delete family member
     */
    public function delete_family_member() {
        if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'exp_family_nonce') ) {
            wp_send_json_error('Invalid request');
        }


        global $wpdb;
        $table = $wpdb->prefix . 'exp_family_members';
        $user_id = get_current_user_id();
        $id = intval($_POST['id'] ?? 0);

        if ( ! $user_id || ! $id ) wp_send_json_error('Invalid request');

        $deleted = $wpdb->delete($table, ['id' => $id, 'user_id' => $user_id]);
        if ( $deleted ) {
            wp_send_json_success('Deleted');
        } else {
            wp_send_json_error('Delete failed');
        }
    }

    /**
     * Get family members
     */
    public function get_family_members() {
        if ( ! isset($_POST['nonce']) || ! wp_verify_nonce($_POST['nonce'], 'exp_family_nonce') ) {
            wp_send_json_error('Invalid request');
        }

        global $wpdb;
        $table = $wpdb->prefix . 'exp_family_members';
        $user_id = get_current_user_id();
        if ( ! $user_id ) {
            wp_send_json_error('Not logged in');
        }
        $results = $wpdb->get_results(
            $wpdb->prepare("SELECT * FROM $table WHERE user_id = %d ORDER BY id DESC", $user_id),
            ARRAY_A
        );
        // Optionally log for debugging
        // error_log(print_r($results, true));
        wp_send_json_success($results);
    }
}
