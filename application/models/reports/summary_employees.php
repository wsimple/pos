<?php
require_once("report.php");
class Summary_employees extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array($this->lang->line('reports_employee'), $this->lang->line('reports_subtotal'), $this->lang->line('reports_total'), $this->lang->line('reports_tax'), $this->lang->line('reports_profit'));
	}
	
	public function getData(array $inputs)
	{
		$this->con->select('CONCAT(first_name, " ",last_name) as employee, sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit', false);
		$this->con->from('sales_items_temp');
		$this->con->join('employees', 'employees.person_id = sales_items_temp.employee_id');
		$this->con->join('people', 'employees.person_id = people.person_id');
		$this->con->where('sale_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
		if ($inputs['sale_type'] == 'sales')
		{
			$this->con->where('quantity_purchased > 0');
			$this->con->where('mode', 0);
		}
		elseif ($inputs['sale_type'] == 'returns')
		{
			$this->con->where('quantity_purchased < 0');
		}elseif ($inputs['sale_type'] == 'shipping')
		{
			$this->con->where('mode', 2);
		}
		$this->con->group_by('employee_id');
		$this->con->order_by('last_name');

		return $this->con->get()->result_array();
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->con->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit');
		$this->con->from('sales_items_temp');
		$this->con->join('employees', 'employees.person_id = sales_items_temp.employee_id');
		$this->con->join('people', 'employees.person_id = people.person_id');
		$this->con->where('sale_date BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
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