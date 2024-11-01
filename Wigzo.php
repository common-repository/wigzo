<?php
/*
    Plugin Name: Wigzo
    Plugin URI: http://wordpress.org/plugins/wigzo
    Description: Implements the required tagging blocks for using Wigzo marketing automation service.
    Author: Wigzo Pte Ltd
    Version: 3.1.6
    License: GPLv2
*/

/*  Copyright 2016 Wigzo Pte Ltd  (email : PLUGIN AUTHOR EMAIL)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Main plugin class.
 *
 * @package Wigzo
 * @since   1.0.0
 */
define('WIGZO_VERSION', '3.1.6');
include_once ("lib.php");
include_once ("auth.php");
// include_once ("oauth/oauth_helpers.php.inc");
include_once (ABSPATH . 'wp-content/plugins/wigzo/common.php' );

add_action ('admin_menu','wigzo_admin_actions');                // Using Admin menu adds a submenu
add_action ('wp_head','wigzo_add_wigzo_script');
add_action ('template_redirect','wigzo_gcm_manifest');
//add_action( 'transition_post_status', 'wigzo_add_product',10,3 );
add_action( 'updated_post_meta', 'wigzo_product_published', 10, 3);
add_action( 'save_post', 'wigzo_after_update' );
add_action( 'woocommerce_thankyou', 'wigzo_payment_checkout'); // On proceed to checkout button
// add_action( 'woocommerce_after_shop_loop_item', 'wigzo_after_add_to_cart' );
//add_action( 'woocommerce_after_single_product', 'wigzo_product_view');
//add_action( "woocommerce_order_status_pending", "payment_checkout");// On Place order button
//add_action('woocommerce_add_to_cart', 'wigzo_after_add_to_cart');

// Post Purchase 
include_once('post_purchase.php'); 
add_action('woocommerce_order_status_changed', 'wigzo_store_order_update_event', 10, 1);
include_once (ABSPATH . 'wp-content/plugins/wigzo/custom_order_status.php');

function wigzo_gcm_manifest() {
    $fname = "gcm_manifest.json";
    if (substr_compare ($_SERVER['REQUEST_URI'], $fname, strlen ($_SERVER["REQUEST_URI"]) - strlen ($fname), strlen ($fname)) === 0) {
		
		header ("Content-type: application/json", true, 200);
		

        $pwa= get_option("wigzo_pwa");
        $gcm_data = array();
        
        $gcm_data["display"] = "standalone";
        $gcm_data["gcm_sender_id"]="446212695181";
        $gcm_data["gcm_user_visible_only"]=true;
		
		//$gcm_data["start_url"]="https://superman.wigzopush.com/index.php";
        $gcm_data["background_color"]='#3E4EB8'; 
        $gcm_data["theme_color"]='#2F3BA2';

        if($pwa)
        {
            $pwa_data = explode(",", get_option("wigzo_pwaData"));
            $pwa_bg_color = $pwa_data[0];
            $pwa_theme_color = $pwa_data[1];
            $pwa_web_name = $pwa_data[2];
            $pwa_short_name = $pwa_data[3];
            $pwa_icon_path = $pwa_data[4];

            $gcm_data["start_url"] = "/index.php";
            $gcm_data["background_color"] = $pwa_bg_color; 
            $gcm_data["theme_color"] = $pwa_theme_color;
			$gcm_data["name"] = $pwa_web_name;
	        $gcm_data["short_name"] = $pwa_short_name;

            $icon_128 = array("src" => $pwa_icon_path, "sizes" => "128x128", "type" => "image/png");
            $icon_144 = array("src" => $pwa_icon_path, "sizes" => "144x144", "type" => "image/png");
            $icon_152 = array("src" => $pwa_icon_path, "sizes" => "152x152", "type" => "image/png");
            $icon_192 = array("src" => $pwa_icon_path, "sizes" => "192x192", "type" => "image/png");
            $icon_256 = array("src" => $pwa_icon_path, "sizes" => "256x256", "type" => "image/png");

            $gcm_data["icons"]= array(0 => $icon_128, 1 => $icon_144, 2 => $icon_152, 3 => $icon_192, 4 => $icon_256);
        }

		else{
			$gcm_data["name"] = "Wigzo Chrome Push Service";
    	    $gcm_data["short_name"] = "Wigzo Push";
		}


        echo json_encode ($gcm_data);
        exit (0);             
    }

    $swname = '/gcm_service_worker.js';
    if (strstr ($_SERVER['REQUEST_URI'], $swname)) {	
        include_once ("gcm_serviceworker.php");
        die ();
    }
}

