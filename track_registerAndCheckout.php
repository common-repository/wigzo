<?php

if ( ! defined( 'ABSPATH' ) ) exit;

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ):

    add_action( 'wp_login', function ( $user_login, WP_User $user ) {
        if ( ! is_admin() ):
            // if ( isset( $_POST['woocommerce-register-nonce'] ) && wp_verify_nonce( $_POST['woocommerce-register-nonce'], 'woocommerce-register-nonce' ) ) {
                $user_id = $user->ID;
                $org_token = get_option("wigzo_org_token");	
                $cookie_id = $_COOKIE['WIGZO_LEARNER_ID'] ?? "";
            
                if ( ! empty( $cookie_id ) ):
    
                    $current_user = get_userdata( $user_id );
                    $phone_number = get_user_meta( $user_id, 'billing_phone', true );
                    $email = $current_user->user_email;
                    $full_name = $current_user->first_name . " " . $current_user->last_name;

                    $endpoint = 'https://app.wigzo.com/rest/v1/learn/identify?org_token=' . $org_token;
                    
                    $body = [
                        'userId'  => $cookie_id,
                        'email' => $email,
                        'phone' => isset( $phone_number ) ? $phone_number : null,
                        'is_active' => true,
                        'source' => 'app'
                    ];

                    if (isset($full_name) && !empty($full_name)) {
                        $body['fullName'] = $full_name;
                    }

                    $body = wp_json_encode( $body );
                    
                    $options = [
                        'body'        => $body,
                        'headers'     => [
                            'Content-Type' => 'application/json',
                        ],
                        'method'      => 'POST',
                        'timeout'     => 60,
                        'redirection' => 5,
                        'blocking'    => true,
                        'httpversion' => '1.0',
                        'sslverify'   => false,
                        'data_format' => 'body',
                    ];
                    
                    $response = wp_remote_post( esc_url_raw( $endpoint ), $options );
    
                    if ( is_wp_error( $response ) ):
                        error_log( print_r( $response->get_error_message(), true ) );
                    else:
                        //error_log( print_r( wp_remote_retrieve_body( $response ), true ) );
                    endif;
                else:
                    error_log( print_r( "Cookie not set.", true ) );
                endif;
        else:
            error_log( print_r( "you in the dashboard.", true ) );
        endif;
    
    }, 10, 2 );

    add_action( 'user_register', function ( $user_id ) {
        if ( ! is_admin() ):
            // if ( isset( $_POST['woocommerce-register-nonce'] ) && wp_verify_nonce( $_POST['woocommerce-register-nonce'], 'woocommerce-register-nonce' ) ) {

                $org_token = get_option("wigzo_org_token");	
                $cookie_id = $_COOKIE['WIGZO_LEARNER_ID'] ?? "";

                if ( ! empty( $cookie_id ) ):

                    $current_user = get_userdata( $user_id );
                    $phone_number = get_user_meta( $user_id, 'billing_phone', true );
                    $email = $current_user->user_email;
                    $full_name = $current_user->first_name . " " . $current_user->last_name;

                    $endpoint = 'https://app.wigzo.com/rest/v1/learn/identify?org_token=' . $org_token;
                    
                    $body = [
                        'userId'  => $cookie_id,
                        'email' => $email,
                        'phone' => isset( $phone_number ) ? $phone_number : null,
                        'is_active' => true,
                        'source' => 'app'
                    ];

                    if (isset($full_name) && !empty($full_name)) {
                        $body['fullName'] = $full_name;
                    }

                    $body = wp_json_encode( $body );
                    
                    $options = [
                        'body'        => $body,
                        'headers'     => [
                            'Content-Type' => 'application/json',
                        ],
                        'method'      => 'POST',
                        'timeout'     => 60,
                        'redirection' => 5,
                        'blocking'    => true,
                        'httpversion' => '1.0',
                        'sslverify'   => false,
                        'data_format' => 'body',
                    ];
                    
                    $response = wp_remote_post( esc_url_raw( $endpoint ), $options );

                    if ( is_wp_error( $response ) ):
                        error_log( print_r( $response->get_error_message(), true ) );
                    else:
                        //error_log( print_r( wp_remote_retrieve_body( $response ), true ) );
                    endif;
                else:
                    error_log( print_r( "Cookie not set.", true ) );
                endif;
        else:
            error_log( print_r( "you in the dashboard.", true ) );
        endif;

    } );

    add_action("woocommerce_order_status_changed", function( $order_id, $checkout = null ) {
        if ( ! is_admin() ):
            global $woocommerce;
            $order = wc_get_order( $order_id );

            if( $order->get_status() === 'processing' || $order->get_status() === 'on-hold'):
                    
                $cookie_id = $_COOKIE['WIGZO_LEARNER_ID'] ?? "";

                if ( ! empty( $cookie_id ) ):

                    $email  = $order->get_billing_email();
                    $phone_number  = $order->get_billing_phone();

                    $org_token = get_option("wigzo_org_token");
                    
                    $first_name = $order->get_billing_first_name();
                    $last_name = $order->get_billing_last_name();
                    $full_name = $first_name . ' ' . $last_name;
                    
                    $endpoint = 'https://app.wigzo.com/rest/v1/learn/identify?org_token=' . $org_token;
                    
                    $body = [
                        'userId'  => $cookie_id,
                        'email' => isset( $email ) ? $email : null,
                        'phone' => isset( $phone_number ) ? $phone_number : null,
                        'is_active' => true,
                        'source' => 'app'
                    ];

                    if (isset($full_name) && !empty($full_name)) {
                        $body['fullName'] = $full_name;
                    }

                    $body = wp_json_encode( $body );
                    
                    $options = [
                        'body'        => $body,
                        'headers'     => [
                            'Content-Type' => 'application/json',
                        ],
                        'method'      => 'POST',
                        'timeout'     => 60,
                        'redirection' => 5,
                        'blocking'    => true,
                        'httpversion' => '1.0',
                        'sslverify'   => false,
                        'data_format' => 'body',
                    ];
                    
                    $response = wp_remote_post( esc_url_raw( $endpoint ), $options );

                    if ( is_wp_error( $response ) ):
                        error_log( print_r( $response->get_error_message(), true ) );
                    else:
                        //error_log( print_r( wp_remote_retrieve_body( $response ), true ) );
                    endif;
                else:
                    error_log( print_r( "Cookie not set.", true ) );
                endif;
            else:
                //error_log( print_r( $order, true ) );
            endif;
        endif;
    }, 10, 4 );
endif;