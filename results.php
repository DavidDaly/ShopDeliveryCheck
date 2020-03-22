<?php 
	/* Copyright 2020 David Daly
	 * Licensed under MIT (https://github.com/DavidDaly/ShopDeliveryCheck/blob/master/LICENSE) */	
	
	$activePage = 'Results';

	require 'header.php';

	$localStats = array( TOTALLY_RELIANT => 0, HIGHLY_RELIANT => 0, PREFERRED => 0, NOT_NEEDED => 0 );
	$localTotal = 0;
	$showLocalStats = FALSE;
	$nationalStats = array( TOTALLY_RELIANT => 0, HIGHLY_RELIANT => 0, PREFERRED => 0, NOT_NEEDED => 0 );
	$advice = '';

	if ( $postcode == '' )
	{
		renderNoPostcode();
	}
	else
	{

		$dbconn = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);
		// Check connection
		if (!$dbconn->connect_error)
		{
			$sql = 	"SELECT COUNT(PostcodeTown), NeedGroup FROM information " .
					"WHERE DeliveryAvailable='false' AND PostcodeTown='$postcodeTown' " .
					"GROUP BY NeedGroup";
			$result = $dbconn->query($sql);

			while ( $row = $result->fetch_assoc() )
			{
				$localStats[$row['NeedGroup']] = $row['COUNT(PostcodeTown)'];
				$localTotal += $row['COUNT(PostcodeTown)'];			
			}
			
			$showLocalStats = ( $localTotal >= 10 );		
			
			if ( !$showLocalStats )
			{	
				$localTotalPeople = '';
				if ( $localTotal == 1 )
				{
					$localTotalPeople = 'you are the only one to have';
				}
				else
				{
					$localTotalPeople = "only $localTotal people have";
				}
				$advice = 	"So far $localTotalPeople told us what's happening in the $postcodeTown area. So for now we can only show you the information we've collected for the whole country. " .
							"Please encourage other people in the $postcodeTown area to use this site so that we can provide better local advice.";
			}
			else
			{	
				switch ( $needGroup )
				{
					case 1:
						if ( ($localStats[HIGHLY_RELIANT] + $localStats[PREFERRED] + $localStats[NOT_NEEDED]) > 0 )
						{
							$advice = "Other people in your area have indicated that they are less reliant on shopping deliveries than you are. " . 
										"Sharing your information may help them re-consider whether they need to use a shopping delivery service.";;
						}
						else
						{
							$advice = "Other people in your area are also totally reliant on shopping delivery services. " . 
										"Please reach out to neighbours, friends and family to see if they can shop on your behalf.";
						}
						break;
					case 2:
						if ( ($localStats[TOTALLY_RELIANT]) > 0 )
						{
							$advice = "Other people in your area have indicated that they are <b>more</b> reliant on deliveries than you are. " . 
										"Please consider cancelling any delivery slots that you have booked and relying instead on neighbours, friends and family to shop on your behalf.";
						}
						elseif ( ($localStats[PREFERRED] + $localStats[NOT_NEEDED]) > 0 )
						{
							$advice = "Other people in your area have indicated that they are less reliant on shopping deliveries than you are. " . 
										"Sharing your information may help them re-consider whether they need to use a shopping delivery service.";
						}
						else
						{
							$advice = "Other people in your area are also highly reliant on shopping delivery services. " . 
										"Please continue to reach out to neighbours, friends and family to see if they can shop on your behalf.";
						}
						break;
					case 3:
					case 4:
						if ( ($localStats[TOTALLY_RELIANT] + $localStats[HIGHLY_RELIANT]) > 0 )
						{
							$advice = "Other people in your area have indicated that they are <b>more</b> reliant on deliveries than you are. " . 
										"Please consider cancelling any delivery slots that you have and shopping in-store instead.";
						}
						else
						{
							$advice = "Currently in your area no one else has indicated that they are relying on shopping delivery services and are unable to access them. " . 
										"However, please keep checking back regularly to see if this situation changes.";
						}
						break;				
				} // Switch
			}
		
			
			$sql = 	"SELECT COUNT(Postcode), NeedGroup FROM information " .
					"WHERE DeliveryAvailable='false' " .
					"GROUP BY NeedGroup";
			$result = $dbconn->query($sql);
			if ( $result->num_rows > 0 )
			{
				while ( $row = $result->fetch_assoc() )
				{
					$nationalStats[$row['NeedGroup']] = $row['COUNT(Postcode)'];	
				}
			}
			
		}
		$dbconn->close();
	
		renderSummary();
	
	}
	
	function renderNoPostcode()
	{
		?>
		
		<div class="container-fluid">
		<div class="row">
		<div class="col-xl-9 col-lg-11 m-2 pb-4 rounded text-center text-light mx-auto">
	

			<section class="jumbotron text-center text-dark border-primary border">
				<div class="container">
					<h1 class="jumbotron-heading">Please enter your details first</h1>
					<p class="lead">Please enter your details so that we can show you summary information about the number and profile of people relying on shopping deliveries in your area.</p>
					<p>
						<a href='your-information' class="btn btn-primary">Enter your details</a>
					</p>
				</div>
			</section>
		
	
		</div><!-- /.container -->
		
		<?php
	}

	function renderSummary()
	{
		global $postcode;
		global $postcodeTown;
		global $localStats;
		global $nationalStats;
		global $showLocalStats;
		global $advice;
		global $socialLinks;
		global $siteURL;

	?>
	
	<div class="container-fluid">
		
		<div class="row">
			<div class="col-xl-9 col-lg-11  pt-0 pb-0 rounded text-left mx-auto">
				<div class="bg-light rounded-top mt-4 p-2 p-sm-4 border-primary border ml-sm-2 ml-xs-2 mb-0 mr-sm-2 mr-xs-2">
						<div class="row">
							<div class="col-lg-12">
								<h3>Summary</h3>
								<p><?=$advice?></p>
								<p>Please bookmark this page. This link is unique to you and you can use it to update your information if your situation changes. Information that is not updated for more than 7 days will be removed to ensure that we are reporting data that is current.</p>
								<p>If you wish to start again to enter new information on behalf of someone else, please click <a href="your-information">here</a>.</p>
								<hr />
								<p><?=$socialLinks?></p>
							</div>
						</div>	
				</div>
			</div>
		</div>
		
		<?php if ( $showLocalStats ) { ?>
		<div class="row">
			<div class="col-xl-9 col-lg-11 pb-0 rounded text-center text-light mx-auto">
				<div class="mb-0 p-2 ml-sm-2 ml-xs-2 mt-0 mr-sm-2 mr-xs-2 border-primary border-left border-right border-bottom">
					<canvas  id="localSummary"></canvas>
				</div>
			</div>
		</div>
		<?php } ?>
		
		<div class="row">
			<div class="col-xl-9 col-lg-11 pb-4 rounded text-center text-light mx-auto">
				<div class="rounded-bottom mb-2 p-2 ml-sm-2 ml-xs-2 mt-0 mr-sm-2 mr-xs-2 border-primary border-bottom border-left border-right">
					<canvas  id="nationalSummary"></canvas>
				</div>
			</div>
		</div>	
		
	</div>
	
	<script>
	
	Chart.defaults.global.animation.duration = 3000;

	<?php if ( $showLocalStats ) { ?>
	new Chart(document.getElementById("localSummary"), {
		type: 'horizontalBar',
		data: 
			{
				labels: ["Totally reliant", "Highly reliant", "Delivery preferred", "Delivery not needed"],
				datasets: [{
					label: '',
					backgroundColor: 'rgba(237, 125, 49, 0.3)',
					borderColor: 'rgba(174, 90, 33, 1)',
					borderWidth: 1,
					data: ['<?=$localStats[TOTALLY_RELIANT]?>', '<?=$localStats[HIGHLY_RELIANT]?>', '<?=$localStats[PREFERRED]?>', '<?=$localStats[NOT_NEEDED]?>']
				}]
			},
		options:
			{
				responsive: true,
				title:
					{
						display: true,
						text: 'People with no delivery slots available in the <?=$postcodeTown?> area',
						fontSize: 16,
						fontColor: "black"
					},
				legend: 
					{
						display: false
					},
				scales: {
					xAxes: [{
						ticks:
						{
							beginAtZero: true
						}
					}]
				}
			}
	});
	<?php } ?>
	
	new Chart(document.getElementById("nationalSummary"), {
		type: 'horizontalBar',
		data: 
			{
				labels: ["Totally reliant", "Highly reliant", "Delivery preferred", "Delivery not needed"],
				datasets: [{
					label: '',
					backgroundColor: 'rgba(91, 155, 213, 0.3)',
					borderColor: 'rgba(65, 113, 156, 1)',
					borderWidth: 1,
					data: ['<?=$nationalStats[TOTALLY_RELIANT]?>', '<?=$nationalStats[HIGHLY_RELIANT]?>', '<?=$nationalStats[PREFERRED]?>', '<?=$nationalStats[NOT_NEEDED]?>']
				}]
			},
		options:
			{
				responsive: true,
				title:
					{
						display: true,
						text: 'People reporting no delivery slots available nationally',
						fontSize: 16,
						fontColor: "black"
					},
				legend: 
					{
						display: false
					},
				scales: {
					xAxes: [{
						ticks:
						{
							beginAtZero: true
						}
					}]
				}
			}
	});

	</script>
	
<?php
	
	}
	
	require 'footer.php';
	
?>		

	