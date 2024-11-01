<?php
if ( ! defined( 'ABSPATH' ) ) exit;

include_once ('lib.php');
function wigzo_update_product($postid){
		//global $wp_current_filter;
		global $wp_current_filter;
		global $woocommerce;
		$page =$wp_current_filter[0];
		
		$page =$wp_current_filter[0];
		
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
		//plugin is activated

			$org_id = get_option ("wigzo_orgId");
			$org_token = get_option ("wigzo_token");
			$wigzo_host = wigzo_getWigzoHost ();


			$postid = get_the_ID();
			if ($postid != NULL && $org_id != NULL && $org_token != NULL && $wigzo_host != NULL){
				$postdata = array ();
				$postdata["productId"] = $postid;

				$regular_price_meta = get_post_meta( $postid, '_regular_price');
				if($regular_price_meta != NULL)
				{
					$price = $regular_price_meta[0];
					$postdata["price"] = $price;
				}
				$product_cats = wp_get_post_terms( $postid, 'product_cat' );
				if($product_cats != NULL)
				{
					$categoryname =$product_cats[0]->name;
				}
				$product_instance = wc_get_product($postid);
				if($product_instance != NULL)
				{
					$product_title = $product_instance->post_title;
					$postdata["title"] = $product_title;
                    $postdata["description"] = $product_instance->get_description();
                    $url = $product_instance->get_permalink();
					if ($url != NULL){
						$postdata["canonical"] = $url;
					}
				}

				$uri = "$wigzo_host/api/v1/product/$org_token";

				//print_r($postdata);
				$ch = curl_init ();
				curl_setopt ($ch, CURLOPT_URL,"$wigzo_host/api/v1/product/$org_id");
				curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
				curl_setopt ($ch, CURLOPT_POST, 1);
				curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode ($postdata));
				curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
				curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);

				$server_output = curl_exec ($ch);

				//$error = curl_error ($ch);
				$httpCode = curl_getinfo ($ch, CURLINFO_HTTP_CODE);
				curl_close ($ch);

			}
		}
}