<?php $this->load->view('partial/header'); ?>
<div id="page_title" style="margin-bottom:8px;">
	<?php 
		if ($register['mode']=='return')  echo $this->lang->line('sales_return');
		elseif ($register['mode']=='shipping') echo $this->lang->line('sales_shipping');
		else echo $this->lang->line('sales_register');
	?>
</div>
<div id="filter-bar" id="titleTextImg" class="middle-gray-bar" style="display: none;">
	<div style="float:left;"><?php echo $this->lang->line('search_options') ?> :</div>
		<div id="search_filter_section" style="text-align: right; font-weight: bold;  font-size: 12px; ">
		<?php	 
		echo form_open("sales/index/".$list['sale_type'],array('id'=>'orders_filter_form')); 
		$labels=array(
					'0'=>$this->lang->line('services_all'),
					'1'=>$this->lang->line('services_today'),
					'2'=>$this->lang->line('services_yesterday'),
					'3'=>$this->lang->line('services_lastweek'),
					'5'=>$this->lang->line('services_lastmonth')
		);
		$barra='';
		foreach ($labels as $i => $value) {
			$barra.=($barra==''?'':'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;').form_radio(array('name'=>'filters','id'=>'filters'.$i,'value'=>$i,'checked'=>($list['cfilter']==$i?1:0))).'&nbsp;'.form_label($value,'labelfilter'.$i);
		}
		echo $barra.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		$options = array("0"=>$this->lang->line('services_status'),
						 "1"=>$this->lang->line('orders_status1'),
						 "2"=>$this->lang->line('orders_status2'));
		// echo form_dropdown('filter_status', $options,$list['sfilter'],"id='filter_status'");
	    echo form_close(); 
		?>
	</div>
</div>
<section class="shippings">
	<ul class="tabs">
		<li class="tab">
			<input type="radio" id="tab-1" name="tab-group">
			<label for="tab-1"><?php echo $this->lang->line('sales_place') ?></label>
			<div class="content">
				<?php $this->load->view('sales/register', $register); ?>
			</div>
		</li>
		<li class="tab">
			<input type="radio" id="tab-2" name="tab-group">
			<label for="tab-2"><?php echo $this->lang->line('orders_historical') ?></label>
			<div class="content">
			<?php $this->load->view("reports/tabular_details",$list);  ?>
			</div>
		</li>
	</ul>
</section>
<?php $this->load->view('partial/footer'); ?>
<script>
$(function() {
	var configs = JSON.parse(sessionStorage.getItem('sales')) || {'active_tab': 'tab-1'};
	$('#'+configs.active_tab).attr('checked', 'checked');
	var idTab = $('.tab > input[type=radio]:checked').attr('id');
	control_tab( idTab );
	$('.tab > input[type=radio]').change(function(e) {
		control_tab(e.target.id);
	});
	$("#orders_filter_form input[type='radio']").click(function(){
		$('#orders_filter_form').submit();
	});
	$("#orders_filter_form select").change(function(){
		$('#orders_filter_form').submit();
	});
});
function control_tab(idTab){
	var obj = {'active_tab': idTab};
	sessionStorage.setItem( 'sales', JSON.stringify(obj) );
	switch( idTab ){
		case 'tab-1':
			$('#filter-bar').hide();
		break;
		case 'tab-2':
			$('#filter-bar').fadeIn('400');
		break;
	}
	console.log(sessionStorage);
}
</script>
