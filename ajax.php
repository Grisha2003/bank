<?php
require_once 'autoload.php';
require 'libs/Shared/Rest.php';

date_default_timezone_set("Asia/Almaty");
header('Content-Type: application/json');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Content-Security-Policy: default-src \'self\'');

$serverData = filter_input_array(INPUT_SERVER, FILTER_SANITIZE_STRING);

global $outData;

$rest = new \Shared\Rest($serverData);
$data = $rest->getData();



function dbConn($settings)
{
    $connectDB = pg_connect("host={$settings['host']} port={$settings['port']} dbname={$settings['base']} user={$settings['user']} password={$settings['password']}");
    
}

if ($data['status'] && empty($data['error'])) {
    $namespace = $data['namespace'];
    $class = $data['class'];
    $file = __DIR__ . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . $namespace . DIRECTORY_SEPARATOR . $class;
    if (file_exists($file)) {
        $namespace = $data['namespace'];
        $class = $data['class'];
        $obj = '\\' . $namespace . '\\' . $class;
        $object = new $obj();
        $outData = $object->execute($data['params']);
    } else {
        $outData = '404';
    }
} else {
    $outData = $data['error'];
}
echo htmlspecialchars($outData, ENT_QUOTES, 'UTF-8');


//
//$obj = $namespace .$class;
//echo "Request '" . $in['action'] . "' to object $obj\n";
//$object = new $obj($srvs);
//$retData = $object->execute($in);
