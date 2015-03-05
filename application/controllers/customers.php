<?php
require_once ("person_controller.php");
class Customers extends Person_controller
{
	function __construct()
	{
		parent::__construct('customers');
	}
	
	function index()
	{
		$config['base_url'] = site_url('/customers/index');
		$config['total_rows'] = $this->Customer->count_all();
		$config['per_page'] = '20';
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		
		$data['controller_name']=strtolower(get_class());
		$data['form_width']=$this->get_form_width();

		$data['manage_table']=get_customers_manage_table( $this->Customer->get_all( $config['per_page'], $this->uri->segment( $config['uri_segment'] ) ), $this );
		$this->load->view('people/manage',$data);
	}

	function account($customer_id = -1,$status='pay'){
		//$config['base_url'] = site_url('/customers/account');
		//$config['total_rows'] = $this->Customer->count_all_credits($customer_id);
		$config['per_page'] = 6;
		$config['uri_segment'] = 3;
		//$this->pagination->initialize($config);

		$data['type']=$status;
		$data['person_info']=$this->Customer->get_info($customer_id);
		$data['credits_person_info']=$this->Customer->get_info_credits($customer_id);
		$data['count_credit']=$this->Customer->count_all_credits($customer_id); 
		$data['balance']=$this->Customer->balance_total($customer_id);

		$data['manage_table']=get_customers_credits_manage_table( $this->Customer->get_all_credits( $config['per_page'], $this->uri->segment( $config['uri_segment']),$customer_id), $this );

		$this->load->view('credits/form',$data);
	}
	
	/*
	Returns customer table data rows. This will be called with AJAX.
	*/
	function search()
	{
		$search=$this->input->post('search');
		$data_rows=get_people_manage_table_data_rows($this->Customer->search($search),$this);
		echo $data_rows;
	}
	
	/*
	Gives search suggestions based on what is being searched for
	*/
	function suggest()
	{
		$suggestions = $this->Customer->get_search_suggestions($this->input->post('q'),$this->input->post('limit'));
		echo implode("\n",$suggestions);
	}
	
	/*
	Loads the customer edit form
	*/
	function view($customer_id=-1)
	{
		$data['person_info']=$this->Customer->get_info($customer_id);
		$this->load->view("customers/form",$data);
	}
	
	function exists_email($email='')
	{
		$email = $this->input->post('email');

		if($this->Customer->exists_email($email)!='0')
		{	
			//si existe
			echo json_encode(array('success'=>true,'message'=>'existe', 'email'=>$this->Customer->exists_email($email)));
		}else{
			//no existe
			echo json_encode(array('success'=>false,'message'=>'no existe','email'=>$this->Customer->exists_email($email)));
		}
	}

