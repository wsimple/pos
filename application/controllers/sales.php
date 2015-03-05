<?php
require_once ('secure_area.php');
class Sales extends Secure_area
{
	function __construct()
	{
		parent::__construct('sales');
		$this->load->library('sale_lib');
		$this->sale_lib->nameS=$this->sale_lib->get_mode().'_';
		$this->load->helper('report');
	}

	function index($force=''){
		$data_sale=array();
		if ($force!=''){
			switch ($force) {
				case 'sale': case 'return': case 'shipping': $this->sale_lib->set_mode($force); break;
				default: $force = $this->sale_lib->set_mode('sale'); break;
			}
		}
		//Customer
		$data_sale['customer']=$this->sale_lib->get_customer();
		if($data_sale['customer']!=-1){
			$info=$this->Customer->get_info($data_sale['customer']);
			$data_sale['customer_name']=$info->first_name.' '.$info->last_name;
			$data_sale['customer_email']=$info->email;
		}
		$data_sale['mode']=$this->sale_lib->get_mode();
		$this->sale_lib->nameS=$data_sale['mode'].'_';
		$this->load->model('Transfers');
		$person_info = $this->Employee->get_logged_in_employee_info();
		$employee = $this->Employee->get_info( $this->sale_lib->get_employee() );
		$data_sale['employee'] = $employee->first_name.' '.$employee->last_name;
		$data_sale['cart']=$this->sale_lib->get_cart();
		$data_sale['general_discount']=$this->sale_lib->get_discount();
		if ($this->session->userdata('discounting') != 'checked') {
			$data_sale['general_discount']=0;
		}
		if (count($data_sale['cart'])==0){
			$data_sale['taxing']= 'checked';
			$this->sale_lib->set_taxing(true);
		}
		$data_sale['modes']=array(
			'sale'=>$this->lang->line('sales_sale'),
			'return'=>$this->lang->line('sales_return')
		);
		$data_sale['subtotal']=$this->sale_lib->get_subtotal();
		$data_sale['taxes']=$this->sale_lib->get_taxes();
		
		//Se cobran taxes?
		$data_sale['taxing'] = '';
		if ($this->sale_lib->get_taxing()) {
			$data_sale['taxing']= 'checked';
		}

		$data_sale['total']=$this->sale_lib->get_total();
		$data_sale['items_module_allowed'] = $this->Employee->has_permission('items', $person_info->person_id);
		$data_sale['comment'] = $this->sale_lib->get_comment();
		$data_sale['email_receipt'] = $this->sale_lib->get_email_receipt();
		$data_sale['payments_total']=$this->sale_lib->get_payments_total();
		$data_sale['amount_due']=$this->sale_lib->get_amount_due();
		$data_sale['payments']=$this->sale_lib->get_payments(true);
		$data_sale['payment_options']=array(
			$this->lang->line('sales_cash') => $this->lang->line('sales_cash'),
			$this->lang->line('sales_check') => $this->lang->line('sales_check'),
			$this->lang->line('sales_giftcard') => $this->lang->line('sales_giftcard'),
			$this->lang->line('sales_debit') => $this->lang->line('sales_debit'),
			$this->lang->line('sales_credit') => $this->lang->line('sales_credit')
		);
		if ($data_sale['mode']=='sale' && $data_sale['customer']!=-1) {
			$lc = $this->Customer->get_info($data_sale['customer'])->max_amount_credit;
			if ($lc!='') {
				$data_sale['payment_options']['account'] = 'Account'; 
			} 
		}

		//Filtros
		$data_report['cfilter']=$this->input->post('filters');
		$data_report['sfilter']=$this->input->post('filter_status')?$this->input->post('filter_status'):0;
		//Fechas
		$dateurl = get_simple_date_ranges();
		$dateurl = array_keys($dateurl);
		$data_sale['fechas'] = $dateurl;
		if ($data_report['cfilter']) $data_report['all_time_date'] = $dateurl[$data_report['cfilter']-1];
		else $data_report['all_time_date'] = $dateurl[7];
		$data_sale['all_time_date'] = $data_report['all_time_date'];
		list($data_report['start_date'], $data_report['end_date']) = explode('/', $data_report['all_time_date']);

		// if ($data_sale['mode']=='shipping') {
		// 	// array_push($data['payment_options'], array('30_dias' => 'A 30 dias', '60_dias' => 'A 60 dias'));
		// 	$data['payment_options']['30_days'] = $this->lang->line('30_days'); 
		// 	$data['payment_options']['60_days'] = $this->lang->line('60_days');
		// 	$data['payment_options']['90_days'] = $this->lang->line('90_days');
		// 	// $data[$key] = $value;


		// 	// $data['payment_options'] = '30_dias => A 30 dias';
		// }

		//Tab 2
		$data_sale['payments_cover_total'] = $data_report['payments_cover_total'] = $this->_payments_cover_total();

		// $loca = urldecode($this->input->get('loc'));
		// if ($loca) $location=$loca;
		$_data['view']='reports/tabular_details';
		$this->load->model('reports/Detailed_sales');
		$model = $this->Detailed_sales;

		// $location='default';
		$this->Sale->con=$model->stabledb(false);
		$this->Sale->create_sales_items_temp_table();
		$report_data = $model->getData(array('start_date'=>$data_report['start_date'], 'end_date'=>$data_report['end_date'], 'sale_type' => $force));
		$summary_data = array();
		$details_data = array();
		foreach($report_data['summary'] as $key=>$row){
			$summary_data[] = array('POS '.$row['sale_id'], $row['sale_date'], $row['items_purchased'], $row['employee_name'], $row['customer_name'], to_currency($row['subtotal']), to_currency($row['total']), to_currency($row['tax']),to_currency($row['profit']), $row['payment_type'],anchor('sales/receipt/'.$row['sale_id'],$this->lang->line('employees_profile_see'), array('class'=>'big_button','target' => '_blank','style'=>'padding: 5px 7px;')));
			foreach($report_data['details'][$key] as $drow){
				$details_data[$key][] = array($drow['name'], $drow['category'], $drow['serialnumber'], $drow['description'], $drow['quantity_purchased'], to_currency($drow['subtotal']), to_currency($drow['total']), to_currency($drow['tax']),to_currency($drow['profit']), $drow['discount_percent'].'%');
			}
		}
		$data_re = array(
			"title" =>'',
			"subtitle" => '',
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"details_data" => $details_data,
			"location"=>'',
			"overall_summary_data" => $model->getSummaryData(array('start_date'=>$data_report['start_date'], 'end_date'=>$data_report['end_date'], 'sale_type' => $force)),
			"export_excel" => 0,
			'sale_type' => $force,
			'last'=>true,
			'cfilter'=>$data_report['cfilter'],
			'sfilter'=>$data_report['sfilter']
		);	
		$this->load->view('sales/index',array('register'=>$data_sale, 'list'=>$data_re));
	}

