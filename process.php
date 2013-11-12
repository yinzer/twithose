<?php
require_once 'vendor/autoload.php';



$dbhost = "127.0.0.1";
$dbname = "tweets";
$dbuser = "root";
$dbpass = "asdf.1234!";

$process = new Process($dbhost, $dbname, $dbuser, $dbpass);