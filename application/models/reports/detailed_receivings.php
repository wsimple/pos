<?php
require_once("report.php");
class Detailed_receivings extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array('summary' => array($this->lang->line('reports_receiving_id'), $this->lang->line('reports_date'), $this->lang->line('reports_items_received'), $this->lang->line('reports_received_by'), $this->lang->line('reports_supplied_by'), $this->lang->line('reports_total'), $this->lang->line('reports_payment_type'), $this->lang->line('reports_comments')),
					'details' => array($this->lang->line('reports_name'), $this->lang->line('reports_category'), $this->lang->line('reports_quantity_purchased'), $this->lang->line('reports_total'), $this->lang->line('reports_discount'))
		);		
	}
	
	public function getData(array $inputs,$debt=false){
		$this->con->select('receiving_id, receiving_date, sum(quantity_purchased) as items_purchased, CONCAT(employee.first_name," ",employee.last_name) as employee_name, CONCAT(supplier.first_name," ",supplier.last_name) as supplier_name, payment as total, sum(profit) as profit, payment_type, comment,sum(total) as money', false);
		$this->con->from('receivings_items_temp');
		$this->con->join('people as employee', 'receivings_items_temp.employee_id = employee.person_id');
		$this->con->join('people as supplier', 'receivings_items_temp.supplier_id = supplier.person_id', 'left');
		if (!$debt){ $this->con->where('receiving_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
			if ($inputs['sale_type'] == 'sales'){
				$this->con->where('quantity_purchased > 0');
			}elseif ($inputs['sale_type'] == 'returns'){
				$this->con->where('quantity_purchased < 0');
			}
		}else{
			$this->con->where('receivings_items_temp.supplier_id IS NOT NULL');
		}
		$this->con->group_by('receiving_id');
		$this->con->order_by('receiving_date DESC');

		if ($debt){
			return $this->con->get()->result_array();
		}else{		
			$data = array();
			$data['summary'] = $this->con->get()->result_array();
			$data['details'] = array();
			
			foreach($data['summary'] as $key=>$value){
				$this->con->select('name, category, quantity_purchased, serialnumber,total, discount_percent');
				$this->con->from('receivings_items_temp');
				$this->con->join('items', 'receivings_items_temp.item_id = items.item_id');
				$this->con->where('receiving_id = '.$value['receiving_id']);
				$data['details'][$key] = $this->con->get()->result_array();
			}
			return $data;
		}
	}
	public function getSummaryData(array $inputs)
	{
		$this->con->select('sum(total) as total');
		$this->con->from('receivings_items_temp');
		$this->con->where('receiving_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
		if ($inputs['sale_type'] == 'sales')
		{
			$this->con->where('quantity_purchased > 0');
		}
		elseif ($inputs['sale_type'] == 'returns')
		{
			$this->con->where('quantity_purchased < 0');
		}
		return $this->con->get()->row_array();
	}
}
?>