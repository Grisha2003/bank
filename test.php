<?php
$name = 'hell';
$date = 'z';
$cde = null;

if (!isset($name) || (isset($date) && isset($cde))) {
    print_r("erro");
} else {
    print_r("JJJJ");
}
