<?php 
	echo form_open_multipart('customers/save/'.$person_info->person_id,array('id'=>'customer_form')); 
	$account = ($person_info->person_id!='') ? '<span style="float: right; font-weight: bold;"> '.$this->lang->line('customers_account_number').' '.$person_info->person_id.'</span>':'';
	$account = trim($account);
?>
<div>
	<h3 class="user-icon" <?php if ($account!='') echo 'style="padding-right: 40px "'; ?> >
		<?php 
			echo $this->lang->line("customers_basic_information");
			if ($account!='') echo $account; 
		?>
	</h3>
	
	<?php $this->load->view("people/form_basic_info"); ?>

	<div class="clearfix my_height"></div>

	<h3 class="info-icon"><?=$this->lang->line('customers_h3_additional_info')?></h3>

	<div class="field_row clearfix">
		<div style="width: 100%; float: left">
			<div class="field_row clearfix">
				<?php 
					echo form_checkbox('taxable', '1', $person_info->taxable == '' ? TRUE : (boolean)$person_info->taxable);
					echo form_label($this->lang->line('customers_taxable').':', 'taxable',array('class'=>'lable-form','style'=>'float:none')); 
				?>
			</div>
		</div>
		<div style="width: 180px; float: left">	
			<?php echo form_label($this->lang->line('customers_lbl_porce_discount').':', 'discount',array('class'=>'lable-form')); ?>
			<div>
			<?php echo form_input(array(
				'name'=>'discount',
				'id'=>'discount',
				'value'=>$person_info->discounts,
				'class'=>'text_box'
			));?>
			</div>
		</div>
		<div style="width: 180px; float: left">	
			<?php echo form_label($this->lang->line('customers_lbl_customer_type').':', 'customer_type',array('class'=>'lable-form')); ?>
			<div>
				<select id="customer_type" name="customer_type">
					<option value="0" <?php if ($person_info->type=='0') echo 'selected'; ?> ><?=$this->lang->line('customers_cbo_type_regular')?></option>
					<option value="1" <?php if ($person_info->type=='1') echo 'selected'; ?> ><?=$this->lang->line('customers_cbo_type_company')?></option>
				</select>
			</div>
		</div>
		<div style="width: 180px; float: left">	
			<?php echo form_label($this->lang->line('customers_lbl_tax_id').':', 'tax_id',array('class'=>'lable-form')); ?>
			<div>
			<?php echo form_input(array(
				'name'=>'tax_id',
				'id'=>'tax_id',
				'value'=>$person_info->tax_id,
				'class'=>'text_box'
			));?>
			</div>
		</div>
	</div>
	
	<div class="clearfix my_height"></div>
	<?php if($this->Employee->isAdmin()){
		if ($person_info->max_amount_credit!='') {
			$amount = explode('/', $person_info->max_amount_credit);
			echo '<input type="hidden" id="amountactive" name="amountactive" value="'.$amount[0].'">';

		} else {
			$amount[0] = '';
			$amount[1] = '';
		}
		?>

		<div style="width: 100%; float: left">
			<div class="field_row clearfix">
				<?php 
					// echo form_checkbox('give_credit', '1', $person_info->taxable == '' ? TRUE : (boolean)$person_info->taxable);
					echo form_checkbox(array(
						'name'        => 'give_credit',
					    'id'          => 'give_credit'
					));
					echo form_label('Give Credit'.':', 'give_credit',array('class'=>'lable-form','style'=>'float:none')); 
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<div  id="credit_panel" style="display:none">
			<h3 class="info-icon">Credits</h3>
			<div class="field_row clearfix">
				<div style="width: 180px; float: left">	
					<?php echo form_label('Amount:', 'amount',array('class'=>'lable-form')); ?>
					<div>
					<?php echo form_input(array(
						'name'=>'amount',
						'id'=>'amount',
						'value'=>$amount[0],
						'class'=>'text_box',
						'size'=>'15'
					));?>
					</div>
				</div>

				<div style="width: 180px; float: left">	
					<?php echo form_label('Payment Term:', 'payment_term',array('style'=>'width:130px','class'=>'lable-form')); ?>
					<div>
						<select id="amount_dyas" name="amount_dyas">
							<option value="0" <?php if ($amount[1]=='') echo 'selected'; ?>>...</option>
							<option value="30_days" <?php if ($amount[1]=='30_days') echo 'selected'; ?>><?=$this->lang->line('30_days')?></option>
							<option value="60_days" <?php if ($amount[1]=='60_days') echo 'selected'; ?>><?=$this->lang->line('60_days')?></option>
							<option value="90_days" <?php if ($amount[1]=='90_days') echo 'selected'; ?>><?=$this->lang->line('90_days')?></option>
						</select>
					</div>
				</div>
				<did id="error_amount" style="display: none"></did>
			</div>
		</div>
	<?php } ?>