	/*Datos de la venta actual por ajax*/
	function get_ajax_sale_details(){
		$subtotal = $this->sale_lib->get_subtotal();
		$taxes = $this->sale_lib->get_taxes();
		$total = $this->sale_lib->get_total();
		$amountdue = $this->sale_lib->get_amount_due();

		echo json_encode(array('total'=>$total, 'subtotal'=>$subtotal, 'taxes'=>$taxes, 'due'=>$amountdue));
	}

	function item_search()
	{
		$suggestions = $this->Item->get_item_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		$suggestions = array_merge($suggestions, $this->Item_kit->get_item_kit_search_suggestions($this->input->post('q'),$this->input->post('limit')));
		echo implode("\n",$suggestions);
	}

	function customer_search()
	{
		$suggestions = $this->Customer->get_customer_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}

	function select_location(){
		if($this->sale_lib->get_mode()=='shipping'){
			$customer_id = $this->input->post('location');
		}
		$this->sale_lib->set_customer($customer_id);
	}

	function set_taxing(){
		$taxing = $this->input->get('taxing');
		$this->sale_lib->set_taxing($taxing);
		$this->_reload();
	}

	function set_discount(){
		$taxing = $this->input->get('customer_discount');
		$this->session->set_userdata( 'discounting',$taxing );
		$this->_reload();
	}

	function select_customer()
	{
		$customer_id = $this->input->post('customer');
		$this->sale_lib->set_customer($customer_id);
		$this->_reload();
	}

	function select_employee()
	{
		$employee_id = $this->input->post('employee');
		$this->sale_lib->set_employee($employee_id);
		$this->_reload();
	}

	function change_mode()
	{
		$mode = $this->input->post('mode');
		$this->sale_lib->set_mode($mode);
		$this->_reload();
	}

	function set_comment() 
	{
		$this->sale_lib->set_comment($this->input->post('comment'));
	}
	
	function set_email_receipt()
	{
		$this->sale_lib->set_email_receipt($this->input->post('email_receipt'));
	}

