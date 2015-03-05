<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Fast I Repair | Work Order</title>
	<link rel="stylesheet" href="<?=base_url()?>css/foundation.css" />
	<link href="<?=base_url()?>css/quicksign.css" rel="stylesheet">
	<link type="text/css" href="<?=base_url()?>css/jquery-ui.css" rel="stylesheet">
	<link type="text/css" href="<?=base_url()?>css/app.css" rel="stylesheet">
	<script src="<?=base_url()?>js/vendor/modernizr.js"></script>
</head>
<body>
    <?php 
    	//_imprimir($_POST);
    	if ($isCustomer=='0'){
    		$aux = explode(' ', $customerSelected);
    		$name = isset($aux[0])?$aux[0]:'';
    		$last_name = isset($aux[1])?$aux[1]:''; 
    	}
    ?>
	<div class="row">
		
		<div class="row">
			<img src="<?=base_url()?>images/logo150x78.png" alt="logo">
		</div>

		<hr>
		
		<div class="row">
			<form data-abide name="frmCases" id="frmCases" action="<?=$config['domain']?>/tracking/approval/" method="POST">
				<div class="large-12 columns">
					
					<div class="row" id="layerBackButton">
						<div class="large-10 columns">
							&nbsp;
						</div>
						<div class="large-2 columns">
							<button type="button" id="btnBackSearch" name="btnBackSearch" class="button expand">Back to Search</button>
						</div>
					</div>

					<?php if ($isCustomer==0){ //if it is a new customer ?>					
					<div class="row">
						<div class="large-12 columns" id="customers_form">
							<h4 class="subheader"><small class="sub_title">Customer Info</small></h4>
							<div class="row">
								<div class="large-3 columns">
									<label>
										<input type="text" name="txtFirstName" id="txtFirstName" value="<?=$name?>" pattern="alpha_numeric" placeholder="First Name" required />
									</label>
									<small class="error radius">First name is required and it has to be alpha numeric.</small>
								</div>
								<div class="large-3 columns">
									<label>
										<input type="text" name="txtLastName" id="txtLastName" value="<?=$last_name?>" pattern="alpha_numeric" placeholder="Last Name" required />
									</label>
									<small class="error radius">Last name is required and it has to be alpha numeric.</small>
								</div>
								<div class="large-3 columns">
									<label>
										<input type="text" name="txtPhoneNumber" id="txtPhoneNumber" placeholder="Phone Number" required />
									</label>
									<small class="error radius">Phone number is required.</small>
								</div>
								<div class="large-3 columns">
									<label>
										<input type="text" name="txtEmail" id="txtEmail" pattern="email" placeholder="Email" />
									</label>
									<small class="error radius">Email is required and it has to have a valid format.</small>
								</div>
							</div>
							
							<div class="row">
								<div class="large-3 columns">
									<label>
										<select name="cboState" id="cboState" required>
											<option value="">State</option>
											<?php foreach ($states as $array){ ?>
												<option value="<?=$array['state']?>"><?=$array['state']?></option>
											<?php } ?>	
										</select>
									</label>
									<small class="error radius">State is required.</small>
								</div>
								<div class="large-3 columns" id="citiesLayer">
									<label>
										<input type="text" name="txtCity" id="txtCity" pattern="" placeholder="City" required />
									</label>
									<small class="error radius">City is required.</small>
								</div>
								<div class="large-3 columns">
									<label>
										<input type="text" name="txtZip" id="txtZip"  placeholder="Zip Code" required />
									</label>
									<small class="error radius">Zip Code is required.</small>
								</div>
								<div class="large-3 columns">&nbsp;</div>
							</div>
						</div>
					</div>
					<?php 
						}else{ 
							form_hidden('isCustomer', $isCustomer);
							form_hidden('txtCustomer', $customerSelected);
					 	}
					 ?>

    				<h4 class="subheader"><small class="sub_title">Device Info</small></h4>
					<div class="row">
						<div class="large-4 columns">
							<label>
							<select name="cboPhoneBrand" id="cboPhoneBrand" required>
								<option value="">Make</option>
									<?php foreach ($phone_brand as $array){ ?>
								<option value="<?=$array['brand_id']?>"><?=$array['brand_name']?></option>
								<?php } ?>	
							</select>
							</label>
							<small class="error radius">Make is required.</small>
						</div>
						
						<div class="large-4 columns">
							<label>
								<input type="text" name="txtModel" id="txtModel" placeholder="Model" required />
							</label>
							<small class="error radius">Model is required.</small>
						</div>
						<div class="large-4 columns">
							<label>
								<input type="text" name="txtColor" id="txtColor" placeholder="Color" required />
							</label>
							<small class="error">Color is required.</small>
						</div>
					</div>	
					
					<h4 class="subheader"><small class="sub_title">Problem</small></h4>
					<div class="row">
						<div class="large-12 columns">
							<textarea id="txtProblem" name="txtProblem" placeholder="Description of Problem" required></textarea></label>
							<small class="error">Problem is required.</small>
						</div>
					</div>
					
					<div class="row">
						<div class="large-12 columns">
							<label for="chkTerm"  class="sub_title">
								<input id="chkTerm" type="checkbox" required>&nbsp;Term of Services
								<small class="error">Term of Services is required.</small>
							</label>
							<h5 class="subheader justify">
								<small class="black_color">
									I understand that Dash is not responsible for any damage to any items due to previous condition and/or usage. 
									Failure to inform the technician of any prior or current condition may result in testing, troubleshooting and repair methods being used 
									when they should not. In this case the customer is responsible for the damage of the product and any damage to equipment used to 
									troubleshoot the product, unless a technician has been negligent and/or abusive. Dash is not responsible for any damage to product 
									caused by attempting a repair in a proper way. Any equipment left over 30 days will be recycled. All personal data will be irrevocable 
									destroyed to protect your privacy. I understand that services rendered by Dash and any damage to this device or data are incidental to 
									the services rendered. Any warranty expressed or implied only covers that part and the labor provided on the part. All parts come with a 
									30 day warranty which does not include physical and/or liquid damage.
								</small>
							</h5>
						</div>
					</div>
					
					<hr>

					<div class="row">
						<div class="large-4 columns">
							<button type="button" id="btnBack" name="btnBack" class="button expand radius">&nbsp;&nbsp;&nbsp;&nbsp;Back&nbsp;&nbsp;&nbsp;&nbsp;</button>
							<input type="hidden" id="isCustomer" name="isCustomer" value="<?=$isCustomer?>">
							<input type="hidden" id="txtCustomer" name="txtCustomer" value="<?=$customerSelected?>">
						</div>
						<div class="large-4 columns">
							<div id="contact-reveal" class="reveal-modal small" data-reveal>
								<h2></h2>
								<h5></h5>
								<a class="close-reveal-modal">&#215;</a>
							</div>

						</div>
						<div class="large-4 columns">
							<button type="button" id="btnSave" name="btnSave" class="button expand radius">&nbsp;&nbsp;&nbsp;&nbsp;Sign and Confirm&nbsp;&nbsp;&nbsp;&nbsp;</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<script type="text/javascript" src="<?=base_url()?>js/jquery.1.8.2.min.js"></script>

	<script type="text/javascript" src="<?=base_url()?>js/jquery-ui.js"></script>
	
	<script type="text/javascript" src="<?=base_url()?>js/foundation.min.js"></script>

	<script type="text/javascript" src="<?=base_url()?>js/jquery.form.min.js"></script>

	<script type="text/javascript" src="<?=base_url()?>js/functions.js"></script>
	
	<script>
		$(document).foundation();

		$('#btnBack').click(function(){
			redirect('<?=base_url()?>');
		});

		$("#txtCity").keyup(function() {
			var txt = $(this);
			$.ajax({
				url: "<?=$config['domain']?>/zips/get_cities/"+($('#cboState').val()!=''?$('#cboState').val():'OK')+'/'+txt.val(),
			    dataType: 'json',
			    success : function(data) {
					var _cities = [];
					$.each(data, function(i, item) {
					    _cities.push(item.name); 
					});

					$("#txtCity").autocomplete({
						source: _cities
					});
			    }
			});
		});		

		//save
		$("#btnSave").click(function() {
			$('#frmCases').submit();
		});

	</script>
  </body>
</html>