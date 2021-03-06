<?php

// Contains common php functions



/**************************************************************************
	Attempts to connect to the database.
	No changes are made to the database.
	
	On success, the connection object is returned.
	
	On failure, an error message is printed and null is returned.
**************************************************************************/
function connectToDatabase() {
	$con = @new mysqli("localhost", "root", "", "ams");
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
	
	
/**************************************************************************
	Parses a string of the form mm/yy into a properly formatted
	SQL-compatible date string.
	
	NOTE: the month is incremented by one, and the day is the first of the
	month, because the actual expiry date of a credit card is on the first
	of the following month.
	
	No changes are made to the database.
**************************************************************************/
function getFormattedExpiryDate($creditcardexpiry) {

	// ERROR: The credit card expiry is not correct format
	if (!filter_var($creditcardexpiry, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>'/^[0-9][0-9]\/[0-9][0-9]$/'))))
		return NULL;
	
	$year = intval('20' . substr($creditcardexpiry, 3, 2));
	$month = intval(substr($creditcardexpiry, 0, 2));
	$day = 1;
	
	if ($year < 0 || $month < 1 || $month > 12)
		return NULL;

	if ($month < 12)
		$month += 1;
	else {
		$year += 1;
		$month = 1;
	}
	
	$expiryDate = $year . '-' . $month . '-' . $day;
	
	return $expiryDate;
	
}



?>
