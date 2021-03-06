<?php 
	/* Copyright 2020 David Daly
	 * Licensed under MIT (https://github.com/DavidDaly/ShopDeliveryCheck/blob/master/LICENSE) */
	
	$isForm = TRUE;
	$activePage = 'Your viewpoint';
	require 'header.php';
	
	// Setup variables for rendering
	
	// Start by checking that a valid postcode has been entered
	$postcodeError = '';
	if  ( isset($_POST['POSTCODE']) )
	{
		if ( !$postcodeValid )
		{
			$postcodeError = '<font color="red">Please enter a valid postcode (first half only)</font>';
		}
	}
	
	// Delivery availability
	$availYesChecked = '';
	$availNoChecked = '';
	$availDontKnowChecked = '';
	$deliveryAvailableError = '';	
	if ( !is_null($deliveryAvailable) )
	{
		if ( $deliveryAvailable == '0')
		{
			$availNoChecked = 'checked="true"';
		}
		elseif ( $deliveryAvailable == '1')
		{
			$availYesChecked = 'checked="true"';
		}
		else
		{
			$availDontKnowChecked = 'checked="true"';
		}
	}
	else
	{
		if ( isset($_POST['POSTCODE']) )
		{
			$deliveryAvailableError = '<font color="red">Please tell us if deliveries are available in your area</font>';
		}
	}
	
	// Commit to stopping non essential delivery
	$nonEssentialCommitYesChecked = '';
	$nonEssentialCommitNoChecked = '';
	$nonEssentialCommitError = '';	
	if ( !is_null($nonEssentialCommit) )
	{
		if ( $nonEssentialCommit == 'FALSE')
		{
			$nonEssentialCommitYesChecked = '';
			$nonEssentialCommitNoChecked = 'checked="true"';
		}
		else
		{
			$nonEssentialCommitYesChecked = 'checked="true"';
			$nonEssentialCommitNoChecked = '';
		}
	}
	else
	{
		if ( isset($_POST['POSTCODE']) )
		{
			$nonEssentialCommitError = '<font color="red">Please tell us if you will commit to not using home delivery services unless it is essential for you to do so</font>';
		}
	}
	
	// What "need group" they are in
	$needGroupChecked = array('', '', '', '');
	$needGroupError = '';
	if ( !is_null($needGroup) )
	{
		for ( $i=0; $i<4; $i++ )
		{
			if ( strval($needGroup)-1 == $i )
			{
				$needGroupChecked[$i] = 'checked="true"';
				$needGroup = $i+1;
			}
		}
	}
	else
	{
		if  ( isset($_POST['POSTCODE']) )
		{
			$needGroupError = '<font color="red">Please tell us how reliant you are on home deliveries</font>';
		}
	}
	
