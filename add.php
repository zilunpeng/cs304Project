<?php
/*
Each webpage in this project conforms to the Model View Controller (MVC) architecture.
Hence, each page is broken down into three parts:
	1. The Model: manages the operations, database transactions, etc.
	2. The View: the user interface.
	3. The Controller: processes commands from the user (typically from HTML forms)
	   and sends commands to the model.
*/

	// CONTROLLER
	// ===============
	
	// The Common PHP Functions
	include "includes/commonfunctions.php";

	// The Common PHP transactions
	include "includes/commontransactions.php";

	session_start();

	// Connect to database
	$con = connectToDatabase();

	$itemToAdd = array();
	$formIsValid = true;
	
	// Perform requested operations from HTML form here
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		global $itemToAdd, $formIsValid, $con;
		handleAddItem();
		if ($formIsValid)
			addItem($con, $itemToAdd);
	}
	
	// Perform all remaining database queries here
	$items = getItems($con);
	
	// Close database connection
	disconnectFromDatabase($con)
?>

<?
	// VIEW
	// ===============
?>
<html>
<head>
	<!-- Meta -->
	<meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<meta content="utf-8" http-equiv="encoding">
	
	<!-- Importing CSS and JavaScript -->
	<link rel="stylesheet" type="text/css" href="includes/styles.css" />
	<link rel="text/javascript" type="text/css" href="includes/commonfunctions.css" />
	
	<!-- Additional JavaScript functions -->
	<script></script>

	<!-- Page title -->
	<title>Manage Items</title>
</head>

<body>
	<div class="container">
		<!-- The left portion of the page -->
		<div class="sidebar">
			<?php include("includes/sidebar.php"); ?>
		</div>
		
		<!-- The right (main) portion of the page -->
		<div class="main">

			<!-- Designated area for all error/success messages (top of page) -->
			<div class="messages">
				<!-- heading -->
				<strong>Error/Success Messages:</strong><br>

				<!-- Error/Success messages (if any) -->
				<?php printMessages(); ?>
			</div>

			<div class="content">
			
				<div class="heading">ITEMS LIST</div>
				<!-- Items -->
				<?php createItemList($items); ?>
				
				<br>
				<div class="heading">UPDATE ITEM</div>
				<!-- Add Item Form -->
				<?php createAddItemForm(); ?>
			</div>
		</div>
	</div>
</body>
</html>

<?php
	// MODEL
	// ===============

	function addItem($con, $item) {
		if (isset($item["price"])) {
			$statement = $con->prepare("UPDATE item SET price=?, stock=stock+? WHERE upc=?");
			$statement->bind_param("dis",$item["price"],$item["qty"],$item["upc"]);
		} else {
			$statement = $con->prepare("UPDATE item SET stock=stock+? WHERE upc=?");
			$statement->bind_param("is",$item["qty"],$item["upc"]);
		}
		
		if (queryItem($con, $item["upc"]) != NULL) {
			$result = $statement->execute();	
			if ($result)
				addToMessages("Item UPC=" . $item["upc"] . " has been successfully updated");
			else {
				global $formIsValid;
				addToMessages("Unknown error encountered while updating item");
				$formIsValid = false;
			}
		} else {
			addToMessages("Item with UPC=".$item["upc"]." does not exist");
		}
	}
	
	function createAddItemForm() {
		global $itemToAdd, $formIsValid;
		?>
		<form action="add.php" method="post">
		<table>
			<tr><td align="right" style="padding-right: 5px;">UPC</td><td align="left"><input type="text" name="upc" value="<?php echo ((isset($itemToAdd["upc"]) && ($formIsValid == false)) ? $itemToAdd["upc"] : "") ?>"></td></tr>
			<tr><td align="right" style="padding-right: 5px;">Price</td><td align="left"><input type="text" name="price" value="<?php echo ((isset($itemToAdd["price"]) && ($formIsValid == false)) ? number_format($itemToAdd["price"],2) : "") ?>"></td></tr>
			<tr><td align="right" style="padding-right: 5px;">Quantity</td><td align="left"><input type="text" name="qty" value="<?php echo ((isset($itemToAdd["qty"]) && ($formIsValid == false)) ? $itemToAdd["qty"] : "") ?>"></td></tr>
			<tr><td></td><td><input type="submit" value="Update"></td></tr>
			</table>
		</form>
		<?php
	}
	
	function createItemList($items) {
		echo "<table style='width:100%; text-align:center;'>";
		echo "<tr>";
		echo "<th>UPC</th>";
		echo "<th>Title</th>";
		echo "<th>Type</th>";
		echo "<th>Category</th>";
		echo "<th>Company</th>";
		echo "<th>Year</th>";
		echo "<th>Price</th>";
		echo "<th>Stock</th>";
		while ($row = mysqli_fetch_array($items, MYSQL_ASSOC)) {
			echo "<tr>";
			echo "<td>" . $row["upc"] . "</td>";
			echo "<td>" . $row["title"] . "</td>";
			echo "<td>" . $row["type"] . "</td>";
			echo "<td>" . $row["category"] . "</td>";
			echo "<td>" . $row["company"] . "</td>";
			echo "<td>" . $row["year"] . "</td>";
			echo "<td>" . number_format($row["price"],2) . "</td>";
			echo "<td>" . $row["stock"] . "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}

	function getItems($con) {
		// Currently defaults to limit of 25 items. Need to add pagination, or change LIMIT in the query
		$query = "SELECT * FROM item";
		$result = mysqli_query($con, $query);
		return $result;
	}
	
	function handleAddItem() {
		GLOBAL $itemToAdd, $formIsValid;
		
		if (isset($_POST["upc"]) && $_POST["upc"] != "")
			$itemToAdd["upc"] = $_POST["upc"];
		else {
			$formIsValid = false;
			addToMessages("UPC cannot be empty");
		}
		if (isset($_POST["price"]) && $_POST["price"] != "") {
			if (is_numeric($_POST["price"]))
				$itemToAdd["price"] = $_POST["price"];
			else {
				$formIsValid = false;
				addToMessages("Price is invalid");
			}
		}
		if (isset($_POST["qty"]) && $_POST["qty"] != "") 
			$itemToAdd["qty"] = $_POST["qty"];
		else {
			$formIsValid = false;
			addToMessages("Quantity cannot be empty");
		}
	}

?>
