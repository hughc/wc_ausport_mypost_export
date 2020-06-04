<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://highbrow.com.au/
 * @since      1.0.0
 *
 * @package    Wc_mypost_export
 * @subpackage Wc_mypost_export/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_mypost_export
 * @subpackage Wc_mypost_export/admin
 * @author     Hugh Campbell <hc@highbrow.com.au>
 */
class Wc_mypost_export_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		//	add_action( 'woocommerce_admin_order_actions_end', array( $this, 'add_listing_actions' ) );


	}


/**
	 * Add PDF actions to the orders listing
	 */
	public function add_listing_actions( $order ) {
		// do not show buttons for trashed orders
		if ( $order->get_status() == 'trash' ) {
			return;
		}

		$listing_actions = array();
		$listing_actions['mypost_export'] = array(
			'url'		=> wp_nonce_url( admin_url( "admin-ajax.php?action=generate_mypost_export&order_ids=" . WCX_Order::get_id( $order ) ), 'generate_mypost_export' ),
			'alt'		=> "PDF " . $document->get_title(),
			'img'		=> ""
		);

		$listing_actions = apply_filters( 'wpo_wcpdf_listing_actions', $listing_actions, $order );			

		foreach ($listing_actions as $action => $data) {
			?>
			<a href="<?php echo $data['url']; ?>" class="button tips wpo_wcpdf <?php echo $action; ?>" target="_blank" alt="<?php echo $data['alt']; ?>" data-tip="<?php echo $data['alt']; ?>">
				<img src="<?php echo $data['img']; ?>" alt="<?php echo $data['alt']; ?>" width="16">
			</a>
			<?php
		}
	}
	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_mypost_export_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_mypost_export_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc_mypost_export-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_mypost_export_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_mypost_export_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc_mypost_export-admin.js', array( 'jquery' ), $this->version, false );

	}

}
