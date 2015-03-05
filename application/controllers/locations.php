<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once ("secure_area.php");
class Locations extends Secure_area {

	public function __construct()
	{
		parent::__construct('locations');
	}

	public function index()
	{
		$data['title'] = $this->lang->line('location_title'); 
		$data['locations'] = $this->Location->get_all_locations();
		$data['controller_name']=strtolower(get_class());

		$this->load->view('location/manage', $data);
	}

	public function view($location_id = 0){
		$data['data'] = $this->Location->get_location($location_id);
		$data['dbdrivers'] = array('mysql'=>'mysql','mysqli'=>'mysqli','postgre'=>'postgre','odbc'=>'odbc','sqlite'=>'sqlite','oci8'=>'oci8');

		$this->load->view('location/form', $data);
	}

	public function save($location_id=0){
		$location_id = ($this->input->post('id')) ? $this->input->post('id') : 0;
		$location_data = array(
		'name'=>$this->input->post('location'),
		'hostname'=>$this->input->post('hostname'),
		'username'=>$this->input->post('username'),
		'password'=>$this->input->post('password'), 
		'dbdriver'=>$this->input->post('dbdriver'),
		'active'=>$this->input->post('active')
		);

		if ($location_id <= 0) { //Solo si es insert
			$location_data['database']=$this->input->post('database');
		}

		$response = $this->Location->save($location_data,$location_id);
		if(is_bool($response) === true)
		{
			if ($response) { //Locacion previa
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('location_updated'),'location_id'=>$location_id));
			}else{
				echo json_encode(array('success'=>false,'message'=>$this->lang->line('location_no_updated'),'location_id'=>$location_id));
			}
		}else{//New location
			if ($response > 0) {
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('location_created'),'location_id'=>$location_id));
			}elseif($response == 0){
				echo json_encode(array('success'=>false,'message'=>$this->lang->line('location_connection_error'),'location_id'=>$response));
			}elseif($response == -1){
				echo json_encode(array('success'=>false,'message'=>$this->lang->line('location_create_error'),'location_id'=>$response));
			}elseif($response == -2){
				echo json_encode(array('success'=>false,'message'=>$this->lang->line('location_create_db_error'),'location_id'=>$response));
			}
		}
	}

	public function enable(){
		$this->Location->toggle_enable( $this->input->post('locations'), 1);
		redirect('locations');
	}

	public function disable(){
		$this->Location->toggle_enable( $this->input->post('locations'), 0);
		redirect('locations');
	}

	function search()
	{
		$search=$this->input->post('search');
		$data_rows=get_locations_manage_table_data_rows($this->Location->search($search),$this);
		echo $data_rows;
	}

	function suggest()
	{
		$suggestions = $this->Location->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}
}

/* End of file locations.php */
/* Location: ./application/controllers/locations.php */