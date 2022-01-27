<?php

use \Hashbarfree\Analytics\Database_Installer as DeleteTable;

/*
 * HT Google Place Review unstall plugin
*/

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) exit; // Exit if accessed directly

include_once dirname( __FILE__ ) . '/inc/database-installer.php';

function hashbar_uninstall(){
	//Delete Table
	DeleteTable::drop_tables();
}
hashbar_uninstall();
delete_option('hthb_analyticstbl_exist');