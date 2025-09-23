<?php
namespace Expensive;

class Auth {
    private static $instance = null;

    private function __construct() {
        add_action( 'template_redirect', [ $this, 'protect_pages' ] );
        add_filter( 'login_redirect', [ $this, 'redirect_after_login' ], 10, 3 );
        add_action( 'wp_logout', [ $this, 'redirect_after_logout' ] );
        add_action( 'init', [ $this, 'handle_registration' ] );
    }

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function protect_pages() {
        if ( ! is_user_logged_in() ) {
            $restricted = [ 'dashboard', 'expenses', 'loans' ];
            if ( is_page( $restricted ) ) {
                wp_redirect( site_url( '/login' ) );
                exit;
            }
        }
    }

    public function redirect_after_login( $redirect_to, $requested_redirect_to, $user ) {
        if ( isset( $user->roles ) && in_array( 'administrator', $user->roles, true ) ) {
            return admin_url();
        }
        return site_url( '/dashboard' );
    }

    public function redirect_after_logout() {
        wp_safe_redirect( site_url( '/login' ) );
        exit;
    }

    public function handle_registration() {
        if ( isset( $_POST['exp_register_nonce'] ) && wp_verify_nonce( $_POST['exp_register_nonce'], 'exp_register_action' ) ) {

            $username = sanitize_user( $_POST['username'] );
            $email    = sanitize_email( $_POST['email'] );
            $password = $_POST['password'];

            $errors = new \WP_Error();

            if ( empty($username) || empty($email) || empty($password) ) {
                $errors->add( 'empty_fields', 'All fields are required.' );
            }
            if ( username_exists( $username ) ) {
                $errors->add( 'username_exists', 'Username already exists.' );
            }
            if ( email_exists( $email ) ) {
                $errors->add( 'email_exists', 'Email already exists.' );
            }
            if ( ! empty($errors->errors) ) {
                set_transient( 'expensive_register_errors', $errors->get_error_messages(), 30 );
                wp_safe_redirect( wp_get_referer() );
                exit;
            }

            $user_id = wp_create_user( $username, $password, $email );

            if ( ! is_wp_error( $user_id ) ) {
                // Save family members if submitted
                if ( ! empty($_POST['family_members']) ) {
                    $family_members = array_map(function($member){
                        return [
                            'name'  => sanitize_text_field($member['name']),
                            'email' => sanitize_email($member['email']),
                            'role'  => sanitize_text_field($member['role']),
                        ];
                    }, $_POST['family_members']);
                    update_user_meta( $user_id, 'family_members', $family_members );
                }

                // Auto-login user
                wp_set_current_user($user_id);
                wp_set_auth_cookie($user_id);
                wp_safe_redirect(site_url('/dashboard'));
                exit;
            } else {
                set_transient('expensive_register_errors', [$user_id->get_error_message()], 30);
                wp_safe_redirect(wp_get_referer());
                exit;
            }
        }
    }
}
