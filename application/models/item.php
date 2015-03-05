<?php
class Item extends CI_Model
{
	var $con;

    function __construct()
    {
        parent::__construct();
        $this->load->database();
        //Seleccion de DB
        // $this->session->set_userdata(array('dblocation'=>'other'));
        $db = ($this->session->userdata('items_location')) ? $this->session->userdata('items_location') :$this->session->userdata('dblocation') ;
        if($db){
            $this->con = $this->load->database($db, true);
        }else{
            $this->con = $this->db;
        }
    }
	/* Determines if a given item_id is an item */
	function exists($item_id)
	{
		$this->con->from('items')->where('item_id', $item_id);
		$query = $this->con->get();

		return ($query->num_rows()==1);
	}
	/*changes db*/
	public function stabledb($db,$retu=false){
        if($db) $this->con = $this->load->database($db, true);
        else $this->con = $this->db;
        if ($retu) return $this->con;
	}
	/*Returns all the items */
	function get_all($data=array(),$limit=10000, $offset=0){
		$this->con->from('items');
		if (count($data)>0 && isset($data['low_inventory'])) $this->get_filt($data);
		$this->con->where('deleted', 0);
		$this->con->order_by('name');
		$this->con->limit($limit);
		$this->con->offset($offset);
		return $this->con->get();
	}
	function count_all($data=array()){
		$this->con->select('item_id');
		$this->con->from('items');
		if (count($data)>0 && isset($data['low_inventory'])) $this->get_filt($data);
		$this->con->where('deleted',0);
		return $this->con->count_all_results();
	}
	private function get_filt($data){
		if ($data['low_inventory'] !=0 ) $this->con->where('quantity <=','reorder_level', false);
		if ($data['is_serialized'] !=0 ) $this->con->where('is_serialized',1);
		if ($data['no_description']!=0 ) $this->con->where('description','');
	}
	function get_broken_items($limit=10000, $offset=0)
	{
		$this->con->from('items')->where('deleted', 0)->where('broken_quantity >', 0)->order_by('name')->limit($limit)->offset($offset);
		return $this->con->get()->result_array();
	}
	/*
	Gets information about a particular item
	*/
	function get_info($item_id,$campos=false)
	{
		if ($campos) $this->con->select($campos);
		$this->con->from('items');
		$this->con->join('model','model.model_id=items.model_id','left');
		$this->con->join('brand','brand.brand_id=model.brand_id','left');
		$this->con->where('item_id',$item_id);

		$query = $this->con->get();

		if($query->num_rows()==1)
		{
			$field=$query->row();
			if (!$campos){
				//service items have not quantity counter, start with a default value
				if($field->is_service) $field->quantity=10;
				//special items (<0) have not stock, 1 as default value
				if($field->item_id<0) $field->quantity=1;
			}
			return $field;
		}
		else
		{
			//Get empty base parent object, as $item_id is NOT an item
			$item_obj=new stdClass();

			//Get all the fields from items table
			$fields = $this->con->list_fields('items');

			foreach ($fields as $field)
			{
				//service items have not quantity counter, start with a default value
				//if($field->is_service) $field->quantity=10;
				$item_obj->$field='';
			}

			return $item_obj;
		}
	}

	/*
	Get an item id given an item number
	*/
	function get_item_id($item_number)
	{
		$this->con->from('items');
		$this->con->where('item_number',$item_number);

		$query = $this->con->get();

		if($query->num_rows()==1)
		{
			return $query->row()->item_id;
		}

		return false;
	}

	/*
	Gets information about multiple items
	*/
	function get_multiple_info($item_ids,$datos=false)
	{
		if($datos) $this->con->select($datos);
		$this->con->from('items');
		$this->con->where_in('item_id',$item_ids);
		$this->con->order_by('item_id', 'asc');
		return $this->con->get();
	}

	/*
	Inserts or updates a item
	*/
	function save(&$item_data,$item_id=false){
		if (!$item_id or !$this->exists($item_id)){
			if($this->con->insert('items',$item_data)){
				$item_data['item_id']=$this->con->insert_id();
				return $item_data['item_id'];
			}
			return false;
		}

		$this->con->where('item_id', $item_id);
		return $this->con->update('items',$item_data);
	}

	/*
	Inserts or updates a item in other inventory
	*/
	function save_in_other_inventory(&$item_data,$item_id=false, $db=null)
	{
		if($db != '...' && $db != null) $otherdb = $this->load->database($db, true);
		else return false;

		//Obtengo la cantidad actual de items en stock
		$otherdb->from('items');
		$otherdb->where('item_id',$item_id);
		$query = $otherdb->get();
		$cur_item_info = $query->row();

		//Sumo items tranapasados a la cantiidad de stock actual
		$item_data['quantity'] += $cur_item_info->quantity;

		//Actualizo la cantidad con los nuevos agregados o extraidos
		if( $item_data['name'] === $cur_item_info->name ){
			$otherdb->where('item_id', $item_id);
			return $otherdb->update('items',$item_data);
		}

		return false;
	}

