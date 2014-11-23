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
	Configure SESSION variables
	**************************************************************/
	configureSESSIONVariables();



	/**************************************************************
	Connect to database
	**************************************************************/
	$con = connectToDatabase();
	


	/**************************************************************
	Perform requested operations from HTML form here
	**************************************************************/
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		
		// OPERATION: User pressed the "checkreceipt" button
		if (isset($_POST["checkreceipt"])) {
			 checkReceipt($_POST["receiptId"], $con);
		}
		
		// OPERATION: User pressed the "addtoreturns" button
		if (isset($_POST["addtoreturns"])) {
			addtoreturns($_POST["addtoreturns"], $con);
		}
		
		// OPERATION: User pressed the "removefromreturns" button
		if (isset($_POST["removefromreturns"])) {
			removefromreturns($_POST["removefromreturns"], $con);
		}
		
		// OPERATION: User pressed the "return" button
		if (isset($_POST["return"])) {
			returnItems($con);
		}
		
		// OPERATION: User pressed the "cancel" button
		if (isset($_POST["cancel"])) {
			unsetSESSIONVariables();
		}
		
	}
	
	
	/**************************************************************
	Perform all remaining database queries here
	**************************************************************/
	$purchasedItems = getPurchasedItems($con, $_SESSION["receiptId"]);
	$returnItems = getReturnItems($con, $_SESSION["returns"], $purchasedItems);
	$subtotal = getSubTotal($returnItems);
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
		function confirmQuantity(enteredQuantity, maxQuantity) {
			'use strict';
			if (maxQuantity == 0) {
				alert("All of these items have already been returned");
				return false;
			}
			if (enteredQuantity <= maxQuantity)
				return true;
			if (confirm("The maximum quantity you can return for this item is " + maxQuantity + ".\nDo you want to accept this quantity?"))
				return true;
			return false;
		}
	</script>

	
	
	<!-- Page title -->
	<title> Return </title>

	
	
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
				<h2 style="font-size: 1.4em;"> RETURN ITEMS </h2>
			</div>
			
			
			
			<div style="width:1000px;margin-left:auto; margin-right:auto; text-align:left;">
				<?php
					createReceiptForm();
				?>
				
				
				<div style="width:100%; text-align:center; padding-top:50px; padding-bottom:30px;">
					<h2 style="font-size: 1.1em;"> ITEMS PURCHASED </h2>
				</div>
			
			
				<?php
					createPurchasedItemsTable($purchasedItems);
				?>
				
				
				<div style="width:100%; text-align:center; padding-top:50px; padding-bottom:30px;">
					<h2 style="font-size: 1.1em;"> ITEMS TO RETURN </h2>
				</div>
			
			
				<?php
					createReturnItemsTable($returnItems);
				?>
				
				
				<div style="width:100%; text-align:center; padding-top:50px; padding-bottom:30px;">
					<h2 style="font-size: 1.1em;"> REFUND AMOUNT </h2>
				</div>


				<?php
					createPriceTable($subtotal, $gst, $pst, $total);
					createReturnCancelButtons();
				?>
			</div>
			

			
		</div>
		
		
		
	</div>
	
	
	
</div>



