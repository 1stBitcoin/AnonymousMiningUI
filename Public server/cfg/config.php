<?php

$dbhost = "mining.com.mysql";
$dbuser = "mining_com";
$dbpass = "Veryg00dPasswordIsLongPP";
$dbname = "mining_com";
$display_errors = false;
$disable_admin_panel = false;

$connection_options = array(
    'disable_curl' => false,
    'local_cafile' => false,
    'force_ipv4' => false    // cURL only
);

// dsn - Data Source Name
// if you use MySQL, leave it as is
// more information:
// http://php.net/manual/en/pdo.construct.php
$dbdsn = "mysql:host=$dbhost;dbname=$dbname";
