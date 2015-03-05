<?php
require_once ("secure_area.php");
class Config extends Secure_area 
{
	function __construct(){
		parent::__construct('config');
	}
	
	function index(){
		$this->load->view("config");
	}

	function save(){
		//carga de imagen
		$config['upload_path'] = './images/';
		$config['allowed_types'] = 'gif|jpg|png';
		$config['max_size']	= '1000000';
		$config['max_width']  = '3600';
		$config['max_height']  = '2800';
		$this->load->library('upload', $config);
		$this->upload->initialize($config);
		//fin carga imagen		

		try { 
			$nameLogo['file_name']='';
			if ($this->upload->do_upload('logo')) {
				$nameLogo = $this->upload->data();
				$data = array('upload_status' => 1,'upload_message' => $nameLogo['file_name']);
				if($this->input->post('logo_name')!='logo.png'){
					unlink('./images/'.$this->input->post('logo_name'));
				}
			}else{
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
			$configR['source_image']	= './images/'.$data['upload_message'];
			$configR['create_thumb'] = false;
			$configR['maintain_ratio'] = false;
			$configR['width']	 = 155;
			$configR['height']	= 80;
			$this->load->library('image_lib', $configR); 
			$this->image_lib->resize();
			//fin redimension
			$logo = $nameLogo['file_name'];
		}else{
			$logo = $this->input->post('logo_name');
			$data['upload_status']=1;
		}	
		$batch_save_data=array(
		'logo'=>$logo,
		'company'=>$this->input->post('company'),
		'address'=>$this->input->post('address'),
		'phone'=>$this->input->post('phone'),
		'email'=>$this->input->post('email'),
		'fax'=>$this->input->post('fax'),
		'website'=>$this->input->post('website'),
		'default_tax_1_rate'=>$this->input->post('default_tax_1_rate'),		
		'default_tax_1_name'=>$this->input->post('default_tax_1_name'),		
		'default_tax_2_rate'=>$this->input->post('default_tax_2_rate'),	
		'default_tax_2_name'=>$this->input->post('default_tax_2_name'),		
		'currency_symbol'=>$this->input->post('currency_symbol'),
		'return_policy'=>$this->input->post('return_policy'),
		'language'=>$this->input->post('language'),
		'timezone'=>$this->input->post('timezone'),
		'print_after_sale'=>$this->input->post('print_after_sale'),
		'alert_after_sale'=>$this->input->post('alert_after_sale'),
		'default_service'=>$this->input->post('default_service'),
		'default_item_percentage'=>$this->input->post('default_item_percentage')
		);
		//echo json_encode($batch_save_data);

		switch ($data['upload_status']) {
			case '-1':case '0':
				echo json_encode(array('x'=>$data['upload_status'],'success'=>false,'message'=>$data['upload_message']));
				break;
			case '1':case '2':
					if( $this->Appconfig->batch_save( $batch_save_data ) )
					{
						$item_data = array(
						'cost_price'=>$batch_save_data['default_service'],
						'unit_price'=>$batch_save_data['default_service']
						);
						$this->Item->save($item_data,3);
						echo json_encode(array('success'=>true,'Upstatus'=>$data['upload_status'],'data'=>$data,'message'=>$this->lang->line('config_saved_successfully')));
					}
				break;
		}		
	}
}
?>