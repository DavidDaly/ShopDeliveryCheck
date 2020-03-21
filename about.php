<?php 
	/* Copyright 2020 David Daly
	 * Licensed under MIT (https://github.com/DavidDaly/ShopDeliveryCheck/blob/master/LICENSE) */
	
	$isForm = FALSE;
	$activePage = 'About';
	
	require 'header.php';

	function RenderTwitterLink($URL)
	{
		?>
		<a style="color:#00A3F3" href="<?=$URL?>" target="_blank">
			<span class="fa-stack fa-1x">
				<i class="fas fa-square fa-stack-2x"></i>
				<i class="fab fa-twitter fa-stack-1x fa-inverse"></i>
			</span>
		</a>
		<?php
	}
	
	function RenderLinkedInLink($URL)
	{
		?>
		<a style="color:#0078B5" href="<?=$URL?>" target="_blank">
			<span class="fa-stack fa-1x">
				<i class="fas fa-square fa-stack-2x"></i>
				<i class="fab fa-linkedin-in fa-stack-1x fa-inverse"></i>
			</span>
		</a>  
		<?php
	}
	
?>

	<div class="container-fluid">
	<div class="row">
	<div class="col-xl-9 col-lg-11 m-2 pb-4 rounded text-center text-light mx-auto">
	

			<section class="jumbotron text-center text-dark border-primary border">
				<div class="container">
					<h1 class="jumbotron-heading">Keep food delivery for people who most need it</h1>
					<p class="lead">With the huge impact of COVID-19 there are many people who cannot leave their home to do shopping. 
									Whether this is because they are following UK government advice to help stop the spread of the virus, or for another reason, 
									it is vital at this time that we use the delivery slots available to support them. This means that anyone who can shop in-store should do so.
									By sharing informaion sbout yourself here, you will help us all to <b>do the right thing</b>.</p>
					<p>
						<a href='your-information<?=$urlPostfix?>' class="btn btn-primary"><b>Enter your details</b></a>
					</p>
					<hr />
					<p>
						<?=$socialLinks?>
					</p>
					<p>
						Want to contribute? Why not <a href="https://github.com/DavidDaly/ShopDeliveryCheck">fork us on GitHub</a>.
					</p>
				</div>
			</section>
		
	
	</div><!-- /.container -->
	
<?php
	
	require 'footer.php';
	
?>		

	