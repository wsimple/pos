<?php echo form_open('suppliers/save/'.$person_info->person_id,array('id'=>'supplier_form')); ?>
<div>

	<h3 class="user-icon">
		<?php 
			echo $this->lang->line('suppliers_h3_contact_info');
			$account = ($person_info->account_number!='') ? '<span style="padding-right: 30px;float: right; font-weight: bold;"> '.$this->lang->line('customers_account_number').' '.$person_info->account_number.'</span>':'';
			echo $account; 
		?>
	</h3>

	<div class="field_row clearfix">
		<div style="width: 180px; float: left">	
			<?php echo form_label($this->lang->line('common_first_name').':', 'first_name',array('class'=>'lable-form-required')); ?>
			<div>
			<?php echo form_input(array(
				'name'=>'first_name',
				'id'=>'first_name',
				'value'=>$person_info->first_name,
				'class'=>'text_box'
			));?>
			</div>
		</div>
		
		<div style="width: 180px; float: left">	
			<?php echo form_label($this->lang->line('common_last_name').':', 'last_name',array('class'=>'lable-form-required')); ?>
			<div>
			<?php echo form_input(array(
				'name'=>'last_name',
				'id'=>'last_name',
				'value'=>$person_info->last_name,
				'class'=>'text_box'
			));?>
			</div>
		</div>

		<div style="width: 180px; float: left">	
			<?php echo form_label($this->lang->line('common_email').':', 'email',array('class'=>'lable-form-required')); ?>
			<div>
			<?php echo form_input(array(
				'name'=>'email',
				'id'=>'email',
				'value'=>$person_info->email,
				'class'=>'text_box'
			));?>
			</div>
			<div id="error_email" style="display:none; float: left; width: 155px;padding: 3px 0px;background-color: red;color: #FFF;"><?=$this->lang->line('email_registered')?></div>
		</div>
		
		<div style="width: 180px; float: left">	
			<?php echo form_label($this->lang->line('common_phone_number').':', 'phone_number',array('class'=>'lable-form-required')); ?>
			<div>
			<?php echo form_input(array(
				'name'=>'phone_number',
				'id'=>'phone_number',
				'value'=>$person_info->phone_number,
				'class'=>'text_box'
			));?>
			</div>
		</div>
	</div>

	<div class="clearfix my_height"></div>

	<h3 class="location-icon"><?=$this->lang->line('suppliers_h3_location')?></h3>

	<div class="field_row clearfix">
		<div style="width: 180px; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('common_country').':', 'country',array('class'=>'lable-form-required')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'country',
					'id'=>'country',
					'value'=>$person_info->country,
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 180px; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('common_state').':', 'state',array('class'=>'lable-form-required')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'state',
					'id'=>'state',
					'value'=>$person_info->state,
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 180px; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('common_city').':', 'city',array('class'=>'lable-form-required')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'city',
					'id'=>'city',
					'value'=>$person_info->city,
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 180px; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('common_zip').':', 'zip',array('class'=>'lable-form-required')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'zip',
					'id'=>'zip',
					'value'=>$person_info->zip,
					'class'=>'text_box'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 100%; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('common_address_1').':', 'address_1',array('class'=>'lable-form-required')); ?>
				<div>
				<?php echo form_textarea(array(
					'name'=>'address_1',
					'id'=>'address_1',
					'value'=>$person_info->address_1,
					'class'=>'text_box',
					'rows'=>'5',
					'cols'=>'70',
					'style' => 'width: 100%'
				));?>
				</div>
			</div>
		</div>
	</div>

	<div class="clearfix my_height"></div>

	<h3 class="business-icon"><?=$this->lang->line('suppliers_h3_business_info')?></h3>

	<div class="field_row clearfix">
		<div style="width: 180px; float: left;">	
			<?php echo form_label($this->lang->line('suppliers_company_name'), 'company_name',array('class'=>'lable-form-required')); ?>
			<div>
			<?php echo form_input(array(
				'name'=>'company_name',
				'id'=>'company_name',
				'value'=>$person_info->company_name,
				'class'=>'text_box',
				'style'=>'font-size: 12px; font-weight: normal;'
			));?>
			</div>
		</div>		

		<div style="width: 180px; float: left">	
			<?php echo form_label($this->lang->line('suppliers_label_work_phone'), 'work_phone',array('class'=>'lable-form-required')); ?>
			<div>
			<?php echo form_input(array(
				'name'=>'work_phone',
				'id'=>'work_phone',
				'value'=>$person_info->work_phone,
				'class'=>'text_box'
			));?>
			</div>
		</div>

		<div style="width: 180px; float: left">	
			<?php echo form_label($this->lang->line('suppliers_label_supplied'), 'prod_supplied',array('class'=>'lable-form-required')); ?>
			<div>
			<?php echo form_input(array(
				'name'=>'prod_supplied',
				'id'=>'prod_supplied',
				'value'=>$person_info->product_supplied,
				'class'=>'text_box'
			));?>
			</div>
		</div>

		<div style="width: 180px; float: left">	
			<?php echo form_label($this->lang->line('suppliers_label_discunt'), 'discount',array('class'=>'lable-form-required')); ?>
			<div>
			<?php echo form_input(array(
				'name'=>'discount',
				'id'=>'discount',
				'value'=>$person_info->discounts,
				'class'=>'text_box'
			));?>
			</div>
		</div>

		<div style="width: 100%; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('suppliers_label_bank_info'), 'bank_info',array('class'=>'lable-form-required')); ?>
				<div>
				<?php echo form_textarea(array(
					'name'=>'bank_info',
					'id'=>'bank_info',
					'value'=>$person_info->bank_info,
					'class'=>'text_box',
					'rows'=>'5',
					'cols'=>'70',
					'style' => 'width: 100%'
				));?>
				</div>
			</div>
		</div>

		<div style="width: 100%; float: left">
			<div class="field_row clearfix">	
				<?php echo form_label($this->lang->line('common_comments').':', 'comments',array('class'=>'lable-form')); ?>
				<div>
				<?php echo form_textarea(array(
					'name'=>'comments',
					'id'=>'comments',
					'value'=>$person_info->comments,
					'class'=>'text_box',
					'rows'=>'5',
					'cols'=>'70',
					'style' => 'width: 100%'
				));?>
				</div>
			</div>
		</div>

	</div>
	
