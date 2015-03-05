<?php
if(!isset($export_excel)) $export_excel=0;
if($export_excel == 1){
	ob_start();
	$this->load->view("partial/header_excel");
}else{
	$this->load->view("partial/header");
}

if(isset($list)){
	$last=count($list)-1;
	$list[0]['first']=true;
	$list[$last]['last']=true;
	for($i=0;$i<=$last;$i++) {
		$this->load->view($view,$list[$i]);
	}
}else{
	$this->load->view($view,$data);
}

if($export_excel == 1){
	$this->load->view("partial/footer_excel");
	$content = ob_end_flush();
	$date=date('Ymd_Hi');
	$filename = trim($this->uri->segment(2));
	// $filename = str_replace(array(' ', '/', '\\'), '', $title);
	$filename .= "_export_$date.xls";
	header('Content-type: application/ms-excel');
	header('Content-Disposition: attachment; filename='.$filename);
	echo $content;
	die();
}else{
?>
	<br/><br/>
	<a class="linkBack big_button" href="#"><span>Back</span></a>
	<a class="linkPrint big_button" href="#"><span>Print</span></a>
	<?php 
	if ($this->uri->segment(2) == 'inventory_low' && $this->Employee->has_privilege('Orders','stock_control')) {
		echo anchor('orders/index/1', $this->lang->line('orders_fill_cart_with_low_stock_items'), 'class="big_button"');
	}
	?>
	<br/><br/>
<?php
	$this->load->view("partial/footer");
} 
?>
