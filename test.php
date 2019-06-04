<?php
	include "services/connection.php";
	$id = $_GET["id"];
	$connection = new Connection();
	$connection->getConn();
	$test = $connection->getUser($id);
	var_dump($test);
	$connection->closeConn();
?>