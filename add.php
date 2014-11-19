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
				Error/Success Messages:<br>

				<!-- Error/Success messages (if any) -->
				<?php printMessages(); ?>
			</div>

			<div class="content">
			
				<div class="header">ITEMS LIST</div>
				<!-- Items -->
				<?php createItemList($items); ?>
				
				<div class="header">ADD ITEM</div>
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
		$statement = $con->prepare("INSERT INTO item (upc,title,type,category,company,year,price,stock) VALUES (?,?,?,?,?,?,?,?)");
		$statement->bind_param("sssssidi",$item["upc"],$item["title"],$item["type"],$item["category"],$item["company"],$item["year"],$item["price"],$item["stock"]);
		$result = $statement->execute();
		
		if ($result)
			addToMessages("Item successfully added");
		else {
			global $formIsValid;
			addToMessages("Error adding item");
			$formIsValid = false;
		}
	}
	
	function createAddItemForm() {
		global $itemToAdd, $formIsValid;
		?>
		<form action="add.php" method="post">
		<table>
			<tr><td align="right">UPC</td><td align="left"><input type="text" name="upc" value="<?php echo ((isset($itemToAdd["upc"]) && ($formIsValid == false)) ? $itemToAdd["upc"] : "") ?>"></td></tr>
			<tr><td align="right">Title</td><td align="left"><input type="text" name="title" value="<?php echo ((isset($itemToAdd["title"]) && ($formIsValid == false)) ? $itemToAdd["title"] : "") ?>"></td></tr>
			<tr><td align="right">Type</td><td align="left"><input type="text" name="type" value="<?php echo ((isset($itemToAdd["type"]) && ($formIsValid == false)) ? $itemToAdd["type"] : "") ?>"></td></tr>
			<tr><td align="right">Category</td><td align="left"><input type="text" name="category" value="<?php echo ((isset($itemToAdd["category"]) && ($formIsValid == false)) ? $itemToAdd["category"] : "") ?>"></td></tr>
			<tr><td align="right">Company</td><td align="left"><input type="text" name="company" value="<?php echo ((isset($itemToAdd["company"]) && ($formIsValid == false)) ? $itemToAdd["company"] : "") ?>"></td></tr>
			<tr><td align="right">Year</td><td align="left"><input type="text" name="year" value="<?php echo ((isset($itemToAdd["year"]) && ($formIsValid == false)) ? $itemToAdd["year"] : "") ?>"></td></tr>
			<tr><td align="right">Price</td><td align="left"><input type="text" name="price" value="<?php echo ((isset($itemToAdd["price"]) && ($formIsValid == false)) ? number_format($itemToAdd["price"],2) : "") ?>"></td></tr>
			<tr><td align="right">Stock</td><td align="left"><input type="text" name="stock" value="<?php echo ((isset($itemToAdd["stock"]) && ($formIsValid == false)) ? $itemToAdd["stock"] : "") ?>"></td></tr>
			<tr><td></td><td><input type="submit"></td></tr>
			</table>
		</form>
		<?php
	}
	
	function createItemList($items) {
		// echo "<pre>";
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
			// print_r($row);
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
		// echo "</pre>";
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
		if (isset($_POST["title"]) && $_POST["title"] != "")
			$itemToAdd["title"] = $_POST["title"];
		else {
			$formIsValid = false;
			addToMessages("Title cannot be empty");
		}
		if (isset($_POST["type"]) && $_POST["type"] != "")
			$itemToAdd["type"] = $_POST["type"];
		else {
			$formIsValid = false;
			addToMessages("Type cannot be empty");
		}
		if (isset($_POST["category"]) && $_POST["category"] != "")
			$itemToAdd["category"] = $_POST["category"];
		else {
			$formIsValid = false;
			addToMessages("Category cannot be empty");
		}
		if (isset($_POST["company"]) && $_POST["company"] != "")
			$itemToAdd["company"] = $_POST["company"];
		else {
			$formIsValid = false;
			addToMessages("Company cannot be empty");
		}
		if (isset($_POST["year"]) && $_POST["year"] != "")
			$itemToAdd["year"] = $_POST["year"];
		else {
			$formIsValid = false;
			addToMessages("Year cannot be empty");
		}
		if (isset($_POST["price"]) && $_POST["price"] != "")
			$itemToAdd["price"] = $_POST["price"];
		else {
			$formIsValid = false;
			addToMessages("Price cannot be empty");
		}
		if (isset($_POST["stock"]) && $_POST["stock"] != "")
			$itemToAdd["stock"] = $_POST["stock"];
		else {
			$formIsValid = false;
			addToMessages("Stock cannot be empty");
		}
	}

?>
