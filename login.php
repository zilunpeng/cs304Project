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
	$successfulLogin = FALSE;
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		// OPERATION: User pressed the login button
		if (isset($_POST["login"])) {
			$successfulLogin = login($_POST["username"], $_POST["password"], $con);
		}
		
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
	</script>

	
	
	<!-- Page title -->
	<title> Login </title>

	
	
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
		
		
		
			<!-- Login Form -->
			<?php
				if ($successfulLogin == TRUE)
					printSuccessfulLogin();
				else
					createLoginForm();
			?>
			

			
		</div>
		
		
		
	</div>
	
	
	
</div>



<?php



	/**************************************************************************
		Prints the login form.
	**************************************************************************/
	function createLoginForm() {
		echo ('
			<div style="width:100%; text-align:center; padding-top:50px; padding-bottom:30px;">
				<h2 style="font-size: 1.4em;"> LOGIN </h2>
			</div>
			<form action="login.php" method="post">
				<table style="margin-left:auto; margin-right:auto; text-align:left;">
					<tr>
						<td> Username: </td>
						<td> <input type="text" name="username"> </td>
					</tr>
					<tr>
						<td> Password: </td>
						<td> <input type="password" name="password"> </td>
					</tr>
					<tr>
						<td> <input type="submit" name="login" value="login"> </td>
					</tr>
				</table>		
			</form>
		');
	}
	
	
	
	function printSuccessfulLogin() {
		echo('
			<div style="width:100%; text-align:center; padding-top:50px; padding-bottom:30px;">
				<h2 style="font-size: 1.4em;"> LOGIN SUCCESSFUL </h2>
			</div>
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
		Attempts to log the user in using the specified username and password.
		No changes are made to the database.
		
		On success, a success message is printed, and $_SESSION["username"]
		is set to the given username.  This super global variable stores the
		username of the currently logged in user.
		
		If an error occurs, an error message is printed and the user is not
		logged in.
		
		@param $username, $password
			The username and password given by the user.
			
		@param $con
			The MySQL connection
	**************************************************************************/
	function login($username, $password, $con) {
	
		// ERROR: no database connection
		if ($con == null) {
			return FALSE;
		}		
	
		// ERROR: user left the username field blank
		if (empty($username)) {
			addToMessages("You must enter a username");
			return FALSE;
		}
		
		// ERROR: user left the password field blank
		if (empty($password)) {
			addToMessages("You must enter a password");
			return FALSE;
		}
		
		$customer = queryCustomer($con, $username);
		
		// ERROR: user name was not found
		if ($customer == null) {
			addToMessages("Invalid username/password combination");
			return FALSE;
		}

		// ERROR: incorrect password
		if ($customer["password"] != $password) {
			addToMessages("Invalid username/password combination");
			return FALSE;
		}
		
		// SUCCESS
		$_SESSION["username"] = $username;
		
		return TRUE;
	}
	
	
	
?>
