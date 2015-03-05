<?php
require_once ("secure_area.php");
class No_Access extends Secure_area 
{
	function __construct()
	{
		parent::__construct();
	}
	
	function index($module_id='',$privi=false)
	{ 	//$data['module_name']=$this->Module->get_module_name($module_id);
		if (!$privi) $data['module_name']=$this->lang->line('module_'.$module_id);
		else $data['module_name']=$this->lang->line('module_pri_'.str_replace('%20','_',$module_id));
		$this->load->view('no_access',$data);
	}
}
?>