<?php
error_reporting(E_ERROR | E_PARSE);
require_once 'henry.php';
require_once 'henryTools.php';

$ip  = '10.42.44.25';
$port = 3000;

$idBiometric  = 51627;  // Biometric Id

$henry = new henryTools($ip, $port);

$ret = $henry->deleteBiometric($idBiometric) ;


if ($ret === true ) {
    $message =  "Ok, biometry deleted !!! ";
} else {
    if (is_numeric(intval($ret))) {
        // If you want to decode error code, use $henry->parseError()
        $message = "Error: " . $henry->parseError(intval($ret));
    } else {
        $message = "Error: " . $ret;
    }
}

echo $message;






