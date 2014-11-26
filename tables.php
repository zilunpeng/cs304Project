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
	$tableName = getTableName();
	$result = NULL;
	
	if ($tableName != NULL) {
		global $result;
		$result = getTable($con, $tableName);
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
	<title>View Tables</title>
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
			
				<!-- Table -->
				<form action="tables.php" method="get">
					<table>
						<tr>
							<td>Table</td>
							<td><select name="table">
								<option value="customer" <?php echo ($tableName=="customer") ? "selected" : "" ; ?>>Customer</option>
								<option value="hassong" <?php echo ($tableName=="hassong") ? "selected" : "" ; ?>>HasSong</option>
								<option value="item" <?php echo ($tableName=="item") ? "selected" : "" ; ?>>Item</option>
								<option value="leadsinger" <?php echo ($tableName=="leadsinger") ? "selected" : "" ; ?>>LeadSinger</option>
								<option value="purchase" <?php echo ($tableName=="purchase") ? "selected" : "" ; ?>>Purchase</option>
								<option value="purchaseitem" <?php echo ($tableName=="purchaseitem") ? "selected" : "" ; ?>>PurchaseItem</option>
								<option value="returnitem" <?php echo ($tableName=="returnitem") ? "selected" : "" ; ?>>ReturnItem</option>
								<option value="returns" <?php echo ($tableName=="returns") ? "selected" : "" ; ?>>Returns</option>
							</select></td>
							<td><button type="submit">Apply</button></td>
						</tr>
					</table>
				</form>
				<?php if ($tableName != NULL) printTable($result); ?>
				
			</div>
		</div>
	</div>
</body>
</html>

<?php
	// MODEL
	// ===============

	function buildQuery( $tableName ) {
		switch($tableName)
		{
			case "customer":
				$tbl = "customer";
				break;
			case "hassong":
				$tbl = "hassong";
				break;
			case "item":
				$tbl = "item";
				break;
			case "leadsinger":
				$tbl = "leadsinger";
				break;
			case "purchase":
				$tbl = "purchase";
				break;
			case "purchaseitem":
				$tbl = "purchaseitem";
				break;
			case "returnitem":
				$tbl = "returnitem";
				break;
			case "returns":
				$tbl = "returns";
				break;
			default:
				addToMessages($tableName . " is not a valid table name. Defaulting to item");
				$tbl = "item";
		}
		$sql = "SELECT * FROM $tbl";
		return $sql;
	}
	
	function getTable($con, $table) {
		$statement = $con->prepare(buildQuery($table));
		$statement->execute();
		$result = $statement->get_result();
		
		return $result;
	}
	
	function getTableName() {
		if (isset($_GET["table"]) && $_GET["table"] != "")
			return $_GET["table"];
		return NULL;
	}

	function printTable($table) {
		$fields = mysqli_fetch_fields($table);
		
		echo "<table style='width:100%; text-align:center;'>";
		echo "<tr style=\"font-weight: bold\">";

		foreach ($fields as $row) {
			$row = get_object_vars($row);
			echo "<th>" . $row["name"] . "</th>";
		}
		echo "</tr>";
		
		while($row = mysqli_fetch_array($table, MYSQLI_ASSOC)) {
			echo "<tr>";
			foreach ($row as $key => $value) {
				if ($key == "price")
					echo "<td>" . number_format($value,2) . "</td>";
				else
					echo "<td>" . $value . "</td>";
			}
			echo "</tr>";
		}
		
		echo "</table>";
	}
?>