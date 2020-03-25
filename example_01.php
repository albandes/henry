<?php
error_reporting(E_ERROR | E_PARSE);
require_once 'henry.php';

$ip  = '10.42.44.25';
$port = 3000;

$command = "01+ED+00+T]20201234}S}B}1}512{";
$biometricBase64 = "RRsSFXcAU0I0wbBrCBBG0GuGPodgCYY4R/BjhA7IkBoFOokBY4QjSWBqhSnKIGiED8owcYQPypAWgxuMMBQHEI0QcgQ9zTFgiCPOcB0CBc6AEoM9DpBfiiFPcXKCBNAAFYY50HFmDiNRAR0HPVFBXoU6UfAPjD/SQVcVHZLxc4MNE3EXAhPUMXEJO5ShZxD//My8zMzMz///vMzMzMzM3f/8zMzMzMzM3f/MzMzMzMzN3d/MzMzMzMzN3d3MzMzMzMzN3d7MzMzMzMzN3e7My8zMzMzN3u7Mu7zMzMzd3u7My7vMzMzd7u7My7zMzM3d7u7My7vMzM3d3u7My7zMvMzd3u7Mu7u7u8zN3uDMy7u7u8zN3gDMu7u7u7zM3gC7u7u7u7zM3gC7u7u7q7zM3gG7u7u7qru8zgL7u7u7u7u7zhP/vLu7u7u7zQMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA";

$commandFull = $command . $biometricBase64 ;
 
$henry = new henry($ip, $port);

$henry->setIp($ip);
$henry->setPort($port);

$commandHexa = $henry->generate($commandFull);
$commandHexa  = str_replace(" ","",$commandHexa);

$ret = $henry->connect();

if($ret !== true)
    die($ret);

$henry->flushBuffer();

$ret = $henry->writeSocket($henry->hex2str($commandHexa));
if (!$ret)
    die($ret) ;

$arrayRet = $henry->listen();
if( $arrayRet['success'] === false ){
    die($arrayRet['message']);
} else {
    echo '<pre>';
    print_r($arrayRet);
}

echo "Error: {$arrayRet['err_or_version']}"; // 000 is OK !!!

