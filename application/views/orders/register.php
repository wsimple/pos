<?php $this->load->view('partial/header'); ?>
<div id="page_title" style="margin-bottom:8px;"> 
	<?php echo $this->lang->line('orders'); ?> 
</div>
<div id="table_action_header" class="middle-gray-bar" style="background: none;padding-left: 15px;width: 98%;">
	<label id="item_label" for="item">
		<?php echo $this->lang->line('sales_find_or_scan_item'); ?>
	</label>
	<input type="text" id="item_list" name="item_list" value="" style="width:500px;">
</div>
<div id="filter-bar" id="titleTextImg" class="middle-gray-bar" style="display: none;">
	<div style="float:left;"><?php echo $this->lang->line('search_options') ?> :</div>
		<div id="search_filter_section" style="text-align: right; font-weight: bold;  font-size: 12px; ">
		<?php 	 
		echo form_open("orders",array('id'=>'orders_filter_form')); 
		$labels=array($this->lang->line('services_all'),
					$this->lang->line('services_today'),
					$this->lang->line('services_yesterday'),
					$this->lang->line('services_lastweek'),
					$this->lang->line('services_lastmonth'));
		$barra='';
		for ($i=0; $i < count($labels); $i++) { 
			$barra.=($barra==''?'':'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').form_radio(array('name'=>'filters','id'=>'filters'.$i,'value'=>$i,'checked'=>($filters==$i?1:0))).'&nbsp;'.form_label($labels[$i],'labelfilter'.$i);
					
		}
		echo $barra.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$options = array("0"=>$this->lang->line('services_status'),
						 "1"=>$this->lang->line('orders_status1'),
						 "2"=>$this->lang->line('orders_status2'));
		echo form_dropdown('filter_status', $options,$filter_status,"id='filter_status'");
		echo form_dropdown('filter_location', $location_lists,$filter_location,"id='filter_location'");
	    echo form_close(); 
		?>
	</div>
