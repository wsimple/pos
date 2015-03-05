<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ModelTracking extends CI_Model {
	private $last_id;

	public function __construct()
	{
		parent::__construct();
		$this->load->database();	
		$this->last_id = '';	
	}

	public function insert($data)
	{
		$this->db->insert('ospos_service_log',$data);
		$this->last_id = $this->db->insert_id();
	}

	public function get_last_id(){
		return $this->last_id;
	}

	public function get_grid($where="",$limit=" LIMIT 12")
	{
		$query = $this->db->query("
			SELECT 
				a.service_id AS service_id,
				(select b.brand_name from ospos_brand b where a.brand_id=b.brand_id) AS make,
				a.model_device AS model,
				a.color AS color,
				(select concat(c.first_name, ' ', c.last_name) from ospos_people c where c.person_id = a.person_id ) AS customer,
				a.date_received AS date01,
				a.date_delivered AS date02,
				a.status AS status,
				a.problem AS problem
			FROM ospos_service_log a
			$where 		
			ORDER BY a.date_received DESC
			$limit
		");
		return $query->result_array();
	}

}

?>