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
		if (isset($_GET["exactdate"])) {
			global $items, $con;
			$items = getSalesReport($con, $_GET["exactdate"], "exact");
		} else if (isset($_GET["startdate"]) && isset($_GET["enddate"])) {
			// do ranged date stuff
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
			
				<div class="header">SALES REPORT</div>
				<form action="salesreport.php" method="get">
					<table>
						<tr><td align="right">Date</td><td align="left"><input type="text" id="datepicker" name="exactdate" value="<?php echo (isset($_GET["date"]) ? $_GET["date"] : "" );?>"></td><td><button type="submit">Apply</button></td></tr>
					</table>
				</form
				<!-- Items -->
				<?php global $items; showSalesReport($items); ?>

			</div>
		</div>
	</div>
</body>
</html>

<?php
	// MODEL
	// ===============

	function getSalesReport($con, $date, $queryType) {
		//$mysql_date = date( 'Y-m-d', strtotime($date) );

		if ($queryType = "exact") {
			$statement = $con->prepare("SELECT item.upc, SUM(quantity), title, type, category, company, year, price, stock FROM purchase INNER JOIN purchaseitem ON purchase.receiptId=purchaseitem.receiptId INNER JOIN item ON purchaseitem.upc=item.upc WHERE purchaseDate = STR_TO_DATE(?, '%m/%d/%Y') GROUP BY item.upc");
			$statement->bind_param("s", $date);
		} else if ($queryType = "range") {
			// do date range query
		}

		$statement->execute();
		$result = $statement->get_result();
		return $result;
	}
	
	function showSalesReport($items) {
		echo "<pre>";
		while ($row = mysqli_fetch_array($items, MYSQL_ASSOC)) {
			print_r($row);
		}
		echo "</pre>";
	}

?>