</div>
<ul class="tabs">
	<li class="tab">
		<input type="radio" id="tab-1" name="tab-group" checked>
		<label for="tab-1">New</label>
		<div class="content">
			<div id="table_holder">
				<?php echo form_open('orders/save/', 'id="form-order"');?>
				<table id="sortable_table" class="tablesorter" style="width: 100%;" >
					<thead>
						<tr>
							<th width="11%"><?php echo $this->lang->line('common_delete'); ?></th>
							<th width="10%"><?php echo $this->lang->line('sales_item_number'); ?></th>
							<th width="35%"><?php echo $this->lang->line('sales_item_name'); ?></th>
							<th width="10%" style="text-align: right"><?php echo $this->lang->line('items_current_quantity'); ?></th>
							<th width="18%" style="text-align: right"><?php echo $this->lang->line('sales_quantity'); ?></th>
						</tr>
					</thead>
					<tbody id="cart_contents">
					<?php if(count($cart)==0){ ?>
						<tr>
							<td colspan='5'><div class='warning_message' style='padding:7px;'><?php echo $this->lang->line('orders_no_items_in_cart'); ?></div></td>
						</tr>
					<?php }else{

							foreach(array_reverse($cart,true) as $line=>$item){
								$cur_item_info = $this->Item->get_info($item['item_id']);
					?>
								<tr id="<?=$item['item_id']?>" class="sale-line<?php echo ($cur_item_info->reorder_level < 1) ? ' no_stock' : '' ; ?>">
									<td>
										<?php echo anchor("orders/delete_item/".$item['item_id'],$this->lang->line('common_delete'),"class='small_button delete_item'")?>
									</td>
									<td>
										<?=$cur_item_info->item_id?>
									</td>
									<td style="align:center;">
										<?=$cur_item_info->name?>
										<input type="hidden" name="items[<?php echo $item['item_id']; ?>][id_item]" value="<?php echo $item['item_id']; ?>">
									</td>
									<td align="right">
										<?=$cur_item_info->quantity?>
										<input type="hidden" name="items[<?php echo $item['item_id']; ?>][current_quantity]" value="<?=$cur_item_info->quantity?>">
									</td>
									<td align="right">
										<input type="text" name="items[<?php echo $item['item_id']; ?>][quantity]" value="<?php echo floor($cur_item_info->reorder_level); ?>" class="item-quantity" style="width: 50px;text-align: right;">
									</td>
								</tr>
					<?php 	}
						} ?>
					</tbody>
				</table>
				<?php 
				echo anchor('orders/index/1', $this->lang->line('orders_fill_cart_with_low_stock_items'), 'class="big_button" style="display: inline-block; margin:10px; float: left;"');
				
				echo form_submit(
							array(
								'name'=>'sendto',
								'id'=>'sendto',
								'value'=>$this->lang->line('orders_send_order'),
								'class'=>'big_button',
								'style'=>'display: inline-block; margin:10px; float: right;'
							));
				echo anchor('orders/cancel_order', $this->lang->line('orders_cancel'), 'class="big_button" style="display: inline-block; margin:10px; float: right;"');
				echo form_close(); 
				?>
			</div>
		</div>
	</li>
	<li class="tab">
		<input type="radio" id="tab-2" name="tab-group">
		<label for="tab-2"><?php echo $this->lang->line('orders_historical') ?></label>
		<div class="content">
			<?php echo $links_orders ?>
				<div id="table_holder">
					<table class="tablesorter report" id="sortable_table">
						<thead>
							<tr style="color:#FFFFFF;background-color:#396B98;">
								<th width="20">+</th>
								<th><?php echo $this->lang->line('reports_order_id') ?></th>
								<th><?php echo $this->lang->line('reports_sent') ?></th>
								<th><?php echo $this->lang->line('reports_location') ?></th>
								<th><?php echo $this->lang->line('services_status') ?></th>
								<th><?php echo $this->lang->line('reports_comments') ?></th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<?php if (count($data_orders)): ?>
								<?php foreach ($data_orders as $order): ?>
								<tr>
									<td class="expand"><span class="small_button thickbox">+</span></td>
									<td><?php echo 'ORDE '.$order['id']; ?></td>
									<td><?php echo $order['date'] ?></td>
									<td><?php echo ($order['location']=='default'?'Principal':$order['location']); ?></td>
									<td><?php echo $status[$order['status']] ?></td>
									<td colspan="1"><?php echo (($order['comments']) ? $order['comments'] : $this->lang->line('reports_no_comment') ); ?></td>
									<?php if ($order['status']==1){ 
										if ($order['location']==$location){
									?>
										<td colspan="1" style="width: 100px;text-align: center;color: #6C6;font-weight: bold;font-size: 12px;">
											<?php 								
												if ($this->Transfers->get_my_reception_detail($order['sale_id'])->num_rows()){
													echo '<span style="color: #37B">'.$this->lang->line('orders_submitted').'</span>';
													echo anchor('receivings/index/'.$order['sale_id'], $this->lang->line('employees_profile_see'), 'class="small_button" style="padding: 5px 6px;"');
												}else echo $this->lang->line('orders_received');
											?>
										</td>
									<?php }else{ ?>
										<td colspan="1" style="color: #6C6;font-weight: bold;font-size: 14px;text-align: center;">
											<?php echo $this->lang->line('orders_status2');  ?>
										</td>
									<?php }
										}else{ 
										 if ($order['location']==$location){ ?>
										<td colspan="1" style="color: #FA4;font-weight: bold;font-size: 14px;text-align: center;">
											<?php echo $this->lang->line('orders_status1');  ?>
										</td>
									<?php }else{ ?>
										<td colspan="1" style="width: 140px;text-align: center;">
											<?php 
												if ($this->Employee->isAdmin()) {
													echo anchor('home/confirm_user/orders-cancel-'.$order['id'].'/'.$this->lang->line('orders_canceled').'/0/0/width:350/height:180/', $this->lang->line('orders_cancel'), array('class'=>"small_button thickbox", 'style'=>"padding: 7px 10px;", 'title'=>'Help'));
												}
											?>
										</td>
									<?php }
									} ?>
								</tr>
								<tr class="hide">
									<td colspan="7">
										<?php 
										$order_details = $this->Order->get_detail($order['id']); 
										$flag = ($order['status']) ? false : true ;
										?>
										<table class="innertable">
											<thead>
												<tr style="color:#FFFFFF;background-color:#0a6184;">
													<th><?php echo $this->lang->line('reports_item_name') ?></th>
													<th><?php echo $this->lang->line('reports_amount_needed') ?></th>
													<?php if ($order['location']==$location){ ?>
													<th><?php echo $this->lang->line('giftcards_current_quantity') ?></th>
													<?php }else{ ?>
													<th><?php echo $this->lang->line('giftcards_current_quantity').' ('.($order['location']=='default'?'Principal':$order['location']).')' ?></th>
													<th><?php echo $this->lang->line('giftcards_current_quantity').' ('.($location=='default'?'Principal':$location).')' ?></th>
													<?php } ?>
													<?php if ($flag) echo '<th></th>'; ?>
												</tr>
											</thead>
											<tbody>
											<?php
											foreach ($order_details->result() as $detail): 
												$stock=$this->Item->get_info($detail->id_item,'quantity,name');
												if ($order['location']!=$location) 
													$colorbg=$stock->quantity<$detail->quantity?'class="no_stock"':'';
												else $colorbg='';

											?>
											<tr <?php echo $colorbg; ?>>
												<td><?php echo $stock->name; ?></td>
												<td><?php echo $detail->quantity ?></td>
												<?php if ($order['location']==$location){ ?>
												<td><?php echo $detail->current_quantity ?></td>
												<?php }else{ ?>
												<td><?php echo $detail->current_quantity ?></td>
												<td><?php echo $stock->quantity ?></td>
												<?php } ?>
												<?php 
												if ($flag) {
													echo '<td class="middle" rowspan="'.$order_details->num_rows().'">';
													echo anchor('orders/check_availability/'.$order['id'], $this->lang->line('orders_make_shipping'), 'class="small_button make-shipping" style="padding: 7px 10px;"');
													echo "</td>";
												}
												$flag=false;
												?>
											</tr>
											<?php endforeach ?>
											</tbody>
										</table>
									</td>
								</tr>
								<?php endforeach; ?>
							<?php else: ?>
								<tr>
									<td colspan="5"><?php echo $this->lang->line('reports_no_have_orders'); ?></td>
								</tr>
							<?php endif ?>
						</tbody>
					</table>
				</div>
		</div>
	</li>
