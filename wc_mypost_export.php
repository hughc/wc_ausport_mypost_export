<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://highbrow.com.au/
 * @since             1.0.0
 * @package           Wc_mypost_export
 *
 * @wordpress-plugin
 * Plugin Name:       WooCommerce Ausport MyPost CSV Export
 * Plugin URI:        https://github.com/hughc/wc_auspost_mypost_export
 * Description:       Adds options to Orders generate MyPost CSV-ready data.
 * Version:           1.0.0
 * Author:            Hugh Campbell
 * Author URI:        https://highbrow.com.au/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wc_mypost_export
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );


/*
Additional Label Information 1
Send Tracking Notifications
Send From Name
Send From Business Name
Send From Address Line 1
Send From Address Line 2
Send From Address Line 3
Send From Suburb
Send From State
Send From Postcode
Send From Phone Number
Send From Email Address
Deliver To Name
Deliver To MyPost Number
Deliver To Business Name
Deliver To Type Of Address
Deliver To Address Line 1
Deliver To Address Line 2
Deliver To Address Line 3
Deliver To Suburb
Deliver To State
Deliver To Postcode
Deliver To Phone Number
Deliver To Email Address
Item Packaging Type
Item Delivery Service
Item Description
Item Length
Item Width
Item Height
Item Weight
Item Dangerous Goods Flag
Signature On Delivery
Extra Cover Amount
*/

/**
 * 
 */


class MyPostExportFields
{
	
	private $sender_fields;


	public static $_instance;
	static $inst = null;

	public static function getInstance() {
		if (self::$inst === null) {
				self::$inst = new MyPostExportFields();
		}
		return self::$inst;
	}


