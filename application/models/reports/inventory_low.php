<?php
require_once("report.php");
class Inventory_low extends Report
{	
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array($this->lang->line('reports_item_name'), $this->lang->line('reports_item_number'), $this->lang->line('reports_description'), $this->lang->line('reports_count'), $this->lang->line('reports_reorder_level'));
	}
	
	public function getData(array $inputs,$order=false){
		if ($order) $this->con->select('item_id');
		else  $this->con->select('name, item_number, quantity, reorder_level, description');
		$this->con->from('items');
		$this->con->where('( quantity < 1 OR quantity < reorder_level) AND deleted=0 AND is_service=0');
		$this->con->order_by('name', 'desc');
		return $this->con->get()->result_array();
	}
	public function get_infoData(){
		$this->con->select('item_id,name, item_number, quantity,is_service,reorder_level, description,cost_price,unit_price,category');
		$this->con->from('items');
		// $this->con->where('quantity <= reorder_level and deleted=0');
		$this->con->order_by('name');		
		return $this->con->get()->result_array();

	}
	
	public function getSummaryData(array $inputs)
	{
		return array();
	}
}
?>