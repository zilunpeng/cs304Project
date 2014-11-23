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
		
		// OPERATION: User pressed the "add to cart" button
		if (isset($_POST["addtocart"])) {
			addtocart($_POST["addtocart"]);
		}
		
		// OPERATION: User pressed the "remove from cart" button
		if (isset($_POST["removefromcart"])) {
			removeFromCart($_POST["removefromcart"]);
		}
		
		// OPERATION: User pressed the "search" button
		if (isset($_POST["search"])) {
			setFormSESSIONVariables();
		}
		
	}
	
	
	
	/**************************************************************
	Perform all remaining database queries here
	**************************************************************/
	$items = search($con, $_SESSION["title"], $_SESSION["category"], $_SESSION["leadsinger"], $_SESSION["quantity"]);
	$cartitems = getItemInformation($con, array_column($_SESSION["cart"], "upc"));
	
	
	
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
	<title> Shopping Cart </title>

	
	
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
		
		
		
			<div style="width:1000px; margin-left:auto; margin-right:auto; text-align:left;">
			
			
			
				<!-- Heading -->
				<div style="width:100%; text-align:center; padding-top:50px; padding-bottom:30px;">
					<h2 style="font-size: 1.4em;"> YOUR SHOPPING CART </h2>
				</div>
				
				
				
				<!-- Cart Table -->
				<?php
					createCartItemList($cartitems, array_column($_SESSION["cart"], "quantity"));
				?>
			
			
			
				<!-- Heading -->
				<div style="width:100%; text-align:center; padding-top:50px; padding-bottom:30px;">
					<h2 style="font-size: 1.1em;"> SEARCH FOR ITEMS </h2>
				</div>
				
				
				
				<div style="width:100%;margin-left:auto; margin-right:auto; text-align:left; padding-bottom:30px;">
					<?php
						createSearchForm($items);
					?>
				</div>
				
				
				
				<?php
					createItemList($items);
				?>
			
			
			
			</div>
			
			
			
		</div>
		
		
		
	</div>
	
	
	
</div>



