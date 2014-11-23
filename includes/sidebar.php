<div class="logo">
	AMS
</div>

<div class="nav-container">
	<ul class="nav">
		<li>
			<a href="#">Customer Menu</a>
			<ul>
				<?php
					if (isset($_SESSION["username"]) && $_SESSION["username"] != NULL) {
						echo ('
							<li><a href="logout.php"> Logout </a> </li>
							<li><a href="cart.php"> Your Shopping Cart </a> </li>
							<li><a href="checkout.php"> Checkout </a> </li>
						');
					}
					else {
						echo ('
							<li><a href="login.php"> Login </a></li>
							<li><a href="registration.php"> Create Account </a></li>
							<li><a href="cart.php"> Your Shopping Cart </a> </li>
							<li><a href="checkout.php"> Checkout </a> </li>
						');
					}
				?>
			</ul>
		</li>
		<li>
			<a href="#">Clerk Menu</a>
			<ul>
				<li><a href="return.php"> Return Items </a></li>
			</ul>
		</li>
		<li>
			<a href="#">Manager Menu</a>
			<ul>
				<li><a href="add.php"> Add Items / Add Stock </a></li>
				<li><a href="orders.php"> Process Online Order Delivery </a></li>
				<li><a href="salesreport.php"> Daily Sales Report </a></li>
				<li><a href="topitems.php"> Top Selling Items </a></li>
			</ul>
		</li>
	</ul>
</div>
	