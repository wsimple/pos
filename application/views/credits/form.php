<?php 
	$account = ($person_info->person_id!='') ? '<span style="float: right; font-weight: bold;"> '.$this->lang->line('customers_account_number').' '.$person_info->person_id.'</span>':'';
	$account = trim($account);
	$datos = explode('/', $person_info->max_amount_credit);
	if ($datos[0]) {?>
		<input type="hidden" id="limit_credit" name="limit_credit" value="<?=$datos[0]?>">
		<input type="hidden" id="period_credit" name="period_credit" value="<?=$datos[1]?>">
	<?php } ?>

<div>
	<h3 class="user-icon" <?php if ($account!='') echo 'style="padding-right: 40px "'; ?> >
		<?php 
			echo $this->lang->line("customers_basic_information");
			if ($account!='') echo $account; 
		?>
	</h3>

	<div class="field_row clearfix">
		<div style="width: 645px;height: 50px;">
			<div style="width: 150px; float: left;border-left: 1px solid #ccc;border-right: 1px solid #ccc; padding-left: 10px">
				<div class="field_row clearfix">
					<div style="font-weight: bold;color: red;margin-bottom: 5px;">	
					<?php echo $this->lang->line('common_first_name'); ?>
					</div>
					<div>
					<?php echo $person_info->first_name;?>
					</div>
				</div>
			</div>
		
			<div style="width: 150px; float: left;border-right:1px solid #ccc; padding-left: 10px">
				<div class="field_row clearfix">
					<div style="font-weight: bold;color: red;margin-bottom: 5px;">	
					<?php echo $this->lang->line('common_last_name'); ?>
					</div>
					<div>
					<?php echo $person_info->last_name;?>
					</div>
				</div>
			</div>
			<div style="width: 150px; float: left;border-right:1px solid #ccc; padding-left: 10px">
				<div class="field_row clearfix">
					<div style="font-weight: bold;color: red;margin-bottom: 5px;">	
					<?php echo $this->lang->line('common_email'); ?>
					</div>
					<div>
					<?php echo $person_info->email;?>
					</div>
				</div>
			</div>
			<div style="width: 150px; float: left;border-right:1px solid #ccc; padding-left: 10px">
				<div class="field_row clearfix">
					<div style="font-weight: bold;color: red;margin-bottom: 5px;">	
					<?php echo $this->lang->line('common_phone_number'); ?>
					</div>
					<div>
					<?php echo $person_info->phone_number;?>
					</div>
				</div>
			</div>
		</div>
	</div>
		
	<h3 class="user-icon"><?php echo $this->lang->line('customers_credit_info');?>. <span style="font-size:.8em;">(<?php echo $this->lang->line('customers_info_report').' '.$this->lang->line('reports_credit') ?>)</span></h3>
	<div id="table_holder">
	<?=$manage_table?>
	</div>

	<?php if ($count_credit!=0&&$type=='pay') { $nameC = $person_info->first_name.' '.$person_info->last_name; ?>
	<div style="text-align: right; font-size: 14px; font-weight: bold">	
		<a href="<?php echo site_url('reports/credits/'.$nameC.'/'.$person_info->person_id);?>" class="small_button"><?php echo $this->lang->line('reports_credit'); ?></a>
	</div>
	<?php } ?>
	<div class="clearfix my_height"></div>
	
	<span style="color:#000;font-weight: bold; font-size: 16px"><?php echo $this->lang->line('customers_limiti_available');?></span> <span style="color:green; font-weight: bold; font-size: 16px" id="dis"></span>
	<?php
		if ($type=='credit') {
			$btn = '';
			$monto = (isset($balance->balance))?$balance->balance:0;
		} else {
			$btn = (isset($balance->balance))? (($balance->balance<=0)? 'disabled' : ''):'';
			$monto = (isset($balance->balance))?$balance->balance:0;
		}

		echo '<input type="hidden" id="hidden_monto" value="'.$monto.'">';
	?>
	<div class="clearfix my_height"></div>

	<?php if ($count_credit!=0||$type=='credit') { 
		echo form_open('customers/save_credit/'.$person_info->person_id."/$type",array('id'=>'customer_form_credit'));
		
		?>
		<h3 class="user-icon"><?php echo $this->lang->line('customers_pay_credit');?></h3>
		<div class="field_row clearfix">
			<div style="width: 645px;margin: 18px 0px;">
				<?php 
				$label = $this->lang->line('customers_amount');
				if($type!='credit') $label .= ' '.$this->lang->line('customers_payable');
				echo form_label($label.':', 'amount_payable',array('class'=>'lable-form'));
				?>

				<input type="text" id="pay_amount_credit" name="pay_amount_credit" class="text_box" style="width: 100px;" <?=$btn?>>
				<input type="hidden" id="pay_amount_tendered" name="pay_amount_tendered">

				<input type="submit" value="<?=$this->lang->line('common_submit')?>" id="submit" class="small_button" name="submit" <?=$btn?>>
			</div>
		</div>
		
		<input type="hidden" id="person_id" name="person_id" value="<?=$person_info->person_id?>">

		<div class="clearfix my_height"></div>
		<div class="field_row clearfix requested">
			<?=$this->lang->line('common_fields_required_message')?>
		</div>
	<?php 
		echo form_close(); 
	} 
	?>
