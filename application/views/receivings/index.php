<?php $this->load->view("partial/header"); ?>
<div id="page_title" style="margin-bottom:8px;"><?php echo $this->lang->line('recvs_register'); ?></div>
<?php 
if(isset($error)){ echo "<div class='error_message'>".$error."</div>"; }
//Convert assoc arr in normal vars
foreach ($data as $key => $value) ${$key} = $value;

if ($this->Transfers->available()): 
	echo form_open("receivings/index/",array('id'=>'receivings_form')); 
	echo form_input(array('name'=>'reception','id'=>'reception','size'=>'40','placeholder'=>$this->lang->line('recvs_scan_code')));?>
	<input type="submit" value="<?php echo $this->lang->line('recvs_load'); ?>" class="small_button">
<?php 
	echo form_close();
endif; 
?>
<div id="filter-bar" id="titleTextImg" class="middle-gray-bar" style="display: none;">
	<div style="float:left;"><?php echo $this->lang->line('search_options') ?> :</div>
		<div id="search_filter_section" style="text-align: right; font-weight: bold;  font-size: 12px; ">
		<?php 	 
		echo form_open("receivings",array('id'=>'orders_filter_form')); 
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
		$status_list = array("0"=>$this->lang->line('services_status'),
						 "1"=>$this->lang->line('orders_status1'),
						 "2"=>$this->lang->line('orders_status2'));
		$other_filters_list = array(
			$this->lang->line('reports_all'),
			$this->lang->line('recvs_sent'),
			$this->lang->line('recvs_receiving')
		);
		echo form_dropdown('filter_status', $status_list,$sfilter,"id='filter_status'");
		echo form_dropdown('filter_other', $other_filters_list,$filters,"id='filter_other'");
	    echo form_close(); 
		?>
	</div>
</div>
<ul class="tabs">
	<li class="tab">
		<input type="radio" id="tab-1" name="tab-group">
		<label for="tab-1"><?php echo $this->lang->line('recvs_receiving') ?></label>
		<div class="content">
		<?php
		if (!$data_receive['reception_shipping']) {
			$this->load->view('receivings/receiving', $data_receive); 
		}else{
			$this->load->view("receivings/receiving_",$data_receive);
		}

		?>
		</div>
	</li>
	<li class="tab">
		<input type="radio" id="tab-2" name="tab-group">
		<label for="tab-2"><?php echo $this->lang->line('orders_historical') ?></label>
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
									<div>
									<?php
									if ($transaction['status'] != 0 && $transaction['receiver'] == $location) {
										echo form_open("receivings/index/", array('id'=>'form-'.$transaction['transfer_id'])); 
										echo form_hidden('reception', $transaction['transfer_id']);
										echo form_submit(array('name'=>'submit','value'=>$this->lang->line('employees_profile_see'),'class'=>'small_button'));
										echo form_close();
									}
									?>
									</div>
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
<div class="clearfix" style="margin-bottom:30px;">&nbsp;</div>
<?php $this->load->view("partial/footer"); ?>
<script type="text/javascript" language="javascript">
$(document).ready(function(){
	//Tab 1
	$("#item").autocomplete('<?php echo site_url("receivings/item_search"); ?>',{
    	minChars:0,
    	max:100,
       	delay:10,
       	selectFirst: false,
    	formatItem: function(row) {
			return row[1];
		}
    });

    $('#reception').keypress(function(event) {
    	if (event.which == 13) {
    		 $('#receivings_submit').click();
    	};
    });

    $("#item").result(function(event, data, formatted){
		$("#add_item_form").submit();
    });

	$('#item').focus();
	$('#item').blur(function(){
    	$(this).attr('value',"<?php echo $this->lang->line('sales_start_typing_item_name'); ?>");
    });

	$('#item,#supplier').click(function(){
    	$(this).attr('value','');
    });

    $("#supplier").autocomplete('<?php echo site_url("receivings/supplier_search"); ?>',
    {
    	minChars:0,
    	delay:10,
    	max:100,
    	formatItem: function(row) {
			return row[1];
		}
    });

    $("#supplier").result(function(event, data, formatted){
		$("#select_supplier_form").submit();
    });

    $('#supplier').blur(function(){
    	$(this).attr('value',"<?php echo $this->lang->line('recvs_start_typing_supplier_name'); ?>");
    });
    $("#finish_sale_button").click(function(){
    	setTimeout(function(){
    		$('#finish_sale_form').submit();
    	},5500);
    	notif({
		  type: "info",
		  msg: '<?php echo $this->lang->line("recvs_confirm_finish_receiving"); ?>',
		  width: "all",
		  height: 100,
		  position: "center"
		});
    });
	//Tab 2
    $("#orders_filter_form input[type='radio']").click(function(){
		$('#orders_filter_form').submit();
	});
	$("#orders_filter_form select").change(function(){
		$('#orders_filter_form').submit();
	});

	$(".tablesorter td.expand span").click(function(event){
		$(this).text($(this).text()!='+'?'+':'-').parents('tr').next().toggle();
	});

    $("#cancel_sale_button").click(function(){
    	setTimeout(function(){
    		$('#cancel_sale_form').submit();
    	},5500);
    	notif({
		  type: "warning",
		  msg: '<?php echo $this->lang->line("recvs_confirm_cancel_receiving"); ?>',
		  width: "all",
		  height: 100,
		  position: "center"
		});
    });
    var configs = JSON.parse(sessionStorage.getItem('receivings')) || {'active_tab': 'tab-1'};
	$('#'+configs.active_tab).attr('checked', 'checked');
	var idTab = $('.tab > input[type=radio]:checked').attr('id');
	control_tab( idTab );
	$('.tab > input[type=radio]').change(function(e) {
		control_tab(e.target.id);
	});
});

function post_item_form_submit(response){
	if(response.success){
		$("#item").attr("value",response.item_id);
		$("hsl(180, 43%, 77%)_item_form").submit();
	}
}

function post_person_form_submit(response){
	if(response.success){
		$("#supplier").attr("value",response.person_id);
		$("#select_supplier_form").submit();
	}
}
function control_tab(idTab){
	var obj = {'active_tab': idTab};
	sessionStorage.setItem( 'receivings', JSON.stringify(obj) );
	switch( idTab ){
		case 'tab-1':
			$('#filter-bar').hide();
			$('#receivings_form').fadeIn(400);
		break;
		case 'tab-2':
			$('#receivings_form').hide();
			$('#filter-bar').fadeIn('400');
		break;
	}
}
</script>