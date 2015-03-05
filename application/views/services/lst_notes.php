<div>
	<div class="field_row clearfix" style="width: 95%;">
		<div style="margin: 0 0 10px; 0">
			<h3 style="color:#396B98"><?php echo $this->lang->line("services_lbl_summary"); ?></h3>
		</div>
		<div style="padding: 0 20px">
			<ul style=" list-style: none">
				<li style="margin: 0 0 5px 0; border-bottom: 1px solid #f4f4f4;"><strong><?php echo $this->lang->line('services_lbl_work_order') ?>:</strong>&nbsp;<?php echo $service_info->service_id; ?></li>
				<li style="margin: 0 0 5px 0; border-bottom: 1px solid #f4f4f4;"><strong><?php echo $this->lang->line('services_name_owner') ?>:</strong>&nbsp;<?php echo $service_info->first_name.' '.$service_info->last_name; ?></li>
				<li style="margin: 0 0 5px 0; border-bottom: 1px solid #f4f4f4;"><strong><?php echo $this->lang->line('services_lbl_device') ?>:</strong>&nbsp;<?php echo $service_info->model_name.' '.$service_info->color; ?></li>
				<li style="margin: 0 0 5px 0; border-bottom: 1px solid #f4f4f4;"><strong><?php echo $this->lang->line('services_IMEI') ?>:</strong>&nbsp;<?php echo $service_info->serial; ?></li>
				<li style="margin: 0 0 5px 0; border-bottom: 1px solid #f4f4f4;"><strong><?php echo $this->lang->line('services_received') ?>:</strong>&nbsp;<?php echo $service_info->date_received; ?></li>
				<li style="margin: 0 0 5px 0; border-bottom: 1px solid #f4f4f4;"><strong><?php echo $this->lang->line('services_delivered') ?>:</strong>&nbsp;<?php echo (trim($service_info->date_delivered)!=''?$service_info->date_delivered:$this->lang->line('services_lbl_instore')); ?></li>
				<li style="margin: 0 0 5px 0; border-bottom: 1px solid #f4f4f4;"><strong><?php echo $this->lang->line('services_status') ?>:</strong>&nbsp;<span class="status"><?php echo $this->lang->line('services_status_'.$service_info->status); ?></span></li>
				<li style="margin: 0 0 5px 0; border-bottom: 1px solid #f4f4f4;"><strong><?php echo $this->lang->line('services_lbl_phone_number') ?>:</strong>&nbsp;<?php echo $service_info->phone_number; ?></li>
				<li style="margin: 0 0 5px 0; border-bottom: 1px solid #f4f4f4;">
					<strong><?php echo $this->lang->line('services_lbl_address') ?>:</strong>&nbsp;
					<?php $address='';
					$address.=$service_info->city;
					if ($service_info->state!='') $address.=($address!=''?', ':' ').$service_info->state;
					if ($service_info->zip!='') $address.=($address!=''?', ':' ').$service_info->zip;
					if ($service_info->address_1!='') $address.=($address!=''?', ':' ').$service_info->address_1;
					echo $address; ?>
				</li>
				<li style="margin: 0 0 5px 0;"><strong><?php echo $this->lang->line('services_problem') ?>:</strong>&nbsp;<?php echo $service_info->problem; ?></li>
				<li style="margin: 0 0 5px 0;">
					<strong><?php echo $this->lang->line('sales_sub_total') ?>:</strong>&nbsp;
					<?php echo ($this->config->item('currency_symbol')?$this->config->item('currency_symbol'):'$').' '.($service_info->price+($this->config->item('default_service')?$this->config->item('default_service'):0));
					?>
				</li>
				<?php if($service_info->add_pay!=''){ ?>
				<li style="margin: 0 0 5px 0; border-bottom: 1px solid #f4f4f4;">
					<strong><?php echo $this->lang->line('credit_pay') ?>:</strong>&nbsp;<br/>
					<label style="margin-left: 10px;">
					<?php
							$payclass = explode(',', $service_info->add_pay); 
							foreach ($payclass as $valor){
								$class = explode(' ', $valor); $tipe=false; $mont=false; 
								switch ($class[0]) {
									case 'Cash': $tipe=$this->lang->line('sales_cash'); break;
									case 'Check': $tipe=$this->lang->line('sales_check'); break;
									case 'GiftCard': 
										$tipe=$this->lang->line('sales_giftcard'); 
										$mont=$this->lang->line("sales_simple_code").':'.$class[1];
									break;
									case 'DebitCard': $tipe=$this->lang->line('sales_debit'); break;
									case 'CreditCard': $tipe=$this->lang->line('sales_credit'); break;
								}
								echo $tipe.' '.($mont?$mont:$class[1]).'<br/>';
							} ?>
					</label>
				</li>
				<?php } ?>
			</ul>
		</div>
	</div>

	<div class="field_row clearfix">
		<div>
			<h3 style="color:#396B98"><?php echo $this->lang->line("services_lbl_new_note"); ?></h3>
		</div>
		<?php echo form_open('services/add_note',array('id'=>'notes_form')); ?>
		<div style="background-color: #F4F4F4; padding: 5px; border: 1px solid #CCC; margin: 5px 0 5px 0;  border-radius: 5px;">
			<div>
				<?php 
					echo form_textarea(array(
						'name'=>'txtNote',
						'id'=>'txtNote',
						'value'=> '',
						'class'=>'text_box',
						'style'=>'width: 540px;',
						'placeholder'=>'write a new note ...',
						'rows'=>'4',
						'cols'=>'70'
					));
					echo form_button(array(
						'name'=>'btnAdd',
						'id'=>'btnAdd',
						'value'=>'btnAdd',
						'content' => '&nbsp;&nbsp;&nbsp;&nbsp;Add&nbsp;&nbsp;&nbsp;&nbsp;',
						'class'=>'small_button thickbox',
						'style'=>'display: inline-block;margin-left: 20px;',
					));
					echo form_hidden('service_id', $service_info->service_id);
				?>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
	
	<div class="field_row clearfix">
		<div style="margin: 0 0 10px; 0">
			<h3 style="color:#396B98"><?php echo $this->lang->line("services_lbl_noteList"); ?></h3>
		</div>
		<div style="padding: 0 20px">
			<ul id="ul_notes" style="list-style: circle">
				<?php
					if (isset($notes) && count($notes)>0){
						foreach ($notes as $array){
							echo '<li style="margin: 0 0 5px 0; border-bottom: 1px solid #f4f4f4"><strong>'.ucwords($array['name']).':</strong>&nbsp;'.$array['note'].', <small>(<em>'.$array['date'].'</em>)</small></li>';	
						}	
					} 
				?>
			</ul>
		</div>
	</div>

</div>

<script>
(function($){

	$("#btnAdd").click(function() {
  		$('#notes_form').submit();
	});


	$('#notes_form').ajaxForm({
	    dataType: 'JSON',
	    success : function(data) { 
	        $('#ul_notes').prepend('<li style="margin: 0 0 5px 0; border-bottom: 1px solid #f4f4f4"><strong>'+data['employee']+':</strong>&nbsp;'+data['note']+', <small>(<em>'+data['date']+'</em>)</small></li>');
	        $("#txtNote").val('').focus();	    
	    }
	});

})(jQueryNew);
</script>