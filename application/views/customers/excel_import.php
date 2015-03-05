<?php echo form_open_multipart('customers/do_excel_import/',array('id'=>'item_form')); ?>

<div>
	<h3><?php echo $this->lang->line('customers_importar'); ?></h3><hr>
		<div class="field_row clearfix">
			<div>
				<?php echo form_label('File path:', 'name',array('class'=>'wide')); ?>
				<div class="form_field">
				<?php echo form_upload(array(
					'name'=>'file_path',
					'id'=>'file_path',
					'value'=>'')
				);?>
				</div>
			</div>
	</div>
</div>
<b><a href="<?php echo site_url('customers/excel'); ?>"><?php echo $this->lang->line('customers_dow_excel'); ?></a></b>
<div class="field_row clearfix requested">
	<?php echo $this->lang->line("customers_resq_excel"); ?>
</div>
<ul id="error_message_box"></ul>
<?php
echo form_submit(array(
	'name'=>'submitf',
	'id'=>'submitf',
	'value'=>$this->lang->line('common_submit'),
	'class'=>'small_button float_right'
)); ?>
<?php 
echo form_close();
?>
<script type='text/javascript'>
//validation and submit handling
$(document).ready(function(){	
	$('#item_form').validate({
		submitHandler:function(form)
		{
			$(form).ajaxSubmit({
			success:function(response)
			{
				tb_remove();
				post_person_form_submit(response);
			},
			dataType:'json'
		});

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules: 
		{
			file_path:"required"
   		},
		messages: 
		{
   			file_path:"Full path to excel file required"
		}
	});
});
</script>