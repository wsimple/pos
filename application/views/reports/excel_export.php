<?php $this->load->view("partial/header"); ?>

<div id="page_title" style="margin-bottom:8px;"><?php echo $report_name.' '.$this->lang->line('reports_report_input'); ?></div>

<?php
	$dbs = $this->Location->get_select_option_list(false, true);
	$dbs['default']='Principal';
	if (count($dbs)>1) $dbs['all'] = 'All';
	
	if(isset($error)){
		echo "<div class='error_message' style=' margin: 0 0 10px 0'>".$error."</div>";
	}
?>	

<div class="box-form-view">

	<div>
		<label class="lable-form" for="locationbd">Select a location:</label>&nbsp;
		<?=form_dropdown('locationbd', $dbs,'', 'id="locationbd"')?>
	</div>

	<div class="sub-title-view">
		Export to Excel:
	</div>
	
	<div style="padding: 0 0 0 25px; font-size: 11px; font-weight: bold;">
		<input type="radio" name="export_excel" id="export_excel_yes" value='1' /> Yes&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<input type="radio" name="export_excel" id="export_excel_no" value='0' checked='checked' /> No
	</div>

</div>

<a class="linkBack big_button" style="height: auto" href="#"><span>Back</span></a>
&nbsp;
<?php
echo form_button(array(
	'name'=>'generate_report',
	'id'=>'generate_report',
	'content'=>$this->lang->line('common_submit'),
	'class'=>'big_button',
	'style'=>'height: auto;')
);
?>

<?php $this->load->view("partial/footer"); ?>

<script type="text/javascript" language="javascript">
$(document).ready(function()
{
	$("#generate_report").click(function()
	{
		var export_excel = 0;
		if ($("#export_excel_yes").attr('checked')){ export_excel = 1; }
		
		window.location = window.location+'/' + export_excel+'/'+$('#locationbd').val();
	});	
});
</script>