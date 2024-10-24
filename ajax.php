<?php

require_once 'autoload.php';
require 'libs/Shared/Rest.php';

date_default_timezone_set("Asia/Almaty");
header('Content-Type: application/json');
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Content-Security-Policy: default-src \'self\'');

session_start();
$serverData = $_SERVER;

global $outData;

$rest = new \Shared\Rest($serverData);
$data = $rest->getData();

function dbConn($host, $username, $password, $database) {
    $connectDB = mysqli_connect($host, $username, $password, $database);
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

        try {
            $db = dbConn('localhost:3306', 'p-345076_user', 'vovazero123', 'p-345076_base');
            $object = new $obj($data['method'], $db);
            $outData = $object->execute($data['params']);
        } catch(Exception $e) {
            $outData = ['error' => $e->getMessage()];
        }
    } else {
        $outData = ['error' => '404'];
        //  $outData = $file;
    }
} else {
    $outData = $data['error'];
}
echo json_encode($outData);


