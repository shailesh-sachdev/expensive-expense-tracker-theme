<?php
namespace Expensive;

class Auth {
    private static $instance = null;

    private function __construct() {
        add_action( 'template_redirect', [ $this, 'protect_pages' ] );
         add_filter( 'login_redirect', [ $this, 'redirect_after_login' ], 10, 3 );
        add_action( 'wp_logout', [ $this, 'redirect_after_logout' ] );
        // Future: add login form handling, registration, password reset
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
        // Only redirect if login was successful and user is not admin
        if ( isset( $user->roles ) && in_array( 'administrator', $user->roles ) ) {
            return admin_url(); // keep admins in wp-admin
        }
        else{
            return site_url( '/dashboard' );
        }
    }
    public function redirect_after_logout() {
        wp_safe_redirect( site_url( '/login' ) );
        exit;
    }
}
