<?php
require_once("report.php");
class Detailed_sales extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array('summary' => array($this->lang->line('reports_sale_id'), $this->lang->line('reports_date'), $this->lang->line('reports_items_purchased'), $this->lang->line('reports_sold_by'), $this->lang->line('reports_sold_to'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'), $this->lang->line('reports_tax'), $this->lang->line('reports_profit'), $this->lang->line('reports_payment_type'), $this->lang->line('')),
					'details' => array($this->lang->line('reports_name'), $this->lang->line('reports_category'), $this->lang->line('reports_serial_number'), $this->lang->line('reports_description'), $this->lang->line('reports_quantity_purchased'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'), $this->lang->line('reports_tax'), $this->lang->line('reports_profit'),$this->lang->line('reports_discount'))
		);		
	}
	
	public function getData(array $inputs)
	{
		$this->con->select('sale_id, sale_date, sum(quantity_purchased) as items_purchased, CONCAT(employee.first_name," ",employee.last_name) as employee_name, CONCAT(customer.first_name," ",customer.last_name) as customer_name, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit, payment_type, comment', false);
		$this->con->from('sales_items_temp');
		$this->con->join('people as employee', 'sales_items_temp.employee_id = employee.person_id');
		$this->con->join('people as customer', 'sales_items_temp.customer_id = customer.person_id', 'left');
		$this->con->where('sale_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
		if ($inputs['sale_type'] == 'sales'){
			$this->con->where('quantity_purchased > 0');
			$this->con->where('mode', 0);
		}elseif ($inputs['sale_type'] == 'returns'){
			$this->con->where('quantity_purchased < 0');
		}elseif ($inputs['sale_type'] == 'shipping'){
			$this->con->where('mode', 2);
		}
		$this->con->group_by('sale_id');
		$this->con->order_by('sale_id', 'desc');

		$data = array();

		$data['summary'] = $this->con->get()->result_array();
		$data['sql'] = $this->con->last_query();
		$data['details'] = array();
		
		foreach($data['summary'] as $key=>$value)
		{
			$this->con->select('name, category, quantity_purchased, serialnumber, sales_items_temp.description, subtotal,total, tax, profit, discount_percent');
			$this->con->from('sales_items_temp');
			$this->con->join('items', 'sales_items_temp.item_id = items.item_id');
			$this->con->where('sale_id = '.$value['sale_id']);
			$data['details'][$key] = $this->con->get()->result_array();
		}
		
		return $data;
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->con->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit');
		$this->con->from('sales_items_temp');
		$this->con->join('people as employee', 'sales_items_temp.employee_id = employee.person_id');
		$this->con->join('people as customer', 'sales_items_temp.customer_id = customer.person_id', 'left');
		$this->con->where('sale_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
		if ($inputs['sale_type'] == 'sales')
		{
			$this->con->where('quantity_purchased > 0');
		}
		elseif ($inputs['sale_type'] == 'returns')
		{
			$this->con->where('quantity_purchased < 0');
		}elseif ($inputs['sale_type'] == 'shipping'){
			$this->con->where('mode', 2);
		}
		
		return $this->con->get()->row_array();
	}
}
?>