</div>
<div class="field_row clearfix requested">
	<?=$this->lang->line('common_fields_required_message')?>
</div>
<input type="hidden" id="email_active" name="email_active" value="0">
<input type="hidden" id="email_existente" name="email_existente" value="<?=$person_info->email?>">

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
				if (response.email &&  $('#email_existente').val()!=response.email) {
        			$('input#email').css({'color':'#FFF','background-color':'red'});
            		$('#error_email').css('display', 'block');
            		$('#email_active').val('1');
        		}else{
					tb_remove();
					post_person_form_submit(response);
				}
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
			    minlength: 2
		    },
    		email: {
			    required: true,
			    email: "email"
		    },
		    phone_number:
			{
				required:true,
				number:true
			},
		    country:
			{
				required:true,
				regex:/^[a-zA-Z\s]+$/
			},
		    state:
			{
				required:true,
				regex:/^[a-zA-Z\s]+$/
			},
		    city:
			{
				required:true,
				regex:/^[a-zA-Z\s]+$/
			},
		    zip:
			{
				required:true,
				number:true
			},
		    work_phone:
			{
				required:true,
				number:true
			},
		    prod_supplied:
			{
				required:true
			},
		    discount:
			{
				required:true,
				number:true
			},
		    bank_info:
			{
				required:true
			},
		    address_1:
			{
				required:true
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
			      regex:"<?php echo  $this->lang->line('common_first_name_only_char');?>"
    		},
    		last_name: {
			      required: "<?php echo $this->lang->line('common_last_name_required'); ?>",
			      regex:"<?php echo  $this->lang->line('common_first_name_only_char');?>",
			      minlength: jQuery.format("<?php echo $this->lang->line('common_at_least'); ?> {0} <?php echo $this->lang->line('common_at_characters'); ?>!")
    		},
     		email: "<?php echo $this->lang->line('common_email_invalid_format'); ?>",
     		phone_number:"<?php echo $this->lang->line('common_phone_invalid_format');  ?>",
     		address_1: "<?php echo $this->lang->line('suppliers_msg_address'); ?>",
     		country: {
			      required: "<?php echo $this->lang->line('suppliers_msg_country'); ?>",
			      regex:"<?php echo $this->lang->line('suppliers_msg_country01'); ?>"
    		},     		
    		state: {
			      required: "<?php echo $this->lang->line('suppliers_msg_state'); ?>",
			      regex:"<?php echo $this->lang->line('suppliers_msg_state01'); ?>"
    		},     		
    		city: {
			      required: "<?php echo $this->lang->line('suppliers_msg_city'); ?>",
			      regex:"<?php echo $this->lang->line('suppliers_msg_city01'); ?>"
    		},
    		zip: "<?php echo $this->lang->line('suppliers_msg_zip'); ?>",
    		work_phone: "<?php echo $this->lang->line('suppliers_msg_work_phone'); ?>",
    		prod_supplied: "<?php echo $this->lang->line('suppliers_msg_supplied'); ?>",
    		discount: "<?php echo $this->lang->line('suppliers_msg_discount'); ?>",
    		bank_info: "<?php echo $this->lang->line('suppliers_msg_bank_info'); ?>"
		}
	});
});
(function($){
	$(function(){
	    $('input#email').focusout(function() {
	        $.ajax({
	            type: "POST",
	            url:'index.php/suppliers/exists_email',
	            data: "email="+$('input#email').val(),
	            dataType: 'json',
	            success: function(respuesta){
	            	console.log('si existe: '+respuesta);
	            	console.log($('#email_existente').val());
	            	if (respuesta.success) { 

	            		if ($('#email_existente').val()!=respuesta.email) {
	            			$('input#email').css({'color':'#FFF','background-color':'red'});
		            		$('#error_email').css('display', 'block');
		            		$('#email_active').val('1');
	            		}else{
		            		$('input#email').css({'color':'#000','background':'none'});
		            		$('#error_email').css('display', 'none');
		            		$('#email_active').val('0');
		            	};
	            	}else{
	            		$('input#email').css({'color':'#000','background':'none'});
	            		$('#error_email').css('display', 'none');
	            		$('#email_active').val('0');
	            	};
	            }
	        });       
	    });

	});
})(jQueryNew);
</script>