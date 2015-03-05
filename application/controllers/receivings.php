<?php
require_once ("secure_area.php");
class Receivings extends Secure_area
{
	function __construct()
	{
		parent::__construct('receivings');
		$this->load->library('receiving_lib');
	}

	function index($cart=0){
		$this->_reload(array(), $cart);
	}

	function item_search()
	{
		$suggestions = $this->Item->get_item_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions($this->input->post('q'),$this->input->post('limit')));
		echo implode("\n",$suggestions);
	}

	function supplier_search()
	{
		$suggestions = $this->Supplier->get_suppliers_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function select_supplier()
	{
		$supplier_id = $this->input->post("supplier");
		$this->receiving_lib->set_supplier($supplier_id);
		$this->_reload();
	}

	function change_mode()
	{
		$mode = $this->input->post("mode");
		$this->receiving_lib->set_mode($mode);
		$this->_reload();
	}

	function add()
	{
		$data=array();
		$mode = $this->receiving_lib->get_mode();
		$item_id_or_number_or_item_kit_or_receipt = $this->input->post("item");
		$quantity = $mode=="receive" ? 1:-1;

		if($this->receiving_lib->is_valid_receipt($item_id_or_number_or_item_kit_or_receipt) && $mode=='return')
		{
			$this->receiving_lib->return_entire_receiving($item_id_or_number_or_item_kit_or_receipt);
		}
		elseif($this->receiving_lib->is_valid_item_kit($item_id_or_number_or_item_kit_or_receipt))
		{
			$this->receiving_lib->add_item_kit($item_id_or_number_or_item_kit_or_receipt);
		}
		elseif(!$this->receiving_lib->add_item($item_id_or_number_or_item_kit_or_receipt,$quantity))
		{
			$data['error']=$this->lang->line('recvs_unable_to_add_item');
		}
		$this->_reload($data);
	}

	function edit_item($item_id)
	{
		$data= array();

		$this->form_validation->set_rules('price', 'lang:items_price', 'required|numeric');
		$this->form_validation->set_rules('quantity', 'lang:items_quantity', 'required|integer');
		$this->form_validation->set_rules('discount', 'lang:items_discount', 'required|integer');

    	$description = $this->input->post("description");
    	$serialnumber = $this->input->post("serialnumber");
		$price = $this->input->post("price");
		$quantity = $this->input->post("quantity");
		$discount = $this->input->post("discount");

		if ($this->form_validation->run() != FALSE)
		{
			$this->receiving_lib->edit_item($item_id,$description,$serialnumber,$quantity,$discount,$price);
		}
		else
		{
			$data['error']=$this->lang->line('recvs_error_editing_item');
		}

		$this->_reload($data);
	}

	function delete_item($item_number)
	{
		$this->receiving_lib->delete_item($item_number);
		$this->_reload();
	}

	function delete_supplier()
	{
		$this->receiving_lib->delete_supplier();
		$this->_reload();
	}

	function complete($other=0)
	{ 
		$this->load->model('Transfers');
		if ($this->session->userdata('from_rece_t')) $other=$this->session->userdata('from_rece_t');
		$this->Transfers->complete_reception($other);

		$data['cart']=$this->receiving_lib->get_cart();
		$data['total']=$this->receiving_lib->get_total();
		$data['receipt_title']=$this->lang->line('recvs_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a');
		$supplier_id=$this->receiving_lib->get_supplier();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$comment = $this->input->post('comment');
		$emp_info=$this->Employee->get_info($employee_id);
		$payment_type = $this->input->post('payment_type');
		$data['payment_type']=$this->input->post('payment_type');

		if ($this->input->post('amount_tendered'))
		{
			$data['amount_tendered'] = $this->input->post('amount_tendered');
			$data['amount_change'] = to_currency($data['amount_tendered'] - round($data['total'], 2));
		}else $data['amount_tendered']=0;
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($supplier_id!=-1)
		{
			$suppl_info=$this->Supplier->get_info($supplier_id);
			$data['supplier']=$suppl_info->first_name.' '.$suppl_info->last_name;
		}

		//SAVE receiving to database
		$data['receiving_id']='RECV '.$this->Receiving->save($data['cart'], $supplier_id,$employee_id,$comment,$payment_type,$data['amount_tendered']);
		
		if ($data['receiving_id'] == 'RECV -1')
		{
			$data['error_message'] = $this->lang->line('receivings_transaction_failed');
		}

		$data['preload'] = $other;
		$this->load->view("receivings/receipt",$data);
		$this->receiving_lib->clear_all();
	}

	function receipt($receiving_id)
	{
		$receiving_info = $this->Receiving->get_info($receiving_id)->row_array();
		$this->receiving_lib->copy_entire_receiving($receiving_id);
		$data['cart']=$this->receiving_lib->get_cart();
		$data['total']=$this->receiving_lib->get_total();
		$data['receipt_title']=$this->lang->line('recvs_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a', strtotime($receiving_info['receiving_time']));
		$supplier_id=$this->receiving_lib->get_supplier();
		$emp_info=$this->Employee->get_info($receiving_info['employee_id']);
		$data['payment_type']=$receiving_info['payment_type'];

		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($supplier_id!=-1)
		{
			$supplier_info=$this->Supplier->get_info($supplier_id);
			$data['supplier']=$supplier_info->first_name.' '.$supplier_info->last_name;
		}
		$data['preload'] = $receiving_id;
		$data['receiving_id']='RECV '.$receiving_id;
		$this->load->view("receivings/receipt",$data);
		$this->receiving_lib->clear_all();

	}

	function _reload($data=array())
	{
		$cart = $this->input->post('reception');
		$this->load->model('Transfers');
		$person_info = $this->Employee->get_logged_in_employee_info();
		$data_receive['cart']=$this->receiving_lib->get_cart($cart);
		$data_receive['modes']=array('receive'=>$this->lang->line('recvs_receiving'),'return'=>$this->lang->line('recvs_return'));
		$data_receive['mode']=$this->receiving_lib->get_mode();
		$data_receive['total']=$this->receiving_lib->get_total();
		$data_receive['items_module_allowed'] = $this->Employee->has_permission('items', $person_info->person_id);
		$data_receive['payment_options']=array(
			$this->lang->line('sales_cash') => $this->lang->line('sales_cash'),
			$this->lang->line('sales_check') => $this->lang->line('sales_check'),
			$this->lang->line('sales_debit') => $this->lang->line('sales_debit'),
			$this->lang->line('sales_credit') => $this->lang->line('sales_credit')
		);
		$data_receive['flag'] = $cart; //Solo para opciones de carros por transfers
		if ($cart>0 && count($data_receive['cart'])>0){
			$this->session->set_userdata('from_rece_t', $cart);
		}
		$supplier_id=$this->receiving_lib->get_supplier();
		if($supplier_id!=-1){
			$info=$this->Supplier->get_info($supplier_id);
			$data_receive['supplier']=$info->first_name.' '.$info->last_name;
		}
		$data_receive['reception_shipping'] = false;
		if( $cart > 0){
			if ($this->Transfers->available()) {
				$data_receive['reception_id'] = $cart;
				$data_receive['reception_shipping'] = true;
			}
		}
		$data['location'] = $this->session->userdata('dblocation');
		$data['cfilter']=0;	$data['sfilter']=0;$data['filters']=0;
		//validar filtro
		if ($this->input->post()!==false){
			//Tab2
			//Filtros
			$data['cfilter']=$this->input->post('filters');
			$data['sfilter']=$this->input->post('filter_status');
			//Filtro Avanzado
			$filters=$this->input->post('filter_other');
			$data['filters']=$filters;
			switch ($filters) {
				case 1: $filters = array('sender'=>$data['location']); break;
				case 2: $filters = array('receiver'=>$data['location']); break;			
			}
			$data['advance_filter']=$filters;
			$this->session->set_userdata('receiving_fil',$data);			
		}elseif($this->session->userdata('receiving_fil')){
			$data=$this->session->userdata('receiving_fil');
		}

		//Paginador
		$config['base_url'] = site_url('/receivings/index');
		$config['total_rows'] = $this->Transfers->count_all_reception($data);
		$config['per_page'] = '20';
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		//Enlaces paginador
		$data['links_shippings'] = $this->pagination->create_links();
		$data['data_shippings']=$this->Transfers->get_all_reception($data, $config['per_page'],$this->uri->segment($config['uri_segment']));
		$data['status'] = array($this->lang->line('orders_status1'),
								$this->lang->line('orders_status2')
								);

		$this->load->view("receivings/index",array('data_receive'=>$data_receive, 'data'=>$data));
	}

    function cancel_receiving(){
    	$this->session->unset_userdata('from_rece_t');
    	$this->receiving_lib->clear_all();
    	$this->_reload();
    }

}
?>