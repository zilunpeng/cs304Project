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

	// Perform requested operations from HTML form here
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		// OPERATION: User pressed the "remove from cart" button
		if (isset($_POST["removefromcart"])) {
			removeFromCart($_POST["removefromcart"]);
		}
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
	<title> View Cart </title>
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
				<!-- Heading -->
				<div class="header">
					ITEMS LIST
				</div>
				<!-- Items -->
				<?php createItemList($items); ?>
				
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

	function createAddItemForm() {
	
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
			echo "<td>" . $row["price"] . "</td>";
			echo "<td>" . $row["stock"] . "</td>";
			echo "</tr>";
		}
		echo "</table>";
		// echo "</pre>";
	}
	
	function editItemCost() {
	
	}
	
	function getItems($con) {
		$query = "SELECT * FROM item";
		$result = mysqli_query($con, $query);
		return $result;
	}

?>
