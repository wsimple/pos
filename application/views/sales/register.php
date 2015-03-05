<?php
if(isset($error)){	echo "<div class='error_message'>$error</div>"; }
if (isset($warning)){	echo "<div class='warning_mesage'>$warning</div>"; }
if (isset($success)){	echo "<div class='success_message'>$success</div>"; }
?>
<div id="register_wrapper">
<?php 
if ($mode!='shipping'){ $class="";
	echo form_open('sales/change_mode',array('id'=>'mode_form')); ?>
			<span><?=$this->lang->line('sales_mode')?></span>
		<?php echo form_dropdown('mode',$modes,$mode,'onchange="this.form.submit();"'); ?>
		<div>
			<div id="new_button"></div>
			<div id="show_suspended_sales_button">
				<?php
				// if($this->Employee->has_privilege('add','giftcards')){
					echo anchor('giftcards/view/sale/width:'.(isset($form_width)?$form_width:360).'/height:'.(isset($form_height)?$form_height:175),
						'<span style="font-size:75%;">Gift Card</span>',
						array('title'=>$this->lang->line('giftcards_new'),'class'=>'small_button thickbox','style'=>'float:left;'));
				// }
				?>
				&nbsp;
				<?php 
				// if(count($cart) > 0)	{
				echo anchor('sales/suspended/width:425','<span style="font-size:75%;">'.$this->lang->line('sales_suspended_sales').'</span>',array('class'=>'small_button thickbox','title'=>$this->lang->line('sales_suspended_sales')));
				// }
				?>
			</div>
		</div>
	</form>
<?php }else $class="nosale"; ?>	
<?php echo form_open('sales/add',array('id'=>'add_item_form','class'=>$class)); ?>
<label id="item_label" for="item">
<?php
if($mode=='sale'){	echo $this->lang->line('sales_find_or_scan_item'); }
else{ echo $this->lang->line('sales_find_or_scan_item_or_receipt'); }
?>
</label>
<?php echo form_input(array('name'=>'item','id'=>'item','size'=>'40','placeholder'=>$this->lang->line('sales_start_typing_item_name')));?>
	<div id="new_item_button_register" >
		<?php 
		if ($mode=='shipping') $action='shipping/';
		else $action='sales/';
		echo anchor('items/view/-100/'.$action.'width:660/height:465','<span>'.$this->lang->line('sales_new_item').'</span>',array('class'=>'small_button thickbox','title'=>$this->lang->line('sales_new_item')));
		?>
	</div>
	<!-- <div id="item_broken_register">
		<?php echo anchor('items/view/-1/width:660/height:465',
		'<div class="small_button"><span>Item Broked</span></div>',
		array('class'=>'small_button thickbox','title'=>'Item Broken'));
		?>
	</div> -->