</ul>

<div class="clearfix" style="margin-bottom:30px;">&nbsp;</div>
<?php $this->load->view('partial/footer'); ?>

<script type="text/javascript" language="javascript">
(function($){
	$('#cart_contents').on('click','.delete_item',function(){
			var that = this;
			var url=this.href;
			if (confirm('<?php echo $this->lang->line("orders_confirm_items"); ?>')){
				$.ajax({
					url: url,
					type: 'GET',
					dataType: 'json',
					success: function(response){
						if (response.status) {
							$(that).parents('tr').fadeOut('slow', function() {
								$(this).remove();
							});
						}

						if ( $('#cart_contents > .sale-line').length == 0 ) {
							var tr = '<tr><td colspan="5"><div class="warning_message" style="padding:7px;"><?php echo $this->lang->line("orders_no_items_in_cart"); ?></div></td></tr>';
							$('#cart_contents').html(tr);							
						}
					}
				});
			}
			return false;
	});

	$('.make-shipping').click(function(event) {
		sessionStorage.removeItem("sales");
	});

	$('#item_list').select2({
		placeholder:'Product Name, Code, Category',
		minimumInputLength:1,
		ajax:{
			url:'index.php/items/suggest2',
			data:function(term,page){ return { term: term }; },
			results:function(data,page){ return { results: data };}
		}
	}).change(function(val, added, removed){
		console.log(val)
		if (val.added) {
			$('#item_list').select2('val','');
			$.ajax({
				url: 'index.php/orders/add',
				type: 'GET',
				dataType: 'json',
				data: {item: val.added.id},
				success: function(response){
					if (response.status) {
						var ele = $('#'+val.added.id);
						var prevQuantity = 1;
						if ($(ele).length > 0){
							prevQuantity = $(ele).find('.item-quantity').val();
							$(ele).remove();
						}
						var tr = '<tr id="'+val.added.id+'" class="sale-line">'+
							'<td><a href="index.php/orders/delete_item/'+val.added.id+'" class="small_button delete_item"><?php echo $this->lang->line("common_delete") ?></a></td>'+
							'<td>'+val.added.id+'</td>'+
							'<td style="align:center;">'+
								val.added.text+
								'<input type="hidden" name="items['+val.added.id+'][id_item]" value="'+val.added.id+'">'+
							'</td>'+
							'<td align="right">'+
								val.added.qty+
								'<input type="hidden" name="items['+val.added.id+'][current_quantity]" value="'+val.added.qty+'">'+
							'</td>'+
							'<td align="right"><input class="item-quantity"  type="text" name="items['+val.added.id+'][quantity]" value="'+val.added.reorder_level+'" style="width: 50px;text-align: right;"></td>'+
							'</tr>';
						if ( $('#cart_contents > .sale-line').length > 0 ) {
							$('#cart_contents').prepend(tr);
						}else{
							$('#cart_contents').html(tr);					
						}

						tb_show(null, "index.php/orders/modqty/height:150/width:200/modal:true", null,function(){
							$('input#new-qty').focus().val(prevQuantity);
						});
					}
				}
			});
			
		}
	});

	$('#cancel_sale_button').click(function(){
		if(confirm("<?=$this->lang->line('sales_confirm_cancel_sale')?>")){
			$('#cancel_sale_form').submit();
		}
	});

	$('#form-order').ajaxForm({
		dataType: 'json',
		beforeSubmit: function(arr, $form, options){
			var b = true;
			var inputs = $('.item-quantity');
			for (var i = inputs.length - 1; i >= 0; i--) {
				if ( $(inputs[i]).val() < 1 ) {
					notif({
					  type: 'error',
					  msg: "<?php echo $this->lang->line('orders_fields_zero') ?>",
					  width: "300px",
					  height: 100,
					  position: "right"
					});
					$(inputs[i]).focus();
					b = false;	
				}
			};
			
			return b;
		},
		success: function(response){
			var msgType = 'error';
			if (response.status) {
				msgType = 'success';
			}

			notif({
			  type: msgType,
			  msg: response.message,
			  width: "all",
			  height: 100,
			  position: "center",
			  onfinish: function(){
			  	if(response.status)self.location.href = 'index.php/orders';
			  }
			});
		}
	});
	var configs = JSON.parse(sessionStorage.getItem('orders')) || {'active_tab': 'tab-1'};
	$('#'+configs.active_tab).attr('checked', 'checked');
	$('.tab > input[type=radio]').change(function(e) {
		control_tab(e.target.id);
	});
	var idTab = $('.tab > input[type=radio]:checked').attr('id');
	control_tab( idTab );

	$("#orders_filter_form input[type='radio']").click(function(){
		$('#orders_filter_form').submit();
	});
	$("#orders_filter_form select").change(function(){
		$('#orders_filter_form').submit();
	});

	$(".tablesorter td.expand span").click(function(event){
		$(this).text($(this).text()!='+'?'+':'-').parents('tr').next().toggle();
	});
	var error='<?php echo (isset($error)?$error:"") ?>';
	if (error!='') 
		setTimeout(function(){
				tb_show("Error", 'index.php/reports/see_dialog_error/'+error+'/width:350/height:350');
		}, 1000);
})(jQueryNew);

function control_tab(idTab){
	var obj = {'active_tab': idTab};
	sessionStorage.setItem( 'orders', JSON.stringify(obj) );
	switch( idTab ){
		case 'tab-1':
			$('#filter-bar').hide();
			$('#table_action_header').fadeIn(400);
		break;
		case 'tab-2':
			$('#table_action_header').hide();
			$('#filter-bar').fadeIn('400');
		break;
	}
	console.log(sessionStorage);
}
</script>
