<?php
if ( ! defined( 'ABSPATH' ) ) exit;

function wigzo_get_auth_token () {
    global $wpdb;

    $uid = get_option ("wigzo_challenge");
    if ($uid == false) {
        $uid = gen_uuid ();
        update_option ("wigzo_challenge", $uid);
    }
    return $uid;
}