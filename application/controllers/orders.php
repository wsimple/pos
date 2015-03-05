<?php
require_once ('secure_area.php');
class Orders extends Secure_area
{
	function __construct()
	{
		parent::__construct('orders');
		$this->load->library('order_lib');
		$this->load->model('Order');
	}

	function index($low_stock=false)
	{
		//Tab 1
		if ($low_stock) {
			$this->load->model('reports/Inventory_low');
			if (!$this->order_lib->get_cart()) {	
				$model = $this->Inventory_low;
				$low_items = $model->getData(array(),true);
				foreach ($low_items as $key) {
					$this->order_lib->add_item($key['item_id']);
				}
			}
		}
		$data['cart'] = $this->order_lib->get_cart();

		//Tab 2
		//validar filtro
		$data['filters']='';$data['filter_status']='';$data['filter_location']='';
		if ($this->input->post()!==false){
			$data['filters']=$this->input->post('filters');
			$data['filter_location']=$this->input->post('filter_location');
			$data['filter_status']=$this->input->post('filter_status');
			$this->session->set_userdata('orders_fil',$data);			
		}elseif($this->session->userdata('orders_fil')){
			$temp=$data['cart'];
			$data=$this->session->userdata('orders_fil');
			$data['cart']=$temp;
		}

		$this->load->model('Location');
		$data['location_lists'] = $this->Location->get_select_option_list(false,true);
		$data['location_lists']=array_merge(array(''=>'Location'),$data['location_lists']);
		$data['location_lists']['default']='Principal';
		$data['location'] = $this->session->userdata('dblocation');
				
		// paginador 
		$config['base_url'] = site_url('/orders/index');
		$config['total_rows'] = $this->Order->count_all($data);
		$config['per_page'] = '20';
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		//Paginator Links
		$data['links_orders'] = $this->pagination->create_links();
		$data['data_orders']=$this->Order->get_all($data,$config['per_page'],$this->uri->segment($config['uri_segment']));
		$data['status'] = array($this->lang->line('orders_status1'),$this->lang->line('orders_status2'));

		$this->load->view('orders/register',$data);
	}

	function item_search()
	{
		$suggestions = $this->Item->get_item_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions($this->input->post('q'),$this->input->post('limit')));
		echo implode("\n",$suggestions);
	}

	function set_comment() 
	{
		$this->sale_lib->set_comment($this->input->post('comment'));
	}

	function add()
	{
		$id = $this->input->get('item');
		$response = array('status'=>false, 'message'=>'Error');
		$response['status'] = $this->order_lib->add_item($id);
		if ($response['status']) {
			$response['message'] = 'Added';
		}

		die(json_encode($response));
	}

	function delete_item($item_number)
	{
		$response = array('status'=>true,'messagge'=>$this->lang->line('orders_delete_item'));
		$this->order_lib->delete_item($item_number);
		die(json_encode($response));
	}

	function save($sale_id = false)
	{
		$response = array('status'=>false,'message'=>$this->lang->line('orders_no_save'));
		$order_data['date'] = date('Y-m-d');
		$order_data['employee_id'] = $this->Employee->get_logged_in_employee_info()->person_id;
		$order_data['comments'] = $this->input->post('comments');
		$order_data['location'] = $this->session->userdata('dblocation');

		$response['status'] = $this->Order->save($order_data, $this->input->post('items'));
		if ($response['status']) {
			$response['message'] = $this->lang->line('orders_saved');
			$this->order_lib->clear_all();
		}

		die(json_encode($response));
	}

	function cancel($order_id){
		$response = array('status'=>false, 'message'=>'Nose a borrado');
		if ($this->Order->cancel($order_id)) {
			$response['message'] = 'Orden Cancelada';
		}
		die( json_encode($response) );
	}

	function cancel_order()
	{
		$this->order_lib->clear_all();
		redirect('orders');
	}

	function modqty(){
		if ($this->input->is_ajax_request()) {
			$this->load->view('orders/form');
		}
	}
	function check_availability($id_order){
		$this->load->model('Order');
		$items=$this->Order->check_availability($id_order);
		if ($items===1) redirect('sales/pending_orders_to_shipping/'.$id_order);
		else redirect('reports/pending_orders/'.$id_order.'/1:'.$items);
	}
}
