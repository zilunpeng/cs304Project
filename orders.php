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

	$order = NULL;
	$items = NULL;
	
	// Perform requested operations from HTML form here
	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		if (isset($_GET["receiptID"])) {
			global $order, $items;
			$id = $_GET["receiptID"];
			$order = getOrder($con, $id);
			$items = getOrderItems($con, $id);
		}
	}
	
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
	<title>View Order</title>
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
				<div class="header">ORDER INFORMATION</div>
				<!-- Order Info -->
				<?php global $order, $items; printOrder($order); printOrder($items); ?>
			</div>
		</div>
	</div>
</body>
</html>

<?php
	// MODEL
	// ===============
	
	function getOrderItems($con, $id) {
		$statement = $con->prepare("SELECT * FROM purchaseitem INNER JOIN item ON purchaseitem.upc=item.upc WHERE receiptId = ?");
		$statement->bind_param("i", $id);
		$statement->execute();
		$result = $statement->get_result();
		return $result;
	}
	
	function getOrder($con, $id) {
		$statement = $con->prepare("SELECT * FROM purchase INNER JOIN customer ON purchase.cid=customer.cid WHERE purchase.receiptId = ?");
		$statement->bind_param("i", $id);
		$statement->execute();
		$result = $statement->get_result();
		return $result;
	}
	
	function printOrder($order) {
		echo "<pre>";
		while($row = mysqli_fetch_array($order, MYSQL_ASSOC)) {
			print_r($row);
		}
		echo "</pre>";
	}

?>
