<?php
require_once ("secure_area.php");
class Home extends Secure_area
{
	function __construct()
	{
		parent::__construct();
	}

	function index(){ 
		$this->load->view("home"); 
	}

	function confirm_user($url='', $title='', $width=600, $height=430){
		if (!$this->input->post('password')) {
			$data['fastUser'] = $this->Employee->get_logged_in_employee_info()->username;
			$data['url'] = 'index.php/'.str_replace('-', '/', $url)."/width:$width/height:$height";
			if ($url == '') {
				$data['url'] = $url;	
			}

			$data['title'] = $title;
			$data['ajax'] = 0;
			if ($width == 0 && $height == 0 && $url!='') $data['ajax'] = 1;

			$this->load->view('confirm_user', $data);
		}else{
			$response = array('success'=>false,'message'=>$this->lang->line('common_wrong_password'));

			$username = $this->Employee->get_logged_in_employee_info()->username;
			$password = $this->input->post("password");
			$response['ajax'] = (bool) $this->input->post("ajax");
			if($this->Employee->login($username,$password)) $response['success'] = true;

			die(json_encode($response));
		}
	}

	function logout($otherUser = ''){
		$this->Employee->logout($otherUser);
	}
}
?>
