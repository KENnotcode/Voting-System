<?php
	$conn = new mysqli('localhost', 'root', '', 'stivoting');

	if ($conn->connect_error) {
	    die("Connection failed: " . $conn->connect_error);
	}
	
?>