	/*
	Updates multiple items at once
	*/
	function update_multiple($item_data,$item_ids)
	{
		$this->con->where_in('item_id',$item_ids);
		return $this->con->update('items',$item_data);
	}

	/*
	Deletes one item
	*/
	function delete($item_id)
	{
		$this->con->where('item_id', $item_id);
		return $this->con->update('items', array('deleted' => 1));
	}

	/*
	Deletes a list of items
	*/
	function delete_list($item_ids)
	{
		$this->con->where('is_locked',0);
		$this->con->where_in('item_id',$item_ids);
		return $this->con->update('items', array('deleted' => 1));
 	}

 	/*
	Get search suggestions to find items
	*/
	function get_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

		$this->con->from('items');
		$this->con->like('name', $search);
		$this->con->where('deleted',0);
		$this->con->order_by('name','asc');
		$by_name = $this->con->get();
		foreach($by_name->result() as $row)
		{
			$suggestions[]=$row->name;
		}

		$this->con->select('category');
		$this->con->from('items');
		$this->con->where('deleted',0);
		$this->con->distinct();
		$this->con->like('category', $search);
		$this->con->order_by('category', 'asc');
		$by_category = $this->con->get();
		foreach($by_category->result() as $row)
		{
			$suggestions[]=$row->category;
		}

		$this->con->from('items');
		$this->con->like('item_number', $search);
		$this->con->where('deleted',0);
		$this->con->order_by('item_number', 'asc');
		$by_item_number = $this->con->get();
		foreach($by_item_number->result() as $row)
		{
			$suggestions[]=$row->item_number;
		}


		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;

	}

	function suggest2($search = '',$is_ser=false){
		$this->con->from('items');
		$this->con->where('deleted',0);
		if ($is_ser) $this->con->where('item_id >',5);
		else $this->con->where('item_id >',0);
		$this->con->where("(item_id LIKE '%$search%' OR name LIKE '%$search%' OR category LIKE '%$search%' OR item_number LIKE '%$search%')");
		// $search = array('item_id' => $search, 'name' => $search, 'category' => $search, 'item_number' => $search);
		// $this->con->or_like($search);
		$this->con->order_by('name', 'asc');
		$query = $this->con->get();

		if ($query->num_rows() > 0) {
			// return $this->con->last_query();
			return $query;
		}

		return false;
	}

	function get_item_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

		$this->con->from('items');
		$this->con->where('deleted',0);
		$this->con->where('item_id >',0);
		$this->con->like('name', $search);
		$this->con->or_like('description', $search);
		$this->con->order_by('name', 'asc');
		$this->con->limit($limit);
		$by_name = $this->con->get();
		foreach($by_name->result() as $row)
		{
			$suggestions[]=$row->item_id.'|'.$row->name;
		}

		$this->con->from('items');
		$this->con->where('deleted',0);
		$this->con->where('item_id >',0);
		$this->con->like('item_number', $search);
		$this->con->order_by('item_number', 'asc');
		$this->con->limit($limit);
		$by_item_number = $this->con->get();
		foreach($by_item_number->result() as $row)
		{
			$suggestions[]=$row->item_id.'|'.$row->item_number;
		}

		return $suggestions;

	}

	function get_category_suggestions($search)
	{
		$suggestions = array();
		$this->con->distinct();
		$this->con->select('category');
		$this->con->from('items');
		$this->con->like('category', $search);
		$this->con->where('deleted', 0);
		$this->con->order_by('category', 'asc');
		$by_category = $this->con->get();
		foreach($by_category->result() as $row)
		{
			$suggestions[]=$row->category;
		}

		return $suggestions;
	}

	/*
	Preform a search on items
	*/
	function search($search)
	{
		$this->con->from('items');
		$this->con->where("(name LIKE '%".$this->con->escape_like_str($search)."%' or
		item_number LIKE '%".$this->con->escape_like_str($search)."%' or
		category LIKE '%".$this->con->escape_like_str($search)."%') and deleted=0");
		$this->con->order_by('name', 'asc');
		return $this->con->get();
	}

	function get_categories()
	{
		$this->con->select('category');
		$this->con->from('items');
		$this->con->where('deleted',0);
		$this->con->distinct();
		$this->con->order_by('category', 'asc');

		return $this->con->get();
	}

	function report_broken($item_id=-1){
		if($item_id>=0){
			$item_info = $this->Item->get_info($item_id);
			if($item_info->quantity>0){
				$data = array(
					'quantity'=>$item_info->quantity - 1,
					'broken_quantity'=>$item_info->broken_quantity + 1
				);
				$this->con->where( array('item_id'=>$item_id) );
				return $this->con->update('items', $data);
			}

		}

		return false;
	}
}
?>
