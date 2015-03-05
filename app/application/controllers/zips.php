<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Zips extends CI_Controller {
	private $data;

	public function __construct()
	{
		parent::__construct();
		$this->data = array();
		$this->load->model('ModelZips');
	}

	public function complete($value){
		$customers = $this->ModelZips->get_seek(" WHERE a.state LIKE '%".$value."%' OR a.city LIKE '%".$value."%' OR a.zip LIKE '%".$value."%' ", "");
		$i = '';
		foreach ($customers as $array){
			$this->data[]['name'] = 'Country: '.formatString($array['country']).', State: '.formatString($array['state']).', City: '.formatString($array['city']).', Zip Code: '.formatString($array['zip']);	
		}
		echo json_encode($this->data);
	}

	public function get_cities($state, $city){
		$cities = $this->ModelZips->getRows(" WHERE state LIKE '".$state."' AND city LIKE '%".$city."%'", $limit='LIMIT 20', $order=' ORDER BY city', $group='GROUP BY city');
		$i = '';
		foreach ($cities as $array){
			$this->data[]['name'] = formatString($array['city']);	
		}
		echo json_encode($this->data);
	}
}

?>