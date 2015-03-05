<?php
require_once ("secure_area.php");
class Stock_control extends Secure_area 
{
	function __construct(){
		parent::__construct('stock_control');
	}
	
	function index(){
		$this->reload_();
	}
	function reload_(){
		if(!$this->session->userdata('stock_control')){
			if($this->Employee->has_privilege('Receivings','stock_control')) $this->goto_receiving();
			if($this->Employee->has_privilege('Shipping','stock_control')) $this->goto_shipping();
			if($this->Employee->has_privilege('Orders','stock_control')) $this->goto_orders();
		}else{
			switch ($this->session->userdata('stock_control')){
				case 'Receivings': redirect('receivings');	break;
				case 'Shipping': redirect('sales/index/shipping');	break;
				case 'Orders': redirect('orders');	break;
			}
		}
	}
	function goto_receiving(){ 
		$this->session->set_userdata('stock_control','Receivings');
		$this->reload_();
	}
	function goto_shipping(){ 
		$this->session->set_userdata('stock_control','Shipping');
		$this->reload_();
	}
	function goto_orders(){ 
		$this->session->set_userdata('stock_control','Orders');
		$this->reload_();
	}
}
?>