<?php



	/**************************************************************************
		Prints the cart table.
	**************************************************************************/
	function createCartItemList($items, $quantities) {
		
		echo ('
			<form action="cart.php" method="post">
				<table style="width:100%; text-align:center; border-bottom: 1px solid black; margin-bottom:10px">
					<tr>
						<td> UPC </td>
						<td> TITLE </td>
						<td> TYPE </td>
						<td> CATEGORY </td>
						<td> COMPANY </td>
						<td> YEAR </td>
						<td> PRICE </td>
						<td> QUANTITY </td>
						<td> </td>
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



	/**************************************************************************
		Prints the search form.
	**************************************************************************/
	function createSearchForm($items) {
		echo ('
			<form action="cart.php" method="post">
				<table style="width:100%; text-align:left; margin-bottom:10px">
					<tr>
						<td> TITLE: </td>
						<td> <input type="text" name="title" value="' . $_SESSION["title"] . '"> </td>
						<td> CATEGORY: </td>
						<td>
							<select name="category" value="' . $_SESSION["category"] . '">');
		if ($_SESSION["category"] == "")
			echo ('<option value="none" selected> </option>');
		else
			echo ('<option value="none"> </option>');		
		if ($_SESSION["category"] == "rock")
			echo ('<option value="rock" selected> rock </option>');
		else
			echo ('<option value="rock"> rock </option>');		
		if ($_SESSION["category"] == "pop")
			echo ('<option value="pop" selected> pop </option>');
		else
			echo ('<option value="pop"> pop </option>');		
		if ($_SESSION["category"] == "rap")
			echo ('<option value="rap" selected> rap </option>');
		else
			echo ('<option value="rap"> rap </option>');		
		if ($_SESSION["category"] == "country")
			echo ('<option value="country" selected> country </option>');
		else
			echo ('<option value="country"> country </option>');		
		if ($_SESSION["category"] == "classical")
			echo ('<option value="classical" selected> classical </option>');
		else
			echo ('<option value="classical"> classical </option>');		
		if ($_SESSION["category"] == "new age")
			echo ('<option value="new age" selected> new age </option>');
		else
			echo ('<option value="new age"> new age </option>');		
		if ($_SESSION["category"] == "instrumental")
			echo ('<option value="instrumental" selected> instrumental </option>');
		else
			echo ('<option value="instrumental"> instrumental </option>');		
		echo ('
							</select>
						</td>
						<td> LEAD SINGER: </td>
						<td> <input type="text" name="leadsinger" value="' . $_SESSION["leadsinger"] . '"> </td>
						<td> QUANTITY: </td>
						<td> <input type="text" name="quantity" value="' . $_SESSION["quantity"] . '"> </td>
						<td> <input type="submit" name="search" value="Search"> </td>
					</tr>
				</table>		
			</form>
		');
	}



	/**************************************************************************
		Prints the search results table.
	**************************************************************************/
	function createItemList($items) {

		echo ('
			<form action="cart.php" method="post">
				<table style="width:100%; text-align:center; border-bottom: 1px solid black;">
					<tr>
						<td> UPC </td>
						<td> TITLE </td>
						<td> TYPE </td>
						<td> CATEGORY </td>
						<td> COMPANY </td>
						<td> YEAR </td>
						<td> PRICE </td>
						<td> LEAD SINGER </td>
						<td> </td>
					</tr>');
		for ($x = 0; $x < count($items); $x++) {
		
			$item = $items[$x];
			
			echo ("<tr>\n");
				echo ("<td> " . $item["upc"] . "</td>\n");
				echo ("<td> " . $item["title"] . "</td>\n");
				echo ("<td> " . $item["type"] . "</td>\n");
				echo ("<td> " . $item["category"] . "</td>\n");
				echo ("<td> " . $item["company"] . "</td>\n");
				echo ("<td> " . $item["year"] . "</td>\n");
				echo ("<td> " . $item["price"] . "</td>\n");
				echo ("<td> " . $item["leadsinger"] . "</td>\n");
			if ($item["stock"] < $_SESSION["quantity"])
				echo ('<td> <button onclick="return confirmQuantity(' . $item["stock"] . ')" name="addtocart" type="submit" value="' . $item["upc"] . '#' . $item["stock"] . '"> Add to Cart </button> </td>' . "\n");
			else
				echo ('<td> <button name="addtocart" type="submit" value="' . $item["upc"] . '#' . $_SESSION["quantity"] . '"> Add to Cart </button> </td>' . "\n");
			echo ("</tr>\n");
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
		Saves the contents of form elements into SESSION variables.
		
		Recall that when the user clicks a button in a form, the page is
		reloaded.  Normally, each time the page is reloaded, the form elements
		are blanked-out.
		
		However, by saving the contents of each form element into SESSION
		variables, the contents of the form elements can be restored to what
		they were before the page was reloaded.
	**************************************************************************/
	function setFormSESSIONVariables() {
		$_SESSION["title"] = $_POST["title"];
		$_SESSION["category"] = $_POST["category"];
		$_SESSION["leadsinger"] = $_POST["leadsinger"];
		$_SESSION["quantity"] = $_POST["quantity"];
		
		// ERROR: user left the quantity field blank
		if ($_SESSION["quantity"] == "") {
			addToMessages("You must enter a quantity");
		}
	}



	/**************************************************************************
		Clears the SESSION variables that stored the saved form data.
	**************************************************************************/
	function unsetFormSESSIONVariables() {
		$_SESSION["title"] = "";
		$_SESSION["category"] = "";
		$_SESSION["leadsinger"] = "";
		$_SESSION["quantity"] = "";
	}
	
	
	
	/**************************************************************************
		Retrieves the items that match the given search criteria.
		No changes are made to the database.
		
		On success, an array of items is returned.
		
		If an error occurs, an error message is printed, and an empty array is
		returned.
		
		@param $con
			The connection to the database
		
		@param $title, $category, $leadsinger, $quantity
			The search criteria
	**************************************************************************/
	function search($con, $title, $category, $leadsinger, $quantity) {

		// ERROR: no database connection
		if ($con == null) {
			return array();
		}
		
		// ERROR: user left the quantity field blank
		// NOTE: An error has already been printed by the
		// setFormSESSIONVariables() function.
		if (empty($quantity)) {
			return array();
		}
		
		// ERROR: the quantity is not an integer
		if (!filter_var($quantity, FILTER_VALIDATE_INT)) {
			addToMessages("Invalid quantity");
			return array();
		}
	
		// The item entities to return
		$items = array();

		// Generate query string
		$qstr = 'SELECT item.upc, title, type, category, company, year, price, stock, sname FROM item, leadsinger WHERE item.upc=leadsinger.upc';
		if ($title != "") {
			if ($category != "none") {
				if ($leadsinger != "") {
					$qstr = $qstr . ' AND title=? AND category=? AND sname=?';
					$query = $con->prepare($qstr);
					$query->bind_param("sss", $title, $category, $leadsinger);
				}
				else {
					$qstr = $qstr . ' AND title=? AND category=?';
					$query = $con->prepare($qstr);
					$query->bind_param("ss", $title, $category);
				}
			}
			else {
				if ($leadsinger != "") {
					$qstr = $qstr . ' AND title=? AND sname=?';
					$query = $con->prepare($qstr);
					$query->bind_param("ss", $title, $leadsinger);
				}
				else {
					$qstr = $qstr . ' AND title=?';
					$query = $con->prepare($qstr);
					$query->bind_param("s", $title);
				}
			}
		}
		else {
			if ($category != "none") {
				if ($leadsinger != "") {
					$qstr = $qstr . ' AND category=? AND sname=?';
					$query = $con->prepare($qstr);
					$query->bind_param("ss", $category, $leadsinger);
				}
				else {
					$qstr = $qstr . ' AND category=?';
					$query = $con->prepare($qstr);
					$query->bind_param("s", $category);
				}
			}
			else {
				if ($leadsinger != "") {
					$qstr = $qstr . ' AND sname=?';
					$query = $con->prepare($qstr);
					$query->bind_param("s", $leadsinger);
				}
				else {
					$query = $con->prepare($qstr);
				}
			}
		}
		
		// The query statement
		$result = array("upc"=>array(), "title"=>array(), "type"=>array(), "category"=>array(), "company"=>array(), "year"=>array(), "price"=>array(), "stock"=>array(), "leadsinger"=>array()); 
		$query->bind_result($result["upc"], $result["title"], $result["type"], $result["category"], $result["company"], $result["year"], $result["price"], $result["stock"], $result["leadsinger"]);

		// Execute query
		$query->execute();

		while ($query->fetch()) {

			array_push($items, array(
				"upc"=>$result["upc"],
				"title"=>$result["title"],
				"type"=>$result["type"],
				"category"=>$result["category"],
				"company"=>$result["company"],
				"year"=>$result["year"],
				"price"=>number_format($result["price"], 2),
				"stock"=>$result["stock"],
				"leadsinger"=>$result["leadsinger"])
			);
			
		}
		
		return $items;
		
	}



	/**************************************************************************
		Adds the specified item to the cart.
		No changes are made to the database.
		
		On success, a success message is printed, and the specified item is
		added to the cart array stored in $_SESSION["cart"].
		
		@param $item
			The a string of the form "upc#quantity"
	**************************************************************************/
	function addtocart($item) {
	
		$array = explode('#', $item);
		
		$upc = $array[0];
	
		$quantity = $array[1];
	
		removefromcart($upc);
		
		array_push($_SESSION["cart"], array("upc"=>$upc, "quantity"=>$quantity));
		
	}



	/**************************************************************************
		Removes the specified item from the cart.
		No changes are made to the database.
		
		@param $upc
			The item to remove
	**************************************************************************/
	function removefromcart($upc) {
	
		for($x = 0; $x < count($_SESSION["cart"]); $x++) {
			if ($_SESSION["cart"][$x]["upc"] == $upc) {
				array_splice($_SESSION["cart"], $x, 1);
				return;
			}
		}
						
	}



	/**************************************************************************
		Misc SESSION variable configuration.
	**************************************************************************/
	function configureSESSIONVariables() {
	
		// Initialize the shopping cart
		if (!isset($_SESSION["cart"]))
			$_SESSION["cart"] = array();
		
		// If this is the first time the page is loaded (i.e. user has
		// not yet submitted a form), then clear out the saved form data.
		if ($_SERVER["REQUEST_METHOD"] != "POST")
			unsetFormSESSIONVariables();

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
