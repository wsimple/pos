<?php
require_once ("secure_area.php");
require_once ("interfaces/idata_controller.php");
class Giftcards extends Secure_area implements iData_controller
{
	function __construct()
	{
		parent::__construct('giftcards');
	}

	function index()
	{
		$config['base_url'] = site_url('/giftcards/index');
		$config['total_rows'] = $this->Giftcard->count_all();
		$config['per_page'] = '20';
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();
		$data['form_height']=$this->get_form_height();
		$data['manage_table']=get_giftcards_manage_table( $this->Giftcard->get_all( $config['per_page'], $this->uri->segment( $config['uri_segment'] ) ), $this );
		// $data['access'] = $this->Employee->permission_privileges('giftcards', 1);
		$this->load->view('giftcards/manage',$data);
	}

	function search()
	{
		$search=$this->input->post('search');
		$data_rows=get_giftcards_manage_table_data_rows($this->Giftcard->search($search),$this);
		echo $data_rows;
	}

	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		$suggestions = $this->Giftcard->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function get_row()
	{
		$giftcard_id = $this->input->post('row_id');
		$data_row=get_giftcard_data_row($this->Giftcard->get_info($giftcard_id),$this);
		echo $data_row;
	}

	function view($giftcard_id=-1)
	{
		$data['seller']=($giftcard_id=='sale');
		if($data['seller']){
			//alguna validacion para vendedores, si es requerido
			$giftcard_id=-1;
		}
		$data['giftcard_info']=$this->Giftcard->get_info($giftcard_id);

		$this->load->view("giftcards/form",$data);
	}
	
	function save($giftcard_id=false)
	{
		$selling=!!$this->input->post('giftcard_sale');//detectar si es una venta
		$value=$this->input->post('value');
		$giftcard_number=$this->input->post('giftcard_number');
		$giftcard_data=array('giftcard_number'=>$giftcard_number);
		//se le agrega el valor si no es una venta
		if(!$selling) $giftcard_data['value']=$value;
		$giftcard_id=$this->Giftcard->save($giftcard_data,$giftcard_id,$selling);
		if($giftcard_id){
			if($selling){
				$this->load->library('Sale_lib');
				$this->sale_lib->add_item(-1,1,0,$value,null,$giftcard_number);//venta de giftcard
			}
			//New giftcard
			if(isset($giftcard_data['new']))
			{
				$success=$this->lang->line('giftcards_successful_adding');
			}
			else //previous giftcard
			{
				$success=$this->lang->line('giftcards_successful_updating');
			}
			echo json_encode(array(
				'success'=>true,
				'message'=>$success.' '.$giftcard_data['giftcard_number'],
				'giftcard_id'=>$giftcard_id
			));
		}
		else//failure
		{
			echo json_encode(array(
				'success'=>false,
				'message'=>$this->lang->line('giftcards_error_adding_updating').' '.$giftcard_data['giftcard_number'],
				'giftcard_id'=>false
			));
		}

	}

	function delete()
	{
		$giftcards_to_delete=$this->input->post('ids');

		if($this->Giftcard->delete_list($giftcards_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('giftcards_successful_deleted').' '.
			count($giftcards_to_delete).' '.$this->lang->line('giftcards_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('giftcards_cannot_be_deleted')));
		}
	}
		
	/*
	get the width for the add/edit form
	*/
	function get_form_width()
	{
		return 360;
	}
	function get_form_height()
	{
		return 200;
	}
}
?>