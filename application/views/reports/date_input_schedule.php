<?php $this->load->view("partial/header"); ?>

<div id="page_title" style="margin-bottom:8px;"><?php echo 'Schedule Employee '.$this->lang->line('reports_report_input'); ?></div>

<div class="box-form-view">
	<?php echo form_open($controller_name.'/report/'); ?>


	<div>
		<?php echo form_label('Employee:', 'search', array('class'=>'lable-form-required')); ?>
	</div>

	<div>
		<input type="text" name ='search' id='search' class="text_box" style="width: 50%" placeholder="Search ..." />
	</div>

	<div>
		&nbsp;
	</div>
	
	<div>
		<?php echo form_label('Select date:','month', array('class'=>'lable-form')); ?>
	</div>

	<div>
		<?php echo form_dropdown('month', $months_of_year); ?>
	</div>	

	<div>
		&nbsp;
	</div>

	<div>
		<?php echo form_label('Select Year:', 'year', array('class'=>'lable-form')); ?>
	</div>

	<div>
		<?php echo form_dropdown('year', $years); ?>
	</div>

</div>
<div class="field_row clearfix" style="color: #FF0000; font-size: 11px">
	The <strong>red</strong> field are required
</div>
<a class="linkBack big_button" style="height: auto" href="#"><span>Back</span></a>
&nbsp;
<input type="submit" value="Submit" name="submit" id="submit" style="height: auto" class="big_button">
<?php echo form_close(); ?>

<script>
	$(function() {
		$("#search").autocomplete('index.php/<?php echo $controller_name; ?>/suggest/1',
			{
				max:100,
				delay:10,
				selectFirst: false,
				formatItem: function(row) {
					return row[1];
				}
			}
		);

		// $("#search").result(function(event, data, formatted){
		// 	if ( $(this).val() != '' ) {
		// 		$.get('index.php/<?php echo $controller_name; ?>/worked_months', {'id': $(this).val()}, function(data) {
		// 			console.log(data);
		// 		});
		// 	}
		// });

		$('#submit').click(function(e){
			if ($('#search').val().length < 1) {
				notif({
				    type: "error",
				    msg: 'Search Employee can not be empty, Please Try Again!',
				    width: "all",
				    height: 100,
				    position: "center"
				});
				e.preventDefault();
				return false;
			}
		});
	});
</script>
<?php $this->load->view("partial/footer"); ?>
