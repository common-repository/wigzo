<?php
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

// include_once ("oauth/oauth_helpers.php.inc");
include_once ("lib.php");

$wigzo_oauth_done = false;

$oauth_token = get_option("wigzo_oauth");
if ($oauth_token) {
    $wigzo_oauth_done = true;
}

if (! $wigzo_oauth_done) {
	include_once("html/wigzo_askoauth.html");
	die ();
}

$clientId = get_option ("wigzo_client_id");
$wigzo_host = wigzo_getWigzoHost();

$wigzo_app = "Disabled";
if (get_option ("wigzo_enabled")) {
	$wigzo_app = "Enabled";
}
$browser_push = "disabled";
if (get_option ("wigzo_browserpush")) {
    $browser_push = "enabled";
}

$onsite = "disabled";
if (get_option ("wigzo_onsitepush")) {
    $onsite = "enabled";
}

$https = "";
if (get_option ("wigzo_viahttps")) {
    $https = "(over https)";
}
$pwa ="Disabled";
if(get_option("wigzo_pwa")){
	$pwa ="Enabled";
}

include_once ("html/wigzo_settings.html");


