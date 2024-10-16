<?php
require_once 'autoload.php';
require 'libs/Shared/Rest.php';

date_default_timezone_set("Asia/Almaty");
header('Content-Type: application/json');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Content-Security-Policy: default-src \'self\'');

//$serverData = filter_input_array(INPUT_SERVER, FILTER_SANITIZE_STRING);
$serverData = $_SERVER;

global $outData;

$rest = new \Shared\Rest($serverData);
$data = $rest->getData();



function dbConn()
{
    $connectDB = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=vovazero123");
    return $connectDB;
}

if ($data['status'] && empty($data['error'])) {
    $namespace = $data['namespace'];
    $class = $data['class'];
    $file = __DIR__ . DIRECTORY_SEPARATOR . 'libs' . DIRECTORY_SEPARATOR . $namespace . DIRECTORY_SEPARATOR . $class . '.php';
    
    if (file_exists($file)) {
        $namespace = $data['namespace'];
        $class = $data['class'];
        $obj = '\\' . $namespace . '\\' . $class;
        $db = dbConn();
        $object = new $obj($data['method'], $db);
        $outData = $object->execute($data['params']);
    } else {
        $outData = ['error' => '404'];
      //  $outData = $file;
    }
} else {
    $outData = $data['error'];
}
echo json_encode($outData);


//
//$obj = $namespace .$class;
//echo "Request '" . $in['action'] . "' to object $obj\n";
//$object = new $obj($srvs);
//$retData = $object->execute($in);