</form>
<div id="registerDiv">
<table id="register">
<thead>
<tr>
<th style="width:11%;"><?php echo $this->lang->line('common_delete'); ?></th>
<th style="width:32%;"><?php echo $this->lang->line('sales_item_number'); ?></th>
<th style="width:33%;"><?php echo $this->lang->line('sales_item_name'); ?></th>
<th style="width:13%;"><?php echo $this->lang->line('sales_price'); ?></th>
<th style="width:11%;"><?php echo $this->lang->line('sales_quantity'); ?></th>
<th style="width:11%;"><?php echo $this->lang->line('sales_discount'); ?></th>
<th style="width:20%;">Sub Total</th>
<!-- <th style="width:11%;"><?php //echo $this->lang->line('sales_edit'); ?></th> -->
</tr>
</thead>
<tbody id="cart_contents">
<?php
if(count($cart)==0){
?>
<tr><td colspan='8'>
<div class='warning_message' style='padding:7px;'><?php echo $this->lang->line('sales_no_items_in_cart'); ?></div>
</tr></tr>
<?php
}else{
	foreach(array_reverse($cart,true) as $line=>$item)
	{
		$cur_item_info = $this->Item->get_info($item['item_id']);
		echo form_open( "sales/edit_item/$line/1", array('id'=>'edit_item'.$item['item_id']) );
	?>
		<tr id="<?=$item['item_id']?>" class="sale-line">
		<td><?=anchor("sales/delete_item/$line",$this->lang->line('common_delete'),"class='small_button'")?></td>
		<td><?=$item['item_number']?></td>
		<td style="align:center;">
			<?=$item['name']?>
			<?php if(!$item['is_service']){ ?><br/><small> [<?=$cur_item_info->quantity?> in stock]</small><?php } ?>
		</td>

		<?php if ($items_module_allowed){	?>
			<td><?=form_input(array('name'=>'price','value'=>($cur_item_info->is_service&&$item['price']==0?'':$item['price']),'size'=>'6','class'=>'edit-item text_box required','ref'=>$item['item_id']))?></td>
		<?php }else{ ?>
			<td><?=$item['price']?></td>
			<?=form_hidden('price',$item['price'])?>
		<?php } ?>
		<td>
		<?php
			if($item['is_serialized']==1):?>
				<?=$item['quantity']?>
				<?=form_hidden('quantity',$item['quantity'])?>
			<?php else: ?>
				<input type="text" name="quantity" class="select-edit-item text_box " size="4"  ref="<?=$item['item_id']?>" value="<?php echo $item['quantity']; ?>"/>
			<?php endif; ?>
			<?php //echo form_input(array('name'=>'quantity','value'=>$item['quantity'],'size'=>'2')); ?>
		</td>

		<td><?=form_input(array('name'=>'discount','value'=>$item['discount'],'size'=>'3', 'class'=>'edit-item text_box required','ref'=>$item['item_id']))?></td>
		<td class="sub-total"><?=to_currency($item['price']*$item['quantity']-$item['price']*$item['quantity']*$item['discount']/100)?></td>
		<!-- <td>
			<?php //echo form_submit('edit_item', $this->lang->line('sales_edit_item'));?>
			<?php //echo form_button( array('value'=>$item['item_id'],'name'=>'item_broken','class'=>'item-broken','content'=>'Report Item') ); ?>
		</td> -->
		</tr>
		<!-- <tr>
		<td style="color:#2F4F4F";><?=$this->lang->line('sales_description_abbrv').':>'?></td>
		<td colspan=2 style="text-align:left;">

		<?php
			if($item['allow_alt_description']==1){
				echo form_input(array('name'=>'description','value'=>$item['description'],'size'=>'20'));
			}else{
				if ($item['description']!=''){
					echo $item['description'];
					echo form_hidden('description',$item['description']);
				}else{
					echo 'None';
					echo form_hidden('description','');
				}
			}
		?>
		</td>
		<td>&nbsp;</td>
		<td style="color:#2F4F4F";>
		<?php if($item['is_serialized']==1){
				echo $this->lang->line('sales_serial').'::';
			} ?>
		</td>
		<td colspan=3 style="text-align:left;">
		<?php if($item['is_serialized']==1){
				echo form_input(array('name'=>'serialnumber','value'=>$item['serialnumber'],'size'=>'20'));
			}else{
				echo form_hidden('serialnumber', '');
			} ?>
		</td>
		</tr> -->
		<tr style="height:3px">
		<td colspan=8 style="background-color:white"> </td>
		</tr>
		</form>
	<?php
	}
} ?>
</tbody>
</table>
</div>
</div>

