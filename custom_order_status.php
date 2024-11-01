<?php
if (!defined('ABSPATH')) exit;

// Register custom order statuses
function register_custom_order_statuses_engage() {
    $statuses = [
        'wc-order-dispatched' => 'Order Dispatched',
        'wc-order-out-for-delivery' => 'Out for Delivery',
        'wc-order-delivered' => 'Order Delivered',
        'wc-order-shipped' => 'Order Shipped',
        'wc-order-pickup' => 'Order Pickup',
        'wc-order-pickup-scheduled' => 'Order Pickup Scheduled'
    ];

    foreach ($statuses as $status => $label) {
        register_post_status($status, array(
            'label'                     => _x($label, 'Order status', 'text_domain'),
            'public'                    => true,
            'exclude_from_search'       => false,
            'show_in_admin_all_list'    => true,
            'show_in_admin_status_list' => true,
            'label_count'               => _n_noop("$label (%s)", "$label (%s)", 'text_domain')
        ));
    }
}
add_action('init', 'register_custom_order_statuses_engage');

// Add custom statuses to WooCommerce
function add_custom_order_statuses_engage($order_statuses) {
    $new_order_statuses = array();

    foreach ($order_statuses as $key => $status) {
        $new_order_statuses[$key] = $status;

        if ('wc-order-processing' === $key) {
            $new_order_statuses['wc-order-dispatched'] = _x('Order Dispatched', 'Order status', 'text_domain');
            $new_order_statuses['wc-order-out-for-delivery'] = _x('Out for Delivery', 'Order status', 'text_domain');
            $new_order_statuses['wc-order-delivered'] = _x('Order Delivered', 'Order status', 'text_domain');
            $new_order_statuses['wc-order-shipped'] = _x('Order Shipped', 'Order status', 'text_domain');
            $new_order_statuses['wc-order-pickup'] = _x('Order Pickup', 'Order status', 'text_domain');
            $new_order_statuses['wc-order-pickup-scheduled'] = _x('Order Pickup Scheduled', 'Order status', 'text_domain');
        }
    }

    return $new_order_statuses;
}
add_filter('wc_order_statuses', 'add_custom_order_statuses_engage');

// Add custom order statuses to actionable statuses
function custom_order_status_action_engage($actions) {
    $actions[] = 'wc-order-dispatched';
    $actions[] = 'wc-order-out-for-delivery';
    $actions[] = 'wc-order-delivered';
    $actions[] = 'wc-order-shipped';
    $actions[] = 'wc-order-pickup';
    $actions[] = 'wc-order-pickup-scheduled';

    return $actions;
}
add_filter('bulk_actions-edit-shop_order', 'custom_order_status_action_engage');

// Add custom order statuses to admin order list filters
function add_custom_order_statuses_admin_order_list_engage($order_statuses) {
	$order_statuses['wc-order-dispatched'] = _x('Order Dispatched', 'Order status', 'text_domain');
    $order_statuses['wc-order-out-for-delivery'] = _x('Out for Delivery', 'Order status', 'text_domain');
    $order_statuses['wc-order-delivered'] = _x('Order Delivered', 'Order status', 'text_domain');
    $order_statuses['wc-order-shipped'] = _x('Order Shipped', 'Order status', 'text_domain');
    $order_statuses['wc-order-pickup'] = _x('Order Pickup', 'Order status', 'text_domain');
    $order_statuses['wc-order-pickup-scheduled'] = _x('Order Pickup Scheduled', 'Order status', 'text_domain');

    return $order_statuses;
}
add_filter('wc_order_statuses', 'add_custom_order_statuses_admin_order_list_engage');

?>