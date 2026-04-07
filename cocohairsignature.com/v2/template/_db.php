<?php
$db_env = [
	// local
	[
		"servername" => "mysql-8.0-container",
		"username" => "cocohairsignature_com",
		"password" => "cocohairsignature_com_password",
		"dbname" => "cocohairsignature_com"
	],
	// live
	[
		"servername" => "localhost",
		"username" => "u723978224_coconame",
		"password" => "/4qAe49G",
		"dbname" => "u723978224_cocodb"
	]
];

#connect2db
$db_auth = $db_env[0];
$db = new mysqli($db_auth['servername'], $db_auth['username'], $db_auth['password'], $db_auth['dbname']);

?>