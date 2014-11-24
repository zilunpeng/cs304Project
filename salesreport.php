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
	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		if (isset($_GET["date"])) {
			global $items, $con;
			$items = getSalesReport($con, $_GET["date"]);
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
			
				<div class="heading">SALES REPORT</div>
				<form action="salesreport.php" method="get">
					<table>
						<tr><td align="right" style="padding-right:5px;">Date</td><td align="left"><input type="text" id="datepicker" name="date" value="<?php echo (isset($_GET["date"]) ? $_GET["date"] : "" );?>"></td><td><button type="submit">Apply</button></td></tr>
					</table>
				</form>
				<br>
				<!-- Items -->
				<?php if (isset($_GET["date"])) { global $items; showSalesReport($items); } ?>

			</div>
		</div>
	</div>
</body>
</html>

<?php
	// MODEL
	// ===============

	function getSalesReport($con, $date) {
		//$mysql_date = date( 'Y-m-d', strtotime($date) );

		$statement = $con->prepare("SELECT item.upc, category, price, SUM(purchaseitem.quantity), price*SUM(purchaseitem.quantity) FROM purchase INNER JOIN purchaseitem ON purchase.receiptId=purchaseitem.receiptId INNER JOIN item ON purchaseitem.upc=item.upc WHERE purchaseDate = STR_TO_DATE(?, '%m/%d/%Y') GROUP BY item.upc ORDER BY category");
		$statement->bind_param("s", $date);

		$statement->execute();
		$result = $statement->get_result();
		return $result;
	}
	
	function showSalesReport($items) {
		echo "<table style='width:100%; text-align:center;'>";
		echo "<tr style=\"font-weight: bold\">";
		echo "<th>UPC</th>";
		echo "<th>Category</th>";
		echo "<th>Price</th>";
		echo "<th>Units</th>";
		echo "<th>Total Value</th>";

		$currentCategory = "";
		$categoryCount = 0;
		$categoryTotal = 0;
		$dailyTotal = 0;
		$dailyCount = 0;
		
		while ($row = mysqli_fetch_array($items, MYSQL_ASSOC)) {
			if ($currentCategory != $row["category"]) {
				if ($currentCategory != "")
					showCategoryTotal($categoryCount, $categoryTotal);
				$currentCategory = $row["category"];
				$categoryCount = 0;
				$categoryTotal = 0;
			}
			
			$categoryCount += $row["SUM(purchaseitem.quantity)"];
			$categoryTotal += $row["price*SUM(purchaseitem.quantity)"];
			
			$dailyCount += $categoryCount;
			$dailyTotal += $categoryTotal;
			
			echo "<tr>";
			echo "<td>" . $row["upc"] . "</td>";
			echo "<td>" . $row["category"] . "</td>";
			echo "<td>" . number_format($row["price"],2) . "</td>";
			echo "<td>" . $row["SUM(purchaseitem.quantity)"] . "</td>";
			echo "<td>" . number_format($row["price*SUM(purchaseitem.quantity)"],2) . "</td>";
			echo "</tr>";
		}
		
		showCategoryTotal($categoryCount, $categoryTotal);
		
		echo "<tr class=\"blank_row\"><td colspan=\"5\"></td></tr>";
		echo "<td colspan=\"2\"><td class=total>Daily Total</td>";
		echo "<td class=total>" . $dailyCount . "</td>";
		echo "<td class=total>" . number_format($dailyTotal,2) . "</td>";
		
		echo "</table>";
	}
	
	function showCategoryTotal($count, $total) {
		echo "<tr>";
		echo "<td colspan=\"2\"><td class=total>Total</td>";
		echo "<td class=total>" . $count . "</td>";
		echo "<td class=total>" . number_format($total,2) . "</td>";
		echo "</tr>";
		echo "<tr class=\"blank_row\"><td colspan=\"5\"></td></tr>";
	}

?>
