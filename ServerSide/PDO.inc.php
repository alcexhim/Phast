<?php
	namespace Phast;

	$engine = 'mysql';
	$host = System::GetConfigurationValue("Database.ServerName");
	$database = System::GetConfigurationValue("Database.DatabaseName");
	$user = System::GetConfigurationValue("Database.UserName");
	$pass = System::GetConfigurationValue("Database.Password");
		
	$dns = $engine . ':dbname=' . $database . ";host=" . $host;

	global $pdo;
	$pdo = new \PDO($dns, $user, $pass);
?>