	//Alain Multiple Payments
	function add_payment($payment_type=null,$payment_amount=null){
		if ($payment_type===null && $payment_amount===null){
			$data=array(); $thifrom=true;
			$this->form_validation->set_rules('amount_tendered', 'lang:sales_amount_tendered', 'numeric');	
			if ($this->form_validation->run() == FALSE){
				if ( $this->input->post('payment_type') == $this->lang->line('sales_gift_card') )
					$data['error']=$this->lang->line('sales_must_enter_numeric_giftcard');
				else
					$data['error']=$this->lang->line('sales_must_enter_numeric');
				$this->_reload($data);
				return;
			}
			$payment_amount=$this->input->post('amount_tendered');
			$payment_type=str_replace(' ','_',$this->input->post('payment_type'));
		}else $payment_type=str_replace(' ','_',$payment_type);
		if ( $payment_type == str_replace(' ','_',$this->lang->line('sales_giftcard')) ){
			$payments = $this->sale_lib->get_payments();
			$payment_type=$payment_type.':'.$payment_amount;
			$current_payments_with_giftcard = isset($payments[$payment_type]) ? $payments[$payment_type]['payment_amount'] : 0;
			$cur_giftcard_value = $this->Giftcard->get_giftcard_value( $payment_amount );
			$total=$this->sale_lib->get_total();
			if ($cur_giftcard_value-$current_payments_with_giftcard <= 0 ){
				if (isset($thifrom)){
					$data['error']='Giftcard balance is '.to_currency($cur_giftcard_value).' !';
					$this->_reload($data);
					return;
				} 
			}elseif (($cur_giftcard_value-$total)>0){
				$data['warning']='Giftcard balance is '.to_currency($cur_giftcard_value-$total).' !';
			}
			$payment_amount=min($total,$cur_giftcard_value);
		}	
		$payment_type=str_replace('_',' ',$payment_type);
		if( !$this->sale_lib->add_payment( $payment_type, $payment_amount ) ){
			$data['error']='Unable to Add Payment! Please try again!';
		}
		if (isset($thifrom)) $this->_reload($data);
	}

	//Alain Multiple Payments
	function delete_payment( $payment_id )
	{
		$this->sale_lib->delete_payment( $payment_id );
		$this->_reload();
	}

	function add($item_id_or_number_or_item_kit_or_receipt=NULL,$service_id=NULL,$noreload=NULL)
	{
		$data=array();
		$mode = $this->sale_lib->get_mode();
		if(!$item_id_or_number_or_item_kit_or_receipt)
			$item_id_or_number_or_item_kit_or_receipt = $this->input->post('item');

		if(!$service_id) $service_id=$this->input->post('service');
		if(!$service_id) $service_id=NULL;
		elseif($service_id){
			$this->sale_lib->clear_all();
			$this->sale_lib->set_customer($this->input->post('customer_id'));
		} 

		$quantity = $mode!='return' ? 1:-1;

		if($this->sale_lib->is_valid_receipt($item_id_or_number_or_item_kit_or_receipt) && $mode=='return'){
			$this->sale_lib->return_entire_sale($item_id_or_number_or_item_kit_or_receipt);
		}elseif($this->sale_lib->is_valid_item_kit($item_id_or_number_or_item_kit_or_receipt)){
			$this->sale_lib->add_item_kit($item_id_or_number_or_item_kit_or_receipt);
		}elseif(!$this->sale_lib->add_item($item_id_or_number_or_item_kit_or_receipt,$quantity,0,null,null,null,$service_id)){
			$data['error']=$this->lang->line('sales_unable_to_add_item');
		}elseif($service_id){

			$array=$this->Service->get_items($service_id);
			foreach($array['all'] as $item){
				$this->sale_lib->add_item($item['id'],1,0,$item['price']);
			}
			$info=$this->Service->get_info($service_id);
			if ($info->add_pay && $info->add_pay!=''){
				$pays=explode(',',$info->add_pay);
				foreach ($pays as $key) {
					$pay=explode(' ',$key); $tipe=false;
					switch ($pay[0]) {
						case 'Cash': $tipe=$this->lang->line('sales_cash'); break;
						case 'Check': $tipe=$this->lang->line('sales_check'); break;
						case 'GiftCard': $tipe=$this->lang->line('sales_giftcard'); break;
						case 'DebitCard': $tipe=$this->lang->line('sales_debit'); break;
						case 'CreditCard': $tipe=$this->lang->line('sales_credit'); break;
					}
					if ($tipe!==false && $pay[1]!=''){
						$this->add_payment($tipe,$pay[1]);
					}
				}
			}
		}

		if($this->sale_lib->out_of_stock($item_id_or_number_or_item_kit_or_receipt)){
			$data['warning'] = $this->lang->line('sales_quantity_less_than_zero');
		}

		if (!$noreload) $this->_reload();
	}

