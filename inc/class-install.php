<?php
namespace Expensive;

class Install {

    public static function activate() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'exp_transactions';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id BIGINT(20) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            family_member_id BIGINT(20) UNSIGNED NULL,
            type ENUM('income','expense') NOT NULL,
            amount DECIMAL(12,2) NOT NULL,
            category VARCHAR(191) DEFAULT '',
            payment_method VARCHAR(191) DEFAULT '',
            comment TEXT NULL,
            date DATE NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX (user_id),
            INDEX (family_member_id),
            INDEX (type),
            INDEX (date)
        ) $charset_collate;";
          // Family members table
        $family_table = $wpdb->prefix . 'exp_family_members';
        $sql2 = "CREATE TABLE $family_table (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            user_id BIGINT(20) UNSIGNED NOT NULL,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) DEFAULT '',
            role VARCHAR(50) DEFAULT '',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            KEY user_id (user_id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );
        dbDelta( $sql2 );

    }
    public static function migrate_transactions() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'exp_transactions';

    $users = get_users();
    foreach ( $users as $user ) {
        $transactions = get_user_meta($user->ID, 'transactions', true);
        if ( ! empty($transactions) && is_array($transactions) ) {
            foreach ( $transactions as $t ) {
                $wpdb->insert($table_name, [
                    'user_id'        => $user->ID,
                    'family_member_id' => 0, // can't map old data directly
                    'type'           => $t['type'] ?? 'expense',
                    'amount'         => $t['amount'] ?? 0,
                    'category'       => $t['category'] ?? '',
                    'payment_method' => $t['payment_method'] ?? '',
                    'comment'        => $t['comment'] ?? '',
                    'date'           => $t['date'] ?? date('Y-m-d'),
                ]);
            }
            delete_user_meta($user->ID, 'transactions'); // optional cleanup
        }
    }
}

}
