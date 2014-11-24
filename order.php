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
		if (isset($_POST["date"]))
			setDate($con, $_POST["receiptId"], $_POST["date"]);
		if (isset($_POST["receiptId"])) {
			global $id;
			$id = $_POST["receiptId"];
		}
	}
	
	if ($_SERVER["REQUEST_METHOD"] == "GET") {
		if (isset($_GET["receiptId"])) {
			global $id;
			$id = $_GET["receiptId"];
		}
	}
	
	$order = getOrder($con, $id);
	$items = getOrderItems($con, $_GET["receiptId"]);
	
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
		// Force loading of the datepicker after document is ready
		$(document).ready(function() {
			$("#datepicker").datepicker();
		});
	</script>

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
				<div class="heading">ORDER INFORMATION</div>
				
				<br>
				<!-- Order Info -->
				<div class=heading>Customer Info</div>
				<?php global $order; printOrderInfo($order); ?>
				<br>
				<?php printSetDateForm(); ?>
				<br>
				<!-- Items Ordered -->
				<div class="heading">Items Ordered</div>
				<?php global $items; printOrderItems($items); ?>
			</div>
		</div>
	</div>
</body>
</html>

<?php
	// MODEL
	// ===============
	
	function getOrderItems($con, $id) {
		$statement = $con->prepare("SELECT *, quantity*stock FROM purchaseitem INNER JOIN item ON purchaseitem.upc=item.upc WHERE receiptId = ?");
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
	
	function printOrderInfo($order) {		
		echo "<table style='width:100%; text-align:left;'>";
		echo "<tr style=\"font-weight: bold\">";
		echo "<th>Customer Name</th>";
		echo "<th>Phone #</th>";
		echo "<th>Address</th>";
		echo "<th>Expected Delivery Date</th>";
		echo "<th>Date Delivered</th>";
		
		while($row = mysqli_fetch_array($order, MYSQL_ASSOC)) {
			global $deliveryDate;
			$deliveryDate = $row["deliveredDate"];
			echo "<tr>";
			echo "<td>" . $row["name"] . "</td>";
			echo "<td>" . $row["phone"] . "</td>";
			echo "<td>" . $row["address"] . "</td>";
			echo "<td>" . $row["expectedDate"] . "</td>";
			echo "<td>" . $row["deliveredDate"] . "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}

	function printOrderItems($items) {
		echo "<table style='width:100%; text-align:center;'>";
		echo "<tr style=\"font-weight: bold\">";
		echo "<th>UPC</th>";
		echo "<th>Title</th>";
		echo "<th>Price</th>";
		echo "<th>Quantity</th>";
		echo "<th>Total Cost</th>";
		
		while($row = mysqli_fetch_array($items, MYSQL_ASSOC)) {
			echo "<tr>";
			echo "<td>" . $row["upc"] . "</td>";
			echo "<td>" . $row["title"] . "</td>";
			echo "<td>" . number_format($row["price"],2) . "</td>";
			echo "<td>" . $row["quantity"] . "</td>";
			echo "<td>" . number_format($row["quantity*stock"],2) . "</td>";
			echo "</tr>";
		}
		echo "</table>";
	}
	
	function printSetDateForm() {
		global $deliveryDate;
		if ($deliveryDate == NULL) {
		?>
			<form action="order.php?receiptId=<?php global $id; echo $id; ?>" method="post">
				<input type="hidden" name="receiptId" value="<?php echo $id; ?>">
				<table>
					<tr>
					<td align="right" style="padding-right:5px;">Delivery Date</td><td align="left"><input type="text" id="datepicker" name="date" style="margin-right:10px;"></td>
					<td><button type="submit">Apply</button></td></tr>
				</table>
			</form>
		<?php
		}
	}
	
	function setDate($con, $id, $date) {
		$statement = $con->prepare("UPDATE purchase SET deliveredDate=STR_TO_DATE(?, '%m/%d/%Y') WHERE receiptId=?");
		$statement->bind_param("si", $date, $id);
		$statement->execute();
		$result = $statement->get_result();
		if ($result)
			addToMessages("Delivery date set");
	}
	
?>
