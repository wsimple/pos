<?php
class Login extends CI_Controller
{
	function __construct()
	{
		parent::__construct();
		if ($this->input->post('locationbd')) {
			$this->session->set_userdata('dblocation', $this->input->post('locationbd'));
		}elseif($this->session->userdata('dblocation')){
			$this->session->set_userdata('dblocation', $this->session->userdata('dblocation'));
		}else{
			$this->session->set_userdata('dblocation', 'default');
		}
	}

	function index($userId='')
	{	
		if($this->Employee->is_logged_in())
		{
			redirect('home');
		}
		else
		{
			$this->form_validation->set_rules('username', 'lang:login_undername', 'callback_login_check');
    	    $this->form_validation->set_error_delimiters('<div class="error">', '</div>');

			if($this->form_validation->run() == FALSE)
			{
				//Para cambio rapido de usuario(LOGOUT alternativo)
				$data['fastUser'] = '';
				if($userId != ''){
					$person = $this->Employee->get_info( $userId );
					$data['fastUser'] = $person->username;
				}
				//echo $this->input->post('locationbd');
				//echo '<pre>'; print_r($data); echo '</pre>';
				$this->load->view('login', $data);
			}
			else
			{
				//MArco su hora de entrada automaticamente
				$this->Employee->open_day($this->Employee->get_logged_in_employee_info()->person_id);
				//Redirecciono al inventario y no al home para comparar inventario actual con entrega
				redirect('home');
			}
		}
	}

	function login_check($username)
	{
		$password = $this->input->post("password");
		$response = $this->Employee->login($username,$password);
		if(!$response)
		{
			if ($response === 0) {//Mensaje En caso de estar fuera de horario
				$this->form_validation->set_message('login_check', $this->lang->line('login_invalid_time'));
			}elseif(!$response){//Mensaje en caso de datos invalidos
				$this->form_validation->set_message('login_check', $this->lang->line('login_invalid_username_and_password'));
			}
			return false;
		}

		return true;
	}
}
?>
