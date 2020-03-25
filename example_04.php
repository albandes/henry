<?php
error_reporting(E_ERROR | E_PARSE);
require_once 'henry.php';

$ip  = '10.42.44.25';
$port = 3000;

$henry = new henry($ip, $port);

$ret = $henry->deleteBiometric('4',0,$biometricBase64) ;


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






