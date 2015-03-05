<?php
class Appconfig extends CI_Model 
{
	
	function __construct()
    {
        parent::__construct();
        //Seleccion de DB
        // $this->session->set_userdata(array('dblocation'=>'other'));
        $db = $this->session->userdata('dblocation');
        if($db)
            $this->db = $this->load->database($db, true);
    }
	function exists($key)
	{
		$this->db->from('app_config');	
		$this->db->where('app_config.key',$key);
		$query = $this->db->get();
		
		return ($query->num_rows()==1);
	}
	
	function get_all()
	{
		$this->db->from('app_config');
		$this->db->order_by("key", "asc");
		return $this->db->get();		
	}
	
	function get($key)
	{
		$query = $this->db->get_where('app_config', array('key' => $key), 1);
		
		if($query->num_rows()==1)
		{
			return $query->row()->value;
		}
		
		return "";
		
	}
	
	function save($key,$value)
	{
		$config_data=array(
		'key'=>$key,
		'value'=>$value
		);
				
		if (!$this->exists($key))
		{
			return $this->db->insert('app_config',$config_data);
		}
		
		$this->db->where('key', $key);
		return $this->db->update('app_config',$config_data);		
	}
	
	function batch_save($data)
	{
		$success=true;
		
		//Run these queries as a transaction, we want to make sure we do all or nothing
		$this->db->trans_start();
		foreach($data as $key=>$value)
		{
			if(!$this->save($key,$value))
			{
				$success=false;
				break;
			}
		}
		
		$this->db->trans_complete();		
		return $success;
		
	}
		
	function delete($key)
	{
		return $this->db->delete('app_config', array('key' => $key)); 
	}
	
	function delete_all()
	{
		return $this->db->empty_table('app_config'); 
	}

	function recoverAll($data){
		$b = false;
		$db_debug = $this->db->db_debug;
		$this->db->db_debug = FALSE;
		if (is_array($data)){
			foreach ($data as $key) {
				if ($key!=''){
					$this->db->query($key);
					if ($this->db->_error_number()!=0){
						$this->db->db_debug = $db_debug;
						return false;
					}	
				} 	
			}
			$b = true;
		}

		$this->db->db_debug = $db_debug;
	 	return $b;
	}
}
?>