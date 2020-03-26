<?php
error_reporting(E_ERROR | E_PARSE);
require_once 'henry.php';
require_once 'henryTools.php';

$ip  = '10.42.44.25';
$port = 3000;

$idBiometric  = 51627;  // Biometric Id
$numBiometric = 0;      // Biometric Number

$henry = new henryTools($ip, $port);

$arrayRet = $henry->getBiometricByIdBase64($idBiometric,$numBiometric);

if( $arrayRet['success'] === false ){
    die("Error: {$arrayRet['message']}");
} else {
    echo '<pre>';
    echo "Fingerprint in base64: {$arrayRet['data']}";
}
