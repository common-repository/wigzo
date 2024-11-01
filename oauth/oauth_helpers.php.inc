<?php

function wigzo_convert_to_longlived ($token) {
	$clientid = esc_html (get_option ("wigzo_client_id"));
	$clientsecret = esc_html (get_option ("wigzo_client_secret"));

	$postdata = array (
		"code" => "$token",
        "clientid" => "$clientid",
        "clientsecret" => "$clientsecret",
	);

	$url = wigzo_getWigzoHost()."/oauth/v2/token";
	
	$curl = function_exists('curl_version') ? 'Enabled' : 'Disabled';
	if($curl =='Enabled'){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt ($ch, CURLOPT_POST, 1);
        curl_setopt ($ch, CURLOPT_POSTFIELDS, json_encode ($postdata));
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        $result_decode = json_decode ($result);

        if (array_key_exists("access_token", $result_decode)) {
            $token = $result_decode->access_token;
            wigzo_setoption("wigzo_oauth", $token);
        }
	}

    return $token;
}

function wigzo_resyncoptions () {
    $token = get_option ("wigzo_oauth");
    $orgtoken = get_option ("wigzo_org_token");
    $plugin_id = get_option ("wigzo_client_id");
	$url = wigzo_getWigzoHost() . "/rest/v1/plugin/$plugin_id/data?token=" . $token."&orgtoken=".$orgtoken;

	$curl = function_exists('curl_version') ? 'Enabled' : 'Disabled';
	if($curl =='Enabled') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
        $result_decode = json_decode($result);

        $ignored = array("message", "code", "status");
        $bools = array("viahttps", "onsitepush", "pwa", "enabled", "browserpush");

        foreach ($result_decode as $key => $value) {
            if (in_array ($key, $ignored)) {
                continue;
            }
            if (in_array ($key, $bools)) {
                if ($value == "true") {
                    wigzo_setoption("wigzo_" . sanitize_text_field($key), 1);
                } else {
                    wigzo_setoption("wigzo_" . sanitize_text_field($key), 0);
                }
                continue;
            }
            wigzo_setoption("wigzo_" . sanitize_text_field ($key), sanitize_text_field ($value));
        }
        /*
            $via = sanitize_text_field ($obj->{'via'});
            $token = sanitize_text_field ($obj->{'token'});
            $enabled = sanitize_text_field ($obj->{'enabled'});
            $org_id = sanitize_text_field ($obj->{'orgId'});
            $viahttps = sanitize_text_field ($obj->{'viahttps'});
            $browserpush = sanitize_text_field ($obj->{'browserpush'});
            $pwa = sanitize_text_field ($obj->{'pwa'});
            $pwaFilesPath = sanitize_text_field ($obj->{'pwaFilesPath'});
            $pwaData = sanitize_text_field ($obj->{'pwaData'});
        */
    }
}
