<?php
error_reporting(E_ERROR | E_PARSE);
require_once 'henry.php';

$ip  = '10.42.44.25';
$port = 3000;

$biometricBase64 = "RRsSFXcAU0I0wbBrCBBG0GuGPodgCYY4R/BjhA7IkBoFOokBY4QjSWBqhSnKIGiED8owcYQPypAWgxuMMBQHEI0QcgQ9zTFgiCPOcB0CBc6AEoM9DpBfiiFPcXKCBNAAFYY50HFmDiNRAR0HPVFBXoU6UfAPjD/SQVcVHZLxc4MNE3EXAhPUMXEJO5ShZxD//My8zMzMz///vMzMzMzM3f/8zMzMzMzM3f/MzMzMzMzN3d/MzMzMzMzN3d3MzMzMzMzN3d7MzMzMzMzN3e7My8zMzMzN3u7Mu7zMzMzd3u7My7vMzMzd7u7My7zMzM3d7u7My7vMzM3d3u7My7zMvMzd3u7Mu7u7u8zN3uDMy7u7u8zN3gDMu7u7u7zM3gC7u7u7u7zM3gC7u7u7q7zM3gG7u7u7qru8zgL7u7u7u7u7zhP/vLu7u7u7zQMAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA";

$henry = new henry($ip, $port);

$ret = $henry->sendBiometricBase64('51627',0,$biometricBase64) ;

$message = ($ret === true ? 'Ok, biometry writed !!! ' : 'Error: {$ret}');

echo $message;



