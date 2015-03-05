<?php echo form_open('suppliers/save/'.$person_info->person_id,array('id'=>'supplier_form')); ?>
<div>
	<h3><?php echo $this->lang->line("suppliers_basic_information"); ?></h3><hr>	
	<div class="field_row clearfix">
		<div style="width: 180px; float: left">	
			<?php echo form_label($this->lang->line('suppliers_company_name').':', 'company_name',array('class'=>'lable-form-required')); ?>
			<div>
			<?php echo form_input(array(
				'name'=>'company_name',
				'id'=>'company_name_input',
				'value'=>$person_info->company_name,
				'class'=>'text_box'
			));?>
			</div>
		</div>
		<div style="width: 180px; float: left">	
			<?php echo form_label($this->lang->line('suppliers_account_number').':', 'account_number',array('class'=>'lable-form')); ?>
			<div>
			<?php echo form_input(array(
				'name'=>'account_number',
				'id'=>'account_number',
				'value'=>$person_info->account_number,
				'class'=>'text_box'
			));?>
			</div>
		</div>
	</div>
	<?php $this->load->view("people/form_basic_info"); ?>		
</div>
<div class="field_row clearfix requested">
	<?=$this->lang->line('common_fields_required_message')?>
</div>
<ul id="error_message_box"></ul>
<?php
echo form_submit(array(
	'name'=>'submit',
	'id'=>'submit',
	'value'=>$this->lang->line('common_submit'),
	'class'=>'small_button float_right')
); ?>
<?php echo form_close(); ?>
<script type='text/javascript'>
//validation and submit handling
$(document).ready(function()
{
	$('#supplier_form').validate({
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
			company_name: {
			    required: true,
			    minlength: 4
		    },
		    first_name: {
			    required: true,
			    regex:/^[a-zA-Z\s]+$/,
			    minlength: 3
		    },
		    last_name: {
			    required: true,
			    regex:/^[a-zA-Z\s]+$/,
			    minlength: 3
		    },
    		email: {
			    required: true,
			    email: "email"
		    },
		    phone_number:
			{
				required:true,
				number:true
			}
   		},
		messages: 
		{
     		company_name: {
			      required: "<?php echo $this->lang->line('suppliers_company_name_required'); ?>",
			      minlength: jQuery.format("<?php echo $this->lang->line('common_at_least'); ?> {0} <?php echo $this->lang->line('common_at_characters'); ?>!")
    		},
     		first_name: {
			      required: "<?php echo $this->lang->line('common_first_name_required'); ?>",
			      regex:"<?php echo  $this->lang->line('common_first_name_only_char');?>",
			      minlength: jQuery.format("<?php echo $this->lang->line('common_at_least'); ?> {0} <?php echo $this->lang->line('common_at_characters'); ?>!")
    		},
    		last_name: {
			      required: "<?php echo $this->lang->line('common_last_name_required'); ?>",
			      regex:"<?php echo  $this->lang->line('common_first_name_only_char');?>",
			      minlength: jQuery.format("<?php echo $this->lang->line('common_at_least'); ?> {0} <?php echo $this->lang->line('common_at_characters'); ?>!")
    		},
     		email: "<?php echo $this->lang->line('common_email_invalid_format'); ?>",
     		phone_number:"<?php echo $this->lang->line('common_phone_invalid_format');  ?>"
		}
	});
});
</script>