<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ModelPhoneModel extends CI_Model {

	public function __construct()
	{
		parent::__construct();
		$this->load->database();		
	}

	public function get_seek($where="",$limit=" LIMIT 12")
	{
		$query = $this->db->query("
			SELECT 
				model_id AS id,
				model_name AS name
			FROM ospos_model
			$where 		
			GROUP BY model_id, model_name
			ORDER BY model_name
			$limit
		");
		return $query->result_array();
	}

	public function get_field($field, $where){
		$query = $this->db->query("SELECT $field FROM ospos_model $where LIMIT 1 ");
		$array = $query->row();
		return $array->$field;
	}

	public function getRows($where='', $limit='', $order=' ORDER BY model_name')
	{
		$query = $this->db->query("
			SELECT * 
			FROM ospos_model 
			$where  
			$order 
			$limit 
		");
        return $query->result_array();
	}

		
}

?>