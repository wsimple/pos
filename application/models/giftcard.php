<?php
class Giftcard extends CI_Model
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
	Determines if a given giftcard_id is an giftcard
	*/
	function exists( $giftcard_id, $giftcard_number=false )
	{
		$query=false;
		if($giftcard_id){
			$this->con->from('giftcards');
			$this->con->where('giftcard_id',$giftcard_id);
			$query = $this->con->get();
		}
		if(!$query||!$query->num_rows()>0){
			$this->con->from('giftcards');
			$this->con->where('giftcard_number',$giftcard_number);
			$query = $this->con->get();
		}
		return ($query->num_rows()>0)?$query:false;
	}

	/*
	Returns all the giftcards
	*/
	function get_all($limit=10000, $offset=0)
	{
		$this->con->from('giftcards');
		$this->con->where('deleted',0);
		$this->con->order_by("giftcard_number", "asc");
		$this->con->limit($limit);
		$this->con->offset($offset);
		return $this->con->get();
	}
	
	function count_all()
	{
		$this->con->from('giftcards');
		$this->con->where('deleted',0);
		return $this->con->count_all_results();
	}

	/*
	Gets information about a particular giftcard
	*/
	function get_info($giftcard_id)
	{
		$this->con->from('giftcards');
		$this->con->where('giftcard_id',$giftcard_id);
		$this->con->where('deleted',0);
		
		$query = $this->con->get();

		if($query->num_rows()==1)
		{
			return $query->row();
		}
		else
		{
			//Get empty base parent object, as $giftcard_id is NOT an giftcard
			$giftcard_obj=new stdClass();

			//Get all the fields from giftcards table
			$fields = $this->con->list_fields('giftcards');

			foreach ($fields as $field)
			{
				$giftcard_obj->$field='';
			}

			return $giftcard_obj;
		}
	}

	/*
	Get an giftcard id given an giftcard number
	*/
	function get_giftcard_id($giftcard_number)
	{
		$this->con->from('giftcards');
		$this->con->where('giftcard_number',$giftcard_number);
		$this->con->where('deleted',0);

		$query = $this->con->get();

		if($query->num_rows()==1)
		{
			return $query->row()->giftcard_id;
		}

		return false;
	}

	/*
	Gets information about multiple giftcards
	*/
	function get_multiple_info($giftcard_ids)
	{
		$this->con->from('giftcards');
		$this->con->where_in('giftcard_id',$giftcard_ids);
		$this->con->where('deleted',0);
		$this->con->order_by("giftcard_number", "asc");
		return $this->con->get();
	}

	/*
	Inserts or updates a giftcard
	*/
	function save(&$giftcard_data,$giftcard_id=false,$add=false)
	{
		$update=false;
		//si no se envia value, solo se crean giftcards nuevas o se restauran borradas
		$value=isset($giftcard_data['value']);
		if($query=$this->exists($giftcard_id,$giftcard_data['giftcard_number'])){
			$giftcard_id=false;
			$row=current($query->result());
			$giftcard_id=$row->giftcard_id;
			if($row->deleted){//la restauramos si fue borrada
				$update=true;
				$giftcard_data['deleted']=0;
				$row->value=0;
			}
			if(!$value) $giftcard_data['value']=0;
			//si es adición, se le suma el valor actual
			if($add) $giftcard_data['value']+=$row->value;
			//si ha cambiado el valor lo actualizamos
			if($giftcard_data['value']!=$row->value) $update=true;
		}else{
			$giftcard_id=false;
		}
		if(!$giftcard_id){//si no existe la intentamos crear
			if(!$value) $giftcard_data['value']=0;//si no hay valor la iniciamos en cero
			if($this->con->insert('giftcards',$giftcard_data)){
				$giftcard_id=$this->con->insert_id();
				$giftcard_data['new']=true;
			}
		}
		if(!$update) return $giftcard_id;
		$this->con->where('giftcard_id',$giftcard_id);
		return $this->con->update('giftcards',$giftcard_data)?$giftcard_id:false;
	}

	/*
	Updates multiple giftcards at once
	*/
	function update_multiple($giftcard_data,$giftcard_ids)
	{
		$this->con->where_in('giftcard_id',$giftcard_ids);
		return $this->con->update('giftcards',$giftcard_data);
	}

	/*
	Deletes one giftcard
	*/
	function delete($giftcard_id)
	{
		$this->con->where('giftcard_id', $giftcard_id);
		return $this->con->update('giftcards', array('deleted' => 1));
	}

	/*
	Deletes a list of giftcards
	*/
	function delete_list($giftcard_ids)
	{
		$this->con->where_in('giftcard_id',$giftcard_ids);
		return $this->con->update('giftcards', array('deleted' => 1));
 	}

 	/*
	Get search suggestions to find giftcards
	*/
	function get_search_suggestions($search,$limit=25)
	{
		$suggestions = array();

		$this->con->from('giftcards');
		$this->con->like('giftcard_number', $search);
		$this->con->where('deleted',0);
		$this->con->order_by("giftcard_number", "asc");
		$by_number = $this->con->get();
		foreach($by_number->result() as $row)
		{
			$suggestions[]=$row->giftcard_number;
		}

		//only return $limit suggestions
		if(count($suggestions > $limit))
		{
			$suggestions = array_slice($suggestions, 0,$limit);
		}
		return $suggestions;

	}

	/*
	Preform a search on giftcards
	*/
	function search($search)
	{
		$this->con->from('giftcards');
		$this->con->where("giftcard_number LIKE '%".$this->con->escape_like_str($search)."%' and deleted=0");
		$this->con->order_by("giftcard_number", "asc");
		return $this->con->get();	
	}
	
	public function get_giftcard_value( $giftcard_number )
	{
		if ( !$this->exists( $this->get_giftcard_id($giftcard_number)))
			return 0;
		
		$this->con->from('giftcards');
		$this->con->where('giftcard_number',$giftcard_number);
		return $this->con->get()->row()->value;
	}
	
	function update_giftcard_value( $giftcard_number, $value )
	{
		$this->con->where('giftcard_number', $giftcard_number);
		$this->con->update('giftcards', array('value' => $value));
	}
}
?>