</div>
<ul id="error_message_box"></ul>

<script type='text/javascript'>

//validation and submit handling
(function($){ 
	$(function(){

		var amount_tendered = $('#amount_tendered'),  hidden_pay = $('#hidden_monto').val(),limit_credit = $('#limit_credit').val(), dis = 0, pay = 0;

		dis = limit_credit*1-hidden_pay*1;
		$('#dis').html('$'+dis.toFixed(2));	
		
		//si el limite nunca ha sido rebajado, se le asigna el limite al monto de comparcion de la deuda
		if (dis == limit_credit) {hidden_pay = limit_credit;};

		if ($(amount_tendered).length > 0) {
			$('#pay_amount_credit').val($(amount_tendered).val()).keyup(function(event) {
				$(amount_tendered).val(event.target.value);
			});

			$('#pay_amount_tendered').val($('#pay_amount_credit').val()).keyup(function(event) {
				$(amount_tendered).val(event.target.value);
			});

			if ("<?=$type=='credit'?>") {
				hidden_pay = $('#pay_amount_credit').val()+1;
			};
		};
		
		$('#pay_amount_tendered').val($('#pay_amount_credit').val()).keyup(function(event) {
			$('#pay_amount_credit').val(event.target.value);
		});
			
			$('#customer_form_credit').validate({
				submitHandler:function(form)
				{	
					if ("<?=$type=='pay'?>") { dis = hidden_pay; }
					pay = $('#pay_amount_credit').val();
					// pay = pay.replace("-", "");
					//si el pago  es menor que el limite de credito diponible, pasa
					if (pay*1<=dis){
						//si elpago es mayor al monto pediente de la deuda, no pasa
						if (pay*1>hidden_pay*1) {							
							alert("<?php echo $this->lang->line('customers_amount_owed');  ?>");
						} else{
							if ($(amount_tendered).length > 0){
								tb_remove(true);
								$('#add_payment_form').submit();
							}else{
								$(form).ajaxSubmit({
									success:function(response)
									{
										// alert(response.success);
										if (response.success) {											
											tb_remove(true);
										}
									}
								});
								tb_remove(true);
							}
						};
					}else{
						alert("<?php echo $this->lang->line('customers_amount_exceed'); ?>");
					}
					return false;
				},
				errorLabelContainer: "#error_message_box",
		 		wrapper: "li",
				rules: 
				{
		    		pay_amount_credit:
					{
						required:true,
						number:true
						
					}
		   		},
				messages: 
				{
		     		pay_amount_credit:"<?php echo $this->lang->line('customers_pay_input_empty');  ?>"
				}
			});


	});
})(jQueryNew);

</script>