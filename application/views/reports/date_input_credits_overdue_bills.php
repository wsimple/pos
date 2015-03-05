<?php $this->load->view("partial/header"); 

echo form_open_multipart('reports/customers_credit_overdue_bills_data/',array('id'=>'credit_customers_data'));
?>

<div id="page_title" style="margin-bottom:8px;"><?php echo $report_name.' '.$this->lang->line('reports_report_input').' - '.$customers_name; ?></div>

<?php 

	if(isset($error)){
		echo "<div class='error_message' style=' margin: 0 0 10px 0'>".$error."</div>";
	}
?>

<div class="box-form-view" style="min-height: 150px !important">

<?php 
	if ($this->Employee->isAdmin()){
	$dbs = $this->Location->get_select_option_list(false, true);
	$dbs['default']='Principal';
	if (count($dbs)>1) $dbs['all'] = 'All';
?>
	<div style="margin-top: 60px">
		<label class="lable-form" for="locationbd">Select a location:</label>&nbsp;
		<?=form_dropdown('locationbd', $dbs,'', 'id="locationbd"')?>
	</div>
<?php }else{ 
		form_hidden('locationbd', $this->session->userdata('dblocation'));
	 } ?>	
	<!-- <div class="sub-title-view">
		<?php echo form_label($this->lang->line('reports_date_range'), 'report_date_range_label'); ?>:
	</div> -->

	<!-- <div id='report_date_range_simple' style="padding: 0 0 0 25px">
		<input type="radio" name="report_type" id="simple_radio" value='simple' checked='checked'/>
		<?php echo form_dropdown('report_date_range_simple',$report_date_range_simple, '', 'id="report_date_range_simple"'); ?>
	</div> -->
		
	<!-- <div id='report_date_range_complex' style="padding: 0 0 0 25px">
		<input type="radio" name="report_type" id="complex_radio" value='complex' />
		<?php echo form_dropdown('start_month',$months, $selected_month, 'id="start_month"'); ?>
		<?php echo form_dropdown('start_day',$days, $selected_day, 'id="start_day"'); ?>
		<?php echo form_dropdown('start_year',$years, $selected_year, 'id="start_year"'); ?>
		-
		<?php echo form_dropdown('end_month',$months, $selected_month, 'id="end_month"'); ?>
		<?php echo form_dropdown('end_day',$days, $selected_day, 'id="end_day"'); ?>
		<?php echo form_dropdown('end_year',$years, $selected_year, 'id="end_year"'); ?>
	</div> -->
	
	<!-- <div class="sub-title-view">
		<?php echo form_label($this->lang->line('reports_sale_type'), 'reports_sale_type_label'); ?>:
	</div>

	<div id='report_sale_type' style="padding: 0 0 30px 25px">
		<?php echo form_dropdown('sale_type',array('all' => $this->lang->line('reports_all'), 'sales' => $this->lang->line('reports_sales'), 'returns' => $this->lang->line('reports_returns')), 'all', 'id="sale_type"'); ?>
	</div> -->
	<!-- <input type="hidden" id="location" name="location" value="default"> -->

	<input type="hidden" id="start_date_complete" name="start_date_complete">
	<input type="hidden" id="end_date_complete" name="end_date_complete"> 
	<input type="hidden" id="customer_id" name="customer_id" value="<?=$customers_id?>">

</div>
<a class="linkBack big_button" style="height: auto" href="#"><span>Back</span></a>
<?php
	echo form_button(array(
		'name'=>'submit',
		'id'=>'submit',
		'content'=>'<span>'.$this->lang->line('common_submit').'</span>',
		'class'=>'big_button',
		'style'=>'height: auto;',
		'type'=>'submit'
		)
	);
	echo form_close(); 
	
	$this->load->view("partial/footer");
?>

<script type="text/javascript" language="javascript">
(function($){
	// $("#generate_report").click(function()
	// {		console.log($('#locationbd').val());
	// 	var sale_type = $("#sale_type").val();
		
	// 	if ($("#simple_radio").attr('checked'))
	// 	{
	// 		window.location = window.location+'/'+$("#report_date_range_simple option:selected").val() + '/' + sale_type+'?loc='+encodeURI($('#locationbd').val().replace('&','%26'));
	// 	}
	// 	else
	// 	{
	// 		var start_date = $("#start_year").val()+'-'+$("#start_month").val()+'-'+$('#start_day').val();
	// 		var end_date = $("#end_year").val()+'-'+$("#end_month").val()+'-'+$('#end_day').val();
	// 		window.location = window.location+'/'+start_date + '/'+ end_date+ '/' + sale_type+'?loc='+encodeURI($('#locationbd').val().replace('&','%26'));
	// 	}
	// });
	
	$("#start_month, #start_day, #start_year, #end_month, #end_day, #end_year").click(function()
	{
		$("#complex_radio").attr('checked', 'checked');
	});
	
	// $('#credit_customers_data').validate({
	// 		submitHandler:function(form)
	// 		{	
	// 			//fecha inicio
	// 			$('#start_date_complete').val($('#start_year').val()+'-'+$('#start_month').val()+'-'+$('#start_day').val());
	// 			//fecha final
	// 			$('#end_date_complete').val($('#end_year').val()+'-'+$('#end_month').val()+'-'+$('#end_day').val());

	// 			alert($('#start_date_complete').val()+' '+$('#end_date_complete').val());
	// 			$(form).ajaxSubmit({
	// 				success:function(response)
	// 				{
	// 						tb_remove();
	// 						post_person_form_submit(response);
	// 				},
	// 				dataType:'json'
	// 			});
				

	// 		},
	// 		errorLabelContainer: "#error_message_box",
	//  		wrapper: "li",
	// 		rules: 
	// 		{
	//     		pay_amount_credit:
	// 			{
	// 				required:true,
	// 				number:true
	// 			}
	//    		},
	// 		messages: 
	// 		{
	//      		pay_amount_credit:"<?php echo $this->lang->line('customers_pay_input_empty');  ?>"
	// 		}
	// 	});

 })(jQueryNew);
</script>