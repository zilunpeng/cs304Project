<?php
	
	
	
	/**************************************************************************
		Queries the database for the item with the given upc.
		
		If the item is found, the function returns an array of the form
				array("upc", "title", "type", "category",
					  "company", "year", "price", "stock")
					  
		If the item is not found, null is returned
		
		No changes are made to the database.

		@param $upc
			The upc to query
		
		@param $con
			The connection to the database
	**************************************************************************/
	function queryItem($con, $upc) {
		$itemArray = queryItems($con, array($upc));
		return $itemArray[0]["result"];
	}
	
	
	
	/**************************************************************************
		Queries the database for items with the given upc.
		
		Returns an array where each entry has the form
				array("upc", "result")
		where "result" indexes an array of the form 
				array("upc", "title", "type", "category",
					  "company", "year", "price", "stock")
 
		If an particular item is not found, then the "result" is null.
		
		No changes are made to the database.

		@param $upcs
			An array of upcs to query
		
		@param $con
			The connection to the database
	**************************************************************************/
	function queryItems($con, $upcs) {
	
		// The array of items to return
		$items = array();
	
		// The array containing the results of the query
		$result = array("upc"=>array(), "title"=>array(), "type"=>array(), "category"=>array(), "company"=>array(), "year"=>array(), "price"=>array(), "stock"=>array()); 
		
		// The prepared statement
		$query = $con->prepare("SELECT upc, title, type, category, company, year, price, stock FROM item WHERE upc=?");
		$query->bind_param("s", $upc);
		$query->bind_result($result["upc"], $result["title"], $result["type"], $result["category"], $result["company"], $result["year"], $result["price"], $result["stock"]);
		
		// Executing the query statement for every upc in the given array
		for($x = 0; $x < count($upcs); $x++) {
		
			// Execute the query for the current upc
			$upc = $upcs[$x];
			$query->execute();
			$query->fetch();
			
			// If the current upc is not found...
			if (count($result["upc"]) == 0) {
				array_push($items,
					array(
						"upc"=>$upc,
						"result"=>null
					)
				);
			}
			// Otherwise, item information is retrieved.
			else {
				array_push($items,
					array(
						"upc"=>$upc,
						"result"=>
							array(
								"upc"=>$result["upc"],
								"title"=>$result["title"],
								"type"=>$result["type"],
								"category"=>$result["category"],
								"company"=>$result["company"],
								"year"=>$result["year"],
								"price"=>round($result["price"], 2),
								"stock"=>$result["stock"]
							)
					)
				);
			}
		}
		
		return $items;
		
	}
	
	
	
	/**************************************************************************
		Creates a new item in the item table.
		
		@param $item
			The item to insert
		
		@param $con
			The connection to the database
	**************************************************************************/
	function insertIntoItem($con, $item) {

		$insert = $con->prepare('INSERT INTO item (upc, title, type, category, company, year, price, stock) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
		$insert->bind_param("isi", $item["upc"], $item["title"], $item["type"], $item["category"], $item["company"], $item["year"], $item["price"], $item["stock"]);
		$insert->execute();
		
	}
	
	
	
	/**************************************************************************
		Queries the database for the customer with the given cid.
		
		If the customer is found, the function returns an array of the form
				array("cid", "name", "password", "phone", "address")
					  
		If the customer is not found, null is returned
		
		No changes are made to the database.

		@param $cid
			The cid to query
		
		@param $con
			The connection to the database
	**************************************************************************/
	function queryCustomer($con, $cid) {
		$customerArray = queryCustomers($con, array($cid));
		return $customerArray[0]["result"];
	}
	
	
	
	/**************************************************************************
		Queries the database for customers with the given cids.
		
		Returns an array where each entry has the form
				array("cid", "result")
		where "result" indexes an array of the form 
				array("cid", "name", "password", "phone", "address")
 
		If an particular customer is not found, then the "result" is null.
		
		No changes are made to the database.

		@param $cids
			An array of cids to query
		
		@param $con
			The connection to the database
	**************************************************************************/
	function queryCustomers($con, $cids) {
	
		// The array of customers to return
		$customers = array();
	
		// The array containing the results of the query
		$result = array("cid"=>array(), "name"=>array(), "password"=>array(), "phone"=>array(), "address"=>array()); 
		
		// The prepared statement
		$query = $con->prepare("SELECT cid, name, password, phone, address FROM customer WHERE cid=?");
		$query->bind_param("s", $cid);
		$query->bind_result($result["cid"], $result["name"], $result["password"], $result["phone"], $result["address"]);
		
		// Executing the query statement for every cid in the given array
		for($x = 0; $x < count($cids); $x++) {
		
			// Execute the query for the current cid
			$cid = $cids[$x];
			$query->execute();
			$query->fetch();
			
			// If the current cid is not found...
			if (count($result["cid"]) == 0) {
				array_push($customers,
					array(
						"cid"=>$cid,
						"result"=>null
					)
				);
			}
			// Otherwise, item information is retrieved.
			else {
				array_push($customers,
					array(
						"cid"=>$cid,
						"result"=>
							array(
								"cid"=>$result["cid"],
								"name"=>$result["name"],
								"password"=>$result["password"],
								"phone"=>$result["phone"],
								"address"=>$result["address"]
							)
					)
				);
			}
		}
		
		return $customers;
		
	}
	
	
	
	/**************************************************************************
		Creates a new customer in the customer table..
		
		@param $customer
			The customer to insert
		
		@param $con
			The connection to the database
	**************************************************************************/
	function insertIntoCustomer($con, $customer) {

		// The insert statement for each new customer entity
		$insert = $con->prepare('INSERT INTO customer (cid, name, password, phone, address) VALUES (?, ?, ?, ?, ?)');
		$insert->bind_param("sssss", $customer["cid"], $customer["name"], $customer["password"], $customer["phone"], $customer["address"]);
		$insert->execute();
		
	}

	
	
	/**************************************************************************
		Queries the database for the purchase with the given receiptId.
		
		If the purchase is found, the function returns an array of the form
				array("receiptId", "purchaseDate", "cid", "cardNumber",
					  "expiryDate", "expectedDate", "deliveredDate")
					  
		If the purchase is not found, null is returned
		
		No changes are made to the database.

		@param $receiptId
			The receiptId to query
		
		@param $con
			The connection to the database
	**************************************************************************/
	function queryPurchase($con, $receiptId) {
		$purchaseArray = queryPurchases($con, array($receiptId));
		return $purchaseArray[0]["result"];
	}
	
	
	
	/**************************************************************************
		Queries the database for purchases with the given receiptIds.
		
		Returns an array where each entry has the form
				array("receiptId", "result")
		where "result" indexes an array of the form 
				array("receiptId", "purchaseDate", "cid", "cardNumber",
					  "expiryDate", "expectedDate", "deliveredDate")
					  
		If an particular item is not found, then the "result" is null.
		
		No changes are made to the database.

		@param $receiptIds
			An array of receiptIds to query
		
		@param $con
			The connection to the database
	**************************************************************************/
	function queryPurchases($con, $receiptIds) {
	
		// The array of purchases to return
		$purchases = array();
	
		// The array containing the results of the query
		$result = array("receiptId"=>array(), "purchaseDate"=>array(), "cid"=>array(), "cardNumber"=>array(), "expiryDate"=>array(), "expectedDate"=>array(), "deliveredDate"=>array());
		
		// The prepared statement
		$query = $con->prepare('SELECT receiptId, purchaseDate, cid, cardNumber, expiryDate, expectedDate, deliveredDate FROM purchase WHERE receiptId=?');
		$query->bind_param("i", $receiptId);
		$query->bind_result($result["receiptId"], $result["purchaseDate"], $result["cid"], $result["cardNumber"], $result["expiryDate"], $result["expectedDate"], $result["deliveredDate"]);
		
		// Executing the query statement for every receiptId in the given array
		for($x = 0; $x < count($receiptIds); $x++) {
		
			// Execute the query for the current receiptId
			$receiptId = $receiptIds[$x];
			$query->execute();
			$query->fetch();
			
			// If the current receiptId is not found...
			if (count($result["receiptId"]) == 0) {
				array_push($purchases,
					array(
						"receiptId"=>$receiptId,
						"result"=>null
					)
				);
			}
			// Otherwise, item information is retrieved.
			else {
				array_push($purchases,
					array(
						"receiptId"=>$receiptId,
						"result"=>
							array(
								"receiptId"=>$result["receiptId"],
								"purchaseDate"=>$result["purchaseDate"],
								"cid"=>$result["cid"],
								"cardNumber"=>$result["cardNumber"],
								"expiryDate"=>$result["expiryDate"],
								"expectedDate"=>$result["expectedDate"],
								"deliveredDate"=>$result["deliveredDate"]
							)
					)
				);
			}
		}
		
		return $purchases;
		
	}
	
	
	
	/**************************************************************************
		Creates a new purchase in the purchase table, and returns the
		receiptId.
		
		@param $cid
			The user cid for the purchase
		
		@param $creditcardnumber, $creditcardexpiry
			The credit card information for the purchase
		
		@param $con
			The connection to the database
	**************************************************************************/
	function insertIntoPurchase($con, $cid, $creditcardnumber, $creditcardexpiry) {
	
		// The purchase date
		$con->query('SET @maxOrdersPerDay = 10');
	
		// The purchase date
		$con->query('SET @purchaseDate = (Select CURDATE())');
		
		// The number of orders not yet delivered
		$con->query('SET @undeliveredOrders = (SELECT COUNT(receiptId) FROM purchase WHERE deliveredDate IS null)');
		
		// The estimated number of days until delivery
		$con->query('SET @expectedDays = FLOOR(@undeliveredOrders / @maxOrdersPerDay)');

		// The expected delivery date
		$con->query('SET @expectedDate = DATE_ADD(@purchaseDate, INTERVAL @expectedDays DAY)');

		// The delivery date
		$con->query('SET @deliveryDate = null');
		
		// The insert statement for the new purchase entity
		$insert = $con->prepare('INSERT INTO purchase (purchaseDate, cid, cardNumber, expiryDate, expectedDate, deliveredDate) VALUES (@purchaseDate, ?, ?, ?, @expectedDate, @deliveryDate)');
		$insert->bind_param("sis", $cid, $creditcardnumber, $creditcardexpiry);
		$insert->execute();
		
		// Return the receiptId
		return mysqli_insert_id($con);
		
	}
	
	
	
	/**************************************************************************
		Queries the database for the PurchaseItems with the given receiptId.
		
		If PurchaseItems are found, the function returns an array where every
		element is an array of the form
				array("receiptId", "upc", "quantity")
					  
		If the PurchaseItem is not found, null is returned
		
		No changes are made to the database.

		@param $receiptId
			The receiptId to query
		
		@param $con
			The connection to the database
	**************************************************************************/
	function queryPurchaseItems($con, $receiptId) {
	
		// The array of purchaseItems to return
		$purchaseItems = array();
	
		// The array containing the results of the query
		$result = array("receiptId"=>array(), "upc"=>array(), "quantity"=>array()); 
		
		// The prepared statement
		$query = $con->prepare("SELECT receiptId, upc, quantity FROM purchaseItem WHERE receiptId=?");
		$query->bind_param("s", $receiptId);
		$query->bind_result($result["receiptId"], $result["upc"], $result["quantity"]);

		$query->execute();
		$query->fetch();
			
		// If the current receiptId is not found...
		if (count($result["receiptId"]) == 0) {
			return array();
		}

		// Otherwise, purchaseItem information is retrieved.
		array_push($purchaseItems,
			array(
				"receiptId"=>$receiptId,
				"upc"=>$result["upc"],
				"quantity"=>$result["quantity"]
			)
		);
		
		return $purchaseItems;
		
	}
	
	
	
	/**************************************************************************
		Creates a new purchaseItem in the purchaseItem table with the given
		receiptId for every item in the given array.
		
		@param $receiptId
			The receiptId for the purchaseItem
		
		@param $items
			An array of (upc,quantity) pairs.
		
		@param $con
			The connection to the database
	**************************************************************************/
	function insertIntoPurchaseItem($con, $receiptId, $items) {

		// The insert statement for each new PurchaseItem entity
		$insert = $con->prepare('INSERT INTO purchaseItem (receiptId, upc, quantity) VALUES (?, ?, ?)');
		$insert->bind_param("isi", $receiptId, $upc, $quantity);
		
		// Executing the insert statement for each item
		for ($x = 0; $x < count($items); $x++) {
			$upc = $items[$x]["upc"];
			$quantity = $items[$x]["quantity"];
			$insert->execute();
		}
		
	}

	
	
	/**************************************************************************
		Queries the database for the return with the given retId.
		
		If the purchase is found, the function returns an array of the form
				array("retId", "date", "receiptId")
					  
		If the return is not found, null is returned
		
		No changes are made to the database.

		@param $retId
			The retId to query
		
		@param $con
			The connection to the database
	**************************************************************************/
	function queryReturn($con, $retId) {
		$returnArray = queryReturns($con, array($retId));
		return $returnArray[0]["result"];
	}
	
	
	
	/**************************************************************************
		Queries the database for returns with the given retIds.
		
		Returns an array where each entry has the form
				array("retId", "result")
		where "result" indexes an array of the form 
				array("retId", "date", "receiptId")
					  
		If an particular return	is not found, then the "result" is null.
		
		No changes are made to the database.

		@param $retIds
			An array of retIds to query
		
		@param $con
			The connection to the database
	**************************************************************************/
	function queryReturns($con, $retIds) {
	
		// The array of returns to return
		$returns = array();
	
		// The array containing the results of the query
		$result = array("retId"=>array(), "returnDate"=>array(), "receiptId"=>array());
		
		// The prepared statement
		$query = $con->prepare('SELECT retId, returnDate, receiptId FROM returns WHERE retId=?');
		$query->bind_param("i", $retId);
		$query->bind_result($result["retId"], $result["returnDate"], $result["receiptId"]);
		
		// Executing the query statement for every retId in the given array
		for($x = 0; $x < count($retIds); $x++) {
		
			// Execute the query for the current retId
			$retId = $retIds[$x];
			$query->execute();
			$query->fetch();
			
			// If the current retId is not found...
			if (count($result["retId"]) == 0) {
				array_push($returns,
					array(
						"retId"=>$retId,
						"result"=>null
					)
				);
			}
			// Otherwise, item information is retrieved.
			else {
				array_push($returns,
					array(
						"retId"=>$retId,
						"result"=>
							array(
								"retId"=>$result["retId"],
								"returnDate"=>$result["returnDate"],
								"receiptId"=>$result["receiptId"],
							)
					)
				);
			}
		}
		
		return $returns;
		
	}
	
	
	
	/**************************************************************************
		Creates a new return in the return table, and returns the retId.
		
		@param $receiptId
			The receiptId for the purchase the return is based on
		
		@param $con
			The connection to the database
	**************************************************************************/
	function insertIntoReturn($con, $receiptId) {
	
		// The insert statement for the new return entity
		$insert = $con->prepare('INSERT INTO returns (returnDate, receiptId) VALUES ((Select CURDATE()), ?)');
		$insert->bind_param("s", $receiptId);
		$insert->execute();
		
		// Return the receiptId
		return mysqli_insert_id($con);
		
	}
	
	
	
	/**************************************************************************
		Queries the database for the ReturnItems with the given retId.
		
		If ReturnItems are found, the function returns an array where every
		element is an array of the form
				array("retId", "upc", "quantity")
					  
		If ReturnItems are not found, null is returned
		
		No changes are made to the database.

		@param $retId
			The retId to query
		
		@param $con
			The connection to the database
	**************************************************************************/
	function queryReturnItems($con, $retId) {
	
		// The array of returnItems to return
		$returnItems = array();
	
		// The array containing the results of the query
		$result = array("retId"=>array(), "upc"=>array(), "quantity"=>array()); 
		
		// The prepared statement
		$query = $con->prepare("SELECT retId, upc, quantity FROM returnItem WHERE retId=?");
		$query->bind_param("s", $retId);
		$query->bind_result($result["retId"], $result["upc"], $result["quantity"]);

		$query->execute();
		$query->fetch();
			
		// If the current retId is not found...
		if (count($result["retId"]) == 0) {
			return array();
		}

		// Otherwise, returnItem information is retrieved.
		array_push($returnItems,
			array(
				"retId"=>$retId,
				"upc"=>$result["upc"],
				"quantity"=>$result["quantity"]
			)
		);
		
		return $returnItems;
		
	}
	
	
	
	/**************************************************************************
		Creates a new returnItem in the returnItem table with the given
		retId for every item in the given array.
		
		@param $retId
			The retId for the returnItem
		
		@param $items
			An array of (upc,quantity) pairs.
		
		@param $con
			The connection to the database
	**************************************************************************/
	function insertIntoReturnItem($con, $retId, $items) {

		// The insert statement for each new returnItem entity
		$insert = $con->prepare('INSERT INTO returnItem (retId, upc, quantity) VALUES (?, ?, ?)');
		$insert->bind_param("isi", $retId, $upc, $quantity);
		
		// Executing the insert statement for each item
		for ($x = 0; $x < count($items); $x++) {
			$upc = $items[$x]["upc"];
			$quantity = $items[$x]["quantity"];
			$insert->execute();
		}
		
	}



?>