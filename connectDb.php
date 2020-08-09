<?php
	
	DEFINE('DB_USER', 'raikam');
	DEFINE('DB_PASSWORD', '****');
	DEFINE('DB_HOST', 'localhost');
	DEFINE('DB_NAME', 'lotterydatabase');
	
	$dbc = @mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME)
	OR dies('Failed to connect to database'.mysqli_connect_error());

?>