<?php



	/**************************************************************************
		Prints the form asking for the receiptId.
	**************************************************************************/
	function createReceiptForm() {
		echo ('
			<form action="return.php" method="post">
				<table style="margin-left:auto; margin-right:auto; text-align:left; border-bottom: 1px solid black; margin-bottom:10px">
					<tr>
						<td> RECEIPT ID: </td>
						<td> <input type="text" name="receiptId" value="' . $_SESSION["receiptId"] . '"> </td>
						<td> <input type="submit" name="checkreceipt" value="checkreceipt"> </td>
					</tr>
				</table>		
			</form>
		');
	}



	/**************************************************************************
		Prints the list of purchased items.
	**************************************************************************/
	function createPurchasedItemsTable($items) {
		
		echo ('
			<form action="return.php" method="post">
				<table style="width:100%; text-align:left; border-bottom: 1px solid black; margin-bottom:10px;">
					<tr>
						<td> UPC </td>
						<td> TITLE </td>
						<td> PRICE </td>
						<td> QUANTITY PURCHASED </td>
						<td> QUANTITY ALREADY RETURNED </td>
						<td> QUANTITY TO RETURN </td>
						<td style="width:130px"> </td>
					</tr>
		');
		for ($x = 0; $x < count($items); $x++) {
		
			$item = $items[$x];
			
			echo ("<tr>\n");
			echo ("<td> " . $item["upc"] . "</td>\n");
			echo ("<td> " . $item["title"] . "</td>\n");
			echo ("<td> " . $item["price"] . "</td>\n");
			echo ("<td> " . $item["quantitypurchased"] . "</td>\n");
			echo ("<td> " . $item["quantityalreadyreturned"] . "</td>\n");
			echo ('<td> <input type="text" id="quantity#' . $x . '"> </td>');
			echo ('
					<script>
						function confirmQuantity' . $x . '() {
							enteredQuantity = document.getElementById("quantity#' . $x . '").value;
							maxQuantity = ' . (intval($item["quantitypurchased"]) - intval($item["quantityalreadyreturned"])) . ';
							if (enteredQuantity <= maxQuantity) {
								finalQuantity = enteredQuantity;
							}
							else {
								finalQuantity = maxQuantity;
							}
							document.getElementById("button#' . $x . '").value = "' . $item["upc"] . '#' . '" + finalQuantity;
							return confirmQuantity(enteredQuantity, maxQuantity);
						}
					</script>
					<td> <button id="button#' . $x . '" onclick="return confirmQuantity' . $x . '()" name="addtoreturns" type="submit"> Add to Returns </button> </td>
				</tr>
			');
			
		}
		echo ('
				</table>		
			</form>
		');
	}



	/**************************************************************************
		Prints the list of items to return.
	**************************************************************************/
	function createReturnItemsTable($items) {
		
		echo ('
			<form action="return.php" method="post">
				<table style="width:100%; text-align:left; border-bottom: 1px solid black; margin-bottom:10px;">
					<tr>
						<td> UPC </td>
						<td> TITLE </td>
						<td> PRICE </td>
						<td> QUANTITY PURCHASED </td>
						<td> QUANTITY ALREADY RETURNED </td>
						<td> QUANTITY TO RETURN </td>
						<td style="width:130px"> </td>
					</tr>
		');
		for ($x = 0; $x < count($items); $x++) {
		
			$item = $items[$x];
			
			echo ("<tr>\n");
			echo ("<td> " . $item["upc"] . "</td>\n");
			echo ("<td> " . $item["title"] . "</td>\n");
			echo ("<td> " . $item["price"] . "</td>\n");
			echo ("<td> " . $item["quantitypurchased"] . "</td>\n");
			echo ("<td> " . $item["quantityalreadyreturned"] . "</td>\n");
			echo ("<td> " . $item["quantitytoreturn"] . "</td>\n");
			echo ('<td> <button name="removefromreturns" value = "' . $item["upc"] . '" type="submit"> Remove from Returns </button> </td>');
			echo ("</tr>\n");
		}
		echo ('
				</table>		
			</form>
		');
	}
	
	
	
	function createPriceTable($subtotal, $gst, $pst, $total) {
		echo ('
			<table style="width:100%; text-align:left; margin-bottom:10px; margin-top:10px;">
				<tr>
					<td style="width:100px;">
						SUBTOTAL:
					</td>
					<td>
						' . $subtotal . '
					</td>
					<td style="width:75%"> </td>
				</tr>
				<tr>
					<td>
						GST:
					</td>
					<td>
						' . $gst . '
					</td>
				</tr>
				<tr>
					<td>
						PST:
					</td>
					<td>
						' . $pst . '
					</td>
				</tr>
				<tr>
					<td>
						TOTAL REFUND:
					</td>
					<td>
						' . $total . '
					</td>
				</tr>
			</table>
		');
	}
	
	
	
	function createReturnCancelButtons() {
		echo ('
			<table style="width:100%;text-align:left; border-top: 1px solid black;">
				<form action="return.php" method="post">
					<tr>
						<td> <input type="submit" name="return" value="Confirm"> </td>
						<td style="text-align: right"> <input type="submit" name="cancel" value="Cancel"> </td>
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
		Clears the SESSION variables that stored the saved form data.
	**************************************************************************/
	function unsetSESSIONVariables() {
		$_SESSION["receiptId"] = "";
		$_SESSION["returns"] = array();
	}
	
	
	
	function checkReceipt($receiptId, $con) {
	
		// ERROR: no database connection
		if ($con == null) {
			return;
		}		
	
		$receipt = queryPurchase($con, $receiptId);
		
		if ($receipt == null) {
			addToMessages("Receipt Not Found");
			return;
		}

		if ($receipt["daysSincePurchase"] > 15) {
			addToMessages("Receipt Expired");
			return;
		}
		
		
		$_SESSION["receiptId"] = $receiptId;
				
	}
	
	
	
	function addToReturns($item, $con) {
	
		$array = explode('#', $item);
		
		// ERROR: user left the quantity field blank
		if (count($array) != 2) {
			addToMessages("You must specify a quantity");
			return;
		}
		
		$upc = $array[0];
	
		$quantity = $array[1];
	
		
		// ERROR: the quantity is not an integer
		if (!filter_var($quantity, FILTER_VALIDATE_INT)) {
			addToMessages("Invalid quantity");
			return;
		}
	
		removefromreturns($upc);
		
		array_push($_SESSION["returns"], array("upc"=>$upc, "quantity"=>$quantity));
		
	}



	/**************************************************************************
		Removes the specified item from the list of returns.
		No changes are made to the database.
		
		@param $upc
			The item to remove
	**************************************************************************/
	function removefromreturns($upc) {
	
		for($x = 0; $x < count($_SESSION["returns"]); $x++) {
			if ($_SESSION["returns"][$x]["upc"] == $upc) {
				array_splice($_SESSION["returns"], $x, 1);
				return;
			}
		}
						
	}
	
	
	
	/**************************************************************************
		Retrieves the full item information for each item that was purchased.
		No changes are made to the database.
		
		On success, an array of items is returned.
		
		@param $con
			The connection to the database
	**************************************************************************/
	function getPurchasedItems($con, $receiptId) {
	
		// ERROR: no database connection
		if ($con == null)
			return array();
		
		if (!isset($receiptId) || $receiptId == null || $receiptId == "")
			return array();
	
		// The item entities to return
		$purchaseItems = queryPurchaseItems($con, $receiptId);
		
		$returns = queryReturnsAssociatedWithReceiptId($con, $receiptId);	
		$returnItems = queryReturnItemsForMultipleReturns($con, array_column($returns, "retId"));
		
		$items = queryItems($con, array_column($purchaseItems, "upc"));
		
		$itemsAppended = array();
		for ($x = 0; $x < count($items); $x++) {
			$quantityalreadyreturned = 0;
			for ($y = 0; $y < count($returnItems); $y++) {
				if ($returnItems[$y]["upc"] == $items[$x]["upc"]) {
					$quantityalreadyreturned = $returnItems[$y]["quantity"];
					break;
				}
			}
			array_push($itemsAppended,
				array(
					"upc"=>$items[$x]["upc"],
					"title"=>$items[$x]["result"]["title"],
					"type"=>$items[$x]["result"]["type"],
					"category"=>$items[$x]["result"]["category"],
					"company"=>$items[$x]["result"]["company"],
					"year"=>$items[$x]["result"]["year"],
					"price"=>number_format($items[$x]["result"]["price"], 2),
					"stock"=>$items[$x]["result"]["stock"],
					"quantitypurchased"=>$purchaseItems[$x]["quantity"],
					"quantityalreadyreturned"=>$quantityalreadyreturned
				)
			);
		}
		
		return $itemsAppended;
		
	}
	
	
	
	/**************************************************************************
		Retrieves the full item information for each item that was purchased.
		No changes are made to the database.
		
		On success, an array of items is returned.
		
		@param $con
			The connection to the database
	**************************************************************************/
	function getReturnItems($con, $returnItems, $purchasedItems) {
	
		// The array of items to return
		$items = array();
	
		// The array containing the results of the query
		$result = array("upc"=>array(), "title"=>array(), "type"=>array(), "category"=>array(), "company"=>array(), "year"=>array(), "price"=>array(), "stock"=>array()); 
		
		// The prepared statement
		$query = $con->prepare("SELECT upc, title, type, category, company, year, price, stock FROM item WHERE upc=?");
		$query->bind_param("s", $upc);
		$query->bind_result($result["upc"], $result["title"], $result["type"], $result["category"], $result["company"], $result["year"], $result["price"], $result["stock"]);
		
		// Executing the query statement for every upc in the given array
		for($x = 0; $x < count($returnItems); $x++) {
		
			// Execute the query for the current upc
			$upc = $returnItems[$x]["upc"];
			$query->execute();
			$query->fetch();
			
			$quantitypurchased = 0;
			$quantityalreadyreturned = 0;
			for($y = 0; $y < count($purchasedItems); $y++) {
				if ($purchasedItems[$y]["upc"] == $upc) {
					$quantitypurchased = $purchasedItems[$y]["quantitypurchased"];
					$quantityalreadyreturned = $purchasedItems[$y]["quantityalreadyreturned"];
					break;
				}
			}
			
			array_push($items,
				array(
					"upc"=>$result["upc"],
					"title"=>$result["title"],
					"type"=>$result["type"],
					"category"=>$result["category"],
					"company"=>$result["company"],
					"year"=>$result["year"],
					"price"=>number_format($result["price"], 2),
					"quantitypurchased"=>$quantitypurchased,
					"quantityalreadyreturned"=>$quantityalreadyreturned,
					"quantitytoreturn"=>$returnItems[$x]["quantity"]
				)
			);
		}
		
		return $items;
	}

				
				
	/**************************************************************************
		Executes the return operation.
		Creates a new return for the customer, then creates a ReturnItem
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
	function returnItems($con) {
	
		// ERROR: no database connection
		if ($con == null) {
			return;
		}		
	
		// ERROR: No items are selected for return
		if (count($_SESSION["returns"]) == 0) {
			addToMessages("No items selected");
			return;
		}
		
		
		// Must be able to rollback if an error occurs
		$con->commit();
		$con->autocommit(FALSE);
		
		
		// Create the new return
		$returnId = insertIntoReturn($con, $_SESSION["receiptId"]);
		
		
		// Create new ReturnItem for each item to return
		insertIntoReturnItem($con, $returnId, $_SESSION["returns"]);
		

		// Commit the changes as one transaction
		$con->commit();
		$con->autocommit(TRUE);
		
		
		unsetSESSIONVariables();
		
		
		// Print success message
		addToMessages ("Return Completed Successfully!");
		addToMessages ("ReturnID: " . $returnId);
		
	}
	
	
	
	/**************************************************************************
		Calculates the subtotal
	**************************************************************************/
	function getSubTotal($items) {
	
		if (count($items) == 0)
			return "";
			
		$subtotal = 0;
		for ($x = 0; $x < count($items); $x++) {
			if ($items[$x]["price"] == "")
				return "";
			$subtotal += $items[$x]["price"] * $items[$x]["quantitytoreturn"];
		}
		
		return $subtotal;
		
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



	/**************************************************************************
		Misc SESSION variable configuration.
	**************************************************************************/
	function configureSESSIONVariables() {
	
		// If this is the first time the page is loaded (i.e. user has
		// not yet submitted a form), then clear out the saved data.
		if ($_SERVER["REQUEST_METHOD"] != "POST")
			unsetSESSIONVariables();

	}
	
	
	
?>