	/*
	Inserts/updates a customer
	*/
	function save($customer_id=-1)
	{
		if ($customer_id==-1){
			if($this->Customer->exists_email($this->input->post('email'))!='0'){	
				//si existe
				die (
					json_encode(
						array(
							'success'=>true,
							'message'=>'existe', 
							'email'=> $this->Customer->exists_email($this->input->post('email'))
						)
					)
				);
			}else{
				$person_data['email'] = $this->input->post('email');
			}
		}else{
			if ($this->Customer->exists_email($this->input->post('email'))=='0'){	
				$person_data['email'] = $this->input->post('email');
			}
		}
		
		$person_data['first_name'] = $this->input->post('first_name');
		$person_data['last_name'] = $this->input->post('last_name');
		$person_data['phone_number'] = $this->input->post('phone_number');
		$person_data['address_1'] = $this->input->post('address_1');
		$person_data['address_2'] = $this->input->post('address_2');
		$person_data['city'] = $this->input->post('city');
		$person_data['state'] = $this->input->post('state');
		$person_data['zip'] = $this->input->post('zip');
		$person_data['country'] = $this->input->post('country');
		$person_data['comments'] = $this->input->post('comments');
		
		if (($this->input->post('amount')!='')&&($this->input->post('amount_dyas'))!='') {
			$max_amount_credit = $this->input->post('amount').'/'.$this->input->post('amount_dyas');
		} else {
			$max_amount_credit = NULL;
		}

		$customer_data=array(
			'taxable'=>$this->input->post('taxable')=='' ? 0:1,
			'discounts'=>$this->input->post('discount'),
			'type'=>$this->input->post('customer_type'),
			'max_amount_credit'=>$max_amount_credit,
			'tax_id'=>$this->input->post('tax_id')
		);
		
		if($this->Customer->save($person_data,$customer_data,$customer_id))
		{
			//New customer
			if($customer_id==-1)
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('customers_successful_adding').' '.
				$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>$person_data['person_id']));
			}
			else //previous customer
			{
				echo json_encode(array('success'=>true,'message'=>$this->lang->line('customers_successful_updating').' '.
				$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>$customer_id, 'retorno'=>$person_data));
			}
		}
		else//failure
		{	
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('customers_error_adding_updating').' '.
			$person_data['first_name'].' '.$person_data['last_name'],'person_id'=>-1));
		}
	}
	
	/*
	Inserts/updates a customer
	*/
	function save_credit($person_id=-1, $type='pay')
	{
		$credit_data['person_custo_id'] = $person_id;
		$credit_data['transfer_id'] = '0';
		$credit_data['person_emplo_id'] = $this->Employee->get_logged_in_employee_info()->person_id;
		$credit_data['payment_period'] = '---';
		$credit_data['type'] = $type;

		if($type=='pay'){
			//creditos del customers
			$credits_custo = $this->Customer->get_type_credits($person_id);
					//monto a pagar
			$monto = str_replace('-','',$this->input->post('pay_amount_credit'));
			//recorremos todos los creditos del customers
			foreach($credits_custo->result() as $credits)
			{
				// echo $person_id.' Entro a los creditos no pagados<br> ';
				$balance = $this->Customer->balance_total($person_id);
				$amount = explode('-',$credits->payment_amount);

				if ($monto > $amount[1]) {

					// echo ' Monto mayor al credito a cancelar. Monto a pagar '.$monto.' credito '.$amount[1];
					$monto = $monto - $amount[1];
					$credit_data['payment_amount'] = $amount[1];
					$credit_data['balance'] = $balance->balance - $amount[1];
					$credit_data['status'] = 'pay';

					//cambiando el status a pagado del credito
					$this->Customer->change_status_credit($credits->credit_id);
					
					//almacenamos los nuevos pagos
					$exito = ($this->Customer->save_credit($credit_data))?true:false;
					// $exito = true;
				}else{
					// echo ' Monto menor al credito a cancelar. Monto a pagar '.$monto.' credito '.$amount[1].'<br>';
					$credit_data['payment_amount'] = $monto;
					$balance = $balance->balance - $monto;

					if ($monto!=0) {
						// echo ' Si el todavia tiene deuda, se suman los avance de pago.<br>';
						$sum = $this->Customer->get_advance_credits($person_id);
						$total = $sum->sum + $monto;
						// echo ' Suma mas monto a pagar '.$total.' credito '.$amount[1].' sum '.$sum->sum.' id_cre '.$credits->credit_id;
						if($total >= $amount[1]){$this->Customer->change_status_credit($credits->credit_id);}
						$credit_data['balance'] = $balance;
						$credit_data['status'] = 'advance';
						$exito = ($this->Customer->save_credit($credit_data))?true:false;
					}
					$monto = 0; 
				}
				// echo ' Balance final '.$credit_data['balance'];
				if ($credit_data['balance']==0) {
					// echo ' cambiando estatus advance '.$person_id.' ';
					$this->Customer->change_advance_credit($person_id);
					
				} 
			}

		}

		if($exito){
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('customers_successful_adding')));
		}else{	
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('customers_error_adding_updating')));
		}
	}

	/*
	This deletes customers from the customers table
	*/
	function delete()
	{
		$customers_to_delete=$this->input->post('ids');
		
		if($this->Customer->delete_list($customers_to_delete))
		{
			echo json_encode(array('success'=>true,'message'=>$this->lang->line('customers_successful_deleted').' '.
			count($customers_to_delete).' '.$this->lang->line('customers_one_or_multiple')));
		}
		else
		{
			echo json_encode(array('success'=>false,'message'=>$this->lang->line('customers_cannot_be_deleted')));
		}
	}
	
	function excel()
	{
		$data = file_get_contents("import_customers.csv");
		$name = 'import_customers.csv';
		force_download($name, $data);
	}
	
	function excel_import()
	{
		$this->load->view("customers/excel_import", null);
	}

	function do_excel_import()
	{
		$msg = 'do_excel_import';
		$failCodes = array();
		if ($_FILES['file_path']['error']!=UPLOAD_ERR_OK)
		{
			$msg = $this->lang->line('items_excel_import_failed');
			echo json_encode( array('success'=>false,'message'=>$msg) );
			return;
		}
		else
		{
			if (($handle = fopen($_FILES['file_path']['tmp_name'], "r")) !== FALSE)
			{
				//Skip first row
				fgetcsv($handle);
				
				$i=1;
				while (($data = fgetcsv($handle)) !== FALSE) 
				{
					$person_data = array(
					'first_name'=>$data[0],
					'last_name'=>$data[1],
					'email'=>$data[2],
					'phone_number'=>$data[3],
					'address_1'=>$data[4],
					'address_2'=>$data[5],
					'city'=>$data[6],
					'state'=>$data[7],
					'zip'=>$data[8],
					'country'=>$data[9],
					'comments'=>$data[10]
					);
					
					$customer_data=array(
					'account_number'=>$data[11]=='' ? null:$data[11],
					'taxable'=>$data[12]=='' ? 0:1,
					);
					
					if(!$this->Customer->save($person_data,$customer_data))
					{	
						$failCodes[] = $i;
					}
					
					$i++;
				}
			}
			else 
			{
				echo json_encode( array('success'=>false,'message'=>'Your upload file has no data or not in supported format.') );
				return;
			}
		}

		$success = true;
		if(count($failCodes) > 1)
		{
			$msg = "Most customers imported. But some were not, here is list of their CODE (" .count($failCodes) ."): ".implode(", ", $failCodes);
			$success = false;
		}
		else
		{
			$msg = "Import Customers successful";
		}

		echo json_encode( array('success'=>$success,'message'=>$msg) );
	}
	
	/*
	get the width for the add/edit form
	*/
	function get_form_width(){ return '600/height:420'; }
}
?>