	function __construct()
	{
		$this->sender_fields= array(
			"send_from_name" => array( 'label' => 'Send From Business Name', 'colname' =>'Send From Business Name', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => "get_store_name"),

		"send_from_address_1" => array( 'label' => 'Send From Address', 'colname' =>'Send From Address', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => 'get_address_line_1'),

		"send_from_address_2" => array( 'label' => 'Send From Address 2', 'colname' =>'Send From Address 2', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => 'get_address_line_2'),

		"send_from_address_3" => array( 'label' => 'Send From Address 3', 'colname' =>'Send From Address 3', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => $this->gen_field_value('')),

		"send_from_suburb" => array( 'label' => 'Send From Suburb', 'colname' =>'Send From Suburb', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => "get_address_city" ),

		"send_from_state" => array( 'label' => 'Send From State', 'colname' =>'Send From State', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => "get_address_state" ),

		"send_from_postcode" => array( 'label' => 'Send From Postcode', 'colname' =>'Send From Postcode', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => "get_address_postcode" ),

		"item_weight" => array( 'label' => 'Item Weight', 'colname' =>'Item Weight', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => "get_total_weight" ),

		"send_tracking_notifications" => array( 'label' => 'Send Tracking Notifications', 'colname' =>'Send Tracking Notifications', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => $this->gen_field_value("YES")),	
		
		"send_email_address" => array( 'label' => 'Send From Email Address', 'colname' =>'Send From Email Address
', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => "get_store_email"),
		
		"box_width" => array( 'label' => 'Item Width', 'colname' =>'Item Width', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => "get_box_width"),
		"box_height" => array( 'label' => 'Item Height', 'colname' =>'Item Height', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => "get_box_height"),
		"box_length" => array( 'label' => 'Item Length', 'colname' =>'Item Length', 'segment' => 'other', 'format'=>'string', 'checked' => 1, 'action' => "get_box_depth"),
		);


		add_filter('woe_get_order_fields', array($this, 'add_fields'));

		foreach ($this->sender_fields as $field_id => $field_data) {
			if (is_callable($field_data['action'])) {
				add_filter('woe_get_order_value_' . $field_id, $field_data['action'],10, 3);
			} else {
				add_filter('woe_get_order_value_' . $field_id, array($this, $field_data['action']),10, 3);
				
			}
		}

		// custom shipping information metaboxes
		add_action( 'add_meta_boxes', array($this, 'add_order_meta_boxes'));
		add_action( 'save_post', array($this, 'save_order_fields'), 10, 1 );

	//	add_filter( 'woocommerce_get_sections_mypost', array($this, 'add_settings_section'));
	//	add_filter( 'woocommerce_get_settings_mypost', array($this, 'draw_settings'), 10, 2);
	  add_filter( 'woocommerce_settings_tabs_array', array($this, 'add_settings_tab'), 50 );
	  add_action( 'woocommerce_settings_tabs_mypost', array($this, 'get_settings_tab'), 50 );
	  add_action( 'woocommerce_update_options_mypost', array($this, 'update_settings'));
    }
    public function add_settings_tab( $settings_tabs ) {
        $settings_tabs['mypost'] = __( 'MyPost Export', 'woocommerce-settings-tab-demo' );
        return $settings_tabs;
    }
    function get_settings_tab() {
	    woocommerce_admin_fields( $this->get_settings() );
	}
	function update_settings() {
	    woocommerce_update_options( $this->get_settings() );
	}

	// after https://www.speakinginbytes.com/2014/07/woocommerce-settings-tab/

	public function add_settings_section( $sections ) {
		$sections['mypost_export'] = __( 'MyPost CSV Export', 'text-domain' );
		return $sections;
	
	}

	/**
	 * Add settings to the specific section we created before
	 */
	function get_settings( ) {
		
			$mypost_settings = array();
			// Add Title to the Settings
			$mypost_settings[] = array(
				'name' => __( 'General Settings', 'text-domain' ),
				'type' => 'title',
				'id' => 'mypost_name_etc'
			);

			// Add second text field option
			$mypost_settings[] = array(
				'name'     => __( 'Send From Name', 'text-domain' ),
				'id'       => 'mypost_send_from_name',
				'type'     => 'text',
				'desc' => __( 'Usually your store name.', 'text-domain')
			);
// Add Title to the Settings

			$mypost_settings[] = array( 'type' => 'sectionend', 'id' => 'mypost_name_etc' );
			$mypost_settings[] = array(
				'name' => __( 'Default Dimensions', 'text-domain' ),
				'type' => 'title',
				'desc' => __( 'If using your own packaging, the default dimensions for a package', 'text-domain' ), 'id' => 'mypost_export' );

			// Add second text field option
			$mypost_settings[] = array(
				'name'     => __( 'width (cm)', 'text-domain' ),
				'id'       => 'mypost_default_width',
				'type'     => 'text',
				'css'     => 'width: 50px;',
			);
			// Add second text field option
			$mypost_settings[] = array(
				'name'     => __( 'height (cm)', 'text-domain' ),
				'id'       => 'mypost_default_height',
				'type'     => 'text',
				'css'     => 'width: 50px;',
			);
			// Add second text field option
			$mypost_settings[] = array(
				'name'     => __( 'depth (cm)', 'text-domain' ),
				'id'       => 'mypost_default_depth',
				'type'     => 'text',
				'css'     => 'width: 50px;',
			);
			
			$mypost_settings[] = array( 'type' => 'sectionend', 'id' => 'mypost_export' );
			return apply_filters( 'wc_settings_tab_mypost_settings', $mypost_settings);
		
	}

	// after https://stackoverflow.com/questions/37772912/woocommerce-add-custom-metabox-to-admin-order-page

	
    function add_order_meta_boxes()
    {
        add_meta_box( 'mv_other_fields', __('MyPost Options','woocommerce'), array($this, 'mv_add_other_fields_for_packaging'), 'shop_order', 'side', 'core' );
    }

    function mv_add_other_fields_for_packaging()
    {
        global $post;
        $order = new WC_Order($post->ID);

        $parcel_type = get_post_meta( $post->ID, '_mypost_parcel_type', true ) ? get_post_meta( $post->ID, '_mypost_parcel_type', true ) : '';
        $delivery_service = get_post_meta( $post->ID, '_mypost_delivery_service', true ) ? get_post_meta( $post->ID, '_mypost_delivery_service', true ) : '';
        // dimensions
        $mypost_package_width = get_post_meta( $post->ID, '_mypost_package_width', true ) ? get_post_meta( $post->ID, '_mypost_package_width', true ) : get_option( 'mypost_default_width' );
        $mypost_package_height = get_post_meta( $post->ID, '_mypost_package_height', true ) ? get_post_meta( $post->ID, '_mypost_package_height', true ) : get_option( 'mypost_default_height' );
        $mypost_package_depth = get_post_meta( $post->ID, '_mypost_package_depth', true ) ? get_post_meta( $post->ID, '_mypost_package_depth', true ) : get_option( 'mypost_default_depth' );
        $mypost_package_weight = get_post_meta( $post->ID, '_mypost_package_weight', true ) ? get_post_meta( $post->ID, '_mypost_package_weight', true ) : $this->gather_total_weight($order);
        ?>
        <input type="hidden" name="mv_other_meta_field_nonce" value="<?php echo wp_create_nonce(); ?>">
        <p style="border-bottom:solid 1px #eee;padding-bottom:13px;">
        	<label for="mypost_parcel_type">Packaging Type</label>
            <select name="mypost_parcel_type">
					<option value="">-- please choose --</option>
					<option value="OWN_PACKAGING" <?php if($parcel_type == "OWN_PACKAGING") echo " selected";?>>Own Box</option>
					<option value="AP_SATCHEL" <?php if($parcel_type == "AP_SATCHEL") echo " selected";?>>AusPost Satchel</option>
			</select>
		</p>
		<p style="border-bottom:solid 1px #eee;padding-bottom:13px;">
        	<label for="mypost_delivery_service">Delivery Service</label>
            <select name="mypost_delivery_service">
					<option value="">-- please choose --</option>
					<option value="PP" <?php if($delivery_service == "PP") echo " selected";?>>Parcel Post</option>
					<option value="EXP" <?php if($delivery_service == "EXP") echo " selected";?>>Express Post</option>
			</select>
		</p>
		<p style="border-bottom:solid 1px #eee;padding-bottom:13px;">
        	<label for="mypost_package_weight"  style="display: inline-block; width:80px;">Weight (kg):</label>
            <input type="text" name="mypost_package_weight" placeholder="<?php echo $mypost_package_weight ; ?>" value="<?php echo $mypost_package_weight ; ?>">
            <br>
		</p>
		<p>
			<strong>Dimensions (if using Our Box packaging)</strong><br>
        	<label for="mypost_package_width"  style="display: inline-block; width:80px;">Width (cm):</label>
            <input type="text" name="mypost_package_width" placeholder="<?php echo $mypost_package_width ; ?>" value="<?php echo $mypost_package_width ; ?>">
            <br>
        	<label for="mypost_package_height"  style="display: inline-block; width:80px;">Height (cm):</label>
            <input type="text" name="mypost_package_height" placeholder="<?php echo $mypost_package_height ; ?>" value="<?php echo $mypost_package_height ; ?>">
            <br>
        	<label for="mypost_package_depth"  style="display: inline-block; width:80px;">Depth (cm):</label>
            <input type="text" name="mypost_package_depth" placeholder="<?php echo $mypost_package_depth ; ?>" value="<?php echo $mypost_package_depth ; ?>">
            <br>
        </p>
           <?php

    }

    function gather_total_weight($order) {

	    $total_weight = 0;

	    foreach( $order->get_items() as $item_id => $product_item ){
	        $quantity = $product_item->get_quantity(); // get quantity
	        $product = $product_item->get_product(); // get the WC_Product object
	        $product_weight = $product->get_weight(); // get the product weight
	        // Add the line item weight to the total weight calculation
	        $total_weight += floatval( $product_weight * $quantity );
	    }

	    return $total_weight;
    }

// Save the data of the Meta field

    function save_order_fields( $post_id ) {

        // We need to verify this with the proper authorization (security stuff).
        // Check if our nonce is set.
        if ( ! isset( $_POST[ 'mv_other_meta_field_nonce' ] ) ) {
            return $post_id;
        }
        $nonce = $_REQUEST[ 'mv_other_meta_field_nonce' ];

        //Verify that the nonce is valid.
        if ( ! wp_verify_nonce( $nonce ) ) {
            return $post_id;
        }

        // If this is an autosave, our form has not been submitted, so we don't want to do anything.
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post_id;
        }

        // Check the user's permissions.
        if ( 'page' == $_POST[ 'post_type' ] ) {

            if ( ! current_user_can( 'edit_page', $post_id ) ) {
                return $post_id;
            }
        } else {

            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return $post_id;
            }
        }
        // --- Its safe for us to save the data ! --- //

