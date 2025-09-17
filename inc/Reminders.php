<?php
namespace Expensive;

class Reminders {
    private static $instance = null;

    private function __construct() {
        // Future: add cron jobs for reminders
        add_action( 'wp', [ $this, 'schedule_reminders' ] );
    }

    public static function get_instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function schedule_reminders() {
        if ( ! wp_next_scheduled( 'expensive_send_reminders' ) ) {
            wp_schedule_event( time(), 'daily', 'expensive_send_reminders' );
        }
    }

    public function send_reminders() {
        // Placeholder: send email/SMS reminders
    }
}