function wigzo_admin_actions () {
    //add_options_page add submenu to the settings menu.//
    // 1st argument is the Title of the page
    // 2nd argument is the name of the submenu as it would be displayed in the deskboard
    // 3rd argument is the capability and look and view of submenu, this means that administrators can view this page only
    //4th Menu slug gives  full power to the file name
    //5th function name which would display the menu page

    if (($_SERVER['REQUEST_METHOD'] === 'POST') && (isset($_REQUEST['nonce']))) {
        /* User has submitted the OAuth Keys form */

        /* Lets validate the noonce first */
        //$noonce_valid = check_admin_referer ("wigzo-otp");
		
        $nonce = $_REQUEST['nonce'];
		if ( ! wp_verify_nonce( $nonce, 'wigzo-otp' ) ) {
			// This nonce is not valid.
			//echo "<center>No once ---->".$nonce."</center>";
			//die( __( 'Security check', 'textdomain' ) ); 
		} else {

        $clientid = sanitize_text_field ($_POST['clientid']);
        $clientsecret = sanitize_text_field ($_POST['clientsecret']);
		$orgToken = sanitize_text_field ($_POST['orgToken']);
        // $redirecturi = esc_url ($_POST['redirecturi']);

        if(!$clientid || !$clientsecret)
        {
                include_once ("wigzo_adminjs.php");
                die ();
        }
        wigzo_setoption ("wigzo_client_id", $clientid);
        wigzo_setoption ("wigzo_client_secret", $clientsecret);
		wigzo_setoption ("wigzo_org_token", $orgToken);

        // $host = wigzo_getWigzoHost ();

        // header ("Location: $host/oauth/v2/authorize?client_id=".$clientid."&client_secret=".$clientsecret."&redirect_uri=".$redirecturi);
        // die ();
		}
	}
    // if (isset ($_GET["code"])) {
    //     /* This is a call from wigzo with our OAuth Code */
    //     $scode = sanitize_text_field($_GET["code"]);

    //     wigzo_convert_to_longlived ($scode);    /* Need to convert to OAuth Long Lived Token, required only once per Authorization */
    //     wigzo_resyncoptions ();

    //     $admin_url = admin_url ();
    //     header ("Location: $admin_url" . "options-general.php?page=wigzo");
    //     die ();
    // }

    // if (isset ($_GET["update"])) {
    //     wigzo_resyncoptions();

    //     $admin_url = admin_url ();
    //     header("Location: $admin_url" . "options-general.php?page=wigzo");
    //     die ();
    // }

    // if (isset ($_GET["action"])) {
    //     if ($_GET['action'] == "wremove") {
    //         delete_option ("wigzo_oauth");

    //         $admin_url = admin_url ();
    //         header("Location: $admin_url" . "options-general.php?page=wigzo");
    //         die ();
    //     }
    // }
    add_options_page ('Wigzo','Wigzo', 'manage_options', "wigzo", 'wigzo_admin');
}

function wigzo_admin () {
	global $wp_current_filter; //Tells us the current page viewed

	$page = $wp_current_filter[0];

   	 /*wigzo_include_wigzo_lib ();*/
     include_once ("wigzo_adminjs.php");
}

function wigzo_add_wigzo_script () {
    include_once ("head.php");
}
function wigzo_add_product($new_status,$old_status,$post){
	//include_once("add_product.php");
	include_once("update_product.php");
	wigzo_update_product($post_id);
}
function wigzo_product_published($post_id)
{
    /*wigzo_include_wigzo_lib ();*/
	include_once("update_product.php");
	wigzo_update_product($post_id);
}
function wigzo_after_update($post_id)
{
	include_once("update_product.php");
}
function wigzo_payment_checkout($order_id){
	include_once("product_buyEvent.php");
	wigzo_buyEvent($order_id);
	
}
// function wigzo_after_add_to_cart($post_id){
// 	include_once("addtocart_product.php");
// 	wigzo_add_to_cart($post_id);
// }

require_once('track_registerAndCheckout.php');

add_action('wp_footer','wigzo_add_to_cart_script');
function wigzo_add_to_cart_script(){
    // if ( is_shop() || is_product_category() || is_product_tag() ):
        ?>
            <script type="text/javascript">
                (function($){ 

                    $( document.body ).on( 'added_to_cart', function(event, fragments, cart_hash, button){
                        const productID = parseInt( $(button[0]).data('product_id') ).toString();
                        wigzo("track", "addtocart", productID);
                    });

                })(jQuery);
            </script>
        <?php
		global $wp;
        $url = add_query_arg( $wp->query_vars, home_url( $wp->request ) );
        if ( is_search() && (strpos($url,'post_type=product') !== false) ) {
            $search_term = $_GET['s'];
            if ($search_term != '') {
                echo '<script type="text/javascript">';
                echo '(function($){';
                echo 'wigzo ("track", "search", "'.$search_term.'");';
                echo '})(jQuery);';
                echo '</script>';
            }
        }
    // endif;
}
 
