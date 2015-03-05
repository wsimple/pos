<?php
require_once("report.php");
class Summary_payments extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array($this->lang->line('reports_payment_type'), $this->lang->line('reports_total'));
	}
	
	public function getData(array $inputs)
	{
		$this->con->select('sales_payments.payment_type, SUM(payment_amount) as payment_amount', false);
		$this->con->from('sales_payments');
		$this->con->join('sales', 'sales.sale_id=sales_payments.sale_id');
		$this->con->where('date(sale_time) BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
		if ($inputs['sale_type'] == 'sales')
		{
			$this->con->where('payment_amount > 0');
		}
		elseif ($inputs['sale_type'] == 'returns')
		{
			$this->con->where('payment_amount < 0');
		}
		$this->con->group_by("payment_type");
		return $this->con->get()->result_array();
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->con->select('sum(subtotal) as subtotal, sum(total) as total, sum(tax) as tax, sum(profit) as profit');
		$this->con->from('sales_items_temp');
		$this->con->join('items', 'sales_items_temp.item_id = items.item_id');
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