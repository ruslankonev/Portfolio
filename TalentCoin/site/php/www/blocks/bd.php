<?php

$host = 'localhost';
$db = 'talentico';
$myname = 'talentico';
$psc = '9F6h4X9aZ1q9U9c5';
$dsn = "mysql:host=$host;dbname=$db";

$opt = array(
	PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
	PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
	PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
);

$pdo = new PDO($dsn, $myname, $psc, $opt);

?>