</div>
<input type="hidden" id="email_active" name="email_active" value="0">
<input type="hidden" id="email_existente" name="email_existente" value="<?=$person_info->email?>">

<?php
echo form_submit(array(
	'name'=>'submit',
	'id'=>'submit',
	'value'=>$this->lang->line('common_submit'),
	'class'=>'small_button float_right'
)); 
echo form_close(); ?>


<div class="field_row clearfix requested">
	<?=$this->lang->line('common_fields_required_message')?>
</div>
<ul id="error_message_box"></ul>


<script type='text/javascript'>

//validation and submit handling
$(document).ready(function()
{
	function val_amount_credit(amount,days){
		if ((amount!='')&&(days!=0)){
				 if (isNaN(amount)){
			        return '1';
			    }else{
			    	return '0';
			    }	
		}else{
			if ((amount=='')&&(days==0)){
				return 'empty';
			}else{
				return 'one_empty'; //	
			}			
		}
	}


	$('#customer_form').validate({
		submitHandler:function(form)
		{			
			if ($('#email_active').val()!='1'){
				var ban = val_amount_credit($('#amount').val(),$('#amount_dyas').val());
				if ((ban==0)||(ban=='empty')) {
					$(form).ajaxSubmit({
						success:function(response)
						{
							if (response.email &&  $('#email_existente').val()!=response.email) {
		            			$('input#email').css({'color':'#FFF','background-color':'red'});
			            		$('#error_email').css('display', 'block');
			            		$('#email_active').val('1');
		            		}else{
								// console.log(response.retorno);
								tb_remove();
								post_person_form_submit(response);
							}
						},
						dataType:'json'
					});
				}else{
					if (ban==1) {
						$('#amount').css('border', '1px solid red');
						$('#amount_dyas').css('border', '1px solid red');
						$('#error_amount').css({background: 'red',display: 'inline-block',color: '#fff',padding: '3px',position: 'relative',top: '30px',left: '-80px'});
						$('#error_amount').html('<?php echo $this->lang->line('customers_only_number_amount');?>');
					}else{
						$('#amount').css('border', '1px solid red');
						$('#amount_dyas').css('border', '1px solid red');
						$('#error_amount').css({background: 'red',display: 'inline-block',color: '#fff',padding: '3px',position: 'relative',top: '30px',left: '-80px'});	
						$('#error_amount').html('<?php echo $this->lang->line('customers_select_both_fields');?>');
					}

				};
			};

		},
		errorLabelContainer: "#error_message_box",
 		wrapper: "li",
		rules: 
		{
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
		    address_1:
			{
				required:true
			}
   		},
		messages: 
		{
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
     		phone_number:"<?php echo $this->lang->line('common_phone_invalid_format');  ?>",

     		address_1: "<?php echo $this->lang->line('customers_msg_address'); ?>",
     		country: {
			      required: "<?php echo $this->lang->line('customers_msg_country'); ?>",
			      regex:"<?php echo $this->lang->line('customers_msg_country01'); ?>"
    		},     		
    		state: {
			      required: "<?php echo $this->lang->line('customers_msg_state'); ?>",
			      regex:"<?php echo $this->lang->line('customers_msg_state01'); ?>"
    		},     		
    		city: {
			      required: "<?php echo $this->lang->line('customers_msg_city'); ?>",
			      regex:"<?php echo $this->lang->line('customers_msg_city01'); ?>"
    		},
    		zip: "<?php echo $this->lang->line('customers_msg_zip'); ?>"
		}
	});
});

(function($){
	$(function(){
	    $('input#email').focusout(function() {
	        $.ajax({
	            type: "POST",
	            url:'index.php/customers/exists_email',
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

	 $('#give_credit').click(function(event) {	
	 	// alert('Activando: '+$('#give_credit').prop("checked"));
	 	$('#credit_panel').toggle();
	 });

	 //alert($('#amountactive').val());

	 if ($('#amountactive').val()) {
	 	$('#credit_panel').show();
	 	$('#give_credit').prop("checked",true);
	 	 // alert($('#amountactive').val());
	 }else{
	 	//alert('empty');
	 }

	});
})(jQueryNew);

</script>