?> 
 
	<div class="container-fluid">
	
	<form action="your-information<?=$urlPostfix?>" method="POST">

	<div class="row">
		<div class="col-xl-9 col-lg-11 m-2 pb-4 rounded text-center text-dark mx-auto">
			<h3>Your Information</h3>
		
				<div class="card mt-4  ml-sm-2 ml-xs-0 mr-sm-2 mr-xs-0 text-dark text-left bg-light border-primary border">
						<div class="card-body pt-1 pb-1 bg-gradient-secondary">
							By using this site you agree that the data you provide can be used in order to generate summary statistics. The data may also be shared with 3rd parties (such as supermarkets, the UK Government, or other relevant organisations) to assist with their planning.<br /><br />
							Please complete the questions below and then click on Save.
						</div>
				</div>
		
				<div class="card mt-4  ml-sm-2 ml-xs-0 mr-sm-2 mr-xs-0 text-dark text-left bg-light border-primary border">
					<h6 class="card-header">Which statement best describes how reliant you are on shopping deliveries at this time:</h6>
						<div class="card-body pt-1 pb-1 bg-gradient-secondary">
							<?=$needGroupError?>
							<div class="custom-control custom-radio my-2">
								<input type="radio" class="custom-control-input" id="NEED-GROUP-1" value="NEED-GROUP-1" name="NEED-GROUP" <?=$needGroupChecked[0]?>>
								<label class="custom-control-label" for="NEED-GROUP-1"><b>Totally reliant:</b> I need to have my shopping delivered. I can't leave the house (due to self-isolation or another reason). I have no one who can shop on my behalf.
								</label>
							</div>
							<div class="custom-control custom-radio my-2">
								<input type="radio" class="custom-control-input" id="NEED-GROUP-2" value="NEED-GROUP-2" name="NEED-GROUP" <?=$needGroupChecked[1]?>>
								<label class="custom-control-label" for="NEED-GROUP-2"><b>Highly reliant:</b> I strongly prefer to have my shopping delivered. I cannot leave the house (due to self-isolation or another reason) but I have someone who can shop on my behalf.
								</label>
							</div>
							<div class="custom-control custom-radio my-2">
								<input type="radio" class="custom-control-input" id="NEED-GROUP-3" value="NEED-GROUP-3" name="NEED-GROUP" <?=$needGroupChecked[2]?>>
								<label class="custom-control-label" for="NEED-GROUP-3"><b>Delivery preferred:</b> I prefer to have my shopping delivered but I am able to shop in store.
								</label>
							</div>
							<div class="custom-control custom-radio my-2">
								<input type="radio" class="custom-control-input" id="NEED-GROUP-4" value="NEED-GROUP-4" name="NEED-GROUP" <?=$needGroupChecked[3]?>>
								<label class="custom-control-label" for="NEED-GROUP-4"><b>Delivery not needed:</b> It is no problem for me to do my shopping in store.
								</label>
							</div>
						</div>
				</div>
		
				<div class="card mt-4  ml-sm-2 ml-xs-0 mr-sm-2 mr-xs-0 text-dark text-left bg-light border-primary border">
					<h6 class="card-header">Are supermarket home delivery slots available over the next 7 days in your area?</h6>
						<div class="card-body pt-1 pb-1 bg-gradient-secondary">
							<?=$deliveryAvailableError?>
							<div class="custom-control custom-radio my-2">
								<input type="radio" class="custom-control-input" id="AVAIL-YES" value="AVAIL-YES" name="AVAIL" <?=$availYesChecked?>>
								<label class="custom-control-label" for="AVAIL-YES">Yes</label>
							</div>
							<div class="custom-control custom-radio my-2">
								<input type="radio" class="custom-control-input" id="AVAIL-NO" value="AVAIL-NO" name="AVAIL" <?=$availNoChecked?>>
								<label class="custom-control-label" for="AVAIL-NO">No</label>
							</div>
							<div class="custom-control custom-radio my-2">
								<input type="radio" class="custom-control-input" id="AVAIL-DONT-KNOW" value="AVAIL-DONT-KNOW" name="AVAIL" <?=$availDontKnowChecked?>>
								<label class="custom-control-label" for="AVAIL-DONT-KNOW">Don't know</label>
							</div>
						</div>
				</div>
		
				<div class="card mt-4  ml-sm-2 ml-xs-0 mr-sm-2 mr-xs-0 text-dark text-left bg-light border-primary border">
					<h6 class="card-header">Will you commit to not using home delivery services unless it is essential for you to do so?</h6>
						<div class="card-body pt-1 pb-1 bg-gradient-secondary">
							<?=$nonEssentialCommitError?>
							<div class="custom-control custom-radio my-2">
								<input type="radio" class="custom-control-input" id="NON-ESSENTIAL-COMMIT-YES" value="NON-ESSENTIAL-COMMIT-YES" name="NON-ESSENTIAL-COMMIT" <?=$nonEssentialCommitYesChecked?>>
								<label class="custom-control-label" for="NON-ESSENTIAL-COMMIT-YES">Yes</label>
							</div>
							<div class="custom-control custom-radio my-2">
								<input type="radio" class="custom-control-input" id="NON-ESSENTIAL-COMMIT-NO" value="NON-ESSENTIAL-COMMIT-NO" name="NON-ESSENTIAL-COMMIT" <?=$nonEssentialCommitNoChecked?>>
								<label class="custom-control-label" for="NON-ESSENTIAL-COMMIT-NO">No</label>
							</div>
						</div>
				</div>
		
				<div class="card mt-4  ml-sm-2 ml-xs-0 mr-sm-2 mr-xs-0 text-dark text-left bg-light border-primary border">
					<h6 class="card-header">Please enter your postcode (first half only):</h6>
						<div class="card-body pt-1 pb-1 bg-gradient-secondary">
							<?=$postcodeError?>
							<input type="text" class="form-control mt-2 mb-2" id="POSTCODE" name="POSTCODE" placeholder="AA11" value="<?=$postcode?>" maxlength="4">
						</div>
				</div>
		
		
		</div>		
				
	</div>	
		
	<div class="row form-group">
	<div class="text-center col-lg-12">
		<div class="btn-group btn-group-justified">
			<div class="btn-group" role="group">
				<button type="submit" class="btn btn-primary">Save</button>
			</div>
		</div>

	</div>
	</div>
	
	</form>	
	
<?php
	
	require 'footer.php';
	
?>	

	
	
		

	

	