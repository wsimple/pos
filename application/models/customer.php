<?php
class Customer extends Person
{
	var $con;

    function __construct()
    {
        parent::__construct();

        //Seleccion de DB
        // $this->session->set_userdata(array('dblocation'=>'other'));
        $db = $this->session->userdata('dblocation');
        if($db)
            $this->con = $this->load->database($db, true);
        else
            $this->con = $this->db;
    }
	/*
	Determines if a given person_id is a customer
	*/
	function exists($search)
	{ //echo '---('.$search.')---';
		$this->con->from('customers');
		$this->con->join('people', 'people.person_id = customers.person_id');

		if(is_array($search)){
			foreach ($search as $field => $value) {
				$this->con->where($field,$value);
			}
		}else	$this->con->where('customers.person_id',$search); //person_id
		$query = $this->con->get();
		return ($query->num_rows()==1);
	}
	
	function exists_email($email)
	{
		$this->con->from('people');
		$this->con->join('customers', 'customers.person_id = people.person_id');
		$this->con->where('email',$email);

		$query = $this->con->get();

		 if($query->num_rows() == 1){
		 	$row = $query->row();
			return $row->email;
		 }else{
		 	return 0;
		 }
	}

	/*
	Returns all the customers
	*/
	function get_all($limit=10000, $offset=0)
	{
		$this->con->from('customers');
		$this->con->join('people','customers.person_id=people.person_id');
		$this->con->where('deleted',0);
		$this->con->order_by("last_name", "asc");
		$this->con->limit($limit);
		$this->con->offset($offset);
		return $this->con->get();
	}

	function get_all_credits($limit=10000, $offset=0,$customer_id=0)
	{
		$this->con->select('credit_id, person_custo_id, payment_amount, payment_period, day_pay, type, status');
		$this->con->from('credits');
		$this->con->join('people', 'people.person_id = credits.person_custo_id');
		$this->con->where('person_custo_id',$customer_id);
		$this->con->order_by("credit_id", "desc");
		$this->con->limit($limit);
		// $this->con->offset($offset);
		//$query = $this->con->get();
		return $this->con->get();
	}

	function get_type_credits($customer_id)
	{
		$this->con->select('credit_id, person_custo_id, payment_amount, payment_period, day_pay, type, status');
		$this->con->from('credits');
		$this->con->where('person_custo_id',$customer_id);
		$this->con->where('type','credit');
		$this->con->where('status','not paid');
		$this->con->order_by("credit_id", "asc");
	
		return $this->con->get();
	}

	function get_advance_credits($customer_id)
	{
		$this->con->select('SUM(payment_amount) as sum');
		$this->con->from('credits');
		$this->con->where('person_custo_id',$customer_id);
		$this->con->where('type','pay');
		$this->con->where('status','advance');
	
		return $this->con->get()->row();
	}

	function change_advance_credit($credit_id){
		$this->con->where('person_custo_id', $credit_id);
		$this->con->where('status','advance');
		$success = $this->con->update('credits',  array('status' => 'advance ready'));
	}

	function change_status_credit($credit_id){
		$this->con->where('credit_id', $credit_id);
		$success = $this->con->update('credits',  array('status' => 'paid'));
	}
	
	function count_all()
	{
		$this->con->from('customers');
		$this->con->where('deleted',0);
		return $this->con->count_all_results();
	}

	function count_all_credits($customer_id)
	{
		$this->con->from('credits');
		$this->con->where('person_custo_id',$customer_id);
		return $this->con->count_all_results();
	}

	/* sume creditos clientes*/
	function sum_all_credits($customer_id)
	{
		$this->con->select('SUM(payment_amount) as sum');
		$this->con->from('credits');
		$this->con->where('person_custo_id',$customer_id);
		$this->con->where('type','credit');
		return $this->con->get()->row();
	}

