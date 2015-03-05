<?php
class Secure_area extends CI_Controller 
{
	/*
	Controllers that are considered secure extend Secure_area, optionally a $module_id can
	be set to also check if a user can access a particular module in the system.
	*/
	function __construct($module_id=null)
	{
		parent::__construct();	
		//Solo desarrolladores
		if (ENVIRONMENT == 'development' && isset($_GET['updatedb'])) {
			$this->load->library('Migration');
			$vrs = ($_GET['updatedb']) ? $_GET['updatedb'] : 1;
			if ( !$this->migration->current($vrs) ){
				show_error($this->migration->error_string());
			}
		}
		
		$this->load->model('Employee');
		if(!$this->Employee->is_logged_in()){ redirect('login'); }
		if ($module_id!='invetories_compare' && $this->Employee->isAdmin()){
			$this->load->model('reports/Inventory_compare');
            $model = $this->Inventory_compare;
            if ($model->getData() == false) {
            	$model->save_inventory('');
            }
            if (!$model->exist_inventory()){ redirect('inventories_compare'); }
        }
		$id_employee_l=$this->Employee->get_logged_in_employee_info()->person_id;
		// $this->$this->uri->segment(2);
		if ($module_id!='items') $this->session->unset_userdata('items_fil');
		if ($module_id!='services') $this->session->unset_userdata('services_fil');
		if ($module_id!='receivings') $this->session->unset_userdata('receiving_fil');
		switch ($module_id) {
			case 'orders': case 'receivings':
				$action=ucwords($module_id);
			break;
			case 'sales': 
				if ($this->uri->segment(3) && $this->uri->segment(3)==='shipping') $action='Shipping';
				else $action='';
			break;
			case 'employees': 
				if ($this->uri->segment(2) && $this->uri->segment(2)==='assistance') $action=1;
				else $action='';
			break;
			case 'reports':
				$report=$this->uri->segment(2);
				$action=false;
				if ($report){
					switch ($report) { 
						case 'inventory_low': $report='Low Stock'; break;
						case 'shippings': $report='Delivery to Receive'; break;
						case 'accounts_payable': $report='Accounts Payable'; break;
						case 'accounts_receivable': $report='Invoice Discounting'; break;
						case 'pending_orders': $report='Pendig Orders'; break;
						default: $action=''; break;
					}
				}else $action='';
			break;
			case 'invetories_compare': case 'share_inventories': case 'no_access': $action=1; break;
			default: $action=''; break;
		}
		if ($action===''){
			if(!$this->Employee->has_permission($module_id,$id_employee_l)) redirect('no_access/'.$module_id);
		}elseif(isset($report)){
			if(!$this->Employee->has_privilege($report,'notification_alert')){ redirect('no_access/'.$report.'/1'); }
		}elseif($action!==1){
			if(!$this->Employee->has_privilege($action,'stock_control')){ redirect('no_access/'.$action.'/1'); }
		}
		
		//Modelos a utilizar en varias notificaciones
		$this->load->model('reports/Detailed_receivings');
		$this->load->model('Transfers');
		$this->Receiving->con=$this->Detailed_receivings->stabledb($this->session->userdata('dblocation'),true);
		$this->Receiving->create_receivings_items_temp_table();

		//load up global data
		$logged_in_employee_info=$this->Employee->get_logged_in_employee_info();
		$data['allowed_modules']=$this->Module->get_allowed_modules($logged_in_employee_info->person_id);
		$data['user_info']=$logged_in_employee_info;

		if ($module_id!='invetories_compare'){

			//Notificaciones
			if($this->Employee->has_privilege('Low Stock','notification_alert')){
				$this->load->model('reports/Inventory_low');
				$this->Inventory_low->stabledb($this->session->userdata('dblocation'),true);
				$data['notifications']['inventory_low']['url']= 'reports/inventory_low/0/';
				$data['notifications']['inventory_low']['title']= $this->lang->line('notification_low_stock');
				$data['notifications']['inventory_low']['data']= $this->Inventory_low->getData(array());
			}

			if($this->Employee->has_privilege('Delivery to Receive','notification_alert')){
				if ($this->Transfers->available()) {
					$data['notifications']['shippings']['url'] = 'reports/shippings';
					$data['notifications']['shippings']['title'] = $this->lang->line('notification_delivery_to_receive');
					$data['notifications']['shippings']['data'] = $this->Transfers->get_my_reception();
				}
			}
			
			if($this->Employee->has_privilege('Accounts Payable','notification_alert')){
				$data['notifications']['accounts_payable']['url']= 'reports/accounts_payable/0/';
				$data['notifications']['accounts_payable']['title']= $this->lang->line('reports_accounts_payable');
				$data['notifications']['accounts_payable']['data']= array_merge($this->Detailed_receivings->getData(array(),true), $this->Transfers->transfers_receivable());
			}

			if($this->Employee->has_privilege('Invoice Discounting','notification_alert')){
				$data['notifications']['accounts_receivable']['url']= 'reports/accounts_receivable/0/';
				$data['notifications']['accounts_receivable']['title']= $this->lang->line('reports_accounts_receivable');
				$data['notifications']['accounts_receivable']['data']= $this->Transfers->transfers_receivable('sender');
			}

			if($this->Employee->has_privilege('Pendig Orders','notification_alert')){
				$this->load->model('Order');
				$data['notifications']['pending_orders']['url']= 'reports/pending_orders/pending';
				$data['notifications']['pending_orders']['title']= $this->lang->line('reports_pending_orders');
				$data['notifications']['pending_orders']['data']= $this->Order->get_all(array('filter_location'=>$this->session->userdata('dblocation')));
			}

			if($this->Employee->has_privilege('Broken Items','notification_alert')){
				$this->load->model('Item');
				$data['notifications']['broken_items']['url']= 'items';
				$data['notifications']['broken_items']['title']= $this->lang->line('notification_broken_items');
				$data['notifications']['broken_items']['data']= $this->Item->get_broken_items();
			}
		}

		//Carga de variables
		$this->load->vars($data);
	}
}
?>
