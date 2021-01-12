<?php

/**
* Plugin Name: Lístenka Pohoda export
* Plugin URI: https://www.ticketa.cz
* Description: Exportování faktur při každé objednávce po zaplacení
* Version: 1
* Author: Ticketa
* Author URI: https://ticketa.cz/
* Developer: Ticketa
* Developer URI: https://ticketa.cz/
*/

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

define('LISTENKAPOH_URL', plugin_dir_url( __FILE__ ) );
define('LISTENKAPOH_PATH', plugin_dir_path( __FILE__ ) );

//// includes ////

include_once ( LISTENKAPOH_PATH . '/includes/vytvorit-fakturu-zakaznik.php' );

// woocommerce_payment_complete

add_action( 'woocommerce_order_status_completed', 'exportuj_fakturu_zakaznik', 10, 3 );

//// menu ////

function remove_invoice_menu_parts() {
	
	$thisuser = wp_get_current_user();
	if ( !in_array( 'administrator', (array) $thisuser->roles ) ) {
		remove_submenu_page( 'edit.php?post_type=monthly_invoice', 'edit.php?post_type=monthly_invoice' );
		remove_submenu_page( 'edit.php?post_type=monthly_invoice', 'post-new.php?post_type=monthly_invoice' );
	}

}
add_action( 'admin_menu', 'remove_invoice_menu_parts' );


//// jazyk ////

function lstpoh_localisation() {
    $plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages';
    load_plugin_textdomain( 'lstpoh', false, $plugin_rel_path );
}
add_action('plugins_loaded', 'lstpoh_localisation');


?>