<?php
/**
 * Installer class
 */

namespace Hashbarfree\Analytics;

class Database_Installer {

    /**
     * [create_tables]
     * @return [void]
    */
    public static function create_tables() {
        global $wpdb;
        $analytics_table_exist =get_option( 'hthb_analyticstbl_exist', $default = false );

        if($analytics_table_exist !== false) return; 

        $table_name      = $wpdb->prefix .'hthb_analytics';       

        $charset_collate = $wpdb-> get_charset_collate();

        $schema = "
        CREATE TABLE $table_name (
        id bigint(9) NOT NULL AUTO_INCREMENT,
        post_id bigint(55) DEFAULT NULL,
        views bigint(55) DEFAULT NULL,
        clicks bigint(55) DEFAULT NULL,
        ip_address varchar(255) DEFAULT NULL,
        country varchar(255) DEFAULT NULL,
        created_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        updated_date datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY  (id)
        ) $charset_collate;
        ";

        if ( ! function_exists( 'dbDelta' ) ) {
            require_once ABSPATH . 'wp-admin/includes/upgrade.php';
        }

        dbDelta( $schema );

        update_option( 'hthb_analyticstbl_exist', 'true');
    }

    /**
     * [drop_tables] Delete table
     * @return [void]
    */
    public static function drop_tables() {
        global $wpdb;
        $table = $wpdb->prefix .'hthb_analytics';
        $wpdb->query( "DROP TABLE IF EXISTS {$table}" );
    }
}