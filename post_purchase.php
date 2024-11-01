<?php
if (!defined('ABSPATH')) exit;

function wigzo_store_order_update_event($order_id) {
    // Check if the transient is set to avoid duplicate processing
    if (get_transient('wigzo_order_update_' . $order_id)) {
        return;
    }

    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    $order_data = $order->get_data();
    $order_status = $order_data['status'];
    $order_total = $order_data['total'];
    $order_currency = $order_data['currency'];
    $order_date = $order_data['date_created']->date('Y-m-d H:i:s');
    $customer_id = $order_data['customer_id'];
    $customer_phone = $order_data['billing']['phone'];
    $customer_email = $order_data['billing']['email'];
    $customer_name = $order_data['billing']['first_name'] . ' ' . $order_data['billing']['last_name'];

    // Prepare the data to send to Wigzo
    $org_token = get_option('wigzo_org_token');

    $endpoint = 'https://app.wigzo.com/rest/v1/learn/shipment/custom/' . $org_token;

    $body = array(
        "order_id" => (string)$order_id,
        "current_status" => $order_status,
        "shipment_status" => $order_status, // Update with actual shipment status if available
        "current_status_id" => "1", // Update with actual current status ID if available
        "shipment_status_id" => "1", // Update with actual shipment status ID if available
        "phone" => $customer_phone,
        "email" => $customer_email,
        "name" => $customer_name
    );

    // Log event data (optional, for debugging)
    // $log_file = plugin_dir_path(__FILE__) . 'order_update_log.log';
    // file_put_contents($log_file, print_r($body, true), FILE_APPEND);

    $body = wp_json_encode($body);

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

    $response = wp_remote_post(esc_url_raw($endpoint), $options);
    if (is_wp_error($response)) {
        error_log(print_r($response->get_error_message(), true));
    } else {
        error_log(print_r($response, true));
    }

    // Set a transient to avoid duplicate processing
    set_transient('wigzo_order_update_' . $order_id, true, 10); // Set transient for 60 seconds
}
?>