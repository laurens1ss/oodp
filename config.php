<?php
ob_start(); // Starts output buffering. This allows you to send headers or redirect the user even after output has been sent to the browser.

try {
	// Creates a new PDO (PHP Data Object) instance to connect to the MySQL database.
	$con = new PDO("mysql:dbname=holla;host=localhost", "root", "");

	// Sets an attribute on the database handle to show warnings if a database error occurs.
	$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
}
catch(PDOException $e) { // Catches any exceptions thrown by the PDO instance.
	// If a connection error occurs, it will output a message saying the connection failed and display the error message.
	echo "Connection failed: " . $e->getMessage();
}
?>
