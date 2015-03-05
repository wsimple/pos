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
    
	<div class="row">

		<div class="row">
			<img src="<?=base_url()?>images/logo150x78.png" alt="logo">
		</div>

		<hr>
		
		<h4 class="subheader"><small class="sub_title">Enter First Name and Last Name below to search or register as New Customer</small></h4>
		
		<div class="row">
			<form data-abide name="frmPickCustomer" id="frmPickCustomer" action="<?=$config['domain']?>/tracking/" method="POST">
				<div class="large-12 columns">
					
					<div class="row collapse">
						<div class="large-12 columns">
							<label>
								<input id="txtCustomer" name="txtCustomer" type="text" class="search_input" placeholder="Enter First Name and Last Name ..." required>
							</label>	
							<small class="error radius">Customer is required!</small>
						</div>
					</div>
					<hr>
					<div class="row">
						<div class="large-2 columns">
							<button type="button" id="btnContinue" name="btnContinue" class="button radius small">&nbsp;&nbsp;&nbsp;&nbsp;Continue&nbsp;&nbsp;&nbsp;&nbsp;</button>
							<input type="hidden" id="customerSelected" name="customerSelected" value="">
							<input type="hidden" id="isCustomer" name="isCustomer" value="">
						</div>
						<div class="large-2 columns">
							&nbsp;
						</div>
						<div class="large-6 columns">
							<div id="contact-reveal" class="reveal-modal small" data-reveal>
								<h2></h2>
								<h5></h5>
								<a class="close-reveal-modal">&#215;</a>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>

	<script type="text/javascript" src="<?=base_url()?>js/jquery.1.8.2.min.js"></script>

	<script type="text/javascript" src="<?=base_url()?>js/jquery-ui.js"></script>
	
	<script type="text/javascript" src="<?=base_url()?>js/foundation.min.js"></script>

	<script type="text/javascript" src="<?=base_url()?>js/functions.js"></script>
	
	<script>
		$(document).foundation();

		var out = '<?=(isset($out)?$out:'')?>';
		var title = '<?=(isset($title)?$title:'')?>';
		var message = '<?=(isset($message)?$message:'')?>';
		var work_order = '<?=(isset($work_order)?$work_order:'')?>';

		if (out=='ok'&&title!=''&&message!=''&&work_order!=''){
			$('#contact-reveal h2').html(title);
	        $('#contact-reveal h5').append(message);
	        $('#contact-reveal h5').append('<br/><br/>'+work_order);
	        $('#contact-reveal').foundation('reveal', 'open');
		        setTimeout(function(){
	                redirect('<?=(isset($url))?$url:base_url()?>');
	            }, 3000);
		}		

		$('#txtCustomer').on('autocompleteselect', function (e, ui) {
			$('#customerSelected').val(ui.item.value);
			$('#isCustomer').val('1');
			$('#frmPickCustomer').submit();
		});

		$('#txtCustomer').keypress(function(e) {
		    if (e.which == 13) {
		        $("#btnContinue").click();
		    }
		});

		$("#btnContinue").click(function() {
			$('#customerSelected').val($('#txtCustomer').val());
			$('#isCustomer').val('0');
			$('#frmPickCustomer').submit();
		});
		
		$("#txtCustomer").keyup(function() {
			var txt = $(this);
			$.ajax({
				url: "<?=$config['domain']?>/people/complete/"+txt.val(),
			    dataType: 'json',
			    success : function(data) {
					var _customers = [];
					$.each(data, function(i, item) {
					    _customers.push(item.name); 
					});

					$( "#txtCustomer" ).autocomplete({
						source: _customers
					});
			    }
			});
		});	
	</script>

</body>
</html>