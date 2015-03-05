<?php
require_once("report.php");
class Inventory_summary extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array($this->lang->line('reports_item_name'), $this->lang->line('reports_item_number'), $this->lang->line('reports_description'), $this->lang->line('reports_count'), $this->lang->line('reports_reorder_level'));
	}
	
	public function getData(array $inputs)
	{
		$this->con->select('name, item_number, quantity, reorder_level, description');
		$this->con->from('items');
		$this->con->where('deleted', 0);	
		$this->con->order_by('name');
		return $this->con->get()->result_array();
	}
	
	public function getSummaryData(array $inputs)
	{
		return array();
	}
}
?>