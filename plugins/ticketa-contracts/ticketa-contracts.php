<?php
/*
* Plugin Name: Ticketa Contracts
* Plugin URI: https://www.ticketa.cz
* Description: Ticketa - vytvareni smluv umelcu
* Version: 1
* Author: Ticketa
* Author URI: https://www.ticketa.cz/
* Developer: Ticketa
* Developer URI: https://www.ticketa.cz/
* Text Domain: tcct
*/

//define( 'WP_DEBUG', true );

if ( !defined( 'ABSPATH' ) ) {
    exit;
}

define('TICKETACONTR_URL', plugin_dir_url( __FILE__ ) );
define('TICKETACONTR_PATH', plugin_dir_path( __FILE__ ) );

//// init ////
function init_ticketa_contracts() {
	
	include ( TICKETACONTR_PATH . "/includes/location-list.php");
	include ( TICKETACONTR_PATH . "/includes/contract-cpt.php");
	include ( TICKETACONTR_PATH . "/includes/contract-ajax-functions.php");
	include ( TICKETACONTR_PATH . "/includes/contract-shortcodes.php");
	remove_filter('the_content', 'wpautop');   
	
	//// jazyk ////
	$plugin_rel_path = basename( dirname( __FILE__ ) ) . '/languages';
    load_plugin_textdomain( 'tcct', false, $plugin_rel_path );
}
add_action( 'plugins_loaded', 'init_ticketa_contracts');

//// javascript ////
function load_scripts() {
    global $post;

    if( is_page() || is_single() ) {
        switch($post->post_name) {
            case 'smlouva-o-prodeji':
            case 'smlouva-o-spolupraci':
            case 'smlouva-o-pronajmu':
            case 'smlouva-o-technicke':
				$lastmodtimejs = filemtime(TICKETACONTR_URL . 'assets/contracts_form_ajax.js');
				wp_register_script('contracts_form_ajax', TICKETACONTR_URL . 'assets/contracts_form_ajax.js', array( 'jquery' ), date("H:i:s"), true);
   				wp_localize_script( 'contracts_form_ajax', 'contracts_ajax_url', admin_url( 'admin-ajax.php' ) );  
				wp_enqueue_script( 'contracts_form_ajax' );   
                break;
        }
    } 
}
add_action('wp_enqueue_scripts', 'load_scripts');
?>