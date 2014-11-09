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
		
		// OPERATION: User pressed the "remove from cart" button
		if (isset($_POST["removefromcart"])) {
			removeFromCart($_POST["removefromcart"]);
		}
		
	}
	
	
	
	/**************************************************************
	Perform all remaining database queries here
	**************************************************************/
	$items = getItemInformation($con, array_column($_SESSION["cart"], "upc"));
	
	
	
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
	<title> View Cart </title>

	
	
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
				<h2 style="font-size: 1.4em;"> VIEW CART </h2>
			</div>
			
			
			
			<!-- Cart Table -->
			<?php
				createItemList($items, array_column($_SESSION["cart"], "quantity"));
			?>
			

			
		</div>
		
		
		
	</div>
	
	
	
</div>



<?php



	/**************************************************************************
		Prints the cart table.
	**************************************************************************/
	function createItemList($items, $quantities) {
		
		echo ('
			<form action="cart.php" method="post">
				<table style="margin-left:auto; margin-right:auto; text-align:left;">
					<tr>
						<td style="font-size: 1.25em;"> UPC </td>
						<td style="font-size: 1.25em;"> TITLE </td>
						<td style="font-size: 1.25em;"> TYPE </td>
						<td style="font-size: 1.25em;"> CATEGORY </td>
						<td style="font-size: 1.25em;"> COMPANY </td>
						<td style="font-size: 1.25em;"> YEAR </td>
						<td style="font-size: 1.25em;"> PRICE </td>
						<td style="font-size: 1.25em;"> QUANTITY </td>
						<td style="font-size: 1.25em;"> </td>
					</tr>');
		for ($x = 0; $x < count($items); $x++) {
		
			$upc = $items[$x]["upc"];
			$quantity = $quantities[$x];
			$item = $items[$x]["result"];
			
			if ($item == null) {
				echo ("<tr>\n");
				echo ("<td> " . $upc . "</td>\n");
				echo ("<td> ITEM COULD NOT BE FOUND </td>\n");
				echo ("<td> </td>\n");
				echo ("<td> </td>\n");
				echo ("<td> </td>\n");
				echo ("<td> </td>\n");
				echo ("<td> </td>\n");
				echo ("<td> " . $quantity . "</td>\n");
				echo ("<td> </td>\n");
				echo ("</tr>\n");
			}
			else {
				echo ("<tr>\n");
				echo ("<td> " . $item["upc"] . "</td>\n");
				echo ("<td> " . $item["title"] . "</td>\n");
				echo ("<td> " . $item["type"] . "</td>\n");
				echo ("<td> " . $item["category"] . "</td>\n");
				echo ("<td> " . $item["company"] . "</td>\n");
				echo ("<td> " . $item["year"] . "</td>\n");
				echo ("<td> " . $item["price"] . "</td>\n");
				echo ("<td> " . $quantity . "</td>\n");
				echo ('<td> <button name="removefromcart" type="submit" value="' . $items[$x]["upc"] . '"> Remove from Cart </button> </td>' . "\n");
				echo ("</tr>\n");
			}
		}
		echo ('
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
		Removes the specified item from the cart.
		No changes are made to the database.
		
		On success, a success message is printed, and the specified item is
		removed from the cart array stored in $_SESSION["cart"].
		
		@param $upc
			The item to remove
	**************************************************************************/
	function removeFromCart($upc) {
	
		for($x = 0; $x < count($_SESSION["cart"]); $x++) {
			if ($_SESSION["cart"][$x]["upc"] == $upc) {
				array_splice($_SESSION["cart"], $x, 1);
				addToMessages ("Remove From Cart Completed Successfully");
				return;
			}
		}
						
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
	
	
	
?>
