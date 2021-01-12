<?php
/**
 * Plugin Name: [Forminator Pro] - Change Upload Path
 * Plugin URI: https://premium.wpmudev.org/
 * Description: This snippet should be changing the Forminator upload path to wp-content/YOUR_PATH. By default it's wp-content/forminator
 * Task: 0/11289012348292/1168134495134017
 * Version: 1.0.0
 * Author: Panos Lyrakis @ WPMUDEV
 * Author URI: https://premium.wpmudev.org/
 * License: GPLv2 or later
 *
 * @package Forminator
 */

if ( ! defined( 'ABSPATH' ) || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	return;
}

if ( ! class_exists( 'WPMUDEV_Forminator_Custom_Uploads' ) ) {

	class WPMUDEV_Forminator_Custom_Uploads { // phpcs:ignore

		/**
		 * The custom path.
		 *
		 * @var string $path_part The path that follows wp_content
		 */
		private $path_part = '/uploads/smlouvy/podepsane/';

		private static $instance = null; // phpcs:ignore

		public static function get_instance() { // phpcs:ignore

			if( is_null( self::$instance ) ) { // phpcs:ignore
				self::$instance = new WPMUDEV_Forminator_Custom_Uploads();
			}
			return self::$instance;
		}

		private function __construct() { // phpcs:ignore

			// For non Ajax submit.
			add_action( 'forminator_custom_form_before_handle_submit', array( $this, 'trigger_set_upload_dir' ) );
			add_action( 'forminator_custom_form_after_handle_submit', array( $this, 'restore_upload_dir' ) );

			// For Ajax submit.
			add_action( 'forminator_custom_form_before_save_entry', array( $this, 'trigger_set_upload_dir' ) );
			add_action( 'forminator_custom_form_after_save_entry', array( $this, 'restore_upload_dir' ) );

		}

		/**
		 * Triggered on both ajax and normal submit.
		 *
		 * @param int $form_id The form ID.
		 */
		public function trigger_set_upload_dir( $form_id ) {
			add_filter( 'upload_dir', array( $this, 'set_upload_dir' ) );
		}

		/**
		 * Set the new upload dir path
		 *
		 * @param array $param The upload dir parameters array.
		 */
		public function set_upload_dir( $param ) {

			$param['path'] = WP_CONTENT_DIR . $this->path_part;
			$param['url']  = WP_CONTENT_URL . $this->path_part;

			return $param;
		}

		/**
		 * Triggered on both ajax and normal submit. Removes the upload_dir filter.
		 *
		 * @param int $form_id The form ID.
		 */
		public function restore_upload_dir( $form_id ) {
			remove_filter( 'upload_dir', array( $this, 'set_upload_dir' ) );
		}

	}

	add_action( 'plugins_loaded', array( 'WPMUDEV_Forminator_Custom_Uploads', 'get_instance' ) );

}