<?php 
	
	define("TOTALLY_RELIANT", "1");
	define("HIGHLY_RELIANT", "2");
	define("PREFERRED", "3");
	define("NOT_NEEDED", "4");
	
	require 'db.php';
	
	$id = NULL;
	
	// Are they a returning visitor
	if ( isset( $_COOKIE['shopdeliverycheck'] ) )
	{
		// See if the ID in the cookie is in the database and, if so, load the details
		$id = $_COOKIE['shopdeliverycheck'];
	}
	
	// Start by checking that postcode has been entered
	$postcode = '';
	if ( isset($_POST['POSTCODE']) )
	{
		$postcode = trim($_POST['POSTCODE']);
	}
	
	// Also check what was selected for delivery availability
	$deliveryAvailable = 'TRUE';
	if ( isset($_POST['AVAIL']) )
	{
		if ( $_POST['AVAIL'] == 'AVAIL-NO' )
		{
			$deliveryAvailable = 'FALSE';
		}
	}
	
	// And finally what "need group" thy are in
	if ( isset($_POST['NEED-GROUP']) )
	{
		for ( $i=0; $i<4; $i++ )
		{
			if ( $_POST['NEED-GROUP'] == 'NEED-GROUP-' . strval($i+1) )
			{
				$needGroup = $i+1;
			}
		}
	}
	else
	{
		// Defualt value
		$needGroup = 4;
	}
	
	// If person has clicked save (i.e. we have post data), save it to the database and ten navigate to Summary for Area
	if ( isset($_POST['POSTCODE']) )
	{	
		$dbconn = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);
		// Check connection
		if (!$dbconn->connect_error)
		{
			if ( $id != NULL )
			{
				$sql = "UPDATE information SET Postcode='$postcode', DeliveryAvailable=$deliveryAvailable, NeedGroup='$needGroup' WHERE ID=$id";
			}
			else
			{
				$sql = "INSERT INTO information (Postcode, DeliveryAvailable, NeedGroup) VALUES ('$postcode', $deliveryAvailable, '$needGroup')";
			}
			
			if ( $dbconn->query($sql) === TRUE )
			{
				// If it was an insert, save the insert ID in a cookie
				if ( $id == NULL )
				{
					setcookie('shopdeliverycheck', $dbconn->insert_id, time() + (1 * 365 * 24 * 60 * 60));
				}
				if ( strlen( trim($postcode)) >= 2 )
				{
					header('Location: results');
				}
			}
			else
			{
				echo $dbconn->error;
			}
		
			$dbconn->close();
		
		}
		else
		{
			echo $dbconn->error;
			
		}
	}
	else
	{
		// See if they are returning
		if ( $id != NULL )
		{
			$dbconn = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);
			// Check connection
			if (!$dbconn->connect_error)
			{
				$sql = "SELECT * FROM information WHERE ID=$id";
				$result = $dbconn->query($sql);
				if ( $result->num_rows > 0 )
				{
					while ( $row = $result->fetch_assoc())
					{
						$postcode = trim($row['Postcode']);
						$deliveryAvailable = $row['DeliveryAvailable'];
						$needGroup = $row['NeedGroup'];
					}
				}
				else
				{
					echo $dbconn->error;
				}
			}
		$dbconn->close();
		}
	}
	
	
	// Create an array to represent the navbar buttons
	$navBar = array (
		'Your Information' => array ('Url' => 'your-information', 'Type' => 'Standard'),
		'Results' => array ('Url' => 'results', 'Type' => 'Standard'),	
		'About' => array ('Url' => 'about', 'Type' => 'Standard') );
	
	function RenderNavBarButtons($navBar)
	{
		foreach ($navBar as $index=>$navBarButton)
		{
			switch ( $navBarButton['Type'] ) {
				case 'Standard':
					RenderStandardNavBarButton($index, $navBarButton['Url']);
					break;
				case 'Dropdown':
					RenderDropdownNavBarButton($index, $navBarButton);
					break;
			}
		}
	}
	
	function RenderStandardNavBarButton($buttonText, $url)
	{
		// Check if this is the button for the current page, and if so style it accordingly
		global $activePage;
		$active = '';
		if ($activePage == $buttonText)
		{
			$active = ' active';
		}
		?>
		<li>
			<a href="<?=$url?>" class="nav-link<?=$active?>"><?=$buttonText?></a>
		</li>
		<?php
	}
	
	function RenderDropdownNavBarButton($buttonText, $navBarButton)
	{
		// Check if this is the button for the current page, and if so style it accordingly
		global $activePage;
		$active = '';
		if ($activePage == $buttonText)
		{
			$active = ' active';
		}
		?>
		<li class="navbar-item dropdown">
			<a href="#" class="nav-link dropdown-toggle<?=$active?>" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				<?=$buttonText?>
			</a>
			<div class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
				<?php foreach ($navBarButton['Items'] as $index=>$dropdownItem) { 
					switch ( $dropdownItem['Type'] ) {
						case 'Standard': ?>
							<a class="dropdown-item" href="#" onclick="<?=$dropdownItem['Url']?>"><?=$index?></a>
							<?php break;
						case 'Divider': ?>
							<div class="dropdown-divider"></div>
							<?php break;
					}
				}?>
			</div>
		</li>
		<?php
	}
	
?>



<!doctype html>
<html lang="en">
	<head>
		<!-- Required meta tags -->
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
		
		<!-- Bootstrap CSS -->
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="fontawesome/css/all.css" rel="stylesheet">

		<title>Shopping Delivery Check</title>
		<script src="./js/chart.bundle.min.js"></script>
		<script src="js/jquery-3.3.1.min.js"></script>		
		<style>
			#bigwrapper {
				background-repeat: no-repeat;
				background-position: top center;
				background-attachment: fixed;
				backgroun-size: cover;
				background-color: RGB(255, 255, 255);
				padding-top: 70px;
			}
		
			@media (max-width: 355px) { 
				#bigwrapper { padding-top: 100px; }
			}
		
		</style>
		
	</head>
	
	<body id="bigwrapper">

	<nav class="navbar navbar-dark bg-primary fixed-top navbar-expand-md form-group" ">
		<a href="about" class="navbar-brand">Shopping Delivery Check</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav ml-auto">
				<?php RenderNavBarButtons($navBar); ?>
			</ul>
		</div>
	</nav>	
	