<div id="overall_sale">
	
	<div id='cancel_suspend_sale_button'>
	<?php
	// Only show this part if there is at least one payment entered.
	if ($mode!='shipping'){
		$txtsuspend='sales_suspend_sale';
		$txtcancel='sales_cancel_sale';
	}else{
		$txtsuspend='sales_suspend_shipping';
		$txtcancel='sales_cancel_shipping';
	}
	if(count($payments) > 0){ ?>
		<div class='small_button' id='suspend_sale_button'><span><?=$this->lang->line($txtsuspend)?></span></div>
	<?php } 
	if(count($cart) > 0){
	?>
		<div class='small_button' id='cancel_sale_button'><span><?=$this->lang->line($txtcancel)?></span></div>
	<?php } ?>
	</div>
	
	<div style="margin-top:5px;text-align:center;">
	<?php 
	// form_open('sales/select_employee',array('id'=>'select_employee_form'))
	// // <label id="Customer_label" for="employee"> $this->lang->line('sales_select_employee')<!-- </label> -->
	// form_input(array('name'=>'employee','id'=>'employee','class'=>'text_box','size'=>'30','value'=>$employee))
	// form_close()
	?>
	<?php
	if ($mode=='sale' || $mode=='return'){
		if ($customer > 0) {
			echo $this->lang->line('sales_customer').': <b>'.$customer_name. '</b><br/>';
			echo anchor('sales/remove_customer','['.$this->lang->line('common_remove').' '.$this->lang->line('Customers_Customer').']');
		}else{
			$customer_val = '';
			echo form_open('sales/select_Customer',array('id'=>'select_Customer_form'));
			echo '<label id="Customer_label" for="Customer">'.$this->lang->line('sales_select_Customer').'</label>';
			echo form_input(array('value'=>$customer_val,'name'=>'customer','id'=>'Customer','size'=>'30','class'=>'text_box','style'=>'width:73%;','placeholder'=>$this->lang->line('sales_start_typing_Customer_name')));
			
			//echo '<div style="margin-top:5px;text-align:center;">';
			//echo '<h3 style="margin: 5px 0 5px 0">'.$this->lang->line('common_or').'</h3>';
			echo anchor('customers/view/-1/width:600/height:420','<span>+</span>',array('class'=>'small_button thickbox','title'=>$this->lang->line('sales_new_customer'),'style'=>'padding:4px 10px;'));
			//echo '<div class="clearfix">&nbsp;</div>';
			echo form_close();
		}
	}else{
		include('application/config/database.php'); //Incluyo donde estaran todas las config de las databses
		$dbs = $this->Location->get_select_option_list(true);
		$dbs['default']='Principal';
		echo form_open('sales/select_location',array('id'=>'select_Customer_form'));
		echo form_label('Receiving Location:', 'location', array('id'=>'Customer_label'));
		echo form_dropdown('location', $dbs, $this->sale_lib->get_Customer(), 'id="location"');
		echo form_close();
	}
	?>

	<div id='sale_details'>
		<div class="sales_sub_total"><?=$this->lang->line('sales_sub_total')?>: <div><?=to_currency($subtotal)?></div></div>
		<!-- combro de descuento opcional -->
		<?php if ($customer > 0 && $amount_due > 0): ?>
			<div class="discount"><?php echo $this->lang->line('customers_customer').' '.$this->lang->line('customers_lbl_td_discount') ?>(<?php echo $general_discount ?>%):
				<?php if ($this->Employee->isAdmin()) { ?>
				<div><input id="customer_discount" type="checkbox" name="customer_discount" <?php echo $this->session->userdata('discounting') ?>></div>
				<?php } ?>
			</div>
		<?php endif ?>
		<!-- FIN combro de descuento opcional -->
		<!-- combro de inpuestos opcional -->
		<div class="taxing"><?php echo $this->lang->line('items_tax') ?>:
			<?php if ($this->Employee->isAdmin()) { ?>
			<div><input id="taxing" type="checkbox" name="taxing" <?=$taxing?>></div>
			<?php } ?>
		</div>
		<!-- FIN combro de inpuestos opcional -->
		<?php foreach($taxes as $name=>$value) { ?>
		<div class="taxing taxing-block clearfix" style="width: 97%;"><?=$name?>: <div><?=to_currency($value)?></div></div>
		<?php } ?> 
		<div class="total clearfix" style="width: 97%;"><?=$this->lang->line('sales_total')?>:<div><?=to_currency($total)?></div></div>
	</div>

	<?php
	// Only show this part if there are Items already in the sale.
	if(count($cart) > 0)
	{
	?>
		<div id="Cancel_sale">
		<?=form_open('sales/cancel_sale',array('id'=>'cancel_sale_form'))?>
		</form>
		</div>
		<div class="clearfix" style="margin-bottom:1px;">&nbsp;</div>
		<?php
		// Only show this part if there is at least one payment entered.
		if(count($payments) > 0 || $mode=='shipping'){
		?>
			<div id="finish_sale">
				<?=form_open('sales/complete',array('id'=>'finish_sale_form'))?>
				<!-- <label id="comment_label" for="comment"><?=$this->lang->line('common_comments')?>:</label> -->
				<?php //echo form_textarea(array('name'=>'comment', 'id' => 'comment', 'value'=>$comment,'rows'=>'4','cols'=>'23'));?>
				<!-- <br /><br /> -->

				<?php

				if(!empty($Customer_email))
				{
					echo $this->lang->line('sales_email_receipt'). ': '. form_checkbox(array(
						'name'		=> 'email_receipt',
						'id'		=> 'email_receipt',
						'value'		=> '1',
						'checked'	=> (boolean)$email_receipt,
						)).'<br/>('.$Customer_email.')<br/>';
				}

				if($payments_cover_total && ($mode=='sale' || $mode=='return')){
					echo "<div class='big_button' id='finish_sale_button'><span>".$this->lang->line('sales_complete_sale')."</span></div>";
				}

				if($payments_cover_total && $mode=='shipping'){
					echo "<div class='big_button' id='finish_sale_button'><span>".$this->lang->line('sales_complete_shipping')."</span></div>";
				}
				?>
			</div>
			</form>
		<?php
		}
		?>
	<div class="payments_">
		<div class="payments_total">Payments Total: <div><?=to_currency($payments_total)?></div></div>
		<div class="amount_due">Amount Due: <div><?=to_currency($amount_due)?></div></div>
	</div>
	<br>
	<a href="#" id="show_payments" class='small_button'><?php echo $this->lang->line('sales_show_payments') ?></a>
	<div id="Payment_Types">
		<div style="height:100px;">
			<?=form_open('sales/add_payment',array('id'=>'add_payment_form'))?>
			<table width="100%">
			<tr>
			<td>
				<?=$this->lang->line('sales_payment').': '?>
			</td>
			<td>
				<?=form_dropdown('payment_type',$payment_options,array(),'id="payment_types"')?>
			</td>
			</tr>
			<tr>
			<td colspan="2">
		<!--	<span id="amount_tendered_label"><?=$this->lang->line( 'sales_amount_tendered' ).': '?></span>
			</td>
			<td> -->
				<?=form_input(array('name'=>'amount_tendered','id'=>'amount_tendered','class'=>'text_box','value'=>to_currency_no_money($amount_due)));?>
			</td>
			</tr>
			</table>
			<div class='big_button' id='add_payment_button' style='float:left;margin:5px 0 0 35px;'>
				<span><?=$this->lang->line('sales_add_payment')?></span>
			</div>
		</div>
		</form>

		<?php
		// Only show this part if there is at least one payment entered.
		if(count($payments) > 0)
		{
		?>
			<table id="register">
			<thead>
			<tr>
				<th style="width:11%;"><?=$this->lang->line('common_delete')?></th>
				<th style="width:60%;"><?='Type'?></th>
				<th style="width:18%;"><?='Amount'?></th>
			</tr>
			</thead>
			<tbody id="payment_contents">
			<?php
				foreach($payments as $payment_id=>$payment)
				{
				echo form_open("sales/edit_payment/$payment_id",array('id'=>'edit_payment_form'.$payment_id));
				?>
				<tr>
					<td><?=anchor( "sales/delete_payment/$payment_id", $this->lang->line('common_delete'),'class="small_button"' )?></td>
					<td><?=$payment['payment_type']?></td>
					<td style="text-align:right;"><?php echo to_currency( $payment['payment_amount'] ); ?></td>
				</tr>
				</form>
				<?php
				}
				?>
			</tbody>
			</table>
			<br/>
		<?php
		}
		?>
	</div>
	<?php
	}
	?>
