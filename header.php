<?php 
	/* Copyright 2020 David Daly
	 * Licensed under MIT (https://github.com/DavidDaly/ShopDeliveryCheck/blob/master/LICENSE) */
	
	define("TOTALLY_RELIANT", "1");
	define("HIGHLY_RELIANT", "2");
	define("PREFERRED", "3");
	define("NOT_NEEDED", "4");
	
	$siteURL = "https://shopdeliverycheck.co.uk/";
	
	$socialLinks = 	'<div class="product-social-links" style="line-height: 13px">' .
					'	<div class="fb-share-button" data-href="' . $siteURL . '" data-layout="button" data-size="small"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fshopdeliverycheck.co.uk%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div>' .
					'		<script src="https://platform.linkedin.com/in.js" type="text/javascript">lang: en_US</script>' .
					'		<script type="IN/Share" data-url="' . $siteURL . '"></script>' .
					'		<a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-text="Please make a commitment that you will only use home deliveries when it is essential" data-url="' . $siteURL . '" data-show-count="false">Tweet</a><script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>' .						
					'	</div>';
	
	require 'db.php';
	
	$id = NULL;
	
	// Are they a returning visitor (specifying id in URL)
	if ( isset( $_GET['id'] ) )
	{
		if ( is_numeric($_GET['id']) )
		{
			$id = $_GET['id'];
		}
	}
	
	// Start by checking that postcode has been entered
	$postcode = '';
	$postcodeTown = '';
	$postcodeValid = FALSE;
	if ( isset($_POST['POSTCODE']) )
	{
		// check that postcode is valid
		$postcode = strtoupper(trim($_POST['POSTCODE']));
		if ( strlen($postcode) >= 2 )
		{
			$dbconn = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);
			// Check connection
			if (!$dbconn->connect_error)
			{
				$sql = "SELECT * FROM postcodes WHERE Postcode = '$postcode'";
				if ( $result = $dbconn->query($sql) );
				{
					if ( $result->num_rows > 0 )
					{
						$postcodeValid = TRUE;
						$row = $result->fetch_assoc();
						$postcodeTown = $row['Town'];
					}
				}
			}
		}
		
		if ( !$postcodeValid )
		{
			// Set back to what the user entered
			$postcode = $_POST['POSTCODE'];
		}
		
	}
	
	// Also check what was selected for delivery availability
	$deliveryAvailable = NULL;
	if ( isset($_POST['AVAIL']) )
	{
		if ( $_POST['AVAIL'] == 'AVAIL-NO' )
		{
			$deliveryAvailable = '0';
		}
		elseif ( $_POST['AVAIL'] == 'AVAIL-YES' )
		{
			$deliveryAvailable = '1';
		}
		else
		{
			$deliveryAvailable = '2'; // Don't know	
		}
		
	}
	
	// Also check what was selected for commiting not to use non-essential home delivery
	$nonEssentialCommit = NULL;
	if ( isset($_POST['NON-ESSENTIAL-COMMIT']) )
	{
		if ( $_POST['NON-ESSENTIAL-COMMIT'] == 'NON-ESSENTIAL-COMMIT-NO' )
		{
			$nonEssentialCommit = 'FALSE';
		}
		else
		{
			$nonEssentialCommit = 'TRUE';
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
		$needGroup = NULL;
	}
	
	// If person has clicked save (i.e. we have post data), save it to the database and then navigate to Summary for Area
	if ( isset($_POST['POSTCODE']) )
	{	
		if ( $postcodeValid && !is_null($deliveryAvailable) && !is_null($needGroup) && !is_null($nonEssentialCommit) )
		{
			$dbconn = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);
			// Check connection
			if (!$dbconn->connect_error)
			{
				if ( $id != NULL )
				{
					$sql = "UPDATE information SET Postcode='$postcode', DeliveryAvailable='$deliveryAvailable', NeedGroup='$needGroup', PostcodeTown='$postcodeTown', CommitStopNonEssential=$nonEssentialCommit WHERE ID=$id";
				}
				else
				{
					$sql = "INSERT INTO information (Postcode, DeliveryAvailable, NeedGroup, PostcodeTown, CommitStopNonEssential) VALUES ('$postcode', '$deliveryAvailable', '$needGroup', '$postcodeTown', $nonEssentialCommit)";
				}
			
				if ( $dbconn->query($sql) === TRUE )
				{
					// If it was an insert, save the insert ID
					if ( $id == NULL )
					{
						$id = $dbconn->insert_id;
					}
					header('Location: results-' . $id);
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
					while ( $row = $result->fetch_assoc() )
					{
						$postcode = trim($row['Postcode']);
						$deliveryAvailable = $row['DeliveryAvailable'];
						$needGroup = $row['NeedGroup'];
						$postcodeTown =  $row['PostcodeTown'];
						if ( $row['CommitStopNonEssential'] == 0 )
						{
							$nonEssentialCommit = 'FALSE';
						}
						else
						{
							$nonEssentialCommit = 'TRUE';
						}
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
	
	$urlPostfix = '';
	if ( $id != NULL )
	{
		$urlPostfix = '-' . $id;
	}
		
	
	// Create an array to represent the navbar buttons
	$navBar = array (
		'Your viewpoint' => array ('Url' => 'your-information' . $urlPostfix, 'Type' => 'Standard'),
		'Info for your area' => array ('Url' => 'results' . $urlPostfix, 'Type' => 'Standard'),	
		'About' => array ('Url' => 'about' . $urlPostfix, 'Type' => 'Standard') );
	
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

		<title>Home Delivery Demand</title>
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
	
	<!-- Facebook JavaScript SDK -->
	<div id="fb-root"></div>
	<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_GB/sdk.js#xfbml=1&version=v6.0"></script>
	
	<nav class="navbar navbar-dark bg-primary fixed-top navbar-expand-md form-group" ">
		<a href="about<?=$urlPostfix?>" class="navbar-brand">Home Delivery Demand</a>
		<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
			<span class="navbar-toggler-icon"></span>
		</button>
		<div class="collapse navbar-collapse" id="navbarNav">
			<ul class="navbar-nav ml-auto">
				<?php RenderNavBarButtons($navBar); ?>
			</ul>
		</div>
	</nav>	
	
