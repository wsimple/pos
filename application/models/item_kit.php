<?php
class Item_kit extends CI_Model
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
	Determines if a given item_id is an item kit
	*/
	function exists($item_kit_id)
	{
		$this->con->from('item_kits');
		$this->con->where('item_kit_id',$item_kit_id);
		$query = $this->con->get();

		return ($query->num_rows()==1);
	}

	/*
	Returns all the item kits
	*/
	function get_all($limit=10000, $offset=0)
	{
		$this->con->from('item_kits');
		$this->con->order_by("name", "asc");
		$this->con->limit($limit);
		$this->con->offset($offset);
		return $this->con->get();
	}
	
	function count_all()
	{
		$this->con->from('item_kits');
		return $this->con->count_all_results();
	}

	/*
	Gets information about a particular item kit
	*/
	function get_info($item_kit_id)
	{
		$this->con->from('item_kits');
		$this->con->where('item_kit_id',$item_kit_id);
		
		$query = $this->con->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $item_kit_id is NOT an item kit
			$item_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->con->list_fields('item_kits');

			foreach ($fields as $field)
			{
				$item_obj->$field='';
			}

			return $item_obj;
		}
	}

	/*
	Gets information about multiple item kits
	*/
	function get_multiple_info($item_kit_ids)
	{
		$this->con->from('item_kits');
		$this->con->where_in('item_kit_id',$item_kit_ids);
		$this->con->order_by("name", "asc");
		return $this->con->get();
	}

	/*
	Inserts or updates an item kit
	*/
	function save(&$item_kit_data,$item_kit_id=false)
	{
		if (!$item_kit_id or !$this->exists($item_kit_id))
		{
			if($this->con->insert('item_kits',$item_kit_data))
			{
				$item_kit_data['item_kit_id']=$this->con->insert_id();
				return true;
			}
			return false;
		}

		$this->con->where('item_kit_id', $item_kit_id);
		return $this->con->update('item_kits',$item_kit_data);
	}

	/*
	Deletes one item kit
	*/
	function delete($item_kit_id)
	{
		return $this->con->delete('item_kits', array('item_kit_id' => $id)); 	
	}

	/*
	Deletes a list of item kits
	*/
	function delete_list($item_kit_ids)
	{
		$this->con->where_in('item_kit_id',$item_kit_ids);
		return $this->con->delete('item_kits');		
 	}

 	/*
	Get search suggestions to find kits
	*/
	function get_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

		$this->con->from('item_kits');
		$this->con->like('name', $search);
		$this->con->order_by("name", "asc");
		$by_name = $this->con->get();
		foreach($by_name->result() as $row)
		{
			$suggestions[]=$row->name;
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;

	}

	function suggest2($search,$limit=25)
	{
		$suggestions = array();

		$this->con->from('item_kits'); 
		$search = array('item_kit_id' => $search, 'name' => $search, 'description' => $search);
		$this->con->or_like($search);
		$this->con->order_by("name", "asc");
		$by_name = $this->con->get();
		if ($by_name->num_rows() > 0) {
			return $by_name;
		}
		return false;
	}
	
	function get_item_kit_search_suggestions($search, $limit=25)
	{
		$suggestions = array();

		$this->con->from('item_kits');
		$this->con->like('name', $search);
		$this->con->order_by("name", "asc");
		$by_name = $this->con->get();
		foreach($by_name->result() as $row)
		{
			$suggestions[]='KIT '.$row->item_kit_id.'|'.$row->name;
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;
		
	}

	/*
	Preform a search on items
	*/
	function search($search)
	{
		$this->con->from('item_kits');
		$this->con->where("name LIKE '%".$this->con->escape_like_str($search)."%' or 
		description LIKE '%".$this->con->escape_like_str($search)."%'");
		$this->con->order_by("name", "asc");
		return $this->con->get();	
	}

	function info_items_for_kits($item_kit_id,$datos=false){
		if ($datos) $this->con->select($datos);
		$this->con->from('item_kit_items');
		$this->con->join('items','item_kit_items.item_id=items.item_id');
		$this->con->where('item_kit_id',$item_kit_id);
		return $this->con->get();
	}
}
?>