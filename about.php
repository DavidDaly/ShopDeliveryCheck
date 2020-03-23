<?php 
	/* Copyright 2020 David Daly
	 * Licensed under MIT (https://github.com/DavidDaly/ShopDeliveryCheck/blob/master/LICENSE) */
	
	$isForm = FALSE;
	$activePage = 'About';
	
	require 'header.php';

	$dataLabels = NULL;
	$dataValues = NULL;

	$dbconn = new mysqli($dbserver, $dbuser, $dbpassword, $dbname);
	// Check connection
	if (!$dbconn->connect_error)
	{
		$sql = 	"SELECT COUNT(PostcodeTown), PostcodeTown FROM information " .
					"WHERE CommitStopNonEssential=true " .
					"GROUP BY PostcodeTown ORDER BY COUNT(PostcodeTown) DESC";
		
		$result = $dbconn->query($sql);
		
		$dataLabels = '[';
		$dataValues = '[';
		
		while ( $row = $result->fetch_assoc() )
		{
			$dataLabels .= "'" . $row['PostcodeTown'] . " Area',";
			$dataValues .= $row['COUNT(PostcodeTown)']	. ',';
		}
		
		// Remove trailing commas and add closing bracket
		$dataLabels = substr($dataLabels, 0, -1) . ']';
		$dataValues = substr($dataValues, 0, -1) . ']';
		
		// If no results, set to null
		if ( $dataLabels == ']' )
		{
			$dataLabels = NULL;
			$dataValues = NULL;
		}		
	}
	
?>

	<div class="container-fluid">
	<div class="row">
		<div class="col-xl-9 col-lg-11 m-2 pb-4 rounded text-center text-light mx-auto">
	

			<section class="jumbotron text-center text-dark border-primary border">
				<div class="container">
					<h1 class="jumbotron-heading">Make a commitment to support those most in need</h1>
					<p class="lead">The impact of COVID-19 is huge. Many people cannot leave their home to do food shopping.
									This website lets you show your solidarity and support for them by making a commitment to use home delivery services only if it is essential for you to do so.
					<p>
						<a href='your-information<?=$urlPostfix?>' class="btn btn-primary">Make your commitment now</a>
					</p>
					<hr />
					<p>
						<?=$socialLinks?>
					</p>
				</div>
			</section>
			
			<!-- Three columns of text below the jumbotron -->
			<div class="row text-dark">
			
				<div class="col-lg-4">
					<span class="fa-stack fa-5x mb-4 text-light">
						<i class="fas fa-circle fa-stack-2x text-primary"></i>
						<i class="far fa-heart fa-stack-1x"></i>
					</span>
					<h2>Support</h2>
					<p class="text-justify">Show your support for people who are unable to go to the shops for food and are relying on home delivery services.</p>
				</div><!-- /.col-lg-4 -->
			
				<div class="col-lg-4">
					<span class="fa-stack fa-5x mb-4 text-light">
						<i class="fas fa-circle fa-stack-2x text-primary"></i>
						<i class="fas fa-comments fa-stack-1x"></i>
					</span>
					<h2>Share</h2>
					<p class="text-justify">Tell us what's going on in your area. Help us to build up a national picture of the demand for home deliveries.</p>
				</div><!-- /.col-lg-4 -->
		  

				<div class="col-lg-4">
					<span class="fa-stack fa-5x mb-4 text-light">
						<i class="fas fa-circle fa-stack-2x text-primary"></i>
						<i class="fas fa-chart-bar fa-stack-1x"></i>
					</span>
					<h2>Understand</h2>
					<p class="text-justify">Find out how many people in your area are depending on home deliveries.</p>

				</div><!-- /.col-lg-4 -->
				
			</div>
		</div>
		
	</div><!-- /.row -->

	
	<?php if ( !is_null($dataLabels)) { ?>
	<div class="row">
		<div class="col-xl-9 col-lg-11 pb-4 rounded text-center text-light mx-auto">
			<div class="rounded-bottom mb-2 p-2 ml-sm-2 ml-xs-2 mt-0 mr-sm-2 mr-xs-2">
				<canvas  id="commitmentsChart"></canvas>
			</div>
		</div>
	</div>
	<?php } ?>
	
	<script>
	
	Chart.defaults.global.animation.duration = 3000;

	new Chart(document.getElementById("commitmentsChart"), {
		type: 'horizontalBar',
		data: 
			{
				labels: <?=$dataLabels?>,
				datasets: [{
					label: '',
					backgroundColor: 'rgba(91, 155, 213, 0.3)',
					borderColor: 'rgba(65, 113, 156, 1)',
					borderWidth: 1,
					data: <?=$dataValues?>
				}]
			},
		options:
			{
				responsive: true,
				title:
					{
						display: true,
						text: 'Commitments made so far',
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
	
	require 'footer.php';
	
?>		

	