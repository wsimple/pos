<?php
require_once("report.php");
class Summary_credits extends Report
{
	function __construct()
	{
		parent::__construct();
	}
	
	public function getDataColumns()
	{
		return array($this->lang->line('reports_credits_date'),$this->lang->line('reports_credits_reference'),$this->lang->line('reports_credits_amount'),$this->lang->line('reports_credits_balance'),$this->lang->line('reports_credits_period'),$this->lang->line('customers_lbl_td_type'),$this->lang->line('reports_credits_status'));
	}
	
	public function getDataColumnsOverdue()
	{
		return array(' ',$this->lang->line('reports_credits_amount'),$this->lang->line('reports_credits_period'),$this->lang->line('reports_credits_date'),$this->lang->line('reports_credits_status'),'Ended');
	}
	

	public function getData(array $inputs)
	{
	
		$this->con->from('credits');
		$this->con->join('people', 'people.person_id = credits.person_custo_id');
		$this->con->where('day_pay BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
		$this->con->where('person_custo_id',$inputs['customer_id']);
		$this->con->order_by("credit_id", "desc");

		return $this->con->get()->result_array();
				
	}
	
	public function getSummaryData(array $inputs)
	{
		$this->con->select('SUM(payment_amount) as sum');
		$this->con->from('credits');
		$this->con->where('day_pay BETWEEN "'. $inputs['start_date']. '" and "'. $inputs['end_date'].'"');
		$this->con->where('person_custo_id',$inputs['customer_id']);
		
		return $this->con->get()->row_array();		
	}

	public function get_all_credits(array $inputs)
	{
		$this->con->select('credit_id, person_custo_id, payment_amount, payment_period, day_pay, status');
		$this->con->from('credits');
		$this->con->join('people', 'people.person_id = credits.person_custo_id');
		$this->con->where('person_custo_id',$customer_id);
		$this->con->order_by("credit_id", "asc");
	
		return $this->con->get();
	}

	function get_info($customer_id)
	{
		$this->con->from('customers');
		$this->con->join('people', 'people.person_id = customers.person_id');
		$this->con->where('customers.person_id',$customer_id);
		$query = $this->con->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $customer_id is NOT an customer
			$person_obj=parent::get_info(-1);

			//Get all the fields from customer table
			$fields = $this->con->list_fields('customers');

			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$person_obj->$field='';
			}

			return $person_obj;
		}
	}
	
	/*
	Gets information about a particular customer by first name
	*/
	public function balance_total($customer_id)
	{
		$this->con->select('balance');
		$this->con->from('credits');
		$this->con->where('credits.person_custo_id',$customer_id);
		$this->con->limit(1);
		$this->con->order_by("credit_id", "desc");
		$query = $this->con->get();

		if($query->num_rows()==1)
			return $query->row();
		else
			return false;
	}

	public function balance_by_day($customer_id,$date_ini = 0,$date_end = 0,$order)
	{

		$this->con->select('balance');
		$this->con->from('credits');
		$this->con->where('credits.person_custo_id',$customer_id);
		$this->con->where('day_pay BETWEEN "'. $date_ini. '" and "'. $date_end.'"');
		$this->con->order_by("credit_id", $order);
		// $this->con->order_by("day_pay", $order);
		$this->con->limit(1);
		$query = $this->con->get();

		if($query->num_rows()==1)
			return $query->row();
		else
			return false;
	}


	public function get_all_credits_overdue_bills($customer_id)
	{
		$this->con->select('credit_id,payment_amount,payment_period,day_pay,status, DATE_ADD(day_pay,INTERVAL payment_period DAY) as ended',false);
		$this->con->from('credits');
		$this->con->where('person_custo_id',$customer_id);
		$this->con->where('status','not paid');
		$this->con->where('DATE_ADD(day_pay,INTERVAL payment_period DAY) <=','CURDATE()',false);	
	
		return $this->con->get()->result_array();
	}

}
?>