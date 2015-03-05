<?php
require_once ("secure_area.php");
require_once (APPPATH."libraries/ofc-library/open-flash-chart.php");
class Reports extends Secure_area 
{
	private function __format_locations(){
		$_data['view']='';
		$_data['export_excel']=$export_excel;
		$rangeDays = $this->range_days($start_date,$end_date);

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	function __construct()
	{
		parent::__construct('reports');
		$this->load->helper('report');
	}

	//Initial report listing screen
	function index()
	{
		$this->load->view("reports/listing",array());
	}

	function _get_common_report_data()
	{
		$data = array();
		$data['report_date_range_simple'] = get_simple_date_ranges();
		$data['months'] = get_months();
		$data['days'] = get_days();
		$data['years'] = get_years();
		$data['selected_month']=date('n');
		$data['selected_day']=date('d');
		$data['selected_year']=date('Y');
		return $data;
	}

	//Input for reports that require only a date range. (see routes.php to see that all graphical summary reports route here)
	function date_input()
	{
		$data = $this->_get_common_report_data();
		$data['report_name'] = ucwords( str_replace('_',' ',$this->uri->segment(2)) );
		$this->load->view("reports/date_input",$data);	
	}

	function credits()
	{
		$data = $this->_get_common_report_data();
		$data['report_name'] = ucwords( str_replace('_',' ',$this->uri->segment(2)) );
		$data['customers_name'] = ucwords( str_replace('%20',' ',$this->uri->segment(3)) );
		$data['customers_id'] = $this->uri->segment(4);
		$this->load->view("reports/credits",$data);	
	}

	function credit_customers()
	{
		$data = $this->_get_common_report_data();
		$data['report_name'] = ucwords( str_replace('_',' ',$this->uri->segment(2)) );
		$data['customers_name'] = ucwords( str_replace('%20',' ',$this->uri->segment(3)) );
		$data['customers_id'] = $this->uri->segment(4);
		$this->load->view("reports/date_input_credits",$data);	
	}

	function customers_credit_overdue_bills()
	{
		$data = $this->_get_common_report_data();
		$data['report_name'] = ucwords( str_replace('_',' ',$this->uri->segment(2)) );
		$data['customers_name'] = ucwords( str_replace('%20',' ',$this->uri->segment(3)) );
		$data['customers_id'] = $this->uri->segment(4);
		$this->load->view("reports/date_input_credits_overdue_bills",$data);	
	}
	//Credits customers report
	function credit_customers_data()
	{
		$loca =  $this->input->post('locationbd');
		if ($loca) $location=$loca;
		$_data['view']='reports/tabular_credits';
		$_data['export_excel']=0;

		$sd = $this->input->post('start_year').'-'.$this->input->post('start_month').'-'.$this->input->post('start_day');
		$ed = $this->input->post('end_year').'-'.$this->input->post('end_month').'-'.$this->input->post('end_day');

		$rangeDays = $this->range_days($sd,$ed);
		$this->load->model('reports/Summary_credits');
		$model = $this->Summary_credits;

		
		$locations=$this->get_locations($location);
		foreach($locations as $location){
			
			$summary_data = array();
			$report_data = array();
			$customer_data = array();

			$report_data = $model->getData(array('start_date'=>$sd, 'end_date'=>$ed, 'customer_id' => $this->input->post('customer_id')));

			foreach($report_data as $row){
				if ($row['payment_period']=='---') {
					$p = '---';
				} else {
					$p = $row['payment_period'].' '.$this->lang->line('customers_period_days');
				}
				$summary_data[] = array($row['day_pay'], $row['transfer_id'], $row['payment_amount'],$row['balance'], $p, ucwords($row['type']), ucwords($row['status']));	
			}
			$customer_data = $model->get_info($this->input->post('customer_id'));
			$data = array(
				"title" =>$this->lang->line('reports_credits_statement'),
				"date_ini" => $sd,
				"date_end" => $ed,
				"headers" => $model->getDataColumns(),
				"data" => $summary_data,
				"data_customer" => $customer_data,
				"balance_last" => $model->balance_total($this->input->post('customer_id')),
				"balance_ini" => $model->balance_by_day($this->input->post('customer_id'),$sd,$ed,'ASC'),
				"balance_final" => $model->balance_by_day($this->input->post('customer_id'),$sd,$ed,'DESC'),
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => $model->getSummaryData(array('start_date'=>$sd, 'end_date'=>$ed, 'customer_id' => $this->input->post('customer_id'))),
				"export_excel" => 0
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	function customers_credit_overdue_bills_data()
	{
		$loca =  $this->input->post('locationbd');
		if ($loca) $location=$loca;
		$_data['view']='reports/tabular_credits_overdue_bills';
		$_data['export_excel']=0;

		// $rangeDays = $this->range_days($sd,$ed);
		$this->load->model('reports/Summary_credits');
		$model = $this->Summary_credits;

		
		$locations=$this->get_locations($location);
		foreach($locations as $location){
			
			$summary_data = array();
			$report_data = array();
			$customer_data = array();

			$report_data = $model->get_all_credits_overdue_bills($this->input->post('customer_id'));

			foreach($report_data as $row){
				$summary_data[] = array($row['credit_id'], $row['payment_amount'], str_replace('_',' ',$row['payment_period']).' '. $this->lang->line('customers_period_days'),$row['day_pay'],ucwords($row['status']), ucwords($row['ended']));	
			}
			$customer_data = $model->get_info($this->input->post('customer_id'));
			$data = array(
				"title" =>$this->lang->line('reports_credits_statement').' - '.ucwords( str_replace('_',' ',$this->uri->segment(2))),
				"headers" => $model->getDataColumnsOverdue(),
				"data" => $summary_data,
				"data_customer" => $customer_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				// "summary_data" => $model->getSummaryData(array('start_date'=>$sd, 'end_date'=>$ed, 'customer_id' => $this->input->post('customer_id'))),
				"export_excel" => 0
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	//Input for reports that require only a date range and an export to excel. (see routes.php to see that all summary reports route here)
	function date_input_excel_export()
	{
		$data = $this->_get_common_report_data();
		$data['report_name'] = ucwords( str_replace('_',' ',$this->uri->segment(2)) );
		$this->load->view("reports/date_input_excel_export",$data);
	}

	//get location (arreglo con todas las bases de datos cuando se coloca 'all')
	function get_locations($location='default'){
		include('application/config/database.php'); //Incluyo donde estaran todas las config de las databses
		if($location!='all') return array($location);
		foreach($db as $key=>$con){
			if ($key!='centralized')
				$locations[]=$key;
		}
		return $locations;
	}

	private function range_days($start_date,$end_date){
		return (date('m/d/Y',strtotime($start_date))==date('m/d/Y',strtotime($end_date))) ? 'Today' : date('m/d/Y',strtotime($start_date)).'-'.date('m/d/Y',strtotime($end_date));
	}

	//Summary sales report
	function summary_sales($start_date,$end_date,$sale_type,$export_excel=0,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$rangeDays = $this->range_days($start_date,$end_date);
		$this->load->model('reports/Summary_sales');
		$model = $this->Summary_sales;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$this->Receiving->con=$this->Sale->con;
			$this->Receiving->create_receivings_items_temp_table();
			$tabular_data = array();
			$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			foreach($report_data as $row)
			{
				$tabular_data[] = array($row['sale_date'], to_currency($row['subtotal']), to_currency($row['total']), to_currency($row['tax']),to_currency($row['profit']));
			}
			$data = array(
				"title" => $this->lang->line('reports_sales_summary_report'),
				"subtitle" => $rangeDays,
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type)),
				"export_excel" => $export_excel
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	//Summary categories report
	function summary_categories($start_date,$end_date,$sale_type,$export_excel=0,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$rangeDays = $this->range_days($start_date,$end_date);
		$this->load->model('reports/Summary_categories');
		$model = $this->Summary_categories;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$this->Receiving->con=$this->Sale->con;
			$this->Receiving->create_receivings_items_temp_table();
			$tabular_data = array();
			$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			foreach($report_data as $row)
			{
				$tabular_data[] = array($row['category'], to_currency($row['subtotal']), to_currency($row['total']), to_currency($row['tax']),to_currency($row['profit']));
			}
			$data = array(
				"title" => $this->lang->line('reports_categories_summary_report'),
				"subtitle" => $rangeDays,
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),	
				"summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type)),
				"export_excel" => $export_excel
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	//Summary customers report
	function summary_customers($start_date,$end_date,$sale_type,$export_excel=0,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $locations=$loca;
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$rangeDays = $this->range_days($start_date,$end_date);
		$this->load->model('reports/Summary_customers');
		$model = $this->Summary_customers;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$this->Receiving->con=$this->Sale->con;
			$this->Receiving->create_receivings_items_temp_table();
			$tabular_data = array();
			$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			foreach($report_data as $row)
			{
				$tabular_data[] = array($row['customer'], to_currency($row['subtotal']), to_currency($row['total']), to_currency($row['tax']),to_currency($row['profit']));
			}
			$data = array(
				"title" => $this->lang->line('reports_customers_summary_report'),
				"subtitle" => $rangeDays,
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type)),
				"export_excel" => $export_excel
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	//Summary suppliers report
	function summary_suppliers($start_date,$end_date,$sale_type,$export_excel=0,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$rangeDays = $this->range_days($start_date,$end_date);
		$this->load->model('reports/Summary_suppliers');
		$model = $this->Summary_suppliers;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$this->Receiving->con=$this->Sale->con;
			$this->Receiving->create_receivings_items_temp_table();
			$tabular_data = array();
			$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			foreach($report_data as $row)
			{
				$tabular_data[] = array($row['supplier'], to_currency($row['subtotal']), to_currency($row['total']), to_currency($row['tax']),to_currency($row['profit']));
			}
			$data = array(
				"title" => $this->lang->line('reports_suppliers_summary_report'),
				"subtitle" => $rangeDays,
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type)),
				"export_excel" => $export_excel
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	//Summary items report
	function summary_items($start_date,$end_date,$sale_type,$export_excel=0,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$rangeDays = $this->range_days($start_date,$end_date);
		$this->load->model('reports/Summary_items');
		$model = $this->Summary_items;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$this->Receiving->con=$this->Sale->con;
			$this->Receiving->create_receivings_items_temp_table();
			$tabular_data = array();
			$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			foreach($report_data as $row)
			{
				$tabular_data[] = array(character_limiter($row['name'], 16), $row['quantity_purchased'], to_currency($row['subtotal']), to_currency($row['total']), to_currency($row['tax']),to_currency($row['profit']));
			}
			$data = array(
				"title" => $this->lang->line('reports_items_summary_report'),
				"subtitle" => $rangeDays,
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type)),
				"export_excel" => $export_excel
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	//Summary employees report
	function summary_employees($start_date,$end_date,$sale_type,$export_excel=0,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$rangeDays = $this->range_days($start_date,$end_date);
		$this->load->model('reports/Summary_employees');
		$model = $this->Summary_employees;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$this->Receiving->con=$this->Sale->con;
			$this->Receiving->create_receivings_items_temp_table();
			$tabular_data = array();
			$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			foreach($report_data as $row)
			{
				$tabular_data[] = array($row['employee'], to_currency($row['subtotal']), to_currency($row['total']), to_currency($row['tax']),to_currency($row['profit']));
			}
			$data = array(
				"title" => $this->lang->line('reports_employees_summary_report'),
				"subtitle" => $rangeDays,
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type)),
				"export_excel" => $export_excel
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	//Summary taxes report
	function summary_taxes($start_date,$end_date,$sale_type,$export_excel=0,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$rangeDays = $this->range_days($start_date,$end_date);
		$this->load->model('reports/Summary_taxes');
		$model = $this->Summary_taxes;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$this->Receiving->con=$this->Sale->con;
			$this->Receiving->create_receivings_items_temp_table();
			$tabular_data = array();
			$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			foreach($report_data as $row)
			{
				$tabular_data[] = array($row['percent'], to_currency($row['subtotal']), to_currency($row['total']), to_currency($row['tax']));
			}
			$data = array(
				"title" => $this->lang->line('reports_taxes_summary_report'),
				"subtitle" => $rangeDays,
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type)),
				"export_excel" => $export_excel
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	//Summary discounts report
	function summary_discounts($start_date, $end_date, $sale_type, $export_excel=0,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$rangeDays = $this->range_days($start_date,$end_date);
		$this->load->model('reports/Summary_discounts');
		$model = $this->Summary_discounts;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$this->Receiving->con=$this->Sale->con;
			$this->Receiving->create_receivings_items_temp_table();
			$tabular_data = array();
			$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			foreach($report_data as $row)
			{
				$tabular_data[] = array($row['discount_percent'],$row['count']);
			}
			$data = array(
				"title" => $this->lang->line('reports_discounts_summary_report'),
				"subtitle" => $rangeDays,
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type)),
				"export_excel" => $export_excel
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	function summary_payments($start_date, $end_date, $sale_type, $export_excel=0,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$rangeDays = $this->range_days($start_date,$end_date);
		$this->load->model('reports/Summary_payments');
		$model = $this->Summary_payments;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$this->Receiving->con=$this->Sale->con;
			$this->Receiving->create_receivings_items_temp_table();
			$tabular_data = array();
			$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			foreach($report_data as $row)
			{
				$tabular_data[] = array($row['payment_type'],to_currency($row['payment_amount']));
			}
			$data = array(
				"title" => $this->lang->line('reports_payments_summary_report'),
				"subtitle" => $rangeDays,
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type)),
				"export_excel" => $export_excel
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	//Graphical report -- global method
	private function graphical_report($model,$type,$start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$_data['view']='reports/graphical';
		//Fixes Eli para que mueestre ho o rango de fecha
		$rangeDays = (date('m/d/Y', strtotime($start_date)) == date('m/d/Y', strtotime($end_date))) ? 'Today' : date('m/d/Y', strtotime($start_date)).'-'.date('m/d/Y', strtotime($end_date));
		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$this->Receiving->con=$this->Sale->con;
			$this->Receiving->create_receivings_items_temp_table();
			$subtitle = trim(get_class($model), '_Summary_');
			if ($subtitle == 'sales') $subtitle = '';
			else $subtitle = ' ('.ucwords($subtitle).')';
			$data = array(
				"title" => $this->lang->line('reports_sales_summary_report').$subtitle,
				"data_file" => site_url("reports/graphical_summary_$type"."_graph/$start_date/$end_date/$sale_type/$location"),
				"subtitle" => $rangeDays,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type))
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	//Graphical summary sales report
	function graphical_summary_sales($start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$this->load->model('reports/Summary_sales');
		$model = $this->Summary_sales;
		$this->graphical_report($model,'sales',$start_date,$end_date,$sale_type,$location);
	}

	//The actual graph data
	function graphical_summary_sales_graph($start_date, $end_date, $sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$this->load->model('reports/Summary_sales');
		$model = $this->Summary_sales;
		$this->Sale->con=$model->stabledb($location,true);
		$this->Sale->create_sales_items_temp_table();
		$this->Receiving->con=$this->Sale->con;
		$this->Receiving->create_receivings_items_temp_table();
		$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[date('m/d/Y', strtotime($row['sale_date']))]= $row['total'];
		}
		$data = array(
			"title" => $this->lang->line('reports_sales_summary_report'),
			"yaxis_label"=>$this->lang->line('reports_revenue'),
			"xaxis_label"=>$this->lang->line('reports_date'),
			"data" => $graph_data
		);
		$this->load->view("reports/graphs/line",$data);
	}

	//Graphical summary items report
	function graphical_summary_items($start_date,$end_date,$sale_type,$location='default')
	{
		$this->load->model('reports/Summary_items');
		$model = $this->Summary_items;
		$this->graphical_report($model,'items',$start_date,$end_date,$sale_type,$location);
	}

	//The actual graph data
	function graphical_summary_items_graph($start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$this->load->model('reports/Summary_items');
		$model = $this->Summary_items;
		$this->Sale->con=$model->stabledb($location,true);
		$this->Sale->create_sales_items_temp_table();
		$this->Receiving->con=$this->Sale->con;
		$this->Receiving->create_receivings_items_temp_table();
		$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['name']] = $row['total'];
		}
		$data = array(
			"title" => $this->lang->line('reports_items_summary_report'),
			"xaxis_label"=>$this->lang->line('reports_revenue'),
			"yaxis_label"=>$this->lang->line('reports_items'),
			"data" => $graph_data
		);
		$this->load->view("reports/graphs/hbar",$data);
	}

	//Graphical summary customers report
	function graphical_summary_categories($start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$this->load->model('reports/Summary_categories');
		$model = $this->Summary_categories;
		$this->graphical_report($model,'categories',$start_date,$end_date,$sale_type,$location);
	}

	//The actual graph data
	function graphical_summary_categories_graph($start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$this->load->model('reports/Summary_categories');
		$model = $this->Summary_categories;
		$this->Sale->con=$model->stabledb($location,true);
		$this->Sale->create_sales_items_temp_table();
		$this->Receiving->con=$this->Sale->con;
		$this->Receiving->create_receivings_items_temp_table();
		$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['category']] = $row['total'];
		}
		$data = array(
			"title" => $this->lang->line('reports_categories_summary_report'),
			"data" => $graph_data
		);
		$this->load->view("reports/graphs/pie",$data);
	}

	function graphical_summary_suppliers($start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$this->load->model('reports/Summary_suppliers');
		$model = $this->Summary_suppliers;
		$this->graphical_report($model,'suppliers',$start_date,$end_date,$sale_type,$location);
	}

	//The actual graph data
	function graphical_summary_suppliers_graph($start_date,$end_date,$sale_type,$location='default')
	{
		$location = ($this->input->get('loc')) ? urldecode($this->input->get('loc')) : 'default';
		//if ($loca) $location=$loca;
		$this->load->model('reports/Summary_suppliers');
		$model = $this->Summary_suppliers;
		$this->Sale->con=$model->stabledb($location,true);
		$this->Sale->create_sales_items_temp_table();
		$this->Receiving->con=$this->Sale->con;
		$this->Receiving->create_receivings_items_temp_table();
		$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['supplier']] = $row['total'];
		}
		$data = array(
			"title" => $this->lang->line('reports_suppliers_summary_report'),
			"data" => $graph_data
		);
		$this->load->view("reports/graphs/pie",$data);
	}

	function graphical_summary_employees($start_date,$end_date,$sale_type,$location='default')
	{
		$location = urldecode($this->input->get('loc'));
		if (isset($loca)) $location=$loca;
		$this->load->model('reports/Summary_employees');
		$model = $this->Summary_employees;
		$this->graphical_report($model,'employees',$start_date,$end_date,$sale_type,$location);
	}

	//The actual graph data
	function graphical_summary_employees_graph($start_date,$end_date,$sale_type,$location='default')
	{
		$location = ($this->input->get('loc')) ? urldecode($this->input->get('loc')) : 'default';
		// if ($loca) $location=$loca;
		$this->load->model('reports/Summary_employees');
		$model = $this->Summary_employees;
		$this->Sale->con=$model->stabledb($location,true);
		$this->Sale->create_sales_items_temp_table();
		$this->Receiving->con=$this->Sale->con;
		$this->Receiving->create_receivings_items_temp_table();
		$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => 'sales'));
		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['employee']] = $row['total'];
		}
		$data = array(
			"title" => $this->lang->line('reports_employees_summary_report'),
			"data" => $graph_data
		);
		$this->load->view("reports/graphs/pie",$data);
	}

	function graphical_summary_taxes($start_date,$end_date,$sale_type,$location='default')
	{
		$location = urldecode($this->input->get('loc'));
		if (isset($loca)) $location=$loca;
		$this->load->model('reports/Summary_taxes');
		$model = $this->Summary_taxes;
		$this->graphical_report($model,'taxes',$start_date,$end_date,$sale_type,$location);
	}

	//The actual graph data
	function graphical_summary_taxes_graph($start_date,$end_date,$sale_type,$location='default')
	{
		$location = ($this->input->get('loc')) ? urldecode($this->input->get('loc')) : 'default';
		$this->load->model('reports/Summary_taxes');
		$model = $this->Summary_taxes;
		$this->Sale->con=$model->stabledb($location,true);
		$this->Sale->create_sales_items_temp_table();
		$this->Receiving->con=$this->Sale->con;
		$this->Receiving->create_receivings_items_temp_table();
		$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['percent']] = $row['total'];
		}
		$data = array(
			"title" => $this->lang->line('reports_taxes_summary_report'),
			"data" => $graph_data
		);
		$this->load->view("reports/graphs/pie",$data);
	}

	//Graphical summary customers report
	function graphical_summary_customers($start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$this->load->model('reports/Summary_customers');
		$model = $this->Summary_customers;
		$this->graphical_report($model,'customers',$start_date,$end_date,$sale_type,$location);
	}

	//The actual graph data
	function graphical_summary_customers_graph($start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca)	$location=$loca;
		$this->load->model('reports/Summary_customers');
		$model = $this->Summary_customers;
		$this->Sale->con=$model->stabledb($location,true);
		$this->Sale->create_sales_items_temp_table();
		$this->Receiving->con=$this->Sale->con;
		$this->Receiving->create_receivings_items_temp_table();
		$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['customer']] = $row['total'];
		}
		$data = array(
			"title" => $this->lang->line('reports_customers_summary_report'),
			"xaxis_label"=>$this->lang->line('reports_revenue'),
			"yaxis_label"=>$this->lang->line('reports_customers'),
			"data" => $graph_data
		);
		$this->load->view("reports/graphs/hbar",$data);
	}

	//Graphical summary discounts report
	function graphical_summary_discounts($start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$this->load->model('reports/Summary_discounts');
		$model = $this->Summary_discounts;
		$this->graphical_report($model,'discounts',$start_date,$end_date,$sale_type,$location);
	}

	//The actual graph data
	function graphical_summary_discounts_graph($start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$this->load->model('reports/Summary_discounts');
		$model = $this->Summary_discounts;
		$this->Sale->con=$model->stabledb($location,true);
		$this->Sale->create_sales_items_temp_table();
		$this->Receiving->con=$this->Sale->con;
		$this->Receiving->create_receivings_items_temp_table();
		$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['discount_percent']] = $row['count'];
		}
		$data = array(
			"title" => $this->lang->line('reports_discounts_summary_report'),
			"yaxis_label"=>$this->lang->line('reports_count'),
			"xaxis_label"=>$this->lang->line('reports_discount_percent'),
			"data" => $graph_data
		);
		$this->load->view("reports/graphs/bar",$data);
	}

	function graphical_summary_payments($start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$this->load->model('reports/Summary_payments');
		$model = $this->Summary_payments;
		$this->graphical_report($model,'payments',$start_date,$end_date,$sale_type,$location);
	}

	//The actual graph data
	function graphical_summary_payments_graph($start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$this->load->model('reports/Summary_payments');
		$model = $this->Summary_payments;
		$this->Sale->con=$model->stabledb($location,true);
		$this->Sale->create_sales_items_temp_table();
		$this->Receiving->con=$this->Sale->con;
		$this->Receiving->create_receivings_items_temp_table();
		$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
		$graph_data = array();
		foreach($report_data as $row)
		{
			$graph_data[$row['payment_type']] = $row['payment_amount'];
		}
		$data = array(
			"title" => $this->lang->line('reports_payments_summary_report'),
			"yaxis_label"=>$this->lang->line('reports_revenue'),
			"xaxis_label"=>$this->lang->line('reports_payment_type'),
			"location"=>ucwords($location),
			"data" => $graph_data
		);
		$this->load->view("reports/graphs/pie",$data);
	}
	function specific_customer_input()
	{
		$data = $this->_get_common_report_data();
		$data['specific_input_name'] = $this->lang->line('reports_customer');
		$customers = array();
		foreach($this->Customer->get_all()->result() as $customer)
		{
			$customers[$customer->person_id] = $customer->first_name .' '.$customer->last_name;
		}
		$data['specific_input_data'] = $customers;
		$this->load->view("reports/specific_input",$data);	
	}

	function specific_customer($start_date,$end_date,$customer_id,$sale_type='all',$export_excel=0)
	{
		$location=$this->session->userdata('dblocation');
		$_data['view']='reports/tabular_details';
		$_data['export_excel']=$export_excel;
		$rangeDays = $this->range_days($start_date,$end_date);

		$this->load->model('reports/Specific_customer');
		$model = $this->Specific_customer;
		$this->Sale->con=$model->stabledb($location,true);
		$this->Sale->create_sales_items_temp_table();
		$this->Receiving->con=$this->Sale->con;
		$this->Receiving->create_receivings_items_temp_table();
		$headers = $model->getDataColumns();
		$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'customer_id' =>$customer_id, 'sale_type' => $sale_type));
		$summary_data = array();
		$details_data = array();
		foreach($report_data['summary'] as $key=>$row)
		{
			$summary_data[] = array(anchor('sales/receipt/'.$row['sale_id'], 'POS '.$row['sale_id'], array('target' => '_blank')), $row['sale_date'], floor($row['items_purchased']), $row['employee_name'], to_currency($row['subtotal']), to_currency($row['total']), to_currency($row['tax']),to_currency($row['profit']), $row['payment_type'], $row['comment']);
			foreach($report_data['details'][$key] as $drow)
			{
				$details_data[$key][] = array($drow['name'], $drow['category'], $drow['serialnumber'], $drow['description'], floor($drow['quantity_purchased']), to_currency($drow['subtotal']), to_currency($drow['total']), to_currency($drow['tax']),to_currency($drow['profit']), $drow['discount_percent'].'%');
			}
		}
		$customer_info = $this->Customer->get_info($customer_id);
		$data = array(
			"title" => $customer_info->first_name .' '. $customer_info->last_name.' '.$this->lang->line('reports_report'),
			"subtitle" => $rangeDays,
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"location"=>ucwords(($location=='default'?'Principal':$location)),
			"details_data" => $details_data,
			"overall_summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date,'customer_id' =>$customer_id, 'sale_type' => $sale_type)),
			"export_excel" => $export_excel,
			"right"=>array(2,4,5,6,7),
			"right_d"=>array(4,5,6,7,8,9)
		);
		$_data['list'][]=$data;
		$this->load->view("reports/format_reports",$_data);
	}

	function specific_employee_input()
	{
		$data = $this->_get_common_report_data();
		$data['specific_input_name'] = $this->lang->line('reports_employee');
		$employees = array();
		foreach($this->Employee->get_all()->result() as $employee)
		{
			$employees[$employee->person_id] = $employee->first_name .' '.$employee->last_name;
		}
		$data['specific_input_data'] = $employees;
		$this->load->view("reports/specific_input",$data);	
	}

	function specific_employee($start_date,$end_date,$employee_id,$sale_type, $export_excel=0)
	{
		$location=$this->session->userdata('dblocation');
		$_data['view']='reports/tabular_details';
		$_data['export_excel']=$export_excel;
		$rangeDays = $this->range_days($start_date,$end_date);
		$this->load->model('reports/Specific_employee');
		$model = $this->Specific_employee;
		$this->Sale->con=$model->stabledb($location,true);
		$this->Sale->create_sales_items_temp_table();
		$this->Receiving->con=$this->Sale->con;
		$this->Receiving->create_receivings_items_temp_table();
		$headers = $model->getDataColumns();
		$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'employee_id' =>$employee_id, 'sale_type' => $sale_type));
		$summary_data = array();
		$details_data = array();
		foreach($report_data['summary'] as $key=>$row)
		{
			$summary_data[] = array(anchor('sales/receipt/'.$row['sale_id'], 'POS '.$row['sale_id'], array('target' => '_blank')), $row['sale_date'], floor($row['items_purchased']), $row['customer_name'], to_currency($row['subtotal']), to_currency($row['total']), to_currency($row['tax']),to_currency($row['profit']), $row['payment_type'], $row['comment']);
			foreach($report_data['details'][$key] as $drow)
			{
				$details_data[$key][] = array($drow['name'], $drow['category'], $drow['serialnumber'], $drow['description'], floor($drow['quantity_purchased']), to_currency($drow['subtotal']), to_currency($drow['total']), to_currency($drow['tax']),to_currency($drow['profit']), $drow['discount_percent'].'%');
			}
		}
		$employee_info = $this->Employee->get_info($employee_id);
		$data = array(
			"title" => $employee_info->first_name .' '. $employee_info->last_name.' '.$this->lang->line('reports_report'),
			"subtitle" => $rangeDays,
			"headers" => $model->getDataColumns(),
			"summary_data" => $summary_data,
			"details_data" => $details_data,
			"location"=>ucwords(($location=='default'?'Principal':$location)),
			"overall_summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date,'employee_id' =>$employee_id, 'sale_type' => $sale_type)),
			"export_excel" => $export_excel,
			"right"=>array(2,4,5,6,7),
			"right_d"=>array(4,5,6,7,8,9)
		);
		$_data['list'][]=$data;
		$this->load->view("reports/format_reports",$_data);
	}

	function detailed_sales($start_date,$end_date,$sale_type,$location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$_data['view']='reports/tabular_details';
		$rangeDays = $this->range_days($start_date,$end_date);
		$export_excel=0;
		$this->load->model('reports/Detailed_sales');
		$model = $this->Detailed_sales;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			$summary_data = array();
			$details_data = array();
			foreach($report_data['summary'] as $key=>$row){
				$summary_data[] = array('POS '.$row['sale_id'], $row['sale_date'], $row['items_purchased'], $row['employee_name'], $row['customer_name'], to_currency($row['subtotal']), to_currency($row['total']), to_currency($row['tax']),to_currency($row['profit']), $row['payment_type'],anchor('sales/receipt/'.$row['sale_id'],$this->lang->line('employees_profile_see'), array('class'=>'big_button','target' => '_blank','style'=>'padding: 5px 7px;')));
				foreach($report_data['details'][$key] as $drow){
					$details_data[$key][] = array($drow['name'], $drow['category'], $drow['serialnumber'], $drow['description'], $drow['quantity_purchased'], to_currency($drow['subtotal']), to_currency($drow['total']), to_currency($drow['tax']),to_currency($drow['profit']), $drow['discount_percent'].'%');
				}
			}
			$data = array(
				"title" =>$this->lang->line('reports_detailed_sales_report'),
				"subtitle" => $rangeDays,
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,"hola"=>$report_data['sql'],
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"overall_summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type)),
				"export_excel" => $export_excel
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	function detailed_receivings($start_date, $end_date, $sale_type, $location='default')
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		$_data['view']='reports/tabular_details';
		$rangeDays = $this->range_days($start_date,$end_date);
		$export_excel=0;
		$this->load->model('reports/Detailed_receivings');
		$model = $this->Detailed_receivings;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$this->Receiving->con=$this->Sale->con;
			$this->Receiving->create_receivings_items_temp_table();
			$headers = $model->getDataColumns();
			$report_data = $model->getData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type));
			$summary_data = array();
			$details_data = array();
			foreach($report_data['summary'] as $key=>$row)
			{
				$summary_data[] = array(anchor('receivings/receipt/'.$row['receiving_id'], 'RECV '.$row['receiving_id'], array('target' => '_blank')), $row['receiving_date'], $row['items_purchased'], $row['employee_name'], $row['supplier_name'], to_currency($row['total']), $row['payment_type'], $row['comment']);
				foreach($report_data['details'][$key] as $drow)
				{
					$details_data[$key][] = array($drow['name'], $drow['category'], $drow['quantity_purchased'], to_currency($drow['total']), $drow['discount_percent'].'%');
				}
			}
			$data = array(
				"title" =>$this->lang->line('reports_detailed_receivings_report'),
				"subtitle" => $rangeDays,
				"headers" => $model->getDataColumns(),
				"summary_data" => $summary_data,
				"details_data" => $details_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"overall_summary_data" => $model->getSummaryData(array('start_date'=>$start_date, 'end_date'=>$end_date, 'sale_type' => $sale_type)),
				"export_excel" => $export_excel,
				"right"=>array(2,5),
				"right_d"=>array(2,3,4)
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	function excel_export()
	{
		$data['report_name'] = ucwords( str_replace('_',' ',$this->uri->segment(2)) );
		$this->load->view("reports/excel_export",$data);		
	}

	function shippings(){
		$this->load->model('Transfers');
		$data['title'] = $this->lang->line('reports_report').' '.$this->lang->line('reports_receiving_orders');
		$data['sub_title'] = $this->lang->line('reports_you_have').' '.$this->lang->line('reports_receiving_orders');
		$data['location'] = $this->session->userdata('dblocation');
		// $data['transfers'] = $this->Transfers->get_my_reception_detail();
		// $data['query'] = $this->Transfers->con->last_query();
		$this->load->view('reports/shippings', $data);
	}

	function inventory_low($export_excel=0,$location=false)
	{
		$loca = urldecode($this->input->get('loc'));
		if ($loca) $location=$loca;
		if (!$location) $location=$this->session->userdata('dblocation');
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$this->load->model('reports/Inventory_low');
		$model = $this->Inventory_low;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$tabular_data = array();
			$model->stabledb($location,true);
			$report_data = $model->getData(array());
			foreach($report_data as $row){
				$tabular_data[] = array($row['name'], ($row['item_number']?$row['item_number']:''), $row['description'], $row['quantity'], $row['reorder_level']);
			}
			$data = array(
				"title" => $this->lang->line('reports_low_inventory_report'),
				"subtitle" => '',
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => $model->getSummaryData(array()),
				"export_excel" => $export_excel,
				"right"=>array(3,4)
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	function inventory_summary($export_excel=0,$location='default')
	{
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$this->load->model('reports/Inventory_summary');
		$model = $this->Inventory_summary;

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$tabular_data = array();
			$model->stabledb($location,true);
			$report_data = $model->getData(array());
			foreach($report_data as $row)
			{
				$tabular_data[] = array($row['name'], $row['item_number'], $row['description'], floor($row['quantity']), floor($row['reorder_level']));
			}
			$data = array(
				"title" => $this->lang->line('reports_inventory_summary_report'),
				"subtitle" => '',
				"headers" => $model->getDataColumns(),
				"data" => $tabular_data,
				"summary_data" => $model->getSummaryData(array()),
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"export_excel" => $export_excel,
				"right"=>array(3,4)
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}
	function inventory_details_items($export_excel=0,$location){
		$_data['view']='reports/details_items';
		$_data['export_excel']=$export_excel;
		// $_data['location']=$this->session->userdata('dblocation');
		$this->load->model('reports/Inventory_low');
		$model = $this->Inventory_low;
		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$model->stabledb($location,true);			
			$data = array(
				"items_info" => $model->get_infoData(),
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"export_excel" => $export_excel
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}
	function accounts_payable($export_excel=0,$location=false){
		if (!$location) $location=$this->session->userdata('dblocation');
		$this->load->model('Transfers');
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$this->load->model('reports/Detailed_receivings');
		$model = $this->Detailed_receivings;
		$headers = array("",$this->lang->line('reports_date'),$this->lang->line('reports_items_received'),$this->lang->line('employees_employee'), $this->lang->line('suppliers_supplier'), $this->lang->line('reports_accounts_payable_payment'), $this->lang->line('reports_payment_type'), $this->lang->line('reports_accounts_payable_debt'));

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$tabular_data = array();
			$this->Sale->con=$model->stabledb($location,true);
			$this->Sale->create_sales_items_temp_table();
			$this->Receiving->con=$this->Sale->con;
			$this->Receiving->create_receivings_items_temp_table();			
			$report_data = $model->getData(array(),true);
			$report_data_transfer = $this->Transfers->transfers_receivable();
			foreach($report_data as $row)
			{
				$tabular_data[] = array('RECV'.$row['receiving_id'],$row['receiving_date'], $row['items_purchased'], $row['employee_name'], $row['supplier_name'],to_currency($row['total']), ($row['payment_type']) ? $row['payment_type'] : $this->lang->line('reports_no_payments'),(to_currency($row['money']-$row['total'])));
			}
			foreach ($report_data_transfer as $row) {
				$cadena = preg_replace("/((\<*)[a-zA-Z]+(\:|\s*))/", '', $row['payment_type']);
				$cadena = str_replace('$', '', $cadena);
				$cadenas = explode('/>', $cadena);
				unset($cadenas[count($cadenas)-1]);
				
				$row['employee_name'] = $this->Employee->get_logged_in_employee_info()->first_name;
				$row['employee_name'] .= ' '.$this->Employee->get_logged_in_employee_info()->last_name;
				$tabular_data[] = array('TRANS'.$row['receiving_id'],$row['receiving_date'], $row['items_purchased'], $row['employee_name'], $row['supplier_name'],to_currency(array_sum($cadenas)), $row['payment_type'],to_currency($row['total']));
			}
			
			$data = array(
				"title" => $this->lang->line('reports_accounts_payable'),
				"subtitle" => '',
				"headers" => $headers,
				"data" => $tabular_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => array(),
				"export_excel" => $export_excel,
				"right"=>array(5,7)
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}
	function accounts_receivable($export_excel=0,$location='default'){
		$_data['view']='reports/tabular';
		$_data['export_excel']=$export_excel;
		$this->load->model('transfers');
		$model = $this->Transfers;
		$headers = array("",$this->lang->line('reports_date'),$this->lang->line('reports_items_received'), $this->lang->line('suppliers_supplier'), $this->lang->line('reports_accounts_payable_payment'), $this->lang->line('reports_payment_type'), $this->lang->line('reports_accounts_receivable_credit'));

		$locations=$this->get_locations($location);
		foreach($locations as $location){
			$tabular_data = array();
			$report_data = $model->transfers_receivable('sender');
			foreach($report_data as $row){
				$payme=0;
				$num=explode('<br />',$row['payment_type']);
				foreach ($num as $value) {
					$num2=explode('$',$value);
					if (isset($num2[1])) $payme=$payme+$num2[1];
				}
				$tabular_data[] = array($row['receiving_id'],$row['receiving_date'], $row['items_purchased'], $row['supplier_name'],to_currency($payme), $row['payment_type'],(to_currency($row['total']-$payme)));
			}
			$data = array(
				"title" => $this->lang->line('reports_accounts_receivable'),
				"subtitle" => '',
				"headers" => $headers,
				"data" => $tabular_data,
				"location"=>ucwords(($location=='default'?'Principal':$location)),
				"summary_data" => array(),
				"export_excel" => $export_excel,
				"right"=>array(4,5,6)
			);
			$_data['list'][]=$data;
		}
		$this->load->view("reports/format_reports",$_data);
	}

	function pending_orders($id_orders='',$error=false,$location=false){
		$data['cfilter']=$this->input->post('filters');
		$data['sfilter']=$this->input->post('filter_status');
		$data['cfilter']=$data['cfilter']?$data['cfilter']:0;
		$data['sfilter']=$data['sfilter']?$data['sfilter']:0;
		if ($id_orders=='pending'){
			$id_orders='';
			$data['sfilter']=1;
		}
		$this->load->model('Order');
		$data['title'] = $this->lang->line('reports_report').' '.$this->lang->line('reports_pending_orders');
		$data['sub_title'] = $this->lang->line('reports_you_have').' '.$this->lang->line('reports_pending_orders');
		$data['location'] = $this->session->userdata('dblocation');
		$data['data']=$this->Order->get_all(
			array(
				'filter_location'=>$data['location'],
			    'filters'=>$data['cfilter'],
				'filter_status'=>$data['sfilter']
			)
		);
		if ($error){ $data['error']=$id_orders.'/'.$error; }
		$data['completelocac']=$location?'/0/0/'.$location:'';
		$this->load->view('reports/orders', $data);
	}

	function control_stock($location=false){
		$data['location'] = $this->session->userdata('dblocation');
		$data['cfilter']=0;	$data['sfilter']=0;$data['filters']=0;
		// datos de shipping
		//Filtros
		$data['cfilter']=$this->input->post('filters');
		$data['sfilter']=$this->input->post('filter_status');
		$data['cfilter']=$data['cfilter']?$data['cfilter']:0;
		$data['sfilter']=$data['sfilter']?$data['sfilter']:0;


		//Paginador 
		$config['base_url'] = base_url().'index.php/reports/control_stock/';
		$config['total_rows'] = $this->Transfers->count_all_reception($data);
		$config['per_page'] = '20';
		$config['uri_segment'] = 3;
		$this->pagination->initialize($config);
		//Enlaces paginador
		$data['links_shippings'] = $this->pagination->create_links();
		$data['data_shippings']=$this->Transfers->get_all_reception($data, $config['per_page'],$this->uri->segment($config['uri_segment']));

		//datos de orders
		//Paginator
		$config1['base_url'] = base_url().'index.php/reports/control_stock/';
		$config['first_url'] = $config1['base_url'].'/0';
		$config1['total_rows'] = $this->Order->count_all();
		$config1['per_page'] = 2;
    	$config1["num_links"] = round($config1["total_rows"] / $config1["per_page"]);
		$config1["uri_segment"] = 3;
		$config1['use_page_numbers'] = TRUE;
		$this->pagination->initialize($config1);
		//Paginator Links
		$data['links_orders'] = $this->pagination->create_links();
		$page_orders = ($this->uri->segment($config1["uri_segment"])) ? $this->uri->segment($config1["uri_segment"]) : 0;
		$data['data_orders']=$this->Order->get_all(false,$data['cfilter'],$data['sfilter'], $config1['per_page'], $page_orders);
		

		//Const
		$data['status'] = array($this->lang->line('orders_status1'),
								$this->lang->line('orders_status2'));
		$data['title'] = $this->lang->line('reports_stock_control_title');
		$data['completelocac']=$location?'/0/0/'.$location:'';
		$this->load->view('reports/control_stock', $data);
	}
	function see_dialog_error($id_orders='',$error=false){
		$error=explode(':', $error);
		switch ($error[0]) {
			case 1:	
				if ($error[1]=='all'){
					$data['content']='<div class="error_message" style="padding: 15px;font-size: 16px;margin: 15px;">'.$this->lang->line('orders_no_items_to_shipping').'</div>'; 
				}else{
					$items=$this->Item->get_multiple_info(explode('+',$error[1]));
					$data['content']='<div class="error_message">'.$this->lang->line('orders_no_items_to_shipping2').'</div><div class="item_no_stock"><ul>';
					foreach ($items->result() as $key) {
						$data['content'].='<li>'.$key->name.'</li>';							
					}
					$data['content'].='</ul></div>';
				}
				$data['content'].='<div>'.
					anchor('sales/pending_orders_to_shipping/'.$id_orders, $this->lang->line('orders_process_anyway'), 'class="big_button"').'</div>';
			break;
			case 2: $data['content']='<div class="error_message" style="padding: 15px;font-size: 16px;margin: 15px;">'.$this->lang->line('orders_no_proce_loca').'</div>'; break;
		}
		$this->load->view('see_dialog',$data);
	}
}
?>
