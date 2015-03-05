<?php echo form_open('services/save/'.$service_info->service_id,array('id'=>'services_form'));
$disabled=$service_info->service_id!=-1?'disabled':'';$acum=0;
$currency_symbol = $this->config->item('currency_symbol') ? $this->config->item('currency_symbol') : '$';
$default_service = $this->config->item('default_service') ? $this->config->item('default_service') : 0;
// $default_service = 0;
?>
<!-- <div id="item_basic_info"> -->
<div class="clearfix"><h3><?php echo $this->lang->line("services_information"); ?></h3><hr></div>
<div style="width: 59%;float: left;">
	<div class="field_row clearfix invisible">
		<div style="width: 162px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('common_first_name').':', 'first_name',array('class'=>'lable-form-required')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'first_name',
					'id'=>'first_name',
					'value'=>'disabled',
					'class'=>'text_box',
					'disabled'=>'disabled'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 162px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('common_last_name').':', 'last_name',array('class'=>'lable-form-required')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'last_name',
					'id'=>'last_name',
					'value'=>'disabled',
					'class'=>'text_box',
					'disabled'=>'disabled'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 162px; float: left">
				<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('common_email').':', 'email',array('class'=>'lable-form-required')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'email',
					'id'=>'email',
					'value'=>'disabled',
					'class'=>'text_box',
					'disabled'=>'disabled'
				));?>
				</div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix">
		<div class="invisible" style="width: 162px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('common_phone_number').':', 'phone_number',array('class'=>'lable-form-required')); ?>
				<div>
				<?=form_input(array(
					'name'=>'phone_number',
					'id'=>'phone_number',
					'value'=>'disabled',
					'class'=>'text_box',
					'disabled'=>'disabled',
				))?>
				</div>
			</div>
		</div>
		<div class="noinvisible" style="width:<?=$disabled===''?'420':'210'?>px;float:left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('services_name_owner').':', 'name',array('class'=>'lable-form-required','style'=>'float:none;display:block;')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'name',
					'id'=>'name',
					'value'=>$service_info->first_name?$service_info->first_name.' '.$service_info->last_name:'',
					'class'=>'text_box',$disabled=>$disabled
				));
				if ($disabled==='') echo form_button(array(
					'name'=>'newc',
					'id'=>'newc',
					'value'=>'newc',
					'content' => '+',
					'class'=>'small_button thickbox',
					'style'=>'display: inline-block;margin-left: 20px;',
				));
				?>
				</div>
			</div>
		</div>
		<div style="width: 210px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('services_IMEI').':', 'code',array('class'=>'lable-form')); ?>
				<div>
				<?php echo form_input(array(
					'name'=>'codeimei',
					'id'=>'codeimei',
					'value'=>$service_info->serial,
					'class'=>'text_box',$disabled=>$disabled
				));?>
				</div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix">
		<div style="width: 162px; float: left">
			<div class="field_row clearfix">
				<?=form_label($this->lang->line('services_brand').':','brand_label',array('class'=>'lable-form-required'))?>
				<div>
				<?php echo form_input(array(
					'name'=>'brand',
					'id'=>'brand',
					'value'=>$service_info->brand_name,
					'class'=>'text_box',$disabled=>$disabled,
					'style'=>'width: 145px;'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 162px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('services_model').':', 'model_label',array('class'=>'lable-form-required')); ?>
				<div >
				<?php echo form_input(array(
					'name'=>'model',
					'id'=>'model',
					'value'=>$service_info->model_name,
					'class'=>'text_box',$disabled=>$disabled,
					'style'=>'width: 145px;'
				));?>
				</div>
			</div>
		</div>
		<div style="width: 162px; float: left">
			<div class="field_row clearfix">
				<?php echo form_label($this->lang->line('services_status').':', 'brand_label',array('class'=>'lable-form-required')); $status=array();
					for($i=1;$i<5;$i++){ $status[$i]=$this->lang->line('services_status_'.$i); }
					//$status[100]=$this->lang->line('services_status_100');
				?>
				<div><?=form_dropdown('status',$status,$service_info->status,'id="status" style="width:112px;"')?></div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix">       
		<div style="float:left;">
			<div class="field_row clearfix">
				<?=form_label($this->lang->line('services_problem').':','comments',array('class'=>'lable-form-required'))?>
				<div>
				<?=form_textarea(array(
					'name'=>'comments',
					'id'=>'comments',
					'value'=> isset($service_info->problem)?$service_info->problem:'',
					'rows'=>'4',
					'cols'=>'48'      
				))?>
				</div>
			</div>
		</div>
	</div>
	<div class="field_row clearfix requested"><?=$this->lang->line('common_fields_required_message')?></div>
	<ul id="error_message_box"></ul>
</div>
<div style="width: 39%;float: left;">
	<div class="field_row clearfix" style="width:100%;">
		<div style="float: left" style="width:100%;">
			<div id="allitems" class="field_row clearfix" style="width:100%;">
				<?=form_label($this->lang->line('services_used_items').':','items',array('class'=>'lable-form','style'=>'float:none;display:block'))?>
				<div>
					<input type="text" id="item_list"  name="item_list" value="<?=$item_list?>" style="width:332px;">
				</div>
				<input type="hidden" name="pric_" value=""><input type="hidden" name="items_" value="">
				<div class="divInput" style="float:left;padding:10px;display:none;">
					<input type="text" id="new-qty" value="1">
					<!-- <input type="text" id="new-qty" min="<?=$defaultPrice?>" value="1"> -->
					<p><?=$this->lang->line('credit_select_items')?></p>
				</div>
			</div>
		</div>
	</div>
	<div style="width:100%;;">
		<div class="field_row clearfix">
			<div style="float: left;width: 70px;">
			<?=form_label($this->lang->line('credit_pay').':','credit_pay_label',array('class'=>'lable-form'))?>	
			</div>
			<div style="float: left">
			<?php echo form_dropdown('payment_type',$payment_options,array(),'id="payment_types" style="margin-right:10px"');
			echo form_input(array(
				'name'=>'add_pay',
				'id'=>'add_pay',
				'size'=>'10',
				'value'=>'',
				'class'=>'text_box'
			));
			echo form_button(
				array(
					'name'=>'btn_add_pay',
					'id'=>'btn_add_pay',
					'value'=>'pay',
					'content' => 'Add',
					'class'=>'small_button float_right'
				)
			);?>
			</div>
		</div>
		<?php  
			$disabled='style="display:none;"';
			if ($service_info->add_pay!='') $disabled='';
			if (count($item_list_json)>0) $disabled='';
			if (($default_service*1)>0) $disabled='';
		?>
		<table id="registerpay" <?=$disabled?>>
			<thead>
			<tr>
				<th style="width:60px;"><?=$this->lang->line('common_delete')?></th>
				<th style="width:125px;"><?='Type'?></th>
				<th style="width:90px;"><?='Amount'?></th>
			</tr>
			</thead>
			<tbody id="payment_contents">
			<?php
			if($service_info->add_pay!=''){
				$payclass = explode(',', $service_info->add_pay); 
				foreach ($payclass as $valor){
					$class = explode(' ', $valor);$tipe=false;$mont=false; 
					if (isset($class[1])){
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
						$acum=$acum+(!$mont?$class[1]:0);
					?>
					<input type="hidden" id="pay_<?=$class[0]?>" name="pay_<?=$class[0]?>" value="<?=$class[1]?>">
					<tr class="<?=$class[0]?>">
						<td><input type="button" class="small_button" value="Delete"></td>
						<td><?=$tipe?></td>
						<td style="text-align: right;"><?=$mont?$mont:to_currency($class[1])?></td>
					</tr>				
					<?php
					}
				}
			}
			if (($default_service*1)>0){ ?>
				<tr>
					<td></td>
					<td><?=$this->lang->line('config_service_price')?></td>
					<td style="text-align: right;style="><?=$currency_symbol?>- <?=$default_service?></td>
				</tr>			
			<?php }
			if (count($item_list_json)>0){
				foreach ($item_list_json as $key) {
					$default_service=$default_service+$key['price'];
			?>
				<tr class="itemsid_<?=$key['id']?>">
					<td><input type="button" class="small_button" value="Delete"></td>
					<td><?=$key['text']?></td>
					<td style="text-align: right;"><?=$currency_symbol?>-<input type="text" value="<?=$key['price']?>" style="text-align: right;width: 70px;float: right;"></td>
				</tr>
			<?php
				}
			}
			?>
		</tbody>
		</table>
		<div id="totalprice" class="priceSub" ><?=$this->lang->line('sales_sub_total')?>:<div></div></div>
		<div id="totalpay" class="priceSub pay" ><?=$this->lang->line('sales_payments_total')?>:<div></div></div>
		<div id="totalrest" class="priceSub rest" ><?=$this->lang->line('credit_pay_Due')?>:<div></div></div>
	</div>	
</div>
<div class="clearfix"></div>
<?php
	echo form_button(
		array(
			'name'=>'enviar',
			'id'=>'enviar',
			'value'=>'enviar',
			'content' => $this->lang->line('common_submit'),
			'class'=>'small_button float_right',
			'style'=>'margin-left: 20px;'
		)
	);
	echo form_close();
	// $hidden = array('item' => '3','customer_id' => $service_info->person_id, 'service' => $service_info->service_id);
	echo form_open('sales/add/3',array('id'=>'payOneServices'), array());
	echo form_button(
		array(
			'name'=>'pay',
			'id'=>'pay',
			'value'=>'pay',
			'content' => $this->lang->line('services_pay'),
			'class'=>'small_button float_right',
			'style'=>$service_info->status==3?'':'display: none; margin-left: 20px;'
		)
	);
	echo form_close();
?>
<script>
	var cost=new Array(),acumulate=('<?=$default_service?>')*1,simbol='<?=$currency_symbol?>',tpay='<?=$acum?>',payband=false,rest=0,items_='',pric_='',permis=true,dataItems=false,lastprice=0,newprice=0;
	function post_service_form_submit(response){
		if(!response.success){ set_feedback(response.message,'error_message',true); }
		else{	
			setTimeout(function() { location.reload(); }, 1500);
			set_feedback(response.message,'success_message',false);
			tb_remove()
		}
	}
$(function(){
	$("#totalprice div").html(simbol+' '+acumulate.toFixed(2));
	$("#totalpay div").html(simbol+' '+(tpay*1).toFixed(2));
	$('#newc').click(function() {
		$('div.invisible input').val('').removeAttr('disabled').parents('div.invisible').show('fast').removeClass('invisible');
		$('div.noinvisible input').val('disabled').attr('disabled', 'disabled').parents('div.noinvisible').hide('fast');
	});
	$('#pay').click(function() {
		payband=true;
		$('#services_form').submit();
	});
	$('#enviar').click(function() {
		payband=false;
		$('#services_form').submit();
	});
	$("#brand").autocomplete("<?php echo site_url('services/suggest_brand');?>",{max:100,minChars:0,delay:10})
			.result(function(event,data,formatted){
				if(data){
					if ( $("#model").data('autocomplete')) {
						$("#model").autocomplete("destroy");
						$("#model").removeData('autocomplete');
					}
					$("#model").autocomplete("<?php echo site_url('services/suggest_models');?>/"+data[0],{max:100,minChars:0,delay:10})
							.result(function(event,data,formatted){}).search();
				}else{
					if ( $("#model").data('autocomplete')) {
						$("#model").autocomplete("destroy");
						$("#model").removeData('autocomplete');
						$("#brand").removeData('autocomplete');
					}
				}
			}).search();
	$("#name").autocomplete("<?php echo site_url('services/suggest_owner');?>",{max:100,minChars:0,delay:10})
				.result(function(event,data,formatted){}).search();
	$('#status').change(function(){ 
		if($(this).val()=='3') $('#pay').show();
		else $('#pay').hide();
	});
	$('#services_form').validate({
		submitHandler:function(form)
		{	
			$('#item_number').val($('#scan_item_number').val());
			if ($('#registerpay tbody tr').length>0){
				var prices=$('#registerpay tbody tr'),pri='';it='';
				$.each(prices, function(index, val) {
					var id=$(val).attr('class').split('_');
					if (id[1] && !isNaN(id[1]*1)){
						pri=pri+(pri!=''?',':'')+$(val).find('input[type="text"]').val();
						it=it+(it!=''?',':'')+id[1]+(id[2]?'_'+id[2]:'');
					}
				});
				$('input[type="hidden"][name="pric_"]').val(pri);$('input[type="hidden"][name="items_"]').val(it);
			}
			$(form).ajaxSubmit({
				success:function(response){
					if (response.noOw){ if (confirm(response.message)) $('#newc').click(); }
					else{
						if (payband){
							$('#payOneServices').attr('action',$('#payOneServices').attr('action')+'/'+response.service_id).submit();
						}else{
							//tb_remove(false);
							post_service_form_submit(response);
						}
					}
				},
				dataType:'json'
			});
			return false;
		},
		errorLabelContainer:"#error_message_box",
 		wrapper:"li",
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
			name:"required",
			// codeimei:"required",
			model:'required',
			status:'required',
			brand:'required',
			comments:'required',
			credit_pay:
			{
				number:true
			},
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
			name:"<?php echo $this->lang->line('services_name_owner_is_required'); ?>",
			// codeimei:"<?php echo $this->lang->line('services_IMEI_is_required'); ?>",
			model:"<?php echo $this->lang->line('services_model_is_required'); ?>",
			status:"<?php echo $this->lang->line('services_status_is_required'); ?>",
			brand:"<?php echo $this->lang->line('services_brand_is_required'); ?>",
			comments:"<?php echo $this->lang->line('services_comments_is_required'); ?>",
			credit_pay:"<?php echo $this->lang->line('services_credit_pay_is_only_numbers'); ?>"
		}
	});
});
//new jquery
(function($){
	$('#item_list').select2({
		placeholder:'Product Name, Code, Category',
		multiple:true,
		minimumInputLength:1,
		ajax:{
			url:'index.php/services/suggest3',
			data:function(term,page){ return { term: term }; },
			results:function(data,page){ return { results: data };}
		}
	}).bind('select2-selecting',function(e){
		if (e.choice.model_name && e.choice.model_name!=''){
			$('#brand').val(e.choice.brand_name);
			$('#model').val(e.choice.model_name);
		}
		if ($('#registerpay tbody tr').length<=0) $('#registerpay').show();
		if (e.choice.item_kit) e.choice.id=e.choice.id+'_kit';
		if ($('#registerpay tbody tr.itemsid_'+e.choice.id).length<=0){
			$('#registerpay tbody').append('<tr class="itemsid_'+e.choice.id+'"><td><input type="button" class="small_button" value="<?=$this->lang->line('common_delete')?>"></td>'+
									'<td>'+e.choice.text+'</td><td>'+simbol+' -<input type="text" value="'+e.choice.price+'" style="text-align: right;width: 70px;float: right;"></td></tr>');
			acumulate=acumulate*1+e.choice.price*1;
			$("#totalprice div").html(simbol+' '+acumulate.toFixed(2));
			rest=rest*1+e.choice.price*1;
			$("#totalrest div").html(simbol+''+(rest*1).toFixed(2));
		}
		$('#select2-drop').css('display','none');
		$("#item_list").select2("val", "");
		$("#item_list").select2("focus");
		e.preventDefault();
	});
	$('#btn_add_pay').click(function() {
		var paytipe=$("#payment_types").val().replace(' ',''),isgift='<?php echo $this->lang->line("sales_giftcard"); ?>';
		var mont=0;
		if ($("#add_pay").val()!='') {
			if (!isNaN($("#add_pay").val()) && $("#add_pay").val()>0){ 
				if (paytipe==isgift.replace(' ','')){
					mont = $("#add_pay").val();
					if ($("#pay_"+paytipe).length>0){
						var pre=$("#pay_"+paytipe).val();
						if (pre.indexOf(","+mont+",") > -1){}
						else $("#pay_"+paytipe).val(pre+mont+',');
					}else{
						$('#registerpay').append('<input type="hidden" id="pay_'+paytipe+'" name="pay_'+paytipe+'" value=",'+mont+',">');
					}
					paytipe=paytipe+'_'+mont;
					mont='<?php echo $this->lang->line("sales_simple_code"); ?>:'+mont;
				}else{
					if ($("#pay_"+paytipe).length>0){
						mont = ($("#pay_"+paytipe).val()*1) + $("#add_pay").val()*1;
						$('#pay_'+paytipe).val(mont);
					}else{
						mont = $("#add_pay").val();
						$('#registerpay').append('<input type="hidden" id="pay_'+paytipe+'" name="pay_'+paytipe+'" value="'+mont+'">');
					}
					tpay=tpay*1+mont*1;
					$("#totalpay div").html(simbol+''+tpay.toFixed(2));
					rest=rest*1-mont*1;
					$("#totalrest div").html(simbol+''+(rest*1).toFixed(2));
					mont=simbol+' '+(mont*1).toFixed(2);
				}
				if ($('#registerpay tbody tr').length<=0) $('#registerpay').show();
				$("#registerpay tbody tr."+paytipe).remove();
				$('#registerpay tbody').append('<tr class="'+paytipe+'"><td><input type="button" class="small_button" value="Delete"></td><td>'+$("#payment_types").val()+'</td><td style="text-align:right;">'+mont+'</td></tr>');
				$('#add_pay').css({'color':'#000','background':'none'});
				$('#error_message_box #msg_add_pay').remove();
			}else{
				$('#error_message_box #msg_add_pay').remove();	
				$('#add_pay').css({'color':'#FFF','background-color':'red'});
				$('#error_message_box').css('display','block').append('<li id="msg_add_pay"><label for="name" generated="true" class="error" style="display: inline;">Solo numero en el campo</label></li>');
			}
		}else{
			$('#error_message_box #msg_add_pay').remove();	
			$('#add_pay').css({'color':'#FFF','background-color':'red'});
			$('#error_message_box').css('display','block').append('<li id="msg_add_pay"><label for="name" generated="true" class="error" style="display: inline;">Vacio no</label></li>')
		}
	});
	$("#registerpay").on('click','.small_button',function() {
		var inputClass = $(this).parents('tr').attr('class'),
			isgift='<?php echo $this->lang->line("sales_giftcard"); ?>'.replace(' ','');
		if (inputClass.indexOf(isgift)>-1){
			var classAndcode=inputClass.split('_');
			var str=$('#pay_'+classAndcode[0]).val().replace(','+classAndcode[1],'');
			$('#pay_'+classAndcode[0]).val(str);
			if (str==='' || str===',') $('#pay_'+classAndcode[0]).remove();
		}else{
			var isItems=inputClass.split('_');
			if (isItems[1] && !isNaN(isItems[1]*1)){
				price=$(this).parents('tr').find('input[type="text"]').val();
				price=$.trim(price)!=''?$.trim(price):0;
				acumulate=acumulate-price
				$("#totalprice div").html(simbol+' '+acumulate.toFixed(2));
				rest=rest*1-price*1;
				$("#totalrest div").html(simbol+''+(rest*1).toFixed(2));
			}else{
				tpay=tpay-$('#pay_'+inputClass).val();
				$("#totalpay div").html(simbol+' '+tpay.toFixed(2));
				rest=rest*1+$('#pay_'+inputClass).val()*1;
				$("#totalrest div").html(simbol+''+(rest*1).toFixed(2));
				$('#pay_'+inputClass).remove();
			}
		}
		$("#registerpay tbody tr."+inputClass).remove();
		if ($('#registerpay tbody tr').length<=0) $('#registerpay').hide();		
	}).on('focus','input[type="text"]',function(){ 
		// lastprice=0,newprice=0
		var text=$.trim($(this).val());
		price=text!=''?text:0;
		lastprice=price;
	}).on('keydown','input[type="text"]',function(e){
		$(this).css('background','#fff');
		var key = e.which,pressed=false;
		if (key>=96 && key<=105 || key>=48 && key<=57 || key>=37 && key<=40 || key==8 || key==46) {
			pressed = true;
		}else{	e.preventDefault();	}
	}).on('keyup','input[type="text"]',function(e){
		var text=$.trim($(this).val());
		if (text!=='' && (text*1)>0 ){}
		else{ $(this).css('background','red'); }
	}).on('blur','input[type="text"]',function(){
		// lastprice=0,newprice=0
		var text=$.trim($(this).val());
		price=text!=''?text:0;
		acumulate=(acumulate*1-lastprice*1)+(price*1);
		$("#totalprice div").html(simbol+' '+acumulate.toFixed(2));
		rest=(rest*1-lastprice*1)+(price*1);
		$("#totalrest div").html(simbol+''+(rest*1).toFixed(2));
	});
	rest=acumulate-tpay;
	$("#totalrest div").html(simbol+''+(rest*1).toFixed(2));
})(jQueryNew);
</script>
