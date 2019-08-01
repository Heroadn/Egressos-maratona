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
            "keys" => array("id" => "", "secret" => ""),
        ),
        "Google" => array(
            "enabled" => FALSE,
            "keys" => array("id" => "", "secret" => ""),
        ),
        "Facebook" => array(
            "enabled" => TRUE,
            "keys" => array("id" => "944857609192270", "secret" => "39a17449113e00ce1725939e50597302"), 
            "trustForwarded" => FALSE,
        ),
        "Twitter" => array(
            "enabled" => FALSE,
            "keys" => array("key" => "#SEU_ID", "secret" => "#SECRET_KEY"),
            "includeEmail" => FALSE,
        ),
        "LinkedIn" => array(
            "enabled" => TRUE,
            "keys" => array("id" => "78118at02cgdoa", "secret" => "V0AkXA5kr02BP04I"),
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