        // Sanitize user input  and update the meta field in the database.
        if (isset( $_POST[ 'mypost_parcel_type' ] ) ) {
	        update_post_meta( $post_id, '_mypost_parcel_type', $_POST[ 'mypost_parcel_type' ] );
	    }
        // Sanitize user input  and update the meta field in the database.
        if (isset( $_POST[ 'mypost_delivery_service' ] ) ) {
	        update_post_meta( $post_id, '_mypost_delivery_service', $_POST[ 'mypost_delivery_service' ] );
    	}
        update_post_meta( $post_id, '_mypost_package_height', $_POST[ 'mypost_package_height' ] );
        update_post_meta( $post_id, '_mypost_package_width', $_POST[ 'mypost_package_width' ] );
        update_post_meta( $post_id, '_mypost_package_depth', $_POST[ 'mypost_package_depth' ] );
        update_post_meta( $post_id, '_mypost_package_weight', $_POST[ 'mypost_package_weight' ] );
    }


	function gen_field_value($passed_value) {
		return function($value,$order,$fieldname) use ($passed_value) {
			return $passed_value;
		};
	}

	function add_fields($fields) {
		foreach ($this->sender_fields as $field_id => $field_data) {
			$fields[$field_id] = $field_data;
		}
		return $fields;
	}

	function get_store_name($value,$order,$fieldname) {
	  return get_option("mypost_send_from_name");
	}
	
	function get_total_weight($value,$order,$fieldname) {
		$saved_weight = $order->get_meta("_mypost_package_weight");
		if ($saved_weight) {
			return $saved_weight;
		} else {
	  		return $this->gather_total_weight($order);
		}
	}

	function get_store_email($value,$order,$fieldname) {
	  return get_option("woocommerce_email_from_address");
	}
	function get_address_line_1($value,$order,$fieldname) {
	  return get_option("woocommerce_store_address");
	}


	function get_address_line_2($value,$order,$fieldname) {
	  return get_option("woocommerce_store_address_2");
	}

	function get_address_city($value,$order,$fieldname) {
		$country = new WC_Countries();
	  return $country->get_base_city( );
	}
	function get_address_postcode($value,$order,$fieldname) {
	  $country = new WC_Countries();
	  return $country->get_base_postcode( );
	}
	function get_address_state($value,$order,$fieldname) {
	  $country = new WC_Countries();
	  return $country->get_base_state( );
	}


	function get_box_width($value,$order,$fieldname) {
		$package_type = $order->get_meta("_mypost_parcel_type");
		if ($package_type == "OWN_PACKAGING") return $order->get_meta("_mypost_package_width");
		return "";
	}

	function get_box_height($value,$order,$fieldname) {
		$package_type = $order->get_meta("_mypost_parcel_type");
		if ($package_type == "OWN_PACKAGING") return $order->get_meta("_mypost_package_height");
		return "";
	}

	function get_box_depth($value,$order,$fieldname) {
		$package_type = $order->get_meta("_mypost_parcel_type");
		if ($package_type == "OWN_PACKAGING") return $order->get_meta("_mypost_package_depth");
		return "";
	}

}



	$instance = MyPostExportFields:: getInstance();