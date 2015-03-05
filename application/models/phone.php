<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Phone extends CI_Model {

	public $con;

	public function __construct()
	{
		parent::__construct();
	}

	public function exists_brand($brand_name = ''){
		$this->db->from('brand_phone');
		$this->db->where('brand_name', $brand_name);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() == 1) {
			return $query->get()->row()->brand_id;
		}

		return false;
	}

	public function exists_model($model_name = ''){
		$this->db->from('model_phone');
		$this->db->where('model_name', $model_name);
		$this->db->limit(1);
		$query = $this->db->get();
		if ($query->num_rows() == 1) {
			return true;
		}

		return false;
	}

	public function get_phone($model_id = 0){
		$this->db->from('model_phone');
		$this->db->join('brand_phone', 'model_phone.brand_id = brand_phone.brand_id');
		$this->db->where('model_phone.model_id', $model_id);
		$this->db->limit(1);
		return $this->db->get();
	}

	public function save_brand($brand_data){
		if (!$phone_id = $this->exists_brand($brand_data['name'])) {
			$this->db->insert('brand_phone', $brand_data);
			return $this->db->insert_id();
		}else{
			// update
		}
		return false;
	}

	public function save_model($model_data, $brand_id){
		$model_data['brand_id'] = $brand_id;

		if ( !$brand_id || !$this->exists_model($model_name['model_name']) ) {
			$this->db->insert('model_phone', $model_data);
			return $this->db->insert_id();
		}else{
			//update
		}

		return false;
	}

	public function suggest($search){
		$this->db->from('brand_phone');
		$this->db->join('model_phone', 'brand_phone.brand_id = model_phone.brand_id');
		$this->db->where("CONCAT(model_name, ' ', brand_name) LIKE '%".$search."%'");
		return $this->db->get();
	}

}

/* End of file phone.php */
/* Location: ./application/models/phone.php */