	// function get_service_items($id){
	// 	var_dump($this->Service->get_id_items($id));
	// }
	function edit_item($line, $ajax=false)
	{
		$data= array();

		$this->form_validation->set_rules('price', 'lang:items_price', 'required|numeric');
		$this->form_validation->set_rules('quantity', 'lang:items_quantity', 'required|numeric');

		$description = $this->input->post('description');
		$serialnumber = $this->input->post('serialnumber');
		$price = $this->input->post('price');
		$quantity = $this->input->post('quantity');
		$discount = $this->input->post('discount');

		if ($this->form_validation->run() != FALSE){
			$this->sale_lib->edit_item($line,$description,$serialnumber,$quantity,$discount,$price);
		}else{
			$data['error']=$this->lang->line('sales_error_editing_item');
		}
		if($this->sale_lib->out_of_stock($this->sale_lib->get_item_id($line))){
			$data['warning'] = $this->lang->line('sales_quantity_less_than_zero');
		}
		if (!$ajax)	$this->_reload($data); 
	}

	function delete_item($item_number)
	{
		$this->sale_lib->delete_item($item_number);
		$this->_reload();
	}

	function remove_customer()
	{
		$this->sale_lib->remove_customer();
		$this->_reload();
	}

	function complete()
	{
		$data['cart']=$this->sale_lib->get_cart();
		if (count($data['cart'])==0) $this->_reload();
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxing()?$this->sale_lib->get_taxes():false;
		$data['total']=$this->sale_lib->get_total();
		// $data['receipt_title']=$this->lang->line('sales_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a');
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->sale_lib->get_employee();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$comment = $this->sale_lib->get_comment();
		$emp_info=$this->Employee->get_info($employee_id);
		$data['payments']=$this->sale_lib->get_payments(true);
		$data['amount_change']=to_currency($this->sale_lib->get_amount_due() * -1);
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		//discount
		$data['general_discount']=$this->sale_lib->get_discount();
		if ($this->session->userdata('discounting') != 'checked') {
			$data['general_discount']=0;
		}

		//Para guardar el tipo de Sale
		$mode = 0;
		switch ( $this->sale_lib->get_mode() ) {
			case 'return':
				$mode = 1;
				//Datos para la vista a generar
				$data['receipt_title'] = $this->lang->line('sales_return');
			break;
			case 'shipping':
				$mode = 2;
				//Datos para la vista a generar
				$data['receipt_title'] = $this->lang->line('sales_shipping');
				$data['employee']=$emp_info->first_name.' '.$emp_info->last_name.' From: '.ucwords($this->session->userdata('dblocation'));

				//Registrar Location como customer 'CALICHE'
				include('application/config/database.php');
				$person_data = array(
					'first_name'=>$customer_id,
					'last_name'=>$db[$customer_id]['database'],
					'email'=>null,
					'phone_number'=>null,
					'address_1'=>$db[$customer_id]['hostname'],
					'address_2'=>null,
					'city'=>null,
					'state'=>null,
					'zip'=>null,
					'country'=>null,
					'comments'=>'location'
				);
				$customer_data=array(
					'account_number'=>null,
					'taxable'=>0
				);
				//Customer by Location
				$location = $customer_id;
				$customer = $this->Customer->get_info_by_name($customer_id);
				if (!$customer) {
					$this->Customer->save($person_data,$customer_data,-1);
					if (isset($customer_data['person_id'])) $customer_id =  $customer_data['person_id'];
					else $customer_id=0;
				}else{
					$customer_id = $customer->person_id;
				}
			break;
			default:
				//Datos para la vista a generar
				$data['receipt_title'] = $this->lang->line('sales_register');
			break;
		}

		$data['receipt_title'] .= ' '.$this->lang->line('sales_receipt');

		if($customer_id>-1 && is_numeric($customer_id))
		{
			$cust_info=$this->Customer->get_info($customer_id);
			if ($this->sale_lib->get_mode() == 'shipping') {
				$data['customer']=ucwords($cust_info->comments).': '.ucwords($cust_info->first_name);
			}else{
				$data['customer']=$cust_info->first_name.' '.$cust_info->last_name;
			}
		}

		//SAVE sale to database
		$sale_id = $this->Sale->save($data['cart'], $customer_id,$employee_id,$comment,$data['payments'], false, $mode);
		//Save Credit
		if (array_key_exists('account', $data['payments'])) {
			$cust_info=$this->Customer->get_info($customer_id);
			$period = explode('/',$cust_info->max_amount_credit);
			$periodo =  explode('_',$period[1]);
			
			$credit_data['person_custo_id'] = $cust_info->account_number;
			$credit_data['transfer_id'] = $sale_id;
			$credit_data['person_emplo_id'] = $this->Employee->get_logged_in_employee_info()->person_id;
			$credit_data['payment_period'] = $periodo[0];
			$credit_data['type'] = 'credit';
			$credit_data['status'] = 'not paid';

			$balance = $this->Customer->balance_total($credit_data['person_custo_id']);
			$credit_data['payment_amount'] = '-'.$data['payments']['account']['payment_amount'];
			$credit_data['balance'] =  (isset($balance->balance)?$balance->balance:0) + $data['payments']['account']['payment_amount'];

			// $credit_data_end = $this->Customer->check_final_date_credit($customer_id);
			// $this->Customer->save_credit($credit_data,$credit_data_end);

			$this->Customer->save_credit($credit_data);
		}
		$data['sale_id']='POS '.$sale_id;
		if ($data['sale_id'] == 'POS -1')
		{
			$data['error_message'] = $this->lang->line('sales_transaction_failed');
		}
		else
		{
			if ($this->sale_lib->get_email_receipt() && !empty($cust_info->email))
			{
				$this->load->library('email');
				$config['mailtype'] = 'html';				
				$this->email->initialize($config);
				$this->email->from($this->config->item('email'), $this->config->item('company'));
				$this->email->to($cust_info->email); 

				$this->email->subject($this->lang->line('sales_receipt'));
				$this->email->message($this->load->view('sales/receipt_email',$data, true));	
				$this->email->send();
			}

			if ( $this->sale_lib->get_mode() == 'shipping'){
				$this->load->model('Transfers');
				$data['sale_id'] = $this->Transfers->save($data['cart'], $location,$employee_id,$comment,$data['payments']);
				//Actualizamos orden
				if ($this->session->userdata('from_order')) {
					$this->load->model('Order');
					$this->Order->complete( $this->session->userdata('from_order'), $data['sale_id'] );
				}
			}
		}
		$this->load->view('sales/receipt',$data);
		$this->sale_lib->clear_all();
	}

