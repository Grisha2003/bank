<?php

$settings = [
    'host'=>'localhost',
    'port'=>5432,
    'dbname'=>'postgres',
    'user'=>'postgres',
    'password'=>'vovazero123'
];
$connectDB = pg_connect("host=localhost port=5432 dbname=postgres user=postgres password=vovazero123");

if ($connectDB != false) {
    print_r("супер");
    
} else {
    print_r("ошибка");
}


