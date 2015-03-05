<?php
class Person extends CI_Model
{
	var $con;

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        //Seleccion de DB
        // $this->session->set_userdata(array('dblocation'=>'other'));
        $db = $this->session->userdata('dblocation');
        if($db)
            $this->con = $this->load->database($db, true);
        else
            $this->con = $this->db;
    }

	/*Determines whether the given person exists*/
	function exists($person_id)
	{	
		$this->con->from('people');
		$this->con->where('people.person_id',$person_id);
		$query = $this->con->get();

		return ($query->num_rows()==1);
	}

	/*Gets all people*/
	function get_all($limit=10000, $offset=0)
	{
		$this->con->from('people');
		$this->con->order_by("last_name", "asc");
		$this->con->limit($limit);
		$this->con->offset($offset);
		return $this->con->get();
	}

	function count_all()
	{
		$this->con->from('people');
		$this->con->where('deleted',0);
		return $this->con->count_all_results();
	}

	/*
	Gets information about a person as an array.
	*/
	function get_info($person_id)
	{
		$query = $this->con->get_where('people', array('person_id' => $person_id), 1);

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//create object with empty properties.
			$fields = $this->con->list_fields('people');
			$person_obj = new stdClass;

			foreach ($fields as $field)
			{
				$person_obj->$field='';
			}

			return $person_obj;
		}
	}

	/*
	Get people with specific ids
	*/
	function get_multiple_info($person_ids)
	{
		$this->con->from('people');
		$this->con->where_in('person_id',$person_ids);
		$this->con->order_by("last_name", "asc");
		return $this->con->get();
	}

	public function exists_v2($where)
	{
		$query = $this->con->query("
			SELECT person_id 
			FROM ospos_people 
			$where
			LIMIT 1
		");
		return ($query->num_rows()) > 0 ? true : false;
	}

	/*
	Inserts or updates a person
	*/
	function save(&$person_data,$person_id=false)
	{//print_r($person_data);
		$email = '';
		if (isset($person_data['email']) && $person_data['email'] != '') {
			$email = $person_data['email'];
		}

		if ($person_id!=-1){

			$this->con->where('person_id', $person_id);
			return $this->con->update('people',$person_data);

		}else{

			if (!$this->exists_v2(" WHERE email LIKE '".$email."' ") && $email!='')
			{
				if ($this->con->insert('people',$person_data))
				{ //echo '......';
					$person_data['person_id']=$this->con->insert_id();
					return $this->con->insert_id();
				}
				return false;
			}
		}
		
	}

	/*
	Deletes one Person (doesn't actually do anything)
	*/
	function delete($person_id)
	{
		return true;;
	}

	/*
	Deletes a list of people (doesn't actually do anything)
	*/
	function delete_list($person_ids)
	{
		return true;
 	}

}
?>
