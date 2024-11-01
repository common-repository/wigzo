<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function wigzo_setoption ($option_name, $new_value) {
    if ( get_option( $option_name ) !== false ) {
        update_option ($option_name, $new_value);

    } else {
        add_option ($option_name, $new_value);
    }
}

function wigzo_getWigzoHost () {
    /* Get current Wigzo Host */
    /* Function to check weather we are running in a Development environment,
    file /tmp/wigzomode only exists in Dev environment */
    if (file_exists ("/tmp/wigzomode")) {
        return trim (file_get_contents ("/tmp/wigzomode"));
    }
    else {
        return "https://app.wigzo.com";
    }
}

function wigzo_gen_uuid () {
    return sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),
        mt_rand( 0, 0xffff ),
        mt_rand( 0, 0x0fff ) | 0x4000,
        mt_rand( 0, 0x3fff ) | 0x8000,
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}