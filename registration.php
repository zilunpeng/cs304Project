<!--
Each webpage in this project conforms to the Model View Controller (MVC) architecture.
Hence, each page is broken down into three parts:
	1. The Model: manages the operations, database transactions, etc.
	2. The View: the user interface.
	3. The Controller: processes commands from the user (typically from HTML forms)
	   and sends commands to the model.
-->



<!--
*******************************************************************
	THE CONTROLLER
	==============
*******************************************************************
-->
<?php
	


	// The Common PHP Functions
	include "includes/commonfunctions.php";
	


	// The Common PHP transactions
	include "includes/commontransactions.php";



	session_start();



	/**************************************************************
	Connect to database
	**************************************************************/
	$con = connectToDatabase();
	
	
	
	/**************************************************************
	Perform requested operations from HTML form here
	**************************************************************/
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		// OPERATION: User pressed the "register" button
		if (isset($_POST["register"])) {
			setFormSESSIONVariables();
			register($_POST["cid"], $_POST["name"], $_POST["password"], $_POST["phone"], $_POST["address"], $con);
		}
		
	}
	// If this is the first time the page is loaded (i.e. user has
	// not yet submitted a form), then clear out the saved form data.
	if ($_SERVER["REQUEST_METHOD"] != "POST") {
		unsetFormSESSIONVariables();
	}
	
	
	
	/**************************************************************
	Perform all remaining database queries here
	**************************************************************/
	
	
	
	/**************************************************************
	Close database connection
	**************************************************************/
	disconnectFromDatabase($con)
	
	
	
?>



<!--
******************************************************************
	THE VIEW
	========
******************************************************************
-->
<html>



<head>



	<!-- meta -->
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	
	
	
	<!-- Importing css and JavaScript -->
	<link rel="stylesheet" type="text/css" href="includes/styles.css" />
	<link rel="text/javascript" type="text/css" href="includes/commonfunctions.css" />
	
	
	
	<!-- Additional JavaScript functions -->
	<script>
		function confirmQuantity(stock) {
			'use strict';
			if (confirm("There are only " + stock + " in stock.\nDo you want to accept this quantity?"))
				return true;
			return false;
		}
	</script>

	
	
	<!-- Page title -->
	<title> Search </title>

	
	
</head>



<body>
<div class="container">

	

	<!-- The left portion of the page -->
	<div class="sidebar">
        <?php
			include("includes/sidebar.php");
		?>
	</div>
		
		
		
	<!-- The right (main) portion of the page -->
	<div class="main">
	
	
	
		<!-- Designated area for all error/success messages (top of page) -->
		<div class="messages">
		
		
		
			<!-- heading -->
			Error/Success Messages:<br>
			
			
			
			<!-- Error/Success messages (if any) -->
			<?php
				printMessages();
			?>
			
			
			
		</div>

		
		
		<div class="content">
		
		
		
			<!-- Heading -->
			<div style="width:100%; text-align:center; padding-top:50px; padding-bottom:30px;">
				<h2 style="font-size: 1.4em;"> REGISTER </h2>
			</div>
			
			
			
				<?php
					createRegistrationForm();
				?>
			

			
		</div>
		
		
		
	</div>
	
	
	
</div>



<?php



	/**************************************************************************
		Prints the registration form.
	**************************************************************************/
	function createRegistrationForm() {
		echo ('
			<form action="registration.php" method="post">
				<table style="margin-left:auto; margin-right:auto; text-align:left; margin-bottom:10px">
					<tr>
						<td> USER ID: </td>
						<td> <input type="text" name="cid" value="' . $_SESSION["cid"] . '"> </td>
						<td> NAME: </td>
						<td> <input type="text" name="name" value="' . $_SESSION["name"] . '"> </td>
						<td> PASSWORD: </td>
						<td> <input type="password" name="password" value="' . $_SESSION["password"] . '"> </td>
						<td> PHONE NUMBER: </td>
						<td> <input type="text" name="phone" value="' . $_SESSION["phone"] . '"> </td>
						<td> ADDRESS: </td>
						<td> <input type="text" name="address" value="' . $_SESSION["address"] . '"> </td>
						<td> <input type="submit" name="register" value="register"> </td>
					</tr>
				</table>		
			</form>
		');
	}
	
	
	
?>



</body>
</html>



<!--
***********************************************************************
	THE MODEL
	=========
***********************************************************************
-->
<?php



	/**************************************************************************
		Saves the contents of form elements into SESSION variables.
		
		Recall that when the user clicks a button in a form, the page is
		reloaded.  Normally, each time the page is reloaded, the form elements
		are blanked-out.
		
		However, by saving the contents of each form element into SESSION
		variables, the contents of the form elements can be restored to what
		they were before the page was reloaded.
	**************************************************************************/
	function setFormSESSIONVariables() {
		$_SESSION["cid"] = $_POST["cid"];
		$_SESSION["name"] = $_POST["name"];
		$_SESSION["password"] = $_POST["password"];
		$_SESSION["phone"] = $_POST["phone"];
		$_SESSION["address"] = $_POST["address"];
	}



	/**************************************************************************
		Clears the SESSION variables that stored the saved form data.
	**************************************************************************/
	function unsetFormSESSIONVariables() {
		$_SESSION["cid"] = "";
		$_SESSION["name"] = "";
		$_SESSION["password"] = "";
		$_SESSION["phone"] = "";
		$_SESSION["address"] = "";
	}
	
	
	
	/**************************************************************************
		Executes the purchase operation.
		Creates a new Order for the customer, then creates a PurchaseItem
		entity for each item in the cart.
		
		On success, commits changes to database and prints a success message
		that includes the expected delivery date.
		Also, it clears out the user's shopping cart.
		
		If an error occurs, an error message is printed, no changes are made
		to the database.
		
		@param $creditcardnumber, $creditcardexpiry
			The credit card information for the purchase
		
		@param $con
			The connection to the database
	**************************************************************************/
	function register($cid, $name, $password, $phone, $address, $con) {
	
		// ERROR: no database connection
		if ($con == null) {
			return;
		}		
	
		// ERROR: No user ID was given
		if ($cid == "") {
			addToMessages("You must enter a user ID");
			return;
		}
	
		// ERROR: No user name was given
		if ($name == "") {
			addToMessages("You must enter a name");
			return;
		}
	
		// ERROR: No password was given
		if ($password == "") {
			addToMessages("You must enter a password");
			return;
		}
	
		// ERROR: No phone number was given
		if ($phone == "") {
			addToMessages("You must enter a phone number");
			return;
		}
	
		// ERROR: No address was given
		if ($address == "") {
			addToMessages("You must enter an address");
			return;
		}
		
		// ERROR: The user ID is not the correct format
		if (!filter_var($cid, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>'/^[0-9a-zA-Z_]{1,16}$/')))) {
			addToMessages("Invalid user ID - only letters, numbers, and _ are allowed");
			return;
		}
		
		
		// Check if the given user ID is taken
		if (queryCustomer($con, $cid) != null) {
			addToMessages("That user ID is taken");
			return;
		}
		
		
		// Create the new user
		$user = insertIntoCustomer($con,
									array(
										"cid"=>$cid,
										"name"=>$name,
										"password"=>$password,
										"phone"=>$phone,
										"address"=>$address
									)
								);
		
		
		// Clear out the credit card information
		unsetFormSESSIONVariables();
		
		
		// Print success message
		addToMessages ("Registration Completed Successfully");
		
	}
	
	
	
?>
