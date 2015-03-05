<?php
$this->load->view("partial/header"); 
//$this->load->view('flange_option', array('control'=>'reports')); 
?>
<section class="stock-manage">
	<div style="text-align:center;">
		<div id="page_title" style="margin-bottom:6px;text-align:center;"><?=$title?></div>
		<div class="page_subtitle" style="margin-bottom:6px;">
		<?php
		// if (isset($error)) echo "<div class='error_message'>$error</div>";
		//echo $sub_title; 
		?>
		</div>
	</div>
	<div id="titleTextImg" class="middle-gray-bar">
		<div style="float:left;"><?php echo $this->lang->line('search_options') ?> :</div>
			<div id="search_filter_section" style="text-align: right; font-weight: bold;  font-size: 12px; ">
			<?php 	 
			echo form_open("reports/control_stock".$completelocac,array('id'=>'orders_filter_form')); 
			$labels=array($this->lang->line('services_all'),
						$this->lang->line('services_today'),
						$this->lang->line('services_yesterday'),
						$this->lang->line('services_lastweek'),
						$this->lang->line('services_lastmonth'));
			$barra='';
			for ($i=0; $i < count($labels); $i++) { 
				$barra.=($barra==''?'':'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').form_radio(array('name'=>'filters','id'=>'filters'.$i,'value'=>$i,'checked'=>($cfilter==$i?1:0))).'&nbsp;'.form_label($labels[$i],'labelfilter'.$i);
						
			}
			echo $barra.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$options = array("0"=>$this->lang->line('services_status'),
							 "1"=>$this->lang->line('orders_status1'),
							 "2"=>$this->lang->line('orders_status2'));
			echo form_dropdown('filter_status', $options,$sfilter,"id='filter_status'");
		    echo form_close(); 
			?>
		</div>
	</div>
	<ul class="tabs">
		<li class="tab">
			<input type="radio" id="tab-1" name="tab-group" checked>
			<label for="tab-1"><?php echo $this->lang->line('module_pri_Orders'); ?></label>
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
											<?php echo anchor('home/confirm_user/orders-cancel-'.$order['id'].'/'.$this->lang->line('orders_canceled').'/0/0/width:350/height:180/', $this->lang->line('orders_cancel'), array('class'=>"small_button thickbox", 'style'=>"padding: 7px 10px;", 'title'=>'Help')) ?>
										</td>
									<?php }
									} ?>
								</tr>
								<tr class="hide">
									<td colspan="7">
										<?php $order_details = $this->Order->get_detail($order['id']); ?>
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
													<th></th>
												</tr>
											</thead>
											<tbody>
											<?php 
											$flag = true;
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
												<?php if ($flag): ?>
													<td class="middle" rowspan="<?php echo $order_details->num_rows() ?>">
													<?php echo anchor('orders/check_availability/'.$order['id'], $this->lang->line('orders_make_shipping'), 'class="small_button" style="padding: 7px 10px;"'); ?>
													</td>
												<?php
												$flag=false; 
												endif ?>
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
		<li class="tab">
			<input type="radio" id="tab-2" name="tab-group">
			<label for="tab-2"><?php echo $this->lang->line('sales_shippings'); ?></label>
			<div class="content">
				<?php echo $links_shippings ?>
				<div id="table_holder">
					<table class="tablesorter report" id="sortable_table">
						<thead>
							<tr style="color:#FFFFFF;background-color:#396B98;">
								<th colspan="1">+</th>
								<th colspan="1"><?php echo $this->lang->line('reports_sale_id') ?></th>
								<th colspan="1"><?php echo $this->lang->line('reports_sent') ?></th>
								<th colspan="1"><?php echo $this->lang->line('orders_from') ?></th>
								<th colspan="1"><?php echo $this->lang->line('orders_to') ?></th>
								<th colspan="1"><?php echo $this->lang->line('services_status') ?></th>
								<th colspan="1" ><?php echo $this->lang->line('reports_comments') ?></th>
								<th colspan="1"></th>
							</tr>
						</thead>
						<tbody>
							<?php if (count($data_shippings)): ?>
								<?php foreach ($data_shippings as $transaction): ?>
								<tr>
									<td class="expand" style="width: 15px;"><span class="small_button thickbox">+</span></td>
									<td>TRAN <?php echo $transaction['transfer_id'] ?></td>
									<td><?php echo $transaction['date'] ?></td>
									<td><?php echo $transaction['sender'] ?></td>
									<td><?php echo $transaction['receiver'] ?></td>
									<td><?php echo $status[$transaction['status']] ?></td>
									<td><?php echo (($transaction['comment']) ? $transaction['comment'] : $this->lang->line('reports_no_comment') ); ?></td>
									<td>
									<?php
									if ($transaction['status'] != 1 && $transaction['receiver'] == $location) {
										echo anchor('receivings/index/'.$transaction['transfer_id'], $this->lang->line('employees_profile_see'), 'class="small_button" style="padding:5px 7px;"');
									}
									?>
									</td>
								</tr>
								<tr class="hide">
									<td colspan="8">
										<?php
										$transaction_details = $this->Transfers->get_reception_detail($transaction['transfer_id']);
										?>
										<table class="innertable">
											<thead>
												<tr style="color:#FFFFFF;background-color:#0a6184;">
													<th><?php echo $this->lang->line('reports_item_name') ?></th>
													<th><?php echo $this->lang->line('reports_serial_number') ?></th>
													<th><?php echo $this->lang->line('reports_quantity_purchased') ?></th>
													<th><?php echo $this->lang->line('items_cost_price') ?></th>
													<th><?php echo $this->lang->line('items_unit_price') ?></th>
													<th><?php echo $this->lang->line('reports_discount_percent') ?></th>
												</tr>
											</thead>
											<tbody>
											<?php foreach ($transaction_details->result() as $detail): ?>
											<tr style="background-color:#ccc;">
												<td><?php echo $this->Item->get_info($detail->item_id)->name ?></td>
												<td><?php echo $detail->serialnumber ?></td>
												<td><?php echo $detail->quantity_purchased ?></td>
												<td><?php echo $detail->item_cost_price ?></td>
												<td><?php echo $detail->item_unit_price ?></td>
												<td><?php echo $detail->discount_percent ?></td>
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
</section>
<script type="text/javascript" language="javascript">
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
</script>
<?php $this->load->view("partial/footer"); ?>