<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
| -------------------------------------------------------------------------
| HybridAuth settings
| -------------------------------------------------------------------------
| Your HybridAuth config can be specified below.
|
| See: https://github.com/hybridauth/hybridauth/blob/v2/hybridauth/config.php
*/
$config['hybridauth'] = array(
    "providers" => array(
        "OpenID" => array(
            "enabled" => FALSE,
        ),
        "Yahoo" => array(
            "enabled" => FALSE,
            "keys" => array("id" => "#SEU_ID", "secret" => "#SECRET_KEY"),
        ),
        "Google" => array(
            "enabled" => FALSE,
            "keys" => array("id" => "#SEU_ID", "secret" => "#SECRET_KEY"),
        ),
        "Facebook" => array(
            "enabled" => TRUE,
            "keys" => array("id" => "#SEU_ID", "secret" => "#SECRET_KEY"), 
            "trustForwarded" => FALSE,
        ),
        "Twitter" => array(
            "enabled" => FALSE,
            "keys" => array("key" => "#SEU_ID", "secret" => "#SECRET_KEY"),
            "includeEmail" => FALSE,
        ),
        "LinkedIn" => array(
            "enabled" => TRUE,
            "keys" => array("id" => "#SEU_ID", "secret" => "#SECRET_KEY"),
        ),
    ),
    // If you want to enable logging, set 'debug_mode' to true.
    // You can also set it to
    // - "error" To log only error messages. Useful in production
    // - "info" To log info and error messages (ignore debug messages)
    "debug_mode" => ENVIRONMENT === 'development',
    // Path to file writable by the web server. Required if 'debug_mode' is not false
    "debug_file" => APPPATH . 'logs/hybridauth.log',
);
