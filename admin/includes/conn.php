<?php
	$conn = new mysqli('localhost', 'root', '', 'appsystem');

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	$conn->set_charset("utf8");
?>