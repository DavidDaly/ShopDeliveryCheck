<?php 
	
	$activePage = 'Summary for Your Area';

	require 'header.php';

	$totallyReliantCount = 0;
	$highlyReliantCount = 0;
	$deliveryPreferredCount = 0; 
	$deliveryNotNeededCount = 0;
	$advice = '';

	if ( $postcode == '' )
	{
		renderNoPostcode();
	}
	else
	{

		$dbconn = new mysqli('localhost', 'root', '', 'shopdelcheck');
		// Check connection
		if (!$dbconn->connect_error)
		{
			$sql = 	"SELECT COUNT(Postcode), NeedGroup FROM information " .
					"WHERE DeliveryAvailable='false' AND Postcode='$postcode' " .
					"GROUP BY NeedGroup";
			$result = $dbconn->query($sql);
			if ( $result->num_rows > 0 )
			{
				while ( $row = $result->fetch_assoc())
				{
					switch ( $row['NeedGroup'] )
					{
						case 1:
							$totallyReliantCount = $row['COUNT(Postcode)'];
							break;
						case 2:
							$highlyReliantCount = $row['COUNT(Postcode)'];
							break;
						case 3:
							$deliveryPreferredCount = $row['COUNT(Postcode)'];
							break;
						case 4:
							$deliveryNotNeededCount = $row['COUNT(Postcode)'];
							break;
					}
				}
				
				switch ( $needGroup )
				{
					case 1:
						if ( ($highlyReliantCount + $deliveryPreferredCount + $deliveryNotNeededCount) > 0 )
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
						if ( ($totallyReliantCount) > 0 )
						{
							$advice = "Other people in your area have indicated that they are <b>more</b> reliant on deliveries than you are. " . 
										"Please consider cancelling any delivery slots that you have booked and relying instead on neighbours, friends and family to shop on your behalf.";
						}
						elseif ( ($deliveryPreferredCount + $deliveryNotNeededCount) > 0 )
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
						if ( ($totallyReliantCount + $highlyReliantCount) > 0 )
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
			else
			{
				echo $dbconn->error;
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
		global $totallyReliantCount;
		global $highlyReliantCount;
		global $deliveryPreferredCount; 
		global $deliveryNotNeededCount;
		global $advice;

	?>
	
	<div class="container-fluid">
		
		<div class="row">
			<div class="col-xl-9 col-lg-11  pt-0 pb-0 rounded text-left mx-auto">
				<div class="bg-light rounded-top mt-4 p-2 p-sm-4 border-primary border ml-sm-2 ml-xs-2 mb-0 mr-sm-2 mr-xs-2">
						<div class="row">
							<div class="col-lg-12">
								<p><?=$advice?></p>
								<p>Please return to this site to update your information if your situation changes. Information that is not updated for more than 7 days will be removed to ensure that we ae reporting informaion that is current.</p>
							</div>
						</div>	
				</div>
			</div>
		</div>
		
		<div class="row">
			<div class="col-xl-9 col-lg-11 pb-4 rounded text-center text-light mx-auto">
				<div class="rounded-bottom mb-2 p-2 ml-sm-2 ml-xs-2 mt-0 mr-sm-2 mr-xs-2 border-primary border-bottom border-left border-right">
					<canvas  id="chartAreaSummary"></canvas>
				</div>
			</div>
		</div>

		
		
	</div>
	
	<script>
	
	Chart.defaults.global.animation.duration = 3000;

	new Chart(document.getElementById("chartAreaSummary"), {
		type: 'horizontalBar',
		data: 
			{
				labels: ["Totally reliant", "Highly reliant", "Delivery preferred", "Delivery not needed"],
				datasets: [{
					label: '',
					backgroundColor: 'rgba(0, 104, 160, 0.2)',
					borderColor: '#00314c',
					borderWidth: 1,
					data: ['<?=$totallyReliantCount?>', '<?=$highlyReliantCount?>', '<?=$deliveryPreferredCount?>', '<?=$deliveryNotNeededCount?>']
				}]
			},
		options:
			{
				responsive: true,
				title:
					{
						display: true,
						text: 'People reporting no delivery slots available in <?=$postcode?>',
						fontSize: 16,
						fontColor: "black"
					},
				legend: 
					{
						display: false
					}
			}
	});

	</script>
	
<?php
	
	}
	
	require 'footer.php';
	
?>		

	