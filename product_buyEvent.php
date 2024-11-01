<?php

if ( ! defined( 'ABSPATH' ) ) exit;
global $wp_current_filter;
global $woocommerce;
$page =$wp_current_filter[0];
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once( ABSPATH . 'wp-content/plugins/wigzo/common.php' );
function wigzo_buyEvent ($order_id) {
    if (is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
        if( is_wc_endpoint_url( 'order-received' ) ) {
            $order = wc_get_order( $order_id );

            // This is the order total
            $total_amount = $order->get_total();

            // This is how to grab line items from the order 
            $line_items = $order->get_items();
            // This loops over line items
            $j= 0;
            $arr_cart = array();
            foreach ( $line_items as $item ) {
                $arr_cart[$j++] = $item['product_id'];

            }
			
			$order_data = $order->get_data();
            $order_first_name = $order_data['billing']['first_name'];
            $order_last_name = $order_data['billing']['last_name'];
            $order_email = $order_data['billing']['email'];
            $order_phone = $order_data['billing']['phone'];
            $payment_method = $order->get_payment_method();
            
            $order_track_object = array(
                "orderId" => $order_id, 
                "email" => $order_email, 
                "phone" => $order_phone, 
                "fullName" =>  ($order_last_name != '') ? $order_first_name .' '. $order_last_name : $order_first_name, 
                "totalOrderValue" => $total_amount,
                'payment_method' => $payment_method
            );

            echo "<script>";
                // Here, you need to add one or more Wigzo API methods as per your requirement.
            echo 'wigzo("track", "buy", '.array_return_values($arr_cart).');';
			echo 'wigzo("track", "order", '.json_encode($order_track_object).');';
            echo "</script>";
        }
    }
}
?>