	/* balance total clientes*/
	function balance_total($customer_id)
	{

		$this->con->select('balance');
		$this->con->from('credits');
		$this->con->where('person_custo_id',$customer_id);
		
		$this->con->order_by("credit_id", "desc");
		$this->con->limit(1);

		$query = $this->con->get();
		
		// return $this->con->get()->row();


		if($query->num_rows()==1)
			return $query->row();
		else
			return false;
		
	}

	/*
	Gets information about a particular customer
	*/
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

	/* credits account customers*/
	function get_info_credits($customer_id)
	{
		$this->con->from('credits');
		$this->con->join('people', 'people.person_id = credits.person_custo_id');
		$this->con->where('credits.person_custo_id',$customer_id);
		$query = $this->con->get();

		if($query->num_rows()!=0)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $customer_id is NOT an customer
			// $person_obj=parent::get_info(-1);

			// //Get all the fields from credits table
			// $fields = $this->con->list_fields('credits');

			// //append those fields to base parent object, we we have a complete empty object
			// foreach ($fields as $field)
			// {
			// 	$person_obj->$field='';
			// }

			return $query->num_rows();
		}
	}

	/*
	Gets information about a particular customer by first name
	*/
	function get_info_by_name($customer_name)
	{
		$this->con->from('customers');
		$this->con->join('people', 'people.person_id = customers.person_id');
		$this->con->where('people.first_name',$customer_name);
		$this->con->limit(1);
		$query = $this->con->get();

		if($query->num_rows()==1)
			return $query->row();
		else
			return false;
	}

	/*
	Gets information about multiple customers
	*/
	function get_multiple_info($customer_ids)
	{
		$this->con->from('customers');
		$this->con->join('people', 'people.person_id = customers.person_id');
		$this->con->where_in('customers.person_id',$customer_ids);
		$this->con->order_by("last_name", "asc");
		return $this->con->get();
	}

	/*
	Inserts or updates a customer
	*/
	function save(&$person_data, &$customer_data,$customer_id=false)
	{
		$success=false;
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->con->trans_start();

		if(parent::save($person_data,$customer_id))
		{
			//if (!$customer_id or !$this->exists($customer_id))
			if ($customer_id==-1)
			{
				$customer_data['person_id'] = $person_data['person_id'];
				$customer_data['account_number'] = $person_data['person_id'];
				$success = $this->con->insert('customers',$customer_data);
			}
			else
			{
				$this->con->where('person_id', $customer_id);
				$success = $this->con->update('customers', $customer_data);
			}

		}

		$this->con->trans_complete();
		return $success;
	}

	function save_credit(&$credit_data, $date_end=-1)
	{
		$success=false;
		$this->con->set('day_pay','now()',false);
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$success = $this->con->insert('credits',$credit_data);

		$id_credit = $this->con->insert_id();

		// if ($date_end) {
		// 	$this->con->select('payment_period,day_pay');
		// 	$this->con->from('credits');
		// 	$this->con->where('credit_id',$id_credit);
		// 	$query = $this->con->get();
		// 	$data = $query->row();
		// 	$day_pay = $data->day_pay;

		// 	$payment_period = explode('_', $data->payment_period);
		// 	$period = $payment_period[0].' '.$payment_period[1];

		// 	$fecha = date_create($day_pay);
		// 	date_add($fecha, date_interval_create_from_date_string($period));
		// 	$dateNew =  date_format($fecha, 'Y-m-d');

		// 	$this->con->where('person_id', $credit_data['person_custo_id']);
		// 	$success = $this->con->update('customers',  array('end_date_credit' => $dateNew));
		// 	return $this->con->last_query();
		// }

		return $id_credit;
	}

	function check_final_date_credit($customer_id){

		$this->con->select('end_date_credit');
		$this->con->from('customers');
		$this->con->where('person_id',$customer_id);
		$date_end_credit = $this->con->get();

		if($date_end_credit->num_rows()==1)
			return $date_end_credit->row();
		else
			return false;
	}

	/*
	Deletes one customer
	*/
	function delete($customer_id)
	{
		$this->con->where('person_id', $customer_id);
		return $this->con->update('customers', array('deleted' => 1));
	}

	/*
	Deletes a list of customers
	*/
	function delete_list($customer_ids)
	{
		$this->con->where_in('person_id',$customer_ids);
		return $this->con->update('customers', array('deleted' => 1));
 	}

 	/*
	Get search suggestions to find customers
	*/
	function get_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

		$this->con->from('customers');
		$this->con->join('people','customers.person_id=people.person_id');
		$this->con->where("(first_name LIKE '%".$this->con->escape_like_str($search)."%' or
		last_name LIKE '%".$this->con->escape_like_str($search)."%' or
		CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->con->escape_like_str($search)."%') and deleted=0");
		$this->con->order_by("last_name", "asc");
		$by_name = $this->con->get();
		foreach($by_name->result() as $row)
		{
			$suggestions[]=$row->first_name.' '.$row->last_name;
		}

		$this->con->from('customers');
		$this->con->join('people','customers.person_id=people.person_id');
		$this->con->where('deleted',0);
		$this->con->like("email",$search);
		$this->con->order_by("email", "asc");
		$by_email = $this->con->get();
		foreach($by_email->result() as $row)
		{
			$suggestions[]=$row->email;
		}

		$this->con->from('customers');
		$this->con->join('people','customers.person_id=people.person_id');
		$this->con->where('deleted',0);
		$this->con->like("phone_number",$search);
		$this->con->order_by("phone_number", "asc");
		$by_phone = $this->con->get();
		foreach($by_phone->result() as $row)
		{
			$suggestions[]=$row->phone_number;
		}

		$this->con->from('customers');
		$this->con->join('people','customers.person_id=people.person_id');
		$this->con->where('deleted',0);
		$this->con->like("account_number",$search);
		$this->con->order_by("account_number", "asc");
		$by_account_number = $this->con->get();
		foreach($by_account_number->result() as $row)
		{
			$suggestions[]=$row->account_number;
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;

	}

	/*
	Get search suggestions to find customers
	*/
	function get_customer_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

		$this->con->from('customers');
		$this->con->join('people','customers.person_id=people.person_id');
		$this->con->where("(first_name LIKE '%".$this->con->escape_like_str($search)."%' or
		last_name LIKE '%".$this->con->escape_like_str($search)."%' or
		CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->con->escape_like_str($search)."%') and deleted=0");
		$this->con->order_by("last_name", "asc");
		$by_name = $this->con->get();
		foreach($by_name->result() as $row)
		{
			$suggestions[]=$row->person_id.'|'.$row->first_name.' '.$row->last_name;
		}

		$this->con->from('customers');
		$this->con->join('people','customers.person_id=people.person_id');
		$this->con->where('deleted',0);
		$this->con->like("account_number",$search);
		$this->con->order_by("account_number", "asc");
		$by_account_number = $this->con->get();
		foreach($by_account_number->result() as $row)
		{
			$suggestions[]=$row->person_id.'|'.$row->account_number;
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;

	}
	/*
	Preform a search on customers
	*/
	function search($search)
	{
		$this->con->from('customers');
		$this->con->join('people','customers.person_id=people.person_id');
		$this->con->where("(first_name LIKE '%".$this->con->escape_like_str($search)."%' or
		last_name LIKE '%".$this->con->escape_like_str($search)."%' or
		email LIKE '%".$this->con->escape_like_str($search)."%' or
		phone_number LIKE '%".$this->con->escape_like_str($search)."%' or
		account_number LIKE '%".$this->con->escape_like_str($search)."%' or
		CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->con->escape_like_str($search)."%') and deleted=0");
		$this->con->order_by("last_name", "asc");

		return $this->con->get();
	}

}
?>
