<?php

if ( ! defined( 'ABSPATH' ) ) exit;
global $wp_current_filter;
global $woocommerce;
$page =$wp_current_filter[0];

$browserpush = get_option ("wigzo_browserpush");

$orgid =esc_html (get_option ("wigzo_org_token"));
$https = get_option ("wigzo_viahttps");
$onsitepush= get_option ("wigzo_onsitepush");
$current_user = wp_get_current_user ();
$um = sanitize_email ($current_user->user_email);
$pwa= get_option("wigzo_pwa");
$org_id = get_option ("wigzo_orgId");
$org_token = get_option ("wigzo_token");
$wigzo_host = wigzo_getWigzoHost();

function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
}
function wigzo_call( $event, $data ){
    echo '<script>';
    echo 'wigzo('.'"'.strval($event).'"'.', '. json_encode( $data ) .')';
    echo '</script>';

}
echo "<meta property='wigzo:version' content='".constant('WIGZO_VERSION')."' />";
echo "\n    <!-- Added by Wigzo Wordpress Plugin -->\n";
if($pwa){
    echo "\n    <!-- PWA ENABLED -->\n";
} else {
    echo "\n    <!-- PWA DISABLED -->\n";
}
$onsitepush_val = $onsitepush ? "true" : "false";
echo <<<EOL
<script>
(function(w,i,g,z,o){
    var a,m;w['WigzoObject']=o;w[o]=w[o]||function(){
    (w[o].q=w[o].q||[]).push(arguments)},w[o].l=1*new Date();w[o].h=z;a=i.createElement(g),
    m=i.getElementsByTagName(g)[0];a.async=1;a.src=z;m.parentNode.insertBefore(a,m)
})(window,document,'script','//app.wigzo.com/wigzo.compressed.js','wigzo');
wigzo ('configure', '$orgid');
</script>
EOL;
echo "\n    <!-- End Wigzo Integration -->\n\n";

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once( ABSPATH . 'wp-content/plugins/wigzo/common.php' );
if (is_plugin_active( 'woocommerce/woocommerce.php' ) && (isset($_COOKIE['WIGZO_LEARNER_ID']))) {

    $postid = get_the_ID();
    $price1 = get_post_meta( $postid, '_regular_price', true);
    $sale = get_post_meta( $postid, '_sale_price');
    $currenturl = get_permalink();
    $amt = WC()->cart->total;
    $cucy = get_woocommerce_currency();
    $cookieID =$_COOKIE['WIGZO_LEARNER_ID'];

    $gcm_path = "/gcm_manifest.json";
    $subpath = "";
    $using_permalinks = get_option ("permalink_structure");
    if (! $using_permalinks) {
        /* We are not using permalinks */
        $gcm_path = "/gcm_manifest.json";
        $subpath = "&subpath=/?";
    }

    if (is_checkout() && ! is_wc_endpoint_url()){
        include_once( ABSPATH . 'wp-content/plugins/wigzo/checkout_started.php' );
    }
    if (is_product_category()){
        include_once( ABSPATH . 'wp-content/plugins/wigzo/category_view.php' );
    }
    if(is_product())
    {
        include_once( ABSPATH . 'wp-content/plugins/wigzo/product_view.php');
        $product = wc_get_product( get_the_ID() );
        $productPrice = (!empty($product->get_sale_price()))?$product->get_sale_price():$product->get_regular_price();
        $product_details = array(
            'canonicalUrl'=> get_permalink(),
            'title'=> $product->get_name(),
            'description'=>  $product->get_description(),
            'price'=> strval($productPrice),
            'prevPrice'=> strval($product->get_regular_price()),
            'productId'=> strval($product->get_id()),
            'image'=> wp_get_attachment_image_url( $product->get_image_id(), 'full'),
            'category'=> get_the_terms( $product->get_id(), 'product_cat' )[0]->name,
            'language'=> 'en'
        );
        $event_name = 'index';

        wigzo_call($event_name, $product_details);

    }
    else{
        ?><meta property="wg:url" content="<?php echo get_permalink(); ?>" /><?php


    }
}
