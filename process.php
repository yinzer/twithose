<?php
require_once 'vendor/autoload.php';

$dbhost = "127.0.0.1";
$dbname = "tweets";
$dbuser = "dbuser";
$dbpass = "dbpass";

$process = new Process($dbhost, $dbname, $dbuser, $dbpass);