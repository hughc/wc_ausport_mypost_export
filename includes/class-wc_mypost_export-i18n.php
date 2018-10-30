<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://highbrow.com.au/
 * @since      1.0.0
 *
 * @package    Wc_mypost_export
 * @subpackage Wc_mypost_export/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Wc_mypost_export
 * @subpackage Wc_mypost_export/includes
 * @author     Hugh Campbell <hc@highbrow.com.au>
 */
class Wc_mypost_export_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'wc_mypost_export',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
