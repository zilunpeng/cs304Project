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
	
	
	
	// Initialize the shopping cart
	if (!isset($_SESSION["cart"]))
		$_SESSION["cart"] = array();



	/**************************************************************
	Connect to database
	**************************************************************/
	$con = connectToDatabase();
	


	/**************************************************************
	Perform requested operations from HTML form here
	**************************************************************/
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		// OPERATION: User pressed the "purchase" button
		if (isset($_POST["purchase"])) {
			setFormSESSIONVariables();
			purchase($_POST["creditcardnumber"], $_POST["creditcardexpiry"], $con);
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
	$items = getItemInformation($con, array_column($_SESSION["cart"], "upc"));
	$subtotal = getSubTotal($items, array_column($_SESSION["cart"], "quantity"));
	$gst = getGST($subtotal);
	$pst = getPST($subtotal);
	$total = getTotal($subtotal, $gst, $pst);
	

	
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
	<title> Checkout </title>

	
	
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
			Error/Success Messages:<br\>
			
			
			
			<!-- Error/Success messages (if any) -->
			<?php
				printMessages();
			?>
			
			
			
		</div>

		

		<div class="content">
		
		
		
			<!-- Heading -->
			<div style=" width:100%; text-align:center; padding-top:50px; padding-bottom:30px;">
				<h2 style="font-size: 1.4em;"> CHECKOUT </h2>
			</div>
			
			
			
			<div style="width:600px; margin-left:auto; margin-right:auto; text-align:left;">
				<div style="width:100%; text-align:center; padding-top:20px;">
				
					<?php
						createItemList($items, array_column($_SESSION["cart"], "quantity"));
					?>
				
				</div>
				
				<div style="width:100%; text-align:center; padding-top:20px;">
				
					<?php
						createPriceTable($subtotal, $gst, $pst, $total);
					?>
				
				</div>
				
				<div style="width:100%; text-align:center; padding-top:20px;">
				
					<?php
						createPurchaseForm();
					?>
				
				</div>
			</div>
			

			
		</div>
		
		
		
	</div>
	
	
	
</div>



<?php



	/**************************************************************************
		Prints the bill and the purchase button.
	**************************************************************************/
	function createItemList($items, $quantities) {
		
		echo ('
				<table style="width:100%; text-align:left">
					<tr>
						<td> UPC </td>
						<td> TITLE </td>
						<td> PRICE </td>
						<td> QUANTITY </td>
					</tr>
		');
		for ($x = 0; $x < count($items); $x++) {
		
			$upc = $items[$x]["upc"];
			$quantity = $quantities[$x];
			$item = $items[$x]["result"];
			
			if ($item == null) {
				echo ("<tr>\n");
				echo ("<td> " . $upc . "</td>\n");
				echo ("<td> ITEM COULD NOT BE FOUND </td>\n");
				echo ("<td> </td>\n");
				echo ("<td> " . $quantity . "</td>\n");
				echo ("</tr>\n");
			}
			else {
				echo ("<tr>\n");
				echo ("<td> " . $item["upc"] . "</td>\n");
				echo ("<td> " . $item["title"] . "</td>\n");
				echo ("<td> " . $item["price"] . "</td>\n");
				echo ("<td> " . $quantity . "</td>\n");
				echo ("</tr>\n");
			}
		}
		echo ('
			</table>
		');
	}
	
	
	
	function createPriceTable($subtotal, $gst, $pst, $total) {
		echo ('
			<table style="width:100%; text-align:left; border-top: 1px solid black; border-bottom: 1px solid black;">
				<tr>
					<td style="width:25%">
						SUBTOTAL:
					</td>
					<td style="width:25%">
						' . $subtotal . '
					</td>
					<td style="width:50%"> </td>
				</tr>
				<tr>
					<td style="width:25%">
						GST:
					</td>
					<td style="width:25%">
						' . $gst . '
					</td>
					<td style="width:50%"> </td>
				</tr>
				<tr>
					<td style="width:25%">
						PST:
					</td>
					<td style="width:25%">
						' . $pst . '
					</td>
					<td style="width:50%"> </td>
				</tr>
				<tr>
					<td style="width:25%">
						TOTAL:
					</td>
					<td style="width:25%">
						' . $total . '
					</td>
					<td style="width:50%"> </td>
				</tr>
			</table>
		');
	}
	
	
	
	function createPurchaseForm() {
		echo ('
			<table style="width:100%;text-align:left;">
				<form action="checkout.php" method="post">
					<tr>
						<td style="width:140px;"> Credit Card Number: </td>
						<td style="width:100px; padding-right:5px;"> <input type="text" maxlength="16" name="creditcardnumber"> </td>
						<td style="width:40%"> </td>
					</tr>
					<tr>
						<td> Card Expiry (mm/yy): </td>
						<td> <input type="text" name="creditcardexpiry" maxlength="5"> </td>
					</tr>
					<tr>
						<td> <input type="submit" name="purchase" value="Purchase"> </td>
					</tr>
				</form>
			</table>
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
		$_SESSION["creditcardnumber"] = $_POST["creditcardnumber"];
		$_SESSION["creditcardexpiry"] = $_POST["creditcardexpiry"];
	}



	/**************************************************************************
		Clears the SESSION variables that stored the saved form data.
	**************************************************************************/
	function unsetFormSESSIONVariables() {
		$_SESSION["creditcardnumber"] = "";
		$_SESSION["creditcardexpiry"] = "";
	}
	
	
	
	/**************************************************************************
		Retrieves the full item information for each item in the cart.
		No changes are made to the database.
		
		On success, an array of items is returned.
		
		If an error occurs, an error message is printed, and an empty array is
		returned.
		
		@param $con
			The connection to the database
	**************************************************************************/
	function getItemInformation($con, $upcs) {
	
		// ERROR: no database connection
		if ($con == null)
			return array();
		
		if (!isset($upcs) || $upcs == null)
			return array();
	
		// The item entities to return
		$items = queryItems($con, $upcs);
		
		return $items;
		
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
	function purchase($creditcardnumber, $creditcardexpiry, $con) {
	
		// ERROR: no database connection
		if ($con == null) {
			return;
		}		
	
		// ERROR: User not logged in
		if (!isset($_SESSION["username"]) || $_SESSION["username"] == NULL) {
			addToMessages("You must log in to complete purchase");
			return;
		}
	
		// ERROR: The shopping cart is empty
		if (count($_SESSION["cart"]) == 0) {
			addToMessages("Your cart is empty");
			return;
		}
		
		// ERROR: No credit card number was given
		if (empty($creditcardnumber)) {
			addToMessages("You must enter a credit card number");
			return;
		}
		
		// ERROR: No credit card expiry was given
		if (empty($creditcardexpiry)) {
			addToMessages("You must enter an expiry date");
			return;
		}
		
		// ERROR: The credit card number is not correct format
		if (!filter_var($creditcardnumber, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>'/^[0-9]{16,16}$/')))) {
			addToMessages("Invalid credit card number");
			return;
		}
		
		// Get SQL-compatible date string for the credit card expiry
		$expiryDate = getFormattedExpiryDate($creditcardexpiry);
		
		// ERROR: The credit card expiry is not correct format
		if ($expiryDate == NULL) {
			addToMessages("Invalid credit card expiry");
			return;
		}
		
		if (checkIfExpired($con, $expiryDate)) {
			addToMessages("Credit card has expired");
			return;
		}

		
		
		// Must be able to rollback if an error occurs
		$con->commit();
		$con->autocommit(FALSE);
		
		
		
		// Create the new purchase
		$receiptId = insertIntoPurchase($con, $_SESSION["username"], $creditcardnumber, $expiryDate);
		
		
		// Get the newly created purchase
		$purchase = queryPurchase($con, $receiptId);
		
		
		// Create new PurchaseItem for each item in the cart
		insertIntoPurchaseItem($con, $purchase["receiptId"], $_SESSION["cart"]);
		

		// Commit the changes as one transaction
		$con->commit();
		$con->autocommit(TRUE);
		
		
		// Empty the shopping cart
		$_SESSION["cart"] = array();
		
		
		// Clear out the credit card information
		unsetFormSESSIONVariables();
		
		
		// Print success message
		addToMessages ("Purchase Completed Successfully!");
		addToMessages ("ReceiptID: " . $purchase["receiptId"]);
		addToMessages ("Estimated Delivery Date: " . $purchase["expectedDate"]);
		
	}
	
	
	
	/**************************************************************************
		Calculates the subtotal
	**************************************************************************/
	function getSubTotal($items, $quantities) {
	
		if (count($items) == 0)
			return "";
			
		$subtotal = 0;
		for ($x = 0; $x < count($items); $x++) {
			if ($items[$x]["result"]["price"] == "")
				return "";
			$subtotal += $items[$x]["result"]["price"] * $quantities[$x];
		}
		
		return number_format($subtotal, 2);
		
	}
	
	
	
	/**************************************************************************
		Calculates the gst
	**************************************************************************/
	function getGST($subtotal) {
	
		if ($subtotal == "")
			return "";
			
		return number_format($subtotal * 0.05, 2);
		
	}
	
	
	
	/**************************************************************************
		Calculates the pst
	**************************************************************************/
	function getPST($subtotal) {
	
		if ($subtotal == "")
			return "";
			
		return number_format($subtotal * 0.07, 2);
		
	}
	
	
	
	/**************************************************************************
		Calculates the total
	**************************************************************************/
	function getTotal($subtotal, $gst, $pst) {
	
		if ($subtotal == "")
			return "";
			
		return number_format($subtotal + $gst + $pst, 2);
		
	}
	
	
	
?>
