<?php
require_once 'autoload.php';

date_default_timezone_set("Asia/Almaty");
header('Content-Type: application/json');


require 'libs/Shared/Rest.php';
$rest = new \Shared\Rest($_SERVER);
$outData = json_encode($rest->getData());
echo $outData;


//
//$obj = $namespace .$class;
//echo "Request '" . $in['action'] . "' to object $obj\n";
//$object = new $obj($srvs);
//$retData = $object->execute($in);
