<?php
	$dbHost = "localhost";
	$dbDatabase = "earsip_active";
	$dbPasswrod = "root";
	$dbUser = "root";
	$mysqli = new mysqli($dbHost, $dbUser, $dbPasswrod, $dbDatabase);
	if($mysqli->connect_errno){
		echo "Failed to connect to MySQL: " . $mysqli->connect_error;
		exit();
	}
?>