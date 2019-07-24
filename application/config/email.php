<?php
/*
|--------------------------------------------------------------------------
| Email variables
|--------------------------------------------------------------------------
|
| 'useragent'
|
|	The storage driver to use: files, database, redis, memcached
|
| 'mailpath'
|
|	The storage driver to use: files, database, redis, memcached
|
| 'smtp_crypto'
|
|	The storage driver to use: files, database, redis, memcached
|
| 'smtp_host'
|
|	The storage driver to use: files, database, redis, memcached
|
| 'smtp_port'
|
|	The storage driver to use: files, database, redis, memcached
|
| 'mailtype'
|
|	The storage driver to use: files, database, redis, memcached
|
| 'charset
|
|	The storage driver to use: files, database, redis, memcached
|
| 'wordwrap'
|
|	The storage driver to use: files, database, redis, memcached
|
| 'smtp_user'
|
|	The storage driver to use: files, database, redis, memcached
|
| 'smtp_pass'
|
|	The storage driver to use: files, database, redis, memcached
|
|--------------------------------------------------------------------------
*/
    $config = array();
    $config['useragent'] = "CodeIgniter";
    $config['mailpath'] = "/usr/bin/sendmail";
    $config['smtp_crypto'] = 'tls';
    $config['protocol'] = "smtp";
    $config['smtp_host'] = "acid-software.net";
    $config['smtp_port'] = "25";
    $config['mailtype'] = 'html';
    $config['charset']  = 'utf-8';
    $config['newline']  = "\r\n";
    $config['wordwrap'] = TRUE;

    $config['smtp_user'] = 'hackthanos@acid-software.net';
    $config['smtp_pass'] = 'k3h!Lq:is1G0>sUT!eExDEyM&?1pXbH8';
?>