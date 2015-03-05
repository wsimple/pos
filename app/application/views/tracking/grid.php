<!doctype html>
<html class="no-js" lang="en">
<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Fast I Repair | Work Order Grid</title>
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
		
		<div class="row">
			<div class="large-12 columns">
				<table>
					<thead>
						<tr>
							<th width="10">#</th>
							<th width="100">Make</th>
							<th width="100">Model</th>
							<th width="100">Color</th>
							<th width="100">Customer</th>
							<th width="100">Received Date</th>
							<th width="100">Picked Date</th>
							<th width="50">Status</th>
							<th width="250">Problem</th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ($orders as $array){ ?>
						<tr>
							<td><?=$array['service_id']?></td>
							<td><?=$array['make']?></td>
							<td><?=$array['model']?></td>
							<td><?=$array['color']?></td>
							<td><?=$array['customer']?></td>
							<td><?=$array['date01']?></td>
							<td><?=(trim($array['date02'])!=''?$array['date02']:'At the store')?></td>
							<td><?=get_status($array['status'])?></td>
							<td><?=substr($array['problem'],0,100)?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</div>

		<div class="row">
			<div id="contact-reveal" class="reveal-modal small" data-reveal>
								<h2></h2>
								<h5></h5>
								<a class="close-reveal-modal">&#215;</a>
							</div>
		</div>
		
	</div>

	<script type="text/javascript" src="<?=base_url()?>js/jquery.1.8.2.min.js"></script>

	<script type="text/javascript" src="<?=base_url()?>js/jquery-ui.js"></script>
	
	<script type="text/javascript" src="<?=base_url()?>js/foundation.min.js"></script>

	<script type="text/javascript" src="<?=base_url()?>js/functions.js"></script>
	
	<script>
		$(document).foundation();

		$("#btnDetail").click(function() {


			$('#contact-reveal h2').html("Detail");
	        $('#contact-reveal h5').append("hello");
	        //$('#contact-reveal h5').append('<br/><br/>'+work_order);
	        $('#contact-reveal').foundation('reveal', 'open');
		       

  			
		});


		

	</script>

</body>
</html>