function wigzo_plugin_admin_notice() {
	//get the current screen
	$screen = get_current_screen();

	//return if not plugin settings page 
	//To get the exact your screen ID just do var_dump($screen)
    // var_dump($screen);
	if ( $screen->id !== 'settings_page_wigzo') return;
		
	//Checks if settings updated 
	if ( isset( $_POST['submit'] ) ) {
		//if settings updated successfully 
		if ( 'Submit' === $_POST['submit'] ) : ?>

			<div class="notice notice-success is-dismissible">
				<p><?php _e('Congratulations! You did a good job.', 'textdomain') ?></p>
			</div>

		<?php else : ?>

			<div class="notice notice-warning is-dismissible">
				<p><?php _e('Sorry, I can not go through this.', 'textdomain') ?></p>
			</div>
			
		<?php endif;
	}
}
add_action( 'admin_notices', 'wigzo_plugin_admin_notice' );

function wigzo_get_cart_total_amount() {
	global $woocommerce;
	$cart = WC()->cart;
	$cart->calculate_totals();
	$cart_total_amount = wc_prices_include_tax() ? $cart->get_cart_contents_total() + $cart->get_cart_contents_tax() : $cart->get_cart_contents_total();
	// $cart_total_amount = $cart->get_total('');
	if ( $cart_total_amount > 0 ) {
		
		$org_token = get_option( 'wigzo_org_token' );
		$cookie_id = '';
		if ( isset( $_COOKIE['WIGZO_LEARNER_ID'] ) ) {
			$cookie_id = sanitize_text_field( wp_unslash( $_COOKIE['WIGZO_LEARNER_ID'] ) );
		}
		if ( isset( $cookie_id ) && ! empty( $cookie_id ) ) :

			$endpoint = 'https://app.wigzo.com/rest/v1/learn/event?org_token=' . $org_token;

			$body = array(
				"eventName" => 'cart_value',
				"eventval" => (string) $cart_total_amount,
				"eventCategory" => "EXTERNAL",
				"userId" => (string) $cookie_id,
				"is_active" => true,
				"source" => "web"
			);

			$body = wp_json_encode( $body );

			$options = array(
				'body'        => $body,
				'headers'     => array(
					'Content-Type' => 'application/json',
				),
				'method'      => 'POST',
				'timeout'     => 60,
				'redirection' => 5,
				'blocking'    => true,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'data_format' => 'body',
			);

			$response = wp_remote_post( esc_url_raw( $endpoint ), $options );
			if ( is_wp_error( $response ) ) :
				error_log( print_r( $response->get_error_message(), true ) );
			endif;
		endif;
	}
}
add_action( 'woocommerce_add_to_cart', 'wigzo_get_cart_total_amount' );
add_action( 'woocommerce_cart_item_removed', 'wigzo_get_cart_total_amount' );
add_action( 'woocommerce_update_cart_action_cart_updated', 'wigzo_get_cart_total_amount' );

function wigzo_single_product_add_to_cart($cart_item_key, $product_id, $quantity, $variation_id, $variation, $cart_item_data) {
	global $woocommerce;
	// $productId = $product_id;
	$product_url = get_permalink($product_id);
	if ( $product_url != '' ) {
		
		$org_token = get_option( 'wigzo_org_token' );
		$cookie_id = '';
		if ( isset( $_COOKIE['WIGZO_LEARNER_ID'] ) ) {
			$cookie_id = sanitize_text_field( wp_unslash( $_COOKIE['WIGZO_LEARNER_ID'] ) );
		}
		if ( isset( $cookie_id ) && ! empty( $cookie_id ) ) :

			$endpoint = 'https://app.wigzo.com/rest/v1/learn/event?org_token=' . $org_token;

			$body = array(
				"eventName" => 'addtocart',
				"eventval" => (string) $product_url,
				"eventCategory" => "EXTERNAL",
				"userId" => (string) $cookie_id,
				"is_active" => true,
				"source" => "web"
			);

			$body = wp_json_encode( $body );

			$options = array(
				'body'        => $body,
				'headers'     => array(
					'Content-Type' => 'application/json',
				),
				'method'      => 'POST',
				'timeout'     => 60,
				'redirection' => 5,
				'blocking'    => true,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'data_format' => 'body',
			);

			$response = wp_remote_post( esc_url_raw( $endpoint ), $options );
			if ( is_wp_error( $response ) ) :
				error_log( print_r( $response->get_error_message(), true ) );
			endif;
		endif;
	}
}
add_action( 'woocommerce_add_to_cart', 'wigzo_single_product_add_to_cart', 10, 6);