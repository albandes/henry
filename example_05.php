<?php
error_reporting(E_ERROR | E_PARSE);
require_once 'henry.php';
require_once 'henryTools.php';

$ip  = '10.42.44.25';
$port = 3000;

/*

Examples:

$parameter     = "ACESSO LIBERADO";
$value = 'A' ;

$parameter  = "MSG_LINHA2";
$value      = "Aguarde a leitura !" ;

$parameter = "MSG_LINHA1_ENTRADA";
$value     = "2} Alto Astral";
*/

$parameter = "MSG_LINHA2_ENTRADA";
$value     = "3";

$henry = new henryTools($ip, $port);

$ret = $henry->setConfig($parameter,$value) ;

if ($ret === true )
    $message =  "Ok, set configuration successful. param: {$parameter}, value: {$value} ";
else
   $message = "Error: " . $ret;

echo $message;






