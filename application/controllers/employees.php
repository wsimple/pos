<?php
require_once ("person_controller.php");
class Employees extends Person_controller
{
	function __construct()
	{
		parent::__construct('employees');
	}

	function index()
	{
		$config['base_url'] = site_url('/employees/index');
		$config['total_rows'] = $this->Employee->count_all();
		$config['per_page'] = '20';
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);

		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['manage_table']=get_people_manage_table( $this->Employee->get_all( $config['per_page'], $this->uri->segment( $config['uri_segment'] ) ), $this );
		$this->load->view('people/manage',$data);
	}

	/*
	Returns employee table data rows. This will be called with AJAX.
	*/
	function search()
	{
		$search=$this->input->post('search');
		$data_rows=get_people_manage_table_data_rows($this->Employee->search($search),$this);
		echo $data_rows;
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest($minimum=false)
	{
		if ($minimum) {
			$suggestions = $this->Employee->get_search_suggestions_minimun($this->input->post('q'),$this->input->post('limit'));
		}else{
			$suggestions = $this->Employee->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		}
		echo implode("\n",$suggestions);
	}

	/*
	Loads the employee edit form
	*/
	function view($employee_id=-1)
	{
		$data['person_info']=$this->Employee->get_info($employee_id);
		// $data['person_schedule']=$this->Schedule->get_schedule($employee_id);
		$data['all_modules']=$this->Module->get_all_modules();
		$data['module_profiles']=$this->Employee->getProfile_Employee('',true);
		$this->load->view("employees/form",$data);
	}

	/*
	Inserts/updates an employee
	*/
	function save($employee_id=-1)
	{
		if ($employee_id==-1 && $this->Employee->exists('',$this->input->post('username'))) {
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('employees_username_exist')));
			return;
		}
		//Horario personal
		$days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
		$person_schedule = array();
		foreach ($days as $day) {
			$status = ($this->input->post( strtolower($day) )!='') ? true : false;
			if($status)
				$person_schedule[] = array('day'=>$day,'in'=>$this->input->post('in'.$day).':00:00','out'=>$this->input->post('out'.$day).':00:00');
		};

		$person_data = array(
			'first_name'=>$this->input->post('first_name'),
			'last_name'=>$this->input->post('last_name'),
			'email'=>$this->input->post('email'),
			'phone_number'=>$this->input->post('phone_number'),
			'address_1'=>$this->input->post('address_1'),
			'address_2'=>$this->input->post('address_2'),
			'city'=>$this->input->post('city'),
			'state'=>$this->input->post('state'),
			'zip'=>$this->input->post('zip'),
			'country'=>$this->input->post('country'),
			'comments'=>$this->input->post('comments')
		);
		$permission_data = $this->input->post("permissions")!=false ? $this->input->post("permissions"):array();
		$full_permission_data = array();
		foreach ($permission_data as $subpermission) {
			$subpermissions = $this->input->post($subpermission)!=false? $this->input->post($subpermission):array();
			$full_permission_data[$subpermission] = ( count($subpermissions) > 0 ) ? implode(',', $subpermissions) : 'none';
		}

		if ($employee_id==-1) $nameuser_emer=$this->input->post('username');
		else $nameuser_emer=$this->input->post('nameuser');
		//Password has been changed OR first time password set
		if($this->input->post('password')!=''){
			$employee_data=array(
			'username'=>$nameuser_emer,
			'password'=>md5($this->input->post('password')),
			'type_employees'=>$this->input->post('employee_profile_type')
			);
		}else{ //Password not changed
			$employee_data=array('username'=>$nameuser_emer,'type_employees'=>$this->input->post('employee_profile_type'));
		}
		if($id = $this->Employee->save($person_data,$employee_data,$full_permission_data,$employee_id))
		{	if ($employee_id!=-1){
				$id=$employee_id;
				$msg_post_subt=$this->lang->line('employees_successful_adding');
				if (file_exists('./images/employees/'.md5($id).'.jpg')) $a=true;
				else $a=false;
			}else{ $msg_post_subt=$this->lang->line('employees_successful_updating'); $a=false; }
			$tuId=md5($this->session->userdata('dblocation').'+'.$id);
			$dat=$this->uploadImagen_photo($tuId,$a);
			if($this->Schedule->save($person_schedule, $id)){
				echo json_encode(array('success'=>true,'message'=>$msg_post_subt.' '.$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>$id,'image'=>$dat));
			}
		}
		else//failure
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('employees_error_adding_updating').' '.
			$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>-1));
		}
	}

	/*
	This deletes employees from the employees table
	*/
	function delete()
	{
		$employees_to_delete=$this->input->post('ids');

		if($this->Employee->delete_list($employees_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('employees_successful_deleted').' '.
			count($employees_to_delete).' '.$this->lang->line('employees_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('employees_cannot_be_deleted')));
		}
	}
	/*
	get the width for the add/edit form
	*/
	function get_form_width(){ return 680; }

	function json_calendar($person_id=0){
		$year = date('Y');
		$month = date('m');
		$events_status = array('incomplete'=>'#CA1C1C', 'notwork'=>'#000', 'working'=>'#0063C6');
		$response = array();

		$month = $this->Employee->get_worked_days($person_id);

		foreach ($month->result() as $day) {
			$color = '';
			$week_day = $this->Employee->get_working_hours($person_id, $day->date);

			//Porcentage de horas trabajas
			$title = ceil(($day->total_segs_worked/$week_day->total_segs_work)*100).'%';
			if ($day->total_segs_worked < $week_day->total_segs_work) {
				$color = $events_status['incomplete'];
			}elseif ($title == '') {
				$color = $events_status['working'];
				$title = 'Working Now';
			}
			array_push($response, array(
				'title'=>$title.' of '.substr($week_day->total_hours, 0, -3).'Hrs',
				'start'=>$day->date,
				'color'=>$color
			));
		}

		echo json_encode($response);
	}

	function worked_report_inputs(){
		$months_worked = array();
		$person_id = $this->input->get('id');
		$year = (isset($_GET['year'])) ? $this->input->get('year') : 0 ;
		$months = $this->Employee->get_worked_details($person_id, $year);

		foreach ($months->result() as $data) {
			array_push($months_worked, array());
		}

		echo json_encode($months_worked);
	}

	function report(){
		if (isset( $_POST['submit'] )) {
			$employee_id= $this->input->post('search');
			$person = $this->Employee->get_info($employee_id);
			$data = array(
				'employee'=>$person->first_name.' '.$person->last_name,
				'employee_id'=>$employee_id,
				'year'=> $this->input->post('year'),
				'month'=> $this->input->post('month')
			);
			$this->load->view("reports/schedule", $data);
		}else{
			foreach (range(date('Y'), date('Y')-10) as $year) {
				$years[$year] = $year;
			}
			$data = array(
				'controller_name'=>'employees',
				'months_of_year'=> array('January','February','March','April','May','June','July','August','September','October','November','December'),
				'years'=>$years,
				'labels_attrib' => array('class'=>'required') 
			);
			$this->load->view("reports/date_input_schedule", $data);
		}
		
	}

	function assistance(){
		$data = array(
			'controller_name' => 'employees',
			'employees_working' => $this->Employee->get_all_working()
		);
		$this->load->view('employees/assistance', $data);
	}


	function open_day(){
		$username = $this->input->post('name');
		$password = $this->input->post('password');

		//Respuesta
		$response = array('message'=>'Wrong Password or Username, try Again!', 'status'=>0);

		$row = $this->Employee->login_($username, $password);
		if($row){
			if ($this->Employee->can_work($row->person_id)) {
				if ( $response['status'] = $this->Employee->open_day($row->person_id) ){
					$response['user'] = $row->person_id;
					if ($response['status'] == 1) {
						$response['message'] = $row->first_name.' '.$row->last_name;
					}elseif($response['status'] == 3){
						$response['message'] = $this->lang->line('employees_already_working');
					}
				}
			}else{
				$response['message'] = 'Out of Schedule';
				$response['status'] = -1;
			}
		}
		
		die(json_encode($response));
	}

	function close_day()
	{
		$response = array('status'=>0, 'message'=>'General Error, Try Again!');
		$person_id = $this->input->post('person_id');
		$password = $this->input->post('logoutpass');
		if ($this->Employee->can_logout($person_id, $password)) {
			if ( $this->Employee->close_day( $person_id ) ) {
				$response['status'] = 1;
				$response['message'] = 'Success Logout!';
			}
		}else{
			$response['status'] = -1;
			$response['message'] = 'Wrong Password or Username, try Again!';
		}

		die( json_encode($response) );
	}
	function profile_employee(){
		$data['profiles']=$this->Employee->getProfile_Employee();
		$data['controller_name']=strtolower(get_class());
		//$data['form_width']=$this->get_form_width();
		$this->load->view("employees/profiles",$data);
	}
	function form_profile_employee($per=-1){
		if ($per!=-1){ $data['profile']=$this->Employee->getProfile_Employee($per); }
		else $data['profile']='';
		$data['all_modules']=$this->Module->get_all_modules();
		$this->load->view("employees/profiles_form",$data);
	}
	function save_profile_employee($per=''){
		if ($per=='' && $this->Employee->exists_profile($this->input->post('name'))){
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('employees_profile_per_name_e')));
			return;
		}//pos-name
		if ($per==''){ $prodile_data = array('name'=>$this->input->post('name'),'new'=>true); }
		else{
			if (!$this->input->post('name') && $per=='administrator') $name_profile='administrator';
			else $name_profile=$this->input->post('name');
			$prodile_data = array('name'=>$name_profile,'new'=>false,'last'=>$this->input->post('pos-name')); }
		// $person_data = array('name'=>$this->input->post('name'));
		$permission_data = $this->input->post("permissions")!=false ? $this->input->post("permissions"):array();
		$full_permission_data = array();
		foreach ($permission_data as $subpermission) {
			$subpermissions = $this->input->post($subpermission)!=false? $this->input->post($subpermission):array();
			$full_permission_data[$subpermission] = ( count($subpermissions) > 0 ) ? implode(',', $subpermissions) : 'none';
		}
		if($this->Employee->create_profile($prodile_data,$full_permission_data)){
			//New employee
			if($per==''){
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('employees_successful_adding').' '.
				$prodile_data['name'],'person_id'=>$prodile_data['name']));
			}else{ 
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('employees_successful_updating').' '.$prodile_data['name'],'person_id'=>$per));
			}
		}else{ //failure
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('employees_error_adding_updating').' '.
			$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>-1));
		}
	}

	// function set_location($location='default'){
	// 	$location = strtolower($location);
	// 	$url = $this->uri->segment(1);
	// 	// echo urldecode($location);
	// 	$this->session->set_userdata('dblocation', urldecode($location));
	// 	redirect($url);
	// }
	function set_location(){
		$this->load->library('sale_lib');
		$location = $this->input->get_post('location');
		// echo $location;
		$url = $this->uri->segment(1);
		// echo urldecode($location);
		$this->session->set_userdata('dblocation', $location);
		$this->session->unset_userdata('items_location'); //Locacion secundaria en items
		$this->sale_lib->clear_all();					  //Borra ventas en sesion no terminadas
		redirect($url);
	}
	function uploadImagen_photo($id,$e){
		if ($this->input->post('photop')!=0){
			$codeBase64=$this->input->post('protocapture');
			if ($codeBase64 && $codeBase64!=""){
				$this->load->helper('base64');
				if ($e) rename('./images/employees/'.$id.".jpg",'./images/employees/temp_'.$id.".jpg");
				if (imagenBase64('employees/'.$id.".jpg",$codeBase64)){
					if ($e) unlink('./images/employees/temp_'.$id.".jpg"); 
					return true;
				}else{
					if ($e) rename('./images/employees/temp_'.$id.".jpg",'./images/employees/'.$id.".jpg");
					return false;
				}
			}return 'no imagen';
		}else{
			//carga de imagen
			$config['upload_path'] = './images/employees';
			$config['allowed_types'] = 'gif|jpg|png';
			$config['max_size']	= '1000000';
			$config['max_width']  = '3600';
			$config['max_height']  = '2800';
			$config['file_name']  = $id.".jpg";
			$this->load->library('upload', $config);
			$this->upload->initialize($config);
			try { 
				$nameLogo['file_name']='';
				if ($e) rename($config['upload_path'].'/'.$config['file_name'],$config['upload_path'].'/temp_'.$config['file_name']);
				if ($this->upload->do_upload('photo_e')) {
					$nameLogo = $this->upload->data();
					if ($e) unlink($config['upload_path'].'/temp_'.$config['file_name']);
					$data = array('upload_status' => 1,'upload_message' => $nameLogo['file_name']);
				}else{
					if ($e) rename($config['upload_path'].'/temp_'.$config['file_name'],$config['upload_path'].'/'.$config['file_name']);
					if ($this->upload->display_errors(true)=='1') {
						$data = array('upload_status' => 2,'upload_message' => 'vacio');
					}else{
						$data = array('upload_status' => 0,'upload_message' => $this->upload->display_errors(false,'',''));
					}
					
				}
			} catch (Exception $e) {
				$data = array('upload_status' => -1,'upload_message' => 'no se cargo');
				$nameLogo['file_name']='';
			}

			if ($nameLogo['file_name']!='') {
				//redimension
				$configR['image_library'] = 'gd2';
				$configR['source_image']	= $config['upload_path'].'/'.$config['file_name'];
				$configR['create_thumb'] = false;
				$configR['maintain_ratio'] = false;
				$configR['width']	 = 140;
				$configR['height']	= 140;
				$this->load->library('image_lib', $configR); 
				$this->image_lib->resize();
				//fin redimension
				// $logo = $nameLogo['file_name'];
			}
			// }else{ $logo = $this->input->post('logo_name'); }
			return $data;
		}
	}

}
?>
