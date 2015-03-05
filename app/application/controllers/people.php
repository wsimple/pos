<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class People extends CI_Controller {

	private $data;

	public function __construct()
	{
		parent::__construct();
		$this->data = array();
		$this->load->model('ModelPeople');
	}

	public function complete($value){
		$customers = $this->ModelPeople->get_seek(" WHERE b.deleted = '0' AND (a.first_name LIKE '%".$value."%' OR a.last_name LIKE '%".$value."%' OR a.email LIKE '%".$value."%') ");
		$i = '';
		foreach ($customers as $array){
			$this->data[]['name'] = formatString($array['first_name'].' '.$array['last_name']).' - '.$array['email'];
		}
		echo json_encode($this->data);
	}
}

?>