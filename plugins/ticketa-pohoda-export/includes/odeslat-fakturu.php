<?php
$login = base64_encode('mserver:vmart');
$url = 'http://46.234.119.70:4444/xml';
$datain = $dom; //file_get_contents($filePath);//

$options = array(
    'http' => array(
        'header' => "STW-Authorization: Basic ".$login."\r\n".
							"Content-type: text/xml; charset=Windows-1250\r\n",
        'method' => 'POST',
        'content' => $datain
    )
);

$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);

if ($result === FALSE) { error_log(  'neproslo :(' ); } else { error_log( $datain ); }
var_dump($result, $dom);
?>