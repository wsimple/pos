<!DOCTYPE html>
<html>
<head>
<link href='<?php echo base_url();?>css/fullcalendar.css' rel='stylesheet' />
<link href='<?php echo base_url();?>css/fullcalendar.print.css' rel='stylesheet' media='print' />
<link href='<?php echo base_url();?>css/ospos.css' rel='stylesheet' media='print' />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src='<?php echo base_url();?>js/fullcalendar.js'></script>
<script>
	$(document).ready(function() {
		var date = new Date(<?php echo $year ?>, <?php echo $month ?>, 1);
		var m = date.getMonth();
		var y = date.getFullYear();
		$('#calendar').fullCalendar({
			year: y,
			month: m,
			contentHeight: 600,
			weekMode: 'liquid',
			events: "<?php echo base_url();?>/index.php/employees/json_calendar/<?php echo $employee_id; ?>",
			header:{
				left: 'title',
				center: '',
				right: 'prev,next'
			},
			
			loading: function(bool) {
				if(bool){ 
					$('#loading').show();
				}else {
					$('#loading').hide();
				};
			}
			
		});
		
	});
</script>
<style>
	body {
		text-align: center;
		font-size: 14px;
		font-family: "Lucida Grande",Helvetica,Arial,Verdana,sans-serif;
	}

	#calendar {
		width: 1024px;
		margin: 0 auto;
	}

	.header-table{
		width: 100%;
	}
	.header-table tr td{
		width: 33.3%;
		text-align: center;
	}
	#loading{
		position: absolute;
		width: 100%;
		height: 100%;
		background-color: hsla(0, 0%, 80%, 0.7);
		 z-index: 5000;
		 top: 0;
		 left: 0;
		 padding: 2em 0;
	}
</style>
</head>
<body>
<?php if ($employee != ' '): ?>
<div id="loading">
	<img src="<?php echo base_url();?>images/loading.gif" alt="Loading">
	<br><h1>Loading data, Please wait...</h1>
</div>
<table class="header-table">
	<tr>
		<td><img src="<?php echo base_url();?>images/logo.png" alt="Logo"></td>
		<td>
			<div id="receipt_header">
				<div id="company_name"><?php echo $this->config->item('company'); ?></div>
				<div id="company_address"><?php echo nl2br($this->config->item('address')); ?></div>
				<div id="company_phone"><?php echo $this->config->item('phone'); ?></div>
			</div>
			<h1><?php echo $this->lang->line('employees_employee').": ".$employee ?></h1>
		</td>
		<td></td>
	</tr>
</table>
<hr>
<div id='calendar'></div>
<?php else: ?>
	<h1>Error</h1>
<?php endif ?>
</body>
</html>