</div>
<div class="clearfix" style="margin-bottom:30px;">&nbsp;</div>
<script type="text/javascript" language="javascript">
(function($){

	$('#Payment_Types').hide();

	var modeShow = '<?php echo $mode ?>';

	if(modeShow=='sale') {
		$('#Payment_Types').show();
		$('#show_payments').hide();
	}

	$('#show_payments').click(function(event) {
		$('#Payment_Types').toggle();
		$(this).text($(this).text()!="<?php echo $this->lang->line('sales_show_payments') ?>"?"<?php echo $this->lang->line('sales_show_payments') ?>":"<?php echo $this->lang->line('sales_hide_payments') ?>");
		if (modeShow=='shipping') {
			$('#Payment_Types').show();
			$(this).hide();
		};
		return false;
	});
	$(document).on('click','.item-broken',function(){
		if(confirm('Report a damaged item! Are you sure?')){
			var itemId = this.value;
			$.ajax({
				url:'index.php/items/report_item_broken/'+itemId,
				type:'POST',
				success:function(data){
					window.location.reload();
				}
			});
		}
	});

})(jQueryNew);
$(function(){
	var customer = '<?php echo $customer ?>';
	//Para el cobro de taxes
	var b = '<?=$taxing?>', afterS = '<?=$this->Appconfig->get('alert_after_sale')?>';
	if (b !== 'checked') {$('.taxing-block').hide();}
	$('#taxing').click(function(event){
		var checked = $(this).is(':checked');
		$('.taxing-block')[checked?'show':'hide']();	
		$.get('index.php/sales/set_taxing',{taxing:(checked?1:0)},function(data){
			set_amounts();
		});
	});
	$('#customer_discount').click(function(event){
		var checked = $(this).is(':checked');
		$.get('index.php/sales/set_discount',{customer_discount:(checked?'checked':false)},function(){
			set_amounts();
		});
	});
	$('.select-edit-item').keydown(function(event) {
		if (!characteres(event,1) && !characteres(event,2) && !characteres(event,4) && !characteres(event,5)) event.preventDefault();
	});
	$('.edit-item').keydown(function(event) {
		if (!characteres(event,1) && !characteres(event,2) && !characteres(event,3) && !characteres(event,4) && !characteres(event,5)) event.preventDefault();
	});
	var band=false;
	$('.select-edit-item,.edit-item').focus(function(){
		band=true;
	}).blur(function(event) {
		if (band){
			event.preventDefault();
			var ref = $(this).attr('ref');
			$('#edit_item'+ref).ajaxSubmit({
				success:function(response){
					set_amounts(ref);
				}
			});
			band=false;
		}
	});
	$('.sale-line td input[type="text"]').css('text-align','right');

	$('#employee').autocomplete('index.php/employees/suggest/1',{
		max:100,
		delay:10,
		selectFirst: false,
		formatItem: function(row) {
			console.log(row);
			return row[1];
		}
	}).result(function(event,data,formatted){
		$('#select_employee_form').submit();
	});
	//$('#item').focus();

	$('#item').autocomplete("<?=site_url('sales/item_search')?>",{
		minChars:0,
		max:100,
		selectFirst: false,
		delay:10,
		formatItem:function(row){
			return row[1];
		}
	}).blur(function(){
		$(this).attr('value',"");
	}).result(function(event,data,formatted){
		// console.log(data);
		$('#add_item_form').submit();
	});

	//Envia formulario de Customer idependientemente del formato de Customer
	$('#location').change(function(event){
		if($(this).val()!='...'){
			// $('#select_Customer_form').submit();
			$('#select_Customer_form').ajaxSubmit();
		};
	});

	$('#Customer').autocomplete('<?=site_url('sales/Customer_search')?>',{
		minChars:0,
		delay:10,
		max:100,
		formatItem:function(row){
			return row[1];
		}
	}).result(function(event, data, formatted){
		$('#select_Customer_form').submit();
	}).blur(function(){
		$(this).attr('value',"");
	});

	$('#comment').change(function(){
		$.post('<?=site_url('sales/set_comment')?>',{comment:$('#comment').val()});
	});

	$('#email_receipt').change(function(){
		$.post('<?=site_url('sales/set_email_receipt')?>',{email_receipt:$('#email_receipt').is(':checked')?1:0});
	});

	$('#finish_sale_button').click(function(){
		var mode = '<?php echo $mode ?>';
		var dbselected = 1;

		if (mode=='shipping') {
			dbselected = document.getElementById('location').selectedIndex
		}

		if (dbselected > 0) {
			if (afterS!='0') {
				if(confirm("<?=$this->lang->line('sales_confirm_finish_sale')?>")){
					$('#finish_sale_form').submit();
				}	
			}else{ $('#finish_sale_form').submit(); }
		}else{
			// alert('You must select a database');
			notif({
				type: 'error',
				msg: "You must select a database first!",
				width: 'all',
				height: 100,
				position: 'center'
			});
		}
	});

	$('#suspend_sale_button').click(function(){
		if(confirm("<?=$this->lang->line('sales_confirm_suspend_sale')?>")){
			$('#finish_sale_form').attr('action','<?=site_url('sales/suspend')?>').submit();
		}
	});

	$('#cancel_sale_button').click(function(){
		if(confirm("<?=$this->lang->line('sales_confirm_cancel_sale')?>")){
			$('#cancel_sale_form').submit();
		}
	});

	$('#add_payment_button').click(function(){
		var payment_type = document.getElementById('payment_types');
		console.log(payment_type.selectedIndex)
		if (payment_type.selectedIndex=='5') {
			var url = 'index.php/home/confirm_user/customers-account-'+customer+'-credit/AccountBalance/650/450/width:350/height:180';
			tb_show('Type', url);
			return false;
		}
		var $items=$('#register input.required');
		if($items.length<1){
			alert("No items selected.");
			return false;
		}
		var submit=true,$this=$(this);
		$items.each(function(){
			if(this.value==''){
				submit=false;
				if(!$(this).hasClass('error')){
					$(this).addClass('error').one('focus',function(){
						$(this).removeClass('error');
					});
				}
			}
		});
		if(submit){
			$('#add_payment_form').submit();
		}else{
			alert("Some values are required, check your products before continue.");
		}
	});

	$('#add_payment_form').submit(function(){
		if($('#amount_tendered').val()==''||$('#amount_tendered').val()==0)$('#amount_tendered').val('O');
	});	

	$('#payment_types').change(checkPaymentTypeGiftcard).ready(checkPaymentTypeGiftcard)

	function set_amounts(line){
		line = line || false;
		$.ajax({
			url:'index.php/sales/get_ajax_sale_details',
			dataType:'json',
			success:function(data){		
				$('#amount_tendered').val(data.due);
				$('#amount-due,div.amount_due div').html(data.due);
				$('.general-total,div.total div').html(data.total).formatCurrency();
				$('#general-sub-total').html(data.subtotal).formatCurrency();
				if(line){
					var taxes = new Array();
					var price = $('tr#'+line+' input[name=price]').val();
		 			var quantity = $('tr#'+line+' input[name="quantity"]').val();
					var discount = $('tr#'+line+' input[name=discount]').val();
					$('tr#'+line+' td.sub-total').html(price*quantity-price*quantity*discount/100).formatCurrency();
					for (var key in data.taxes){ taxes.push(data.taxes[key]); }
					$('.taxes').each(function(index, el) {
						$(this).html(taxes[index]).formatCurrency();					
					});
				}
			}
		});
	}

	function post_item_form_submit(response){
		if(response.success){
			$('#item').attr('value',response.item_id);
			$('#add_item_form').submit();
		}
	}

	function post_person_form_submit(response){
		if(response.success){
			$('#Customer').attr('value',response.person_id);
			$('#select_Customer_form').submit();
		}
	}

	function checkPaymentTypeGiftcard(){
		if ($('#payment_types').val()=="<?=$this->lang->line('sales_giftcard')?>"){
			$('#amount_tendered_label').html("<?=$this->lang->line('sales_giftcard_number')?>");
			$('#amount_tendered').val('').focus();
		}else{
			$('#amount_tendered_label').html("<?=$this->lang->line('sales_amount_tendered')?>");
		}
	}
	function characteres(c,o){
		switch(o){
			case 1://numeros
				if ((c.keyCode>=48 && c.keyCode<=57)||(c.keyCode>=96 && c.keyCode<=105)) return true;
				break;
			case 2://direccion
				if (c.keyCode>=37 && c.keyCode<=40) return true;
				break;
			case 3://punto
				if (c.keyCode==110 || c.keyCode==190) return true;
				break;
			case 4://borrar
				if (c.keyCode==8 || c.keyCode==46) return true;
				break;
			case 5://tabular
				if (c.keyCode==9) return true;
				break;
		}
		return false;
	}
});
</script>
