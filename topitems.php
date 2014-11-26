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

	$number = 5;
	
	// Perform requested operations from HTML form here
	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		$validQuery = false;
		if (isset($_GET["date"]) && $_GET["date"] != "") {
			$validQuery = true;
		} else {
			addToMessages("You must enter a date");
		}
		if (isset($_GET["number"]) && $_GET["number"] != "") {
			global $number;
			$number = $_GET["number"];
		} else {
			addToMessages("Number of items to list was not entered. Defaulted to 5");
		}

		if ($validQuery) {
			global $items, $con;
			$items = getSalesReport($con, $_GET["date"], $number);
		}
	}
	
	// Perform all remaining database queries here

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
	
	<!-- Datepicker Stuff -->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.10.2.js"></script>
	<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
	
	<!-- Additional JavaScript functions -->
	<script>
	$(function() {
		$( "#datepicker" ).datepicker();
	});
	</script>

	<!-- Page title -->
	<title>Daily Sales Report</title>
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
				Error/Success Messages:<br>

				<!-- Error/Success messages (if any) -->
				<?php printMessages(); ?>
			</div>

			<div class="content">
			
				<div class="heading">TOP ITEMS</div>
				<form action="topitems.php" method="get">
					<table>
						<tr><td align="right" style="padding-right:5px;">Date</td><td align="left"><input type="text" id="datepicker" name="date" value="<?php echo (isset($_GET["date"]) ? $_GET["date"] : "" );?>" style="margin-right:20px;"></td><td>Number of Items</td><td><input type="text" name="number" style="width:40px;margin-right:20px;" value="<?php echo (isset($_GET["number"]) ? $_GET["number"] : "5" );?>"></td><td><button type="submit">Apply</button></td></tr>
					</table>
				</form>
				<br>
				<!-- Items -->
				<?php if ($validQuery) { global $items; showSalesReport($items); } ?>

			</div>
		</div>
	</div>
</body>
</html>

<?php
	// MODEL
	// ===============

	function getSalesReport($con, $date, $number) {
		//$mysql_date = date( 'Y-m-d', strtotime($date) );
		$statement = $con->prepare("SELECT title, company, stock, quantity FROM purchase INNER JOIN purchaseitem ON purchase.receiptId=purchaseitem.receiptId INNER JOIN item ON purchaseitem.upc=item.upc WHERE purchaseDate = STR_TO_DATE(?, '%m/%d/%Y') GROUP BY item.upc ORDER BY purchaseitem.quantity DESC LIMIT ?");
		$statement->bind_param("si", $date, $number);

		$statement->execute();
		$result = $statement->get_result();
		return $result;
	}
	
	function showSalesReport($items) {
		echo "<table style='width:100%; text-align:center;'>";
		echo "<tr style=\"font-weight: bold\">";
		echo "<th>#</th>";
		echo "<th>Title</th>";
		echo "<th>Company</th>";
		echo "<th>Amount in Stock</th>";
		echo "<th>Quantity Sold</th>";

		$count = 0;
		
		while ($row = mysqli_fetch_array($items, MYSQL_ASSOC)) {
			$count += 1;
			echo "<tr>";
			echo "<td>" . $count . "</td>";
			echo "<td>" . $row["title"] . "</td>";
			echo "<td>" . $row["company"] . "</td>";
			echo "<td>" . $row["stock"] . "</td>";
			echo "<td>" . $row["quantity"] . "</td>";
			echo "</tr>";
		}
		
		echo "</table>";
	}

?>
