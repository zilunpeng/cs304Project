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

	// Perform all remaining database queries here
	$orders = getOrders($con);
	
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
	<title>View Orders</title>
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
			
				<div class="heading">ORDERS</div>
				<!-- Orders -->
				<?php global $orders; printOrders($orders); ?>
				
			</div>
		</div>
	</div>
</body>
</html>

<?php
	// MODEL
	// ===============

	function getOrders($con) {
		$query = "SELECT * FROM purchase INNER JOIN customer ON purchase.cid=customer.cid";
		$result = mysqli_query($con, $query);
		return $result;
	}

	function printOrders($orders) {
		echo "<table style='width:100%; text-align:center;'>";
		echo "<tr style=\"font-weight: bold\">";
		echo "<th>Order ID</th>";
		echo "<th>Customer Name</th>";
		echo "<th>Expected Delivery Date</th>";
		echo "<th>Date Delivered</th>";
		
		while($row = mysqli_fetch_array($orders, MYSQL_ASSOC)) {
			echo "<tr>";
			echo "<td><a href=\"order.php?receiptId=".$row["receiptId"]."\">" . $row["receiptId"] . "</a></td>";
			echo "<td>" . $row["name"] . "</td>";
			echo "<td>" . $row["expectedDate"] . "</td>";
			echo "<td>" . $row["deliveredDate"] . "</td>";
			echo "</tr>";
		}
		
		echo "</table>";
	}
?>