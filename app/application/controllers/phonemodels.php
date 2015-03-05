<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class PhoneModels extends CI_Controller {

	private $data;

	public function __construct()
	{
		parent::__construct();
		$this->data = array();
		$this->load->model('ModelPhoneModel');
	}

	public function complete($value){
		$customers = $this->ModelPhoneModel->get_seek(" WHERE model_name LIKE '%".$value."%' ");
		$i = '';
		foreach ($customers as $array){

			$this->data[]['name'] = 'Code: '.$array['id'].', Model: '.formatString($array['name']);
			
		}
		echo json_encode($this->data);
	}
}

?>