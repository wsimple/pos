<?php
class Supplier extends Person
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
	function exists($person_id)
	{
		$this->con->from('suppliers');
		$this->con->join('people', 'people.person_id = suppliers.person_id');
		$this->con->where('suppliers.person_id',$person_id);
		$query = $this->con->get();

		return ($query->num_rows()==1);
	}

	function exists_email($email)
	{
		$this->con->from('people');
		$this->con->join('suppliers', 'suppliers.person_id = people.person_id');
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
	Returns all the suppliers
	*/
	function get_all($limit=10000, $offset=0)
	{
		$this->con->from('suppliers');
		$this->con->join('people','suppliers.person_id=people.person_id');
		$this->con->where('deleted', 0);
		$this->con->order_by("last_name", "asc");
		$this->con->limit($limit);
		$this->con->offset($offset);
		return $this->con->get();
	}

	function count_all()
	{
		$this->con->from('suppliers');
		$this->con->where('deleted',0);
		return $this->con->count_all_results();
	}

	/*
	Gets information about a particular supplier
	*/
	function get_info($supplier_id)
	{
		$this->con->from('suppliers');
		$this->con->join('people', 'people.person_id = suppliers.person_id');
		$this->con->where('suppliers.person_id',$supplier_id);
		$query = $this->con->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $supplier_id is NOT an supplier
			$person_obj=parent::get_info(-1);

			//Get all the fields from supplier table
			$fields = $this->con->list_fields('suppliers');

			//append those fields to base parent object, we we have a complete empty object
			foreach ($fields as $field)
			{
				$person_obj->$field='';
			}

			return $person_obj;
		}
	}

	/*
	Gets information about multiple suppliers
	*/
	function get_multiple_info($suppliers_ids)
	{
		$this->con->from('suppliers');
		$this->con->join('people', 'people.person_id = suppliers.person_id');
		$this->con->where_in('suppliers.person_id',$suppliers_ids);
		$this->con->order_by("last_name", "asc");
		return $this->con->get();
	}

	/*
	Inserts or updates a suppliers
	*/
	function save(&$person_data, &$supplier_data,$supplier_id=false)
	{
		$success=false;
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->con->trans_start();

		if(parent::save($person_data,$supplier_id))
		{
			if (!$supplier_id or !$this->exists($supplier_id))
			{
				$supplier_data['person_id'] = $person_data['person_id'];
				$success = $this->con->insert('suppliers',$supplier_data);
				$this->update_account($person_data['person_id']);
			}
			else
			{
				$this->con->where('person_id', $supplier_id);
				$success = $this->con->update('suppliers',$supplier_data);
				$this->update_account($supplier_id);
			}

		}

		$this->con->trans_complete();
		return $success;
	}

	function update_account($supplier_id){
		$account = array (
			//'account_number'=>str_pad($supplier_id, 10, "0", STR_PAD_LEFT),
			'account_number'=>$supplier_id
		);
		$this->con->where('person_id', $supplier_id);
		$this->con->update('suppliers',$account);
	}

	/*
	Deletes one supplier
	*/
	function delete($supplier_id)
	{
		$this->con->where('person_id', $supplier_id);
		return $this->con->update('suppliers', array('deleted' => 1));
	}

	/*
	Deletes a list of suppliers
	*/
	function delete_list($supplier_ids)
	{
		$this->con->where_in('person_id',$supplier_ids);
		return $this->con->update('suppliers', array('deleted' => 1));
 	}

 	/*
	Get search suggestions to find suppliers
	*/
	function get_search_suggestions($search,$limit=25)
	{
		$suggestions = array();
		$this->con->from('suppliers');
		$this->con->join('people','suppliers.person_id=people.person_id');
		$this->con->where('deleted', 0);
		$this->con->like("company_name",$search);
		$this->con->order_by("company_name", "asc");
		$by_company_name = $this->con->get();
		foreach($by_company_name->result() as $row)
		{
			$suggestions[]=$row->company_name;
		}


		$this->con->from('suppliers');
		$this->con->join('people','suppliers.person_id=people.person_id');
		$this->con->where("(first_name LIKE '%".$this->con->escape_like_str($search)."%' or
		last_name LIKE '%".$this->con->escape_like_str($search)."%' or
		CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->con->escape_like_str($search)."%') and deleted=0");
		$this->con->order_by("last_name", "asc");
		$by_name = $this->con->get();
		foreach($by_name->result() as $row)
		{
			$suggestions[]=$row->first_name.' '.$row->last_name;
		}

		$this->con->from('suppliers');
		$this->con->join('people','suppliers.person_id=people.person_id');
		$this->con->where('deleted', 0);
		$this->con->like("email",$search);
		$this->con->order_by("email", "asc");
		$by_email = $this->con->get();
		foreach($by_email->result() as $row)
		{
			$suggestions[]=$row->email;
		}

		$this->con->from('suppliers');
		$this->con->join('people','suppliers.person_id=people.person_id');
		$this->con->where('deleted', 0);
		$this->con->like("phone_number",$search);
		$this->con->order_by("phone_number", "asc");
		$by_phone = $this->con->get();
		foreach($by_phone->result() as $row)
		{
			$suggestions[]=$row->phone_number;
		}

		$this->con->from('suppliers');
		$this->con->join('people','suppliers.person_id=people.person_id');
		$this->con->where('deleted', 0);
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
	Get search suggestions to find suppliers
	*/
	function get_suppliers_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

		$this->con->from('suppliers');
		$this->con->join('people','suppliers.person_id=people.person_id');
		$this->con->where('deleted', 0);
		$this->con->like("company_name",$search);
		$this->con->order_by("company_name", "asc");
		$by_company_name = $this->con->get();
		foreach($by_company_name->result() as $row)
		{
			$suggestions[]=$row->person_id.'|'.$row->company_name;
		}


		$this->con->from('suppliers');
		$this->con->join('people','suppliers.person_id=people.person_id');
		$this->con->where("(first_name LIKE '%".$this->con->escape_like_str($search)."%' or
		last_name LIKE '%".$this->con->escape_like_str($search)."%' or
		CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->con->escape_like_str($search)."%') and deleted=0");
		$this->con->order_by("last_name", "asc");
		$by_name = $this->con->get();
		foreach($by_name->result() as $row)
		{
			$suggestions[]=$row->person_id.'|'.$row->first_name.' '.$row->last_name;
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;

	}
	/*
	Perform a search on suppliers
	*/
	function search($search)
	{
		$this->con->from('suppliers');
		$this->con->join('people','suppliers.person_id=people.person_id');
		$this->con->where("(first_name LIKE '%".$this->con->escape_like_str($search)."%' or
		last_name LIKE '%".$this->con->escape_like_str($search)."%' or
		company_name LIKE '%".$this->con->escape_like_str($search)."%' or
		email LIKE '%".$this->con->escape_like_str($search)."%' or
		phone_number LIKE '%".$this->con->escape_like_str($search)."%' or
		account_number LIKE '%".$this->con->escape_like_str($search)."%' or
		CONCAT(`first_name`,' ',`last_name`) LIKE '%".$this->con->escape_like_str($search)."%') and deleted=0");
		$this->con->order_by("last_name", "asc");

		return $this->con->get();
	}

}
?>
