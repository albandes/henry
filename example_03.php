<?php
error_reporting(E_ERROR | E_PARSE);
require_once 'henry.php';

$ip  = '10.42.44.25';
$port = 3000;

$henry = new henry($ip, $port);

$arrayRet = $henry->getBiometricByIdBase64('123',0);

if( $arrayRet['success'] === false ){
    die("Error: {$arrayRet['message']}");
} else {
    echo '<pre>';
    echo "Fingerprint in base64: {$arrayRet['data']}";
}
