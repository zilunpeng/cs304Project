<?php

// Contains common php functions



/**************************************************************************
	Attempts to connect to the database.
	No changes are made to the database.
	
	On success, the connection object is returned.
	
	On failure, an error message is printed and null is returned.
**************************************************************************/
function connectToDatabase() {
	$con = @new mysqli("localhost", "root", "cpsc304", "ams");
	if ($con->connect_error) {
		addToMessages ("Failed to connect to the database");
		return null;
	}
	return $con;
}



/**************************************************************************
	Disconnects from the database.
	No changes are made to the database.
**************************************************************************/
function disconnectFromDatabase($con) {
	if ($con == null)
		return;
	$con->close();
}
	
	

/**************************************************************************
	Add to the list of error/success messages.
	No changes are made to the database.
	
	@param $message
		The message to add.
**************************************************************************/
function addToMessages($message) {

	GLOBAL $messages;
	
	if (!isset($messages))
		$messages = array();
		
	array_push($messages, $message);
	
}

	

/**************************************************************************
	Prints the error/success messages from the model section.
	No changes are made to the database.
**************************************************************************/
function printMessages() {

	GLOBAL $messages;
	
	if (!isset($messages) || count($messages) == 0)
		return;
		
	echo ("<ul>\n");
	for ($x = 0; $x < count($messages); $x++)
		echo ("<li>" . $messages[$x] . "</li>\n");
	echo ("</ul>\n");
	
}



?>
