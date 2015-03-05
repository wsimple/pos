<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Tracking extends CI_Controller {

	private $data;
	private $id_customer;
	private $case;
	private $customer;

	public function __construct()
	{
		parent::__construct();
		$this->load->model('ModelPhoneModel');
		$this->load->model('ModelPhoneBrand');
		$this->load->model('ModelTracking');
		$this->data = array();
		$this->id_customer = '';
		$this->case = array();
	}

	public function index()
	{		
		$this->load->model('ModelZips');
		$this->data = array(
			'phone_models' => $this->ModelPhoneModel->getRows(),//'states' => $this->ModelZips->getRows('', '', '', $group=' GROUP BY state')
			'phone_brand' => $this->ModelPhoneBrand->getRows()
		);
		if ($this->input->post('customerSelected')!='' && $this->input->post('isCustomer')!=''){
			$this->data['customerSelected'] = $this->input->post('customerSelected');
			$this->data['isCustomer'] = $this->input->post('isCustomer');
			if ($this->input->post('isCustomer')==0){
				$this->data['states'] = $this->ModelZips->getRows('', '', '', $group=' GROUP BY state');
			}
		}
		$this->load->layout('tracking/workOrder',$this->data);
	}

	public function pick_customer()
	{	
		$this->load->layout('tracking/pickCustomer', $this->data);
	}

	public function approval(){
		$this->load->model('ModelPeople');
		$customer = array();

		//if the user pick the customer in the search box
		if ($this->input->post('txtCustomer')!='' && $this->input->post('isCustomer')=='1'){

			$email_customer = explode('-', $this->input->post('txtCustomer'));
			$id_customer = $this->ModelPeople->get_field('person_id', " WHERE email LIKE '".trim($email_customer[1])."'");

		}elseif($this->input->post('isCustomer')=='0'){ //new customer
//echo '2-';
			if (!$this->ModelPeople->exists($this->input->post('txtEmail'))){
				$customer = array(
					'first_name' => $this->input->post('txtFirstName'),
					'last_name' => $this->input->post('txtLastName'),
					'phone_number' => $this->input->post('txtPhoneNumber'),
					'email' => $this->input->post('txtEmail'),
					'city' => $this->input->post('txtCity'),
					'state' => $this->input->post('cboState'),
					'zip' => $this->input->post('txtZip'),
					'country' => 'US',
					'comments' => 'New customer from app, date: '.date('Y-m-d')
				);
			}elseif($this->input->post('txtEmail')!='' && $this->ModelPeople->exists($this->input->post('txtEmail'))){ //echo '3->';
				$id_customer = $this->ModelPeople->get_field('person_id', " WHERE email LIKE '".$this->input->post('txtEmail')."'");
				$person = $this->ModelPeople->getRow($id_customer);
			}

		}//elseif

		//approval view
		$this->data = array(
			'customer_name' => isset($id_customer) ? $this->ModelPeople->full_name($id_customer) : $customer['first_name'].' '.$customer['last_name'],
			'device' => $this->ModelPhoneBrand->get_field('brand_name', " WHERE brand_id = '".$this->input->post('cboPhoneBrand')."'"),
			'case' => 
				array(
					'person_id' => isset($id_customer) ? $id_customer : '', 
					'model_id' => $this->input->post('cboPhoneModel'),
					'brand_id' => $this->input->post('cboPhoneBrand'),
					'model_device' => $this->input->post('txtModel'),
					'color' => $this->input->post('txtColor'),
					'problem' => $this->input->post('txtProblem')
				),
			'customer' => $customer,
			'id_customer' => isset($id_customer) ? $id_customer : '',
			'email_customer' => isset($email_customer[1]) ? $email_customer[1] : (isset($person->email)?$person->email:$customer['email'])
		);

		// _imprimir($_POST);
		// _imprimir($customer);
		// _imprimir($this->data);

		$this->load->layout('tracking/approval',$this->data);
	}

	public function save(){
		$this->load->model('ModelPeople');
		
		$id_customer = '';
		if ($this->input->post('email')!=''&&$this->input->post('id_customer')==''){
			$arrayCustomer = array(
				'first_name' => $this->input->post('first_name'),
				'last_name' => $this->input->post('last_name'),
				'phone_number' => $this->input->post('phone_number'),
				'email' => $this->input->post('email'),
				'address_1' => '',
				'address_2' => '',
				'city' => $this->input->post('city'),
				'state' => $this->input->post('state'),
				'zip' => $this->input->post('zip'),
				'country' => $this->input->post('country'),
				'comments' => $this->input->post('comments')
			);
			$this->ModelPeople->insert_customer($arrayCustomer);
			$id_customer = $this->ModelPeople->get_last_id();
		}elseif ($this->input->post('id_customer')!='') {
			$id_customer = $this->input->post('id_customer');
			$arrayCustomer = $this->ModelPeople->getRow($id_customer);
		}

		//work order insert
		$case = array(
			'person_id' => $id_customer, 
			'model_id' => $this->input->post('model_id'),
			'serial' => $this->input->post('serial'),
			'color' => $this->input->post('color'),
			'problem' => $this->input->post('problem'),
			'model_device' => $this->input->post('model_device'),
			'brand_id' => $this->input->post('brand_id')
		);	
		
		$this->ModelTracking->insert($case);
		$id_work_order = $this->ModelTracking->get_last_id();
		$img = sigJsonToImage($this->input->post('output'));
		imagepng($img, 'images/signatures/wo_'.$id_work_order.'_person_'.$id_customer.'.png');
		imagedestroy($img);

		//send email
		$emailData = array(
			'customer' => ($this->input->post('id_customer')=='') ? $arrayCustomer['first_name'].' '.$arrayCustomer['last_name'] : $this->ModelPeople->full_name($id_customer),
			'email' => ($this->input->post('id_customer')=='') ? $arrayCustomer['email'] : $arrayCustomer->email,
			'work_order' => $id_work_order,
			'signature' => base_url().'images/signatures/wo_'.$id_work_order.'_person_'.$id_customer.'.png',
			'destiny' => 'workorder@fast-i-repair.com',
			'phone_number' => ($this->input->post('id_customer')=='') ? $arrayCustomer['phone_number'] : $arrayCustomer->phone_number,
			'problem' => $case['problem'],
			'email_customer' => ($this->input->post('id_customer')=='') ? $arrayCustomer['email'] : $arrayCustomer->email,
		);
		$this->send_email($emailData);

		//out
		$this->data = array(
			'out' => 'ok',
			'url' => base_url(),
			'title' => 'Message',
			'message' => 'Your request was saved successfully!',
			'work_order' => 'Your work order is: '.$this->ModelTracking->get_last_id()
		);

		$this->load->layout('tracking/pickCustomer',$this->data);
	}

	function send_email($case, $debug=false){
		$this->load->library('email');

		$body = '
			<table align="center" cellpadding="0" cellspacing="0" border="0" style="width: 600px; font-size: 12px; font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;font-weight: normal;border: 1px solid #f4f4f4; ">		
			<tr>
			<td style="border: 1px solid #f4f4f4;border-bottom: none;"><img src="'.base_url().'images/top_mail.png" alt=""></td>
			</tr>		
			<tr>
			<td style="padding:10px;border: 1px solid #f4f4f4;border-bottom: none; border-top: none;"><h4>Customer Info</h4></td>
			</tr>		
			<tr>
			<td style="padding:10px;border: 1px solid #f4f4f4;border-bottom: none;"><strong>Customer:</strong>&nbsp;&nbsp;&nbsp;'.$case['customer'].'</td>
			</tr>		
			<tr>
			<td style="padding:10px;border: 1px solid #f4f4f4;border-bottom: none;"><strong>Email:</strong>&nbsp;&nbsp;&nbsp;'.$case['email'].'</td>
			</tr>		
			<tr>
			<td style="padding:10px;border: 1px solid #f4f4f4;border-bottom: none;"><strong>Tel&eacute;fono:</strong>&nbsp;&nbsp;&nbsp;'.$case['phone_number'].'</td>
			</tr>		
			<tr>
			<td style="padding:10px;border: 1px solid #f4f4f4;border-bottom: none;"><h4>Request Info</h4></td>
			</tr>		
			<tr>
			<td style="padding:10px;border: 1px solid #f4f4f4;border-bottom: none;"><strong>Work Order Number:</strong>&nbsp;&nbsp;&nbsp;'.$case['work_order'].'</td>
			</tr>			
			<tr>
			<td style="padding:10px;border: 1px solid #f4f4f4;border-bottom: none;"><strong>Problem:</strong>&nbsp;&nbsp;&nbsp;'.$case['problem'].'</td>
			</tr>			
			<tr>
			<td style="padding:10px;border: 1px solid #f4f4f4;border-bottom: none;"><strong>Term of Services</strong></td>
			</tr>
			<tr>
			<td style=" padding: 10px; text-align: justify;  ">
				<small>
					I understand that Dash is not responsible for any damage to any items due to previous condition and/or usage. 
					Failure to inform the technician of any prior or current condition may result in testing, troubleshooting and repair methods being used 
					when they should not. In this case the customer is responsible for the damage of the product and any damage to equipment used to 
					troubleshoot the product, unless a technician has been negligent and/or abusive. Dash is not responsible for any damage to product 
					caused by attempting a repair in a proper way. Any equipment left over 30 days will be recycled. All personal data will be irrevocable 
					destroyed to protect your privacy. I understand that services rendered by Dash and any damage to this device or data are incidental to 
					the services rendered. Any warranty expressed or implied only covers that part and the labor provided on the part. All parts come with a 
					30 day warranty which does not include physical and/or liquid damage.
				</small>
			</td>
			</tr>
		
			<tr>
			<td style="padding:10px;border: 1px solid #f4f4f4;border-bottom: none;"><strong>Approval:</strong>&nbsp;&nbsp;&nbsp;<img src="'.$case['signature'].'" alt=""></td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			</tr>
			</table>		
		';

		$this->email->initialize(emailSetting());
		$this->email->from('info@fast-i-repair.com', 'DASH Cellular Repair');
		$this->email->to($case['destiny']);
		$this->email->subject('New work order # '.$case['work_order']);
		$this->email->message($body);
		$this->email->cc($case['email_customer']);
		$this->email->send();
		
		if ($debug)
			echo $this->email->print_debugger();	
	}

	public function grid(){
		$this->data = array(
			'orders' => $this->ModelTracking->get_grid()
		);
		
		$this->load->layout('tracking/grid',$this->data);		
	}
}
?>