	function receipt($sale_id)
	{
		$sale_info = $this->Sale->get_info($sale_id)->row_array();
		$this->sale_lib->copy_entire_sale($sale_id);
		$data['cart']=$this->sale_lib->get_cart();
		$data['payments']=$this->sale_lib->get_payments(true);
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']=$this->lang->line('sales_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a', strtotime($sale_info['sale_time']));
		$customer_id=$this->sale_lib->get_customer();
		$emp_info=$this->Employee->get_info($sale_info['employee_id']);
		$data['payment_type']=$sale_info['payment_type'];
		$data['amount_change']=to_currency($this->sale_lib->get_amount_due() * -1);
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($customer_id!=-1){
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name;
		}
		$data['sale_id']='POS '.$sale_id;
		$this->load->view('sales/receipt',$data);
		$this->sale_lib->clear_all();

	}
	
	// function edit($sale_id)
	// {
	// 	$data = array();

	// 	$data['customers'] = array('' => 'No Customer');
	// 	foreach ($this->Customer->get_all()->result() as $customer)
	// 	{
	// 		$data['customers'][$customer->person_id] = $customer->first_name . ' '. $customer->last_name;
	// 	}

	// 	$data['employees'] = array();
	// 	foreach ($this->Employee->get_all()->result() as $employee)
	// 	{
	// 		$data['employees'][$employee->person_id] = $employee->first_name . ' '. $employee->last_name;
	// 	}

	// 	$data['sale_info'] = $this->Sale->get_info($sale_id)->row_array();

	// 	$this->load->view('sales/edit', $data);
	// }

	function delete($sale_id){
		$data = array();
		if ($this->Sale->delete($sale_id)){
			$data['success'] = true;
		}else{
			$data['success'] = false;
		}
		$this->load->view('sales/delete', $data);
	}

	function save($sale_id)
	{
		$sale_data = array(
			'sale_time' => date('Y-m-d', strtotime($this->input->post('date'))),
			'customer_id' => $this->input->post('customer_id') ? $this->input->post('customer_id') : null,
			'employee_id' => $this->input->post('employee_id'),
			'comment' => $this->input->post('comment')
		);

		if ($this->Sale->update($sale_data, $sale_id))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('sales_successfully_updated')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('sales_unsuccessfully_updated')));
		}
	}

	function _payments_cover_total()
	{
		$total_payments = 0;

		foreach($this->sale_lib->get_payments() as $payment)
		{
			$total_payments += $payment['payment_amount'];
		}

		/* Changed the conditional to account for floating point rounding */
		if ( ( $this->sale_lib->get_mode() == 'sale' ) && ( ( to_currency_no_money( $this->sale_lib->get_total() ) - $total_payments ) > 1e-6 ) )
		{
			return false;
		}

		return true;
	}

	function _reload($data=array()){
		if ($this->sale_lib->get_mode()==='shipping') redirect('sales/index/shipping');
		else redirect('sales');
	}

	function cancel_sale()
	{
		$this->sale_lib->clear_all();
		$this->_reload();
	}

	function suspend()
	{
		$data['cart']=$this->sale_lib->get_cart();
		$data['subtotal']=$this->sale_lib->get_subtotal();
		$data['taxes']=$this->sale_lib->get_taxes();
		$data['total']=$this->sale_lib->get_total();
		$data['receipt_title']=$this->lang->line('sales_receipt');
		$data['transaction_time']= date('m/d/Y h:i:s a');
		$customer_id=$this->sale_lib->get_customer();
		$employee_id=$this->Employee->get_logged_in_employee_info()->person_id;
		$comment = $this->input->post('comment');
		$emp_info=$this->Employee->get_info($employee_id);
		$payment_type = $this->input->post('payment_type');
		$data['payment_type']=$this->input->post('payment_type');
		//Alain Multiple payments
		$data['payments']=$this->sale_lib->get_payments(true);
		$data['amount_change']=to_currency($this->sale_lib->get_amount_due() * -1);
		$data['employee']=$emp_info->first_name.' '.$emp_info->last_name;

		if($customer_id!=-1)
		{
			$cust_info=$this->Customer->get_info($customer_id);
			$data['customer']=$cust_info->first_name.' '.$cust_info->last_name;
		}

		$total_payments = 0;

		foreach($data['payments'] as $payment)
		{
			$total_payments += $payment['payment_amount'];
		}

		//SAVE sale to database
		$data['sale_id']='POS '.$this->Sale_suspended->save($data['cart'], $customer_id,$employee_id,$comment,$data['payments']);
		if ($data['sale_id'] == 'POS -1')
		{
			$data['error_message'] = $this->lang->line('sales_transaction_failed');
		}
		$this->sale_lib->clear_all();
		$this->_reload(array('success' => $this->lang->line('sales_successfully_suspended_sale')));
	}

	function suspended()
	{
		$data = array();
		$data['suspended_sales'] = $this->Sale_suspended->get_all()->result_array();
		$this->load->view('sales/suspended', $data);
	}

	function unsuspend()
	{
		$sale_id = $this->input->post('suspended_sale_id');
		$this->sale_lib->clear_all();
		$this->sale_lib->copy_entire_suspended_sale($sale_id);
		$this->Sale_suspended->delete($sale_id);
		$this->_reload();
	}
	function pending_orders_to_shipping($id_order){
		$this->load->model('Order');
		$data['location']=$this->Order->get_info($id_order);
		$data['location']=$data['location']['info']->row()->location;
		if 	($data['location']==$this->session->userdata('dblocation'))	redirect('reports/pending_orders/'.$id_order.'/2');
		else{
			$this->session->set_userdata('from_order', $id_order);
			$this->sale_lib->set_mode('shipping');
			$this->sale_lib->nameS=$this->sale_lib->get_mode().'_';
			$this->sale_lib->set_customer($data['location']);				
			$data['dat'] = $this->Order->get_detail($id_order)->result();
			foreach ($data['dat'] as $key) {
				$this->sale_lib->add_item($key->id_item,$key->quantity,0,null,null,null,null);	
			}
			$this->_reload();
		}

	}

	function workorder(){
		$data['test'] = 'test';
		$this->load->view('sales/workorder',$data);
	}
}
